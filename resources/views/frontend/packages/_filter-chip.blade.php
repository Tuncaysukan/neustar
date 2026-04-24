@php
    /** @var string $label */
    /** @var string $remove */
@endphp
<a href="{{ $remove }}"
   class="inline-flex items-center gap-1.5 pl-2.5 pr-1.5 py-1 rounded-md bg-base-200 border border-base-300 text-xs font-medium text-base-content hover:bg-base-300 transition">
    <span>{{ $label }}</span>
    <span class="text-base-content/50 hover:text-error" aria-hidden="true">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M4.3 4.3a1 1 0 011.4 0L10 8.6l4.3-4.3a1 1 0 111.4 1.4L11.4 10l4.3 4.3a1 1 0 01-1.4 1.4L10 11.4l-4.3 4.3a1 1 0 01-1.4-1.4L8.6 10 4.3 5.7a1 1 0 010-1.4z" clip-rule="evenodd"/>
        </svg>
    </span>
</a>
