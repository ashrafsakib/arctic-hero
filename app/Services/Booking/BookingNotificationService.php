<?php

namespace App\Services\Booking;

use App\Models\Booking;
use App\Notifications\BookingSubmitted;
use App\Services\CompanySettings;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class BookingNotificationService
{
    public function __construct(private readonly CompanySettings $settings) {}

    public function send(Booking $booking): bool
    {
        try {
            $booking->customer->notify(new BookingSubmitted($booking));
            Notification::route('mail', $this->settings->all()['email'])
                ->notify(new BookingSubmitted($booking, forAdmin: true));
        } catch (QueryException|TransportExceptionInterface $exception) {
            report($exception);
            Log::warning('A booking notification could not be delivered.', [
                'booking_id' => $booking->id,
            ]);

            return false;
        }

        return true;
    }
}
