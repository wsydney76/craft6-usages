@php
    use CraftCms\Cms\Asset\Elements\Asset;
    use CraftCms\Cms\Entry\Elements\Entry;use function CraftCms\Cms\template;
@endphp

@props([
    'element' => null,
    'relatedElements' => [],
    'relations' => [],
])

@if(!$element instanceof Asset && !$element instanceof Entry)
    <p class="text-red-500">
        {{ t('This element type is not supported by the Usages plugin.') }}
    </p>
@endif

<div style="margin-bottom: 16px">
    @if($element instanceof Asset)
        {{ $element->volume->name }}
    @endif

    @if($element instanceof Entry)
        {{ $element->section?->name ?? $element->type->name  }}
    @endif

    <strong>{{ $element->title }}</strong>
</div>

<craft-tabs placement="block-start" size="medium" selected-index="0">
    <craft-tab slot="tab">Used by</craft-tab>
    <div slot="panel">
        {!! template('_craft6-usages/tabs/usages', [
            'relatedElements' => $relatedElements,
        ]) !!}
    </div>

    @if($element instanceof Asset && $element->kind === 'image')
        <craft-tab slot="tab">Image Analysis</craft-tab>
        <div slot="panel">
            {!! template('_craft6-usages/tabs/image-analysis', [
                'analysis' => $element->analysis
            ]) !!}
        </div>
    @endif

    <craft-tab slot="tab">Raw</craft-tab>
    <div slot="panel">
        {!! template('_craft6-usages/tabs/raw', [
            'relations' => $relations
        ]) !!}
    </div>
</craft-tabs>



