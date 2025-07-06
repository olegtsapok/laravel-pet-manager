@extends('layouts.app')

@section('content')
    <h1 class="mb-4">Add Pet</h1>

    <form enctype="multipart/form-data" action="{{ route('pets.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Pet Name</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select" required>
                @foreach(['available', 'pending', 'sold'] as $status)
                    <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>

        
        <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <input type="text" name="category" id="category" class="form-control" value="{{ old('category', isset($pet) ? $pet['category']['name'] ?? '' : '') }}">
        </div>


        <div class="mb-3">
            <label for="tags" class="form-label">Tags (comma-separated)</label>
            <input type="text" name="tags" id="tags" class="form-control" value="{{ old('tags', isset($pet) ? collect($pet['tags'] ?? [])->pluck('name')->implode(', ') : '') }}">
        </div>


        <div class="mb-3">
            <label for="photoUrls" class="form-label">Photo URLs (comma-separated)</label>
            <input type="text" name="photoUrls" id="photoUrls" class="form-control" value="{{ old('photoUrls', isset($pet) ? implode(', ', $pet['photoUrls'] ?? []) : '') }}">
        </div>

        
        <div class="mb-3">
            <label for="imageFile" class="form-label">Upload Image</label>
            <input type="file" name="imageFile" id="imageFile" class="form-control">
        </div>

        <button type="submit" class="btn btn-success">Create Pet</button>
    </form>
@endsection
