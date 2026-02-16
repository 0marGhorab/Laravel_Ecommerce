<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoBanner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BannerController extends Controller
{
    public function index(): View
    {
        $banners = PromoBanner::ordered()->paginate(10);
        return view('admin.banners.index', compact('banners'));
    }

    public function create(): View
    {
        return view('admin.banners.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'cta_text' => 'nullable|string|max:100',
            'cta_url' => 'nullable|string|max:500',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('banners', 'public');
        }
        PromoBanner::create($data);
        return redirect()->route('admin.banners.index')->with('success', 'Banner created.');
    }

    public function edit(PromoBanner $banner): View
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, PromoBanner $banner): RedirectResponse
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'cta_text' => 'nullable|string|max:100',
            'cta_url' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        if ($request->hasFile('image')) {
            if ($banner->image) {
                Storage::disk('public')->delete($banner->image);
            }
            $data['image'] = $request->file('image')->store('banners', 'public');
        }
        $banner->update($data);
        return redirect()->route('admin.banners.index')->with('success', 'Banner updated.');
    }

    public function destroy(PromoBanner $banner): RedirectResponse
    {
        if ($banner->image) {
            Storage::disk('public')->delete($banner->image);
        }
        $banner->delete();
        return redirect()->route('admin.banners.index')->with('success', 'Banner deleted.');
    }
}
