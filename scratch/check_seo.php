<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TariffSeoContent;

$item = TariffSeoContent::where('page_key', 'tariff_district:kayseri:pinarbasi')->first();

if ($item) {
    echo "Found record for: " . $item->city_name . " " . $item->district_name . "\n";
    echo "H1: " . $item->h1_title . "\n";
    echo "FAQs Count: " . count($item->faqs) . "\n";
} else {
    echo "Record NOT found!\n";
}
