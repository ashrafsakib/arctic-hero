<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ContactSubmissionController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FleetController;
use App\Http\Controllers\Admin\VehicleController;
use App\Models\Booking;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/contact', [ContactController::class, 'create'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.send');
Route::get('/fleet', [FleetController::class, 'index'])->name('fleet.index');
Route::get('/booking', [BookingController::class, 'create'])->name('booking.create');
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
Route::get('/booking/complete', [BookingController::class, 'complete'])->middleware('auth')->name('booking.complete');
Route::get('/booking/{booking}/confirmation', [BookingController::class, 'confirmation'])->middleware('auth')->name('booking.confirmation');

Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }

    $upcomingQuery = $user->bookings()
        ->whereDate('booking_date', '>=', today())
        ->whereNotIn('status', [Booking::COMPLETED, Booking::CANCELLED, Booking::REJECTED]);

    $upcomingCount = (clone $upcomingQuery)->count();
    $upcomingBookings = (clone $upcomingQuery)
        ->with('vehicleType')
        ->orderBy('booking_date')
        ->orderBy('booking_time')
        ->limit(5)
        ->get();
    $completedTrips = $user->bookings()->where('status', Booking::COMPLETED)->count();
    $totalTrips = $user->bookings()->count();
    $firstName = explode(' ', trim($user->name))[0] ?? $user->name;

    return view('dashboard', compact('user', 'firstName', 'upcomingBookings', 'upcomingCount', 'completedTrips', 'totalTrips'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::get('/contact-submissions', [ContactSubmissionController::class, 'index'])->name('contact-submissions.index');
    Route::get('/contact-submissions/{contactSubmission}', [ContactSubmissionController::class, 'show'])->name('contact-submissions.show');
    Route::patch('/contact-submissions/{contactSubmission}', [ContactSubmissionController::class, 'update'])->name('contact-submissions.update');
    Route::delete('/contact-submissions/{contactSubmission}', [ContactSubmissionController::class, 'destroy'])->name('contact-submissions.destroy');
    Route::resource('vehicles', VehicleController::class);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
