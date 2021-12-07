<?php

namespace App\Http\Controllers;

use App\Mail\AccountAcepted;
use App\Models\ProfileAddress;
use App\Models\User;
use App\Models\UserBrandSaleSystem;
use App\Models\UserSaleSystem;
use App\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
    }


    public function index()
    {
        $data['page'] = 'index';
        $data['user'] = $user = Auth::user();
        $data['address'] = ProfileAddress::where('user_id', $user->id)->get();

        return view('profile.index', $data);
    }

    /*
     * POST METHOD TO UPDATE
     * */
    public function update(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->validate([
                'name' => 'required',
                'phon' => 'required',
                'city' => 'required'
            ]);

            $user = Auth::user();

            foreach ($data as $key => $item)
                $user->$key = $item;

            $user->save();
        }

        return response()->redirectToRoute('profile.index');
    }

    /*
     * POST METHOD TO CHANGE PASSWORD
     * */
    public function changePassword(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $data = $request->validate([
                'old_password' => 'required',
                'password' => 'required|confirmed|min:8',
            ]);

            if (Hash::check($data['old_password'], Auth::user()->password)) {
                User::find($id)->update(['password' => Hash::make($data['password'])]);
            }
        }

        return response()->redirectToRoute('profile.index');
    }

    public function address(Request $request, $id = 0)
    {
        $action = $request->segment('3');

        switch ($action) {
            case 'create':
                $data['user'] = Auth::user();
                return \response()->view('profile.components.address', $data);
                break;
            case 'store':
                $data = $request->validate([
                    'city' => 'required',
                    'address' => 'required',
                    'region' => 'required',
                    'house' => 'required',
                    'user_id' => 'required'
                ]);

                ProfileAddress::store($data);
                break;
            case 'edit':
                $data['user'] = Auth::user();
                $data['item'] = ProfileAddress::find($id);
                return \response()->view('profile.components.address', $data);
                break;
            case 'update':
                $item = ProfileAddress::find($id);

                $data = $request->validate([
                    'city' => 'required',
                    'address' => 'required',
                    'region' => 'required',
                    'house' => 'required',
                    'user_id' => 'required'
                ]);

                foreach ($data as $key => $value)
                    $item->$key = $value;

                $item->save();
                break;

            case 'delete':
                $item = ProfileAddress::find($id);

                $item->delete();
                break;
            case 'change':
                $user = Auth::user();

                $user->address = $id;
                $user->save();
                break;
            case 'autocomplete':
                if (empty($request->get('regionId'))) {
                    $url = "https://kladr-api.ru/api.php?token=9dTKNARAAFBGtQYTebaTie53NfA254EF&contentType=region&query=" . $request->get('s');
                    $data = file_get_contents($url);

                    try {
                        $items = [];
                        collect(json_decode($data)->result)->each(function ($item) use (&$items) {
                            $items[] = [
                                'id' => $item->id,
                                'name' => $item->name,
                                "city" => true
                            ];
                        });

                        return \response()->json($items);
                    } catch (\Exception $e) {
                        return \response()->json(['status' => 'error', 'msg' => 'К сожалению, данный регион не найден']);
                    }
                }
                elseif (empty($request->get('city'))) {
                    $url = "https://kladr-api.ru/api.php?token=9dTKNARAAFBGtQYTebaTie53NfA254EF&contentType=city&query=" . $request->get('s').'&regionId='.$request->get('regionId');
                    $data = file_get_contents($url);

                    try {
                        $items = [];
                        collect(json_decode($data)->result)->each(function ($item) use (&$items) {
                            $items[] = [
                                'id' => $item->id,
                                'name' => $item->name,
                                "city" => true
                            ];
                        });

                        return \response()->json($items);
                    } catch (\Exception $e) {
                        return \response()->json(['status' => 'error', 'msg' => 'К сожалению, данный город не найден']);
                    }
                } else {

                    $url = "https://kladr-api.ru/api.php?token=9dTKNARAAFBGtQYTebaTie53NfA254EF&contentType=street&query=" . $request->get('s') . '&cityId=' . $request->get('city');
                    $data = file_get_contents($url);

                    try {
                        $items = [];
                        collect(json_decode($data)->result)->each(function ($item) use (&$items) {
                            $items[] = [
                                'id' => $item->id,
                                'name' => $item->name,
                                'city' => false
                            ];
                        });

                        return \response()->json($items);
                    } catch (\Exception $e) {
                        return \response()->json(['status' => 'error', 'msg' => 'К сожалению, данный адрес не найден']);
                    }
                }
                break;
        }

        return \redirect()->back();
    }

    public function orders(Request $request) {
        $action = $request->segment(3);
        $data = [];

        switch ($action) {
            case 'current':
                $data['page'] = 'current-orders';
                $data['orders'] = Order::getUserCurrentOrders($request->user()->id);
                break;
            case 'order-history':
                $data['page'] = 'order-history';
                $data['orders'] = Order::getUserCurrentOrders($request->user()->id, true);
                break;
        }

        return \response()->view('profile.orders.current', $data);
    }
    public function reOrders( $id) {

        $expr = DB::table('order_products')
            ->join('orders', 'orders.id', '=', 'order_products.order_id')
            ->join('products', 'order_products.product_id', '=', 'products.id')
            ->join('users', 'users.id', '=', 'orders.user_id')
            ->join('user_address', 'user_address.user_id', '=', 'users.id')
            ->select('products.title AS title',
                'products.images as images',
                'products.total as total',
                'orders.created_at as order_date',
                'products.id',
                'order_products.order_id',
                'users.name as u_name',
                'users.email as u_email',
                'users.phone as u_phone',
                'user_address.city as u_city',
                'user_address.address as u_address',
                'products.price as price',
                'order_products.qty as quantity')
            ->where('orders.id', $id)
            ->where('order_products.qty', '>', 0)
            ->get();

        $cart=[];
        foreach ($expr as $exp ) {
            $cart[$exp->id] = [
                "title" => $exp->title,
                "images" => $exp->images,
                "quantity" => $exp->quantity,
                "price" => $exp->price,
                "total" => $exp->total
            ];
        }
        session()->put(compact('cart'));

        $cart = session()->get('cart');
        return redirect()->back()->with('success', 'Товар добавлен в корзину');
    }

    public function orderHistory(Request $request) {
        $action = $request->segment(3);
        $data['page'] = 'order-history';

        $data['orders'] = DB::table('order_products')
            ->join('orders', 'orders.id', '=', 'order_products.order_id')
            ->join('products', 'order_products.product_id', '=', 'products.id')
            ->select('products.title AS title',
                'products.id as prod_id',
                'order_products.order_id',
                'orders.user_id',
                'orders.random',
                'orders.status',
                'orders.id as order_id',
                'orders.created_at',
                'products.price as price',
                'products.title',
                'products.images',
                'order_products.qty as quantity',
                'products.oneC_7 as oneC_7')
            ->where('orders.user_id', Auth::id())
            ->get();

        return \response()->view('profile.orders.'.$action, $data);

    }

    public function updateTableCategories(Request $request, $id) {
        $data = \Illuminate\Support\Facades\Request::post('userRange');
        $categoriesCheckBox = \Illuminate\Support\Facades\Request::post('category');
        $categoryChilds = \Illuminate\Support\Facades\Request::post('category_childs');

        UserSaleSystem::where('user_id', $id)->delete();
        User::removeUserSales($id);

        foreach ($data as $category_id => $percent) {
            if (in_array($category_id, array_keys($categoriesCheckBox)))
                User::addSaleToCategory($category_id, $percent, $id);

            foreach ($categoriesCheckBox as $categoryCheck_id => $checkBox)
                if (in_array($categoryCheck_id, explode(',', $categoryChilds[$category_id])))
                    User::addSaleToCategory($categoryCheck_id, $percent, $id);

            if (!empty($percent))
                UserSaleSystem::create(['sale' => (float)$percent, 'category_id' => $category_id, 'user_id' => $id]);
        }
        return Redirect::route('voyager.users.edit', ['id' => $id]);
    }

    public function updateTableBrands(Request $request, $id) {
        $data = \Illuminate\Support\Facades\Request::post('priceRange');
        $brandsCheckBox = \Illuminate\Support\Facades\Request::post('brands');

        UserBrandSaleSystem::where('user_id', $id)->delete();
        (new \App\Models\Brands)->removeBrandSalesToUser($id);

        foreach ($data as $brand_id => $percent) {
            if (in_array($brand_id, array_keys($brandsCheckBox)))
                User::addSaleToBrand($brand_id, $percent, $id);

            if (!empty($percent))
                UserBrandSaleSystem::create(['sale' => (float)$percent, 'brand_id' => $brand_id, 'user_id' => $id]);
        }

        return Redirect::route('voyager.users.edit', ['id' => $id]);
    }

    public function activeAccount(Request $request, $id){
        if($request->active){
            $userData = User::find($id);
            $userData->active = $request->active;
            $userData->save();

            if($request->active == 'on'){
                Mail::to($userData->email)->send(new AccountAcepted());
            }

            return json_encode(array('statusCode'=>200));
        }
    }
}