<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    // Show upload form and product list
    public function create()
    {
        // Get latest products from database
        $products = Product::latest()->get();

        // Return blade view with products data
        return view('product.create', compact('products'));
    }

    // Store new product with image upload
    public function store(Request $request)
    {
        // Validate form inputs
        $request->validate([
            'name' => 'required',     // Product name required
            'image' => 'required|image' // Image file required
        ]);

        // Image upload automatically handled by Trait
        Product::create($request->all());

        // Redirect back with success message
        return back()->with('success','Product Uploaded Successfully!');
    }
}