<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\ShiprocketService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncShiprocketOrderStatus extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'shiprocket:sync-status
                            {--dry-run : Log changes without saving to DB}';

    /**
     * The console command description.
     */
    protected $description = 'Fetch live status from Shiprocket for all active orders and update local status';

    public function __construct(private readonly ShiprocketService $shiprocket)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

        // ── Fetch all orders that are NOT in a terminal state ─────────────────
        // Terminal states: reached, delivered, cancelled
        // Also skip orders that haven't been pushed to Shiprocket yet
        // (no shiprocket_order_id means they haven't been pushed)
        $orders = Order::whereNotNull('shiprocket_order_id')
            ->whereNotIn('status', Order::TERMINAL_STATUSES)
            ->get();

        if ($orders->isEmpty()) {
            $this->info('No active orders to sync.');
            return self::SUCCESS;
        }

        $this->info("Syncing status for {$orders->count()} order(s)..." . ($isDryRun ? ' [DRY RUN]' : ''));

        $updated = 0;
        $failed  = 0;

        foreach ($orders as $order) {
            $data = $this->shiprocket->fetchOrderStatus($order->shiprocket_order_id);

            if ($data === null) {
                $this->warn("  ✗ Order #{$order->id} (SR: {$order->shiprocket_order_id}) — API call failed");
                $failed++;
                continue;
            }

            // Pull shipment status from the response
            // Primary source: data.shipments.status
            // Fallback: data.status (order-level status)
            $rawStatus = strtoupper(
                $data['shipments']['status'] ?? $data['status'] ?? ''
            );

            if (empty($rawStatus)) {
                $this->warn("  ? Order #{$order->id} — no status in response, skipping");
                continue;
            }

            // Map Shiprocket status → our local status
            $newStatus = Order::SHIPROCKET_STATUS_MAP[$rawStatus] ?? null;

            if ($newStatus === null) {
                // Unknown status — log it so we can add a mapping later
                Log::warning('Shiprocket: unmapped status received.', [
                    'order_id'   => $order->id,
                    'raw_status' => $rawStatus,
                ]);
                $this->warn("  ? Order #{$order->id} — unmapped Shiprocket status: {$rawStatus}");
                continue;
            }

            if ($order->status === $newStatus) {
                $this->line("  ~ Order #{$order->id} — status unchanged ({$newStatus})");
                continue;
            }

            $oldStatus = $order->status;

            if (!$isDryRun) {
                $order->update(['status' => $newStatus]);
            }

            Log::info('Shiprocket: order status synced.', [
                'order_id'   => $order->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'raw'        => $rawStatus,
                'dry_run'    => $isDryRun,
            ]);

            $this->info("  ✓ Order #{$order->id} — {$oldStatus} → {$newStatus}");
            $updated++;
        }

        $this->newLine();
        $this->info("Done. Updated: {$updated}, Failed: {$failed}, Total checked: {$orders->count()}");

        return self::SUCCESS;
    }
}
