<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;
use App\Models\Driver;
use Carbon\Carbon;
use App\Models\Delivery;
use Illuminate\Support\Facades\DB;
use Hash;
use Auth;





class DriverController extends Controller
{

     public function dashboard()
    {
        $driver = Auth::user(); // assuming the driver is authenticated as 'user'

        // All deliveries assigned to this driver
        $myDeliveries = Delivery::where('id', $driver->id)->count();

        // Pending pickups: e.g., status = 'pending'
        $pendingPickups = Delivery::where('id', $driver->id)
            ->where('status', 'pending')
            ->count();

        // Completed deliveries: e.g., status = 'delivered'
        $completedDeliveries = Delivery::where('id', $driver->id)
            ->where('status', 'delivered')
            ->count();

        // Delivery issues (optional): e.g., status = 'failed' or 'problem'
        $deliveryIssues = Delivery::where('id', $driver->id)
            ->whereIn('status', ['failed', 'problem'])
            ->count();

        return view('drivers.welcome', [
            'myDeliveries' => $myDeliveries,
            'pendingPickups' => $pendingPickups,
            'completedDeliveries' => $completedDeliveries,
            'deliveryIssues' => $deliveryIssues,
        ]);
    }
    public function pending(Request $request)
    {
        $query = Driver::where('is_active', false);

        // Search
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        // Filter today's submissions
        if ($request->has('today') && $request->input('today') == '1') {
            $query->whereDate('created_at', Carbon::today());
        }

        $drivers = $query->orderBy('created_at', 'desc')->paginate(5);

        return view('admin.drivers.pending', compact('drivers'));
    }


    public function approve($id){
        $driver = Driver::findOrFail($id);
        $driver->is_active = true;
        $driver->save();
        return redirect()->route('admin.drivers.pending')->with('success', 'Driver approved successfully.');
    }

    public function reject($id){
        $driver = Driver::findOrFail($id);
        $driver->delete();

        return redirect()->route('admin.drivers.pending')->with('success', 'Driver rejected successfully.');
    }
    public function index()
    {
        $drivers = Driver::all(); // You can later use pagination
        return view('drivers.index', compact('drivers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('drivers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:drivers,email',
            'phone'      => 'nullable|string|max:20',
            'username'   => 'required|string|unique:drivers,username',
            'password'   => 'required|string|min:6',
            'age'        => 'nullable|integer|min:18',
            'plate_number' => 'required|string|max:20',
            'pricing_model' => 'required|in:number_of_rides,monthly',
            'is_active'  => 'required|boolean',
            'driving_license_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'national_id_image'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
    
        // Handle file uploads
        $drivingLicensePath = null;
        $nationalIdPath = null;
    
        if ($request->hasFile('driving_license_image')) {
            $drivingLicensePath = $request->file('driving_license_image')->store('drivers/licenses', 'public');
        }
    
        if ($request->hasFile('national_id_image')) {
            $nationalIdPath = $request->file('national_id_image')->store('drivers/national_ids', 'public');
        }
    
        // Create the driver
        $driver = Driver::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'username'   => $request->username,
            'password'   => bcrypt($request->password),
            'age'        => $request->age,
            'plate_number' => $request->plate_number,
            'pricing_model' => $request->pricing_model,
            'is_active'  => $request->is_active,
            'user_id'    => $request->user_id ?? null,
            'driving_license_path' => $drivingLicensePath,
            'national_id_path'     => $nationalIdPath,
        ]);
    
        return redirect()->route('drivers.index')->with('success', 'Driver created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $driver = Driver::with([
            'user',
            'address',
            'availabilities.getShiftAvailability',
            'availabilities.getRegionAvailability',
            'deliveries.package.client',
            'deliveries.reviews',
        ])->findOrFail($id);
    
        return view('drivers.show', compact('driver'));
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $driver = Driver::findOrFail($id);
        return view('drivers.edit', compact('driver'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $driver = Driver::findOrFail($id);
    
        $validated = $request->validate([
            'username' => 'required|string|max:255',
            'password' => 'nullable|string|min:6',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'age' => 'nullable|integer|min:18',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|unique:drivers,email,' . $driver->id,
            'driving_license' => 'required|string|unique:drivers,driving_license,' . $driver->id,
            'national_id' => 'required|string|unique:drivers,national_id,' . $driver->id,
            'plate_number' => 'required|string|unique:drivers,plate_number,' . $driver->id,
            'pricing_model' => 'required|string',
            'is_active' => 'boolean',
            'user_id' => 'required|exists:users,id',
        ]);
    
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']); // Don't update password
        }
    
        $driver->update($validated);
    
        return redirect()->route('drivers.index')->with('success', 'Driver updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $driver = Driver::findOrFail($id);
        $driver->delete();
    
        return redirect()->route('drivers.index')->with('success', 'Driver deleted successfully.');
    }


   public function availabilityPage() 
{
    $user = Auth::user();
    $driver = Driver::where('user_id', $user->id)->first();

    $shifts = Shift::all(); // Fetch all shifts to populate the dropdown

    return view('drivers.availability', compact('driver', 'shifts'));
}

    public function regionsPage()
    {
        return view('drivers.regions');
    }
    public function shiftsPage()
    {
         $driver = Driver::where('email', auth()->user()->email)->first();
        return view('drivers.shifts',compact('driver'));
    }

    public function updateRegion(Request $request, $id)
    {
        $driver = Driver::findOrFail($id);

        $request->validate([
            'region' => 'required|exists:regions,id',
        ]);

        $driver->region_id = $request->region;
        $driver->save();

        return back()->with('success', 'Region updated successfully.');
    }


 public function updateAvailability(Request $request, $id)
{
    $driver = Driver::findOrFail($id);

    $request->validate([
        'shift_id' => 'required|exists:shifts,id',
    ]);

    // You might want to prevent duplicate entries
    $existing = DB::table('availabilities')
        ->where('drivers_id', $driver->id)
        ->where('shifts_id', $request->shift_id)
        ->first();

    if (!$existing) {
        DB::table('availabilities')->insert([
            'drivers_id' => $driver->id,
            'shifts_id' => $request->shift_id,
            'set_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    return back()->with('success', 'Availability updated.');
}

  public function CreateShift(Request $request, $id)
{
    $driver = Driver::findOrFail($id);

    $request->validate([
        'mode' => 'required|in:daily,weekly',
    ]);

    $type = $request->mode;

    if ($type === 'weekly') {
        $weeklyData = $request->input('weekly', []);
        foreach ($weeklyData as $day => $times) {
            if (!empty($times['start']) && !empty($times['end'])) {
                Shift::create([
                    'day' => $day,
                    'starting_time' => $times['start'],
                    'end_time' => $times['end'],
                ]);
            }
        }
    } elseif ($type === 'daily') {
        $today = now()->format('l'); // E.g., 'Monday'
        $dailyData = $request->input('daily', []);
        Shift::create([
            'day' => $today,
            'starting_time' => $dailyData['start'] ?? null,
            'end_time' => $dailyData['end'] ?? null,
        ]);
    }

    return back()->with('success', 'Availability updated.');
}
    public function showLocationUpdateForm()
{
    return view('drivers.UpdateLocation');
}

public function updateLocation(Request $request)
{
    $request->validate([
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
    ]);

    $user = Auth::user();

    $driver = Driver::where('user_id', $user->id)->first();

    if (!$driver) {
        return response()->json(['message' => 'Driver not found.'], 404);
    }

    $driver->latitude = $request->latitude;
    $driver->longitude = $request->longitude;
    $driver->save();

    return response()->json(['message' => 'Location updated successfully.']);
}
}