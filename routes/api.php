<?php

use App\Http\Controllers\Api\BlogApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\DoctorApiController;
use App\Http\Controllers\Api\GalleryApiController;
use App\Http\Controllers\Api\InquiryApiController;
use App\Http\Controllers\Api\LeadApiController;
use App\Http\Controllers\Api\MediaApiController;
use App\Http\Controllers\Api\ProfileApiController;
use App\Http\Controllers\Api\PublicContentApiController;
use App\Http\Controllers\Api\ServiceApiController;
use App\Http\Controllers\Api\SettingApiController;
use App\Http\Controllers\Api\TestimonialApiController;
use App\Http\Controllers\Api\VideoApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public API Routes (v1)
|--------------------------------------------------------------------------
*/
Route::prefix('v1')->group(function () {
    // Settings & Navigation Module
    Route::get('/settings/public', [SettingApiController::class, 'publicSettings']);
    Route::get('/navigation/{location?}', [SettingApiController::class, 'navigation']);

    // Catalog & Clinical Content Modules
    Route::get('/services', [ServiceApiController::class, 'index']);
    Route::get('/services/{slug}', [ServiceApiController::class, 'show']);
    Route::get('/team', [PublicContentApiController::class, 'team']);
    Route::get('/videos', [VideoApiController::class, 'index']);
    Route::get('/gallery', [GalleryApiController::class, 'publicGallery']);
    Route::get('/testimonials', [PublicContentApiController::class, 'testimonials']);
    Route::get('/faqs', [PublicContentApiController::class, 'faqs']);
    Route::get('/blog', [BlogApiController::class, 'publicIndex']);
    Route::get('/blog/{slug}', [BlogApiController::class, 'publicShow']);

    // Inquiries & Booking Forms with Rate Limiting
    Route::middleware('throttle:10,1')->group(function () {
        Route::post('/inquiries', [InquiryApiController::class, 'submitInquiry']);
        Route::post('/appointments', [InquiryApiController::class, 'bookAppointment']);
    });

    /*
    |--------------------------------------------------------------------------
    | Admin API Routes (v1) - Modular Dedicated Controllers
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->group(function () {
        // Executive Dashboard
        Route::get('/dashboard', [DashboardApiController::class, 'dashboard']);

        // Inquiries Management
        Route::get('/inquiries', [InquiryApiController::class, 'index']);
        Route::put('/inquiries/{inquiry}/status', [InquiryApiController::class, 'updateStatus']);
        Route::put('/inquiries/{inquiry}', [InquiryApiController::class, 'update']);
        Route::delete('/inquiries/{inquiry}', [InquiryApiController::class, 'destroy']);

        // Patient CRM Leads Pipeline
        Route::get('/leads', [LeadApiController::class, 'index']);
        Route::post('/leads', [LeadApiController::class, 'store']);
        Route::put('/leads/{lead}', [LeadApiController::class, 'update']);
        Route::delete('/leads/{lead}', [LeadApiController::class, 'destroy']);
        Route::post('/leads/{lead}/notes', [LeadApiController::class, 'addNote']);
        Route::post('/leads/{lead}/follow-ups', [LeadApiController::class, 'scheduleFollowUp']);

        // Dynamic Clinical Categories
        Route::get('/categories', [CategoryApiController::class, 'index']);
        Route::post('/categories', [CategoryApiController::class, 'store']);
        Route::put('/categories/{category}', [CategoryApiController::class, 'update']);
        Route::delete('/categories/{category}', [CategoryApiController::class, 'destroy']);

        // Treatments & Procedures Catalog
        Route::get('/services', [ServiceApiController::class, 'index']);
        Route::post('/services', [ServiceApiController::class, 'store']);
        Route::put('/services/{service}', [ServiceApiController::class, 'update']);
        Route::delete('/services/{service}', [ServiceApiController::class, 'destroy']);

        // Clinical Videos & Media Library
        Route::get('/videos', [VideoApiController::class, 'index']);
        Route::post('/videos', [VideoApiController::class, 'store']);
        Route::put('/videos/{video}', [VideoApiController::class, 'update']);
        Route::delete('/videos/{video}', [VideoApiController::class, 'destroy']);

        // Results Before & After Gallery
        Route::get('/gallery', [GalleryApiController::class, 'index']);
        Route::post('/gallery', [GalleryApiController::class, 'store']);
        Route::put('/gallery/{item}', [GalleryApiController::class, 'update']);
        Route::delete('/gallery/{item}', [GalleryApiController::class, 'destroy']);

        // Blog & Doctor Articles
        Route::get('/blogs', [BlogApiController::class, 'index']);
        Route::post('/blogs', [BlogApiController::class, 'store']);
        Route::put('/blogs/{post}', [BlogApiController::class, 'update']);
        Route::delete('/blogs/{post}', [BlogApiController::class, 'destroy']);

        // Patient Stories & Testimonials Module
        Route::get('/testimonials', [TestimonialApiController::class, 'index']);
        Route::post('/testimonials', [TestimonialApiController::class, 'store']);
        Route::get('/testimonials/{testimonial}', [TestimonialApiController::class, 'show']);
        Route::put('/testimonials/{testimonial}', [TestimonialApiController::class, 'update']);
        Route::delete('/testimonials/{testimonial}', [TestimonialApiController::class, 'destroy']);

        // Doctors, Specialists & Clinical Team Credentials
        Route::get('/doctors', [DoctorApiController::class, 'index']);
        Route::post('/doctors', [DoctorApiController::class, 'store']);
        Route::get('/doctors/{doctor}', [DoctorApiController::class, 'show']);
        Route::put('/doctors/{doctor}', [DoctorApiController::class, 'update']);
        Route::delete('/doctors/{doctor}', [DoctorApiController::class, 'destroy']);

        // Frequently Asked Questions (FAQs) Module
        Route::apiResource('faqs', \App\Http\Controllers\Api\FaqApiController::class);

        // Clinic Site Settings & Branding Assets
        Route::get('/settings', [SettingApiController::class, 'index']);
        Route::post('/settings', [SettingApiController::class, 'update']);

        // Administrator Profile & Security
        Route::get('/profile', [ProfileApiController::class, 'show']);
        Route::post('/profile', [ProfileApiController::class, 'update']);

        // Media Library & File Uploads
        Route::get('/media', [MediaApiController::class, 'index']);
        Route::post('/media/upload', [MediaApiController::class, 'upload']);
    });
});

