<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi Data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'category' => 'required|string',
            'stock' => 'required|integer',
            // Validasi untuk bagian JSON
            'specs' => 'nullable|array',
            'marketing' => 'nullable|array',
            'production' => 'nullable|array',
            'images' => 'nullable|array',
        ]);

        // 2. Simpan ke Database
        $product = Product::create([
            'sku' => 'LX-' . strtoupper(Str::random(6)), // Auto-generate SKU Luxury
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'category' => $validated['category'],

            // Laravel 13 otomatis encode array ini ke JSON karena $casts di Model
            'specifications' => $request->specs,
            'marketing_assets' => $request->marketing,
            'production_costs' => $request->production,
            'media' => $request->images, // Array of URLs/Paths

            'is_published' => $request->has('is_published'),
        ]);

        return response()->json([
            'message' => 'Product Created Successfully!',
            'data' => $product
        ], 201);
    }
}
