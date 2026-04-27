<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class SiteSettingController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::orderBy('order')->get()->groupBy('group');
        $groups   = ['general' => 'Genel', 'contact' => 'İletişim', 'social' => 'Sosyal Medya', 'seo' => 'SEO'];
        return view('admin.site-settings.index', compact('settings', 'groups'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'settings'   => ['required', 'array'],
            'settings.*' => ['nullable', 'string', 'max:2000'],
        ]);

        foreach ($data['settings'] as $key => $value) {
            SiteSetting::set($key, $value);
        }

        // Tüm site ayarları cache'ini temizle
        \Illuminate\Support\Facades\Cache::forget('site_settings');

        return back()->with('success', 'Site ayarları kaydedildi.');
    }
}
