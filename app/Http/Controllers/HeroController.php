<?php

namespace App\Http\Controllers;

use App\Models\HeroSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class HeroController extends Controller
{
    // ── Admin: show the edit form ─────────────────────────────────────────────
    public function index()
    {
        $hero = HeroSection::current();

        return view('admin.hero.index', compact('hero'));
    }

    // ── Admin: save changes ───────────────────────────────────────────────────
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pill_text'           => 'required|string|max:255',
            'title_main'          => 'required|string|max:255',
            'title_gradient'      => 'required|string|max:255',
            'subtitle'            => 'nullable|string',
            'cta_primary_label'   => 'required|string|max:100',
            'cta_primary_url'     => 'required|string|max:255',
            'cta_secondary_label' => 'required|string|max:100',
            'cta_secondary_url'   => 'required|string|max:255',
            'badge_rating'        => 'nullable|string|max:100',
            'badge_lab'           => 'nullable|string|max:100',
            'image_path'          => 'nullable|string',
            // Minerals & trust badges come in as flat indexed arrays from the form
            'minerals'            => 'nullable|array',
            'minerals.*.label'    => 'required|string|max:100',
            'minerals.*.value'    => 'required|string|max:100',
            'trust_badges'        => 'nullable|array',
            'trust_badges.*.icon' => 'required|string|max:100',
            'trust_badges.*.label'=> 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Please fix the errors below.');
        }

        $hero = HeroSection::current();

        $hero->update([
            'pill_text'           => $request->pill_text,
            'title_main'          => $request->title_main,
            'title_gradient'      => $request->title_gradient,
            'subtitle'            => $request->subtitle,
            'cta_primary_label'   => $request->cta_primary_label,
            'cta_primary_url'     => $request->cta_primary_url,
            'cta_secondary_label' => $request->cta_secondary_label,
            'cta_secondary_url'   => $request->cta_secondary_url,
            'badge_rating'        => $request->badge_rating,
            'badge_lab'           => $request->badge_lab,
            'minerals'            => $request->input('minerals', []),
            'trust_badges'        => $request->input('trust_badges', []),
            'image_path'          => $request->input('image_path') ?: $hero->image_path,
        ]);

        return back()->with('success', 'Hero section updated successfully.');
    }

    // ── AJAX: upload the hero image ───────────────────────────────────────────
    public function uploadImage(Request $request)
    {
        if (!$request->hasFile('image') || !$request->file('image')->isValid()) {
            return response()->json(['message' => 'No valid file received.'], 422);
        }

        $request->validate([
            'image' => 'image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        // Delete the old hero image from storage if one exists
        $hero = HeroSection::current();
        if ($hero->image_path) {
            Storage::disk('public')->delete($hero->image_path);
        }

        $path = $request->file('image')->store('hero', 'public');

        return response()->json([
            'path' => $path,
            'url'  => Storage::disk('public')->url($path),
        ]);
    }
}
