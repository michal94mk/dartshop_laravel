@extends('layouts.app')

@section('content')
    <h1>Your cart</h1>

    @if($pagedData->count() > 0)
        <!-- CART VIEW -->
        <div id="cart-view" class="section">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="cart-view-area">
                            <div class="cart-view-table">
                                <form action="#">
                                    @csrf
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>Product</th>
                                                    <th>Price</th>
                                                    <th>Quantity</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($pagedData as $item)
                                                    <tr>
                                                        <td>
                                                            <div class="cart-product-img">
                                                                @if ($item['product']->image)
                                                                    <img src="{{ $item['product']->image }}" alt="{{ $item['product']->name }}" height="100px" width="100px">
                                                                @else
                                                                    <span class="text-muted">No photo</span>
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="cart-product-name">
                                                                <h6>{{ $item['product']->name }}</h6>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="cart-product-price">
                                                                <p id="price-{{ $item['product']->id }}">${{ $item['product']->price }}</p>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="cart-product-quantity">
                                                                <form action="{{ route('cart.delete', $item['product']->id) }}" method="post" style="display: inline;">
                                                                    @csrf
                                                                    <button type="submit" class="decrease" data-product-id="{{ $item['product']->id }}">-</button>
                                                                </form>
                                                                <input class="quantity" type="number"
                                                                    value="{{ $item['quantity'] }}"
                                                                    data-product-id="{{ $item['product']->id }}"
                                                                    min="1" readonly style="width: 50px;" >
                                                                <form action="{{ route('cart.add', $item['product']->id) }}" method="post" style="display: inline;">
                                                                    @csrf
                                                                    <button type="submit" class="increase" data-product-id="{{ $item['product']->id }}">
                                                                        +
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="cart-product-total">
                                                                <p id="total-{{ $item['product']->id }}">${{ $item['product']->price * $item['quantity'] }}</p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </form>
                                <!-- Cart Total view -->
                                <div class="cart-view-total">
                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <td>Total</td>
                                                <td id="cart-total"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <button class="button-blue">Checkout</button>
                                </div>
                                <!-- /Cart Total view -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="store-filter clearfix">
            <span class="store-qty">
                Showing {{ $pagedData->firstItem() }}-{{ $pagedData->lastItem() }} of {{ $pagedData->total() }} products
            </span>
            <ul class="store-pagination">
                @if ($pagedData->currentPage() > 1)
                    <li><a href="{{ route('cart.view', ['page' => $pagedData->currentPage() - 1]) }}"><</a></li>
                @endif

                @php
                    $leftLimit = max($pagedData->currentPage() - 2, 1);
                    $rightLimit = min($pagedData->currentPage() + 2, $pagedData->lastPage());
                @endphp

                @if ($leftLimit > 1)
                    <li><a href="{{ route('cart.view', ['page' => 1]) }}">1</a></li>
                    @if ($leftLimit > 2)
                        <li><span class="dots">...</span></li>
                    @endif
                @endif

                @for ($i = $leftLimit; $i <= $rightLimit; $i++)
                    <li class="{{ $i == $pagedData->currentPage() ? 'active' : '' }}">
                        <a href="{{ route('cart.view', ['page' => $i]) }}">{{ $i }}</a>
                    </li>
                @endfor

                @if ($rightLimit < $pagedData->lastPage())
                    @if ($rightLimit < $pagedData->lastPage() - 1)
                        <li><span class="dots">...</span></li>
                    @endif
                    <li><a href="{{ route('cart.view', ['page' => $pagedData->lastPage()]) }}">{{ $pagedData->lastPage() }}</a></li>
                @endif

                @if ($pagedData->currentPage() < $pagedData->lastPage())
                    <li><a href="{{ route('cart.view', ['page' => $pagedData->currentPage() + 1]) }}">></a></li>
                @endif
            </ul>
        </div>
                <!-- /Pagination -->
    @else
        <p>Your cart is empty.</p>
    @endif
@endsection
