@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Your Delivery Offers</h2>

    @if ($offers->isEmpty())
        <p>You have not submitted any offers yet.</p>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Offer ID</th>
                    <th>Package Details</th>
                    <th>Pick-up Address</th>
                    <th>Drop-off Address</th>
                    <th>Scheduled At</th>
                    <th>Price</th>
                    <th>Currency</th>
                    <th>Payment Method</th>
                    <th>Submitted At</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($offers as $offer)
                    <tr>
                        <td>{{ $offer->id }}</td>

                        <td>
                            @php
                                $pkg = $offer->request->package;
                            @endphp

                            @if($pkg)
                                Dimensions: {{ $pkg->height }} × {{ $pkg->width }} × {{ $pkg->depth }} {{ $pkg->measurement_unit ?? '' }} <br>
                                Weight: {{ $pkg->weight }} {{ $pkg->weight_unit ?? '' }} <br>
                                Breakable: {{ $pkg->is_breakable ? 'Yes' : 'No' }} <br>
                                Flammable: {{ $pkg->is_flammable ? 'Yes' : 'No' }} <br>
                                Has Fluid: {{ $pkg->has_fluid ? 'Yes' : 'No' }}
                            @else
                                N/A
                            @endif
                        </td>

                        <td>
                            {{ $offer->request->takeofAddress->street ?? 'N/A' }},
                            House #: {{ $offer->request->takeofAddress->house_number ?? 'N/A' }},
                            Building: {{ $offer->request->takeofAddress->building ?? 'N/A' }},
                            Region: {{ $offer->request->takeofAddress->region->region_name ?? 'N/A' }}
                        </td>

                        <td>
                            {{ $offer->request->dropoffAddress->street ?? 'N/A' }},
                            House #: {{ $offer->request->dropoffAddress->house_number ?? 'N/A' }},
                            Building: {{ $offer->request->dropoffAddress->building ?? 'N/A' }},
                            Region: {{ $offer->request->dropoffAddress->region->region_name ?? 'N/A' }}
                        </td>

                        <td>{{ $offer->request->scheduled_at ? \Carbon\Carbon::parse($offer->request->scheduled_at)->format('Y-m-d H:i') : 'N/A' }}</td>

                        <td>{{ $offer->price }}</td>
                        <td>{{ strtoupper($offer->currency) }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $offer->payment_method)) }}</td>
                        <td>{{ $offer->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection