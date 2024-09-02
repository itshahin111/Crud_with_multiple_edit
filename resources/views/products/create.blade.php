@include('layouts.app')
@extends('layouts.app')

@section('content')
    <nav class="navbar bg-body-tertiary">
        <a class="nav-link active" href="{{ route('products.index') }}">Home</a>
    </nav>
    <div class="container">
        <h1>Create Product</h1>
        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="d-flex flex-row-reverse">
                <button type="button" class="btn btn-primary" id="add-item-row">Add More</button>
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="name" placeholder="Name" required>
            </div>

            <div id="input-fields-container">
                <div class="input-group mb-3 item-row">
                    <input type="text" class="form-control" name="detail[]" placeholder="Description" required>
                    <input type="file" class="form-control" name="image_path[]">
                    <button type="button" class="btn btn-danger remove-item-row" style="display:none;"
                        onclick="remove(this)">Remove</button>
                </div>
            </div>


            <button type="submit" class="btn btn-success mt-3">Submit</button>
        </form>
    </div>

    <script>
        $('#add-item-row').click(function() {
            var clonedDiv = $('.item-row:first').clone();
            clonedDiv.find('input').val(''); // Reset the values in the cloned input fields
            clonedDiv.appendTo('#input-fields-container');
            clonedDiv.find('.remove-item-row').show(); // Show the remove button on the cloned row
        });

        function remove(btn) {
            $(btn).closest('.item-row').remove();
        }
    </script>
@endsection
