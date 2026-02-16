<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with('category')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate(15)->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:100',
            'short_description' => 'nullable|string',
            'long_description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,draft,archived',
        ]);
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        if (Product::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $data['slug'] . '-' . uniqid();
        }
        $data['category_id'] = $request->filled('category_id') ? $data['category_id'] : null;
        Product::create($data);
        return redirect()->route('admin.products.index')->with('success', 'Product created.');
    }

    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:100',
            'short_description' => 'nullable|string',
            'long_description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,draft,archived',
        ]);
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        if (Product::where('slug', $data['slug'])->where('id', '!=', $product->id)->exists()) {
            $data['slug'] = $data['slug'] . '-' . uniqid();
        }
        $data['category_id'] = $request->filled('category_id') ? $data['category_id'] : null;
        $product->update($data);
        return redirect()->route('admin.products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted.');
    }
}
