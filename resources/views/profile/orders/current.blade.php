@extends('layouts.app')

@section('content')
    <section class="box__personalarea">
        <div class="container">
            @include('profile.components.tabs')
            <div class="row">
                <div class="col-12">
                    <div class="box__ptofile-currentorder">
                        @foreach ($orders as $order)
                            @php
                                $amount = 0;
                                $changes = false;
                                foreach ($order->products as $product) {
                                	if (is_null($product->info)) continue;
                                    $amount += $product->price + $product->price_changed;

                                    if ($product->qty == 0 || $product->excepted == 1)
                                    	$changes = true;
                                }
                            @endphp
                            <div class="box__item">
                                <div class="wrapper__currentorder">
                                    <div class="row">
                                        <div class="col-12 col-xl-2">
                                            <div class="box__currentorder-ordernumber">Заказ
                                                № {{ $order->id }}</div>
                                            <div class="btn btn__currentorder-order" data-href="/reorder/{{$order->id}}"
                                                 data-btn-popup="repeatorder"><a href="#">Повторить заказ</a>
                                            </div>
                                        </div>
                                        <div class="col-12 col-xl-1">
                                            <div
                                                class="box__currentorder-orderdate">{{ date('d', strtotime($order->created_at)) }} {{ rusDate(date('m', strtotime($order->created_at))) }} {{ date('Y', strtotime($order->created_at)) }}</div>
                                        </div>
                                        <div class="col-12 col-xl-5">
                                            @if ($changes)
                                                <div class="box__currentorder-warning">Имеются изменения в заказе</div>
                                            @endif
                                        </div>
                                        <div class="col-6 col-xl-2">
                                            <div class="box__currentorder-orderpriceall"><span
                                                    class="orderpriceall-title">Стоимость: </span> {{ $amount }}
                                                <span>₽</span>
                                            </div>
                                        </div>
                                        <div class="col-6 col-xl-2">
                                            <div class="box__currentorder-status"><span class="orderpriceall-title">Статус заказа: </span>
                                                @switch($order->status)
                                                    @case("Saved")
                                                    Сохранён
                                                    @break
                                                    @case("Processed")
                                                    Обрабатывается
                                                    @break
                                                    @case("Collected")
                                                    Собран
                                                    @break
                                                    @case("Completed")
                                                    Готов к выдаче
                                                    @break
                                                    @case("Shipped")
                                                    Отгружен
                                                    @break
                                                    @case("Deleted")
                                                    Удалён
                                                    @break
                                                    @default
                                                    {{$order->status}}
                                                @endswitch
                                            </div>
                                        </div>
                                    </div>
                                    <div class="btn__currentorder-toggle">
                                        <button></button>
                                    </div>
                                </div>
                                <div class="wrapper__currentorder-info">
                                    <div class="box__item-infotitle">
                                        <div class="row">
                                            <div class="col-12 col-xl-3"><h4>Наименование</h4></div>
                                            <div class="col-12 col-xl-2"><h4>Количество</h4></div>
                                            <div class="col-12 col-xl-2"><h4>Цена</h4></div>
                                            <div class="col-12 col-xl-1"><h4>% скидки</h4></div>
                                            <div class="col-12 col-xl-2"><h4>Цена со скидкой</h4></div>
                                            <div class="col-12 col-xl-2"><h4>Стоимость</h4></div>
                                        </div>
                                    </div>
                                    @foreach (collect($order->products)->where('price_changed', '!=', 0)->where('qty_changed', '!=', 0)->toArray() as $product)
                                        @if (is_null($product->info))
                                            @continue
                                        @endif
                                        <div
                                            class="box__item-info {{ $changes ? 'changes' : '' }} {{ $product->qty_changed + $product->qty <= 0 ? 'deleted-product' : '' }}"
                                            style="{{ $changes ? 'background-color:#ffdcdc' : '' }}">
                                            <div class="row">
                                                <div class="col-12 col-xl-3">
                                                    <div class="wrapper__item-titleimg">
                                                        <div class="box__item-img"><a href="#"><span
                                                                    style="background-image: url( {{  thumbImg($product->info->images, 50, 70) }} )"></span></a>
                                                        </div>
                                                        <div class="box__item-title"><a href="#">
                                                                <h4>{{ $product->info->title }}</h4></a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-xl-2">
                                                    <div class="box__item-quality">
                                                        {{ $product->qty_changed }} шт.
                                                    </div>
                                                </div>
                                                <div class="col-6 col-xl-2">
                                                    <div class="box__item-priceonly">

                                                    </div>
                                                </div>
                                                <div class="col-6 col-xl-1">
                                                    <div class="box__item-discount">
                                                    </div>
                                                </div>
                                                <div class="col-6 col-xl-2">
                                                    <div class="box__item-discountprice">
                                                    </div>
                                                </div>
                                                <div class="col-6 col-xl-2">
                                                    <div class="box__item-priceall">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    @foreach ($order->products as $product)
                                        @php($changes = ($product->qty + $product->qty_changed <= 0 || $product->excepted == 1))
                                        @if (is_null($product->info))
                                            @continue
                                        @endif

                                    @if ($product->price + $product->price_changed == 0)
                                        @continue
                                    @endif

                                        @php($sale = \App\Product::getMaxSaleToProduct($product->info->id, $product->price, $product->qty))
                                        <div
                                            class="box__item-info">
                                            <div class="row">
                                                <div class="col-12 col-xl-3">
                                                    <div class="wrapper__item-titleimg">
                                                        <div class="box__item-img"><a href="#"><span
                                                                    style="background-image: url( {{  thumbImg($product->info->images, 50, 70) }} )"></span></a>
                                                        </div>
                                                        <div class="box__item-title"><a href="#">
                                                                <h4>{{ $product->info->title }}</h4></a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-xl-2">
                                                    <div class="box__item-quality">
                                                            <span>Количество: </span>
                                                            <strong>{{ $product->qty + $product->qty_changed }}
                                                                шт.</strong>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-xl-2">
                                                    <div class="box__item-priceonly">
                                                        <span>Цена: </span>{{ $product->info->price }} ₽
                                                    </div>
                                                </div>
                                                <div class="col-6 col-xl-1">
                                                    <div class="box__item-discount">
                                                        <span>% скидки: </span>{{ $product->sale }}
                                                    </div>
                                                </div>
                                                <div class="col-6 col-xl-2">
                                                    <div class="box__item-discountprice">
                                                        <span>Цена со скидкой: </span> {{ number_format($product->info->price - ( $product->sale * $product->info->price / 100), 2) }}
                                                        ₽
                                                    </div>
                                                </div>
                                                <div class="col-6 col-xl-2">
                                                    <div class="box__item-priceall">
                                                        <span>Стоимость: </span>
                                                        <strong> {{ $product->price + $product->price_changed }} ₽</strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            {{ $orders->links('profile.components.pagination') }}
        </div>
    </section>

    <div class="box__popup" data-popup="repeatorder" aria-hidden="false" role="dialog">
        <div class="wrapper-popup">
            <div class="btn__close">
                <button aria-label="Закрыть попап" data-btn-closepopup=""><span></span></button>
            </div>
            <div class="row">
                <div class="col-12">
                    <h2 class="text-center">Повторить заказ</h2>
                    <div class="box__description">При повторе заказа, товары располагающиеся в корзине будут
                        удалены.
                    </div>
                    <div class="btn">
                        <a href="/reorder" class="link-button">Продолжить</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function () {
            $('body').on('click', '.btn.btn__currentorder-order', function () {
                $('.link-button').attr('href', $(this).data('href'))
            })
        })
    </script>
@endsection
