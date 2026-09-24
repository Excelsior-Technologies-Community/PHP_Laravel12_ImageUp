<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Edit Product Image</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        body {
            background: #f4f7fb;
            font-family: "Segoe UI", Arial, sans-serif;
        }

        .edit-container {
            max-width: 800px;
            margin: 50px auto;
        }

        .edit-card {
            background: #ffffff;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        }

        .current-image {
            width: 250px;
            height: 250px;
            object-fit: cover;
            border-radius: 15px;
            border: 2px solid #ddd;
        }

        .preview-image {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 15px;
            border: 2px solid #ddd;
        }

        #previewContainer {
            display: none;
        }

    </style>

</head>

<body>

<nav class="navbar navbar-dark bg-dark">

    <div class="container">

        <a
            href="{{ route('products.index') }}"
            class="navbar-brand"
        >
            🖼️ Image Upload Manager
        </a>

    </div>

</nav>


<div class="container edit-container">

    <div class="edit-card">

        <h2 class="mb-4">
            ✏️ Edit Product Image
        </h2>


        @if($errors->any())

            <div class="alert alert-danger">

                <ul class="mb-0">

                    @foreach($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        @endif


        <form
            method="POST"
            action="{{ route('products.update', $product) }}"
            enctype="multipart/form-data"
        >

            @csrf

            @method('PUT')


            {{-- Product Name --}}
            <div class="mb-4">

                <label class="form-label fw-bold">
                    Product Name
                </label>

                <input
                    type="text"
                    name="name"
                    class="form-control"
                    value="{{ old('name', $product->name) }}"
                    required
                >

            </div>


            {{-- Current Image --}}
            <div class="mb-4">

                <label class="form-label fw-bold">
                    Current Image
                </label>

                <br>

                @if($product->image)

                    <img
                        src="{{ asset($product->image) }}"
                        class="current-image"
                        alt="{{ $product->name }}"
                    >

                @else

                    <p class="text-muted">
                        No image available.
                    </p>

                @endif

            </div>


            {{-- Replace Image --}}
            <div class="mb-3">

                <label class="form-label fw-bold">
                    Replace Image
                </label>

                <input
                    type="file"
                    name="image"
                    id="editImageInput"
                    class="form-control"
                    accept="image/jpeg,image/png,image/gif,image/webp"
                >

                <small class="text-muted">
                    Leave empty if you don't want to replace the image.
                    Maximum 5MB.
                </small>

            </div>


            {{-- New Image Preview --}}
            <div
                id="previewContainer"
                class="mb-4"
            >

                <label class="form-label fw-bold">
                    New Image Preview
                </label>

                <br>

                <img
                    id="previewImage"
                    class="preview-image"
                    alt="New Image Preview"
                >

            </div>


            <div class="d-flex gap-2">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    💾 Save Changes
                </button>

                <a
                    href="{{ route('products.index') }}"
                    class="btn btn-secondary"
                >
                    Cancel
                </a>

            </div>

        </form>

    </div>

</div>


<script>

    document
        .getElementById('editImageInput')
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

</body>

</html>