@extends('layouts.admin')

@section('header')
    CSS / JS Editörü
@endsection

@section('content')
<div x-data="{ active: 'custom_css' }" class="space-y-4">

    {{-- Tab bar --}}
    <div class="bg-white rounded-lg shadow p-1 flex gap-1 w-fit">
        @foreach($codes as $key => $code)
        <button type="button"
                @click="active = '{{ $key }}'"
                :class="active === '{{ $key }}'
                    ? 'bg-blue-600 text-white shadow-sm'
                    : 'text-gray-600 hover:bg-gray-100'"
                class="px-4 py-2 rounded-md text-sm font-medium transition">
            {{ $code['label'] }}
        </button>
        @endforeach
    </div>

    {{-- Editör panelleri --}}
    @foreach($codes as $key => $code)
    <div x-show="active === '{{ $key }}'" x-cloak>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-4 border-b flex items-center justify-between gap-4">
                <div>
                    <h3 class="text-base font-bold">{{ $code['label'] }}</h3>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $code['hint'] }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button"
                            onclick="formatCode('{{ $key }}')"
                            class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded transition">
                        Biçimlendir
                    </button>
                    <button type="button"
                            onclick="copyCode('{{ $key }}')"
                            class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded transition">
                        Kopyala
                    </button>
                </div>
            </div>

            <form action="{{ route('admin.custom-code.update', $key) }}" method="POST" id="form-{{ $key }}">
                @csrf
                @method('PUT')

                {{-- CodeMirror container --}}
                <div class="relative border-b border-gray-100">
                    <div id="editor-{{ $key }}"
                         class="w-full font-mono text-sm"
                         style="min-height: 420px; max-height: 70vh;">{{ $code['content'] }}</div>
                    <textarea name="content"
                              id="textarea-{{ $key }}"
                              class="hidden">{{ $code['content'] }}</textarea>
                </div>

                <div class="p-4 flex items-center justify-between gap-4">
                    <div class="text-xs text-gray-400" id="status-{{ $key }}">
                        @if(session('success') && request()->is('*custom-code*'))
                            <span class="text-green-600">✓ {{ session('success') }}</span>
                        @endif
                    </div>
                    <div class="flex gap-2">
                        <button type="button"
                                onclick="resetCode('{{ $key }}')"
                                class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 text-sm">
                            Sıfırla
                        </button>
                        <button type="submit"
                                onclick="syncTextarea('{{ $key }}')"
                                class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700 text-sm font-medium">
                            Kaydet
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endforeach

    {{-- Bilgi kutusu --}}
    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-sm text-amber-800">
        <strong>Not:</strong>
        <ul class="mt-1 space-y-1 list-disc list-inside text-xs">
            <li><strong>Özel CSS</strong> tüm sayfalara <code>&lt;/head&gt;</code> öncesi eklenir — anında aktif olur.</li>
            <li><strong>Özel JS</strong> tüm sayfalara <code>&lt;/body&gt;</code> öncesi eklenir — anında aktif olur.</li>
            <li><strong>CSS Değişkenleri</strong> kaydedilince <code>resources/css/variables.css</code> dosyası güncellenir.
                Renk değişikliklerinin tam yansıması için <code>npm run build</code> çalıştırın.</li>
        </ul>
    </div>
</div>
@endsection

@push('scripts')
{{-- CodeMirror 5 CDN --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/theme/dracula.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/css/css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/javascript/javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/edit/matchbrackets.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/edit/closebrackets.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/selection/active-line.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/comment/comment.min.js"></script>

<style>
.CodeMirror {
    height: 420px;
    max-height: 70vh;
    font-size: 13px;
    font-family: 'JetBrains Mono', 'Fira Code', 'Cascadia Code', monospace;
    line-height: 1.6;
}
.CodeMirror-scroll { min-height: 420px; }
</style>

<script>
const editors = {};
const originals = {};

const configs = @json(collect($codes)->map(fn($c, $k) => ['mode' => $c['mode'], 'content' => $c['content']]));

document.addEventListener('DOMContentLoaded', function () {
    Object.entries(configs).forEach(([key, cfg]) => {
        const el = document.getElementById('editor-' + key);
        if (!el) return;

        const cm = CodeMirror(function(elt) {
            el.parentNode.replaceChild(elt, el);
        }, {
            value: cfg.content || '',
            mode: cfg.mode,
            theme: 'dracula',
            lineNumbers: true,
            matchBrackets: true,
            autoCloseBrackets: true,
            styleActiveLine: true,
            indentUnit: 4,
            tabSize: 4,
            indentWithTabs: false,
            lineWrapping: false,
            extraKeys: {
                'Ctrl-/': 'toggleComment',
                'Cmd-/': 'toggleComment',
                'Ctrl-S': function(cm) { syncAndSubmit(key); },
                'Cmd-S':  function(cm) { syncAndSubmit(key); },
            },
        });

        editors[key] = cm;
        originals[key] = cfg.content || '';
    });
});

function syncTextarea(key) {
    const cm = editors[key];
    const ta = document.getElementById('textarea-' + key);
    if (cm && ta) ta.value = cm.getValue();
}

function syncAndSubmit(key) {
    syncTextarea(key);
    document.getElementById('form-' + key).submit();
}

function copyCode(key) {
    const cm = editors[key];
    if (!cm) return;
    navigator.clipboard.writeText(cm.getValue()).then(() => {
        const s = document.getElementById('status-' + key);
        if (s) { s.innerHTML = '<span class="text-blue-600">✓ Kopyalandı</span>'; setTimeout(() => s.innerHTML = '', 2000); }
    });
}

function resetCode(key) {
    if (!confirm('Değişiklikleri geri almak istediğinize emin misiniz?')) return;
    const cm = editors[key];
    if (cm) cm.setValue(originals[key] || '');
}

function formatCode(key) {
    // Basit CSS/JS biçimlendirme — sadece boşlukları düzenler
    const cm = editors[key];
    if (!cm) return;
    // CodeMirror'da built-in formatter yok, kullanıcıya bilgi ver
    const s = document.getElementById('status-' + key);
    if (s) { s.innerHTML = '<span class="text-amber-600">Biçimlendirme için Ctrl+/ ile yorum ekleyebilirsiniz.</span>'; setTimeout(() => s.innerHTML = '', 3000); }
}
</script>
@endpush
