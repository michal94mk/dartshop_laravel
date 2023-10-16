<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
		 <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

		<title>Electro - HTML Ecommerce Template</title>

 		<!-- Google font -->
         <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,700" rel="stylesheet">

         <!-- Bootstrap -->
         <link type="text/css" rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}"/>

         <!-- Slick -->
         <link type="text/css" rel="stylesheet" href="{{ asset('css/slick.css') }}"/>
         <link type="text/css" rel="stylesheet" href="{{ asset('css/slick-theme.css') }}"/>

         <!-- nouislider -->
         <link type="text/css" rel="stylesheet" href="{{ asset('css/nouislider.min.css') }}"/>

         <!-- Font Awesome Icon -->
         <link rel="stylesheet" href="{{ asset('css/font-awesome.min.css') }}">

         <!-- Custom stlylesheet -->
         <link type="text/css" rel="stylesheet" href="{{ asset('css/style.css') }}"/>

 		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
 		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
 		<!--[if lt IE 9]>
 		  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
 		  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
 		<![endif]-->

    </head>
	<body>
		<!-- HEADER -->
		<header>
			<!-- TOP HEADER -->
            <div id="top-header">
                <div class="container">
                    <ul class="header-links pull-left">
                        <li><a href="#"><i class="fa fa-phone"></i> +021-95-51-84</a></li>
                        <li><a href="#"><i class="fa fa-envelope-o"></i> email@email.com</a></li>
                        <li><a href="#"><i class="fa fa-map-marker"></i> 1734 Stonecoal Road</a></li>
                    </ul>
                    <ul class="header-links pull-right">
                        <li><a href="#"><i class="fa fa-dollar"></i> USD</a></li>
                        @guest
                            <li><a href="{{ route('login') }}"><i class="fa fa-user-o"></i> {{ __('Login') }}</a></li>
                            <li><a href="{{ route('register') }}"><i class="fa fa-user-o"></i> {{ __('Register') }}</a></li>
                        @else
                            @if(auth()->check())
                                @if(auth()->user()->role === 'admin')
                                    <li><a href="{{ route('admin.categories.index') }}"><i class="fa fa-cog"></i> Admin Panel</a></li>
                                @endif
                            @endif
                            <li><a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fa fa-sign-out"></i> {{ __('Logout') }}
                            </a></li>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                            <li><a href="{{ route('profile.edit') }}"><i class="fa fa-user-o"></i> My Account</a></li>
                        @endguest
                    </ul>
                </div>
            </div>



			<!-- /TOP HEADER -->

			<!-- MAIN HEADER -->
			<div id="header">
				<!-- container -->
				<div class="container">
					<!-- row -->
					<div class="row">
						<!-- LOGO -->
						<div class="col-md-3">
							<div class="header-logo">
								<a href="#" class="logo">
									<img src="{{ asset('img/logo.png') }}" alt="">
								</a>
							</div>
						</div>
						<!-- /LOGO -->

						<!-- SEARCH BAR -->
                        <div class="col-md-6">
                            <div class="header-search">
                                <form>
                                    <select class="input-select">
                                        <option value="0">All Categories</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <input class="input" placeholder="Search here">
                                    <button class="search-btn">Search</button>
                                </form>
                            </div>
                        </div>
						<!-- /SEARCH BAR -->

						<!-- ACCOUNT -->
						<div class="col-md-3 clearfix">
							<div class="header-ctn">
								<!-- Wishlist -->
								<div>
									<a href="#">
										<i class="fa fa-heart-o"></i>
										<span>Your Wishlist</span>
										<div class="qty">2</div>
									</a>
								</div>
								<!-- /Wishlist -->

								<!-- Cart -->
                                <div class="dropdown">
                                    <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                        <i class="fa fa-shopping-cart"></i>
                                        <span>Your Cart</span>
                                        <div class="qty" id="totalQty">{{$totalQty}}</div>
                                    </a>
                                    <div class="cart-dropdown">
                                        <div class="cart-list">
                                        </div>

                                        <div class="cart-summary">
                                            <small><span class="qty"></span> Item(s) selected</small>
                                            <h5>SUBTOTAL: $0.00</h5>
                                        </div>
                                        <div class="cart-btns">
                                            <a href="{{route('cart.view')}}">View Cart</a>
                                            <a href="#">Checkout  <i class="fa fa-arrow-circle-right"></i></a>
                                        </div>
                                    </div>
                                </div>


								<!-- /Cart -->

								<!-- Menu Toogle -->
								<div class="menu-toggle">
									<a href="#">
										<i class="fa fa-bars"></i>
										<span>Menu</span>
									</a>
								</div>
								<!-- /Menu Toogle -->
							</div>
						</div>
						<!-- /ACCOUNT -->
					</div>
					<!-- row -->
				</div>
				<!-- container -->
			</div>
			<!-- /MAIN HEADER -->
		</header>
		<!-- /HEADER -->

		<!-- NAVIGATION -->
		<nav id="navigation">
			<!-- container -->
			<div class="container">
				<!-- responsive-nav -->
				<div id="responsive-nav">
					<!-- NAV -->
					<ul class="main-nav nav navbar-nav">
						<li class="active"><a href="{{ route('home') }}">Home</a></li>
						<li><a href="#">Hot Deals</a></li>
						<li><a href="{{ route('frontend.categories.index') }}">Categories</a></li>
						<li><a href="#">About us</a></li>
						<li><a href="#">Contact</a></li>
					</ul>
					<!-- /NAV -->
				</div>
				<!-- /responsive-nav -->
			</div>
			<!-- /container -->
		</nav>
		<!-- /NAVIGATION -->

		<!-- BREADCRUMB -->
		<div id="breadcrumb" class="section">
			<!-- container -->
			<div class="container">
				<!-- row -->
				<div class="row">
					<div class="col-md-12">
						<h3 class="breadcrumb-header">Regular Page</h3>
						<ul class="breadcrumb-tree">
							<li><a href="#">Home</a></li>
							<li class="active">Blank</li>
						</ul>
					</div>
				</div>
				<!-- /row -->
			</div>
			<!-- /container -->
		</div>
		<!-- /BREADCRUMB -->

		<!-- SECTION -->
		<div class="section">
			<!-- container -->
			<div class="container">
                @yield('content')
			</div>
			<!-- /container -->
		</div>
		<!-- /SECTION -->



		<!-- NEWSLETTER -->
		<div id="newsletter" class="section">
			<!-- container -->
			<div class="container">
				<!-- row -->
				<div class="row">
					<div class="col-md-12">
						<div class="newsletter">
							<p>Sign Up for the <strong>NEWSLETTER</strong></p>
							<form>
								<input class="input" type="email" placeholder="Enter Your Email">
								<button class="newsletter-btn"><i class="fa fa-envelope"></i> Subscribe</button>
							</form>
							<ul class="newsletter-follow">
								<li>
									<a href="#"><i class="fa fa-facebook"></i></a>
								</li>
								<li>
									<a href="#"><i class="fa fa-twitter"></i></a>
								</li>
								<li>
									<a href="#"><i class="fa fa-instagram"></i></a>
								</li>
								<li>
									<a href="#"><i class="fa fa-pinterest"></i></a>
								</li>
							</ul>
						</div>
					</div>
				</div>
				<!-- /row -->
			</div>
			<!-- /container -->
		</div>
		<!-- /NEWSLETTER -->

		<!-- FOOTER -->
		<footer id="footer">
			<!-- top footer -->
			<div class="section">
				<!-- container -->
				<div class="container">
					<!-- row -->
					<div class="row">
						<div class="col-md-3 col-xs-6">
							<div class="footer">
								<h3 class="footer-title">About Us</h3>
								<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut.</p>
								<ul class="footer-links">
									<li><a href="#"><i class="fa fa-map-marker"></i>1734 Stonecoal Road</a></li>
									<li><a href="#"><i class="fa fa-phone"></i>+021-95-51-84</a></li>
									<li><a href="#"><i class="fa fa-envelope-o"></i>email@email.com</a></li>
								</ul>
							</div>
						</div>

						<div class="col-md-3 col-xs-6">
							<div class="footer">
								<h3 class="footer-title">Categories</h3>
								<ul class="footer-links">
									<li><a href="#">Hot deals</a></li>
									<li><a href="#">Laptops</a></li>
									<li><a href="#">Smartphones</a></li>
									<li><a href="#">Cameras</a></li>
									<li><a href="#">Accessories</a></li>
								</ul>
							</div>
						</div>

						<div class="clearfix visible-xs"></div>

						<div class="col-md-3 col-xs-6">
							<div class="footer">
								<h3 class="footer-title">Information</h3>
								<ul class="footer-links">
									<li><a href="#">About Us</a></li>
									<li><a href="#">Contact Us</a></li>
									<li><a href="#">Privacy Policy</a></li>
									<li><a href="#">Orders and Returns</a></li>
									<li><a href="#">Terms & Conditions</a></li>
								</ul>
							</div>
						</div>

						<div class="col-md-3 col-xs-6">
							<div class="footer">
								<h3 class="footer-title">Service</h3>
								<ul class="footer-links">
									<li><a href="#">My Account</a></li>
									<li><a href="#">View Cart</a></li>
									<li><a href="#">Wishlist</a></li>
									<li><a href="#">Track My Order</a></li>
									<li><a href="#">Help</a></li>
								</ul>
							</div>
						</div>
					</div>
					<!-- /row -->
				</div>
				<!-- /container -->
			</div>
			<!-- /top footer -->

			<!-- bottom footer -->
			<div id="bottom-footer" class="section">
				<div class="container">
					<!-- row -->
					<div class="row">
						<div class="col-md-12 text-center">
							<ul class="footer-payments">
								<li><a href="#"><i class="fa fa-cc-visa"></i></a></li>
								<li><a href="#"><i class="fa fa-credit-card"></i></a></li>
								<li><a href="#"><i class="fa fa-cc-paypal"></i></a></li>
								<li><a href="#"><i class="fa fa-cc-mastercard"></i></a></li>
								<li><a href="#"><i class="fa fa-cc-discover"></i></a></li>
								<li><a href="#"><i class="fa fa-cc-amex"></i></a></li>
							</ul>
							<span class="copyright">
								<!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
								Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | This template is made with <i class="fa fa-heart-o" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">Colorlib</a>
							<!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
							</span>


						</div>
					</div>
						<!-- /row -->
				</div>
				<!-- /container -->
			</div>
			<!-- /bottom footer -->
		</footer>
		<!-- /FOOTER -->

		<!-- jQuery Plugins -->
        <script src="{{ asset('js/jquery.min.js') }}"></script>
        <script src="{{ asset('js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('js/slick.min.js') }}"></script>
        <script src="{{ asset('js/nouislider.min.js') }}"></script>
        <script src="{{ asset('js/jquery.zoom.min.js') }}"></script>
        <script src="{{ asset('js/main.js') }}"></script>
        <script>
            $(document).ready(function() {
                if (window.location.pathname === '/') {
                    $.getScript('{{ asset('js/countdown.js') }}');
                }
                updateCartDropdown();
                updateCartTotalAndPrice();

                function updateCartTotalAndPrice() {
                    const totalElements = document.querySelectorAll('.cart-product-total p');
                    let cartTotal = 0;

                    totalElements.forEach((totalElement) => {
                        const total = parseFloat(totalElement.textContent.replace('$', ''));
                        if (!isNaN(total)) {
                            cartTotal += total;
                        }
                    });

                    const cartTotalElement = document.querySelector('#cart-total');
                    if (cartTotalElement) {
                        cartTotalElement.textContent = `$${cartTotal.toFixed(2)}`;
                    }

                    const productElements = document.querySelectorAll('.quantity');
                    productElements.forEach((inputElement) => {
                        const productId = inputElement.getAttribute('data-product-id');
                        const newQuantity = parseInt(inputElement.value);
                        const priceElement = document.querySelector(`#price-${productId}`);
                        const totalElement = document.querySelector(`#total-${productId}`);
                        const productPrice = parseFloat(priceElement.textContent.replace('$', ''));

                        if (!isNaN(productPrice)) {
                            const newTotalPrice = productPrice * newQuantity;
                            priceElement.textContent = `$${productPrice.toFixed(2)}`;
                            totalElement.textContent = `$${newTotalPrice.toFixed(2)}`;
                        }
                    });
                }

                function updateQuantityOnPage(inputElement, newQuantity) {
                    inputElement.value = newQuantity;
                    updateCartDropdown();
                    updateCartTotalAndPrice();
                }

                document.addEventListener('click', function(e) {
                    if (e.target.classList.contains('increase') || e.target.classList.contains('decrease')) {
                        e.preventDefault();
                        const productId = e.target.getAttribute('data-product-id');
                        const inputQuantity = document.querySelector(`.quantity[data-product-id="${productId}"]`);
                        let newQuantity = parseInt(inputQuantity.value);

                        if (e.target.classList.contains('increase')) {
                            addToCart(productId, false);
                            updateQuantityOnPage(inputQuantity, newQuantity + 1);
                        } else {
                            deleteFromCart(productId);

                            if (newQuantity > 1) {
                                updateQuantityOnPage(inputQuantity, newQuantity - 1);
                            } else if (newQuantity === 1) {
                                const row = e.target.closest('tr');
                                row.remove();
                            }
                        }
                    }
                });

                function updateQuantityOnPage(inputElement, newQuantity) {
                    inputElement.value = newQuantity;
                    updateCartDropdown();
                    updateCartTotalAndPrice();
                }

                function addToCart(productId, showAlert = true) {
                    $.ajax({
                        url: '/cart/add/' + productId,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (showAlert) {
                                alert('Produkt dodany do koszyka!');
                            }
                            updateCartDropdown(response);
                        },
                        error: function(error) {
                            alert('Wystąpił błąd podczas dodawania produktu do koszyka.');
                        }
                    });
                }

                function deleteFromCart(productId) {
                    $.ajax({
                        url: '/cart/delete/' + productId,
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

                updateCartDropdown();
                attachAddToCartListeners();
                $('button#filter-button').click(getProducts);
                $("#products-per-page").on("change", getProducts);
                $("#sort").on("change", getProducts);

                $('.cart-list').on('click', '.delete', function() {
                    var productId = $(this).data('product-id');
                    console.log('productId:', productId);
                    deleteFromCart(productId);
                });

                function attachAddToCartListeners() {
                    $('.add-to-cart-btn').click(function(event) {
                        event.preventDefault();
                        var productId = $(this).data('product-id');
                        addToCart(productId);
                    });
                }

                const prefix = window.location.pathname.startsWith('/admin') ? '/admin' : '';

                function updateCartDropdown() {
                    $.ajax({
                        url: '{{ route("cart.contents") }}',
                        method: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            const cart = response.cart;
                            console.log(response.cart);

                            const $cartList = $('.cart-list');
                            $cartList.empty();

                            let totalQuantity = 0;

                            $.each(cart, function(productId, item) {
                                const imageSrc = item.product.image;

                                const productHtml = `
                                    <div class="product-widget">
                                        <div class="product-img">
                                            <img src="${imageSrc}" alt="Obrazek">
                                        </div>
                                        <div class="product-body">
                                            <h3 class "product-name"><a href="#">${item.product.name}</a></h3>
                                            <h4 class="product-price"><span class="qty">${item.quantity}x</span> $${item.product.price}</h4>
                                        </div>
                                        <button class="delete" data-product-id="${productId}"><i class="fa fa-close"></i></button>
                                    </div>`;

                                $cartList.append(productHtml);

                                totalQuantity += item.quantity;
                            });

                            $('#totalQty').text(totalQuantity);
                            console.log('Total:', totalQuantity);
                        },
                        error: function(error) {
                            console.log('Wystąpił błąd podczas aktualizowania koszyka:', error);
                        }
                    });
                }

                function getProducts() {
                    const sort = $("#sort").val();
                    const paginate = $("#products-per-page").val();
                    const form = $('form#filter-form').serialize();
                    $.ajax({
                        method: "GET",
                        url: "/categories",
                        data: form + "&" + $.param({ paginate: paginate, sort: sort })
                    })
                    .done(function(response) {
                        $('div#products-container').empty();
                        $.each(response.data, function (index, product) {
                            const imageSrc = product.image ? product.image : 'images/default_image.jpg';
                            const productHtml =
                                `<div class="col-md-4 col-xs-6">
                                    <div class="product">
                                        <div class="product-img">
                                            <img src="${imageSrc}" alt="No photo">
                                            <div class="product-label">
                                                <span class="sale">-30%</span>
                                                <span class="new">NEW</span>
                                            </div>
                                        </div>
                                        <div class="product-body">
                                            <p class="product-category">${product.category_name}</p>
                                            <h3 class="product-name"><a href="#">${product.name}</a></h3>
                                            <h4 class="product-price">${product.price}<del class="product-old-price"></del></h4>
                                            <div class="product-rating">
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                            </div>
                                            <div class="product-btns">
                                                <button class="add-to-wishlist"><i class="fa fa-heart-o"></i><span class="tooltipp">add to wishlist</span></button>
                                                <button class="add-to-compare"><i class="fa fa-exchange"></i><span class="tooltipp">add to compare</span></button>
                                                <button class="quick-view"><i class="fa fa-eye"></i><span class="tooltipp">quick view</span></button>
                                            </div>
                                        </div>
                                        <div class="add-to-cart">
                                            <button class="add-to-cart-btn" data-product-id="${product.id}"><i class="fa fa-shopping-cart"></i>Add to cart</button>
                                        </div>
                                    </div>
                                </div>`;

                            $('div#products-container').append(productHtml);
                        });
                        attachAddToCartListeners();
                    })
                    .fail(function(data) {
                        alert("ERROR");
                    });
                }
            });
        </script>
	</body>
</html>
