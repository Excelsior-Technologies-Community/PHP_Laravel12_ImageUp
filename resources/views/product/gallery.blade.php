<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

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
        }

        .gallery-card:hover {
            transform: translateY(-4px);
        }

        .gallery-image {
            width: 100%;
            height: 220px;
            object-fit: contain;
            cursor: pointer;
        }

        .image-preview {
            width: 100%;
            max-height: 80vh;
            object-fit: contain;
        }

        .metadata {
            font-size: 14px;
        }

        .select-box {
            position: absolute;
            top: 12px;
            left: 12px;
            z-index: 10;
        }

        .image-wrapper {
            position: relative;
        }

        .selected-card {
            border: 3px solid #dc3545 !important;
        }
    </style>

</head>

<body>

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


    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold mb-1">
                Image Gallery
            </h2>

            <p class="text-muted mb-0">
                Manage, preview, download and bulk delete uploaded images.
            </p>

        </div>

        <div>

            <span class="badge bg-primary fs-6">

                {{ $products->total() }}

                Images

            </span>

        </div>

    </div>


    {{-- Bulk Management Toolbar --}}
    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <div
                class="d-flex flex-wrap align-items-center gap-2"
            >

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


                <span
                    id="selectedCount"
                    class="badge bg-secondary"
                >
                    0 Selected
                </span>


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

            </div>

        </div>

    </div>


    {{-- Gallery --}}
    @if($products->count() > 0)

        <div class="row g-4">

            @foreach($products as $product)

                @php

                    $imagePath = public_path($product->image);

                    $width = null;
                    $height = null;
                    $fileSize = null;

                    if (
                        $product->image &&
                        file_exists($imagePath)
                    ) {

                        $imageInfo = @getimagesize($imagePath);

                        if ($imageInfo) {

                            $width = $imageInfo[0];
                            $height = $imageInfo[1];

                        }

                        $fileSize = filesize($imagePath);

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
                        class="card shadow-sm h-100 gallery-card"
                        id="card-{{ $product->id }}"
                    >

                        {{-- Image --}}
                        <div class="image-wrapper">

                            <div class="select-box">

                                <input
                                    type="checkbox"
                                    class="form-check-input product-checkbox"
                                    value="{{ $product->id }}"
                                    data-name="{{ $product->name }}"
                                >

                            </div>


                            <img
                                src="{{ asset($product->image) }}"
                                alt="{{ $product->name }}"
                                class="gallery-image rounded-top"
                                data-bs-toggle="modal"
                                data-bs-target="#imageModal"
                                onclick="showImage(
                                    '{{ asset($product->image) }}',
                                    '{{ addslashes($product->name) }}'
                                )"
                            >

                        </div>


                        {{-- Card Content --}}
                        <div class="card-body">

                            <h5 class="card-title fw-bold">

                                {{ $product->name }}

                            </h5>


                            <div class="metadata text-muted mb-3">

                                @if($width && $height)

                                    <div>
                                        📐 Dimensions:
                                        {{ $width }} × {{ $height }} px
                                    </div>

                                @endif


                                <div>
                                    📦 Size:
                                    {{ $formattedSize }}
                                </div>


                                <div>
                                    📅 Uploaded:
                                    {{ $product->created_at->format('d M Y, h:i A') }}
                                </div>

                            </div>


                            {{-- Actions --}}
                            <div class="d-flex gap-2">

                                <button
                                    type="button"
                                    class="btn btn-outline-primary btn-sm flex-fill"
                                    data-bs-toggle="modal"
                                    data-bs-target="#imageModal"
                                    onclick="showImage(
                                        '{{ asset($product->image) }}',
                                        '{{ addslashes($product->name) }}'
                                    )"
                                >
                                    🔍 View
                                </button>


                                <a
                                    href="{{ route('products.download', $product) }}"
                                    class="btn btn-success btn-sm flex-fill"
                                >
                                    📥 Download
                                </a>

                            </div>

                        </div>

                    </div>

                </div>

            @endforeach

        </div>


        {{-- Pagination --}}
        <div class="d-flex justify-content-center mt-5">

            {{ $products->links('pagination::bootstrap-5') }}

        </div>

    @else

        <div class="card shadow-sm">

            <div class="card-body text-center py-5">

                <div class="display-1 mb-3">
                    🖼️
                </div>

                <h4>
                    No Images Found
                </h4>

                <p class="text-muted">
                    Upload some products to see them in the gallery.
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


{{-- Image Preview Modal --}}
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

    const selectAll =
        document.getElementById('selectAll');

    const checkboxes =
        document.querySelectorAll('.product-checkbox');

    const selectedCount =
        document.getElementById('selectedCount');

    const bulkDeleteButton =
        document.getElementById('bulkDeleteButton');

    const selectedInputs =
        document.getElementById('selectedInputs');


    function updateSelection()
    {
        const selected = [];

        checkboxes.forEach(function (checkbox) {

            const card =
                document.getElementById(
                    'card-' + checkbox.value
                );

            if (checkbox.checked) {

                selected.push(checkbox.value);

                card.classList.add('selected-card');

            } else {

                card.classList.remove('selected-card');

            }

        });


        selectedCount.textContent =
            selected.length + ' Selected';


        bulkDeleteButton.disabled =
            selected.length === 0;


        selectedInputs.innerHTML = '';


        selected.forEach(function (id) {

            const input =
                document.createElement('input');

            input.type = 'hidden';

            input.name = 'products[]';

            input.value = id;

            selectedInputs.appendChild(input);

        });


        selectAll.checked =
            selected.length === checkboxes.length &&
            checkboxes.length > 0;
    }


    selectAll.addEventListener(
        'change',
        function ()
        {
            checkboxes.forEach(function (checkbox) {

                checkbox.checked =
                    selectAll.checked;

            });

            updateSelection();
        }
    );


    checkboxes.forEach(function (checkbox) {

        checkbox.addEventListener(
            'change',
            updateSelection
        );

    });


    function confirmBulkDelete()
    {
        const selected =
            document.querySelectorAll(
                '.product-checkbox:checked'
            );

        if (selected.length === 0) {

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


    function showImage(imageUrl, imageName)
    {
        document.getElementById(
            'modalImage'
        ).src = imageUrl;


        document.getElementById(
            'modalImageTitle'
        ).textContent =
            imageName + ' - Preview';
    }

</script>

</body>

</html>