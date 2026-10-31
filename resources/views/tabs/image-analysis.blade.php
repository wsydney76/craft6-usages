@php
    /** @var CraftCms\Cms\Asset\Analysis\Data\ImageAnalysis $analysis */
@endphp

@props([
    'analysis'
])

<h3 class="mb-2">Text</h3>
{{ $analysis->text ?? 'n/a' }}

<h3 class="mt-4 mb-2">Keywords</h3>

@forelse($analysis->keywords ?? [] as $keyword)
    <div>
        {{ $keyword }}
    </div>
@empty
    n/a
@endforelse

<h3 class="mt-4 mb-2">Type</h3>
<p>{{ $analysis->type->label() }}</p>