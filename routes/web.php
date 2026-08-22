<?php

use App\Http\Controllers\Web\AboutController;
use App\Http\Controllers\Web\AdminWebController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\BlogWebController;
use App\Http\Controllers\Web\ContactWebController;
use App\Http\Controllers\Web\GalleryWebController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\SeoWebController;
use App\Http\Controllers\Web\ServiceWebController;
use App\Http\Controllers\Web\VideoWebController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Web Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/services', [ServiceWebController::class, 'index'])->name('services.index');
Route::get('/services/{slug}', [ServiceWebController::class, 'show'])->name('services.show');
Route::get('/videos', [VideoWebController::class, 'index'])->name('videos.index');
Route::get('/gallery', [GalleryWebController::class, 'index'])->name('gallery.index');
Route::get('/blog', [BlogWebController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogWebController::class, 'show'])->name('blog.show');
Route::get('/contact', [ContactWebController::class, 'index'])->name('contact');

// SEO Routes
Route::get('/sitemap.xml', [SeoWebController::class, 'sitemap'])->name('seo.sitemap');
Route::get('/robots.txt', [SeoWebController::class, 'robots'])->name('seo.robots');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Admin Portal Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminWebController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard', [AdminWebController::class, 'dashboard'])->name('dashboard.index');
    Route::get('/inquiries', [AdminWebController::class, 'inquiries'])->name('inquiries');
    Route::get('/leads', [AdminWebController::class, 'leads'])->name('leads');
    Route::get('/categories', [AdminWebController::class, 'categories'])->name('categories');
    Route::get('/services', [AdminWebController::class, 'services'])->name('services');
    Route::get('/videos', [AdminWebController::class, 'videos'])->name('videos');
    Route::get('/gallery', [AdminWebController::class, 'gallery'])->name('gallery');
    Route::get('/blogs', [AdminWebController::class, 'blogs'])->name('blogs');
    Route::get('/testimonials', [AdminWebController::class, 'testimonials'])->name('testimonials');
    Route::get('/doctors', [AdminWebController::class, 'doctors'])->name('doctors');
    Route::get('/faqs', [AdminWebController::class, 'faqs'])->name('faqs');
    Route::get('/about', [AdminWebController::class, 'aboutPage'])->name('about');
    Route::get('/settings', [AdminWebController::class, 'settings'])->name('settings');
    Route::get('/profile', [AdminWebController::class, 'profile'])->name('profile');
    Route::post('/profile', [AdminWebController::class, 'updateProfile'])->name('profile.update');
    Route::get('/seo', [AdminWebController::class, 'seo'])->name('seo');
    Route::get('/seo/get', [AdminWebController::class, 'getSeoMeta'])->name('seo.get');
    Route::post('/seo/save', [AdminWebController::class, 'saveSeoMeta'])->name('seo.save');
    Route::post('/seo/global', [AdminWebController::class, 'saveGlobalSeo'])->name('seo.global');
});
