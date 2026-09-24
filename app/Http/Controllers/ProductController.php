<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    // Show dashboard, upload form and product list
    public function create(Request $request)
    {
        // Analytics
        $totalImages = Product::whereNotNull('image')->count();

        $todayImages = Product::whereNotNull('image')
            ->whereDate('created_at', today())
            ->count();

        $monthImages = Product::whereNotNull('image')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $latestProduct = Product::latest()->first();

        // Search
        $search = $request->input('search', '');

        // Period filter
        $period = $request->input('period', '');

        // Product query
        $query = Product::latest();

        // Search by product name
        if ($search !== '') {
            $query->where(
                'name',
                'like',
                '%' . $search . '%'
            );
        }

        // Filter by upload period
        if ($period === 'today') {

            $query->whereDate(
                'created_at',
                today()
            );

        } elseif ($period === 'week') {

            $query->whereBetween(
                'created_at',
                [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ]
            );

        } elseif ($period === 'month') {

            $query->whereMonth(
                'created_at',
                now()->month
            )->whereYear(
                'created_at',
                now()->year
            );

        } elseif ($period === 'year') {

            $query->whereYear(
                'created_at',
                now()->year
            );
        }

        // Pagination
        $products = $query
            ->paginate(6)
            ->withQueryString();

        return view(
            'product.create',
            compact(
                'products',
                'totalImages',
                'todayImages',
                'monthImages',
                'latestProduct',
                'search',
                'period'
            )
        );
    }

    // Store new product with image
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
        ]);

        Product::create([
            'name' => $request->name,
        ]);

        return back()->with(
            'success',
            'Product uploaded successfully!'
        );
    }

    // Show edit page
    public function edit(Product $product)
    {
        return view(
            'product.edit',
            compact('product')
        );
    }

    // Update product and replace image
    public function update(
        Request $request,
        Product $product
    ) {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
        ]);

        $oldImage = $product->image;

        $product->name = $request->name;

        /*
         * When a new image is uploaded,
         * the HasImageUpload trait will
         * automatically create the new image.
         */
        if ($request->hasFile('image')) {
            $product->image = null;
        }

        $product->save();

        // Delete old physical image
        // after successful replacement.
        if (
            $request->hasFile('image') &&
            $oldImage
        ) {
            $oldImagePath = public_path(
                $oldImage
            );

            if (File::exists($oldImagePath)) {
                File::delete(
                    $oldImagePath
                );
            }
        }

        return redirect()
            ->route('products.index')
            ->with(
                'success',
                'Product updated successfully!'
            );
    }

    // Delete one product and its image
    public function destroy(Product $product)
    {
        $imagePath = $product->image
            ? public_path($product->image)
            : null;

        $product->delete();

        if (
            $imagePath &&
            File::exists($imagePath)
        ) {
            File::delete($imagePath);
        }

        return back()->with(
            'success',
            'Product and image deleted successfully!'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Image Gallery
    |--------------------------------------------------------------------------
    */

    // Display dedicated image gallery
    public function gallery()
    {
        $products = Product::whereNotNull('image')
            ->latest()
            ->paginate(12);

        return view(
            'product.gallery',
            compact('products')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Download Image
    |--------------------------------------------------------------------------
    */

    public function download(Product $product)
    {
        if (!$product->image) {
            abort(
                404,
                'Image not found.'
            );
        }

        $uploadsDirectory = realpath(
            public_path('uploads')
        );

        $imagePath = public_path(
            $product->image
        );

        $realImagePath = realpath(
            $imagePath
        );

        /*
         * Make sure the requested file
         * exists inside uploads directory.
         */
        if (
            !$realImagePath ||
            !$uploadsDirectory ||
            !str_starts_with(
                $realImagePath,
                $uploadsDirectory .
                DIRECTORY_SEPARATOR
            )
        ) {
            abort(
                404,
                'Image file not found.'
            );
        }

        return response()->download(
            $realImagePath,
            basename($realImagePath)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Bulk Delete
    |--------------------------------------------------------------------------
    */

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'products' => 'required|array|min:1',
            'products.*' => 'integer|exists:products,id',
        ]);

        $products = Product::whereIn(
            'id',
            $request->products
        )->get();

        $deletedCount = 0;

        foreach ($products as $product) {

            // Delete physical image
            if ($product->image) {

                $imagePath = public_path(
                    $product->image
                );

                if (File::exists($imagePath)) {
                    File::delete(
                        $imagePath
                    );
                }
            }

            // Delete database record
            $product->delete();

            $deletedCount++;
        }

        return back()->with(
            'success',
            $deletedCount .
            ' image(s) deleted successfully!'
        );
    }
}