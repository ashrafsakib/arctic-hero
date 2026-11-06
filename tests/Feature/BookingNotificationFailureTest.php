<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use App\Services\Booking\BookingNotificationService;
use App\Services\CompanySettings;
use Illuminate\Contracts\Notifications\Dispatcher;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Log;
use Mockery;
use Symfony\Component\Mailer\Exception\TransportException;
use Tests\TestCase;

class BookingNotificationFailureTest extends TestCase
{
    public function test_admin_mail_transport_failure_is_reported_without_reversing_customer_delivery(): void
    {
        $customer = Mockery::mock(User::class)->makePartial();
        $booking = new Booking(['id' => 42]);
        $booking->setRelation('customer', $customer);

        $settings = Mockery::mock(CompanySettings::class);
        $settings->shouldReceive('all')->once()->andReturn(['email' => 'service@example.com']);
        $this->app->instance(CompanySettings::class, $settings);

        $dispatcher = Mockery::mock(Dispatcher::class);
        $dispatcher->shouldReceive('send')
            ->once()
            ->with($customer, Mockery::type(\App\Notifications\BookingSubmitted::class));
        $dispatcher->shouldReceive('send')
            ->once()
            ->with(Mockery::type(AnonymousNotifiable::class), Mockery::type(\App\Notifications\BookingSubmitted::class))
            ->andThrow(new TransportException('SMTP is unavailable.'));
        Log::shouldReceive('error')->once();
        Log::shouldReceive('warning')->once();

        $this->assertFalse((new BookingNotificationService($settings, $dispatcher))->send($booking));
    }

    public function test_customer_notification_failure_is_reported_and_admin_delivery_is_not_attempted(): void
    {
        $customer = Mockery::mock(User::class)->makePartial();
        $booking = new Booking(['id' => 43]);
        $booking->setRelation('customer', $customer);

        $settings = Mockery::mock(CompanySettings::class);
        $settings->shouldNotReceive('all');
        $dispatcher = Mockery::mock(Dispatcher::class);
        $dispatcher->shouldReceive('send')
            ->once()
            ->with($customer, Mockery::type(\App\Notifications\BookingSubmitted::class))
            ->andThrow(new TransportException('SMTP is unavailable.'));
        Log::shouldReceive('error')->once();
        Log::shouldReceive('warning')->once();

        $this->assertFalse((new BookingNotificationService($settings, $dispatcher))->send($booking));
    }
}
