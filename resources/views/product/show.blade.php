@extends('layouts.app')

@section('content')
  <!-- User Cart Data (for persistence) -->
  @auth
    <div data-user-cart='@json(auth()->user()->cart_data ?? [])' style="display:none;"></div>
  @endauth

  <nav class="navbar navbar-expand-lg fixed-top navbar-engix" id="mainNav" data-testid="main-nav">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}" data-testid="brand-logo">
        <img src="{{ asset('asset/logo.png') }}" alt="engix CARE" class="navbar-logo" />
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent" aria-controls="navContent" aria-expanded="false" aria-label="Toggle navigation" data-testid="nav-toggle">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navContent">
        <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="{{ url('/') }}#home">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ url('/') }}#products">Products</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ url('/') }}#benefits">Benefits</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ url('/') }}#ingredients">Ingredients</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ url('/') }}#testimonials">Testimonials</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ url('/') }}#faq">FAQ</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ url('/') }}#contact">Contact</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('offers.index') }}"><i class="fa-solid fa-tag me-1"></i>Offers</a></li>
          @auth
            <li class="nav-item"><a class="nav-link" href="{{ route('orders.index') }}"><i class="fa-solid fa-box me-1"></i>My Orders</a></li>
          @endauth
        </ul>
        <div class="d-flex gap-2 nav-cta">
          <a href="#" class="btn btn-primary-engix" id="cartOpen" data-testid="nav-shop-now"><i class="fa-solid fa-bag-shopping me-1"></i>Cart <span class="cart-badge" id="cartBadge" data-testid="cart-badge">0</span></a>
          @auth
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-inline">
              @csrf
              <button type="submit" class="btn btn-ghost-engix"><i class="fa-solid fa-right-from-bracket me-1"></i>Logout</button>
            </form>
          @else
            <a href="{{ route('login') }}" class="btn btn-ghost-engix"><i class="fa-regular fa-user me-1"></i>Login</a>
          @endauth
        </div>
      </div>
    </div>
  </nav>

  <main class="product-detail-page">
    <section class="section py-5" style="padding-top: 120px;">
      <div class="container">
        <div class="product-detail-breadcrumb d-flex flex-wrap gap-2 align-items-center mb-4">
          <a href="{{ url('/') }}" class="text-decoration-none text-muted"><i class="fa-solid fa-house"></i> Home</a>
          <span class="text-muted">/</span>
          <a href="{{ url('/') }}#products" class="text-decoration-none text-muted">Products</a>
          <span class="text-muted">/</span>
          <span class="text-dark fw-semibold">{{ $product->name }}</span>
        </div>

        <div class="row gy-5">
          <div class="col-lg-6">
            @php
              $images = $product->image ?? [];
              if (!is_array($images)) {
                $images = json_decode($images, true) ?: [];
              }
              $images = array_values(array_filter($images));
              $mainImage = count($images) ? Storage::disk('public')->url($images[0]) : asset('asset/210A0234.png');
              $imageUrls = count($images)
                ? array_map(fn($path) => Storage::disk('public')->url($path), $images)
                : [asset('asset/210A0234.png')];

              $videos = $product->video ?? [];
              if (!is_array($videos)) {
                $videos = json_decode($videos, true) ?: [];
              }
              $videos = array_values(array_filter($videos));
              $videoUrls = array_map(fn($path) => Storage::disk('public')->url($path), $videos);

              // Build combined media list: each item has type (image/video) and url
              $mediaList = array_map(fn($url) => ['type' => 'image', 'url' => $url], $imageUrls);
              foreach ($videoUrls as $vUrl) {
                $mediaList[] = ['type' => 'video', 'url' => $vUrl];
              }
            @endphp

            <div class="product-gallery"
              data-images='@json($imageUrls)'
              data-media='@json($mediaList)'>

              <div class="product-gallery-main">
                @php
                  $pt = (int)($product->image_padding_top ?? 0);
                  $pr = (int)($product->image_padding_right ?? 0);
                  $pb = (int)($product->image_padding_bottom ?? 0);
                  $pl = (int)($product->image_padding_left ?? 0);
                @endphp
                <style>
                  /* Desktop only: apply admin-configured padding */
                  @media (min-width: 992px) {
                    .product-main-img {
                      padding-top: {{ $pt }}px !important;
                      padding-right: {{ $pr }}px !important;
                      padding-bottom: {{ $pb }}px !important;
                      padding-left: {{ $pl }}px !important;
                    }
                  }
                </style>
                <div style="width:100%; overflow:hidden; display:flex; align-items:center; justify-content:center; min-height:200px;">
                  <img src="{{ $mainImage }}" alt="{{ $product->name }}"
                    class="product-main-img"
                    style="display:block; width:auto; max-width:100%; height:auto; max-height:480px; object-fit:contain; box-sizing:border-box;" />
                  @if(count($videoUrls) > 0)
                    <video
                      class="product-gallery-video"
                      muted
                      autoplay
                      preload="auto"
                      playsinline
                      loop
                      style="display:none; width:100%; max-height:480px; background:#000; border-radius:0.5rem; object-fit:contain;">
                    </video>
                  @endif
                </div>
              </div>

              {{-- Thumbnail dots/strip --}}
              @if(count($mediaList) > 1)
                <div class="product-gallery-thumbs mt-2 d-flex gap-2 justify-content-center flex-wrap">
                  @foreach($mediaList as $i => $media)
                    <button type="button"
                      class="gallery-thumb {{ $i === 0 ? 'active' : '' }}"
                      data-index="{{ $i }}"
                      aria-label="Slide {{ $i + 1 }}"
                      style="width:52px; height:52px; border:2px solid {{ $i === 0 ? '#b08d57' : '#e0e0e0' }}; border-radius:6px; overflow:hidden; padding:0; background:#f5f5f5; cursor:pointer; flex-shrink:0; transition:border-color .2s;">
                      @if($media['type'] === 'image')
                        <img src="{{ $media['url'] }}" alt="Thumb {{ $i + 1 }}"
                          style="width:100%; height:100%; object-fit:cover; display:block;" />
                      @else
                        <span style="display:flex; align-items:center; justify-content:center; width:100%; height:100%; color:#555; font-size:20px;">
                          <i class="fa-solid fa-play"></i>
                        </span>
                      @endif
                    </button>
                  @endforeach
                </div>
              @endif
            </div>
          </div>

          <div class="col-lg-6">
            <div class="product-detail-intro">
              @if($product->category)
                <span class="cat-tag mb-2 d-inline-block">{{ $product->category }}</span>
              @endif

              <div class="d-flex align-items-center gap-3 mb-3">
                <div class="rating">
                  @for($star = 1; $star <= 5; $star++)
                    <i class="fa-{{ $star <= round($product->rating ?? 0) ? 'solid' : 'regular' }} fa-star"></i>
                  @endfor
                </div>
                @if($product->review_count)
                  <small class="text-muted">{{ number_format($product->rating, 1) }} · {{ number_format($product->review_count) }} reviews</small>
                @else
                  <small class="text-muted">{{ number_format($product->rating, 1) }} rating</small>
                @endif
              </div>

              <h1 class="product-detail-title">{{ $product->name }}</h1>
              @if($product->subtitle)
                <p class="text-muted mb-3">{{ $product->subtitle }}</p>
              @endif

              <div class="price-row mb-4 d-flex align-items-baseline gap-3">
                @if($product->discounted_price)
                  <span class="price">₹{{ number_format($product->discounted_price, 2) }}</span>
                  @if($product->original_price && $product->original_price > $product->discounted_price)
                    <s class="text-muted fs-5">₹{{ number_format($product->original_price, 2) }}</s>
                    @php
                      $discount = round((($product->original_price - $product->discounted_price) / $product->original_price) * 100);
                    @endphp
                    <span class="badge bg-success fs-6">{{ $discount }}% off</span>
                  @endif
                @else
                  <span class="price">₹{{ number_format($product->price, 2) }}</span>
                @endif
                @if($product->unit_value && $product->unit_label)
                  <small class="text-muted">/ {{ $product->unit_value }}{{ $product->unit_label }}</small>
                @endif
              </div>

              @if($product->description)
                <p class="mb-4">{{ $product->description }}</p>
              @endif

              @if(!empty($product->bullet_points))
                <ul class="benefit-list mb-4">
                  @foreach($product->bullet_points as $bullet)
                    <li>{{ $bullet }}</li>
                  @endforeach
                </ul>
              @endif

              <div class="mb-4">
                @if($product->show_stock)
                  <div class="stock-bar mb-2">
                    <div class="stock-fill" style="width: {{ $product->stock > 0 ? min(100, round(($product->stock / 200) * 100)) : 0 }}%;"></div>
                  </div>
                  <p class="stock-text mb-2">
                    <i class="fa-solid fa-fire"></i>
                    @if($product->stock_label)
                      {!! $product->stock_label !!}
                    @elseif($product->stock > 0)
                      <b>{{ $product->stock }} left</b> in stock
                    @else
                      <b>Out of stock</b>
                    @endif
                  </p>
                @endif
                @if($product->warning_text)
                  <div class="prod-note mb-3"><i class="fa-solid fa-circle-info"></i> {{ $product->warning_text }}</div>
                @endif
              </div>

              <div class="d-flex flex-column flex-sm-row gap-3">
                <button class="btn btn-primary-engix add-cart-btn" data-name="{{ $product->name }}" data-price="{{ intval($product->discounted_price ?? $product->price) }}" data-product-id="{{ $product->id }}"><i class="fa-solid fa-cart-plus me-1"></i>Add to Cart</button>
                <a href="{{ url('/') }}#products" class="btn btn-outline-engix">Browse Other Products</a>
              </div>
            </div>
          </div>
        </div>

        <div class="row g-4 mt-5">
          <div class="col-lg-7">
            <div class="section-head mb-4" data-aos="fade-up">
              <span class="eyebrow">Product Details</span>
              <h2 class="section-title">About this formula</h2>
            </div>
            <div class="card p-4 border border-outline-variant/30 bg-white shadow-sm">
              <div class="row gy-3">
                <div class="col-sm-6">
                  <strong>SKU</strong>
                  <p class="text-muted mb-0">{{ $product->sku }}</p>
                </div>
                <div class="col-sm-6">
                  <strong>Status</strong>
                  <p class="text-muted mb-0">{{ $product->status }}</p>
                </div>
                <div class="col-sm-6">
                  <strong>Category</strong>
                  <p class="text-muted mb-0">{{ $product->category }}</p>
                </div>
                <div class="col-sm-6">
                  <strong>Stock</strong>
                  <p class="text-muted mb-0">{{ $product->stock }}</p>
                </div>
              </div>
            </div>

            <div class="mt-5">
              <div class="section-head mb-4" data-aos="fade-up">
                <span class="eyebrow">Ingredient profile</span>
                <h2 class="section-title">Core ingredients</h2>
              </div>
              @if($product->ingredient)
                <div class="ing-card mb-4 p-4">
                  <h5 class="mb-2">{{ $product->ingredient->name ?? 'Main Ingredient' }} @if($product->ingredient->amount)<small class="ing-amount">{{ $product->ingredient->amount }}</small>@endif</h5>
                  @if($product->ingredient->description)
                    <p>{{ $product->ingredient->description }}</p>
                  @endif
                </div>
                @if($product->ingredient->subIngredients->isNotEmpty())
                  <div class="row g-4">
                    @foreach($product->ingredient->subIngredients as $sub)
                      <div class="col-sm-6">
                        <div class="ing-card ing-card--no-img">
                          <h5>{{ $sub->name }}@if($sub->amount)<small class="ing-amount">{{ $sub->amount }}{{ $sub->unit }}</small>@endif</h5>
                          @if($sub->description)
                            <p>{{ $sub->description }}</p>
                          @endif
                        </div>
                      </div>
                    @endforeach
                  </div>
                @endif
              @else
                <div class="text-center text-muted py-4">
                  <i class="fa-solid fa-flask-vial fa-2x mb-3"></i>
                  <p>Ingredient information is not available for this product yet.</p>
                </div>
              @endif
            </div>
          </div>

          <div class="col-lg-5">
            <div class="section-head mb-4" data-aos="fade-up">
              <span class="eyebrow">What customers say</span>
              <h2 class="section-title">Reviews</h2>
            </div>

            <div class="review-cards-list">
              @forelse($product->reviews as $review)
                <div class="review-card-item">
                  <div class="review-card-header">
                    <div class="review-avatar">
                      {{ strtoupper(substr($review->name, 0, 1)) }}
                    </div>
                    <div class="review-meta">
                      <span class="review-author">{{ $review->name }}</span>
                      <span class="review-date">{{ $review->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="review-stars-badge">
                      <span class="review-stars-num">{{ $review->stars }}</span>
                      <i class="fa-solid fa-star"></i>
                    </div>
                  </div>

                  <div class="review-card-stars">
                    @for($star = 1; $star <= 5; $star++)
                      <i class="fa-{{ $star <= $review->stars ? 'solid' : 'regular' }} fa-star"></i>
                    @endfor
                  </div>

                  <div class="review-card-body">
                    @if($review->title)
                      <p class="review-card-title">{{ $review->title }}</p>
                    @endif
                    <p class="review-card-text">{{ $review->body }}</p>
                  </div>
                </div>
              @empty
                <div class="review-empty">
                  <i class="fa-regular fa-comment-dots"></i>
                  <p>No reviews yet. Be the first to share your experience!</p>
                </div>
              @endforelse
            </div>

            @if(session('success'))
              <div class="alert alert-success mt-4">{{ session('success') }}</div>
            @endif
            @if($errors->any())
              <div class="alert alert-danger mt-4">
                <ul class="mb-0">
                  @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            <div class="review-form-card mt-4 p-4 bg-white border border-outline-variant/30 shadow-sm">
              <h4 class="mb-3">Submit your review</h4>
              <form action="{{ route('products.reviews.store', $product) }}" method="POST">
                @csrf
                <div class="mb-3">
                  <label for="review-name" class="form-label">Name</label>
                  <input id="review-name" name="name" type="text" class="form-control" value="{{ old('name') }}" required />
                </div>
                <div class="mb-3">
                  <label for="review-email" class="form-label">Email (optional)</label>
                  <input id="review-email" name="email" type="email" class="form-control" value="{{ old('email') }}" />
                </div>
                <div class="mb-3">
                  <label for="review-title" class="form-label">Review title</label>
                  <input id="review-title" name="title" type="text" class="form-control" value="{{ old('title') }}" />
                </div>
                <div class="mb-3">
                  <label class="form-label">Rating</label>
                  <select name="stars" class="form-select" required>
                    <option value="">Choose rating</option>
                    @for($i = 5; $i >= 1; $i--)
                      <option value="{{ $i }}" @selected(old('stars') == $i)>{{ $i }} star{{ $i > 1 ? 's' : '' }}</option>
                    @endfor
                  </select>
                </div>
                <div class="mb-3">
                  <label for="review-body" class="form-label">Review</label>
                  <textarea id="review-body" name="body" rows="4" class="form-control" required>{{ old('body') }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary-engix">Submit review</button>
              </form>
            </div>

            <div class="mt-4 text-center">
              <a href="{{ url('/') }}#testimonials" class="btn btn-white-engix">Read more reviews</a>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
@endsection
