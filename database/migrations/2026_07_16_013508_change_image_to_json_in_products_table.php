<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Migrate existing string image values into a JSON array
        DB::table('products')->whereNotNull('image')->where('image', '!=', '')->get()
            ->each(function ($row) {
                DB::table('products')->where('id', $row->id)->update([
                    'image' => json_encode([$row->image]),
                ]);
            });

        Schema::table('products', function (Blueprint $table) {
            $table->text('image')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Convert back: take first element of JSON array as a plain string
        DB::table('products')->whereNotNull('image')->get()
            ->each(function ($row) {
                $arr = json_decode($row->image, true);
                DB::table('products')->where('id', $row->id)->update([
                    'image' => is_array($arr) ? ($arr[0] ?? null) : $row->image,
                ]);
            });
    }
};
