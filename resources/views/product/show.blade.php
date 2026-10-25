@extends('layouts.app')

@section('title', $product->name . ' | Engix Care')
@section('meta_title', $product->name . ' | Engix Care')
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($product->description ?: ($product->subtitle ?: $product->name)), 155))

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
                <div style="width:100%; overflow:hidden; display:flex; align-items:center; justify-content:center; min-height:200px; position:relative;">
                  <img src="{{ $mainImage }}" alt="{{ $product->name }}"
                    class="product-main-img"
                    style="display:block; width:auto; max-width:100%; height:auto; max-height:480px; object-fit:contain; box-sizing:border-box;" />
                  @if(count($videoUrls) > 0)
                    <video
                      class="product-gallery-video"
                      muted
                      autoplay
                      preload="metadata"
                      playsinline
                      style="display:none; width:100%; max-height:480px; background:#000; border-radius:0.5rem; object-fit:contain;">
                    </video>
                    <button class="video-unmute-btn" title="Sound on/off"
                      style="display:none; position:absolute; bottom:12px; right:12px; background:rgba(0,0,0,0.55); border:none; border-radius:50%; width:38px; height:38px; align-items:center; justify-content:center; cursor:pointer; z-index:10; transition:background .2s;">
                      <i class="fa-solid fa-volume-xmark" style="color:#fff; font-size:16px;"></i>
                    </button>
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

              <a href="#reviews" class="product-rating-link d-inline-flex align-items-center gap-3 mb-3 text-decoration-none">
                <div class="rating">
                  @for($star = 1; $star <= 5; $star++)
                    <i class="fa-{{ $star <= round($product->rating ?? 0) ? 'solid' : 'regular' }} fa-star"></i>
                  @endfor
                </div>
                @if($product->review_count)
                  <small class="text-muted">{{ number_format($product->rating, 1) }} · {{ number_format($product->review_count) }} reviews</small>
                @else
                  <small class="text-muted">Be the first to review</small>
                @endif
              </a>

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

              @if(!empty($product->bullet_points))
                <h2 class="product-highlights-title">Product highlights</h2>
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
                <button class="btn btn-primary-engix add-cart-btn" data-name="{{ $product->name }}" data-price="{{ intval($product->discounted_price ?? $product->price) }}" data-product-id="{{ $product->id }}" data-img="{{ $mainImage }}"><i class="fa-solid fa-cart-plus me-1"></i>Add to Cart</button>
                <a href="{{ url('/') }}#products" class="btn btn-outline-engix">Browse Other Products</a>
              </div>
            </div>
          </div>
        </div>

        <nav class="product-section-nav" role="tablist" aria-label="Product information">
          <button type="button" id="tab-details" class="product-tab {{ session('success') || $errors->any() ? '' : 'is-active' }}" role="tab" aria-selected="{{ session('success') || $errors->any() ? 'false' : 'true' }}" aria-controls="panel-details" tabindex="{{ session('success') || $errors->any() ? '-1' : '0' }}">
            Product details
          </button>
          <button type="button" id="tab-ingredients" class="product-tab" role="tab" aria-selected="false" aria-controls="panel-ingredients" tabindex="-1">
            Ingredients
          </button>
          <button type="button" id="tab-reviews" class="product-tab {{ session('success') || $errors->any() ? 'is-active' : '' }}" role="tab" aria-selected="{{ session('success') || $errors->any() ? 'true' : 'false' }}" aria-controls="panel-reviews" tabindex="{{ session('success') || $errors->any() ? '0' : '-1' }}">
            Reviews ({{ number_format($product->review_count) }})
          </button>
        </nav>

        <div class="product-tab-panels">
          <section class="product-tab-panel {{ session('success') || $errors->any() ? '' : 'is-active' }}" id="panel-details" role="tabpanel" aria-labelledby="tab-details" tabindex="0" @if(session('success') || $errors->any()) hidden @endif>
            <div class="row g-4">
              <div class="col-12" id="overview">
            <div class="section-head mb-4">
              <span class="eyebrow">Product Details</span>
              <h2 class="section-title">Everything you need to know</h2>
            </div>
            @if($product->description)
              <p class="product-overview-copy">{{ $product->description }}</p>
            @endif
            <div class="product-facts-card">
              <div class="row gy-3">
                @if($product->category)
                  <div class="col-sm-6">
                    <span>Category</span>
                    <strong>{{ $product->category }}</strong>
                  </div>
                @endif
                @if($product->unit_value && $product->unit_label)
                  <div class="col-sm-6">
                    <span>Pack size</span>
                    <strong>{{ $product->unit_value }}{{ $product->unit_label }}</strong>
                  </div>
                @endif
              </div>
            </div>

              </div>
            </div>
          </section>

          <section class="product-tab-panel" id="panel-ingredients" role="tabpanel" aria-labelledby="tab-ingredients" tabindex="0" hidden>
            <div class="col-12" id="ingredients">
              <div class="section-head mb-4">
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
          </section>

          <section class="product-tab-panel {{ session('success') || $errors->any() ? 'is-active' : '' }}" id="panel-reviews" role="tabpanel" aria-labelledby="tab-reviews" tabindex="0" @unless(session('success') || $errors->any()) hidden @endunless>
            <div class="product-review-panel" id="reviews">
            <div class="section-head mb-4">
              <span class="eyebrow">What customers say</span>
              <h2 class="section-title">Reviews</h2>
            </div>

            <div class="product-review-summary">
              <div class="product-review-score">{{ number_format($product->rating, 1) }}</div>
              <div>
                <div class="rating">
                  @for($star = 1; $star <= 5; $star++)
                    <i class="fa-{{ $star <= round($product->rating) ? 'solid' : 'regular' }} fa-star"></i>
                  @endfor
                </div>
                <span>{{ number_format($product->review_count) }} customer {{ $product->review_count === 1 ? 'review' : 'reviews' }}</span>
              </div>
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
          </section>

        </div>

        <section class="product-trust-strip" aria-label="Product and shopping information">
          <div class="product-trust-item">
            <span class="product-trust-icon" aria-hidden="true"><i class="fa-solid fa-leaf"></i></span>
            <span>Thoughtfully<br />Formulated</span>
          </div>
          <div class="product-trust-item">
            <span class="product-trust-icon" aria-hidden="true"><i class="fa-solid fa-flask"></i></span>
            <span>Ingredient<br />Details</span>
          </div>
          <div class="product-trust-item">
            <span class="product-trust-icon" aria-hidden="true"><i class="fa-solid fa-truck-fast"></i></span>
            <span>Fast<br />Delivery</span>
          </div>
          <div class="product-trust-item">
            <span class="product-trust-icon" aria-hidden="true"><i class="fa-solid fa-shield-halved"></i></span>
            <span>Secure<br />Payments</span>
          </div>
        </section>

        @if($relatedProducts->isNotEmpty())
          <section class="related-products-section" id="related-products">
            <div class="section-head mb-4">
              <span class="eyebrow">Explore more</span>
              <h2 class="section-title">You may also like</h2>
            </div>
            <div class="row g-4">
              @foreach($relatedProducts as $relatedProduct)
                @php
                  $relatedImages = $relatedProduct->image ?? [];
                  $relatedImage = is_array($relatedImages) && count($relatedImages)
                    ? Storage::disk('public')->url($relatedImages[0])
                    : asset('asset/210A0234.png');
                  $relatedPrice = $relatedProduct->discounted_price ?? $relatedProduct->price;
                @endphp
                <div class="col-sm-6 col-lg-3">
                  <article class="related-product-card">
                    <a href="{{ route('products.show', $relatedProduct) }}" class="related-product-image">
                      <img src="{{ $relatedImage }}" alt="{{ $relatedProduct->name }}" loading="lazy" />
                    </a>
                    <div class="related-product-body">
                      @if($relatedProduct->category)
                        <span class="cat-tag">{{ $relatedProduct->category }}</span>
                      @endif
                      <h3>{{ $relatedProduct->name }}</h3>
                      @if($relatedProduct->subtitle)
                        <p>{{ $relatedProduct->subtitle }}</p>
                      @endif
                      <div class="related-product-footer">
                        <strong>₹{{ number_format($relatedPrice, 2) }}</strong>
                        <a href="{{ route('products.show', $relatedProduct) }}" aria-label="View {{ $relatedProduct->name }}">
                          <i class="fa-solid fa-arrow-right"></i>
                        </a>
                      </div>
                    </div>
                  </article>
                </div>
              @endforeach
            </div>
          </section>
        @endif
      </div>
    </section>
  </main>
  <footer class="footer" data-testid="footer">
    <div class="container">
      <div class="row g-4">
        <div class="col-lg-4">
          <a href="{{ url('/') }}#home" class="footer-brand"><img src="{{ asset('asset/logo.png') }}" alt="engix CARE" style="height:40px;width:auto;object-fit:contain;margin-right:10px;" /><span>engix<span class="brand-accent">CARE</span></span></a>
          <p class="footer-desc">Premium effervescent health supplements crafted for real Indian lifestyles. Doctor formulated, WHO-GMP &amp; HACCP certified manufacturing, FSSAI approved.</p>
          <div class="social">
            <a href="#" aria-label="Facebook" data-testid="social-fb"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="#" aria-label="Instagram" data-testid="social-ig"><i class="fa-brands fa-instagram"></i></a>
          </div>
        </div>
        <div class="col-6 col-lg-2">
          <h6 class="footer-title">Quick Links</h6>
          <ul class="footer-links">
            <li><a href="{{ url('/') }}#home">Home</a></li>
            <li><a href="{{ url('/') }}#products">Products</a></li>
            <li><a href="{{ url('/') }}#benefits">Benefits</a></li>
            <li><a href="{{ url('/') }}#ingredients">Ingredients</a></li>
            <li><a href="{{ url('/') }}#faq">FAQ</a></li>
          </ul>
        </div>
        <div class="col-6 col-lg-2">
          <h6 class="footer-title">Policies</h6>
          <ul class="footer-links">
            <li><a href="#">Privacy Policy</a></li>
            <li><a href="#">Refund Policy</a></li>
            <li><a href="#">Terms of Service</a></li>
            <li><a href="#">Shipping</a></li>
            <li><a href="{{ url('/') }}#contact">Contact</a></li>
          </ul>
        </div>
        <div class="col-lg-4">
          <h6 class="footer-title">Brand &amp; Manufacturer Info</h6>
          <div style="font-size:0.82rem;color:#94a3b8;line-height:1.65;margin-bottom:16px;">
            <p style="margin:0 0 8px;"><strong style="color:#cbd5e1;">Marketed by:</strong><br />
            VHK International<br />
            Shop No-3, Surjit Colony, Bapunagar,<br />
            Ahmedabad, Gujarat – 380024</p>
            <p style="margin:0 0 8px;"><strong style="color:#cbd5e1;">Manufactured by:</strong><br />
            Growequal LTD. <span style="font-size:0.75rem;opacity:0.8;">(WHO-GMP &amp; HACCP Certified)</span><br />
            D-15, Sahjanand Business Park, S.P. Ring Road,<br />
            Nikol, Ahmedabad, Gujarat – 382350, India</p>
            <p style="margin:0;"><strong style="color:#cbd5e1;">Customer Care:</strong><br />
            <a href="mailto:carewithengix@gmail.com" style="color:#22C55E;text-decoration:none;">carewithengix@gmail.com</a></p>
          </div>
        </div>
      </div>
      <hr class="footer-divider" />
      <div class="row g-3 mb-4 justify-content-lg-end">
        <div class="col-lg-6 d-flex align-items-center justify-content-lg-end">
          <div style="text-align:right;">
            <p style="font-size:0.75rem;color:#64748b;margin:0 0 4px;letter-spacing:0.06em;text-transform:uppercase;font-weight:600;">Certified &amp; Trusted</p>
            <div style="display:flex;gap:14px;align-items:center;justify-content:flex-end;flex-wrap:wrap;">
              <span style="background:rgba(255,255,255,0.08);border-radius:8px;padding:6px 12px;font-size:0.75rem;color:#cbd5e1;font-weight:600;">WHO-GMP</span>
              <span style="background:rgba(255,255,255,0.08);border-radius:8px;padding:6px 12px;font-size:0.75rem;color:#cbd5e1;font-weight:600;">HACCP</span>
              <span style="background:rgba(255,255,255,0.08);border-radius:8px;padding:6px 12px;font-size:0.75rem;color:#cbd5e1;font-weight:600;">FSSAI</span>
            </div>
          </div>
        </div>
      </div>
      <hr class="footer-divider" style="margin-top:0;" />
      <div class="footer-bottom">
        <p>&copy; <span id="year"></span> Engix Care. All rights reserved.</p>
        <p>Made with <i class="fa-solid fa-heart" style="color:#22C55E"></i> for a healthier tomorrow.</p>
      </div>
    </div>
  </footer>

  <button id="backToTop" class="back-to-top" aria-label="Back to top" data-testid="back-to-top"><i class="fa-solid fa-arrow-up"></i></button>

  <script>
    (function () {
      var tabList = document.querySelector('.product-section-nav[role="tablist"]');
      if (!tabList) return;

      var tabs = Array.from(tabList.querySelectorAll('[role="tab"]'));
      var panels = tabs.map(function (tab) {
        return document.getElementById(tab.getAttribute('aria-controls'));
      });

      function activateTab(tab, moveFocus) {
        tabs.forEach(function (item, index) {
          var selected = item === tab;
          item.classList.toggle('is-active', selected);
          item.setAttribute('aria-selected', selected ? 'true' : 'false');
          item.setAttribute('tabindex', selected ? '0' : '-1');
          panels[index].hidden = !selected;
          panels[index].classList.toggle('is-active', selected);
        });

        if (moveFocus) tab.focus();
      }

      tabs.forEach(function (tab, index) {
        tab.addEventListener('click', function () {
          activateTab(tab, false);
        });

        tab.addEventListener('keydown', function (event) {
          var nextIndex = index;
          if (event.key === 'ArrowRight') nextIndex = (index + 1) % tabs.length;
          else if (event.key === 'ArrowLeft') nextIndex = (index - 1 + tabs.length) % tabs.length;
          else if (event.key === 'Home') nextIndex = 0;
          else if (event.key === 'End') nextIndex = tabs.length - 1;
          else return;

          event.preventDefault();
          activateTab(tabs[nextIndex], true);
        });
      });

      var reviewLink = document.querySelector('.product-rating-link');
      var reviewTab = document.getElementById('tab-reviews');
      if (reviewLink && reviewTab) {
        reviewLink.addEventListener('click', function (event) {
          event.preventDefault();
          activateTab(reviewTab, false);
          tabList.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
      }
    })();
  </script>
@endsection
