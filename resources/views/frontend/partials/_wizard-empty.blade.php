@php($__errorKey = $errorVar ?? 'nError')
<div class="border border-dashed border-base-300 rounded-lg p-6 text-center">
    <p class="text-sm text-base-content/60" x-text="{{ $__errorKey }} || 'Eşleşen kayıt bulunamadı.'"></p>
</div>
