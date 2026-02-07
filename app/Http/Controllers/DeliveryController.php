<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\User;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryController extends Controller
{
    // Display the user's own deliveries
public function userDeliveries(Request $request)
{
    $user = auth()->user();
    $client = Client::where('user_id', $user->id)->first();

    if (!$client) {
        return redirect()->back()->withErrors('Client not found.');
    }

    $query = Delivery::with(['takeOffAddress', 'dropOffAddress', 'package'])
        ->whereHas('package', function ($q) use ($client) {
            $q->where('client_id', $client->id);
        });

    // Apply status filter if provided
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Apply sorting
    if ($request->filled('sort_by')) {
        switch ($request->sort_by) {
            case 'time_asc':
                $query->orderBy('scheduled_at', 'asc');
                break;
            case 'time_desc':
                $query->orderBy('scheduled_at', 'desc');
                break;
            case 'price_asc':
                $query->orderBy('cost', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('cost', 'desc');
                break;
        }
    }

    $deliveries = $query->get();

    return view('deliveries.user', compact('deliveries'));
}

 public function driverDeliveries()
{
    $driver = \App\Models\Driver::where('email', auth()->user()->email)->first();

    $query = Delivery::where('drivers_id', $driver->id);

    // Filter by status if provided
    if ($status = request('status')) {
        $query->where('status', $status);
    }

    // Parse the sort_by parameter
    $sortBy = request('sort_by', '');

    // Default sort field and direction
    $sortField = 'created_at';
    $sortDirection = 'desc';

    // Map sort_by values to DB fields and directions
    $sortOptions = [
        'time_asc' => ['created_at', 'asc'],
        'time_desc' => ['created_at', 'desc'],
        'price_asc' => ['cost', 'asc'],
        'price_desc' => ['cost', 'desc'],
    ];

    if (isset($sortOptions[$sortBy])) {
        [$sortField, $sortDirection] = $sortOptions[$sortBy];
    }

    $deliveries = $query->orderBy($sortField, $sortDirection)->get();

    return view('deliveries.driver', compact('deliveries'));
}



    // Allow the driver to update the delivery status
   public function updateStatus(Request $request, $deliveryId)
{
    $request->validate([
        'status' => 'required|string|in:pending,on_my_way_to_pickup,on_my_way_to_dropoff,paid_awaiting_delivery,completed,cancelled',
    ]);

    $delivery = Delivery::findOrFail($deliveryId);
    $delivery->status = $request->status;
    $delivery->save();

    return redirect()->route('driver.deliveries')->with('success', 'Delivery status updated.');
}


    // Admin view for all deliveries
   public function adminDeliveries()
{
    $deliveries = Delivery::with(['driver', 'takeOfAddress', 'dropOfAddress', 'package'])->get(); // Eager load the necessary relationships
    return view('deliveries.admin', compact('deliveries'));
}

}