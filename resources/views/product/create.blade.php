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