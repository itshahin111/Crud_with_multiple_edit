@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Edit Product</h1>
        <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group mb-3">
                <label for="name">Product Name</label>
                <input type="text" class="form-control" name="name" id="name" value="{{ $product->name }}" required>
            </div>

            <div id="input-fields-container">
                @foreach ($product->details as $index => $detail)
                    <div class="item-row border p-3 mb-2" data-id="{{ $detail->id }}">
                        <input type="text" class="form-control mb-2" name="detail[]" value="{{ $detail->detail }}"
                            required>

                        @if ($detail->image_path)
                            <img src="{{ asset('storage/' . $detail->image_path) }}" width="100" class="mb-2">
                            <input type="hidden" name="existing_image_path[]" value="{{ $detail->image_path }}">
                            <input type="hidden" name="id[]" value="{{ $detail->id }}">
                        @else
                            <input type="hidden" name="existing_image_path[]" value="">
                        @endif

                        <input type="file" class="form-control mb-3" name="image_path[]">

                        <!-- Remove Button -->
                        <button type="button" class="btn btn-danger remove-item-row"
                            data-id="{{ $detail->id }}">Remove</button>
                    </div>
                @endforeach
            </div>

            <!-- Add More Button -->
            <button type="button" class="btn btn-secondary" id="add-item-row">Add More Details</button>

            <button type="submit" class="btn btn-primary mt-3">Update Product</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Event listener for adding a new detail row
            $('#add-item-row').on('click', function() {
                var clonedDiv = `
                    <div class="item-row border p-3 mb-2">
                        <input type="text" class="form-control mb-2" name="detail[]" required>
                        <input type="file" class="form-control mb-3" name="image_path[]">
                        <input type="hidden" name="existing_image_path[]" value="">
                        <button type="button" class="btn btn-danger remove-item-row">Remove</button>
                    </div>
                `;
                $('#input-fields-container').append(clonedDiv);
            });

            // Event listener for removing a detail row
            $(document).on('click', '.remove-item-row', function() {
                var row = $(this).closest('.item-row');
                var detailId = $(this).data('id');

                if (detailId) {
                    // Confirm before deleting
                    if (confirm('Are you sure you want to delete this detail?')) {
                        // AJAX request to delete detail from the server
                        $.ajax({
                            url: `/product-details/${detailId}`, // Adjust this URL to match your routes
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                // Remove row from the DOM on successful deletion
                                row.remove();
                                alert('Detail deleted successfully.');
                            },
                            error: function(xhr) {
                                alert('Failed to delete detail.');
                            }
                        });
                    }
                } else {
                    // Just remove the row if it's not saved in the database
                    row.remove();
                }
            });
        });
    </script>
@endsection
