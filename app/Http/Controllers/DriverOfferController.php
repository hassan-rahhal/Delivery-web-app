<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Driver;
use App\Models\Availability;
use App\Models\FindDriverRequest;

class DriverOfferController extends Controller
{
    /**
     * Show all available requests that match the logged-in driver's availability and regions.
     */
    public function showAvailableRequestsForDriver()
{
    $user = Auth::user();
    $driver = Driver::where('user_id', $user->id)->first();

    if (!$driver) {
        return redirect()->route('login')->withErrors('Driver profile not found.');
    }

    // Get ALL requests regardless of shift or region
    $requests = FindDriverRequest::with([
        'package.client',
        'takeofAddress.region',
        'dropoffAddress.region'
    ])->get();

    return view('drivers.requests', [
        'requests' => $requests
    ]);
}

    /**
     * Example method to create a new offer for a delivery request.
     * This assumes the offer data is passed in $request.
     */
   public function createOffer(Request $request)
{
    $user = Auth::user();
    $driver = Driver::where('user_id', $user->id)->first();

    if (!$driver) {
        return redirect()->route('login')->withErrors('Driver profile not found.');
    }

    $validated = $request->validate([
        'request_id' => 'required|exists:find_driver_requests,id',
        'price' => 'required|numeric',
        'currency' => 'required|string',
        'payment_method' => 'required|string',
    ]);

    $offer = new \App\Models\DriverOffer();
    $offer->driver_id = $driver->id;

    // Use the actual DB column name here
    $offer->find_driver_request_id = $validated['request_id'];

    $offer->price = $validated['price'];
    $offer->currency = $validated['currency'];
    $offer->payment_method = $validated['payment_method'];
    $offer->save();
   return  redirect()->route('driver.offers.list')->with('success', 'Payment successful and delivery status updated!');
}

    /**
     * Example method to list all offers by the logged-in driver.
     */
    public function listOffers()
    {
        $user = Auth::user();
        $driver = Driver::where('user_id', $user->id)->first();

        if (!$driver) {
            return redirect()->route('login')->withErrors('Driver profile not found.');
        }

          $offers = \App\Models\DriverOffer::with('request') // eager load the request relation
        ->where('driver_id', $driver->id)
        ->get();
        return view('drivers.offers', [
            'offers' => $offers,
        ]);
    }
public function makeOffer($requestId)
{
    $user = Auth::user();
    $driver = Driver::where('user_id', $user->id)->first();

    if (!$driver) {
        return redirect()->route('login')->withErrors('Driver profile not found.');
    }

    // Optional: load the request info to display in the form
    $requestData = \App\Models\FindDriverRequest::findOrFail($requestId);

    return view('offers.create', [
        'request' => $requestData,
        'driver' => $driver,
    ]);
}

    // Other controller methods as needed...
}