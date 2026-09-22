<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\Product;
use App\Models\PromotionOffer;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Revenue Stats ────────────────────────────────────────────────────
        $totalRevenue   = Order::where('payment_status', 'paid')->sum('total');
        $todaySales     = Order::where('payment_status', 'paid')
            ->whereDate('created_at', today())
            ->sum('total');

        // ── Order Stats ──────────────────────────────────────────────────────
        $totalOrders     = Order::count();
        $pendingOrders   = Order::where('status', 'pending')->count();
        $completedOrders = Order::where('status', 'delivered')->count();

        // ── Other Counts ─────────────────────────────────────────────────────
        $totalProducts = Product::count();
        $totalUsers    = User::where('is_admin', false)->count();
        $totalOffers   = PromotionOffer::count();
        $newInquiries  = ContactMessage::count();

        // ── Recent Orders (last 8) ───────────────────────────────────────────
        $recentOrders = Order::latest()
            ->take(8)
            ->get();

        // ── Latest Inquiries (last 5) ────────────────────────────────────────
        $latestInquiries = ContactMessage::latest()
            ->take(5)
            ->get();

        // ── Low Stock Products (stock <= 10) ─────────────────────────────────
        $lowStockProducts = Product::where('stock', '<=', 10)
            ->where('status', 'Active')
            ->orderBy('stock')
            ->take(5)
            ->get();

        // ── Top Selling Products via order items ─────────────────────────────
        // Orders store items as JSON; we tally per product_id in PHP
        $topProducts = $this->getTopSellingProducts(5);

        // ── Monthly Revenue (last 6 months for bar chart) ────────────────────
        $monthlyRevenue = $this->getMonthlyRevenue(6);

        return view('admin.dashboard.index', compact(
            'totalRevenue',
            'todaySales',
            'totalOrders',
            'pendingOrders',
            'completedOrders',
            'totalProducts',
            'totalUsers',
            'totalOffers',
            'newInquiries',
            'recentOrders',
            'latestInquiries',
            'lowStockProducts',
            'topProducts',
            'monthlyRevenue',
        ));
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Aggregate sold quantities from Order->items JSON and
     * return the top-N products with total_sold and total_revenue.
     */
    private function getTopSellingProducts(int $limit): \Illuminate\Support\Collection
    {
        $orders = Order::whereNotIn('status', ['cancelled'])->get(['items']);

        $tally = [];
        foreach ($orders as $order) {
            foreach ((array) $order->items as $item) {
                $id = $item['product_id'] ?? ($item['id'] ?? null);
                if (!$id) continue;
                $tally[$id]['qty']     = ($tally[$id]['qty']     ?? 0) + ($item['quantity'] ?? 1);
                $tally[$id]['revenue'] = ($tally[$id]['revenue'] ?? 0)
                    + (($item['price'] ?? 0) * ($item['quantity'] ?? 1));
            }
        }

        arsort($tally);   // sort by qty desc
        $topIds = array_slice(array_keys($tally), 0, $limit, true);

        if (empty($topIds)) {
            return collect();
        }

        $products = Product::whereIn('id', $topIds)->get()->keyBy('id');

        return collect($topIds)->map(function ($id) use ($products, $tally) {
            $product = $products->get($id);
            if (!$product) return null;
            return (object) [
                'product'      => $product,
                'total_sold'   => $tally[$id]['qty'],
                'total_revenue' => $tally[$id]['revenue'],
            ];
        })->filter()->values();
    }

    /**
     * Return last N months of paid-order revenue, oldest first.
     * Each entry: { label: 'Jul', revenue: 1234.56, pct: 80 }
     */
    private function getMonthlyRevenue(int $months): \Illuminate\Support\Collection
    {
        $orders = Order::where('payment_status', 'paid')
            ->where('created_at', '>=', now()->subMonths($months)->startOfMonth())
            ->get(['created_at', 'total']);

        $rows = $orders->groupBy(function (Order $order) {
            return $order->created_at->format('Y-m');
        })->map(function ($orders) {
            return (object) ['revenue' => $orders->sum('total')];
        });

        $max = $rows->max('revenue') ?: 1;

        $result = collect();
        for ($i = $months - 1; $i >= 0; $i--) {
            $key     = now()->subMonths($i)->format('Y-m');
            $label   = now()->subMonths($i)->format('M');
            $revenue = $rows->has($key) ? (float) $rows[$key]->revenue : 0;
            $result->push((object) [
                'label'   => $label,
                'revenue' => $revenue,
                'pct'     => (int) round(($revenue / $max) * 100),
            ]);
        }
        return $result;
    }
}
