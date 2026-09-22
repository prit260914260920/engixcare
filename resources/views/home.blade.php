@extends('layouts.app')
@section('content')
<!-- Loading Screen -->
  <div id="preloader" data-testid="preloader">
    <div class="loader-inner">
      <img src="{{ asset('asset/logo.png') }}" alt="Engix Care" class="loader-logo" />
    </div>
  </div>

  <!-- Hidden root for React (kept to avoid console errors from CRA bundle) -->
  <div id="root" style="display:none"></div>

  <!-- User Cart Data (for persistence) -->
  @auth
    <div data-user-cart='@json(auth()->user()->cart_data ?? [])' style="display:none;"></div>
  @endauth

  <!-- ============ HEADER ============ -->
  <nav class="navbar navbar-expand-lg fixed-top navbar-engix" id="mainNav" data-testid="main-nav">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="#home" data-testid="brand-logo">
        <img src="{{ asset('asset/logo.png') }}" alt="engix CARE" class="navbar-logo" />
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent" aria-controls="navContent" aria-expanded="false" aria-label="Toggle navigation" data-testid="nav-toggle">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navContent">
        <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link active" href="#home" data-testid="nav-home">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="#products" data-testid="nav-products">Products</a></li>
          <li class="nav-item"><a class="nav-link" href="#benefits" data-testid="nav-benefits">Benefits</a></li>
          <li class="nav-item"><a class="nav-link" href="#ingredients" data-testid="nav-ingredients">Ingredients</a></li>
          <li class="nav-item"><a class="nav-link" href="#testimonials" data-testid="nav-testimonials">Testimonials</a></li>
          <li class="nav-item"><a class="nav-link" href="#faq" data-testid="nav-faq">FAQ</a></li>
          <li class="nav-item"><a class="nav-link" href="#contact" data-testid="nav-contact">Contact</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('offers.index') }}" data-testid="nav-offers"><i class="fa-solid fa-tag me-1"></i>Offers</a></li>
          @auth
            <li class="nav-item"><a class="nav-link" href="{{ route('orders.index') }}" data-testid="nav-orders"><i class="fa-solid fa-box me-1"></i>My Orders</a></li>
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
            <a href="{{ route('login') }}" class="btn btn-ghost-engix" data-testid="nav-login"><i class="fa-regular fa-user me-1"></i>Login</a>
          @endauth
        </div>
      </div>
    </div>
  </nav>

  <!-- ============ HERO ============ -->
  <section class="hero" id="home" data-testid="hero-section">
    <div class="hero-bg-shape"></div>
    <div class="container">
      <div class="row align-items-center g-5">
        <div class="col-lg-6" data-aos="fade-right">
          <span class="hero-pill" data-testid="hero-pill"><i class="fa-solid fa-user-doctor"></i> {{ $hero->pill_text }}</span>
          <h1 class="hero-title" data-testid="hero-title">{{ $hero->title_main }} <span class="text-gradient">{{ $hero->title_gradient }}</span></h1>
          <p class="hero-sub" data-testid="hero-sub">{{ $hero->subtitle }}</p>
          <div class="hero-cta d-flex flex-wrap gap-3">
            <a href="{{ $hero->cta_primary_url }}" class="btn btn-primary-engix btn-lg" data-testid="hero-buy-now"><i class="fa-solid fa-cart-shopping me-2"></i>{{ $hero->cta_primary_label }}</a>
            <a href="{{ $hero->cta_secondary_url }}" class="btn btn-outline-engix btn-lg" data-testid="hero-explore"><i class="fa-solid fa-flask me-2"></i>{{ $hero->cta_secondary_label }}</a>
          </div>

          <!-- Key minerals strip -->
          @if(!empty($hero->minerals))
          <div class="minerals-strip d-flex flex-wrap gap-3 my-3">
            @foreach($hero->minerals as $mineral)
              <div class="mineral-item"><strong>{{ $mineral['label'] }}</strong><span>{{ $mineral['value'] }}</span></div>
            @endforeach
          </div>
          @endif

          @if(!empty($hero->trust_badges))
          <div class="trust-row" data-testid="trust-badges">
            @foreach($hero->trust_badges as $badge)
              <div class="trust-item"><i class="{{ $badge['icon'] }}"></i><span>{{ $badge['label'] }}</span></div>
            @endforeach
          </div>
          @endif
        </div>

        <div class="col-lg-6" data-aos="fade-left">
          <div class="hero-image-wrap">
            @if($hero->badge_rating)
              <div class="hero-badge float-badge-1"><i class="fa-solid fa-star"></i> {{ $hero->badge_rating }}</div>
            @endif
            @if($hero->badge_lab)
              <div class="hero-badge float-badge-2"><i class="fa-solid fa-vial-circle-check"></i> {{ $hero->badge_lab }}</div>
            @endif
            <img src="{{ $hero->imageUrl() }}" alt="engix CARE Effervescent Tablets" class="hero-image" data-testid="hero-image" />
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============ COUNTERS ============ -->
  <section class="counters" data-testid="counters-section">
    <div class="container">
      <div class="row g-4 text-center">
        <div class="col-6 col-md-3" data-aos="zoom-in">
          <div class="counter-box">
            <div class="counter-num" data-count="{{ $statHappyCustomers }}" data-testid="counter-customers">0</div>
            <p>Happy Customers</p>
          </div>
        </div>
        <div class="col-6 col-md-3" data-aos="zoom-in" data-aos-delay="100">
          <div class="counter-box">
            <div class="counter-num" data-count="{{ $statPremiumProducts }}" data-testid="counter-products">0</div>
            <p>Premium Products</p>
          </div>
        </div>
        <div class="col-6 col-md-3" data-aos="zoom-in" data-aos-delay="200">
          <div class="counter-box">
            <div class="counter-num" data-count="{{ $statSatisfactionRate }}" data-testid="counter-satisfaction">0</div><span class="pct">%</span>
            <p>Satisfaction Rate</p>
          </div>
        </div>
        <div class="col-6 col-md-3" data-aos="zoom-in" data-aos-delay="300">
          <div class="counter-box">
            <div class="counter-num" data-count="15" data-testid="counter-years">0</div><span class="pct">+</span>
            <p>Years of Research</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============ WELLNESS QUIZ ============ -->
  <section class="section wellness-quiz" data-testid="quiz-section">
    <div class="container">
      <div class="quiz-wrap" data-aos="zoom-in">
        <div class="quiz-header">
          <span class="eyebrow"><i class="fa-solid fa-wand-magic-sparkles"></i> Personalised Wellness</span>
          <h2 class="section-title">Find Your Perfect Formula<br /><span class="text-gradient">In 30 Seconds</span></h2>
          <p class="section-sub">Answer 3 quick questions — we'll pick the right supplement for you.</p>
          <div class="quiz-progress">
            <div class="qp-bar" id="qpBar" style="width:0%"></div>
            <span id="qpText">Question 1 of 3</span>
          </div>
        </div>

        <div class="quiz-slides" id="quizSlides" data-testid="quiz-slides">
          <!-- Slide 1 -->
          <div class="quiz-slide active" data-slide="0">
            <h4>What's your biggest daily challenge?</h4>
            <div class="quiz-options">
              <button class="quiz-opt" data-q1="energy" data-testid="quiz-q1-energy"><i class="fa-solid fa-battery-quarter"></i><span>Low Energy &amp; Fatigue</span></button>
              <button class="quiz-opt" data-q1="nerve" data-testid="quiz-q1-nerve"><i class="fa-solid fa-brain"></i><span>Brain Fog &amp; Weak Nerves</span></button>
              <button class="quiz-opt" data-q1="hydration" data-testid="quiz-q1-hydration"><i class="fa-solid fa-droplet"></i><span>Poor Hydration &amp; Recovery</span></button>
              <button class="quiz-opt" data-q1="both" data-testid="quiz-q1-both"><i class="fa-solid fa-circle-half-stroke"></i><span>All of the Above</span></button>
            </div>
          </div>
          <!-- Slide 2 -->
          <div class="quiz-slide" data-slide="1">
            <h4>How would you describe your energy levels?</h4>
            <div class="quiz-options quiz-2col">
              <button class="quiz-opt" data-q2="crash" data-testid="quiz-q2-crash"><i class="fa-solid fa-bolt-lightning"></i><span>I crash by afternoon</span></button>
              <button class="quiz-opt" data-q2="low" data-testid="quiz-q2-low"><i class="fa-solid fa-arrow-down"></i><span>Consistently low all day</span></button>
              <button class="quiz-opt" data-q2="moderate" data-testid="quiz-q2-moderate"><i class="fa-solid fa-gauge-simple"></i><span>Moderate, could be better</span></button>
              <button class="quiz-opt" data-q2="good" data-testid="quiz-q2-good"><i class="fa-solid fa-face-smile"></i><span>Good, just need B12 support</span></button>
            </div>
          </div>
          <!-- Slide 3 -->
          <div class="quiz-slide" data-slide="2">
            <h4>What matters most to you right now?</h4>
            <div class="quiz-options quiz-2col">
              <button class="quiz-opt" data-q3="recharge" data-testid="quiz-q3-recharge"><i class="fa-solid fa-plug-circle-bolt"></i><span>Quick recharge &amp; hydration</span></button>
              <button class="quiz-opt" data-q3="nerve" data-testid="quiz-q3-nerve"><i class="fa-solid fa-dna"></i><span>Nerve &amp; metabolic health</span></button>
              <button class="quiz-opt" data-q3="active" data-testid="quiz-q3-active"><i class="fa-solid fa-person-running"></i><span>Stay active &amp; recover faster</span></button>
              <button class="quiz-opt" data-q3="overall" data-testid="quiz-q3-overall"><i class="fa-solid fa-star"></i><span>Overall daily wellness</span></button>
            </div>
          </div>
          <!-- Result -->
          <div class="quiz-slide quiz-result" data-slide="3">
            <div class="qr-icon"><i class="fa-solid fa-vial-circle-check"></i></div>
            <span class="qr-pill">Match Ready</span>
            <h4>Your perfect pick is <span class="text-gradient" id="qrStackName">Ready</span></h4>
            <p id="qrStackDesc">Based on your answers, here's your personalised Engix Care recommendation.</p>
            <div class="qr-stack" id="qrStack"></div>
            <div class="qr-price"><span id="qrPrice">₹299</span> <s id="qrOld">₹399</s> <b>Save 25%</b></div>
            <div class="qr-cta">
              <button class="btn btn-primary-engix btn-lg add-cart-btn" data-name="Your Recommended Product" id="qrAddBtn" data-testid="qr-add"><i class="fa-solid fa-cart-plus me-1"></i>Add to Cart</button>
              <button class="btn btn-ghost-engix" id="qrRetake" data-testid="qr-retake"><i class="fa-solid fa-rotate me-1"></i>Retake Quiz</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============ FROM LAB TO YOU ============ -->
  <section class="section lab-journey" data-testid="lab-section">
    <div class="container">
      <div class="section-head text-center" data-aos="fade-up">
        <span class="eyebrow">From Lab to You</span>
        <h2 class="section-title">Manufactured at FSSAI-Certified <span class="text-gradient">Facilities</span></h2>
        <p class="section-sub">Every bottle passes through 4 rigorous quality gates before it reaches your door.</p>
      </div>

      <div class="lab-track" data-aos="fade-up">
        <div class="lab-line"></div>
        <div class="lab-step">
          <span class="lab-num">STEP 1</span>
          <div class="lab-icon"><i class="fa-solid fa-flask"></i></div>
          <h5>Formulation</h5>
          <p>Ratios designed and validated by our doctor-led nutrition team.</p>
        </div>
        <div class="lab-step">
          <span class="lab-num">STEP 2</span>
          <div class="lab-icon"><i class="fa-solid fa-industry"></i></div>
          <h5>Manufacturing</h5>
          <p>Produced at GMP/FSSAI-certified plants with strict quality control.</p>
        </div>
        <div class="lab-step">
          <span class="lab-num">STEP 3</span>
          <div class="lab-icon"><i class="fa-solid fa-microscope"></i></div>
          <h5>Lab Testing</h5>
          <p>Every batch third-party tested for purity, potency and safety.</p>
        </div>
        <div class="lab-step">
          <span class="lab-num">STEP 4</span>
          <div class="lab-icon"><i class="fa-solid fa-box-open"></i></div>
          <h5>Quality Sealed</h5>
          <p>Tamper-proof packaging shipped within 24 hours across India.</p>
        </div>
      </div>

      <div class="doc-strip" data-aos="fade-up">
        <img src="https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=400&q=80" alt="Dr. Aarav Sharma" />
        <div class="doc-info">
          <span class="doc-eyebrow">Formulated by</span>
          <h5>Dr. Aarav Sharma <small>BAMS, Nutrition Specialist</small></h5>
          <p>"Every Engix Care formula follows clinically validated doses — no fillers, no shortcuts. Just science-backed effervescent nutrition your body actually absorbs."</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ============ FEATURES ============ -->
  <section class="section features" id="benefits" data-testid="features-section">
    <div class="container">
      <div class="section-head text-center" data-aos="fade-up">
        <span class="eyebrow">Why Engix Care</span>
        <h2 class="section-title">Health Benefits You Can <span class="text-gradient">Feel</span></h2>
        <p class="section-sub">Every capsule crafted for your daily wellness — pure, potent, and precise.</p>
      </div>

      <div class="row g-4 mt-2">
        <div class="col-md-6 col-lg-4" data-aos="fade-up"><div class="feature-card"><div class="icon-wrap green"><i class="fa-solid fa-shield-virus"></i></div><h5>Immunity Booster</h5><p>Fortify your body's natural defense against seasonal threats.</p></div></div>
        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100"><div class="feature-card"><div class="icon-wrap blue"><i class="fa-solid fa-capsules"></i></div><h5>Rich in Vitamins</h5><p>Full spectrum vitamins & essential minerals in every dose.</p></div></div>
        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="200"><div class="feature-card"><div class="icon-wrap green"><i class="fa-solid fa-seedling"></i></div><h5>Natural Ingredients</h5><p>Plant based & bioavailable actives — no shortcuts.</p></div></div>
        <div class="col-md-6 col-lg-4" data-aos="fade-up"><div class="feature-card"><div class="icon-wrap blue"><i class="fa-solid fa-flask-vial"></i></div><h5>No Harmful Chemicals</h5><p>Free from parabens, GMOs and artificial fillers.</p></div></div>
        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100"><div class="feature-card"><div class="icon-wrap green"><i class="fa-solid fa-stethoscope"></i></div><h5>Doctor Recommended</h5><p>Formulated with guidance from certified nutritionists.</p></div></div>
        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="200"><div class="feature-card"><div class="icon-wrap blue"><i class="fa-solid fa-truck-fast"></i></div><h5>Fast Delivery</h5><p>Doorstep delivery across India in 2–4 business days.</p></div></div>
      </div>
    </div>
  </section>

  <!-- ============ PRODUCTS ============ -->
  <section class="section products bg-soft" id="products" data-testid="products-section">
    <div class="container">
      <div class="section-head text-center" data-aos="fade-up">
        <span class="eyebrow">Our Complete Range</span>
        <h2 class="section-title">Science-Backed. <span class="text-gradient">Results-Proven.</span></h2>
        <p class="section-sub">Effervescent formulas doctor-formulated for real Indian lifestyles.</p>
      </div>

      <div class="row g-4 mt-2">
        @forelse($products as $i => $product)
          @php
            $delay     = ($i % 3) * 80;
            $images    = $product->image ?? [];
            $imgUrl    = count($images)
                ? \Illuminate\Support\Facades\Storage::disk('public')->url($images[0])
                : 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&w=600&q=80';

            // Badge CSS class based on badge_color
            $badgeClass = match($product->badge_color) {
                'green'  => 'new',
                'blue'   => 'hot',
                'red'    => 'sale',
                'purple' => 'premium',
                default  => 'sale',
            };

            // Stock bar width (max 100%, capped at 200 for visual scale)
            $stockPct = $product->stock > 0 ? min(100, round(($product->stock / 200) * 100)) : 0;
          @endphp

          <div class="col-sm-6 col-lg-4" data-aos="fade-up" {{ $delay ? 'data-aos-delay="'.$delay.'"' : '' }}>
            <div class="product-card" id="product-{{ $product->id }}" data-testid="product-card-{{ $product->id }}">

              {{-- Badge --}}
              @if($product->badge_label)
                <div class="product-badge {{ $badgeClass }}">{{ $product->badge_label }}</div>
              @endif

              {{-- Image --}}
              <div class="product-img">
                <div class="product-quick">
                  <a class="pq-btn" href="{{ route('products.show', $product) }}" title="Quick view"><i class="fa-regular fa-eye"></i></a>
                  <button class="pq-btn" title="Wishlist"><i class="fa-regular fa-heart"></i></button>
                </div>
                <img src="{{ $imgUrl }}" alt="{{ $product->name }}" />
              </div>

              {{-- Body --}}
              <div class="product-body">
                @if($product->category)
                  <span class="cat-tag">{{ $product->category }}</span>
                @endif

                {{-- Star rating --}}
                @if($product->rating > 0)
                  <div class="rating">
                    @for($s = 1; $s <= 5; $s++)
                      <i class="fa-{{ $s <= round($product->rating) ? 'solid' : 'regular' }} fa-star"></i>
                    @endfor
                    @if($product->review_count)
                      <small>({{ number_format($product->review_count) }})</small>
                    @endif
                  </div>
                @endif

                <h5>{{ $product->name }}</h5>

                @if($product->subtitle)
                  <p class="prod-desc">{{ $product->subtitle }}</p>
                @endif

                {{-- Bullet points --}}
                @if(!empty($product->bullet_points))
                  <ul class="benefit-list">
                    @foreach($product->bullet_points as $bullet)
                      <li>{{ $bullet }}</li>
                    @endforeach
                  </ul>
                @endif

                {{-- Warning / note --}}
                @if($product->warning_text)
                  <div class="prod-note">
                    <i class="fa-solid fa-circle-info"></i> {{ $product->warning_text }}
                  </div>
                @endif

                {{-- Stock bar --}}
                @if($product->show_stock)
                <div class="stock-bar">
                  <div class="stock-fill" style="width:{{ $stockPct }}%"></div>
                </div>
                <p class="stock-text">
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

                {{-- Price display --}}
                <div class="price-row my-3 d-flex align-items-baseline gap-2">
                  @if($product->discounted_price)
                    <span class="price fw-bold">₹{{ number_format($product->discounted_price, 0) }}</span>
                    @if($product->original_price && $product->original_price > $product->discounted_price)
                      <s class="text-muted" style="font-size:0.88rem;">₹{{ number_format($product->original_price, 0) }}</s>
                      @php
                        $discount = round((($product->original_price - $product->discounted_price) / $product->original_price) * 100);
                      @endphp
                      <span class="badge bg-success" style="font-size:0.72rem;">{{ $discount }}% off</span>
                    @endif
                  @else
                    <span class="price fw-bold">₹{{ number_format($product->price, 0) }}</span>
                  @endif
                </div>

                <button class="btn btn-primary-engix w-100 add-cart-btn"
                  data-name="{{ $product->name }}"
                  data-price="{{ intval($product->discounted_price ?? $product->price) }}"
                  data-product-id="{{ $product->id }}"
                  data-testid="add-cart-{{ $product->id }}">
                  <i class="fa-solid fa-cart-plus me-1"></i>Add to Cart
                </button>
                <a href="{{ route('products.show', $product) }}" class="btn btn-outline-engix w-100 text-center mt-2"
                  data-testid="view-detail-{{ $product->id }}">
                  <i class="fa-solid fa-eye me-1"></i>View Details
                </a>
              </div>

            </div>
          </div>
        @empty
          <div class="col-12 text-center py-5 text-muted">
            <i class="fa-solid fa-box-open fa-2x mb-3"></i>
            <p>No products available right now. Check back soon!</p>
          </div>
        @endforelse
      </div>
    </div>
  </section>

  <!-- ============ WHY CHOOSE ============ -->
  <section class="section why-choose" data-testid="why-section">
    <div class="container">
      <div class="row align-items-center g-5">
        <div class="col-lg-6" data-aos="fade-right">
          <img src="{{ asset('asset/DSC09011.png') }}" alt="engix CARE products" class="why-image" />
        </div>
        <div class="col-lg-6" data-aos="fade-left">
          <span class="eyebrow">Why Choose Us</span>
          <h2 class="section-title">The Engix Care <span class="text-gradient">Difference</span></h2>
          <p class="section-sub mb-4">A brand built on science, integrity and the promise of purity.</p>
          <div class="row g-3">
            <div class="col-sm-6"><div class="why-item"><i class="fa-solid fa-vial-circle-check"></i><span>Clinically Tested</span></div></div>
            <div class="col-sm-6"><div class="why-item"><i class="fa-solid fa-award"></i><span>Premium Ingredients</span></div></div>
            <div class="col-sm-6"><div class="why-item"><i class="fa-solid fa-globe"></i><span>International Quality</span></div></div>
            <div class="col-sm-6"><div class="why-item"><i class="fa-solid fa-people-group"></i><span>Trusted by Thousands</span></div></div>
            <div class="col-sm-6"><div class="why-item"><i class="fa-solid fa-industry"></i><span>Certified Manufacturing</span></div></div>
            <div class="col-sm-6"><div class="why-item"><i class="fa-solid fa-truck-fast"></i><span>Fast Delivery</span></div></div>
            <div class="col-sm-6"><div class="why-item"><i class="fa-solid fa-lock"></i><span>Secure Payment</span></div></div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============ INGREDIENTS ============ -->
  <section class="section ingredients bg-soft" id="ingredients" data-testid="ingredients-section">
    <div class="container">
      <div class="section-head text-center" data-aos="fade-up">
        <span class="eyebrow">Pure Science</span>
        <h2 class="section-title">Powerful <span class="text-gradient">Ingredients</span></h2>
        <p class="section-sub">Actual ingredients straight from our product labels — nothing hidden.</p>
      </div>

      @php
        $ingProducts = $products->filter(fn($p) => isset($ingredientsByProduct[$p->id]));
      @endphp

      @if($ingProducts->isEmpty())
        <p class="text-center text-muted mt-4">No ingredient information available yet.</p>
      @else

        {{-- ── Product Tab Switcher ── --}}
        <div class="ing-tabs" data-aos="fade-up">
          @foreach($ingProducts->values() as $idx => $p)
            <button class="ing-tab {{ $idx === 0 ? 'active' : '' }}"
              data-tab="ing-product-{{ $p->id }}"
              data-testid="ing-tab-{{ $p->id }}">
              <i class="fa-solid fa-flask-vial"></i>
              {{ $p->name }}
              @if($p->subtitle)<small>{{ $p->subtitle }}</small>@endif
            </button>
          @endforeach
        </div>

        {{-- ── Per-product panels ── --}}
        @foreach($ingProducts->values() as $idx => $p)
          @php $ing = $ingredientsByProduct[$p->id]; @endphp

          <div class="ing-panel {{ $idx === 0 ? 'active' : '' }}"
            id="ing-product-{{ $p->id }}"
            data-testid="ing-panel-{{ $p->id }}">

            {{-- Main ingredient label --}}
            @if($ing->name)
              <div class="text-center mb-3" data-aos="fade-up">
                <span class="badge rounded-pill bg-primary-subtle text-primary px-3 py-2 fs-6 fw-semibold">
                  {{ $ing->name }}{{ $ing->amount ? ' · ' . $ing->amount : '' }}
                </span>
                @if($ing->description)
                  <p class="text-muted mt-2 mb-0" style="font-size:.9rem;">{{ $ing->description }}</p>
                @endif
              </div>
            @endif

            {{-- Sub-ingredient cards (no images) --}}
            <div class="row g-4 mt-2">
              @forelse($ing->subIngredients as $subIdx => $sub)
                @php $delay = ($subIdx % 4) * 80; @endphp
                <div class="col-sm-6 col-lg-3" data-aos="fade-up" {{ $delay ? 'data-aos-delay="'.$delay.'"' : '' }}>
                  <div class="ing-card ing-card--no-img">
                    <h5>
                      {{ $sub->name }}
                      @if($sub->amount)
                        <small class="ing-amount">{{ $sub->amount }}{{ $sub->unit }}</small>
                      @endif
                    </h5>
                    @if($sub->description)
                      <p>{{ $sub->description }}</p>
                    @endif
                  </div>
                </div>
              @empty
                <div class="col-12 text-center text-muted py-4">
                  <i class="fa-solid fa-flask-vial fa-lg mb-2 d-block"></i>
                  Sub-ingredient details coming soon.
                </div>
              @endforelse
            </div>

            {{-- Nutritional highlights strip (sub-ingredients with amounts) --}}
            @php $highlighted = $ing->subIngredients->filter(fn($s) => $s->amount); @endphp
            @if($highlighted->isNotEmpty())
              <div class="ing-nutrition-strip" data-aos="fade-up">
                <span class="ins-label">{{ $p->name }} · Nutritional Highlights</span>
                <div class="ins-items">
                  @foreach($highlighted as $s)
                    <div class="ins-item">
                      <strong>{{ $s->name }}</strong>
                      <span>{{ $s->amount }}{{ $s->unit }}</span>
                    </div>
                  @endforeach
                </div>
                @if($p->warning_text)
                  <span class="ins-note">
                    <i class="fa-solid fa-circle-info"></i> {{ $p->warning_text }}
                  </span>
                @endif
              </div>
            @endif

          </div>{{-- /ing-panel --}}
        @endforeach

      @endif
    </div>
  </section>

  <!-- ============ HOW IT WORKS ============ -->
  <section class="section how-it-works" data-testid="how-section">
    <div class="container">
      <div class="section-head text-center" data-aos="fade-up">
        <span class="eyebrow">How It Works</span>
        <h2 class="section-title">Wellness in <span class="text-gradient">4 Simple Steps</span></h2>
      </div>

      <div class="row g-4 mt-3">
        <div class="col-md-6 col-lg-3" data-aos="fade-up"><div class="step-card"><div class="step-num">01</div><i class="fa-solid fa-mouse-pointer step-icon"></i><h5>Choose Product</h5><p>Browse our curated bestsellers.</p></div></div>
        <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="100"><div class="step-card"><div class="step-num">02</div><i class="fa-solid fa-bag-shopping step-icon"></i><h5>Order Online</h5><p>Fast, secure & simple checkout.</p></div></div>
        <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="200"><div class="step-card"><div class="step-num">03</div><i class="fa-solid fa-truck-fast step-icon"></i><h5>Fast Delivery</h5><p>Delivered to your doorstep in days.</p></div></div>
        <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="300"><div class="step-card"><div class="step-num">04</div><i class="fa-solid fa-heart-pulse step-icon"></i><h5>Daily Healthy Life</h5><p>Feel the difference every day.</p></div></div>
      </div>
    </div>
  </section>

  <!-- ============ TESTIMONIALS ============ -->
  <section class="section testimonials bg-soft" id="testimonials" data-testid="testimonials-section">
    <div class="container">
      <div class="section-head text-center" data-aos="fade-up">
        <span class="eyebrow">Real Results, Real People</span>
        @if($totalReviews > 0)
          <h2 class="section-title">Over <span class="text-gradient">{{ number_format($totalReviews) }} Five-Star</span> Reviews</h2>
          <p class="section-sub"><span class="rev-stars">★★★★★</span> {{ $avgRating }} out of 5 · {{ number_format($totalReviews) }}+ verified reviews</p>
        @else
          <h2 class="section-title">Real <span class="text-gradient">Customer</span> Reviews</h2>
          <p class="section-sub">Be the first to share your experience.</p>
        @endif
      </div>

      <div class="row g-4 mt-2">
        @forelse($reviews as $index => $review)
          @php
            $delays   = [0, 80, 160, 0, 80, 160];
            $delay    = $delays[$index] ?? 0;
            $initial  = strtoupper(mb_substr($review->name, 0, 1));
            $colors   = ['#6c63ff','#e85d9f','#2cb67d','#f4a261','#3b82f6','#ef4444'];
            $bgColor  = $colors[$index % count($colors)];
          @endphp
          <div class="col-md-6 col-lg-4" data-aos="fade-up" @if($delay) data-aos-delay="{{ $delay }}" @endif>
            <div class="review-card">
              {{-- Product tag --}}
              @if($review->product)
                <span class="rev-tag">{{ $review->product->name }}</span>
              @endif

              <div class="review-head">
                {{-- Avatar: initials circle (no external image dependency) --}}
                <div class="rev-avatar-initials" style="background:{{ $bgColor }};">{{ $initial }}</div>
                <div>
                  <h6>{{ $review->name }} <i class="fa-solid fa-circle-check verified"></i></h6>
                  <div class="rating small">
                    @for($s = 1; $s <= 5; $s++)
                      <i class="fa-{{ $s <= $review->stars ? 'solid' : 'regular' }} fa-star"></i>
                    @endfor
                  </div>
                  <small>Verified Buyer</small>
                </div>
              </div>

              @if($review->title)
                <h6 class="rev-title">{{ $review->title }}</h6>
              @endif
              <p>"{{ $review->body }}"</p>
            </div>
          </div>
        @empty
          <div class="col-12 text-center text-muted py-4">
            <p>No reviews yet. Be the first to share your experience!</p>
          </div>
        @endforelse
      </div>
    </div>
  </section>

  <!-- ============ LATEST ARTICLES / BLOG ============ -->
  <section class="section blog" data-testid="blog-section">
    <div class="container">
      <div class="section-head text-center" data-aos="fade-up">
        <span class="eyebrow">Wellness Journal</span>
        <h2 class="section-title">Latest <span class="text-gradient">Articles</span></h2>
        <p class="section-sub">Doctor-authored insights on nutrition, wellness & modern Indian lifestyles.</p>
      </div>

      <div class="row g-4 mt-2">
        <div class="col-md-6 col-lg-4" data-aos="fade-up">
          <a href="#" class="blog-card" data-testid="blog-1">
            <div class="blog-img"><img src="https://images.unsplash.com/photo-1490645935967-10de6ba17061?auto=format&fit=crop&w=800&q=80" alt="" /><span class="blog-tag">Weight</span></div>
            <div class="blog-body">
              <small><i class="fa-solid fa-user-doctor"></i> Dr. Aarav Sharma · 5 min read</small>
              <h5>Ideal Weight &amp; Calorie Calculator: A Doctor's Guide</h5>
              <p>Learn how to calculate your ideal weight and daily calorie needs based on your lifestyle and goals.</p>
              <span class="blog-link">Read Article <i class="fa-solid fa-arrow-right"></i></span>
            </div>
          </a>
        </div>
        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
          <a href="#" class="blog-card" data-testid="blog-2">
            <div class="blog-img"><img src="https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=800&q=80" alt="" /><span class="blog-tag">Detox</span></div>
            <div class="blog-body">
              <small><i class="fa-solid fa-user-doctor"></i> Dr. Aarav Sharma · 7 min read</small>
              <h5>The Silent Fatty Liver Epidemic in India (2026)</h5>
              <p>Why millions of Indians don't know they have it — and 5 daily habits to reverse it naturally.</p>
              <span class="blog-link">Read Article <i class="fa-solid fa-arrow-right"></i></span>
            </div>
          </a>
        </div>
        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="200">
          <a href="#" class="blog-card" data-testid="blog-3">
            <div class="blog-img"><img src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=800&q=80" alt="" /><span class="blog-tag">Nutrition</span></div>
            <div class="blog-body">
              <small><i class="fa-solid fa-user-doctor"></i> Dr. Aarav Sharma · 4 min read</small>
              <h5>L-Carnitine for Weight Loss: Benefits &amp; the Science</h5>
              <p>How this amino acid supercharges your fat-burning process — and when to take it.</p>
              <span class="blog-link">Read Article <i class="fa-solid fa-arrow-right"></i></span>
            </div>
          </a>
        </div>
      </div>
      <div class="text-center mt-4"><a href="#" class="btn btn-outline-engix" data-testid="blog-view-all">View All Articles <i class="fa-solid fa-arrow-right ms-1"></i></a></div>
    </div>
  </section>

  <!-- ============ AI RECOMMENDATIONS ============ -->
  {{-- <section class="section ai-recommend" data-testid="ai-recommend-section">
    <div class="container">
      <div class="section-head text-center" data-aos="fade-up">
        <span class="eyebrow"><i class="fa-solid fa-sparkles"></i> AI Powered</span>
        <h2 class="section-title">Personalised <span class="text-gradient">Just For You</span></h2>
        <p class="section-sub">Our wellness AI analyses your goals and recommends the perfect stack.</p>
      </div>

      <div class="row g-4 mt-2">
        <div class="col-md-6 col-lg-4" data-aos="fade-up">
          <div class="ai-card">
            <div class="ai-glow"></div>
            <div class="ai-badge"><i class="fa-solid fa-wand-magic-sparkles"></i> 98% Match</div>
            <img src="https://images.unsplash.com/photo-1587854692152-cbe660dbde88?auto=format&fit=crop&w=600&q=80" alt="Energy Stack" />
            <h5>Energy & Focus Stack</h5>
            <p>B-Complex + Vitamin D3 + Magnesium — tuned for busy professionals.</p>
            <div class="ai-meta"><span><i class="fa-solid fa-bolt"></i> Energy</span><span><i class="fa-solid fa-brain"></i> Focus</span></div>
            <button class="btn btn-primary-engix w-100 add-cart-btn" data-name="Energy Stack" data-testid="ai-cart-1"><i class="fa-solid fa-cart-plus me-1"></i>Add Stack – ₹1,299</button>
          </div>
        </div>
        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
          <div class="ai-card featured">
            <div class="ai-glow"></div>
            <div class="ai-badge"><i class="fa-solid fa-wand-magic-sparkles"></i> Top Pick</div>
            <img src="https://images.unsplash.com/photo-1607619056574-7b8d3ee536b2?auto=format&fit=crop&w=600&q=80" alt="Immunity Stack" />
            <h5>Immunity Shield Stack</h5>
            <p>Vitamin C + Zinc + Elderberry — daily defence against seasonal threats.</p>
            <div class="ai-meta"><span><i class="fa-solid fa-shield-virus"></i> Immunity</span><span><i class="fa-solid fa-leaf"></i> Natural</span></div>
            <button class="btn btn-primary-engix w-100 add-cart-btn" data-name="Immunity Stack" data-testid="ai-cart-2"><i class="fa-solid fa-cart-plus me-1"></i>Add Stack – ₹999</button>
          </div>
        </div>
        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="200">
          <div class="ai-card">
            <div class="ai-glow"></div>
            <div class="ai-badge"><i class="fa-solid fa-wand-magic-sparkles"></i> 94% Match</div>
            <img src="https://images.unsplash.com/photo-1626716493137-b67fe9501e76?auto=format&fit=crop&w=600&q=80" alt="Heart Stack" />
            <h5>Heart & Longevity Stack</h5>
            <p>Omega 3 + Vitamin K2 + CoQ10 — cardiovascular support for life.</p>
            <div class="ai-meta"><span><i class="fa-solid fa-heart"></i> Heart</span><span><i class="fa-solid fa-infinity"></i> Longevity</span></div>
            <button class="btn btn-primary-engix w-100 add-cart-btn" data-name="Heart Stack" data-testid="ai-cart-3"><i class="fa-solid fa-cart-plus me-1"></i>Add Stack – ₹1,499</button>
          </div>
        </div>
      </div>
    </div>
  </section> --}}

  <!-- ============ TRUST STRIP + MONEY-BACK ============ -->
  <section class="trust-strip" data-testid="trust-strip">
    <div class="container">
      <div class="trust-grid">
        <div class="ts-item"><i class="fa-solid fa-shield-check"></i><h6>100% Authentic</h6><p>Verified sourcing</p></div>
        <div class="ts-item"><i class="fa-solid fa-award"></i><h6>GMP Certified</h6><p>WHO standards</p></div>
        <div class="ts-item"><i class="fa-solid fa-vial-circle-check"></i><h6>Lab Tested</h6><p>3rd-party assured</p></div>
        <div class="ts-item"><i class="fa-solid fa-leaf"></i><h6>Non-GMO</h6><p>Clean & pure</p></div>
        <div class="ts-item"><i class="fa-solid fa-truck-fast"></i><h6>Free Shipping</h6><p>On orders ₹499+</p></div>
      </div>

      <div class="guarantee-card" data-aos="fade-up">
        <div class="guarantee-icon"><i class="fa-solid fa-medal"></i></div>
        <div class="guarantee-text">
          <h4>30-Day Money-Back Guarantee</h4>
          <p>Not feeling the difference? Get a full refund — no questions asked. Your wellness, our promise.</p>
        </div>
        <div class="guarantee-pay">
          <span>Secure Checkout</span>
          <div class="pay-icons">
            <i class="fa-brands fa-cc-visa" title="Visa"></i>
            <i class="fa-brands fa-cc-mastercard" title="Mastercard"></i>
            <i class="fa-brands fa-cc-amex" title="Amex"></i>
            <i class="fa-brands fa-google-pay" title="Google Pay"></i>
            <i class="fa-brands fa-apple-pay" title="Apple Pay"></i>
            <i class="fa-solid fa-lock" title="SSL Secure"></i>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============ COMPARISON TABLE ============ -->
  <section class="section comparison bg-soft" data-testid="comparison-section">
    <div class="container">
      <div class="section-head text-center" data-aos="fade-up">
        <span class="eyebrow">Head to Head</span>
        <h2 class="section-title">Engix Care vs <span class="text-gradient">Ordinary Brands</span></h2>
        <p class="section-sub">See why 120,000+ Indians make the switch.</p>
      </div>

      <div class="compare-wrap" data-aos="fade-up">
        <div class="compare-row compare-head">
          <div class="compare-cell">Feature</div>
          <div class="compare-cell winner"><i class="fa-solid fa-heart-pulse"></i> Engix Care</div>
          <div class="compare-cell dim">Other Brands</div>
        </div>
        <div class="compare-row"><div class="compare-cell">Clinically Studied Doses</div><div class="compare-cell winner"><i class="fa-solid fa-circle-check"></i></div><div class="compare-cell dim"><i class="fa-solid fa-circle-xmark"></i></div></div>
        <div class="compare-row"><div class="compare-cell">3rd-Party Lab Tested</div><div class="compare-cell winner"><i class="fa-solid fa-circle-check"></i></div><div class="compare-cell dim"><i class="fa-solid fa-circle-xmark"></i></div></div>
        <div class="compare-row"><div class="compare-cell">No Artificial Fillers</div><div class="compare-cell winner"><i class="fa-solid fa-circle-check"></i></div><div class="compare-cell dim"><i class="fa-solid fa-circle-xmark"></i></div></div>
        <div class="compare-row"><div class="compare-cell">30-Day Money-Back</div><div class="compare-cell winner"><i class="fa-solid fa-circle-check"></i></div><div class="compare-cell dim"><i class="fa-solid fa-circle-xmark"></i></div></div>
        <div class="compare-row"><div class="compare-cell">Doctor-Formulated</div><div class="compare-cell winner"><i class="fa-solid fa-circle-check"></i></div><div class="compare-cell dim"><i class="fa-solid fa-triangle-exclamation"></i></div></div>
        <div class="compare-row"><div class="compare-cell">Free Shipping ₹499+</div><div class="compare-cell winner"><i class="fa-solid fa-circle-check"></i></div><div class="compare-cell dim"><i class="fa-solid fa-circle-xmark"></i></div></div>
        <div class="compare-row"><div class="compare-cell">Loyalty Cashback</div><div class="compare-cell winner"><i class="fa-solid fa-circle-check"></i> 10%</div><div class="compare-cell dim">0%</div></div>
      </div>
    </div>
  </section>

  <!-- ============ BUNDLE BUILDER ============ -->
  {{-- <section class="section bundle-builder" data-testid="bundle-section">
    <div class="container">
      <div class="section-head text-center" data-aos="fade-up">
        <span class="eyebrow">Build & Save</span>
        <h2 class="section-title">Design Your <span class="text-gradient">Custom Stack</span></h2>
        <p class="section-sub">Bundle 2 for 10% OFF · 3 for 15% OFF · 4+ for 20% OFF</p>
      </div>

      <div class="row g-4 mt-2 align-items-start">
        <div class="col-lg-8">
          <div class="row g-3" data-testid="bundle-picks">
            <div class="col-sm-6"><label class="bundle-item"><input type="checkbox" class="bundle-check" data-price="694" data-name="Glow Elixir" data-testid="bundle-1" /><div><img src="https://images.unsplash.com/photo-1556228453-efd6c1ff04f6?auto=format&fit=crop&w=200&q=80" alt="" /><div><h6>Glow Elixir</h6><span>₹694</span></div><i class="fa-solid fa-check bundle-tick"></i></div></label></div>
            <div class="col-sm-6"><label class="bundle-item"><input type="checkbox" class="bundle-check" data-price="340" data-name="Hair Revive" data-testid="bundle-2" /><div><img src="https://images.unsplash.com/photo-1522337660859-02fbefca4702?auto=format&fit=crop&w=200&q=80" alt="" /><div><h6>Hair Revive</h6><span>₹340</span></div><i class="fa-solid fa-check bundle-tick"></i></div></label></div>
            <div class="col-sm-6"><label class="bundle-item"><input type="checkbox" class="bundle-check" data-price="340" data-name="DeepRest" data-testid="bundle-3" /><div><img src="https://images.unsplash.com/photo-1531353826977-0941b4779a1c?auto=format&fit=crop&w=200&q=80" alt="" /><div><h6>DeepRest</h6><span>₹340</span></div><i class="fa-solid fa-check bundle-tick"></i></div></label></div>
            <div class="col-sm-6"><label class="bundle-item"><input type="checkbox" class="bundle-check" data-price="340" data-name="Vital Fuel" data-testid="bundle-4" /><div><img src="https://images.unsplash.com/photo-1517836357463-d25dfeac3438?auto=format&fit=crop&w=200&q=80" alt="" /><div><h6>Vital Fuel</h6><span>₹340</span></div><i class="fa-solid fa-check bundle-tick"></i></div></label></div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="bundle-summary" data-testid="bundle-summary">
            <span class="bs-eyebrow">Your Bundle</span>
            <h4>Custom Wellness Stack</h4>
            <div class="bs-count"><span id="bsCount">0</span> items selected</div>
            <div class="bs-line"><span>Subtotal</span><b id="bsSub">₹0</b></div>
            <div class="bs-line discount"><span>Bundle Discount</span><b id="bsDiscount">– ₹0</b></div>
            <div class="bs-line total"><span>You Pay</span><b id="bsTotal">₹0</b></div>
            <div class="bs-save" id="bsSave">Add 2+ items to unlock discount</div>
            <button class="btn btn-primary-engix w-100 mt-2 add-cart-btn" data-name="Custom Bundle" id="bundleBuy" data-testid="bundle-buy"><i class="fa-solid fa-bag-shopping me-1"></i>Add Bundle to Cart</button>
          </div>
        </div>
      </div>
    </div>
  </section> --}}

  <!-- ============ SPECIAL OFFER + COUNTDOWN ============ -->
  @php
    // Use first_order PromotionOffer if available, else fall back to PromotionSetting
    $bannerHeading   = $firstOrderOffer?->title       ?? $promotionSettings->special_offer_heading;
    $bannerCode      = $firstOrderOffer?->coupon_code ?? $promotionSettings->special_offer_coupon_code;
    $bannerPct       = $firstOrderOffer?->percentage  ?? $promotionSettings->special_offer_percentage;
    $bannerDiscText  = $firstOrderOffer?->discount_text ?? $promotionSettings->special_offer_discount;
    $bannerDesc      = $firstOrderOffer?->description  ?? null;
    $showBanner      = !empty($bannerCode);
  @endphp

  @if($showBanner)
  <section class="special-offer" data-testid="offer-section">
    <div class="container">
      <div class="offer-box" data-aos="zoom-in">
        <div class="offer-left">
          <span class="offer-tag"><span class="live-dot"></span> Limited Time Offer</span>
          @if($bannerPct > 0)
            <div class="offer-pct-badge">{{ $bannerPct }}% OFF</div>
          @endif
          <h2>{{ $bannerHeading }}</h2>
          <p>Use coupon code <span class="coupon" data-testid="coupon-code" id="soSpecialCode">{{ strtoupper($bannerCode) }}</span>
            <button class="offer-copy-btn" onclick="copyCode('soSpecialCode', this)" title="Copy code"><i class="fa-regular fa-copy"></i></button>
            at checkout
          </p>
          @if($bannerDiscText)
            <p class="offer-disc-text"><i class="fa-solid fa-tag me-1"></i>{{ $bannerDiscText }}</p>
          @elseif($bannerPct > 0)
            <p class="offer-disc-text"><i class="fa-solid fa-tag me-1"></i>{{ $bannerPct }}% OFF</p>
          @endif
          @if($bannerDesc)
            <p class="offer-banner-desc">{{ $bannerDesc }}</p>
          @endif
          <div class="countdown" data-testid="countdown">
            <div class="cd-cell"><span id="cd-days">00</span><small>Days</small></div>
            <div class="cd-cell"><span id="cd-hours">00</span><small>Hours</small></div>
            <div class="cd-cell"><span id="cd-mins">00</span><small>Mins</small></div>
            <div class="cd-cell"><span id="cd-secs">00</span><small>Secs</small></div>
          </div>
        </div>
        <div class="offer-right">
          <a href="#products" class="btn btn-white-engix btn-lg" data-testid="offer-shop-now"><i class="fa-solid fa-bag-shopping me-2"></i>Shop Now</a>
          @if(!empty($bannerCode))
            <button class="btn btn-outline-light btn-lg mt-2 banner-apply-btn"
              data-coupon="{{ $bannerCode }}"
              data-offer-title="{{ $bannerHeading }}"
              data-testid="offer-apply-btn">
              <i class="fa-solid fa-bolt me-2"></i>Apply
            </button>
          @endif
        </div>
      </div>
    </div>
  </section>
  @endif

  @if($promotionOffers->isNotEmpty() && $promotionSettings->show_offer_cards)
  <section class="section" data-testid="promotion-offers-section">
    <div class="container">
      <div class="section-head text-center" data-aos="fade-up">
        <span class="eyebrow">Latest Offers</span>
        <h2 class="section-title">Explore <span class="text-gradient">Exciting Deals</span></h2>
      </div>
      <div class="row g-4 mt-3">
        @foreach($promotionOffers as $offer)
          <div class="col-md-6 col-lg-4" data-aos="fade-up">
            <div class="promo-offer-card h-100">
              {{-- Top strip: type badge + percentage badge --}}
              <div class="poc-top">
                <span class="poc-type-badge">{{ ucfirst(str_replace('_', ' ', $offer->type)) }}</span>
                @if($offer->percentage > 0)
                  <span class="poc-pct-badge">{{ $offer->percentage }}% OFF</span>
                @elseif($offer->discount_text)
                  <span class="poc-pct-badge">{{ $offer->discount_text }}</span>
                @endif
              </div>

              {{-- Title & description --}}
              <h5 class="poc-title">{{ $offer->title }}</h5>
              @if($offer->description)
                <p class="poc-desc">{{ $offer->description }}</p>
              @endif

              {{-- Validity note --}}
              @if($offer->target_amount)
                <div class="poc-validity">
                  <i class="fa-solid fa-circle-info"></i>
                  Valid on orders above ₹{{ number_format($offer->target_amount) }}
                </div>
              @endif

              {{-- Coupon row --}}
              @if($offer->coupon_code)
                <div class="poc-coupon-row">
                  <div class="poc-coupon-wrap">
                    <span class="poc-coupon-label">COUPON</span>
                    <span class="poc-coupon-code" id="poc-code-{{ $offer->id }}">{{ $offer->coupon_code }}</span>
                  </div>
                  <button class="poc-copy-btn" onclick="copyCode('poc-code-{{ $offer->id }}', this)" title="Copy code">
                    <i class="fa-regular fa-copy"></i>
                  </button>
                </div>
                <button class="poc-cta-btn apply-offer-btn"
                        data-coupon="{{ $offer->coupon_code }}"
                        data-offer-title="{{ $offer->title }}">
                  <i class="fa-solid fa-bolt me-1"></i>Apply &amp; Shop
                </button>
              @else
                <a href="#products" class="poc-cta-btn poc-cta-secondary">
                  <i class="fa-solid fa-bag-shopping me-1"></i>Shop Now
                </a>
              @endif
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>
  @endif

  <!-- ============ GALLERY ============ -->
  <section class="section gallery" data-testid="gallery-section">
    <div class="container">
      <div class="section-head text-center" data-aos="fade-up">
        <span class="eyebrow">Wellness Lifestyle</span>
        <h2 class="section-title">Live Your <span class="text-gradient">Best Life</span></h2>
      </div>
      <div class="row g-3 mt-3">
        <div class="col-6 col-md-4 col-lg-3" data-aos="zoom-in"><div class="gallery-img"><img src="{{ asset('asset/DSC09004.png') }}" alt="engix CARE Boost Energy" /></div></div>
        <div class="col-6 col-md-4 col-lg-3" data-aos="zoom-in" data-aos-delay="80"><div class="gallery-img"><img src="{{ asset('asset/210A0226.png') }}" alt="engix CARE Boost Energy tablets" /></div></div>
        <div class="col-6 col-md-4 col-lg-3" data-aos="zoom-in" data-aos-delay="160"><div class="gallery-img"><img src="{{ asset('asset/DSC09006.png') }}" alt="engix CARE Vitamin B12" /></div></div>
        <div class="col-6 col-md-4 col-lg-3" data-aos="zoom-in" data-aos-delay="240"><div class="gallery-img"><img src="{{ asset('asset/210A0258.png') }}" alt="engix CARE Vitamin B12 tablets" /></div></div>
        <div class="col-6 col-md-4 col-lg-3" data-aos="zoom-in"><div class="gallery-img"><img src="{{ asset('asset/DSC09008.png') }}" alt="engix CARE product range" /></div></div>
        <div class="col-6 col-md-4 col-lg-3" data-aos="zoom-in" data-aos-delay="80"><div class="gallery-img"><img src="{{ asset('asset/210A0230.png') }}" alt="engix CARE Boost Energy – side view" /></div></div>
        <div class="col-6 col-md-4 col-lg-3" data-aos="zoom-in" data-aos-delay="160"><div class="gallery-img"><img src="{{ asset('asset/DSC09010.png') }}" alt="engix CARE product collection" /></div></div>
        <div class="col-6 col-md-4 col-lg-3" data-aos="zoom-in" data-aos-delay="240"><div class="gallery-img"><img src="{{ asset('asset/210A0260.png') }}" alt="engix CARE Vitamin B12 – pack shot" /></div></div>
      </div>
    </div>
  </section>

  <!-- ============ FAQ ============ -->
  <section class="section faq bg-soft" id="faq" data-testid="faq-section">
    <div class="container">
      <div class="section-head text-center" data-aos="fade-up">
        <span class="eyebrow">FAQ</span>
        <h2 class="section-title">Questions? <span class="text-gradient">We've Got Answers</span></h2>
      </div>

      <div class="row justify-content-center mt-3">
        <div class="col-lg-9">
          <div class="accordion accordion-engix" id="faqAccordion" data-aos="fade-up">
            <div class="accordion-item">
              <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" data-testid="faq-q1">Are Engix Care products safe?</button></h2>
              <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion"><div class="accordion-body">Yes. All our formulations undergo strict third-party lab testing and are manufactured at GMP-certified facilities to ensure the highest safety standards.</div></div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" data-testid="faq-q2">Are they doctor recommended?</button></h2>
              <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion"><div class="accordion-body">Every product is formulated in consultation with certified nutritionists and reviewed by licensed medical practitioners.</div></div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" data-testid="faq-q3">How long does delivery take?</button></h2>
              <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion"><div class="accordion-body">Standard delivery takes 2–4 business days across India with free shipping on orders above ₹499.</div></div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" data-testid="faq-q4">Can I return products?</button></h2>
              <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion"><div class="accordion-body">Yes, we offer a 7-day easy return policy on unopened products. Contact our support team to initiate a return.</div></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============ CONTACT ============ -->
  <section class="section contact" id="contact" data-testid="contact-section">
    <div class="container">
      <div class="section-head text-center" data-aos="fade-up">
        <span class="eyebrow">Get in Touch</span>
        <h2 class="section-title">Talk To Our <span class="text-gradient">Wellness Experts</span></h2>
      </div>

      <div class="row g-4 mt-3">
        <div class="col-lg-6" data-aos="fade-right">
          <form id="contactForm" class="contact-form" novalidate data-testid="contact-form">
            <div class="row g-3">
              <div class="col-md-6"><label class="form-label">Name</label><input type="text" class="form-control" name="name" placeholder="Your name" required data-testid="contact-name" /></div>
              <div class="col-md-6"><label class="form-label">Email</label><input type="email" class="form-control" name="email" placeholder="you@example.com" required data-testid="contact-email" /></div>
              <div class="col-12"><label class="form-label">Phone</label><input type="tel" class="form-control" name="phone" placeholder="+91 98765 43210" data-testid="contact-phone" /></div>
              <div class="col-12"><label class="form-label">Message</label><textarea class="form-control" name="message" rows="5" placeholder="How can we help?" required data-testid="contact-message"></textarea></div>
              <div class="col-12"><button type="submit" class="btn btn-primary-engix btn-lg w-100" data-testid="contact-submit"><i class="fa-solid fa-paper-plane me-2"></i>Send Message</button></div>
            </div>
            <div id="formSuccess" class="form-success" data-testid="form-success"><i class="fa-solid fa-circle-check"></i> Thank you! We'll get back to you shortly.</div>
          </form>
        </div>
        <div class="col-lg-6" data-aos="fade-left">
          <div class="map-wrap">
            <iframe title="map" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3671.9!2d72.6209!3d23.0395!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x395e87a0b9b9b9b9%3A0x0!2sBapunagar%2C+Ahmedabad%2C+Gujarat!5e0!3m2!1sen!2sin!4v1700000000000" loading="lazy" data-testid="map"></iframe>
          </div>
          <div class="contact-info-cards mt-3">
            <div class="ci-card"><i class="fa-solid fa-location-dot"></i><div><h6>Address</h6><p>Shop No-3, Surjit Colony, Bapunagar, Ahmedabad, Gujarat – 380024</p></div></div>
            <div class="ci-card"><i class="fa-solid fa-envelope"></i><div><h6>Customer Care Email</h6><p><a href="mailto:vhkinternational2026@gmail.com" style="color:inherit;text-decoration:none;">vhkinternational2026@gmail.com</a></p></div></div>
            <div class="ci-card"><i class="fa-brands fa-whatsapp" style="color:#25D366;"></i><div><h6>WhatsApp</h6><p><a href="https://wa.me/919327865063?text=Hi%20VHK%20International%2C%20I%20have%20a%20question%20about%20engix%20CARE" target="_blank" rel="noopener" style="color:inherit;text-decoration:none;">Chat with us on WhatsApp</a></p></div></div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============ FOOTER ============ -->
  <footer class="footer" data-testid="footer">
    <div class="container">
      <div class="row g-4">
        <div class="col-lg-4">
          <a href="#home" class="footer-brand"><img src="{{ asset('asset/logo.png') }}" alt="engix CARE" style="height:40px;width:auto;object-fit:contain;margin-right:10px;" /><span>engix<span class="brand-accent">CARE</span></span></a>
          <p class="footer-desc">Premium effervescent health supplements crafted for real Indian lifestyles. Doctor formulated, WHO-GMP &amp; HACCP certified manufacturing, FSSAI approved.</p>
          <div class="social">
            <a href="#" aria-label="Facebook" data-testid="social-fb"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="#" aria-label="Instagram" data-testid="social-ig"><i class="fa-brands fa-instagram"></i></a>
            <a href="#" aria-label="X" data-testid="social-x"><i class="fa-brands fa-x-twitter"></i></a>
            <a href="#" aria-label="YouTube" data-testid="social-yt"><i class="fa-brands fa-youtube"></i></a>
            <a href="#" aria-label="LinkedIn" data-testid="social-li"><i class="fa-brands fa-linkedin-in"></i></a>
          </div>
        </div>
        <div class="col-6 col-lg-2">
          <h6 class="footer-title">Quick Links</h6>
          <ul class="footer-links">
            <li><a href="#home">Home</a></li>
            <li><a href="#products">Products</a></li>
            <li><a href="#benefits">Benefits</a></li>
            <li><a href="#ingredients">Ingredients</a></li>
            <li><a href="#faq">FAQ</a></li>
          </ul>
        </div>
        <div class="col-6 col-lg-2">
          <h6 class="footer-title">Policies</h6>
          <ul class="footer-links">
            <li><a href="#">Privacy Policy</a></li>
            <li><a href="#">Refund Policy</a></li>
            <li><a href="#">Terms of Service</a></li>
            <li><a href="#">Shipping</a></li>
            <li><a href="#contact">Contact</a></li>
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
            Growequal <span style="font-size:0.75rem;opacity:0.8;">(WHO-GMP &amp; HACCP Certified)</span><br />
            D-15, Sahjanand Business Park, S.P. Ring Road,<br />
            Nikol, Ahmedabad, Gujarat – 382350, India</p>
            <p style="margin:0;"><strong style="color:#cbd5e1;">Customer Care:</strong><br />
            <a href="mailto:vhkinternational2026@gmail.com" style="color:#22C55E;text-decoration:none;">vhkinternational2026@gmail.com</a></p>
          </div>
        </div>
      </div>
      <hr class="footer-divider" />
      <div class="row g-3 mb-4">
        <div class="col-lg-6">
          <h6 class="footer-title">Join Our Newsletter</h6>
          <p class="text-muted-2">Get 10% OFF your first order + wellness tips in your inbox.</p>
          <form id="newsletterForm" class="newsletter" data-testid="newsletter-form">
            <input type="email" placeholder="Enter your email" required data-testid="newsletter-input" />
            <button type="submit" data-testid="newsletter-submit"><i class="fa-solid fa-paper-plane"></i></button>
          </form>
          <p id="newsletterMsg" class="newsletter-msg" data-testid="newsletter-msg"></p>
        </div>
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

  <!-- Back to top -->
  <button id="backToTop" class="back-to-top" aria-label="Back to top" data-testid="back-to-top"><i class="fa-solid fa-arrow-up"></i></button>

  <!-- Floating Discount Coupon -->
  @if($promotionSettings->floating_coupon_code)
  <div id="floatingCoupon" class="floating-coupon" data-testid="floating-coupon">
    <button class="fc-close" id="fcClose" aria-label="Close" data-testid="fc-close"><i class="fa-solid fa-xmark"></i></button>
    <div class="fc-icon"><i class="fa-solid fa-gift"></i></div>
    <div class="fc-body">
      <h6>{{ $promotionSettings->floating_coupon_title }}</h6>
      @if($promotionSettings->floating_coupon_percentage > 0)
        <small class="fc-pct">Save {{ $promotionSettings->floating_coupon_percentage }}% with this code</small>
      @endif
      <div class="fc-code" id="fcCodeText">{{ $promotionSettings->floating_coupon_code }} <button class="fc-copy" id="fcCopy" onclick="copyCode('fcCodeText', this)" data-testid="fc-copy"><i class="fa-regular fa-copy"></i></button></div>
    </div>
  </div>
  @endif

  @if($promotionSettings->show_engix_club)
  <!-- Loyalty Rewards Widget (FAB + Panel) -->
  <button id="loyaltyFab" class="loyalty-fab" aria-label="Rewards" data-testid="loyalty-fab">
    <i class="fa-solid fa-crown"></i>
    <span class="fab-pulse"></span>
  </button>
  <div id="loyaltyPanel" class="loyalty-panel" data-testid="loyalty-panel">
    <div class="lp-head">
      <div>
        <span class="lp-eyebrow">Engix Club</span>
        <h5><i class="fa-solid fa-crown"></i> Your Rewards</h5>
      </div>
      <button class="lp-close" id="lpClose" aria-label="Close" data-testid="lp-close"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="lp-points">
      <div class="lp-num" id="lpNum" data-count="1250">0</div>
      <span>ENGIX Points</span>
      <div class="lp-progress"><div class="lp-bar" style="width:64%"></div></div>
      <small>750 pts to <b>Gold Tier</b></small>
    </div>
    <ul class="lp-list">
      <li><i class="fa-solid fa-percent"></i><div><h6>10% Cashback</h6><p>On every purchase, back into ENGIX wallet</p></div></li>
      <li><i class="fa-solid fa-gift"></i><div><h6>Birthday Gift</h6><p>Free Vitamin C bottle on your special day</p></div></li>
      <li><i class="fa-solid fa-star"></i><div><h6>Early Access</h6><p>Members-only launches 48 hrs early</p></div></li>
      <li><i class="fa-solid fa-truck-fast"></i><div><h6>Free Delivery</h6><p>All orders, always — no minimum</p></div></li>
    </ul>
    <button class="btn btn-primary-engix w-100" data-testid="lp-join"><i class="fa-solid fa-crown me-2"></i>Join Engix Club — Free</button>
  </div>
  @endif

  {{-- Live Purchase Notification (hidden) --}}

  <!-- Welcome Offer Modal -->
  <div id="welcomeModal" class="welcome-modal" data-testid="welcome-modal">
    <div class="wm-overlay" id="wmOverlay"></div>
    <div class="wm-card">
      <button class="wm-close" id="wmClose" aria-label="Close" data-testid="wm-close"><i class="fa-solid fa-xmark"></i></button>
      <div class="wm-confetti"></div>
      @if($promotionSettings->welcome_modal_percentage > 0)
        <div class="wm-pct-ring">{{ $promotionSettings->welcome_modal_percentage }}%<small>OFF</small></div>
      @else
        <div class="wm-badge"><i class="fa-solid fa-gift"></i> Welcome Gift</div>
      @endif
      <h3>{{ $promotionSettings->welcome_modal_title }}</h3>
      <p>{{ $promotionSettings->welcome_modal_description }}</p>
      <div class="wm-form">
        <input type="email" placeholder="Enter your email" id="wmEmail" data-testid="wm-email" />
        <button id="wmClaim" class="btn btn-primary-engix" data-testid="wm-claim">Claim My {{ $promotionSettings->welcome_modal_code }}</button>
      </div>
      <div class="wm-code-preview">
        Use code <b id="wmCodeText">{{ $promotionSettings->welcome_modal_code }}</b> at checkout
        <button class="wm-copy-btn" onclick="copyCode('wmCodeText', this)" title="Copy code"><i class="fa-regular fa-copy"></i></button>
      </div>
      <p class="wm-small">By claiming, you agree to receive our wellness newsletter. Unsubscribe anytime.</p>
      <button class="wm-nope" id="wmNope" data-testid="wm-nope">No thanks, I'll pay full price</button>
    </div>
  </div>

  <!-- Sticky Mobile Buy Bar -->
  @php
    $stickyProduct = $products->firstWhere('featured', true) ?? $products->first();
  @endphp
  @if($stickyProduct)
  <div id="stickyBuy" class="sticky-buy" data-testid="sticky-buy">
    <div class="sb-left">
      <span class="sb-title">{{ $stickyProduct->name }}</span>
      <span class="sb-price">
        ₹{{ number_format($stickyProduct->discounted_price ?? $stickyProduct->price, 0) }}
        @if($stickyProduct->original_price && $stickyProduct->original_price > ($stickyProduct->discounted_price ?? $stickyProduct->price))
          <s>₹{{ number_format($stickyProduct->original_price, 0) }}</s>
        @endif
      </span>
    </div>
    <a href="#product-{{ $stickyProduct->id }}" class="btn btn-primary-engix sb-btn" data-testid="sb-buy">
      <i class="fa-solid fa-bag-shopping me-1"></i>Buy Now
    </a>
  </div>
  @endif

  <!-- ============ AI CHAT ASSISTANT ============ -->
  <button id="chatFab" class="chat-fab" aria-label="Chat with Engix AI" data-testid="chat-fab">
    <i class="fa-solid fa-comment-medical"></i>
    <span class="chat-badge">1</span>
  </button>
  <div id="chatBox" class="chat-box" data-testid="chat-box">
    <div class="chat-head">
      <div class="ch-left">
        <div class="ch-avatar"><i class="fa-solid fa-user-doctor"></i></div>
        <div>
          <h6>Ask Engix AI <span class="ch-online">Online</span></h6>
          <small>Your personal wellness assistant</small>
        </div>
      </div>
      <button class="ch-close" id="chatClose" aria-label="Close" data-testid="chat-close"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="chat-body" id="chatBody" data-testid="chat-body">
      <div class="chat-msg bot">
        <div class="cm-avatar"><i class="fa-solid fa-user-doctor"></i></div>
        <div class="cm-bubble">Hi! I'm Engix AI 👋 Ask me anything about vitamins, immunity, or which supplement is right for you.</div>
      </div>
      <div class="chat-quick" id="chatQuick">
        <button data-q="Which vitamin is best for immunity?" data-testid="cq-1">Best for immunity?</button>
        <button data-q="Are your products doctor recommended?" data-testid="cq-2">Doctor recommended?</button>
        <button data-q="When will I feel the difference?" data-testid="cq-3">When will I feel results?</button>
        <button data-q="How do I use the coupon?" data-testid="cq-4">Use my coupon</button>
      </div>
    </div>
    <div class="chat-foot">
      <input type="text" id="chatInput" placeholder="Type your question…" data-testid="chat-input" />
      <button id="chatSend" aria-label="Send" data-testid="chat-send"><i class="fa-solid fa-paper-plane"></i></button>
    </div>
  </div>

  <!-- ============ SPIN THE WHEEL ============ -->
  @if($promotionSettings->is_spin_to_win_enabled)
  <button id="wheelTrigger" class="wheel-trigger" aria-label="Spin to win" data-testid="wheel-trigger">
    <i class="fa-solid fa-gift"></i>
    <span>Spin to Win</span>
  </button>
  <div id="wheelModal" class="wheel-modal" data-testid="wheel-modal">
    <div class="wm-overlay" id="wheelOverlay"></div>
    <div class="wheel-card">
      <button class="wm-close" id="wheelClose" aria-label="Close" data-testid="wheel-close"><i class="fa-solid fa-xmark"></i></button>
      <span class="wheel-eyebrow"><i class="fa-solid fa-sparkles"></i> Spin & Win</span>
      <h3>Try Your Luck!<br /><span class="text-gradient">Win up to 40% OFF</span></h3>
      <div class="wheel-wrap">
        <div class="wheel-pointer"><i class="fa-solid fa-caret-down"></i></div>
        <div id="wheel" class="wheel">
          <div class="wheel-seg wheel-seg-0"><span>10% OFF</span></div>
          <div class="wheel-seg wheel-seg-1"><span>Free Shipping</span></div>
          <div class="wheel-seg wheel-seg-2"><span>25% OFF</span></div>
          <div class="wheel-seg wheel-seg-3"><span>Try Again</span></div>
          <div class="wheel-seg wheel-seg-4"><span>40% OFF</span></div>
          <div class="wheel-seg wheel-seg-5"><span>Free Gift</span></div>
          <div class="wheel-hub"><i class="fa-solid fa-heart-pulse"></i></div>
        </div>
      </div>
      <p id="wheelResult" class="wheel-result"></p>
      <button id="wheelSpin" class="btn btn-primary-engix btn-lg" data-testid="wheel-spin"><i class="fa-solid fa-rotate me-2"></i>Spin the Wheel</button>
    </div>
  </div>
  @endif

  <!-- Actual scroll offers from DB (active + public, with coupon code) -->
  @php
    $scrollOffersJson = $promotionOffers
      ->filter(fn($o) => !empty($o->coupon_code))
      ->map(fn($o) => [
        'title'        => $o->title,
        'desc'         => $o->description ?? '',
        'code'         => $o->coupon_code,
        'pct'          => $o->percentage ?? 0,
        'type'         => $o->type ?? 'general',
        'discountText' => $o->discount_text ?? '',
      ])
      ->values();
  @endphp
  <script>
    window.SCROLL_OFFERS = @json($scrollOffersJson);
  </script>

  <!-- ============ RANDOM SCROLL OFFER POPUP ============ -->
  <div id="scrollOffer" class="scroll-offer" data-testid="scroll-offer">
    <button class="so-close" id="soClose" aria-label="Close" data-testid="so-close"><i class="fa-solid fa-xmark"></i></button>
    <div class="so-glow"></div>
    <div class="so-icon" id="soIcon"><i class="fa-solid fa-fire"></i></div>
    <div class="so-body">
      <span class="so-tag" id="soTag">Flash Sale</span>
      @if($promotionSettings->scroll_offer_percentage > 0)
        <div class="so-pct-badge">{{ $promotionSettings->scroll_offer_percentage }}% OFF</div>
      @endif
      <h5 id="soTitle">{{ $promotionSettings->scroll_offer_title }}</h5>
      <p id="soDesc">{{ $promotionSettings->scroll_offer_description }}</p>
      <div class="so-code" id="soCode">
        <span id="soCodeText">{{ $promotionSettings->scroll_offer_code }}</span>
        <button class="so-copy" id="soCopy" onclick="copyCode('soCodeText', this)" data-testid="so-copy"><i class="fa-regular fa-copy"></i></button>
      </div>
      <button class="btn btn-primary-engix so-cta apply-offer-btn" id="soCta"
              data-coupon="{{ $promotionSettings->scroll_offer_code }}"
              data-offer-title="{{ $promotionSettings->scroll_offer_title }}"
              data-testid="so-cta"><i class="fa-solid fa-bolt me-1"></i>Claim Offer</button>
    </div>
  </div>

  <!-- WhatsApp Floating Widget -->
  <a href="https://wa.me/919327865063?text=Hi%20VHK%20International%2C%20I%20have%20a%20question%20about%20engix%20CARE" target="_blank" rel="noopener" class="whatsapp-fab" aria-label="Chat on WhatsApp" data-testid="whatsapp-fab">
    <i class="fa-brands fa-whatsapp"></i>
    <span class="wa-tip">Chat with us</span>
  </a>

<style>
  /* ── Offer Card (Explore Exciting Deals) ───────────────── */
  .promo-offer-card {
    background: linear-gradient(145deg, rgba(16,185,129,.07), #fff);
    border: 1.5px solid rgba(16,185,129,.22);
    border-radius: 20px;
    padding: 24px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    transition: transform .25s, box-shadow .25s;
  }
  .promo-offer-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(16,185,129,.16);
  }
  .poc-top { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 6px; }
  .poc-type-badge {
    font-size: .7rem; font-weight: 700; letter-spacing: .07em; text-transform: uppercase;
    padding: 4px 12px; border-radius: 999px;
    background: rgba(16,185,129,.12); color: #059669;
  }
  .poc-pct-badge {
    font-size: .78rem; font-weight: 800; letter-spacing: .05em;
    padding: 4px 12px; border-radius: 999px;
    background: linear-gradient(135deg,#10b981,#059669); color: #fff;
    box-shadow: 0 2px 8px rgba(16,185,129,.35);
  }
  .poc-title { font-size: 1.05rem; font-weight: 700; color: #1e293b; margin: 0; }
  .poc-desc  { font-size: .88rem; color: #64748b; margin: 0; flex: 1; }
  .poc-validity {
    font-size: .78rem; color: #64748b;
    background: rgba(100,116,139,.07);
    border-radius: 8px; padding: 6px 10px;
    display: flex; align-items: center; gap: 6px;
  }
  .poc-coupon-row {
    display: flex; align-items: center; gap: 0;
    border: 1.5px dashed rgba(16,185,129,.5); border-radius: 10px;
    overflow: hidden; background: rgba(16,185,129,.04);
  }
  .poc-coupon-wrap {
    flex: 1; display: flex; flex-direction: column;
    padding: 8px 14px;
  }
  .poc-coupon-label { font-size: .65rem; font-weight: 700; letter-spacing: .1em; color: #94a3b8; text-transform: uppercase; }
  .poc-coupon-code  { font-size: 1rem; font-weight: 800; color: #059669; letter-spacing: .06em; }
  .poc-copy-btn {
    padding: 0 14px; border: none; background: rgba(16,185,129,.1); color: #059669;
    cursor: pointer; font-size: .95rem; align-self: stretch;
    display: flex; align-items: center; transition: background .2s;
  }
  .poc-copy-btn:hover { background: rgba(16,185,129,.22); }
  .poc-cta-btn {
    display: block; text-align: center; padding: 10px 18px;
    border-radius: 10px; font-size: .88rem; font-weight: 700;
    background: linear-gradient(135deg,#10b981,#059669); color: #fff;
    text-decoration: none; transition: opacity .2s, transform .2s;
    box-shadow: 0 4px 12px rgba(16,185,129,.3);
    margin-top: auto;
  }
  .poc-cta-btn:hover { opacity: .9; transform: translateY(-1px); color: #fff; }
  .poc-cta-secondary { background: rgba(16,185,129,.12); color: #059669; box-shadow: none; }
  .poc-cta-secondary:hover { background: rgba(16,185,129,.22); color: #059669; }

  /* ── Special Offer banner extras ───────────────────────── */
  .offer-pct-badge {
    display: inline-block; font-size: 1.5rem; font-weight: 900;
    background: rgba(255,255,255,.22); border: 2px solid rgba(255,255,255,.5);
    border-radius: 12px; padding: 4px 18px; margin-bottom: 10px;
    letter-spacing: .04em; color: #fff;
  }
  .offer-copy-btn {
    background: none; border: none; color: inherit; cursor: pointer;
    opacity: .75; padding: 0 4px; font-size: .9rem; vertical-align: middle;
    transition: opacity .2s;
  }
  .offer-copy-btn:hover { opacity: 1; }
  .offer-disc-text { font-size: .9rem; opacity: .85; margin-top: 4px; }

  /* ── Floating Coupon extras ─────────────────────────────── */
  .fc-pct { display: block; font-size: .72rem; color: #059669; font-weight: 600; margin-bottom: 2px; }

  /* ── Welcome Modal percentage ring ──────────────────────── */
  .wm-pct-ring {
    width: 80px; height: 80px; border-radius: 50%;
    background: linear-gradient(135deg,#10b981,#059669);
    color: #fff; display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    font-size: 1.6rem; font-weight: 900; line-height: 1;
    margin: 0 auto 12px; box-shadow: 0 6px 18px rgba(16,185,129,.4);
  }
  .wm-pct-ring small { font-size: .65rem; font-weight: 700; letter-spacing: .08em; }
  .wm-copy-btn {
    background: none; border: none; color: #059669; cursor: pointer;
    font-size: .85rem; padding: 0 4px; vertical-align: middle;
    opacity: .75; transition: opacity .2s;
  }
  .wm-copy-btn:hover { opacity: 1; }

  /* ── Scroll Offer percentage badge ──────────────────────── */
  .so-pct-badge {
    display: inline-block; font-size: 1.1rem; font-weight: 900;
    background: linear-gradient(135deg,#10b981,#059669);
    color: #fff; border-radius: 8px; padding: 3px 14px;
    margin-bottom: 6px; box-shadow: 0 3px 10px rgba(16,185,129,.35);
  }
</style>

<script>
  /* ── Universal copy-code helper ─────────────────────────── */
  function copyCode(elementId, btn) {
    var el = document.getElementById(elementId);
    if (!el) return;
    var text = el.textContent.trim();
    navigator.clipboard.writeText(text).then(function () {
      var icon = btn.querySelector('i');
      if (icon) {
        icon.className = 'fa-solid fa-circle-check';
        setTimeout(function () { icon.className = 'fa-regular fa-copy'; }, 1800);
      }
    }).catch(function () {
      /* fallback */
      var range = document.createRange();
      range.selectNode(el);
      window.getSelection().removeAllRanges();
      window.getSelection().addRange(range);
      document.execCommand('copy');
      window.getSelection().removeAllRanges();
    });
  }
</script>

@endsection
