@extends('layouts.app')

@section('content')
		<!-- SECTION -->
		<div class="section">
			<!-- container -->
			<div class="container">
				<!-- row -->
				<div class="row">
					<!-- shop -->
					<div class="col-md-4 col-xs-6">
						<div class="shop">
							<div class="shop-img">
								<img src="./img/shop01.png" alt="">
							</div>
							<div class="shop-body">
								<h3>Laptop<br>Collection</h3>
								<a href="#" class="cta-btn">Shop now <i class="fa fa-arrow-circle-right"></i></a>
							</div>
						</div>
					</div>
					<!-- /shop -->

					<!-- shop -->
					<div class="col-md-4 col-xs-6">
						<div class="shop">
							<div class="shop-img">
								<img src="./img/shop03.png" alt="">
							</div>
							<div class="shop-body">
								<h3>Accessories<br>Collection</h3>
								<a href="#" class="cta-btn">Shop now <i class="fa fa-arrow-circle-right"></i></a>
							</div>
						</div>
					</div>
					<!-- /shop -->

					<!-- shop -->
					<div class="col-md-4 col-xs-6">
						<div class="shop">
							<div class="shop-img">
								<img src="./img/shop02.png" alt="">
							</div>
							<div class="shop-body">
								<h3>Cameras<br>Collection</h3>
								<a href="#" class="cta-btn">Shop now <i class="fa fa-arrow-circle-right"></i></a>
							</div>
						</div>
					</div>
					<!-- /shop -->
				</div>
				<!-- /row -->
			</div>
			<!-- /container -->
		</div>
		<!-- /SECTION -->


@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        updateCartDropdown();

        attachAddToCartListeners();

        $(document).on('click', '.delete', function() {
            var productId = $(this).data('product-id');
            removeFromCart(productId);
        });

        function attachAddToCartListeners() {
            $('.add-to-cart-btn').click(function(event) {
                event.preventDefault(); // Dodane preventDefault
                var productId = $(this).data('product-id');
                addToCart(productId);
            });
        }

        function addToCart(productId) {
            $.ajax({
                url: '/cart/add/' + productId,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    alert('Produkt dodany do koszyka!');
                    updateCartDropdown(response);
                },
                error: function(error) {
                    alert('Wystąpił błąd podczas dodawania produktu do koszyka.');
                }
            });
        }

        function updateCartDropdown() {
            $.ajax({
                url: '{{ route("cart.contents") }}',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    const cart = response.cart;
                    $('.cart-list').empty();

                    let totalQuantity = 0;

                    $.each(cart, function(productId, item) {
                        const productHtml = `
                            <div class="product-widget">
                                <div class="product-img">
                                    ${item.product.image ? `<img src="${item.product.image}" alt="No photo">` : '<img src="{{ asset("images/default_image.jpg") }}" alt="No photo">'}
                                </div>
                                <div class="product-body">
                                    <h3 class="product-name"><a href="#">${item.product.name}</a></h3>
                                    <h4 class="product-price"><span class="qty">${item.quantity}x</span> $${item.product.price}</h4>
                                </div>
                                <button class="delete" data-product-id="${productId}"><i class="fa fa-close"></i></button>
                            </div>`;
                        $('.cart-list').append(productHtml);

                        totalQuantity += item.quantity;
                    });

                    // Aktualizacja reszty informacji o koszyku (subtotal, ilość)
                    // ...

                    // Jeśli potrzebujesz, możesz też zaktualizować liczbę przedmiotów na przycisku koszyka
                    $('#totalQty').text(totalQuantity);
                },
                error: function(error) {
                    console.log('Wystąpił błąd podczas aktualizowania koszyka:', error);
                }
            });
        }


        function removeFromCart(productId) {
            $.ajax({
                url: '/cart/remove/' + productId,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    updateCartDropdown(response.cart);
                },
                error: function(error) {
                    alert('Wystąpił błąd podczas usuwania produktu z koszyka.');
                }
            });
        }
    });
</script>


@endsection
