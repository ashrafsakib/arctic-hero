<?php

namespace Tests\Feature;

use App\Models\ContactSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminContactSubmissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_review_update_and_delete_contact_submissions(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $submission = $this->createSubmission();

        $this->actingAs($admin)
            ->get(route('admin.contact-submissions.index'))
            ->assertOk()
            ->assertSeeText('Contact inbox')
            ->assertSeeText('Aino Korhonen')
            ->assertSeeText('Booking');

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSeeText('New contact submissions');

        $this->get(route('admin.contact-submissions.show', $submission))
            ->assertOk()
            ->assertSeeText('Booking')
            ->assertSeeText('I need to update my airport pickup time.');

        $submission->refresh();
        $this->assertSame(ContactSubmission::READ, $submission->status);
        $this->assertNotNull($submission->read_at);

        $this->patch(route('admin.contact-submissions.update', $submission), ['status' => 'closed'])
            ->assertRedirect(route('admin.contact-submissions.show', $submission));
        $this->assertDatabaseHas('contact_submissions', [
            'id' => $submission->id,
            'status' => ContactSubmission::CLOSED,
        ]);

        $this->delete(route('admin.contact-submissions.destroy', $submission))
            ->assertRedirect(route('admin.contact-submissions.index'));
        $this->assertDatabaseMissing('contact_submissions', ['id' => $submission->id]);
    }

    public function test_only_active_admins_can_access_the_contact_inbox(): void
    {
        $this->get(route('admin.contact-submissions.index'))->assertRedirect(route('login'));

        $customer = User::factory()->create(['role' => 'customer', 'status' => 'active']);
        $this->actingAs($customer)->get(route('admin.contact-submissions.index'))->assertForbidden();

        $disabledAdmin = User::factory()->create(['role' => 'admin', 'status' => 'inactive']);
        $this->actingAs($disabledAdmin)->get(route('admin.contact-submissions.index'))->assertForbidden();
    }

    public function test_admin_can_filter_contact_submissions_by_status_and_topic(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $booking = $this->createSubmission(['topic' => 'booking']);
        $other = $this->createSubmission([
            'name' => 'Mika Example',
            'email' => 'mika@example.com',
            'topic' => 'vehicle',
            'status' => 'closed',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.contact-submissions.index', ['status' => 'new', 'topic' => 'booking']))
            ->assertOk()
            ->assertSeeText($booking->name)
            ->assertDontSeeText($other->name);
    }

    private function createSubmission(array $overrides = []): ContactSubmission
    {
        return ContactSubmission::query()->create(array_merge([
            'name' => 'Aino Korhonen',
            'email' => 'aino@example.com',
            'phone' => '+358 40 555 0000',
            'topic' => 'booking',
            'message' => 'I need to update my airport pickup time.',
            'status' => ContactSubmission::NEW,
            'email_status' => 'sent',
            'email_sent_at' => now(),
        ], $overrides));
    }
}