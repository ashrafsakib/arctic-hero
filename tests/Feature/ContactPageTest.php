<?php

namespace Tests\Feature;

use App\Mail\ContactMessage;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactPageTest extends TestCase
{
    public function test_contact_page_shows_company_contact_details_and_message_form(): void
    {
        $this->get(route('contact.index'))
            ->assertOk()
            ->assertSeeText('Let’s talk')
            ->assertSeeText(config('company.phone'))
            ->assertSeeText(config('company.email'))
            ->assertSee('action="'.route('contact.send').'"', false)
            ->assertSee('aria-current="page"', false);
    }

    public function test_valid_contact_message_is_sent_to_company_inbox_with_visitor_reply_to(): void
    {
        Mail::fake();

        $this->post(route('contact.send'), [
            'name' => 'Aino Korhonen',
            'email' => 'aino@example.com',
            'phone' => '+358 40 555 0000',
            'topic' => 'booking',
            'message' => 'I have a question about my airport pickup.',
            'website' => '',
        ])->assertRedirect(route('contact.index'))
            ->assertSessionHas('contact_sent', true);

        Mail::assertSent(ContactMessage::class, function (ContactMessage $mail): bool {
            return $mail->hasTo(config('company.email'))
                && $mail->hasReplyTo('aino@example.com')
                && $mail->details['name'] === 'Aino Korhonen'
                && $mail->details['topic'] === 'booking';
        });

        $this->get(route('contact.index'))
            ->assertOk()
            ->assertSeeText('Message received.');
    }

    public function test_invalid_contact_message_is_not_sent(): void
    {
        Mail::fake();

        $this->from(route('contact.index'))
            ->post(route('contact.send'), [
                'name' => 'Aino Korhonen',
                'email' => 'not-an-email',
                'topic' => 'unknown',
                'message' => 'Short',
            ])
            ->assertSessionHasErrors(['email', 'topic', 'message']);

        Mail::assertNothingSent();
    }
}