<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Address;
use App\Models\Delivery;
use App\Models\Package;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\FindDriverRequest;
use App\Models\Social_Media_Account;
use App\Models\Social_Media_Provider;


class ClientProfileController extends Controller
{
    public function addSocialMediaAccount(Request $request, $id)
    {
        $request->validate([
            'provider_id' => 'required|exists:social__media__providers,id',
            'account_name' => 'required|string',
            'profile_url' => 'nullable|url',
        ]);

        $client = Client::findOrFail($id);

        $client->socialMediaAccounts()->create([
            'social_media_providers_id' => $request->provider_id,
            'account_name' => $request->account_name,
            'profile_url' => $request->profile_url,
        ]);

        return redirect()->back()->with('success_social', 'Social media account added successfully!');
    }
    public function dashboard()
    {
        $client = Client::where('email', auth()->user()->email)->first();
        $totalDeliveries = Delivery::whereHas('package', function ($q) use ($client) {
            $q->where('client_id', $client->id);
        })->count();
        $totalRequests = FindDriverRequest::whereHas('package', function ($query) use ($client) {
            $query->where('client_id', $client->id);
        })->count();
        return view('client.welcome', [
            'packagesInTransit' => $client->packages()->count(),
            'addressCount' => $client->addresses()->count(),
            'deliveriesCount' => $totalDeliveries,
            'requestsCount' => $totalRequests,
            'client' => $client,
        ]);
    }

    public function show($clientId)
    {
        \Log::info("Client Profile ID: $clientId"); // Log the client ID
        $client = Client::with(['reviews'])->findOrFail($clientId);

        return view('client.clientprofile', compact('client'));
    }
    public function uploadProfileImage(Request $request, $id)
    {
        $request->validate([
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $client = Client::findOrFail($id);

        // Delete old image if exists
        if ($client->profile_image && Storage::exists($client->profile_image)) {
            Storage::delete($client->profile_image);
        }

        $path = $request->file('profile_image')->store('profile_images', 'public');
        $client->profile_image = $path;
        $client->save();

        return redirect()->route('client.profile', ['id' => $id])->with('success', 'Profile image updated!');
    }


    // Update the client profile
    public function update(Request $request, $clientId)
    {
        $client = Client::findOrFail($clientId);

        // Validate the incoming request data
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'premium_level' => 'nullable|string|max:50',
            'user_name' => 'nullable|string|max:255',
            // You can add other validation rules as per your requirements
        ]);

        // Update the client data
        $client->update($validated);

        return back()->with('success', 'Profile updated!');
    }

    // Add or update client address
    public function addAddress(Request $request, $clientId)
    {
        $client = Client::findOrFail($clientId);

        // Validate the incoming address data
        $validated = $request->validate([
            'address' => 'required|string|max:255',
        ]);

        // Create or attach address to the client
        $address = Address::create($validated);
        $client->getAddress()->attach($address);

        return response()->json([
            'message' => 'Address added successfully!',
            'address' => $address
        ]);
    }

}
