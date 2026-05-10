@extends('layouts.app')

@section('content')
<h2 class="mb-4">Available Requests</h2>

@if($requests->isEmpty())
    <p class="text-muted">No available requests found for this driver.</p>
@else
    <div class="row row-cols-1 row-cols-md-2 g-4">
        @foreach($requests as $request)
            <div class="col">
                <div class="card shadow-sm h-100 d-flex flex-column justify-content-between">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Request #{{ $request->id }}</h5>
                        <p class="card-text mb-1">
                               <p><strong>Scheduled At:</strong> {{ $request->scheduled_at ?? 'N/A' }}</p>

                        </p>
                        <p class="card-text mb-1">
                               <p><strong>Client Name:</strong> {{ $request->package->client->first_name .' '.$request->package->client->last_name?? 'N/A' }}</p>

                        </p>
                         <p class="card-text mb-1">
                               <p><strong>Client phone number:</strong> {{ $request->package->client->phone?? 'N/A' }}</p>

                        </p>
                             @if($request->package)
                        <p><strong>Package Details:</strong></p>
                        <ul>
                            <li>Height: {{ $request->package->height }}</li>
                            <li>Width: {{ $request->package->width }}</li>
                            <li>Depth: {{ $request->package->depth }}</li>
                            <li>Weight: {{ $request->package->weight }} {{ $request->package->weight_unit }}</li>
                            <li>Measurement Unit: {{ $request->package->measurement_unit }}</li>
                            <li>Breakable: {{ $request->package->is_breakable ? 'Yes' : 'No' }}</li>
                            <li>Flammable: {{ $request->package->is_flammable ? 'Yes' : 'No' }}</li>
                            <li>Has Fluid: {{ $request->package->has_fluid ? 'Yes' : 'No' }}</li>
                            @if($request->package->picture)
                                <li><img src="{{ asset('storage/' . $request->package->picture) }}" width="80" alt="Package Picture">
                                </li>
                            @endif
                        </ul>
                    @else
                        <p><em>No package details available</em></p>
                    @endif
                        <p class="card-text mb-3">
                             <p><strong>Takeoff Address:</strong>
                        {{ $request->takeofAddress->street ?? 'N/A' }},
                        House #: {{ $request->takeofAddress->house_number ?? 'N/A' }},
                        Building: {{ $request->takeofAddress->building ?? 'N/A' }},
                        Zip Code: {{ $request->takeofAddress->zip_code ?? 'N/A' }},
                        Coordinates: {{ $request->takeofAddress->coordinates ?? 'N/A' }},
                        Floor: {{ $request->takeofAddress->floor ?? 'N/A' }},
                        Type: {{ $request->takeofAddress->type ?? 'N/A' }},
                        Region: {{ $request->takeofAddress->region->region_name ?? 'N/A' }},
                        Latitude: {{ $request->takeofAddress->latitude ?? 'N/A' }},
                        Longitude: {{ $request->takeofAddress->longitude ?? 'N/A' }}
                    </p>
                    
                    <p><strong>Dropoff Address:</strong>
                        {{ $request->dropoffAddress->street ?? 'N/A' }},
                        House #: {{ $request->dropoffAddress->house_number ?? 'N/A' }},
                        Building: {{ $request->dropoffAddress->building ?? 'N/A' }},
                        Zip Code: {{ $request->dropoffAddress->zip_code ?? 'N/A' }},
                        Coordinates: {{ $request->dropoffAddress->coordinates ?? 'N/A' }},
                        Floor: {{ $request->dropoffAddress->floor ?? 'N/A' }},
                        Type: {{ $request->dropoffAddress->type ?? 'N/A' }},
                        Region: {{ $request->dropoffAddress->region->region_name ?? 'N/A' }},
                        Latitude: {{ $request->dropoffAddress->latitude ?? 'N/A' }},
                        Longitude: {{ $request->dropoffAddress->longitude ?? 'N/A' }}
                    </p>
                        </p>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('offers.make', ['id' => $request->id]) }}" class="btn btn-primary">Make Offer</a>

                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection