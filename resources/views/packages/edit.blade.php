@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Edit Package</h2>

    {{-- Server Side Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Please fix the following errors:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Client Side Alert Box --}}
    <div id="alertBox" class="alert alert-danger" style="display:none;"></div>

    <form action="{{ route('packages.update', $package->id) }}" method="POST" enctype="multipart/form-data" id="packageForm">
        @csrf
        @method('PUT')

        <!-- Height -->
        <div class="form-group mb-3">
            <label for="height">Height</label>
            <input 
                type="number" 
                name="height" 
                id="height" 
                class="form-control @error('height') is-invalid @enderror" 
                value="{{ old('height', $package->height) }}" 
                step="0.01" 
                min="0.01" 
                placeholder="Enter height"
                required
            >
            <div id="height-error" style="color:red; display:none;">Height must be greater than zero.</div>
            @error('height')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Width -->
        <div class="form-group mb-3">
            <label for="width">Width</label>
            <input 
                type="number" 
                name="width" 
                id="width" 
                class="form-control @error('width') is-invalid @enderror" 
                value="{{ old('width', $package->width) }}" 
                step="0.01" 
                min="0.01" 
                placeholder="Enter width"
                required
            >
            <div id="width-error" style="color:red; display:none;">Width must be greater than zero.</div>
            @error('width')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Depth -->
        <div class="form-group mb-3">
            <label for="depth">Depth</label>
            <input 
                type="number" 
                name="depth" 
                id="depth" 
                class="form-control @error('depth') is-invalid @enderror" 
                value="{{ old('depth', $package->depth) }}" 
                step="0.01" 
                min="0.01" 
                placeholder="Enter depth"
                required
            >
            <div id="depth-error" style="color:red; display:none;">Depth must be greater than zero.</div>
            @error('depth')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Weight -->
        <div class="form-group mb-3">
            <label for="weight">Weight</label>
            <input 
                type="number" 
                name="weight" 
                id="weight" 
                class="form-control @error('weight') is-invalid @enderror" 
                value="{{ old('weight', $package->weight) }}" 
                step="0.01" 
                min="0.01" 
                placeholder="Enter weight"
                required
            >
            <div id="weight-error" style="color:red; display:none;">Weight must be greater than zero.</div>
            @error('weight')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Weight Unit -->
        <div class="form-group mb-3">
            <label for="weight_unit">Weight Unit</label>
            <select name="weight_unit" id="weight_unit" class="form-control @error('weight_unit') is-invalid @enderror" required>
                <option value="" disabled>Select weight unit</option>
                <option value="kg" {{ old('weight_unit', $package->weight_unit) == 'kg' ? 'selected' : '' }}>kg</option>
                <option value="lb" {{ old('weight_unit', $package->weight_unit) == 'lb' ? 'selected' : '' }}>lb</option>
                <option value="g"  {{ old('weight_unit', $package->weight_unit) == 'g'  ? 'selected' : '' }}>g</option>
            </select>
            @error('weight_unit')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Measurement Unit -->
        <div class="form-group mb-3">
            <label for="measurement_unit">Measurement Unit</label>
            <select name="measurement_unit" id="measurement_unit" class="form-control @error('measurement_unit') is-invalid @enderror" required>
                <option value="" disabled>Select measurement unit</option>
                <option value="cm"   {{ old('measurement_unit', $package->measurement_unit) == 'cm'   ? 'selected' : '' }}>cm</option>
                <option value="m"    {{ old('measurement_unit', $package->measurement_unit) == 'm'    ? 'selected' : '' }}>m</option>
                <option value="inch" {{ old('measurement_unit', $package->measurement_unit) == 'inch' ? 'selected' : '' }}>inch</option>
            </select>
            @error('measurement_unit')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Checkboxes -->
        <div class="form-group mb-3">
            <label>Package Properties</label>
            <div class="d-flex gap-4 mt-2">
                <div class="form-check">
                    <input 
                        type="checkbox" 
                        class="form-check-input" 
                        name="is_breakable" 
                        id="is_breakable"
                        value="1" 
                        {{ old('is_breakable', $package->is_breakable) ? 'checked' : '' }}
                    >
                    <label class="form-check-label" for="is_breakable">Is Breakable</label>
                </div>

                <div class="form-check">
                    <input 
                        type="checkbox" 
                        class="form-check-input" 
                        name="is_flammable" 
                        id="is_flammable"
                        value="1" 
                        {{ old('is_flammable', $package->is_flammable) ? 'checked' : '' }}
                    >
                    <label class="form-check-label" for="is_flammable">Is Flammable</label>
                </div>

                <div class="form-check">
                    <input 
                        type="checkbox" 
                        class="form-check-input" 
                        name="has_fluid" 
                        id="has_fluid"
                        value="1" 
                        {{ old('has_fluid', $package->has_fluid) ? 'checked' : '' }}
                    >
                    <label class="form-check-label" for="has_fluid">Has Fluid</label>
                </div>
            </div>
        </div>

        <!-- Picture -->
        <div class="form-group mb-4">
            <label for="image">Package Picture</label>

            {{-- Show existing image --}}
            @if ($package->picture)
                <div class="mb-2">
                    <p class="text-muted">Current Image:</p>
                    <img 
                        src="{{ asset('storage/' . $package->picture) }}" 
                        alt="Current Package Image" 
                        style="width:150px; height:150px; object-fit:cover; border-radius:8px; border:2px solid #dee2e6;"
                    >
                </div>
            @endif

            <input 
                id="image" 
                name="image" 
                type="file" 
                class="form-control @error('image') is-invalid @enderror" 
                accept="image/*"
            >
            <small class="text-muted">Leave empty to keep the current image.</small>
            @error('image')
                <div class="text-danger">{{ $message }}</div>
            @enderror

            {{-- New Image Preview --}}
            <div id="imagePreview" class="mt-2" style="display:none;">
                <p class="text-muted">New Image Preview:</p>
                <img 
                    id="previewImg" 
                    src="" 
                    alt="Preview" 
                    style="width:150px; height:150px; object-fit:cover; border-radius:8px; border:2px solid #dee2e6;"
                >
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Update Package</button>
            <a href="{{ route('packages.index') }}" class="btn btn-secondary">Cancel</a>
        </div>

    </form>
</div>
@endsection

@section('script')
<script>
    // Image preview
    document.getElementById('image').addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewImg').src = e.target.result;
                document.getElementById('imagePreview').style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    // Live validation on input
    ['height', 'width', 'depth', 'weight'].forEach(function(id) {
        document.getElementById(id).addEventListener('input', function() {
            const value = parseFloat(this.value);
            const errorDiv = document.getElementById(id + '-error');

            if (isNaN(value) || value <= 0) {
                errorDiv.style.display = 'block';
                this.style.borderColor = 'red';
            } else {
                errorDiv.style.display = 'none';
                this.style.borderColor = 'green';
            }
        });
    });

    // Form submit validation
    document.getElementById('packageForm').addEventListener('submit', function(e) {
        let isValid = true;
        let messages = [];

        const fields = [
            { id: 'height', label: 'Height' },
            { id: 'width',  label: 'Width'  },
            { id: 'depth',  label: 'Depth'  },
            { id: 'weight', label: 'Weight' },
        ];

        // Reset all errors first
        fields.forEach(function(field) {
            document.getElementById(field.id + '-error').style.display = 'none';
            document.getElementById(field.id).style.borderColor = '';
        });

        document.getElementById('alertBox').style.display = 'none';

        // Validate each field
        fields.forEach(function(field) {
            const value = parseFloat(document.getElementById(field.id).value);

            if (isNaN(value) || value <= 0) {
                isValid = false;
                messages.push(field.label + ' must be greater than zero.');
                document.getElementById(field.id + '-error').style.display = 'block';
                document.getElementById(field.id).style.borderColor = 'red';
            }
        });

        if (!isValid) {
            e.preventDefault();
            const alertBox = document.getElementById('alertBox');
            alertBox.innerHTML = '<strong>Please fix the following errors:</strong><ul>' 
                + messages.map(m => '<li>' + m + '</li>').join('') 
                + '</ul>';
            alertBox.style.display = 'block';

            // Scroll to top to see alert
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });
</script>
@endsection