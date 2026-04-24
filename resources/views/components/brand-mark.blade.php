{{--
    Brand mark — displays an operator logo when available, falls back to an
    initial-avatar with a deterministic hue derived from the brand name.

    Usage:
        <x-brand-mark :operator="$package->operator" />
        <x-brand-mark :operator="$operator" size="lg" />
        <x-brand-mark :operator="$operator" size="sm" rounded="full" />

    Sizes: xs(24) · sm(32) · md(40) · lg(56) · xl(72)
--}}
@props([
    'operator' => null,
    'size'     => 'md',
    'rounded'  => 'md',
    'class'    => '',
])

@php
    $sizeMap = [
        'xs' => ['box' => 'h-6 w-6',  'text' => 'text-[11px]', 'pad' => 'p-0.5'],
        'sm' => ['box' => 'h-8 w-8',  'text' => 'text-xs',      'pad' => 'p-1'],
        'md' => ['box' => 'h-10 w-10','text' => 'text-sm',      'pad' => 'p-1.5'],
        'lg' => ['box' => 'h-14 w-14','text' => 'text-lg',      'pad' => 'p-2'],
        'xl' => ['box' => 'h-[72px] w-[72px]', 'text' => 'text-xl', 'pad' => 'p-2.5'],
    ];
    $s = $sizeMap[$size] ?? $sizeMap['md'];

    $radius = [
        'md'   => 'rounded-md',
        'lg'   => 'rounded-lg',
        'xl'   => 'rounded-xl',
        'full' => 'rounded-full',
    ][$rounded] ?? 'rounded-md';

    $name    = $operator?->name ?? '';
    $logo    = $operator?->logo_url ?? null;
    $initial = $operator?->initial ?? mb_strtoupper(mb_substr($name, 0, 1) ?: '?');
    $hue     = $operator?->brand_hue ?? 200;

    // Subtle tinted background for the initial fallback. We keep saturation
    // + lightness low so the chip reads as "brand color" not as a toy.
    $bg   = "hsl({$hue} 35% 92%)";
    $fg   = "hsl({$hue} 55% 32%)";
    $ring = "hsl({$hue} 35% 82%)";
@endphp

@if($logo)
    <img
        src="{{ $logo }}"
        alt="{{ $name }} logosu"
        loading="lazy"
        class="{{ $s['box'] }} {{ $radius }} object-contain bg-base-100 border border-base-300 {{ $s['pad'] }} {{ $class }}"
    >
@else
    <span
        class="{{ $s['box'] }} {{ $radius }} grid place-items-center font-semibold border {{ $s['text'] }} {{ $class }}"
        style="background: {{ $bg }}; color: {{ $fg }}; border-color: {{ $ring }};"
        aria-label="{{ $name }} logosu"
        role="img"
    >{{ $initial }}</span>
@endif
