<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $driver->first_name }}'s Performance</title>
    <link href="{{ asset('https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>
<body>
<div class="container py-5">
    <h2 class="mb-4">{{ $driver->first_name }}'s Performance (Last 30 Days)</h2>

    <div class="card p-4 mb-4">
        <h5>Driver Info</h5>
        <p><strong>Name:</strong> {{ $driver->first_name }} {{ $driver->last_name }}</p>
        <p><strong>Email:</strong> {{ $driver->email }}</p>
        <p><strong>Plate Number:</strong> {{ $driver->plate_number }}</p>
        <p><strong>Pricing Model:</strong> {{ $driver->pricing_model }}</p>
    </div>

    <div class="card p-4 mb-4">
        <h5>Performance Summary</h5>
        <p><strong>Total Deliveries:</strong> {{ $deliveries->count() }}</p>
        <p><strong>Total Earnings:</strong> ${{ number_format($totalEarnings, 2) }}</p>
        <p><strong>Average Rating:</strong> {{ number_format($avgRating, 2) }}/5 ⭐</p>
        <p><strong>Last Delivery:</strong> {{ $lastDelivery ? \Carbon\Carbon::parse($lastDelivery)->format('Y-m-d H:i') : 'N/A' }}</p>
    </div>
    <div class="card p-4 mt-4" style="max-width: 800px; margin: 0 auto;">
        <h5 class="text-center">Earnings & Delivery Trends (Last 30 Days)</h5>
        <canvas id="performanceChart" height="600"></canvas>
    </div>

    <script>
        const ctx = document.getElementById('performanceChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [
                    {
                        label: 'Earnings ($)',
                        data: {!! json_encode($chartEarnings) !!},
                        borderColor: 'green',
                        yAxisID: 'yEarnings',
                        borderWidth: 2,
                        fill: false
                    },
                    {
                        label: 'Deliveries',
                        data: {!! json_encode($chartDeliveries) !!},
                        borderColor: 'blue',
                        yAxisID: 'yDeliveries',
                        borderWidth: 2,
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                scales: {
                    yEarnings: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Earnings ($)'
                        },
                        beginAtZero: true
                    },
                    yDeliveries: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false
                        },
                        title: {
                            display: true,
                            text: 'Deliveries'
                        },
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>

    <div class="card p-4">
        <h5>Deliveries (Last 30 Days)</h5>
        @if($deliveries->isEmpty())
            <p>No deliveries found.</p>
        @else
            <a href="{{ route('admin.driver.performance.pdf', $driver->id) }}" class="btn btn-outline-danger mb-4">📄 Export PDF</a>

            <table class="table table-bordered mt-3">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Cost</th>
                    <th>Pickup</th>
                    <th>Dropoff</th>
                    <th>Weight</th>
                    <th>Flags</th>
                    <th>Date</th>
                </tr>
                </thead>
                <tbody>
                @foreach($deliveries as $d)
                    <tr>
                        <td>{{ $d->id }}</td>
                        <td>{{ $d->package->client->first_name ?? 'N/A' }}</td>
                        <td>${{ $d->cost }}</td>
                        <td>{{ $d->takeOffAddress->region->city ?? 'N/A' }}</td>
                        <td>{{ $d->dropOffAddress->region->city ?? 'N/A' }}</td>
                        <td>{{ $d->package->weight ?? 'N/A' }} {{ $d->package->weight_unit ?? '' }}</td>
                        <td>
                            @if($d->package?->is_breakable) 🧱 Fragile @endif
                            @if($d->package?->is_flammable) 🔥 Flammable @endif
                            @if($d->package?->has_fluid) 💧 Fluid @endif
                        </td>
                        <td>{{ $d->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <a href="{{ route('admin.driver.performance') }}" class="btn btn-secondary mt-4">⬅ Back to Dashboard</a>
</div>

</body>
</html>
