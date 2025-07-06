@extends('layouts.app')

@section('content')
    <h1 class="mb-4">Pet List</h1>

    {{-- Filter Form --}}
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-3">
            <label for="status" class="form-label">Filter by Status</label>
            <select name="status" id="status" class="form-select" onchange="this.form.submit()">
                @foreach(['available', 'pending', 'sold'] as $opt)
                    <option value="{{ $opt }}" {{ $status === $opt ? 'selected' : '' }}>{{ ucfirst($opt) }}</option>
                @endforeach
            </select>
        </div>
    </form>

    @if($pets->isEmpty())
        <p class="text-muted">No pets found for status "{{ $status }}".</p>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>
                            <a href="{{ route('pets.index', array_merge(request()->all(), ['sort' => 'id', 'direction' => $sort === 'id' && $direction === 'asc' ? 'desc' : 'asc'])) }}">
                                ID
                                @if($sort === 'id') <small>{{ $direction === 'asc' ? '↑' : '↓' }}</small> @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('pets.index', array_merge(request()->all(), ['sort' => 'name', 'direction' => $sort === 'name' && $direction === 'asc' ? 'desc' : 'asc'])) }}">
                                Name
                                @if($sort === 'name') <small>{{ $direction === 'asc' ? '↑' : '↓' }}</small> @endif
                            </a>
                        </th>
                        <th>Status</th>
                        <th>Photos</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pets as $pet)
                        <tr>
                            <td>{{ $pet['id'] }}</td>
                            <td>{{ $pet['name'] ?? 'Unnamed' }}</td>
                            <td>
                                <span class="badge bg-{{ $pet['status'] === 'available' ? 'success' : ($pet['status'] === 'pending' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($pet['status'] ?? 'unknown') }}
                                </span>
                            </td>
                            <td>
                                @if(!empty($pet['photoUrls']))
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($pet['photoUrls'] as $url)
                                            <a href="{{ $url }}" target="_blank">
                                                <img src="{{ $url }}" alt="Photo" style="height: 50px; width: auto;" class="border rounded">
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('pets.edit', $pet['id']) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('pets.destroy', $pet['id']) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-center mt-3">
            {{ $pets->appends(request()->all())->links() }}
        </div>
    @endif
@endsection
