@extends('layouts.app')

@section('content')
		<!-- SECTION -->
		<div class="section">
			<!-- container -->
			<div class="container">
				<!-- row -->
				<div class="row">
					<!-- ASIDE -->
					<div id="aside" class="col-md-3">
						<!-- aside Widget -->
                        <form id="filter-form">
                            {{-- @csrf --}}
                            <div class="aside">
                                <h3 class="aside-title">Categories</h3>
                                <div class="checkbox-filter">
                                        @foreach($categories as $category)
                                            <div class="input-checkbox">
                                                <input type="checkbox" name="filter[categories][]" id="category-{{ $category->id }}" value="{{ $category->id }}" class="category-checkbox">
                                                <label for="category-{{ $category->id }}">
                                                    <span></span>
                                                    {{ $category->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                </div>
                            </div>
                            <!-- /aside Widget -->

                            <!-- aside Widget -->
                            <div class="aside">
                                <h3 class="aside-title">Price</h3>
                                <div class="price-filter">
                                    <div id="price-slider"></div>
                                    <div class="input-number price-min">
                                        <input id="price-min" type="number" name="filter[price_min]">
                                        <span class="qty-up">+</span>
                                        <span class="qty-down">-</span>
                                    </div>
                                    <span>-</span>
                                    <div class="input-number price-max">
                                        <input id="price-max" type="number" name="filter[price_max]">
                                        <span class="qty-up">+</span>
                                        <span class="qty-down">-</span>
                                    </div>
                                </div>
                            </div>

						<!-- /aside Widget -->

						<!-- aside Widget -->
						<div class="aside">
                            <h3 class="aside-title">Brand</h3>
                            <div class="checkbox-filter">
                                @foreach ($brands as $brand)
                                    <div class="input-checkbox">
                                        <input type="checkbox" name="filter[brands][]" id="brand-{{ $brand->id }}" value="{{ $brand->id }}" class="brand-checkbox">
                                        <label for="brand-{{ $brand->id }}">
                                            <span></span>
                                            {{ $brand->name }}
                                            {{-- <small>({{ $brand->products_count }})</small> --}}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </form>
						<!-- /aside Widget -->
                        <button id="filter-button">Filter</button>
						<!-- aside Widget -->
						<div class="aside">
							<h3 class="aside-title">Top selling</h3>
							<div class="product-widget">
								<div class="product-img">
									<img src="./img/product01.png" alt="">
								</div>
								<div class="product-body">
									<p class="product-category">Category</p>
									<h3 class="product-name"><a href="#">product name goes here</a></h3>
									<h4 class="product-price">$980.00 <del class="product-old-price">$990.00</del></h4>
								</div>
							</div>

							<div class="product-widget">
								<div class="product-img">
									<img src="./img/product02.png" alt="">
								</div>
								<div class="product-body">
									<p class="product-category">Category</p>
									<h3 class="product-name"><a href="#">product name goes here</a></h3>
									<h4 class="product-price">$980.00 <del class="product-old-price">$990.00</del></h4>
								</div>
							</div>

							<div class="product-widget">
								<div class="product-img">
									<img src="./img/product03.png" alt="">
								</div>
								<div class="product-body">
									<p class="product-category">Category</p>
									<h3 class="product-name"><a href="#">product name goes here</a></h3>
									<h4 class="product-price">$980.00 <del class="product-old-price">$990.00</del></h4>
								</div>
							</div>
						</div>
						<!-- /aside Widget -->
					</div>
					<!-- /ASIDE -->

					<!-- STORE -->
					<div id="store" class="col-md-9">
						<!-- store top filter -->
						<div class="store-filter clearfix">
							<div class="store-sort">
								<label>
									Sort By:
									<select class="input-select">
										<option value="0">Popular</option>
										<option value="1">Position</option>
									</select>
								</label>

								<label>
									Show:
									<select class="input-select">
										<option value="0">20</option>
										<option value="1">50</option>
									</select>
								</label>
							</div>
							<ul class="store-grid">
								<li class="active"><i class="fa fa-th"></i></li>
								<li><a href="#"><i class="fa fa-th-list"></i></a></li>
							</ul>
						</div>
						<!-- /store top filter -->

						<!-- store products -->
                        <div id="products-container" class="row">
                            <div class="row">
                                @foreach($products as $product)
                                <div class="col-md-4 col-xs-6">
                                    <div class="product">
                                        <div class="product-img">
                                            @if ($product->image)
                                                <img src="{{ asset($product->image) }}" alt="No photo">
                                            @else
                                                <img src="{{ asset('images/default_image.jpg') }}" alt="No photo">
                                            @endif

                                            <div class="product-label">
                                                <span class="sale">-30%</span>
                                                <span class="new">NEW</span>
                                            </div>
                                        </div>
                                        <div class="product-body">
                                            <p class="product-category">{{ $product->category->name }}</p>
                                            <h3 class="product-name"><a href="#">{{ $product->name }}</a></h3>
                                            <h4 class="product-price">${{ $product->price }} <del class="product-old-price">${{ $product->old_price }}</del></h4>
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
                                            <button class="add-to-cart-btn"><i class="fa fa-shopping-cart"></i> add to cart</button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

						<!-- /store products -->
						<!-- store bottom filter -->
						<div class="store-filter clearfix">
							<span class="store-qty">Showing 20-100 products</span>
							<ul class="store-pagination">
								<li class="active">1</li>
								<li><a href="#">2</a></li>
								<li><a href="#">3</a></li>
								<li><a href="#">4</a></li>
								<li><a href="#"><i class="fa fa-angle-right"></i></a></li>
							</ul>
						</div>
						<!-- /store bottom filter -->
					</div>
					<!-- /STORE -->
				</div>
				<!-- /row -->
			</div>
			<!-- /container -->
		</div>
		<!-- /SECTION -->
@endsection
@section('scripts')
<script>
    $(function() {
        $('button#filter-button').click(function() {
            const form = $('form#filter-form').serialize();
            $.ajax({
                method: "GET",
                url: "/categories",
                data: form
            })
            .done(function(response) {
                $('div#products-container').empty();
                $.each(response.data, function (index, product) {
                    const imageSrc = product.image ? product.image : 'images/default_image.jpg';
                    const html = '<div class="col-md-4 col-xs-6">' +
                        '    <div class="product">' +
                        '        <div class="product-img">' +
                        '            <img src="' + imageSrc + '" alt="No photo">' +
                        '            <div class="product-label">' +
                        '                <span class="sale">-30%</span>' +
                        '                <span class="new">NEW</span>' +
                        '            </div>' +
                        '        </div>' +
                        '        <div class="product-body">' +
                        '            <p class="product-category">' + product.category_name + '</p>' +
                        '            <h3 class="product-name"><a href="#">' + product.name + '</a>' + '</h3>' +
                        '            <h4 class="product-price">' + product.price + '<del class="product-old-price">' + '</del></h4>' +
                        '            <div class="product-rating">' +
                        '                <i class="fa fa-star"></i>' +
                        '                <i class="fa fa-star"></i>' +
                        '                <i class="fa fa-star"></i>' +
                        '                <i class="fa fa-star"></i>' +
                        '                <i class="fa fa-star"></i>' +
                        '            </div>' +
                        '            <div class="product-btns">' +
                        '                <button class="add-to-wishlist"><i class="fa fa-heart-o"></i><span class="tooltipp">add to wishlist</span></button>' +
                        '                <button class="add-to-compare"><i class="fa fa-exchange"></i><span class="tooltipp">add to compare</span></button>' +
                        '                <button class="quick-view"><i class="fa fa-eye"></i><span class="tooltipp">quick view</span></button>' +
                        '            </div>' +
                        '        </div>' +
                        '        <div class="add-to-cart">' +
                        '            <button class="add-to-cart-btn"><i class="fa fa-shopping-cart"></i> add to cart</button>' +
                        '        </div>' +
                        '    </div>' +
                        '</div>';
                    $('div#products-container').append(html);
                });
            })
            .fail(function(data) {
                alert("ERROR");
            });
        });
    });
</script>

@endsection
