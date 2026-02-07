@extends('layouts.app')

@section('content')
<form method="POST" action="{{ route('requests.store') }}" class="p-4 shadow rounded bg-light">
    @csrf
    <input type="hidden" name="package_id" value="{{ $package_id }}">

    <h4 class="mb-4">Send Delivery Request</h4>
<div class="form-group mb-3">
    <label for="takeof_address_id" class="form-label">Pick-up Address</label>
    <select name="takeof_address_id" id="takeof_address_id" class="form-select" required>
        <option value="" disabled selected>Select pick-up location</option>
        @foreach($addresses as $address)
            <option value="{{ $address->id }}">
                {{ $address->region->region_name ?? 'N/A' }}, 
                {{ $address->street ?? 'N/A' }},
                House #: {{ $address->house_number ?? 'N/A' }},
                Building: {{ $address->building ?? 'N/A' }},
                Zip Code: {{ $address->zip_code ?? 'N/A' }},
                Coordinates: {{ $address->coordinates ?? 'N/A' }},
                Floor: {{ $address->floor ?? 'N/A' }},
                Type: {{ $address->type ?? 'N/A' }},
                Latitude: {{ $address->latitude ?? 'N/A' }},
                Longitude: {{ $address->longitude ?? 'N/A' }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-group mb-3">
    <label for="dropof_address_id" class="form-label">Drop-off Address</label>
    <select name="dropof_address_id" id="dropof_address_id" class="form-select" required>
        <option value="" disabled selected>Select drop-off location</option>
        @foreach($addresses as $address)
            <option value="{{ $address->id }}">
                {{ $address->region->region_name ?? 'N/A' }}, 
                {{ $address->street ?? 'N/A' }},
                House #: {{ $address->house_number ?? 'N/A' }},
                Building: {{ $address->building ?? 'N/A' }},
                Zip Code: {{ $address->zip_code ?? 'N/A' }},
                Coordinates: {{ $address->coordinates ?? 'N/A' }},
                Floor: {{ $address->floor ?? 'N/A' }},
                Type: {{ $address->type ?? 'N/A' }},
                Latitude: {{ $address->latitude ?? 'N/A' }},
                Longitude: {{ $address->longitude ?? 'N/A' }}
            </option>
        @endforeach
    </select>
</div>


    <div class="form-group mb-4">
        <label for="scheduled_at" class="form-label">Scheduled Time</label>
        <input type="datetime-local" name="scheduled_at" id="scheduled_at" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-success w-100">Send Request</button>
</form>
@endsection