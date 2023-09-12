@extends('layouts.app')

@section('content')
<!-- CART VIEW -->
<div id="cart-view" class="section">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="cart-view-area">
                    <div class="cart-view-table">
                        <form action="#">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Total</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Sample cart item, repeat this for each item in the cart -->
                                        <tr>
                                            <td>
                                                <div class="cart-product-img">
                                                    <img src="product-image.jpg" alt="Product Image">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="cart-product-name">
                                                    <h6>Product Name</h6>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="cart-product-price">
                                                    <p>$50.00</p>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="cart-product-quantity">
                                                    <input class="quantity" type="number" value="1">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="cart-product-total">
                                                    <p>$50.00</p>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="cart-product-remove">
                                                    <a href="#"><i class="fa fa-trash"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                        <!-- End of sample cart item -->

                                        <!-- Repeat the above block for each item in the cart -->

                                    </tbody>
                                </table>
                            </div>
                        </form>
                        <!-- Cart Total view -->
                        <div class="cart-view-total">
                            <h4>Cart Totals</h4>
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td>Subtotal</td>
                                        <td>$100.00</td>
                                    </tr>
                                    <tr>
                                        <td>Shipping</td>
                                        <td>Free</td>
                                    </tr>
                                    <tr>
                                        <td>Total</td>
                                        <td>$100.00</td>
                                    </tr>
                                </tbody>
                            </table>
                            <a href="#" class="btn btn-primary">Proceed to Checkout</a>
                        </div>
                        <!-- /Cart Total view -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /CART VIEW -->

    <div class="container">
        <h1>Twój koszyk</h1>

        @if(count($cart) > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Nazwa produktu</th>
                        <th>Ilość</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cart as $productId => $item)
    <div class="cart-item">
        <h2>Nazwa produktu: {{ $item['product']['name'] }}</h2>
        <p>Opis: {{ $item['product']['description'] }}</p>
        <p>Cena: ${{ $item['product']['price'] }}</p>
        <p>Ilość: {{ $item['quantity'] }}</p>
    </div>
@endforeach

                </tbody>
            </table>
        @else
            <p>Twój koszyk jest pusty.</p>
        @endif
    </div>
@endsection
