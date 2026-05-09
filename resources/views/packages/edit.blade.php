@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Package</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Errors:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('packages.update', $package->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="height">Height</label>
            <input type="text" name="height" class="form-control" value="{{ old('height', $package->height) }}" required>
        </div>

        <div class="form-group">
            <label for="width">Width</label>
            <input type="text" name="width" class="form-control" value="{{ old('width', $package->width) }}" required>
        </div>

        <div class="form-group">
            <label for="depth">Depth</label>
            <input type="text" name="depth" class="form-control" value="{{ old('depth', $package->depth) }}" required>
        </div>

        <div class="form-group">
            <label for="weight">Weight</label>
            <input type="text" name="weight" class="form-control" value="{{ old('weight', $package->weight) }}" required>
        </div>

        <div class="form-group">
            <label for="weight_unit">Weight Unit</label>
            <select name="weight_unit" class="form-control" required>
    <option value="kg" {{ old('weight_unit', $package->weight_unit) == 'kg' ? 'selected' : '' }}>kg</option>
    <option value="lb" {{ old('weight_unit', $package->weight_unit) == 'lb' ? 'selected' : '' }}>lb</option>
</select>
        </div>

        <div class="form-group">
            <label for="measurement_unit">Measurement Unit</label>
            <select name="measurement_unit" class="form-control" required>
    <option value="cm" {{ old('measurement_unit', $package->measurement_unit) == 'cm' ? 'selected' : '' }}>cm</option>
    <option value="m" {{ old('measurement_unit', $package->measurement_unit) == 'm' ? 'selected' : '' }}>m</option>
    <option value="inch" {{ old('measurement_unit', $package->measurement_unit) == 'inch' ? 'selected' : '' }}>inch</option>
</select>
        </div>

        <div class="form-group">
            <label for="is_breakable">Is Breakable</label>
            <input type="checkbox" name="is_breakable" value="1" {{ old('is_breakable', $package->is_breakable) ? 'checked' : '' }}>
        </div>

        <div class="form-group">
            <label for="is_flammable">Is Flammable</label>
            <input type="checkbox" name="is_flammable" value="1" {{ old('is_flammable', $package->is_flammable) ? 'checked' : '' }}>
        </div>

        <div class="form-group">
            <label for="has_fluid">Has Fluid</label>
            <input type="checkbox" name="has_fluid" value="1" {{ old('has_fluid', $package->has_fluid) ? 'checked' : '' }}>
        </div>

        

        <div class="form-group">
            <label for="image">Picture</label><br>
            @if ($package->picture)
                <img src="{{ asset('storage/' . $package->picture) }}" alt="Package Image" style="width: 150px; margin-bottom: 10px;"><br>
            @endif
            <input id="image" name="image" type="file" class="form-control" accept="image/*">
            @error('image')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Update Package</button>
    </form>
</div>
@endsection
