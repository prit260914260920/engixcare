<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user')->latest();

        // Search by order number, name, email, or phone
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sub) use ($q) {
                $sub->where('name',  'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhereRaw("CONCAT('ENG-', LPAD(id, 6, '0')) LIKE ?", ["%{$q}%"]);
            });
        }

        // Filter by fulfilment status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $orders = $query->paginate(20)->withQueryString();

        // ── Summary stats ──────────────────────────────────────────────────
        $stats = [
            'total'    => Order::count(),
            'placed'   => Order::where('status', 'placed')->count(),
            'revenue'  => Order::where('payment_status', 'paid')->sum('total'),
            'pending'  => Order::where('payment_status', 'pending')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    /**
     * Dedicated Payments page — focuses on payment status & method.
     */
    public function payments(Request $request)
    {
        $query = Order::with('user')->latest();

        // Search by order number, name, email, or phone
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sub) use ($q) {
                $sub->where('name',  'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhereRaw("CONCAT('ENG-', LPAD(id, 6, '0')) LIKE ?", ["%{$q}%"]);
            });
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $orders = $query->paginate(20)->withQueryString();

        // ── Payment-focused stats ──────────────────────────────────────────
        $stats = [
            'total'   => Order::count(),
            'paid'    => Order::where('payment_status', 'paid')->count(),
            'pending' => Order::where('payment_status', 'pending')->count(),
            'failed'  => Order::where('payment_status', 'failed')->count(),
            'revenue' => Order::where('payment_status', 'paid')->sum('total'),
            'online'  => Order::where('payment_method', 'online')->count(),
            'cod'     => Order::where('payment_method', 'cod')->count(),
        ];

        return view('admin.payments.index', compact('orders', 'stats'));
    }

    /**
     * Return a single order as JSON (for the detail modal).
     */
    public function show(Order $order)
    {
        return response()->json([
            'id'             => $order->id,
            'order_number'   => $order->order_number,
            'name'           => $order->name,
            'email'          => $order->email,
            'phone'          => $order->phone,
            'address'        => implode(', ', array_filter([
                $order->address_line1,
                $order->address_line2,
                $order->city,
                $order->state,
                $order->pincode,
            ])),
            'items'          => $order->items,
            'subtotal'       => number_format($order->subtotal, 2),
            'discount'       => number_format($order->discount, 2),
            'gst'            => number_format($order->gst ?? 0, 2),
            'total'          => number_format($order->total, 2),
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status,
            'status'         => $order->status,
            'coupon_code'    => $order->coupon_code,
            'razorpay_payment_id' => $order->razorpay_payment_id,
            'notes'          => $order->notes,
            'created_at'     => $order->created_at->format('d M Y, H:i'),
        ]);
    }

    /**
     * Update the fulfilment status of an order (AJAX PATCH).
     */
    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => 'required|in:placed,processing,shipped,delivered,cancelled',
        ]);

        $order->update(['status' => $data['status']]);

        return response()->json([
            'success' => true,
            'status'  => $order->status,
            'message' => 'Order status updated to ' . ucfirst($order->status) . '.',
        ]);
    }

    /**
     * Update the payment status of an order (AJAX PATCH).
     */
    public function updatePaymentStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'payment_status' => 'required|in:pending,paid,failed',
        ]);

        $order->update(['payment_status' => $data['payment_status']]);

        return response()->json([
            'success'        => true,
            'payment_status' => $order->payment_status,
            'message'        => 'Payment status updated to ' . ucfirst($order->payment_status) . '.',
        ]);
    }
}
