<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PromotionOffer;
use App\Models\PromotionSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Razorpay\Api\Api as RazorpayApi;
use Razorpay\Api\Errors\SignatureVerificationError;

class CheckoutController extends Controller
{
    /**
     * Show the checkout form, pre-filled from the logged-in user's cart.
     */
    public function show()
    {
        $user      = Auth::user();
        $cartItems = $user->cart_data ?? [];

        if (empty($cartItems)) {
            return redirect()->route('home')->with('info', 'Your cart is empty.');
        }

        ['subtotal' => $subtotal, 'discount' => $discount, 'total' => $total]
            = $this->calcTotals($cartItems, request()->query('coupon_code'), $user);

        return view('checkout.show', compact('user', 'cartItems', 'subtotal', 'discount', 'total'));
    }

    /**
     * Store a new order.
     * - COD: create order immediately, redirect to confirmation.
     * - Online: create a pending order + Razorpay order, return JSON for JS to open the payment modal.
     */
    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $data = $request->validate([
            'name'           => 'required|string|max:100',
            'email'          => 'required|email|max:255',
            'phone'          => 'required|string|max:20',
            'address_line1'  => 'required|string|max:255',
            'address_line2'  => 'nullable|string|max:255',
            'city'           => 'required|string|max:100',
            'state'          => 'required|string|max:100',
            'pincode'        => 'required|string|size:6',
            'payment_method' => 'required|in:cod,online',
            'coupon_code'    => 'nullable|string|max:100',
            'notes'          => 'nullable|string|max:500',
        ]);

        $cartItems = $user->cart_data ?? [];

        if (empty($cartItems)) {
            return redirect()->route('home')->with('info', 'Your cart is empty.');
        }

        ['subtotal' => $subtotal, 'discount' => $discount, 'total' => $total]
            = $this->calcTotals($cartItems, $data['coupon_code'] ?? null, $user);

        // ── COD flow ──────────────────────────────────────────────────────────
        if ($data['payment_method'] === 'cod') {
            $order = Order::create([
                'user_id'        => $user->id,
                'name'           => $data['name'],
                'email'          => $data['email'],
                'phone'          => $data['phone'],
                'address_line1'  => $data['address_line1'],
                'address_line2'  => $data['address_line2'] ?? null,
                'city'           => $data['city'],
                'state'          => $data['state'],
                'pincode'        => $data['pincode'],
                'items'          => $cartItems,
                'coupon_code'    => isset($data['coupon_code']) ? strtoupper(trim($data['coupon_code'])) : null,
                'subtotal'       => $subtotal,
                'discount'       => $discount,
                'total'          => $total,
                'payment_method' => 'cod',
                'payment_status' => 'pending',
                'status'         => 'placed',
                'notes'          => $data['notes'] ?? null,
            ]);

            $user->cart_data = [];
            $user->save();

            return redirect()->route('checkout.confirmation', $order->id);
        }

        // ── Online / Razorpay flow ────────────────────────────────────────────
        // Create a pending order in our DB first so we have an order ID to associate.
        $order = Order::create([
            'user_id'        => $user->id,
            'name'           => $data['name'],
            'email'          => $data['email'],
            'phone'          => $data['phone'],
            'address_line1'  => $data['address_line1'],
            'address_line2'  => $data['address_line2'] ?? null,
            'city'           => $data['city'],
            'state'          => $data['state'],
            'pincode'        => $data['pincode'],
            'items'          => $cartItems,
            'coupon_code'    => isset($data['coupon_code']) ? strtoupper(trim($data['coupon_code'])) : null,
            'subtotal'       => $subtotal,
            'discount'       => $discount,
            'total'          => $total,
            'payment_method' => 'online',
            'payment_status' => 'pending',
            'status'         => 'placed',
            'notes'          => $data['notes'] ?? null,
        ]);

        try {
            $api = new RazorpayApi(
                config('services.razorpay.key_id'),
                config('services.razorpay.key_secret')
            );

            // Razorpay expects amount in paise (smallest currency unit)
            $razorpayOrder = $api->order->create([
                'amount'          => (int) round($total * 100),
                'currency'        => 'INR',
                'receipt'         => $order->order_number,
                'payment_capture' => 1,
            ]);

            // Persist the Razorpay order ID so we can verify later
            $order->update(['razorpay_order_id' => $razorpayOrder->id]);

        } catch (\Exception $e) {
            // Roll back the pending order and surface the error
            $order->delete();
            return response()->json([
                'success' => false,
                'message' => 'Unable to initiate payment. Please try again. (' . $e->getMessage() . ')',
            ], 500);
        }

        return response()->json([
            'success'          => true,
            'razorpay_order_id'=> $razorpayOrder->id,
            'amount'           => (int) round($total * 100),
            'currency'         => 'INR',
            'order_id'         => $order->id,
            'key_id'           => config('services.razorpay.key_id'),
            'name'             => $data['name'],
            'email'            => $data['email'],
            'phone'            => $data['phone'],
            'description'      => 'Order ' . $order->order_number,
        ]);
    }

    /**
     * Verify Razorpay payment signature and mark the order as paid.
     * Called via AJAX after the Razorpay modal reports success.
     */
    public function verifyPayment(Request $request)
    {
        $data = $request->validate([
            'razorpay_order_id'   => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature'  => 'required|string',
            'order_id'            => 'required|integer',
        ]);

        $order = Order::findOrFail($data['order_id']);

        // Ownership check
        if ((int) $order->user_id !== (int) Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        try {
            $api = new RazorpayApi(
                config('services.razorpay.key_id'),
                config('services.razorpay.key_secret')
            );

            $api->utility->verifyPaymentSignature([
                'razorpay_order_id'   => $data['razorpay_order_id'],
                'razorpay_payment_id' => $data['razorpay_payment_id'],
                'razorpay_signature'  => $data['razorpay_signature'],
            ]);

        } catch (SignatureVerificationError $e) {
            // Signature mismatch — mark as failed
            $order->update(['payment_status' => 'failed']);
            return response()->json(['success' => false, 'message' => 'Payment verification failed. Please contact support.'], 422);
        }

        // Signature valid → mark paid and clear cart
        $order->update([
            'razorpay_order_id'   => $data['razorpay_order_id'],
            'razorpay_payment_id' => $data['razorpay_payment_id'],
            'razorpay_signature'  => $data['razorpay_signature'],
            'payment_status'      => 'paid',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->cart_data = [];
        $user->save();

        return response()->json([
            'success'        => true,
            'confirmation_url' => route('checkout.confirmation', $order->id),
        ]);
    }

    /**
     * Handle Razorpay payment dismissal / failure reported by the JS modal.
     */
    public function paymentFailed(Request $request)
    {
        $data = $request->validate([
            'order_id' => 'required|integer',
            'reason'   => 'nullable|string|max:500',
        ]);

        $order = Order::find($data['order_id']);

        if ($order && (int) $order->user_id === (int) Auth::id()) {
            $order->update(['payment_status' => 'failed']);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Show the order confirmation page.
     */
    public function confirmation(Order $order)
    {
        if ((int) $order->user_id !== (int) Auth::id()) {
            abort(403);
        }

        return view('checkout.confirmation', compact('order'));
    }

    /**
     * Show all orders for the authenticated user.
     */
    public function myOrders()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Show details of a specific order.
     */
    public function orderDetail(Order $order)
    {
        if ((int) $order->user_id !== (int) Auth::id()) {
            abort(403);
        }

        return view('orders.detail', compact('order'));
    }

    /**
     * Render a print-ready invoice for the given order.
     */
    public function downloadInvoice(Order $order)
    {
        if ((int) $order->user_id !== (int) Auth::id()) {
            abort(403);
        }

        return view('orders.invoice', compact('order'));
    }

    /**
     * Validate a coupon code and return the discount info as JSON.
     */
    public function validateCoupon(Request $request)
    {
        $data = $request->validate([
            'coupon_code' => 'required|string|max:100',
            'subtotal'    => 'required|numeric|min:0',
        ]);

        $user       = Auth::user();
        $couponCode = trim($data['coupon_code']);
        $subtotal   = (float) $data['subtotal'];
        $discount   = 0;
        $percentage = 0;
        $message    = '';
        $valid      = false;

        $offer = PromotionOffer::active()
            ->where('coupon_code', $couponCode)
            ->first();

        if ($offer) {
            if ($offer->percentage > 0 && $offer->isApplicableForOrder($subtotal, $user)) {
                $percentage = (int) $offer->percentage;
                $discount   = (int) round($subtotal * ($percentage / 100));
                $message    = $percentage . '% off applied — ' . $offer->title;
                $valid      = true;
            } elseif (!$offer->isApplicableForOrder($subtotal, $user)) {
                if ($offer->type === 'first_order') {
                    $message = 'This coupon is for first orders only.';
                } elseif ($offer->type === 'amount_based' && $offer->target_amount) {
                    $message = 'Add ₹' . number_format(max(0, $offer->target_amount - $subtotal)) . ' more to use this coupon.';
                } else {
                    $message = 'This coupon is not applicable on your current order.';
                }
            } else {
                $message = 'This coupon has no discount configured.';
            }
        } else {
            $settings  = PromotionSetting::current();
            $upperCode = strtoupper($couponCode);
            $widgetMap = [
                strtoupper((string) $settings->special_offer_coupon_code) => [$settings->special_offer_percentage, 'Special Offer'],
                strtoupper((string) $settings->floating_coupon_code)      => [$settings->floating_coupon_percentage, $settings->floating_coupon_title ?? 'Discount'],
                strtoupper((string) $settings->welcome_modal_code)        => [$settings->welcome_modal_percentage, $settings->welcome_modal_title ?? 'Welcome Offer'],
                strtoupper((string) $settings->scroll_offer_code)         => [$settings->scroll_offer_percentage, $settings->scroll_offer_title ?? 'Flash Sale'],
            ];

            if (isset($widgetMap[$upperCode]) && $widgetMap[$upperCode][0] > 0) {
                [$pct, $label] = $widgetMap[$upperCode];
                $percentage = (int) $pct;
                $discount   = (int) round($subtotal * ($percentage / 100));
                $message    = $percentage . '% off applied — ' . $label;
                $valid      = true;
            } else {
                $message = 'Invalid or expired coupon code.';
            }
        }

        return response()->json([
            'valid'      => $valid,
            'discount'   => $discount,
            'percentage' => $percentage,
            'message'    => $message,
            'code'       => $valid ? strtoupper($couponCode) : null,
        ]);
    }

    /**
     * Check first-order eligibility for a coupon and return JSON.
     */
    public function applyOffer(Request $request)
    {
        $data = $request->validate([
            'coupon_code' => 'required|string|max:100',
        ]);

        if (!Auth::check()) {
            return response()->json([
                'ok'      => false,
                'reason'  => 'unauthenticated',
                'message' => 'Please log in to apply this offer.',
            ], 401);
        }

        $user       = Auth::user();
        $couponCode = trim($data['coupon_code']);

        $offer = PromotionOffer::active()
            ->where('coupon_code', $couponCode)
            ->first();

        if (!$offer) {
            return response()->json([
                'ok'      => false,
                'reason'  => 'invalid',
                'message' => 'Invalid or expired coupon code.',
            ]);
        }

        if ($offer->type === 'first_order') {
            $hasOrders = Order::where('user_id', $user->id)->exists();
            if ($hasOrders) {
                return response()->json([
                    'ok'      => false,
                    'reason'  => 'not_first_order',
                    'message' => 'This offer is only valid on your first order.',
                ]);
            }
        }

        return response()->json([
            'ok'          => true,
            'coupon_code' => strtoupper($couponCode),
            'message'     => ($offer->percentage > 0 ? $offer->percentage . '% off' : $offer->discount_text) . ' applied — ' . $offer->title,
        ]);
    }

    /**
     * Check whether the authenticated user has already used a given coupon code.
     */
    public function hasUsedCoupon(Request $request)
    {
        $data = $request->validate([
            'coupon_code' => 'required|string|max:100',
        ]);

        if (!Auth::check()) {
            return response()->json(['used' => false], 401);
        }

        $user = Auth::user();
        $code = trim($data['coupon_code']);

        $used = Order::where('user_id', $user->id)
            ->whereRaw('LOWER(coupon_code) = ?', [strtolower($code)])
            ->exists();

        return response()->json(['used' => (bool) $used]);
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function calcTotals(array $items, ?string $couponCode = null, $user = null): array
    {
        $subtotal = array_sum(array_map(fn($i) => $i['price'] * $i['qty'], $items));
        $discount = 0;

        if ($couponCode) {
            $offer = PromotionOffer::active()
                ->where('coupon_code', $couponCode)
                ->first();

            if ($offer && $offer->percentage > 0 && $offer->isApplicableForOrder($subtotal, $user)) {
                $discount = round($subtotal * ($offer->percentage / 100));
            }

            if ($discount === 0) {
                $settings = PromotionSetting::current();

                $widgetMap = [
                    $settings->special_offer_coupon_code => $settings->special_offer_percentage,
                    $settings->floating_coupon_code      => $settings->floating_coupon_percentage,
                    $settings->welcome_modal_code        => $settings->welcome_modal_percentage,
                    $settings->scroll_offer_code         => $settings->scroll_offer_percentage,
                ];

                $upperCode = strtoupper(trim($couponCode));
                foreach ($widgetMap as $code => $pct) {
                    if ($pct > 0 && strtoupper(trim((string) $code)) === $upperCode) {
                        $discount = round($subtotal * ($pct / 100));
                        break;
                    }
                }
            }
        }

        $amountAfterDiscount = $subtotal - $discount;
        $total = $amountAfterDiscount;

        return compact('subtotal', 'discount', 'total');
    }
}
