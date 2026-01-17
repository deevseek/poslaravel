<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        $perPage = $request->integer('per_page', 15);
        $categories = $query->paginate($perPage);

        return response()->json([
            'data' => $categories->items(),
            'meta' => [
                'current_page' => $categories->currentPage(),
                'last_page' => $categories->lastPage(),
                'per_page' => $categories->perPage(),
                'total' => $categories->total(),
                'from' => $categories->firstItem(),
                'to' => $categories->lastItem(),
            ],
            'links' => [
                'first' => $categories->url(1),
                'last' => $categories->url($categories->lastPage()),
                'prev' => $categories->previousPageUrl(),
                'next' => $categories->nextPageUrl(),
            ],
        ]);
    }

    public function show(Category $category)
    {
        return response()->json(['data' => $category]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $category = Category::create($validated);

        return response()->json(['data' => $category], 201);
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
        ]);

        $category->update($validated);

        return response()->json(['data' => $category]);
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json(['message' => 'Deleted.']);
    }
}
