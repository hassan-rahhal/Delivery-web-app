<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\{
    AuthController,
    ClientProfileController,
    SocialAuthController,
    PackageController,
    DeliveryController,
    StripePaymentController,
    FindDriverRequestController,
    DriverOfferController,
    DriverController,
    AdminExportController,
    AdminPerformanceController,
    ReviewController,
    ClientMapController,
    AddressController
};

// Welcome Page
Route::get('/', function () {
    return view('WelcomeGuest');
})->name('welcomePage');

// Auth Routes
Route::middleware(['web'])->group(function () {
    Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('register', [AuthController::class, 'registerClient'])->name('register-submit');

    Route::get('register/Driver', [AuthController::class, 'showRegisterDriverForm'])->name('registerDriver');
    Route::post('register/Driver', [AuthController::class, 'registerDriver'])->name('registerDriver.submit');

    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.submit');

    Route::get('/verify-otp', [AuthController::class, 'showOTPForm'])->name('otp.form');
    Route::post('/verify-otp', [AuthController::class, 'verifyOTP'])->name('verify.otp');

    Route::get('/Social/verify-otp', [SocialAuthController::class, 'showOTPForm'])->name('otp.formSocial');
    Route::post('/Social/verify-otp', [SocialAuthController::class, 'verifyOTP'])->name('verify.otpSocial');

    Route::get('/set-password', [SocialAuthController::class, 'showSetPasswordForm'])->name('password.set.form');
    Route::post('/set-password', [SocialAuthController::class, 'submitSetPassword'])->name('password.set.submit');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Social Login
Route::get('/auth/google/redirect', [SocialAuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);
Route::get('/auth/github', [SocialAuthController::class, 'redirectToGithub'])->name('auth.github');
Route::get('/auth/callback/github', [SocialAuthController::class, 'handleGithubCallback'])->name('github.callback');

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', fn() => view('admin.dashboard'))->name('admin.dashboard');

    // Driver Approval
    Route::get('/drivers/pending', [DriverController::class, 'pending'])->name('admin.drivers.pending');
    Route::post('/drivers/{driver}/approve', [DriverController::class, 'approve'])->name('admin.drivers.approve');
    Route::post('/drivers/{driver}/reject', [DriverController::class, 'reject'])->name('admin.drivers.reject');

    // Exports
    Route::get('/drivers/export/excel', [AdminExportController::class, 'exportExcel'])->name('admin.drivers.export.excel');
    Route::get('/drivers/export/pdf', [AdminExportController::class, 'exportPDF'])->name('admin.drivers.export.pdf');

    // Performance
    Route::get('/driver-performance', [AdminPerformanceController::class, 'index'])->name('admin.driver.performance');
    Route::get('/driver-performance/{driver}', [AdminPerformanceController::class, 'show'])->name('admin.driver.performance.show');
    Route::get('/driver-performance/{driver}/pdf', [AdminPerformanceController::class, 'exportPDF'])->name('admin.driver.performance.pdf');
});

// Client Routes
Route::middleware(['auth', 'role:client'])->group(function () {
    Route::get('client-dashboard', [ClientProfileController::class, 'dashboard'])->name('client-dashboard');
    Route::get('/client/profile/{id}', [ClientProfileController::class, 'show'])->name('client.profile');
    Route::post('/client/{id}/upload-profile-image', [ClientProfileController::class, 'uploadProfileImage'])->name('client.uploadProfileImage');
    Route::put('/client/{id}/update', [ClientProfileController::class, 'update'])->name('client.update');
    Route::post('/client/{id}/social-media', [ClientProfileController::class, 'addSocialMediaAccount'])->name('client.addSocialMediaAccount');

    // Reviews
    Route::get('/deliveries/{id}/review', [ReviewController::class, 'showReview'])->name('reviews.show');
    Route::post('/deliveries/{id}/review', [ReviewController::class, 'store'])->name('reviews.store');

    // Map
    Route::get('/client-map/{clientId}', [ClientMapController::class, 'index']);
    Route::get('/available-drivers/{regionId}/{scheduledTime}', [ClientMapController::class, 'showAvailableDriversForRegionAndTime'])->name('available.drivers');

    // Deliveries
    Route::get('/deliveries/user', [DeliveryController::class, 'userDeliveries'])->name('deliveries.user');

    // Payment
    Route::get('/stripe', [StripePaymentController::class, 'stripe'])->name('stripe');
    Route::post('/stripe', [StripePaymentController::class, 'stripePost'])->name('stripe.post');
    Route::get('/pay/delivery/{delivery_id}', [StripePaymentController::class, 'stripe'])->name('pay.delivery');
    Route::post('/pay/delivery/{delivery_id}', [StripePaymentController::class, 'stripePost'])->name('pay.delivery.post');

    // Requests
    Route::get('/requests', [FindDriverRequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/create/{package_id}', [FindDriverRequestController::class, 'create'])->name('requests.create');
    Route::post('/requests', [FindDriverRequestController::class, 'store'])->name('requests.store');
    Route::get('/requests/{id}/offers', [FindDriverRequestController::class, 'showOffers'])->name('requests.offers');
    Route::post('/accept-offer/{offer}', [FindDriverRequestController::class, 'acceptOffer'])->name('acceptOffer');
    Route::delete('/requests/{id}', [FindDriverRequestController::class, 'destroy'])->name('requests.destroy');

    // Packages
    Route::resource('packages', PackageController::class);

    // Addresses
    Route::resource('addresses', AddressController::class);
});

// Driver Routes
Route::middleware(['auth', 'role:driver'])->group(function () {
    Route::get('driver-dashboard', [DriverController::class, 'dashboard'])->name('driver-dashboard');

    // Deliveries
    Route::get('/driver/deliveries', [DeliveryController::class, 'driverDeliveries'])->name('driver.deliveries');
    Route::post('/driver/delivery/{deliveryId}/update-status', [DeliveryController::class, 'updateStatus'])->name('driver.updateStatus');

    // Requests & Offers
    Route::get('/driver/requests', [DriverOfferController::class, 'showAvailableRequestsForDriver'])->name('driver.requests');
    Route::get('/driver/requests/{id}/offer', [DriverOfferController::class, 'makeOffer'])->name('offers.make');
    Route::post('/driver/offers/create', [DriverOfferController::class, 'createOffer'])->name('driver.offers.create');
    Route::get('/driver/offers', [DriverOfferController::class, 'listOffers'])->name('driver.offers.list');

    // Scheduling
    Route::get('availability', [DriverController::class, 'availabilityPage'])->name('driver.availability');
    Route::get('regions', [DriverController::class, 'regionsPage'])->name('driver.regions');
    Route::get('shifts', [DriverController::class, 'shiftsPage'])->name('driver.shifts');
    Route::put('/driver/{id}/shift', [DriverController::class, 'CreateShift'])->name('driver.CreateShift');
    Route::put('/driver/{id}/availability', [DriverController::class, 'updateAvailability'])->name('driver.updateAvailability');
    Route::put('/driver/{id}/region', [DriverController::class, 'updateRegion'])->name('driver.update.region');

    // Reviews
    Route::get('/reviews/{id}', [ReviewController::class, 'showDriverReviews'])->name('driver.reviews');

    // Drivers resource
    Route::resource('drivers', DriverController::class);
});

// Location Update
Route::middleware(['auth'])->group(function () {
    Route::get('/driver/update-location', [DriverController::class, 'showLocationUpdateForm'])->name('driver.location.form');
    Route::post('/update-driver-location', [DriverController::class, 'updateLocation'])->name('driver.location.update');
});

// Admin Deliveries
Route::get('/deliveries/admin', [DeliveryController::class, 'adminDeliveries'])->name('admin.deliveries');

// Authenticated User Info
Route::middleware('auth:sanctum')->get('/user', fn(Request $request) => $request->user());