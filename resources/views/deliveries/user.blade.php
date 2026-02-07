@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Your Deliveries</h2>

        {{-- Filter and Sort Form --}}
        <form method="GET" action="{{ route('deliveries.user') }}" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <label for="status">Filter by Status:</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">All</option>
                        <option value="paid, awaiting delivery" {{ request('status') == 'paid, awaiting delivery' ? 'selected' : '' }}>Paid, Awaiting Delivery</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="on_my_way_to_pickup" {{ request('status') == 'on_my_way_to_pickup' ? 'selected' : '' }}>On My Way to Pickup</option>
                        <option value="on_my_way_to_dropoff" {{ request('status') == 'on_my_way_to_dropoff' ? 'selected' : '' }}>On My Way to Dropoff</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>

                </div>
                <div class="col-md-4">
                    <label for="sort_by">Sort By:</label>
                    <select name="sort_by" id="sort_by" class="form-control">
                        <option value="">None</option>
                        <option value="time_asc" {{ request('sort_by') == 'time_asc' ? 'selected' : '' }}>Time (Earliest
                            First)</option>
                        <option value="time_desc" {{ request('sort_by') == 'time_desc' ? 'selected' : '' }}>Time (Latest
                            First)</option>
                        <option value="price_asc" {{ request('sort_by') == 'price_asc' ? 'selected' : '' }}>Price (Low to
                            High)</option>
                        <option value="price_desc" {{ request('sort_by') == 'price_desc' ? 'selected' : '' }}>Price (High to
                            Low)</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                </div>
            </div>
        </form>

        {{-- Delivery List --}}
        @if ($deliveries->isEmpty())
            <p>You have no deliveries.</p>
        @else
            @foreach ($deliveries as $delivery)
                <div class="card mb-3">
                    <div class="card-body">

                        @if($delivery->package)
                            <p><strong>Package Details:</strong></p>
                            <ul>
                                <li>Height: {{  $delivery->package->height }}</li>
                                <li>Width: {{  $delivery->package->width }}</li>
                                <li>Depth: {{  $delivery->package->depth }}</li>
                                <li>Weight: {{  $delivery->package->weight }} {{  $delivery->package->weight_unit }}</li>
                                <li>Measurement Unit: {{  $delivery->package->measurement_unit }}</li>
                                <li>Breakable: {{  $delivery->package->is_breakable ? 'Yes' : 'No' }}</li>
                                <li>Flammable: {{  $delivery->package->is_flammable ? 'Yes' : 'No' }}</li>
                                <li>Has Fluid: {{  $delivery->package->has_fluid ? 'Yes' : 'No' }}</li>
                                @if($delivery->package->picture)
                                    <li><img src="{{ asset('storage/' . $delivery->package->picture) }}" width="80" alt="Package Picture">
                                    </li>
                                @endif
                            </ul>
                        @else
                            <p><em>No package details available</em></p>
                        @endif
                        <p><strong>Status:</strong> {{ $delivery->status }}</p>

                        <p><strong>Takeoff Address:</strong><br>
                            {{ $delivery->takeOffAddress->street ?? 'N/A' }},
                            House #: {{ $delivery->takeOffAddress->house_number ?? 'N/A' }},
                            Building: {{ $delivery->takeOffAddress->building ?? 'N/A' }},
                            Zip Code: {{ $delivery->takeOffAddress->zip_code ?? 'N/A' }},
                            Coordinates: {{ $delivery->takeOffAddress->coordinates ?? 'N/A' }},
                            Floor: {{ $delivery->takeOffAddress->floor ?? 'N/A' }},
                            Type: {{ $delivery->takeOffAddress->type ?? 'N/A' }},
                            Region: {{ $delivery->takeOffAddress->region->region_name ?? 'N/A' }},
                            Latitude: {{ $delivery->takeOffAddress->latitude ?? 'N/A' }},
                            Longitude: {{ $delivery->takeOffAddress->longitude ?? 'N/A' }}
                        </p>

                        <p><strong>Dropoff Address:</strong><br>
                            {{ $delivery->dropOffAddress->street ?? 'N/A' }},
                            House #: {{ $delivery->dropOffAddress->house_number ?? 'N/A' }},
                            Building: {{ $delivery->dropOffAddress->building ?? 'N/A' }},
                            Zip Code: {{ $delivery->dropOffAddress->zip_code ?? 'N/A' }},
                            Coordinates: {{ $delivery->dropOffAddress->coordinates ?? 'N/A' }},
                            Floor: {{ $delivery->dropOffAddress->floor ?? 'N/A' }},
                            Type: {{ $delivery->dropOffAddress->type ?? 'N/A' }},
                            Region: {{ $delivery->dropOffAddress->region->region_name ?? 'N/A' }},
                            Latitude: {{ $delivery->dropOffAddress->latitude ?? 'N/A' }},
                            Longitude: {{ $delivery->dropOffAddress->longitude ?? 'N/A' }}
                        </p>

                        <p><strong>Cost:</strong> {{ $delivery->cost }} {{ $delivery->currency }}</p>
                        <p><strong>Scheduled at:</strong> {{ $delivery->scheduled_at ?? 'N/A' }}</p>
                        <p><strong>Scheduled at:</strong> {{ $delivery->scheduled_at ?? 'N/A' }}</p>

                        @if($delivery->status === 'completed')
                            <a href="{{ route('reviews.show', $delivery->id) }}" class="btn btn-success mt-3">
                                Leave a Review
                            </a>
                        @endif

                    </div>
                </div>
            @endforeach
        @endif
    </div>
@endsection