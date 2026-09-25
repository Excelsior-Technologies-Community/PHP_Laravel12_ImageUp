<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Image Gallery & Bulk Management</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        body {
            background: #f8f9fa;
        }

        .gallery-card {
            transition: 0.2s ease;
            border: 1px solid #e5e7eb;
        }

        .gallery-card:hover {
            transform: translateY(-4px);
            box-shadow:
                0 10px 25px rgba(0, 0, 0, .08) !important;
        }

        .gallery-image {
            width: 100%;
            height: 220px;
            object-fit: contain;
            cursor: pointer;
            background: #f1f3f5;
        }

        .image-preview {
            width: 100%;
            max-height: 80vh;
            object-fit: contain;
        }

        .metadata {
            font-size: 14px;
            line-height: 1.8;
        }

        .select-box {
            position: absolute;
            top: 12px;
            left: 12px;
            z-index: 10;
            background: rgba(255, 255, 255, .92);
            padding: 5px 7px;
            border-radius: 6px;
        }

        .image-wrapper {
            position: relative;
        }

        .selected-card {
            border: 3px solid #dc3545 !important;
        }

        .missing-card {
            border: 3px solid #ffc107 !important;
        }

        .filter-card {
            background: #fff;
        }

        .stat-card {
            border: 0;
            border-radius: 14px;
        }

        .stat-number {
            font-size: 25px;
            font-weight: 700;
        }

        .pagination {
            margin-bottom: 0;
        }

        .pagination .page-link {
            min-width: 40px;
            text-align: center;
        }

        /*
        |--------------------------------------------------------------------------
        | Numeric Pagination Only
        |--------------------------------------------------------------------------
        */

        .pagination .page-item:first-child,
        .pagination .page-item:last-child {
            display: none;
        }

        .missing-image-box {
            width: 100%;
            height: 220px;
        }

        .filter-title {
            font-size: 16px;
        }

        .toolbar-button {
            min-height: 38px;
        }

    </style>

</head>

<body>

{{-- =========================================================
     NAVBAR
========================================================= --}}

<nav class="navbar navbar-dark bg-dark mb-4">

    <div class="container">

        <span class="navbar-brand fw-bold">
            🖼️ Image Gallery
        </span>

        <a
            href="{{ route('products.index') }}"
            class="btn btn-light btn-sm"
        >
            ← Back to Dashboard
        </a>

    </div>

</nav>


<div class="container pb-5">

    {{-- =====================================================
         SUCCESS
    ====================================================== --}}

    @if(session('success'))

        <div
            class="alert alert-success alert-dismissible fade show"
            role="alert"
        >

            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif


    {{-- =====================================================
         ERROR
    ====================================================== --}}

    @if(session('error'))

        <div
            class="alert alert-danger alert-dismissible fade show"
            role="alert"
        >

            {{ session('error') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif


    {{-- =====================================================
         VALIDATION ERRORS
    ====================================================== --}}

    @if($errors->any())

        <div class="alert alert-danger">

            <strong>
                Please fix the following:
            </strong>

            <ul class="mb-0 mt-2">

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- =====================================================
         HEADER
    ====================================================== --}}

    <div
        class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3"
    >

        <div>

            <h2 class="fw-bold mb-1">
                Image Gallery
            </h2>

            <p class="text-muted mb-0">
                Manage, filter, preview, download and bulk manage images.
            </p>

        </div>

        <div>

            <span class="badge bg-primary fs-6">

                {{ $products->total() }}

                Images

            </span>

        </div>

    </div>


    {{-- =====================================================
         STATISTICS
    ====================================================== --}}

    <div class="row g-3 mb-4">

        <div class="col-md-3">

            <div class="card shadow-sm stat-card h-100">

                <div class="card-body">

                    <div class="text-muted">
                        Filtered Images
                    </div>

                    <div class="stat-number text-primary">

                        {{ $products->total() }}

                    </div>

                </div>

            </div>

        </div>


        <div class="col-md-3">

            <div class="card shadow-sm stat-card h-100">

                <div class="card-body">

                    <div class="text-muted">
                        Storage Used
                    </div>

                    <div class="stat-number text-success">

                        {{ $totalStorage ?? '0 KB' }}

                    </div>

                </div>

            </div>

        </div>


        <div class="col-md-3">

            <div class="card shadow-sm stat-card h-100">

                <div class="card-body">

                    <div class="text-muted">
                        Missing Images
                    </div>

                    <div class="stat-number text-warning">

                        {{ $missingCount ?? 0 }}

                    </div>

                </div>

            </div>

        </div>


        <div class="col-md-3">

            <div class="card shadow-sm stat-card h-100">

                <div class="card-body">

                    <div class="text-muted">
                        Selected
                    </div>

                    <div
                        class="stat-number text-danger"
                        id="selectedCountTop"
                    >
                        0
                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =====================================================
         FILTERS
    ====================================================== --}}

    <div class="card shadow-sm filter-card mb-4">

        <div class="card-header bg-dark text-white">

            <strong class="filter-title">
                🔎 Image Filters & Sorting
            </strong>

        </div>


        <div class="card-body">

            <form
                action="{{ route('products.gallery') }}"
                method="GET"
            >

                <div class="row g-3">


                    {{-- SEARCH --}}

                    <div class="col-md-4">

                        <label class="form-label fw-semibold">
                            Search Product
                        </label>

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Search by product name..."
                            value="{{ request('search', '') }}"
                        >

                    </div>


                    {{-- SORT --}}

                    <div class="col-md-2">

                        <label class="form-label fw-semibold">
                            Sort By
                        </label>

                        <select
                            name="sort"
                            class="form-select"
                        >

                            <option
                                value="id_asc"
                                {{ request('sort', 'id_asc') === 'id_asc' ? 'selected' : '' }}
                            >
                                ID - Ascending
                            </option>

                            <option
                                value="id_desc"
                                {{ request('sort') === 'id_desc' ? 'selected' : '' }}
                            >
                                ID - Descending
                            </option>

                            <option
                                value="name_asc"
                                {{ request('sort') === 'name_asc' ? 'selected' : '' }}
                            >
                                Name - A to Z
                            </option>

                            <option
                                value="name_desc"
                                {{ request('sort') === 'name_desc' ? 'selected' : '' }}
                            >
                                Name - Z to A
                            </option>

                            <option
                                value="newest"
                                {{ request('sort') === 'newest' ? 'selected' : '' }}
                            >
                                Newest
                            </option>

                            <option
                                value="oldest"
                                {{ request('sort') === 'oldest' ? 'selected' : '' }}
                            >
                                Oldest
                            </option>

                        </select>

                    </div>


                    {{-- PER PAGE --}}

                    <div class="col-md-2">

                        <label class="form-label fw-semibold">
                            Per Page
                        </label>

                        <select
                            name="per_page"
                            class="form-select"
                        >

                            @foreach([6, 12, 24, 48] as $size)

                                <option
                                    value="{{ $size }}"
                                    {{ (int) request('per_page', 12) === $size ? 'selected' : '' }}
                                >
                                    {{ $size }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- FORMAT --}}

                    <div class="col-md-2">

                        <label class="form-label fw-semibold">
                            Image Format
                        </label>

                        <select
                            name="format"
                            class="form-select"
                        >

                            <option
                                value=""
                                {{ request('format', '') === '' ? 'selected' : '' }}
                            >
                                All Formats
                            </option>

                            <option
                                value="jpg"
                                {{ request('format') === 'jpg' ? 'selected' : '' }}
                            >
                                JPG
                            </option>

                            <option
                                value="jpeg"
                                {{ request('format') === 'jpeg' ? 'selected' : '' }}
                            >
                                JPEG
                            </option>

                            <option
                                value="png"
                                {{ request('format') === 'png' ? 'selected' : '' }}
                            >
                                PNG
                            </option>

                            <option
                                value="gif"
                                {{ request('format') === 'gif' ? 'selected' : '' }}
                            >
                                GIF
                            </option>

                            <option
                                value="webp"
                                {{ request('format') === 'webp' ? 'selected' : '' }}
                            >
                                WEBP
                            </option>

                        </select>

                    </div>


                    {{-- FROM DATE --}}

                    <div class="col-md-2">

                        <label class="form-label fw-semibold">
                            From Date
                        </label>

                        <input
                            type="date"
                            name="date_from"
                            class="form-control"
                            value="{{ request('date_from', '') }}"
                        >

                    </div>


                    {{-- TO DATE --}}

                    <div class="col-md-2">

                        <label class="form-label fw-semibold">
                            To Date
                        </label>

                        <input
                            type="date"
                            name="date_to"
                            class="form-control"
                            value="{{ request('date_to', '') }}"
                        >

                    </div>


                    {{-- MIN SIZE --}}

                    <div class="col-md-2">

                        <label class="form-label fw-semibold">
                            Min Size (KB)
                        </label>

                        <input
                            type="number"
                            name="min_size"
                            class="form-control"
                            min="0"
                            step="0.01"
                            placeholder="e.g. 12"
                            value="{{ request('min_size', '') }}"
                        >

                    </div>


                    {{-- MAX SIZE --}}

                    <div class="col-md-2">

                        <label class="form-label fw-semibold">
                            Max Size (KB)
                        </label>

                        <input
                            type="number"
                            name="max_size"
                            class="form-control"
                            min="0"
                            step="0.01"
                            placeholder="e.g. 24"
                            value="{{ request('max_size', '') }}"
                        >

                    </div>


                    {{-- BUTTONS --}}

                    <div class="col-md-4 d-flex align-items-end gap-2">

                        <button
                            type="submit"
                            class="btn btn-primary toolbar-button"
                        >
                            🔎 Apply Filters
                        </button>

                        <a
                            href="{{ route('products.gallery') }}"
                            class="btn btn-secondary toolbar-button"
                        >
                            Reset
                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- =====================================================
         EXPORT / CLEANUP
    ====================================================== --}}

    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <div class="d-flex flex-wrap gap-2">


                {{-- EXPORT --}}

                @if(Route::has('products.gallery.export'))

                    <a
                        href="{{ route('products.gallery.export', request()->query()) }}"
                        class="btn btn-outline-success toolbar-button"
                    >
                        📊 Export Filtered CSV
                    </a>

                @else

                    <button
                        type="button"
                        class="btn btn-outline-secondary toolbar-button"
                        disabled
                    >
                        📊 Export Filtered CSV
                    </button>

                @endif


                {{-- CLEANUP --}}

                @if(
                    ($missingCount ?? 0) > 0 &&
                    Route::has('products.gallery.cleanup-missing')
                )

                    <form
                        action="{{ route('products.gallery.cleanup-missing') }}"
                        method="POST"
                        onsubmit="return confirm('Are you sure you want to permanently remove all missing-image database records?')"
                    >

                        @csrf

                        @method('DELETE')

                        <button
                            type="submit"
                            class="btn btn-outline-warning toolbar-button"
                        >
                            🧹 Clean Missing Images
                        </button>

                    </form>

                @endif


                <span class="badge bg-primary d-flex align-items-center px-3">

                    Showing
                    {{ $products->count() }}
                    of
                    {{ $products->total() }}

                </span>

            </div>

        </div>

    </div>


    {{-- =====================================================
         BULK MANAGEMENT
    ====================================================== --}}

    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <div class="d-flex flex-wrap align-items-center gap-2">


                {{-- SELECT ALL --}}

                <div class="form-check me-3">

                    <input
                        type="checkbox"
                        class="form-check-input"
                        id="selectAll"
                    >

                    <label
                        class="form-check-label fw-semibold"
                        for="selectAll"
                    >
                        Select All
                    </label>

                </div>


                {{-- COUNT --}}

                <span
                    id="selectedCount"
                    class="badge bg-secondary"
                >
                    0 Selected
                </span>


                {{-- ZIP --}}

                @if(Route::has('products.gallery.download-zip'))

                    <form
                        action="{{ route('products.gallery.download-zip') }}"
                        method="POST"
                        id="bulkZipForm"
                    >

                        @csrf

                        <div id="zipSelectedInputs"></div>

                        <button
                            type="submit"
                            class="btn btn-success"
                            id="bulkZipButton"
                            disabled
                        >
                            📦 Download Selected ZIP
                        </button>

                    </form>

                @else

                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        disabled
                    >
                        📦 Download Selected ZIP
                    </button>

                @endif


                {{-- DELETE --}}

                @if(Route::has('products.bulkDestroy'))

                    <form
                        action="{{ route('products.bulkDestroy') }}"
                        method="POST"
                        id="bulkDeleteForm"
                        class="ms-auto"
                    >

                        @csrf

                        @method('DELETE')

                        <div id="selectedInputs"></div>

                        <button
                            type="submit"
                            class="btn btn-danger"
                            id="bulkDeleteButton"
                            disabled
                            onclick="return confirmBulkDelete()"
                        >
                            🗑️ Delete Selected
                        </button>

                    </form>

                @endif

            </div>

        </div>

    </div>


    {{-- =====================================================
         GALLERY
    ====================================================== --}}

    @if($products->count() > 0)

        <div class="row g-4">

            @foreach($products as $product)

                @php

                    $imagePath = $product->image
                        ? public_path($product->image)
                        : null;

                    $imageExists =
                        $imagePath &&
                        file_exists($imagePath) &&
                        is_file($imagePath);

                    $width = null;
                    $height = null;
                    $fileSize = null;
                    $extension = null;

                    if ($imageExists) {

                        $imageInfo =
                            @getimagesize($imagePath);

                        if ($imageInfo) {

                            $width =
                                $imageInfo[0];

                            $height =
                                $imageInfo[1];
                        }

                        $fileSize =
                            @filesize($imagePath);

                        $extension =
                            strtolower(
                                pathinfo(
                                    $imagePath,
                                    PATHINFO_EXTENSION
                                )
                            );
                    }

                    $formattedSize = 'N/A';

                    if ($fileSize !== null) {

                        if ($fileSize >= 1048576) {

                            $formattedSize =
                                number_format(
                                    $fileSize / 1048576,
                                    2
                                ) . ' MB';

                        } elseif ($fileSize >= 1024) {

                            $formattedSize =
                                number_format(
                                    $fileSize / 1024,
                                    2
                                ) . ' KB';

                        } else {

                            $formattedSize =
                                $fileSize . ' Bytes';
                        }
                    }

                @endphp


                <div class="col-md-6 col-lg-4">

                    <div
                        class="card shadow-sm h-100 gallery-card {{ !$imageExists ? 'missing-card' : '' }}"
                        id="card-{{ $product->id }}"
                    >


                        {{-- IMAGE --}}

                        <div class="image-wrapper">

                            <div class="select-box">

                                <input
                                    type="checkbox"
                                    class="form-check-input product-checkbox"
                                    value="{{ $product->id }}"
                                    data-name="{{ $product->name }}"
                                >

                            </div>


                            @if($imageExists)

                                <img
                                    src="{{ asset($product->image) }}"
                                    alt="{{ $product->name }}"
                                    class="gallery-image rounded-top"
                                    data-bs-toggle="modal"
                                    data-bs-target="#imageModal"
                                    onclick="showImage(
                                        @js(asset($product->image)),
                                        @js($product->name)
                                    )"
                                >

                            @else

                                <div
                                    class="gallery-image missing-image-box rounded-top d-flex flex-column justify-content-center align-items-center bg-warning-subtle"
                                >

                                    <div class="display-5">
                                        ⚠️
                                    </div>

                                    <strong class="text-warning-emphasis">
                                        Missing Image
                                    </strong>

                                    <small class="text-muted">
                                        Physical file not found
                                    </small>

                                </div>

                            @endif

                        </div>


                        {{-- CARD BODY --}}

                        <div class="card-body">


                            <div
                                class="d-flex justify-content-between align-items-start gap-2"
                            >

                                <h5 class="card-title fw-bold mb-3">

                                    {{ $product->name }}

                                </h5>

                                <span class="badge bg-dark">

                                    #{{ $product->id }}

                                </span>

                            </div>


                            @if(!$imageExists)

                                <div class="alert alert-warning py-2">

                                    ⚠️ Physical image file is missing.

                                </div>

                            @endif


                            {{-- METADATA --}}

                            <div class="metadata text-muted mb-3">


                                @if($width && $height)

                                    <div>

                                        📐 Dimensions:

                                        {{ $width }}
                                        ×
                                        {{ $height }}

                                        px

                                    </div>

                                @endif


                                <div>

                                    📦 Size:

                                    {{ $formattedSize }}

                                </div>


                                @if($extension)

                                    <div>

                                        🖼️ Format:

                                        {{ strtoupper($extension) }}

                                    </div>

                                @endif


                                <div>

                                    📅 Uploaded:

                                    {{ $product->created_at
                                        ? $product->created_at->format('d M Y, h:i A')
                                        : 'N/A'
                                    }}

                                </div>

                            </div>


                            {{-- ACTIONS --}}

                            <div class="d-flex gap-2">

                                @if($imageExists)

                                    <button
                                        type="button"
                                        class="btn btn-outline-primary btn-sm flex-fill"
                                        data-bs-toggle="modal"
                                        data-bs-target="#imageModal"
                                        onclick="showImage(
                                            @js(asset($product->image)),
                                            @js($product->name)
                                        )"
                                    >
                                        🔍 View
                                    </button>


                                    @if(Route::has('products.download'))

                                        <a
                                            href="{{ route('products.download', $product) }}"
                                            class="btn btn-success btn-sm flex-fill"
                                        >
                                            📥 Download
                                        </a>

                                    @else

                                        <button
                                            type="button"
                                            class="btn btn-outline-secondary btn-sm flex-fill"
                                            disabled
                                        >
                                            📥 Download
                                        </button>

                                    @endif

                                @else

                                    <button
                                        type="button"
                                        class="btn btn-outline-secondary btn-sm flex-fill"
                                        disabled
                                    >
                                        🔍 Unavailable
                                    </button>

                                @endif

                            </div>

                        </div>

                    </div>

                </div>

            @endforeach

        </div>


        {{-- =================================================
             PAGINATION
        ================================================== --}}

        <div class="d-flex justify-content-center mt-5">

            {{ $products
                ->onEachSide(1)
                ->links('pagination::bootstrap-5')
            }}

        </div>

    @else

        {{-- =================================================
             EMPTY
        ================================================== --}}

        <div class="card shadow-sm">

            <div class="card-body text-center py-5">

                <div class="display-1 mb-3">
                    🖼️
                </div>

                <h4>
                    No Images Found
                </h4>

                <p class="text-muted">
                    No images match the selected filters.
                </p>

                <a
                    href="{{ route('products.index') }}"
                    class="btn btn-primary"
                >
                    Upload Image
                </a>

            </div>

        </div>

    @endif

</div>


{{-- =========================================================
     IMAGE MODAL
========================================================= --}}

<div
    class="modal fade"
    id="imageModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-xl modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5
                    class="modal-title"
                    id="modalImageTitle"
                >
                    Image Preview
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>

            <div class="modal-body text-center">

                <img
                    src=""
                    id="modalImage"
                    class="image-preview"
                    alt="Image Preview"
                >

            </div>

        </div>

    </div>

</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


<script>

    /*
    |--------------------------------------------------------------------------
    | Elements
    |--------------------------------------------------------------------------
    */

    const selectAll =
        document.getElementById('selectAll');

    const checkboxes =
        document.querySelectorAll(
            '.product-checkbox'
        );

    const selectedCount =
        document.getElementById(
            'selectedCount'
        );

    const selectedCountTop =
        document.getElementById(
            'selectedCountTop'
        );

    const bulkDeleteButton =
        document.getElementById(
            'bulkDeleteButton'
        );

    const bulkZipButton =
        document.getElementById(
            'bulkZipButton'
        );

    const selectedInputs =
        document.getElementById(
            'selectedInputs'
        );

    const zipSelectedInputs =
        document.getElementById(
            'zipSelectedInputs'
        );


    /*
    |--------------------------------------------------------------------------
    | Update Selection
    |--------------------------------------------------------------------------
    */

    function updateSelection()
    {
        const selected = [];

        checkboxes.forEach(
            function (checkbox)
            {
                const card =
                    document.getElementById(
                        'card-' + checkbox.value
                    );

                if (checkbox.checked)
                {
                    selected.push(
                        checkbox.value
                    );

                    if (card)
                    {
                        card.classList.add(
                            'selected-card'
                        );
                    }
                }
                else
                {
                    if (card)
                    {
                        card.classList.remove(
                            'selected-card'
                        );
                    }
                }
            }
        );


        /*
        |--------------------------------------------------------------------------
        | Selected Count
        |--------------------------------------------------------------------------
        */

        if (selectedCount)
        {
            selectedCount.textContent =
                selected.length +
                ' Selected';
        }


        if (selectedCountTop)
        {
            selectedCountTop.textContent =
                selected.length;
        }


        /*
        |--------------------------------------------------------------------------
        | Buttons
        |--------------------------------------------------------------------------
        */

        if (bulkDeleteButton)
        {
            bulkDeleteButton.disabled =
                selected.length === 0;
        }

        if (bulkZipButton)
        {
            bulkZipButton.disabled =
                selected.length === 0;
        }


        /*
        |--------------------------------------------------------------------------
        | Delete Hidden Inputs
        |--------------------------------------------------------------------------
        */

        if (selectedInputs)
        {
            selectedInputs.innerHTML = '';

            selected.forEach(
                function (id)
                {
                    const input =
                        document.createElement(
                            'input'
                        );

                    input.type = 'hidden';

                    input.name =
                        'products[]';

                    input.value = id;

                    selectedInputs.appendChild(
                        input
                    );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | ZIP Hidden Inputs
        |--------------------------------------------------------------------------
        */

        if (zipSelectedInputs)
        {
            zipSelectedInputs.innerHTML = '';

            selected.forEach(
                function (id)
                {
                    const input =
                        document.createElement(
                            'input'
                        );

                    input.type = 'hidden';

                    input.name =
                        'products[]';

                    input.value = id;

                    zipSelectedInputs.appendChild(
                        input
                    );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Select All State
        |--------------------------------------------------------------------------
        */

        if (selectAll)
        {
            selectAll.checked =
                selected.length ===
                checkboxes.length &&
                checkboxes.length > 0;

            selectAll.indeterminate =
                selected.length > 0 &&
                selected.length <
                checkboxes.length;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Select All
    |--------------------------------------------------------------------------
    */

    if (selectAll)
    {
        selectAll.addEventListener(
            'change',
            function ()
            {
                checkboxes.forEach(
                    function (checkbox)
                    {
                        checkbox.checked =
                            selectAll.checked;
                    }
                );

                updateSelection();
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Individual Checkboxes
    |--------------------------------------------------------------------------
    */

    checkboxes.forEach(
        function (checkbox)
        {
            checkbox.addEventListener(
                'change',
                updateSelection
            );
        }
    );


    /*
    |--------------------------------------------------------------------------
    | Bulk Delete Confirmation
    |--------------------------------------------------------------------------
    */

    function confirmBulkDelete()
    {
        const selected =
            document.querySelectorAll(
                '.product-checkbox:checked'
            );

        if (selected.length === 0)
        {
            alert(
                'Please select at least one image.'
            );

            return false;
        }

        return confirm(
            'Are you sure you want to delete ' +
            selected.length +
            ' selected image(s)? This action cannot be undone.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ZIP Confirmation
    |--------------------------------------------------------------------------
    */

    const bulkZipForm =
        document.getElementById(
            'bulkZipForm'
        );

    if (bulkZipForm)
    {
        bulkZipForm.addEventListener(
            'submit',
            function (event)
            {
                const selected =
                    document.querySelectorAll(
                        '.product-checkbox:checked'
                    );

                if (selected.length === 0)
                {
                    event.preventDefault();

                    alert(
                        'Please select at least one image.'
                    );

                    return false;
                }

                if (
                    !confirm(
                        'Download ' +
                        selected.length +
                        ' selected image(s) as ZIP?'
                    )
                )
                {
                    event.preventDefault();

                    return false;
                }
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Image Preview
    |--------------------------------------------------------------------------
    */

    function showImage(
        imageUrl,
        imageName
    )
    {
        const image =
            document.getElementById(
                'modalImage'
            );

        const title =
            document.getElementById(
                'modalImageTitle'
            );

        if (image)
        {
            image.src = imageUrl;
        }

        if (title)
        {
            title.textContent =
                imageName +
                ' - Preview';
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Initial Selection
    |--------------------------------------------------------------------------
    */

    updateSelection();

</script>

</body>

</html>