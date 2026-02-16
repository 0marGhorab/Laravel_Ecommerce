<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $query = Category::withCount('products')->latest('sort_order');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $categories = $query->paginate(15)->withQueryString();
        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        $parentCategories = Category::whereNull('parent_id')->orderBy('name')->get();
        return view('admin.categories.create', compact('parentCategories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'parent_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        if (Category::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $data['slug'] . '-' . uniqid();
        }
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['parent_id'] = $request->filled('parent_id') ? $data['parent_id'] : null;
        Category::create($data);
        return redirect()->route('admin.categories.index')->with('success', 'Category created.');
    }

    public function edit(Category $category): View
    {
        $parentCategories = Category::whereNull('parent_id')->where('id', '!=', $category->id)->orderBy('name')->get();
        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'parent_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        if (Category::where('slug', $data['slug'])->where('id', '!=', $category->id)->exists()) {
            $data['slug'] = $data['slug'] . '-' . uniqid();
        }
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['parent_id'] = $request->filled('parent_id') ? $data['parent_id'] : null;
        $category->update($data);
        return redirect()->route('admin.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'Cannot delete category with products. Reassign or delete products first.');
        }
        $category->children()->update(['parent_id' => null]);
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Category deleted.');
    }
}
