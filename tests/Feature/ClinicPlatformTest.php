<?php

namespace Tests\Feature;

use App\Jobs\SendAppointmentNotificationJob;
use App\Jobs\SendAppointmentThankYouJob;
use App\Jobs\SendInquiryNotificationJob;
use App\Jobs\SendInquiryThankYouJob;
use App\Models\Inquiry;
use App\Models\Lead;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ClinicPlatformTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_home_page_renders_successfully(): void
    {
        $this->get('/')->assertStatus(200)->assertSee('Lumique', false);
    }

    public function test_about_page_renders_successfully(): void
    {
        $this->get('/about')->assertStatus(200)->assertSee('Dr. Alisha Vance', false);
    }

    public function test_services_page_renders_successfully(): void
    {
        $this->get('/services')->assertStatus(200)->assertSee('HydraFacial', false);
    }

    public function test_videos_page_renders_successfully(): void
    {
        $this->get('/videos')->assertStatus(200)->assertSee('Video Library', false);
    }

    public function test_gallery_page_renders_successfully(): void
    {
        $this->get('/gallery')->assertStatus(200)->assertSee('Transformations Gallery', false);
    }

    public function test_blog_page_renders_successfully(): void
    {
        $this->get('/blog')->assertStatus(200)->assertSee('Hyaluronic Acid', false);
    }

    public function test_contact_page_renders_successfully(): void
    {
        $this->get('/contact')->assertStatus(200)->assertSee('Linking Road', false);
    }

    public function test_seo_sitemap_and_robots(): void
    {
        $this->get('/sitemap.xml')->assertStatus(200);
        $this->get('/robots.txt')->assertStatus(200)->assertSee('Sitemap:', false);
    }

    public function test_public_api_endpoints_return_json_data(): void
    {
        $response = $this->getJson('/api/v1/services');
        $response->assertStatus(200)
                 ->assertJsonStructure(['success', 'data']);

        $teamRes = $this->getJson('/api/v1/team');
        $teamRes->assertStatus(200)
                ->assertJsonFragment(['name' => 'Dr. Alisha Vance, MD, DVD']);
    }

    public function test_inquiry_submission_creates_record_lead_and_dispatches_dual_emails(): void
    {
        Queue::fake();

        $payload = [
            'name' => 'Anjali Mehta',
            'email' => 'anjali.mehta@example.com',
            'phone' => '+91 99887 76655',
            'subject' => 'Acne Treatment Inquiry',
            'message' => 'Interested in chemical peels and HydraFacial packages.',
        ];

        $response = $this->postJson('/api/v1/inquiries', $payload);

        $response->assertStatus(201)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('inquiries', [
            'email' => 'anjali.mehta@example.com',
            'type' => 'contact',
        ]);

        $this->assertDatabaseHas('leads', [
            'email' => 'anjali.mehta@example.com',
            'status' => 'new',
        ]);

        Queue::assertPushed(SendInquiryNotificationJob::class);
        Queue::assertPushed(SendInquiryThankYouJob::class);
    }

    public function test_appointment_submission_creates_record_and_dispatches_appointment_emails(): void
    {
        Queue::fake();

        $payload = [
            'name' => 'Karan Singhania',
            'email' => 'karan.s@example.com',
            'phone' => '+91 98765 43210',
            'service_name' => 'Advanced PRP / GFC Hair Restoration',
            'preferred_date' => now()->addDays(2)->toDateString(),
            'preferred_time' => 'Morning (10:00 AM – 1:00 PM)',
            'message' => 'Hair loss consultation request.',
        ];

        $response = $this->postJson('/api/v1/appointments', $payload);

        $response->assertStatus(201)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('inquiries', [
            'email' => 'karan.s@example.com',
            'type' => 'appointment',
        ]);

        Queue::assertPushed(SendAppointmentNotificationJob::class);
        Queue::assertPushed(SendAppointmentThankYouJob::class);
    }

    public function test_admin_dashboard_and_settings_apis(): void
    {
        $dashboardRes = $this->getJson('/api/v1/admin/dashboard');
        $dashboardRes->assertStatus(200)
                     ->assertJsonStructure(['success', 'data' => ['total_inquiries', 'total_leads']]);

        $settingsUpdate = $this->postJson('/api/v1/admin/settings', [
            'site_name' => 'Lumique Aesthetic Clinic Mumbai',
        ]);
        $settingsUpdate->assertStatus(200);

        $this->assertEquals('Lumique Aesthetic Clinic Mumbai', SiteSetting::get('site_name'));
    }
}
