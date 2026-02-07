<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Delivery;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminPerformanceController extends Controller
{
    public function index(Request $request)
    {
        // Date filtering
        //default is 30 days
        $startDate = $request->input('start_date') ?? Carbon::now()->subDays(30)->toDateString();
        $endDate = $request->input('end_date') ?? Carbon::now()->toDateString();

        // Get drivers with deliveries in the selected range
        //Eager-loads (with([...]) their:
            //Deliveries (filtered by date range)
            //All reviews (we'll filter those later in-memory)
        $drivers = Driver::where('is_active', true)
            ->with(['deliveries' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }, 'reviews'])
            ->get()
            ->map(function ($driver) use ($startDate, $endDate) { //Loops over each driver
                $deliveries = $driver->deliveries; //extract their deliveries
                $reviews = $driver->reviews->whereBetween('created_at', [$startDate, $endDate]);//and their reviews filtered by date

                return [//calculate metrics for each driver
                    'id' => $driver->id,
                    'name' => $driver->first_name,
                    'email' => $driver->email,
                    'plate_number' => $driver->plate_number,
                    'total_deliveries' => $deliveries->count(),
                    'total_earnings' => $deliveries->sum('cost'),
                    'average_rating' => round($reviews->avg('rating') ?? 0, 2),
                    'last_delivery' => $deliveries->max('created_at'),
                ];
            });

        // Sorting by earning or by rating
        if ($request->input('sort') === 'earnings') {
            $drivers = $drivers->sortByDesc('total_earnings');
        } elseif ($request->input('sort') === 'rating') {
            $drivers = $drivers->sortByDesc('average_rating');
        }

        // Top 5 this week (for display)
        $topDrivers = $drivers->take(5);

        return view('admin.performance.index', compact('drivers', 'topDrivers', 'startDate', 'endDate'));
    }

    public function show(Driver $driver)
    {
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        $deliveries = Delivery::where('drivers_id', $driver->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with([
                'package.client',
                'takeOffAddress.region',
                'dropOffAddress.region',
            ])
            ->get();

        $totalEarnings = $deliveries->sum('cost');
        $lastDelivery = $deliveries->max('created_at');
        $avgRating = Review::whereIn('deliveries_id', $deliveries->pluck('id'))->avg('rating');

        $chartLabels = [];
        $chartEarnings = [];
        $chartDeliveries = [];

        $grouped = $deliveries->groupBy(fn($d) => Carbon::parse($d->created_at)->format('Y-m-d'));

        foreach ($grouped as $date => $dList) {
            $chartLabels[] = $date;
            $chartEarnings[] = $dList->sum('cost');
            $chartDeliveries[] = $dList->count();
        }

        return view('admin.performance.show', compact(
            'driver', 'deliveries', 'totalEarnings', 'avgRating', 'lastDelivery',
            'chartLabels', 'chartEarnings', 'chartDeliveries'
        ));
    }

    public function exportPDF(Driver $driver)
    {
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        $deliveries = Delivery::where('drivers_id', $driver->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['package', 'takeOffAddress', 'dropOffAddress'])
            ->get();

        $totalEarnings = $deliveries->sum('cost');
        $lastDelivery = $deliveries->max('created_at');
        $avgRating = Review::whereIn('deliveries_id', $deliveries->pluck('id'))->avg('rating');

        $pdf = Pdf::loadView('admin.performance.pdf', compact('driver', 'deliveries', 'totalEarnings', 'avgRating', 'lastDelivery'));
        return $pdf->download("Driver_{$driver->id}_Performance.pdf");
    }


}
