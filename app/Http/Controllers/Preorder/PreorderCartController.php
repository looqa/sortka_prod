<?php

namespace App\Http\Controllers\Preorder;

use App\Http\Controllers\Controller;
use App\Mail\SuccessPreorder;
use App\Models\Page;
use App\Models\Preorder;
use App\Models\PreorderCheckout;
use App\Models\PreorderCheckoutProduct;
use App\Models\PreorderProduct;
use App\Models\User;
use App\Services\Preorder\PreorderService;
use Doctrine\Common\Cache\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PreorderCartController extends Controller
{
    public function index() {
        $cart = PreorderService::getFullCart();
        $cartKeys = collect(array_keys($cart));
        $page = 'preorder';

        $address = auth()->user()->address()->get() ?? collect();
        $user = auth()->user();
        $preorder_minimal = 0;
        if (count($cart)) {
            $preorder_minimal = (Preorder::find(Arr::first($cart)['preorder_id']))->min_order ?? 0;
        }

        /*foreach ($cart as &$item) {
            $item['multiplicity_tu'] = PreorderProduct::find($item['id'])->multiplicity_tu;
        }*/

        $cart = collect($cart)->groupBy('preorder_id')->toArray();

        foreach ($cart as $preorder_id => &$item) {
            $preorder = Preorder::find($preorder_id);
            if (!$preorder) {
                continue;
            }
            $preorder = $preorder->toArray();
            $preorder['products'] = $item;
            $preorder['total_amount'] = 0;
            foreach ($item as $product) {
                $preorder['total_amount'] += $product['price'] * $product['quantity'];
            }

            $preorder['prepay_amount'] = round($preorder['total_amount'] / 100 * $preorder['prepay_percent'], 2);

            $item = $preorder;
        }

        return view('preorder.cart', compact('cart', 'cartKeys', 'page', 'address', 'user', 'preorder_minimal'));
    }

    public function cart(User $user = null)
    {
        if (!$user) $user = auth()->user();
        $cart = PreorderService::getFullUserCart($user->id);

        foreach ($cart as &$item) {
            $product = PreorderProduct::find($item['id']);
            if (!$product) {
                PreorderService::removeFromCart($item['id']);
                unset($cart[$item['id']]);
                continue;
            }
            $item['multiplicity_tu'] = PreorderProduct::find($item['id'])->multiplicity_tu;
        }

        $cart = collect($cart)->groupBy('preorder_id')->toArray();

        foreach ($cart as $preorder_id => &$item) {
            $preorder = Preorder::find($preorder_id)->toArray();
            $preorder['products'] = $item;
            $preorder['total_amount'] = 0;
            foreach ($item as $product) {
                $preorder['total_amount'] += $product['price'] * $product['quantity'];
            }

            $preorder['prepay_amount'] = round($preorder['total_amount'] / 100 * $preorder['prepay_percent'], 2);

            $item = $preorder;
        }

        return [
            'status' => 'success',
            'cart' => $cart
        ];
    }

    public function empty(Preorder $preorder, User $user = null)
    {
        if ($user === null) $user = auth()->user();
        PreorderService::removePreorderFromCart($preorder->id, $user->id);

        return back();
    }

    public function create(Request $request, User $user = null)
    {
        if (!$user) $user = auth()->user();
        $history = cache()->get('preorder_history_' . $user->id) ?? [];
        $cart = $this->cart()['cart'];
        $order = $cart[$request->get('preorder_id')];

        //$history[] = $order;
        $props = [
            'user_id' => $user->id,
            'preorder_id' => $order['id']
        ];
        if (auth()->user()->role_id === 1 && auth()->user()->id == $user->id) {
            $props['is_internal'] = true;
        }
        $checkoutedPreorder = PreorderCheckout::create($props);
        foreach ($order['products'] as $product) {
            PreorderCheckoutProduct::create([
                'preorder_checkout_id' => $checkoutedPreorder->id,
                'preorder_product_id' => $product['id'],
                'qty' => $product['quantity']
            ]);
        }

        try {
            \Mail::to('sotkapredzakaz@mail.ru')->send(
                new SuccessPreorder($checkoutedPreorder, true)
            );
            \Mail::to($checkoutedPreorder->user)->send(
                new SuccessPreorder($checkoutedPreorder)
            );

        } catch (\Exception $e) {
            \Log::channel('orders')->error($e->getMessage(), [
                'exception' => $e,
                'data' => $checkoutedPreorder
            ]);
        }

        //cache()->put('preorder_history_' . auth()->id(), $history);

        PreorderService::removePreorderFromCart($request->get('preorder_id'), $user->id);

        return redirect()->back();
    }
}
