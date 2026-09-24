<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Image Upload Analytics Dashboard</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        body {
            background: #f4f7fb;
            font-family: "Segoe UI", Arial, sans-serif;
        }

        .navbar-brand {
            font-weight: 700;
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 35px auto;
        }

        .stat-card {
            border: none;
            border-radius: 15px;
            padding: 25px;
            background: #ffffff;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.07);
            height: 100%;
        }

        .stat-title {
            color: #6c757d;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            margin-top: 8px;
        }

        .upload-card,
        .products-card {
            border: none;
            border-radius: 15px;
            background: #ffffff;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.07);
        }

        .upload-card {
            padding: 25px;
        }

        .product-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            background: #ffffff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            height: 100%;
            transition: 0.25s;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 22px rgba(0, 0, 0, 0.12);
        }

        .product-image {
            width: 100%;
            height: 220px;
            object-fit: contain;
            background: #f1f1f1;
        }

        .product-content {
            padding: 18px;
        }

        .product-name {
            font-size: 19px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .product-date {
            font-size: 13px;
            color: #777;
        }

        .preview-container {
            display: none;
            margin-top: 15px;
        }

        .preview-image {
            width: 180px;
            height: 180px;
            object-fit: cover;
            border-radius: 12px;
            border: 2px solid #ddd;
        }

        .section-title {
            font-weight: 700;
            margin-bottom: 20px;
        }

        .filter-card {
            background: #ffffff;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.06);
        }

        .latest-image {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 10px;
        }

        .pagination {
            margin-top: 25px;
        }

        .empty-state {
            padding: 50px 20px;
            text-align: center;
            color: #777;
        }

    </style>

</head>

<body>

<nav class="navbar navbar-dark bg-dark">

    <div class="container">

        <a
            class="navbar-brand"
            href="{{ route('products.index') }}"
        >
            🖼️ Image Upload Manager
        </a>

    </div>

</nav>


<div class="container dashboard-container">

    {{-- Success Message --}}
    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif


    {{-- Validation Errors --}}
    @if($errors->any())

        <div class="alert alert-danger">

            <strong>Please fix the following:</strong>

            <ul class="mb-0 mt-2">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- ========================================= --}}
    {{-- ANALYTICS DASHBOARD --}}
    {{-- ========================================= --}}

    <h3 class="section-title">
        📊 Image Upload Analytics
    </h3>

    <div class="row g-4 mb-4">

        {{-- Total Images --}}
        <div class="col-md-3">

            <div class="stat-card">

                <div class="stat-title">
                    Total Images
                </div>

                <div class="stat-value text-primary">
                    {{ $totalImages }}
                </div>

                <small class="text-muted">
                    All uploaded product images
                </small>

            </div>

        </div>


        {{-- Today's Images --}}
        <div class="col-md-3">

            <div class="stat-card">

                <div class="stat-title">
                    Uploaded Today
                </div>

                <div class="stat-value text-success">
                    {{ $todayImages }}
                </div>

                <small class="text-muted">
                    Images uploaded today
                </small>

            </div>

        </div>


        {{-- Monthly Images --}}
        <div class="col-md-3">

            <div class="stat-card">

                <div class="stat-title">
                    This Month
                </div>

                <div class="stat-value text-warning">
                    {{ $monthImages }}
                </div>

                <small class="text-muted">
                    Current month uploads
                </small>

            </div>

        </div>


        {{-- Latest Product --}}
        <div class="col-md-3">

            <div class="stat-card">

                <div class="stat-title">
                    Latest Upload
                </div>

                @if($latestProduct)

                    <div class="mt-2 fw-bold">

                        {{ $latestProduct->name }}

                    </div>

                    <small class="text-muted">

                        {{ $latestProduct->created_at->format('d M Y, h:i A') }}

                    </small>

                @else

                    <div class="text-muted mt-2">
                        No uploads yet
                    </div>

                @endif

            </div>

        </div>

    </div>


    {{-- ========================================= --}}
    {{-- UPLOAD FORM --}}
    {{-- ========================================= --}}

    <div class="upload-card mb-4">

        <h3 class="section-title">
            📤 Upload New Product Image
        </h3>

        <form
            method="POST"
            action="{{ route('products.store') }}"
            enctype="multipart/form-data"
        >

            @csrf

            <div class="row">

                <div class="col-md-6 mb-3">

                    <label class="form-label fw-bold">
                        Product Name
                    </label>

                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        placeholder="Enter product name"
                        value="{{ old('name') }}"
                        required
                    >

                </div>


                <div class="col-md-6 mb-3">

                    <label class="form-label fw-bold">
                        Product Image
                    </label>

                    <input
                        type="file"
                        name="image"
                        id="imageInput"
                        class="form-control"
                        accept="image/jpeg,image/png,image/gif,image/webp"
                        required
                    >

                    <small class="text-muted">
                        JPG, JPEG, PNG, GIF or WEBP | Maximum 5MB
                    </small>

                </div>

            </div>


            {{-- Image Preview --}}
            <div
                class="preview-container"
                id="previewContainer"
            >

                <p class="fw-bold mb-2">
                    Image Preview
                </p>

                <img
                    id="previewImage"
                    class="preview-image"
                    alt="Image Preview"
                >

            </div>


            <button
                type="submit"
                class="btn btn-primary mt-3"
            >
                📤 Upload Image
            </button>

        </form>

    </div>


    {{-- ========================================= --}}
    {{-- SEARCH + FILTER --}}
    {{-- ========================================= --}}

    <div class="filter-card mb-4">

        <h5 class="fw-bold mb-3">
            🔎 Search & Filter Images
        </h5>

        <form
            method="GET"
            action="{{ route('products.index') }}"
        >

            <div class="row g-3">

                <div class="col-md-6">

                    <label class="form-label">
                        Search Product
                    </label>

                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Search by product name..."
                        value="{{ $search }}"
                    >

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        Upload Period
                    </label>

                    <select
                        name="period"
                        class="form-select"
                    >

                        <option value="">
                            All Time
                        </option>

                        <option
                            value="today"
                            {{ $period === 'today' ? 'selected' : '' }}
                        >
                            Today
                        </option>

                        <option
                            value="week"
                            {{ $period === 'week' ? 'selected' : '' }}
                        >
                            This Week
                        </option>

                        <option
                            value="month"
                            {{ $period === 'month' ? 'selected' : '' }}
                        >
                            This Month
                        </option>

                        <option
                            value="year"
                            {{ $period === 'year' ? 'selected' : '' }}
                        >
                            This Year
                        </option>

                    </select>

                </div>


                <div class="col-md-2 d-flex align-items-end">

                    <div class="w-100">

                        <button
                            type="submit"
                            class="btn btn-dark w-100"
                        >
                            Search
                        </button>

                    </div>

                </div>

            </div>


            @if($search || $period)

                <div class="mt-3">

                    <a
                        href="{{ route('products.index') }}"
                        class="btn btn-outline-secondary btn-sm"
                    >
                        Reset Filters
                    </a>

                </div>

            @endif

        </form>

    </div>


    {{-- ========================================= --}}
    {{-- PRODUCT IMAGE MANAGEMENT --}}
    {{-- ========================================= --}}

    <div class="products-card p-4">

<div class="d-flex justify-content-between align-items-center mb-3">

    <h3 class="section-title mb-0">
        🖼️ Uploaded Product Images
    </h3>

    <div class="d-flex align-items-center gap-2">

        <span class="badge bg-primary">

            {{ $products->total() }} Results

        </span>

        <a
            href="{{ route('products.gallery') }}"
            class="btn btn-dark btn-sm"
        >
            🗂️ Open Image Gallery
        </a>

    </div>

</div>
        </div>


        @if($products->count())

            <div class="row g-4">

                @foreach($products as $product)

                    <div class="col-md-6 col-lg-4">

                        <div class="product-card">

                            @if($product->image)

                                <img
                                    src="{{ asset($product->image) }}"
                                    class="product-image"
                                    alt="{{ $product->name }}"
                                >

                            @else

                                <div
                                    class="product-image d-flex align-items-center justify-content-center"
                                >
                                    No Image
                                </div>

                            @endif


                            <div class="product-content">

                                <div class="product-name">
                                    {{ $product->name }}
                                </div>

                                <div class="product-date mb-3">

                                    Uploaded:
                                    {{ $product->created_at->format('d M Y, h:i A') }}

                                </div>


                                <div class="d-flex gap-2">

                                    {{-- Edit --}}
                                    <a
                                        href="{{ route('products.edit', $product) }}"
                                        class="btn btn-warning btn-sm"
                                    >
                                        ✏️ Edit
                                    </a>


                                    {{-- Delete --}}
                                    <form
                                        method="POST"
                                        action="{{ route('products.destroy', $product) }}"
                                        onsubmit="return confirm('Are you sure you want to delete this product and its image?');"
                                    >

                                        @csrf

                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-danger btn-sm"
                                        >
                                            🗑️ Delete
                                        </button>

                                    </form>

                                </div>

                            </div>

                        </div>

                    </div>

                @endforeach

            </div>


            {{-- Pagination --}}
            <div class="d-flex justify-content-center">

                {{ $products->links('pagination::bootstrap-5') }}

            </div>

        @else

            <div class="empty-state">

                <h4>
                    No Images Found
                </h4>

                <p>
                    Try changing your search or filter,
                    or upload your first product image.
                </p>

            </div>

        @endif

    </div>

</div>


<script>

    /*
     * Image preview before upload.
     */
    document
        .getElementById('imageInput')
        .addEventListener('change', function(event) {

            const file = event.target.files[0];

            const previewContainer =
                document.getElementById('previewContainer');

            const previewImage =
                document.getElementById('previewImage');


            if (file) {

                const reader = new FileReader();

                reader.onload = function(e) {

                    previewImage.src = e.target.result;

                    previewContainer.style.display = 'block';

                };

                reader.readAsDataURL(file);

            } else {

                previewImage.src = '';

                previewContainer.style.display = 'none';

            }

        });

</script>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

</body>

</html>