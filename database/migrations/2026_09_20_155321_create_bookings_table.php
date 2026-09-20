<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique();
            $table->foreignId('customer_id')->constrained('users')->restrictOnDelete();
            $table->string('pickup_address');
            $table->decimal('pickup_lat', 10, 7)->nullable();
            $table->decimal('pickup_lng', 10, 7)->nullable();
            $table->string('destination_address');
            $table->decimal('destination_lat', 10, 7)->nullable();
            $table->decimal('destination_lng', 10, 7)->nullable();
            $table->date('booking_date');
            $table->time('booking_time');
            $table->unsignedTinyInteger('passengers');
            $table->unsignedTinyInteger('luggage_count')->default(0);
            $table->foreignId('vehicle_type_id')->constrained()->restrictOnDelete();
            $table->decimal('distance_km', 8, 2)->nullable();
            $table->unsignedInteger('estimated_duration_minutes')->nullable();
            $table->decimal('base_fare', 10, 2);
            $table->decimal('distance_fare', 10, 2);
            $table->decimal('time_fare', 10, 2);
            $table->decimal('extra_charge', 10, 2)->default(0);
            $table->decimal('total_fare', 10, 2);
            $table->string('status')->default('pending')->index();
            $table->text('customer_notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->string('payment_method')->default('pay_directly');
            $table->string('payment_status')->default('pending');
            $table->timestamps();
            $table->index(['customer_id', 'booking_date']);
            $table->index(['vehicle_type_id', 'booking_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
