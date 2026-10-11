<?php

namespace App\Http\Controllers;

use App\Models\FestiveBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FestiveBannerController extends Controller
{
    // ── Admin: list ───────────────────────────────────────────────────────────
    public function index()
    {
        $banners = FestiveBanner::orderBy('sort_order')->orderByDesc('id')->get();
        return view('admin.festive_banners.index', compact('banners'));
    }

    // ── Admin: show create form ───────────────────────────────────────────────
    public function create()
    {
        return view('admin.festive_banners.form', ['banner' => null]);
    }

    // ── Admin: store new banner ───────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $this->validateBanner($request);
        if ($validated->fails()) {
            return back()->withErrors($validated)->withInput();
        }

        FestiveBanner::create($this->buildPayload($request));

        return redirect()->route('admin.festive_banners.index')
            ->with('success', 'Festive banner created successfully.');
    }

    // ── Admin: show edit form ─────────────────────────────────────────────────
    public function edit(FestiveBanner $festiveBanner)
    {
        return view('admin.festive_banners.form', ['banner' => $festiveBanner]);
    }

    // ── Admin: update banner ──────────────────────────────────────────────────
    public function update(Request $request, FestiveBanner $festiveBanner)
    {
        $validated = $this->validateBanner($request);
        if ($validated->fails()) {
            return back()->withErrors($validated)->withInput();
        }

        $festiveBanner->update($this->buildPayload($request, $festiveBanner));

        return redirect()->route('admin.festive_banners.index')
            ->with('success', 'Festive banner updated successfully.');
    }

    // ── Admin: delete banner ──────────────────────────────────────────────────
    public function destroy(FestiveBanner $festiveBanner)
    {
        // Delete uploaded images from storage
        foreach (['bg_image_path', 'product_image_path', 'accent_image_path', 'full_image_path'] as $field) {
            if ($festiveBanner->$field) {
                Storage::disk('public')->delete($festiveBanner->$field);
            }
        }

        $festiveBanner->delete();

        return redirect()->route('admin.festive_banners.index')
            ->with('success', 'Festive banner deleted.');
    }

    // ── Admin: toggle active ──────────────────────────────────────────────────
    public function toggleActive(FestiveBanner $festiveBanner)
    {
        $festiveBanner->update(['is_active' => !$festiveBanner->is_active]);

        return response()->json([
            'success'   => true,
            'is_active' => $festiveBanner->is_active,
        ]);
    }

    // ── AJAX: upload an image ─────────────────────────────────────────────────
    public function uploadImage(Request $request)
    {
        if (!$request->hasFile('image') || !$request->file('image')->isValid()) {
            return response()->json(['message' => 'No valid file received.'], 422);
        }

        $request->validate([
            'image' => 'image|mimes:jpeg,png,jpg,webp|max:8192',
        ]);

        // Optionally delete the old file when replacing
        $oldPath = $request->input('old_path');
        if ($oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        $path = $request->file('image')->store('festive-banners', 'public');

        return response()->json([
            'path' => $path,
            'url'  => Storage::disk('public')->url($path),
        ]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function validateBanner(Request $request)
    {
        return Validator::make($request->all(), [
            'name'               => 'required|string|max:255',
            'banner_type'        => 'required|in:custom,image_only',
            'tag_line'           => 'nullable|string|max:255',
            'title'              => 'nullable|string|max:255',
            'offer_label'        => 'nullable|string|max:255',
            'discount_text'      => 'nullable|string|max:100',
            'sub_text'           => 'nullable|string|max:500',
            'fine_print'         => 'nullable|string|max:500',
            'cta_label'          => 'nullable|string|max:100',
            'cta_url'            => 'nullable|string|max:500',
            'bg_image_path'      => 'nullable|string',
            'product_image_path' => 'nullable|string',
            'accent_image_path'  => 'nullable|string',
            'full_image_path'    => 'nullable|string',
            'full_image_url'     => 'nullable|string|max:500',
            'bg_color_from'      => 'nullable|string|max:20',
            'bg_color_to'        => 'nullable|string|max:20',
            'text_color'         => 'nullable|string|max:20',
            'discount_bg_color'  => 'nullable|string|max:20',
            'discount_text_color'=> 'nullable|string|max:20',
            'is_active'          => 'boolean',
            'start_date'         => 'nullable|date',
            'end_date'           => 'nullable|date|after_or_equal:start_date',
            'sort_order'         => 'nullable|integer|min:0',
        ]);
    }

    private function buildPayload(Request $request, ?FestiveBanner $existing = null): array
    {
        $type = $request->input('banner_type', 'custom');

        return [
            'name'               => $request->name,
            'banner_type'        => $type,
            'tag_line'           => $request->tag_line,
            'title'              => $request->title,
            'offer_label'        => $request->offer_label,
            'discount_text'      => $request->discount_text,
            'sub_text'           => $request->sub_text,
            'fine_print'         => $request->fine_print,
            'cta_label'          => $request->input('cta_label', 'SHOP NOW'),
            'cta_url'            => $request->input('cta_url', '#products'),
            'bg_image_path'      => $request->input('bg_image_path') ?: ($existing?->bg_image_path),
            'product_image_path' => $request->input('product_image_path') ?: ($existing?->product_image_path),
            'accent_image_path'  => $request->input('accent_image_path') ?: ($existing?->accent_image_path),
            'full_image_path'    => $request->input('full_image_path') ?: ($existing?->full_image_path),
            'full_image_url'     => $request->input('full_image_url'),
            'bg_color_from'      => $request->input('bg_color_from', '#c0392b'),
            'bg_color_to'        => $request->input('bg_color_to', '#8b1a1a'),
            'text_color'         => $request->input('text_color', '#ffffff'),
            'discount_bg_color'  => $request->input('discount_bg_color', '#ffffff'),
            'discount_text_color'=> $request->input('discount_text_color', '#c0392b'),
            'is_active'          => $request->boolean('is_active'),
            'start_date'         => $request->start_date ?: null,
            'end_date'           => $request->end_date ?: null,
            'sort_order'         => $request->input('sort_order', 0),
        ];
    }
}
