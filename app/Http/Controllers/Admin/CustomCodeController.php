<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomCode;
use Illuminate\Http\Request;

class CustomCodeController extends Controller
{
    private array $editors = [
        'custom_css' => [
            'label' => 'Özel CSS',
            'mode'  => 'css',
            'hint'  => 'Tüm sayfalara eklenir. Tailwind/DaisyUI sınıflarını override etmek için kullanın.',
        ],
        'custom_js' => [
            'label' => 'Özel JavaScript',
            'mode'  => 'javascript',
            'hint'  => 'Tüm sayfalara </body> öncesi eklenir. Alpine.js ile uyumlu.',
        ],
        'variables_css' => [
            'label' => 'CSS Değişkenleri (variables.css)',
            'mode'  => 'css',
            'hint'  => 'Renk ve token değişkenleri. Değişiklik sonrası "npm run build" gerekebilir.',
        ],
    ];

    public function index()
    {
        $codes = [];
        foreach ($this->editors as $key => $meta) {
            $codes[$key] = [
                'label'   => $meta['label'],
                'mode'    => $meta['mode'],
                'hint'    => $meta['hint'],
                'content' => CustomCode::where('key', $key)->value('content') ?? $this->defaultContent($key),
            ];
        }

        return view('admin.custom-code.index', compact('codes'));
    }

    public function update(Request $request, string $key)
    {
        if (! array_key_exists($key, $this->editors)) {
            abort(404);
        }

        $request->validate([
            'content' => ['nullable', 'string', 'max:500000'],
        ]);

        CustomCode::set($key, $this->editors[$key]['label'], $request->input('content', ''));

        // variables_css ise dosyayı da güncelle
        if ($key === 'variables_css') {
            $content = $request->input('content', '');
            file_put_contents(resource_path('css/variables.css'), $content);
        }

        return back()->with('success', $this->editors[$key]['label'] . ' başarıyla kaydedildi.');
    }

    private function defaultContent(string $key): string
    {
        return match ($key) {
            'variables_css' => file_get_contents(resource_path('css/variables.css')),
            default         => '',
        };
    }
}
