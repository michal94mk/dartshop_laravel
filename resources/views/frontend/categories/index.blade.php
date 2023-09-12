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
					</div>
					<!-- /ASIDE -->

					<!-- STORE -->
					<div id="store" class="col-md-9">
						<!-- store top filter -->
						<div class="store-filter clearfix">
							<div class="store-sort">
								<label>
									Sort By:
									<select class="input-select" name="filter[sort]" id="sort">
                                        <option value="price-asc">Price Low to High</option>
                                        <option value="price-desc">Price High to Low</option>
                                        <option value="name-asc">Name A to Z</option>
                                        <option value="name-desc">Name Z to A</option>
									</select>
								</label>

								<label>
                                    Show:
                                    <select class="input-select" id="products-per-page">
                                        <option value="9">9</option>
                                        <option value="12">12</option>
                                        <option value="15">15</option>
                                    </select>
                                </label>
							</div>
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
                                                <img src="{{ asset('storage/images/default_image.jpg') }}" alt="No photo">
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
                                            <form action="{{ route('cart.add', $product->id) }}" method="post">
                                                @csrf
                                                <button class="add-to-cart-btn" data-product-id="{{ $product->id }}">
                                                    <i class="fa fa-shopping-cart"></i> Add to cart
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>


						<!-- /store products -->
						<!-- store bottom filter -->
                        <div class="store-filter clearfix">
                            <span class="store-qty">
                                Showing {{ $products->firstItem() }}-{{ $products->lastItem() }} of {{ $products->total() }} products
                            </span>
                            <ul class="store-pagination">
                                @if ($products->currentPage() > 1)
                                    <li><a href="{{ $products->previousPageUrl() }}"><</a></li>
                                @endif

                                @php
                                    $leftLimit = max($products->currentPage() - 2, 1);
                                    $rightLimit = min($products->currentPage() + 2, $products->lastPage());
                                @endphp

                                @if ($leftLimit > 1)
                                    <li><a href="{{ $products->url(1) }}">1</a></li>
                                    @if ($leftLimit > 2)
                                        <li><span class="dots">...</span></li>
                                    @endif
                                @endif

                                @for ($i = $leftLimit; $i <= $rightLimit; $i++)
                                    <li class="{{ $i == $products->currentPage() ? 'active' : '' }}">
                                        <a href="{{ $products->url($i) }}">{{ $i }}</a>
                                    </li>
                                @endfor

                                @if ($rightLimit < $products->lastPage())
                                    @if ($rightLimit < $products->lastPage() - 1)
                                        <li><span class="dots">...</span></li>
                                    @endif
                                    <li><a href="{{ $products->url($products->lastPage()) }}">{{ $products->lastPage() }}</a></li>
                                @endif

                                @if ($products->currentPage() < $products->lastPage())
                                    <li><a href="{{ $products->nextPageUrl() }}">></a></li>
                                @endif
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
