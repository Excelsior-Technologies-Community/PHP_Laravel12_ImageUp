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

        .dashboard-container {
            max-width: 1250px;
            margin: 35px auto;
        }

        .stat-card {
            border: none;
            border-radius: 15px;
            padding: 22px;
            background: #ffffff;
            box-shadow: 0 5px 20px rgba(0,0,0,.07);
            height: 100%;
        }

        .stat-title {
            color: #6c757d;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .stat-value {
            font-size: 30px;
            font-weight: 700;
            margin-top: 8px;
        }

        .upload-card,
        .products-card,
        .filter-card {
            border: none;
            border-radius: 15px;
            background: #ffffff;
            box-shadow: 0 5px 20px rgba(0,0,0,.07);
        }

        .upload-card,
        .filter-card {
            padding: 25px;
        }

        .product-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            background: #ffffff;
            box-shadow: 0 4px 15px rgba(0,0,0,.08);
            height: 100%;
            transition: .25s;
        }

        .product-card:hover {
            transform: translateY(-4px);
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

        .empty-state {
            padding: 50px 20px;
            text-align: center;
            color: #777;
        }

        .filter-section {
            border: 1px solid #e5e5e5;
            border-radius: 10px;
            padding: 15px;
            background: #fafafa;
        }

        .pagination {
            margin-top: 25px;
        }

        .missing-badge {
            font-size: 11px;
        }

    </style>

</head>


<body>

<nav class="navbar navbar-dark bg-dark">

    <div class="container">

        <a
            class="navbar-brand fw-bold"
            href="{{ route('products.index') }}"
        >
            🖼️ Image Upload Manager
        </a>

        <a
            href="{{ route('products.gallery') }}"
            class="btn btn-light btn-sm"
        >
            🗂️ Gallery
        </a>

    </div>

</nav>


<div class="container dashboard-container">

    {{-- Success --}}

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


    {{-- Error --}}

    @if(session('error'))

        <div class="alert alert-danger alert-dismissible fade show">

            {{ session('error') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif


    {{-- Validation --}}

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


    {{-- ================================================= --}}
    {{-- STATISTICS --}}
    {{-- ================================================= --}}

    <h3 class="fw-bold mb-4">
        📊 Image Upload Analytics
    </h3>


    <div class="row g-4 mb-4">

        <div class="col-md-3">

            <div class="stat-card">

                <div class="stat-title">
                    Total Images
                </div>

                <div class="stat-value text-primary">
                    {{ $totalImages }}
                </div>

                <small class="text-muted">
                    All uploaded images
                </small>

            </div>

        </div>


        <div class="col-md-3">

            <div class="stat-card">

                <div class="stat-title">
                    Uploaded Today
                </div>

                <div class="stat-value text-success">
                    {{ $todayImages }}
                </div>

                <small class="text-muted">
                    Today's uploads
                </small>

            </div>

        </div>


        <div class="col-md-3">

            <div class="stat-card">

                <div class="stat-title">
                    This Month
                </div>

                <div class="stat-value text-warning">
                    {{ $monthImages }}
                </div>

                <small class="text-muted">
                    Current month
                </small>

            </div>

        </div>


        <div class="col-md-3">

            <div class="stat-card">

                <div class="stat-title">
                    Storage Used
                </div>

                <div class="stat-value text-info">
                    {{ $storageFormatted }}
                </div>

                <small class="text-muted">
                    Physical image storage
                </small>

            </div>

        </div>

    </div>


    {{-- ================================================= --}}
    {{-- UPLOAD --}}
    {{-- ================================================= --}}

    <div class="upload-card mb-4">

        <h3 class="fw-bold mb-4">
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
                        JPG, JPEG, PNG, GIF, WEBP | Maximum 5MB
                    </small>

                </div>

            </div>


            <div
                class="preview-container"
                id="previewContainer"
            >

                <p class="fw-bold">
                    Image Preview
                </p>

                <img
                    id="previewImage"
                    class="preview-image"
                    alt="Preview"
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


    {{-- ================================================= --}}
    {{-- FILTER --}}
    {{-- ================================================= --}}

    <div class="filter-card mb-4">

        <h5 class="fw-bold mb-3">
            🔎 Advanced Search & Filters
        </h5>


        <form
            method="GET"
            action="{{ route('products.index') }}"
        >

            <div class="row g-3">

                {{-- Search --}}

                <div class="col-md-4">

                    <label class="form-label">
                        Product Name
                    </label>

                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Search..."
                        value="{{ $search }}"
                    >

                </div>


                {{-- Period --}}

                <div class="col-md-4">

                    <label class="form-label">
                        Quick Period
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


                {{-- Format --}}

                <div class="col-md-4">

                    <label class="form-label">
                        Image Format
                    </label>

                    <select
                        name="format"
                        class="form-select"
                    >

                        <option value="">
                            All Formats
                        </option>

                        <option
                            value="jpg"
                            {{ $format === 'jpg' ? 'selected' : '' }}
                        >
                            JPG
                        </option>

                        <option
                            value="jpeg"
                            {{ $format === 'jpeg' ? 'selected' : '' }}
                        >
                            JPEG
                        </option>

                        <option
                            value="png"
                            {{ $format === 'png' ? 'selected' : '' }}
                        >
                            PNG
                        </option>

                        <option
                            value="gif"
                            {{ $format === 'gif' ? 'selected' : '' }}
                        >
                            GIF
                        </option>

                        <option
                            value="webp"
                            {{ $format === 'webp' ? 'selected' : '' }}
                        >
                            WEBP
                        </option>

                    </select>

                </div>


                {{-- From Date --}}

                <div class="col-md-3">

                    <label class="form-label">
                        From Date
                    </label>

                    <input
                        type="date"
                        name="date_from"
                        class="form-control"
                        value="{{ $dateFrom }}"
                    >

                </div>


                {{-- To Date --}}

                <div class="col-md-3">

                    <label class="form-label">
                        To Date
                    </label>

                    <input
                        type="date"
                        name="date_to"
                        class="form-control"
                        value="{{ $dateTo }}"
                    >

                </div>


                {{-- Min Size --}}

                <div class="col-md-3">

                    <label class="form-label">
                        Min Size (KB)
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        name="min_size"
                        class="form-control"
                        placeholder="Example: 10"
                        value="{{ $minSize }}"
                    >

                </div>


                {{-- Max Size --}}

                <div class="col-md-3">

                    <label class="form-label">
                        Max Size (KB)
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        name="max_size"
                        class="form-control"
                        placeholder="Example: 500"
                        value="{{ $maxSize }}"
                    >

                </div>


                {{-- Sort --}}

                <div class="col-md-4">

                    <label class="form-label">
                        Sort By
                    </label>

                    <select
                        name="sort"
                        class="form-select"
                    >

                        <option
                            value="latest"
                            {{ $sort === 'latest' ? 'selected' : '' }}
                        >
                            Newest First
                        </option>

                        <option
                            value="oldest"
                            {{ $sort === 'oldest' ? 'selected' : '' }}
                        >
                            Oldest First
                        </option>

                        <option
                            value="id_asc"
                            {{ $sort === 'id_asc' ? 'selected' : '' }}
                        >
                            ID Ascending
                        </option>

                        <option
                            value="id_desc"
                            {{ $sort === 'id_desc' ? 'selected' : '' }}
                        >
                            ID Descending
                        </option>

                        <option
                            value="name_asc"
                            {{ $sort === 'name_asc' ? 'selected' : '' }}
                        >
                            Name A-Z
                        </option>

                        <option
                            value="name_desc"
                            {{ $sort === 'name_desc' ? 'selected' : '' }}
                        >
                            Name Z-A
                        </option>

                    </select>

                </div>


                {{-- Per Page --}}

                <div class="col-md-4">

                    <label class="form-label">
                        Products Per Page
                    </label>

                    <select
                        name="per_page"
                        class="form-select"
                    >

                        @foreach([6,12,24,48] as $number)

                            <option
                                value="{{ $number }}"
                                {{ $perPage == $number ? 'selected' : '' }}
                            >
                                {{ $number }} Products
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- Buttons --}}

                <div class="col-md-4 d-flex align-items-end gap-2">

                    <button
                        type="submit"
                        class="btn btn-dark flex-fill"
                    >
                        🔎 Apply Filters
                    </button>

                    <a
                        href="{{ route('products.index') }}"
                        class="btn btn-outline-secondary"
                    >
                        Reset
                    </a>

                </div>

            </div>

        </form>


        <div class="mt-4 pt-3 border-top">

            <div class="d-flex flex-wrap gap-2">

                {{-- CSV --}}

                <a
                    href="{{ route('products.export', request()->query()) }}"
                    class="btn btn-success"
                >
                    📄 Export CSV
                </a>


                {{-- Missing Records --}}

                @if($missingImages > 0)

                    <form
                        method="POST"
                        action="{{ route('products.cleanMissing') }}"
                        onsubmit="return confirm('Delete all database records whose image files are missing?');"
                    >

                        @csrf

                        @method('DELETE')

                        <button
                            type="submit"
                            class="btn btn-danger"
                        >
                            🧹 Clean {{ $missingImages }} Missing
                        </button>

                    </form>

                @endif

            </div>

        </div>

    </div>


    {{-- ================================================= --}}
    {{-- RESULTS --}}
    {{-- ================================================= --}}

    <div class="products-card p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h3 class="fw-bold mb-1">
                    🖼️ Uploaded Product Images
                </h3>

                <span class="text-muted">
                    Showing {{ $filteredCount }} filtered result(s)
                </span>

            </div>


            <div>

                <a
                    href="{{ route('products.gallery') }}"
                    class="btn btn-dark btn-sm"
                >
                    🗂️ Open Gallery
                </a>

            </div>

        </div>


        @if($products->count())

            <div class="row g-4">

                @foreach($products as $product)

                    <div class="col-md-6 col-lg-4">

                        <div class="product-card">

                            @if(
                                $product->image &&
                                file_exists(
                                    public_path($product->image)
                                )
                            )

                                <img
                                    src="{{ asset($product->image) }}"
                                    class="product-image"
                                    alt="{{ $product->name }}"
                                >

                            @else

                                <div
                                    class="product-image d-flex align-items-center justify-content-center text-danger"
                                >
                                    ⚠️ Image Missing
                                </div>

                            @endif


                            <div class="product-content">

                                <div class="product-name">

                                    {{ $product->name }}

                                </div>


                                <div class="product-date mb-3">

                                    ID:
                                    {{ $product->id }}

                                    <br>

                                    Uploaded:
                                    {{ $product->created_at->format('d M Y, h:i A') }}

                                </div>


                                <div class="d-flex gap-2">

                                    <a
                                        href="{{ route('products.edit', $product) }}"
                                        class="btn btn-warning btn-sm"
                                    >
                                        ✏️ Edit
                                    </a>


                                    @if($product->image)

                                        <a
                                            href="{{ route('products.download', $product) }}"
                                            class="btn btn-success btn-sm"
                                        >
                                            📥 Download
                                        </a>

                                    @endif


                                    <form
                                        method="POST"
                                        action="{{ route('products.destroy', $product) }}"
                                        onsubmit="return confirm('Delete this product and image?');"
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


            {{-- Number Pagination --}}

            <div class="d-flex justify-content-center mt-4">

                {{ $products->onEachSide(2)->links('pagination::bootstrap-5') }}

            </div>

        @else

            <div class="empty-state">

                <h4>
                    No Images Found
                </h4>

                <p>
                    Try changing your filters or upload an image.
                </p>

            </div>

        @endif

    </div>

</div>


<script>

    /*
    |--------------------------------------------------------------------------
    | Image Preview
    |--------------------------------------------------------------------------
    */

    const imageInput =
        document.getElementById('imageInput');

    if (imageInput) {

        imageInput.addEventListener(
            'change',
            function(event) {

                const file =
                    event.target.files[0];

                const previewContainer =
                    document.getElementById(
                        'previewContainer'
                    );

                const previewImage =
                    document.getElementById(
                        'previewImage'
                    );


                if (file) {

                    const reader =
                        new FileReader();

                    reader.onload =
                        function(e) {

                            previewImage.src =
                                e.target.result;

                            previewContainer.style.display =
                                'block';
                        };

                    reader.readAsDataURL(file);

                } else {

                    previewImage.src = '';

                    previewContainer.style.display =
                        'none';
                }

            }
        );
    }

</script>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

</body>

</html>