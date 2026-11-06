<?php

namespace App\Services\Booking;

use App\Models\Booking;
use App\Notifications\BookingSubmitted;
use App\Services\CompanySettings;
use Illuminate\Contracts\Notifications\Dispatcher;
use Illuminate\Database\QueryException;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class BookingNotificationService
{
    public function __construct(
        private readonly CompanySettings $settings,
        private readonly Dispatcher $notifications,
    ) {}

    public function send(Booking $booking): bool
    {
        try {
            $this->notifications->send($booking->customer, new BookingSubmitted($booking));
            $adminRecipient = (new AnonymousNotifiable)->route('mail', $this->settings->all()['email']);
            $this->notifications->send($adminRecipient, new BookingSubmitted($booking, forAdmin: true));
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
