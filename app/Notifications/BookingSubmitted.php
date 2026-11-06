<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingSubmitted extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Booking $booking,
        private readonly bool $forAdmin = false,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return $notifiable instanceof AnonymousNotifiable
            ? ['mail']
            : ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $booking = $this->booking->loadMissing(['customer', 'vehicleType']);

        return (new MailMessage)
            ->subject('Booking received: '.$booking->booking_code)
            ->greeting('Booking received')
            ->line('Booking reference: '.$booking->booking_code)
            ->line('Customer: '.$booking->customer->name.' ('.$booking->customer->email.')')
            ->line('Pickup: '.$booking->pickup_address)
            ->line('Destination: '.$booking->destination_address)
            ->line('Vehicle: '.$booking->vehicleType->name)
            ->line('Pickup time: '.$booking->booking_date->format('M j, Y').' at '.$booking->booking_time)
            ->line('Estimated fare: €'.number_format((float) $booking->total_fare, 2))
            ->action(
                'View booking',
                $this->forAdmin
                    ? route('admin.bookings.show', $booking)
                    : route('bookings.show', $booking),
            );
    }

    /**
     * @return array<string, int|string>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'booking_code' => $this->booking->booking_code,
            'status' => $this->booking->status,
            'message' => 'Your booking request has been received.',
        ];
    }
}
