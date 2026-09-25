<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;
use ZipArchive;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductController extends Controller
{
    /**
     * Product index/dashboard.
     *
     * Keeps your existing products.index route working.
     */
    public function create(Request $request)
    {
        return $this->gallery($request);
    }

    /**
     * Image Gallery.
     *
     * Supports:
     * - Search
     * - ID ascending
     * - ID descending
     * - Name A-Z
     * - Name Z-A
     * - Newest
     * - Oldest
     * - 6 / 12 / 24 / 48 per page
     * - Image format
     * - From date
     * - To date
     * - Minimum physical image size
     * - Maximum physical image size
     * - Missing image detection
     * - Storage calculation
     */
    public function gallery(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Read Request Filters
        |--------------------------------------------------------------------------
        */

        $search = trim(
            (string) $request->input('search', '')
        );

        $period = $request->input('period');

        $format = strtolower(
            trim(
                (string) $request->input('format', '')
            )
        );

        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $minSize = $request->input('min_size');
        $maxSize = $request->input('max_size');

        $sort = $request->input(
            'sort',
            'id_asc'
        );

        /*
        |--------------------------------------------------------------------------
        | Per Page
        |--------------------------------------------------------------------------
        */

        $allowedPerPage = [
            6,
            12,
            24,
            48,
        ];

        $perPage = (int) $request->input(
            'per_page',
            12
        );

        if (!in_array(
            $perPage,
            $allowedPerPage,
            true
        )) {
            $perPage = 12;
        }

        /*
        |--------------------------------------------------------------------------
        | Size Validation
        |--------------------------------------------------------------------------
        */

        $minSize = is_numeric($minSize)
            ? (float) $minSize
            : null;

        $maxSize = is_numeric($maxSize)
            ? (float) $maxSize
            : null;

        /*
        |--------------------------------------------------------------------------
        | Allowed Image Formats
        |--------------------------------------------------------------------------
        */

        $allowedFormats = [
            'jpg',
            'jpeg',
            'png',
            'gif',
            'webp',
        ];

        if (
            $format !== '' &&
            !in_array(
                $format,
                $allowedFormats,
                true
            )
        ) {
            $format = '';
        }

        /*
        |--------------------------------------------------------------------------
        | Get Products With Images
        |--------------------------------------------------------------------------
        */

        $products = Product::query()
            ->whereNotNull('image')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($search !== '') {

            $products = $products->filter(
                function ($product) use ($search) {

                    return str_contains(
                        strtolower(
                            (string) $product->name
                        ),
                        strtolower($search)
                    );
                }
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Period Filter
        |--------------------------------------------------------------------------
        */

        if (!empty($period)) {

            $today = now();

            if ($period === 'today') {

                $products = $products->filter(
                    function ($product) use ($today) {

                        if (!$product->created_at) {
                            return false;
                        }

                        return $product
                            ->created_at
                            ->isToday();
                    }
                );
            }

            if ($period === 'week') {

                $products = $products->filter(
                    function ($product) use ($today) {

                        if (!$product->created_at) {
                            return false;
                        }

                        return $product
                            ->created_at
                            ->between(
                                $today
                                    ->copy()
                                    ->startOfWeek(),

                                $today
                                    ->copy()
                                    ->endOfWeek()
                            );
                    }
                );
            }

            if ($period === 'month') {

                $products = $products->filter(
                    function ($product) use ($today) {

                        if (!$product->created_at) {
                            return false;
                        }

                        return
                            $product->created_at->month
                            === $today->month
                            &&
                            $product->created_at->year
                            === $today->year;
                    }
                );
            }

            if ($period === 'year') {

                $products = $products->filter(
                    function ($product) use ($today) {

                        if (!$product->created_at) {
                            return false;
                        }

                        return
                            $product->created_at->year
                            === $today->year;
                    }
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | From Date
        |--------------------------------------------------------------------------
        */

        if (!empty($dateFrom)) {

            $products = $products->filter(
                function ($product) use ($dateFrom) {

                    if (!$product->created_at) {
                        return false;
                    }

                    return
                        $product
                        ->created_at
                        ->format('Y-m-d')
                        >=
                        $dateFrom;
                }
            );
        }

        /*
        |--------------------------------------------------------------------------
        | To Date
        |--------------------------------------------------------------------------
        */

        if (!empty($dateTo)) {

            $products = $products->filter(
                function ($product) use ($dateTo) {

                    if (!$product->created_at) {
                        return false;
                    }

                    return
                        $product
                        ->created_at
                        ->format('Y-m-d')
                        <=
                        $dateTo;
                }
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Physical Image Format + Physical Image Size
        |--------------------------------------------------------------------------
        */

        $products = $products->filter(
            function ($product) use (
                $format,
                $minSize,
                $maxSize
            ) {

                if (!$product->image) {
                    return false;
                }

                $imagePath = public_path(
                    $product->image
                );

                /*
                |--------------------------------------------------------------------------
                | Physical File Must Exist
                |--------------------------------------------------------------------------
                */

                if (
                    !file_exists($imagePath) ||
                    !is_file($imagePath)
                ) {
                    return false;
                }

                /*
                |--------------------------------------------------------------------------
                | Actual Extension
                |--------------------------------------------------------------------------
                */

                $extension = strtolower(
                    pathinfo(
                        $imagePath,
                        PATHINFO_EXTENSION
                    )
                );

                /*
                |--------------------------------------------------------------------------
                | Format Filter
                |--------------------------------------------------------------------------
                */

                if ($format !== '') {

                    if ($extension !== $format) {
                        return false;
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Physical File Size
                |--------------------------------------------------------------------------
                */

                $fileSizeBytes = filesize(
                    $imagePath
                );

                $fileSizeKb =
                    $fileSizeBytes / 1024;

                /*
                |--------------------------------------------------------------------------
                | Minimum Size
                |--------------------------------------------------------------------------
                */

                if ($minSize !== null) {

                    if ($fileSizeKb < $minSize) {
                        return false;
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Maximum Size
                |--------------------------------------------------------------------------
                */

                if ($maxSize !== null) {

                    if ($fileSizeKb > $maxSize) {
                        return false;
                    }
                }

                return true;
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        switch ($sort) {

            case 'id_desc':

                $products = $products->sortByDesc(
                    'id'
                );

                break;

            case 'name_asc':

                $products = $products->sortBy(
                    function ($product) {

                        return strtolower(
                            (string) $product->name
                        );
                    }
                );

                break;

            case 'name_desc':

                $products = $products->sortByDesc(
                    function ($product) {

                        return strtolower(
                            (string) $product->name
                        );
                    }
                );

                break;

            case 'newest':

                $products = $products->sortByDesc(
                    function ($product) {

                        return $product->created_at;
                    }
                );

                break;

            case 'oldest':

                $products = $products->sortBy(
                    function ($product) {

                        return $product->created_at;
                    }
                );

                break;

            case 'id_asc':

            default:

                $products = $products->sortBy(
                    'id'
                );

                break;
        }

        /*
        |--------------------------------------------------------------------------
        | Reset Keys
        |--------------------------------------------------------------------------
        */

        $products = $products->values();

        /*
        |--------------------------------------------------------------------------
        | Filtered Total
        |--------------------------------------------------------------------------
        */

        $filteredTotal = $products->count();

        /*
        |--------------------------------------------------------------------------
        | Current Page
        |--------------------------------------------------------------------------
        */

        $currentPage = max(
            1,
            (int) $request->input(
                'page',
                1
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Slice Current Page
        |--------------------------------------------------------------------------
        */

        $offset =
            ($currentPage - 1) * $perPage;

        $currentProducts = $products
            ->slice(
                $offset,
                $perPage
            )
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Laravel Paginator
        |--------------------------------------------------------------------------
        */

        $products = new LengthAwarePaginator(
            $currentProducts,
            $filteredTotal,
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Total Images
        |--------------------------------------------------------------------------
        */

        $totalImages = Product::query()
            ->whereNotNull('image')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Today's Images
        |--------------------------------------------------------------------------
        */

        $todayImages = Product::query()
            ->whereNotNull('image')
            ->whereDate(
                'created_at',
                today()
            )
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Current Month Images
        |--------------------------------------------------------------------------
        */

        $monthImages = Product::query()
            ->whereNotNull('image')
            ->whereMonth(
                'created_at',
                now()->month
            )
            ->whereYear(
                'created_at',
                now()->year
            )
            ->count();

        /*
        |--------------------------------------------------------------------------
        | All Image Records
        |--------------------------------------------------------------------------
        */

        $allImageProducts = Product::query()
            ->whereNotNull('image')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Storage Calculation
        |--------------------------------------------------------------------------
        */

        $totalBytes = 0;

        foreach (
            $allImageProducts
            as $product
        ) {

            if (!$product->image) {
                continue;
            }

            $imagePath = public_path(
                $product->image
            );

            if (
                file_exists($imagePath) &&
                is_file($imagePath)
            ) {

                $totalBytes += filesize(
                    $imagePath
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Format Storage
        |--------------------------------------------------------------------------
        */

        if (
            $totalBytes >=
            1024 * 1024 * 1024
        ) {

            $totalStorage = number_format(
                $totalBytes /
                    (1024 * 1024 * 1024),
                2
            ) . ' GB';
        } elseif (
            $totalBytes >=
            1024 * 1024
        ) {

            $totalStorage = number_format(
                $totalBytes /
                    (1024 * 1024),
                2
            ) . ' MB';
        } elseif (
            $totalBytes >= 1024
        ) {

            $totalStorage = number_format(
                $totalBytes / 1024,
                2
            ) . ' KB';
        } else {

            $totalStorage =
                $totalBytes . ' Bytes';
        }

        /*
        |--------------------------------------------------------------------------
        | Missing Physical Images
        |--------------------------------------------------------------------------
        */

        $missingCount = 0;

        foreach (
            $allImageProducts
            as $product
        ) {

            if (!$product->image) {
                continue;
            }

            $imagePath = public_path(
                $product->image
            );

            if (
                !file_exists($imagePath) ||
                !is_file($imagePath)
            ) {

                $missingCount++;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Return Gallery
        |--------------------------------------------------------------------------
        */

        return view(
            'product.gallery',
            compact(
                'products',
                'totalImages',
                'todayImages',
                'monthImages',
                'totalStorage',
                'missingCount'
            )
        );
    }

    /**
     * Download product image
     */
    public function download(Product $product)
    {
        // Make sure product has an image path
        if (!$product->image) {
            return back()->with('error', 'This product does not have an image.');
        }

        // Build physical image path
        $filePath = public_path($product->image);

        // Check physical file exists
        if (!file_exists($filePath)) {
            return back()->with(
                'error',
                'The physical image file was not found.'
            );
        }

        // Get extension
        $extension = strtolower(
            pathinfo($filePath, PATHINFO_EXTENSION)
        );

        // Create safe download filename
        $safeName = preg_replace(
            '/[^A-Za-z0-9\-_]/',
            '_',
            $product->name
        );

        $downloadName = $safeName . '.' . $extension;

        return response()->download(
            $filePath,
            $downloadName
        );
    }

    /**
     * Bulk delete selected products
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'products' => ['required', 'array', 'min:1'],
            'products.*' => ['integer', 'exists:products,id'],
        ]);

        $productIds = $request->input('products', []);

        $products = Product::whereIn('id', $productIds)->get();

        $deletedCount = 0;

        foreach ($products as $product) {

            // Delete physical image if it exists
            if (!empty($product->image)) {
                $imagePath = public_path($product->image);

                if (file_exists($imagePath) && is_file($imagePath)) {
                    @unlink($imagePath);
                }
            }

            // Delete database record
            $product->delete();

            $deletedCount++;
        }

        return redirect()
            ->back()
            ->with('success', $deletedCount . ' product(s) deleted successfully.');
    }

    /**
     * Export filtered gallery products to CSV
     */
    public function exportGallery(Request $request)
    {
        $products = Product::query()
            ->whereNotNull('image')
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->when($request->filled('date_from'), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->date_to);
            })
            ->get();

        $filename = 'product-gallery-' . now()->format('Y-m-d-H-i-s') . '.csv';

        return response()->streamDownload(function () use ($products) {

            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'ID',
                'Product Name',
                'Image',
                'Format',
                'Size',
                'File Exists',
                'Created At',
            ]);

            foreach ($products as $product) {

                $path = $product->image
                    ? public_path($product->image)
                    : null;

                $exists = $path && file_exists($path);

                $format = $exists
                    ? strtoupper(pathinfo($path, PATHINFO_EXTENSION))
                    : '';

                $size = $exists
                    ? filesize($path)
                    : 0;

                fputcsv($handle, [
                    $product->id,
                    $product->name,
                    $product->image,
                    $format,
                    $size,
                    $exists ? 'Yes' : 'No',
                    $product->created_at?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }


    /**
     * Download selected product images as ZIP
     */
    public function downloadZip(Request $request)
    {
        $request->validate([
            'products' => ['required', 'array', 'min:1'],
            'products.*' => ['integer', 'exists:products,id'],
        ]);

        $products = Product::whereIn(
            'id',
            $request->input('products')
        )->get();

        $zipDirectory = storage_path('app/temp');

        if (!is_dir($zipDirectory)) {
            mkdir($zipDirectory, 0755, true);
        }

        $zipPath = $zipDirectory . '/selected-images-' . time() . '.zip';

        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Unable to create ZIP file.');
        }

        $addedFiles = 0;

        foreach ($products as $product) {

            if (empty($product->image)) {
                continue;
            }

            $filePath = public_path($product->image);

            if (!file_exists($filePath) || !is_file($filePath)) {
                continue;
            }

            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

            $safeName = preg_replace(
                '/[^A-Za-z0-9\-_]/',
                '_',
                $product->name
            );

            $zipFileName = $product->id . '_' . $safeName . '.' . $extension;

            $zip->addFile($filePath, $zipFileName);

            $addedFiles++;
        }

        $zip->close();

        if ($addedFiles === 0) {

            if (file_exists($zipPath)) {
                unlink($zipPath);
            }

            return back()->with(
                'error',
                'None of the selected products have a physical image file.'
            );
        }

        return response()
            ->download($zipPath, 'selected-images.zip')
            ->deleteFileAfterSend(true);
    }
}
