<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\HeroController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

// ── Home ──────────────────────────────────────────────────────────────────────
Route::get('/', function () {
    $products = \App\Models\Product::where('status', 'Active')
        ->where('is_visible', true)
        ->orderByDesc('featured')
        ->orderByDesc('id')
        ->get();

    // Load ingredients with sub-ingredients for each visible product, keyed by product_id
    $ingredientsByProduct = \App\Models\Ingredient::with('subIngredients')
        ->whereIn('product_id', $products->pluck('id'))
        ->get()
        ->keyBy('product_id');

    $hero = \App\Models\HeroSection::current();
    $promotionSettings = \App\Models\PromotionSetting::current();
    $promotionOffers = \App\Models\PromotionOffer::active()->get();

    // First-order offer for the special offer banner (most recent active first_order offer)
    $firstOrderOffer = \App\Models\PromotionOffer::active()
        ->where('type', 'first_order')
        ->first();

    // Live reviews for the testimonials section
    $reviews = \App\Models\Review::with('product')
        ->visible()
        ->latest()
        ->take(6)
        ->get();

    $totalReviews = \App\Models\Review::visible()->count();
    $avgRating    = round(\App\Models\Review::visible()->avg('stars') ?? 0, 1);

    // ── Counter stats ─────────────────────────────────────────────────────────
    // Unique customers who have placed at least one paid order
    $statHappyCustomers = \App\Models\Order::where('payment_status', 'paid')
        ->distinct('user_id')
        ->count('user_id');

    // Visible, active products in the catalogue
    $statPremiumProducts = \App\Models\Product::where('status', 'Active')
        ->where('is_visible', true)
        ->count();

    // Satisfaction rate: avg star rating as a percentage (out of 5 → %)
    $statSatisfactionRate = $avgRating > 0
        ? (int) round(($avgRating / 5) * 100)
        : 0;

    return view('home', compact(
        'products', 'ingredientsByProduct', 'hero',
        'promotionSettings', 'promotionOffers', 'firstOrderOffer',
        'reviews', 'totalReviews', 'avgRating',
        'statHappyCustomers', 'statPremiumProducts', 'statSatisfactionRate',
    ));
})->name('home');

// ── Public Auth ───────────────────────────────────────────────────────────────
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ── Products (Public) ─────────────────────────────────────────────────────────
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::post('/products/{product}/reviews', [ProductController::class, 'submitReview'])->name('products.reviews.store');

// ── Cart ──────────────────────────────────────────────────────────────────────
Route::get('/cart', [CartController::class, 'index'])->middleware('auth')->name('cart.index');
Route::post('/cart/save', [CartController::class, 'save'])->middleware('auth')->name('cart.save');

Route::post('/coupon/validate', [CheckoutController::class, 'validateCoupon'])->middleware('auth')->name('coupon.validate');
Route::post('/offer/apply', [CheckoutController::class, 'applyOffer'])->name('offer.apply');

// ── Checkout ──────────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/checkout',                              [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout',                             [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/confirmation/{order}',         [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');
    Route::get('/user/has-used-coupon',                  [CheckoutController::class, 'hasUsedCoupon'])->name('user.has-used-coupon');

    // Razorpay
    Route::post('/checkout/razorpay/verify',             [CheckoutController::class, 'verifyPayment'])->name('checkout.razorpay.verify');
    Route::post('/checkout/razorpay/payment-failed',     [CheckoutController::class, 'paymentFailed'])->name('checkout.razorpay.failed');
});

// ── Offers (Public) ───────────────────────────────────────────────────────────
Route::get('/offers', [PromotionController::class, 'publicOffers'])->name('offers.index');

// ── My Orders (Authenticated) ─────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/my-orders', [CheckoutController::class, 'myOrders'])->name('orders.index');
    Route::get('/my-orders/{order}', [CheckoutController::class, 'orderDetail'])->name('orders.detail');
    Route::get('/my-orders/{order}/invoice', [CheckoutController::class, 'downloadInvoice'])->name('orders.invoice');
    Route::get('/my-orders/{order}/invoice/pdf', [CheckoutController::class, 'downloadInvoicePdf'])->name('orders.invoice.pdf');
});

// ── Contact (Public) ──────────────────────────────────────────────────────────
Route::post('/contact/messages', [ContactMessageController::class, 'store'])->name('contact.messages.store');

// ══════════════════════════════════════════════════════════════════════════════
// ADMIN
// ══════════════════════════════════════════════════════════════════════════════

// ── Admin Auth (public — no middleware) ───────────────────────────────────────
Route::get('/admin/login',   [AuthController::class, 'showAdminLogin'])->name('admin.login');
Route::post('/admin/login',  [AuthController::class, 'adminLogin'])->name('admin.login.submit');
Route::post('/admin/logout', [AuthController::class, 'adminLogout'])->name('admin.logout');

// ── Admin Panel (protected by 'admin' middleware) ─────────────────────────────
Route::middleware('admin')->prefix('admin')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // ── Products ──────────────────────────────────────────────────────────────
    Route::get('/products',                         [ProductController::class, 'index'])->name('admin.products.index');
    Route::post('/products/filter',                 [ProductController::class, 'filter'])->name('admin.products.filter');
    Route::post('/products/upload-image',           [ProductController::class, 'uploadImage'])->name('admin.products.upload-image');
    Route::delete('/products/delete-image',         [ProductController::class, 'deleteImage'])->name('admin.products.delete-image');
    Route::post('/products/upload-video',           [ProductController::class, 'uploadVideo'])->name('admin.products.upload-video');
    Route::delete('/products/delete-video',         [ProductController::class, 'deleteVideo'])->name('admin.products.delete-video');
    Route::get('/products/{product}/edit-data',     [ProductController::class, 'editData'])->name('admin.products.edit-data');
    Route::post('/products',                        [ProductController::class, 'store'])->name('admin.products.store');
    Route::put('/products/{product}',               [ProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/products/{product}',            [ProductController::class, 'destroy'])->name('admin.products.destroy');

    // ── Ingredients ───────────────────────────────────────────────────────────
    Route::get('/ingredients',                        [IngredientController::class, 'index'])->name('admin.ingredients.index');
    Route::post('/ingredients/filter',                [IngredientController::class, 'filter'])->name('admin.ingredients.filter');
    Route::get('/ingredients/{ingredient}/edit-data', [IngredientController::class, 'editData'])->name('admin.ingredients.edit-data');
    Route::post('/ingredients',                       [IngredientController::class, 'store'])->name('admin.ingredients.store');
    Route::put('/ingredients/{ingredient}',           [IngredientController::class, 'update'])->name('admin.ingredients.update');
    Route::delete('/ingredients/{ingredient}',        [IngredientController::class, 'destroy'])->name('admin.ingredients.destroy');

    // ── Hero Section ──────────────────────────────────────────────────────────
    Route::get('/hero',               [HeroController::class, 'index'])->name('admin.hero.index');
    Route::put('/hero',               [HeroController::class, 'update'])->name('admin.hero.update');
    Route::post('/hero/upload-image', [HeroController::class, 'uploadImage'])->name('admin.hero.upload-image');

    // ── Promotions ────────────────────────────────────────────────────────────
    Route::get('/promotions',                  [PromotionController::class, 'index'])->name('admin.promotions.index');
    Route::put('/promotions',                  [PromotionController::class, 'update'])->name('admin.promotions.update');
    Route::post('/promotions/offers',          [PromotionController::class, 'storeOffer'])->name('admin.promotions.offers.store');
    Route::put('/promotions/offers/{offer}',   [PromotionController::class, 'updateOffer'])->name('admin.promotions.offers.update');
    Route::delete('/promotions/offers/{offer}',[PromotionController::class, 'deleteOffer'])->name('admin.promotions.offers.delete');

    // ── Reviews & Ratings ─────────────────────────────────────────────────────
    Route::get('/reviews',                    [ReviewController::class, 'index'])->name('admin.reviews.index');
    Route::patch('/reviews/{review}/toggle',  [ReviewController::class, 'toggleVisible'])->name('admin.reviews.toggle');
    Route::delete('/reviews/{review}',        [ReviewController::class, 'destroy'])->name('admin.reviews.destroy');

    // ── Contact Messages / Inquiries ──────────────────────────────────────────
    Route::get('/inquiries',                       [ContactMessageController::class, 'index'])->name('admin.inquiries.index');
    Route::delete('/inquiries/{contactMessage}',   [ContactMessageController::class, 'destroy'])->name('admin.inquiries.destroy');

    // ── Orders ────────────────────────────────────────────────────────────────
    Route::get('/orders',                            [OrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/orders/{order}',                    [OrderController::class, 'show'])->name('admin.orders.show');
    Route::patch('/orders/{order}/status',           [OrderController::class, 'updateStatus'])->name('admin.orders.update-status');
    Route::patch('/orders/{order}/payment-status',   [OrderController::class, 'updatePaymentStatus'])->name('admin.orders.update-payment-status');

    // ── Payments ──────────────────────────────────────────────────────────────
    Route::get('/payments', [OrderController::class, 'payments'])->name('admin.payments.index');

    // ── Coupons ───────────────────────────────────────────────────────────────
    Route::get('/coupons',            [PromotionController::class, 'coupons'])->name('admin.coupons.index');
    Route::post('/coupons',           [PromotionController::class, 'storeOffer'])->name('admin.coupons.store');
    Route::put('/coupons/{offer}',    [PromotionController::class, 'updateOffer'])->name('admin.coupons.update');
    Route::delete('/coupons/{offer}', [PromotionController::class, 'deleteOffer'])->name('admin.coupons.delete');
});

Route::get('/run-link', function () {
    $target = storage_path('app/public');
    $link = public_path('storage');

    // Clear out any existing broken links or files first
    if (file_exists($link) || is_link($link)) {
        File::delete($link);
    }

    // Directly request a native PHP symlink
    if (symlink($target, $link)) {
        return 'Storage link created successfully using native PHP!';
    }

    return 'Failed to create symbolic link. Check folder permissions.';
});

Route::get('/clear-config', function () {
    Artisan::call('config:clear');
    return 'Configuration cache cleared successfully!';
});
