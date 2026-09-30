<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShiprocketService
{
    private string $baseUrl = 'https://apiv2.shiprocket.in/v1/external';
    private string $token;
    private string $pickupLocation;

    // ── Box definitions ───────────────────────────────────────────────────────
    // Small box  : max 2 qty units
    // Medium box : max 4 qty units
    private const BOX_SMALL = [
        'capacity' => 2,
        'length'   => 16,   // cm
        'breadth'  => 10,   // cm
        'height'   => 5,    // cm
        'weight'   => 0.20, // kg
    ];

    private const BOX_MEDIUM = [
        'capacity' => 4,
        'length'   => 19,   // cm
        'breadth'  => 15,   // cm
        'height'   => 4,    // cm
        'weight'   => 0.40, // kg
    ];

    public function __construct()
    {
        $this->token          = config('services.shiprocket.token', '');
        $this->pickupLocation = config('services.shiprocket.pickup_location', '');
    }

    /**
     * Calculate box dimensions for the given total qty.
     *
     * Strategy:
     *   qty <= 2  → 1 small box
     *   qty 3–4   → 1 medium box
     *   qty > 4   → fill medium boxes first, leftover in small boxes
     *
     * For multiple boxes Shiprocket expects a single combined dimension:
     *   - length  = max length across all boxes
     *   - breadth = max breadth across all boxes
     *   - height  = sum of heights (stacked)
     *   - weight  = sum of all box weights (packaging) + product weight (approx 0 here)
     *
     * @return array{length: float, breadth: float, height: float, weight: float}
     */
    private function calcBoxDimensions(int $totalQty): array
    {
        $smallBoxes  = 0;
        $mediumBoxes = 0;

        if ($totalQty <= 2) {
            $smallBoxes = 1;
        } elseif ($totalQty <= 4) {
            $mediumBoxes = 1;
        } else {
            // Fill medium boxes first, then use small for leftovers
            $mediumBoxes = intdiv($totalQty, self::BOX_MEDIUM['capacity']);
            $remaining   = $totalQty % self::BOX_MEDIUM['capacity'];

            if ($remaining > 0) {
                // Leftover fits in a small box (capacity 2)?
                // If remaining > small capacity, use one more medium instead
                if ($remaining <= self::BOX_SMALL['capacity']) {
                    $smallBoxes = 1;
                } else {
                    $mediumBoxes++;
                }
            }
        }

        // Combined dimensions
        $allBoxLengths  = array_merge(
            array_fill(0, $mediumBoxes, self::BOX_MEDIUM['length']),
            array_fill(0, $smallBoxes,  self::BOX_SMALL['length'])
        );
        $allBoxBreadths = array_merge(
            array_fill(0, $mediumBoxes, self::BOX_MEDIUM['breadth']),
            array_fill(0, $smallBoxes,  self::BOX_SMALL['breadth'])
        );
        $allBoxHeights  = array_merge(
            array_fill(0, $mediumBoxes, self::BOX_MEDIUM['height']),
            array_fill(0, $smallBoxes,  self::BOX_SMALL['height'])
        );
        $allBoxWeights  = array_merge(
            array_fill(0, $mediumBoxes, self::BOX_MEDIUM['weight']),
            array_fill(0, $smallBoxes,  self::BOX_SMALL['weight'])
        );

        return [
            'length'  => (float) max($allBoxLengths),
            'breadth' => (float) max($allBoxBreadths),
            'height'  => (float) array_sum($allBoxHeights),
            'weight'  => (float) round(array_sum($allBoxWeights), 2),
        ];
    }

    /**
     * Push a confirmed order to Shiprocket.
     * Returns the Shiprocket order_id on success, or null on failure.
     */
    public function createOrder(Order $order): ?string
    {
        if (empty($this->token)) {
            Log::warning('Shiprocket: token not configured, skipping order push.', [
                'order_id' => $order->id,
            ]);
            return null;
        }

        // ── Build order_items array from the stored cart items ────────────────
        $orderItems = [];
        foreach ($order->items as $item) {
            $orderItems[] = [
                'name'          => $item['name'] ?? 'Product',
                'sku'           => $item['sku']  ?? ('SKU-' . ($item['product_id'] ?? '0')),
                'units'         => (int) ($item['qty']   ?? 1),
                'selling_price' => (float) ($item['price'] ?? 0),
                'discount'      => '',
                'tax'           => '',
                'hsn'           => '',
            ];
        }

        // ── Split full name into first + last ─────────────────────────────────
        $nameParts = explode(' ', trim($order->name), 2);
        $firstName = $nameParts[0];
        $lastName  = $nameParts[1] ?? '';

        // ── Map payment method ────────────────────────────────────────────────
        $paymentMethod = strtolower($order->payment_method) === 'cod' ? 'COD' : 'Prepaid';

        // ── Calculate box dimensions based on total qty ───────────────────────
        $totalQty   = array_sum(array_column($order->items, 'qty'));
        $dimensions = $this->calcBoxDimensions((int) $totalQty);

        // ── Build the payload ─────────────────────────────────────────────────
        $payload = [
            'order_id'               => (string) $order->id,
            'order_date'             => $order->created_at->format('Y-m-d H:i'),
            'pickup_location'        => $this->pickupLocation,
            'comment'                => $order->notes ?? '',
            'billing_customer_name'  => $firstName,
            'billing_last_name'      => $lastName,
            'billing_address'        => $order->address_line1,
            'billing_address_2'      => $order->address_line2 ?? '',
            'billing_city'           => $order->city,
            'billing_pincode'        => (int) $order->pincode,
            'billing_state'          => $order->state,
            'billing_country'        => 'India',
            'billing_email'          => $order->email,
            'billing_phone'          => (int) $order->phone,
            'shipping_is_billing'    => true,
            'order_items'            => $orderItems,
            'payment_method'         => $paymentMethod,
            'shipping_charges'       => 0,
            'giftwrap_charges'       => 0,
            'transaction_charges'    => 0,
            'total_discount'         => (float) ($order->discount ?? 0),
            'sub_total'              => (float) $order->total,
            'length'                 => $dimensions['length'],
            'breadth'                => $dimensions['breadth'],
            'height'                 => $dimensions['height'],
            'weight'                 => $dimensions['weight'],
        ];

        try {
            $response = Http::withToken($this->token)
                ->timeout(30)
                ->post("{$this->baseUrl}/orders/create/adhoc", $payload);

            if ($response->successful()) {
                $shiprocketOrderId = $response->json('order_id') ?? $response->json('shipment_id');

                Log::info('Shiprocket: order created successfully.', [
                    'order_id'            => $order->id,
                    'shiprocket_order_id' => $shiprocketOrderId,
                ]);

                return (string) $shiprocketOrderId;
            }

            Log::error('Shiprocket: API returned non-2xx response.', [
                'order_id' => $order->id,
                'status'   => $response->status(),
                'body'     => $response->body(),
            ]);

        } catch (\Throwable $e) {
            Log::error('Shiprocket: exception while creating order.', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);
        }

        return null;
    }
}
