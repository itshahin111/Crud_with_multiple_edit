@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-center">Products</h1>
        <a href="{{ route('products.create') }}" class="btn btn-primary mb-3">Create Product</a>
        <table class="table table-bordered text-center align-middle">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Details</th>
                    <th>Images</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>
                            @foreach ($product->details as $detail)
                                <div>{{ $detail->detail }}</div>
                            @endforeach
                        </td>
                        <td class="text-center">
                            <div class="d-flex flex-column align-items-center">
                                @foreach ($product->details as $detail)
                                    @if ($detail->image_path)
                                        <img src="{{ asset('storage/' . $detail->image_path) }}" width="100" class="mb-2">
                                    @endif
                                @endforeach
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-warning mb-2">Edit</a>
                            <form action="{{ route('products.destroy', $product) }}" method="POST"
                                style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
