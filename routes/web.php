<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');

Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/hiz-testi', [\App\Http\Controllers\HomeController::class, 'speedTest'])->name('speed-test');
// Upload hız testi proxy — CORS sorununu önler
Route::post('/hiz-testi-upload', function (\Illuminate\Http\Request $request) {
    // Gelen veriyi oku ve at — sadece süreyi ölçmek için
    $request->getContent();
    return response()->json(['ok' => true]);
})->middleware('throttle:60,1')->name('speed-test.upload');
Route::get('/taahhut-sayaci', [\App\Http\Controllers\HomeController::class, 'commitmentCounter'])->name('commitment-counter');
Route::post('/taahhut-sayaci/hatirlatici', [\App\Http\Controllers\HomeController::class, 'commitmentReminderStore'])
    ->middleware('throttle:10,1')
    ->name('commitment-counter.reminder');
Route::post('/iletisim', [\App\Http\Controllers\ContactController::class, 'store'])->name('contact.store');

Route::get('/internet-paketleri', [\App\Http\Controllers\PackageController::class, 'index'])->name('packages.index');
Route::get('/internet-paketleri/{operatorSlug}', [\App\Http\Controllers\PackageController::class, 'index'])
    ->where('operatorSlug', '^(?!basvur|karsilastir)[a-z0-9-]+$')
    ->name('packages.operator');
Route::get('/internet-paketleri/{operatorSlug}/{infraSlug}', [\App\Http\Controllers\PackageController::class, 'index'])
    ->where(['operatorSlug' => '[a-z0-9-]+', 'infraSlug' => 'fiber|vdsl|adsl|fixed-wireless'])
    ->name('packages.operator_infra');
Route::get('/paket/{slug}', [\App\Http\Controllers\PackageController::class, 'show'])->name('packages.show');
Route::get('/internet-paketleri/{slug}/basvur', [\App\Http\Controllers\PackageController::class, 'apply'])->name('packages.apply');
Route::get('/paket/{slug}/basvur', [\App\Http\Controllers\PackageController::class, 'apply'])->name('packages.apply.short');
Route::post('/internet-paketleri/{slug}/basvur', [\App\Http\Controllers\PackageController::class, 'submitApplication'])->name('packages.apply.submit');

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
Route::get('/markalar', [\App\Http\Controllers\HomeController::class, 'operators'])->name('operators.index');
Route::get('/markalar/{slug}', [\App\Http\Controllers\HomeController::class, 'operatorDetail'])->name('operators.show');

Route::get('/blog', [\App\Http\Controllers\BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{categorySlug}', [\App\Http\Controllers\BlogController::class, 'categoryIndex'])->name('blog.category');
Route::get('/blog/{categorySlug}/{slug}', [\App\Http\Controllers\BlogController::class, 'show'])->name('blog.show');
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
// /internet-altyapi/{il} → /internet-tarifeleri/ucuz-{il}-ev-interneti-fiyatlari (301)
Route::get('/internet-altyapi/{city}', function (string $city) {
    return redirect()->to("/internet-tarifeleri/ucuz-{$city}-ev-interneti-fiyatlari", 301);
})->where('city', '[a-z0-9-]+')->name('location.city');

// /internet-altyapi/{il}/{ilçe} → /internet-tarifeleri/{il}/ucuz-{ilçe}-ev-interneti-fiyatlari (301)
Route::get('/internet-altyapi/{city}/{district}', function (string $city, string $district) {
    return redirect()->to("/internet-tarifeleri/{$city}/ucuz-{$district}-ev-interneti-fiyatlari", 301);
})->where(['city' => '[a-z0-9-]+', 'district' => '[a-z0-9-]+'])->name('location.district');
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
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class)
        ->except(['show']);
    // Yorum moderasyonu
    Route::get('reviews', [\App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('reviews.index');
    Route::post('reviews/{review}/approve', [\App\Http\Controllers\Admin\ReviewController::class, 'approve'])->name('reviews.approve');
    Route::delete('reviews/{review}', [\App\Http\Controllers\Admin\ReviewController::class, 'reject'])->name('reviews.reject');
    Route::post('reviews/bulk-approve', [\App\Http\Controllers\Admin\ReviewController::class, 'bulkApprove'])->name('reviews.bulk-approve');
    // Taahhüt hatırlatıcıları
    Route::get('commitment-reminders', [\App\Http\Controllers\Admin\CommitmentReminderController::class, 'index'])->name('commitment-reminders.index');
    Route::delete('commitment-reminders/{commitmentReminder}', [\App\Http\Controllers\Admin\CommitmentReminderController::class, 'destroy'])->name('commitment-reminders.destroy');
    // Site ayarları
    Route::get('site-settings', [\App\Http\Controllers\Admin\SiteSettingController::class, 'index'])->name('site-settings.index');
    Route::put('site-settings', [\App\Http\Controllers\Admin\SiteSettingController::class, 'update'])->name('site-settings.update');
    Route::resource('seo', \App\Http\Controllers\Admin\SeoContentController::class);
    // CSS / JS Editörü
    Route::get('custom-code', [\App\Http\Controllers\Admin\CustomCodeController::class, 'index'])
        ->name('custom-code.index');
    Route::put('custom-code/{key}', [\App\Http\Controllers\Admin\CustomCodeController::class, 'update'])
        ->where('key', '[a-z_]+')
        ->name('custom-code.update');
    Route::resource('tariff-seo', \App\Http\Controllers\Admin\TariffSeoController::class)
        ->except(['show'])
        ->parameters(['tariff-seo' => 'tariffSeo']);
    Route::resource('location-meta', \App\Http\Controllers\Admin\LocationMetaTemplateController::class)
        ->except(['show'])
        ->parameters(['location-meta' => 'locationMeta']);
    Route::post('location-meta/{locationMeta}/apply', [\App\Http\Controllers\Admin\LocationMetaTemplateController::class, 'apply'])
        ->name('location-meta.apply');
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
