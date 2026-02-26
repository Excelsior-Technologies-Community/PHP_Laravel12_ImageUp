#  PHP_Laravel12_ImageUp

![Laravel](https://img.shields.io/badge/Laravel-12-red)
![PHP](https://img.shields.io/badge/PHP-8.2%2B-blue)

---

##  Overview

**PHP_Laravel12_ImageUp** is a beginner-friendly Laravel 12 project demonstrating automatic image upload and resizing using a reusable Model Trait (ImageUp-style). Images are resized, stored in the public directory, and displayed instantly after upload.

This project follows clean Laravel architecture and is production-ready.

---

##  Features

*  Laravel 12 installation
*  Image upload with automatic resize
*  Auto image saving using Model Trait
*  Database image path storage
*  Instant image display
*  Clean MVC structure
*  Beginner-friendly setup

---

##  Folder Structure

```
app/
 ├── Models/
 │    └── Product.php
 ├── Traits/
 │    └── HasImageUpload.php
 ├── Http/
 │    └── Controllers/
 │         └── ProductController.php

resources/
 └── views/
      └── product/
           └── create.blade.php

public/
 └── uploads/

routes/
 └── web.php
```

---

##  Requirements

* PHP 8.2+
* Composer
* XAMPP / Laragon / Local server
* MySQL
* Node.js (optional)
* VS Code (recommended)

---


## STEP 1 — Create Laravel 12 Project

```bash
composer create-project laravel/laravel image-upload-project
```

Run server:

```bash
php artisan serve
```

Open browser:

```
http://127.0.0.1:8000
```

---

## STEP 2 — Configure Database

Open `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

---

## STEP 3 — Install Image Resize Library

We use Intervention Image for resizing images.

```bash
composer require intervention/image:^2.7
```

---

## STEP 4 — Create Upload Folder

Create folder inside public directory:

```
public/uploads
```

OR via PowerShell:

```powershell
New-Item -ItemType Directory -Path public/uploads -Force
```

---

## STEP 5 — Create Model & Migration

```bash
php artisan make:model Product -m
```

### Edit Migration

`database/migrations/...create_products_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
```

Run migration:

```bash
php artisan migrate
```

---

## STEP 6 — Create Image Upload Trait

Create folder:

```
app/Traits
```

Create file:

```
app/Traits/HasImageUpload.php
```

### Trait Code (Auto Upload + Resize)

```php
<?php

namespace App\Traits;

use Intervention\Image\ImageManagerStatic as Image;

trait HasImageUpload
{
    public static function bootHasImageUpload()
    {
        static::saving(function ($model) {

            // check image exists
            if (request()->hasFile('image')) {

                $file = request()->file('image');

                $filename = time().'.'.$file->getClientOriginalExtension();

                $destination = public_path('uploads/'.$filename);

                // resize image automatically
                Image::make($file)
                    ->resize(400, 400)
                    ->save($destination);

                // save path in DB
                $model->image = 'uploads/'.$filename;
            }
        });
    }
}
```

---

## STEP 7 — Product Model

`app/Models/Product.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasImageUpload;

class Product extends Model
{
    use HasImageUpload;

    protected $fillable = [
        'name',
        'image'
    ];
}
```

---

## STEP 8 — Create Controller

```bash
php artisan make:controller ProductController
```

### Controller Code

`app/Http/Controllers/ProductController.php`

```php
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
            'name' => 'required',     
            'image' => 'required|image' 
        ]);

        // Image upload automatically handled by Trait
        Product::create($request->all());

        // Redirect back with success message
        return back()->with('success','Product Uploaded Successfully!');
    }
}
```

---

## STEP 9 — Routes

`routes/web.php`

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

// Display product upload page
Route::get('/', [ProductController::class,'create']);

// Handle product form submission
Route::post('/products',[ProductController::class,'store'])
    ->name('products.store');
```

---

## STEP 10 — Create Blade View

Create folder:

```
resources/views/product
```

Create file:

```
create.blade.php
```

Full Blade File (UI + CSS)

```html
<!DOCTYPE html>
<html>
<head>
<title>Auto Image Upload</title>

<style>
/* ===== PAGE ===== */
body{
    font-family: 'Segoe UI', Arial, sans-serif;
    background: linear-gradient(135deg,#eef2f7,#dfe9f3);
    padding:40px;
    margin:0;
}

/* ===== MAIN BOX ===== */
.container{
    width:900px;
    margin:auto;
    background:#ffffff;
    padding:35px;
    border-radius:14px;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
}

/* ===== HEADINGS ===== */
h2{
    margin-top:0;
    color:#333;
    font-weight:600;
}

/* ===== INPUTS ===== */
input[type=text],
input[type=file]{
    width:100%;
    padding:12px;
    margin-top:8px;
    border:1px solid #ccc;
    border-radius:8px;
    font-size:15px;
    transition:.3s;
}

input:focus{
    border-color:#4CAF50;
    outline:none;
    box-shadow:0 0 5px rgba(76,175,80,0.3);
}

/* ===== BUTTON ===== */
button{
    background:linear-gradient(135deg,#28a745,#218838);
    color:white;
    padding:12px 25px;
    border:none;
    border-radius:8px;
    font-size:16px;
    cursor:pointer;
    transition:.3s;
}

button:hover{
    transform:translateY(-2px);
    box-shadow:0 6px 15px rgba(0,0,0,0.15);
}

/* ===== SUCCESS MESSAGE ===== */
.success{
    background:#e6ffed;
    color:#1e7e34;
    padding:12px;
    border-left:5px solid #28a745;
    border-radius:6px;
    margin-bottom:20px;
}

/* ===== PRODUCT CARD ===== */
.product{
    display:flex;
    align-items:center;
    gap:25px;
    border-radius:12px;
    padding:18px;
    margin-top:18px;
    background:#fafafa;
    box-shadow:0 4px 12px rgba(0,0,0,0.06);
    transition:.3s;
}

.product:hover{
    transform:translateY(-4px);
    box-shadow:0 10px 20px rgba(0,0,0,0.12);
}

/* ===== IMAGE ===== */
.product img{
    width:150px;
    height:150px;
    object-fit:cover;
    border-radius:10px;
    border:2px solid #eee;
}

/* ===== PRODUCT TITLE ===== */
.product h3{
    margin:0;
    font-size:22px;
    color:#333;
    font-weight:600;
}

/* ===== RESPONSIVE ===== */
@media(max-width:900px){
    .container{
        width:95%;
    }

    .product{
        flex-direction:column;
        text-align:center;
    }

    .product img{
        width:200px;
        height:200px;
    }
}
</style>

</head>
<body>

<div class="container">

<h2> Upload Product</h2>

@if(session('success'))
<div class="success">{{ session('success') }}</div>
@endif

<form method="POST"
action="{{ route('products.store') }}"
enctype="multipart/form-data">
@csrf

<label>Product Name</label>
<input type="text" name="name" placeholder="Enter product name" required>

<br><br>

<label>Product Image</label>
<input type="file" name="image" required>

<br><br>

<button>Upload Product</button>

</form>

<hr>

<h2> Uploaded Products</h2>

@foreach($products as $product)

<div class="product">

<img src="/{{ $product->image }}">

<h3>{{ $product->name }}</h3>

</div>

@endforeach

</div>

</body>
</html>
```

---

## STEP 11 — Run Project

```bash
php artisan serve
```

Open:

```
http://127.0.0.1:8000
```
<img width="967" height="422" alt="Screenshot 2026-02-26 121559" src="https://github.com/user-attachments/assets/7a1f59ea-422d-47dd-ad48-06e5dc767497" />

---
<img width="973" height="678" alt="Screenshot 2026-02-26 121611" src="https://github.com/user-attachments/assets/682e837d-0c0e-4ff1-beb1-39b487b3e630" />

---

## Final Result

Your system now:

* Automatically uploads images
* Resizes images (400×400)
* Saves path in database
* Displays images instantly
* Uses clean Laravel architecture
* Works perfectly in Laravel 12

