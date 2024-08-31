@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Edit Product</h1>
        <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="name">Product Name</label>
                <input type="text" class="form-control" name="name" id="name" value="{{ $product->name }}" required>
            </div>

            <div class="form-group">
                <label for="details">Product Details</label>
                @foreach ($product->details as $index => $detail)
                    <input type="text" class="form-control mb-2" name="detail[]" value="{{ $detail->detail }}" required>

                    @if ($detail->image_path)
                        <img src="{{ asset('storage/' . $detail->image_path) }}" width="100" class="mb-2">
                        <!-- Retain the existing image path -->
                        <input type="hidden" name="existing_image_path[]" value="{{ $detail->image_path }}">
                    @else
                        <input type="hidden" name="existing_image_path[]" value="">
                    @endif

                    <!-- File input for uploading a new image -->
                    <input type="file" class="form-control mb-3" name="image_path[]">
                @endforeach
                <button type="button" class="btn btn-secondary" onclick="addDetailField()">Add More Details</button>
            </div>

            <button type="submit" class="btn btn-primary">Update Product</button>
        </form>
    </div>

    <script>
        function addDetailField() {
            const container = document.querySelector('.form-group');

            const inputDetail = document.createElement('input');
            inputDetail.type = 'text';
            inputDetail.name = 'detail[]';
            inputDetail.className = 'form-control mb-2';
            inputDetail.required = true;

            const inputImage = document.createElement('input');
            inputImage.type = 'file';
            inputImage.name = 'image_path[]';
            inputImage.className = 'form-control mb-3';

            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'existing_image_path[]';
            hiddenInput.value = '';

            container.appendChild(inputDetail);
            container.appendChild(inputImage);
            container.appendChild(hiddenInput);
        }
    </script>
@endsection
