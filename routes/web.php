<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/hiz-testi', [\App\Http\Controllers\HomeController::class, 'speedTest'])->name('speed-test');
Route::get('/taahhut-sayaci', [\App\Http\Controllers\HomeController::class, 'commitmentCounter'])->name('commitment-counter');
Route::post('/iletisim', [\App\Http\Controllers\ContactController::class, 'store'])->name('contact.store');

Route::get('/internet-paketleri', [\App\Http\Controllers\PackageController::class, 'index'])->name('packages.index');
Route::get('/internet-paketleri/{slug}', [\App\Http\Controllers\PackageController::class, 'show'])->name('packages.show');

// İl/ilçe bazlı tarife sayfaları
// /internet-tarifeleri/ucuz-istanbul-ev-interneti-fiyatlari
Route::get('/internet-tarifeleri/{urlSlug}', [\App\Http\Controllers\TariffController::class, 'city'])
    ->where('urlSlug', 'ucuz-[a-z0-9-]+-ev-interneti-fiyatlari')
    ->name('tariffs.city');
// /internet-tarifeleri/istanbul/ucuz-pendik-ev-interneti-fiyatlari
Route::get('/internet-tarifeleri/{citySlug}/{urlSlug}', [\App\Http\Controllers\TariffController::class, 'district'])
    ->where(['citySlug' => '[a-z0-9-]+', 'urlSlug' => 'ucuz-[a-z0-9-]+-ev-interneti-fiyatlari'])
    ->name('tariffs.district');
Route::post('/internet-paketleri/{slug}/yorum', [\App\Http\Controllers\PackageReviewController::class, 'store'])->name('packages.reviews.store');
Route::get('/karsilastir', [\App\Http\Controllers\CompareController::class, 'index'])->name('compare');
Route::get('/operatorler', [\App\Http\Controllers\HomeController::class, 'operators'])->name('operators.index');
Route::get('/operatorler/{slug}', [\App\Http\Controllers\HomeController::class, 'operatorDetail'])->name('operators.show');

Route::get('/blog', [\App\Http\Controllers\BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [\App\Http\Controllers\BlogController::class, 'show'])->name('blog.show');
Route::middleware('throttle:120,1')->prefix('api/adres')->group(function () {
    Route::get('mahalleler/{city}/{district}', [\App\Http\Controllers\LocationController::class, 'neighborhoods'])
        ->where(['city' => '[a-z0-9-]+', 'district' => '[a-z0-9-]+'])
        ->name('location.neighborhoods');
    Route::get('sokaklar/{mahalleKod}', [\App\Http\Controllers\LocationController::class, 'streets'])
        ->where(['mahalleKod' => '[0-9]+'])
        ->name('location.streets');
    Route::get('binalar/{csbmKod}', [\App\Http\Controllers\LocationController::class, 'buildings'])
        ->where(['csbmKod' => '[0-9]+'])
        ->name('location.buildings');
    Route::get('daireler/{binaKodu}', [\App\Http\Controllers\LocationController::class, 'doors'])
        ->where(['binaKodu' => '[0-9]+'])
        ->name('location.doors');
});
Route::get('/internet-altyapi/{city}', [\App\Http\Controllers\LocationController::class, 'city'])->name('location.city');
Route::get('/internet-altyapi/{city}/{district}', [\App\Http\Controllers\LocationController::class, 'district'])->name('location.district');
Route::post('/altyapi-sorgu', [\App\Http\Controllers\LocationController::class, 'lookup'])
    ->middleware('throttle:60,1')
    ->name('location.lookup');
Route::post('/altyapi-basvuru', [\App\Http\Controllers\LocationController::class, 'submitLead'])
    ->middleware('throttle:20,1')
    ->name('location.lead');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('operators', \App\Http\Controllers\Admin\OperatorController::class);
    Route::resource('packages', \App\Http\Controllers\Admin\InternetPackageController::class);
    Route::resource('sponsors', \App\Http\Controllers\Admin\SponsorController::class);
    Route::resource('blogs', \App\Http\Controllers\Admin\BlogController::class);
    Route::resource('faqs', \App\Http\Controllers\Admin\FaqController::class);
    Route::resource('seo', \App\Http\Controllers\Admin\SeoContentController::class);
    Route::resource('tariff-seo', \App\Http\Controllers\Admin\TariffSeoController::class)
        ->except(['show'])
        ->parameters(['tariff-seo' => 'tariffSeo']);
    Route::resource('infrastructure', \App\Http\Controllers\Admin\InfrastructureStatusController::class)
        ->except(['show']);
    Route::get('infrastructure-leads', [\App\Http\Controllers\Admin\InfrastructureLeadController::class, 'index'])
        ->name('infrastructure-leads.index');
    Route::get('infrastructure-leads/{lead}', [\App\Http\Controllers\Admin\InfrastructureLeadController::class, 'show'])
        ->name('infrastructure-leads.show');
    Route::patch('infrastructure-leads/{lead}', [\App\Http\Controllers\Admin\InfrastructureLeadController::class, 'update'])
        ->name('infrastructure-leads.update');
    Route::delete('infrastructure-leads/{lead}', [\App\Http\Controllers\Admin\InfrastructureLeadController::class, 'destroy'])
        ->name('infrastructure-leads.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
