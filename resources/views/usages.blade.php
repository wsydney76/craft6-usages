@props([
    'element' => null,
    'relatedElements' => [],
    'relations' => [],
])

@php
    /** @var RelatedElementDTO $relatedElement */
        use CraftCms\Cms\Asset\Elements\Asset;
        use wsydney76\usages\Data\RelatedElementDTO;
@endphp

<craft-tabs placement="block-start" size="medium" selected-index="0">
    <craft-tab slot="tab">Used by</craft-tab>
    <div slot="panel">
        @forelse($relatedElements as $relatedElement)
            <craft-card style="margin: 12px 0; background-color: {{ $relatedElement->backgroundColor }}"
                        thumb-alignment="end"
                        ondblclick="Craft.openSlideout('{{ $relatedElement->element->cpEditUrl }}')">
                <span style="display: inline-flex; align-items: center; gap: 0.5em;"
                      slot="label">{!! $relatedElement->labelHtml !!}
                </span>

                {{-- href has ugly red borders, avoid that by using script --}}
                <craft-button onclick="Craft.openSlideout('{{ $relatedElement->element->cpEditUrl }}')"
                              icon="pencil"
                              variant="plain"
                              size="small"
                              aria-label="Edit"
                              slot="actions">
                </craft-button>

                @if($relatedElement->thumbnailHtml)
                    <div slot="thumbnail">
                        {!! $relatedElement->thumbnailHtml !!}
                    </div>
                @endif

                <craft-link>
                    <a href="{{ $relatedElement->cpEditUrl }}">{{ $relatedElement->title }}</a>
                </craft-link>

                @if($relatedElement->bodyHtml)
                    <div class="mt-4">{!! $relatedElement->bodyHtml !!}</div>
                @endif

                <div style="display: flex; justify-content: space-between; inline-size: 100%;" slot="footer">
                    <span>ID: {{ $relatedElement->element->id }} Status: {{ $relatedElement->element->status }}</span>
                    <span>{{ $relatedElement->statusLabel }}</span>
                </div>
            </craft-card>
        @empty
            <p>No usages found.</p>
        @endforelse
    </div>

    @if($element instanceof Asset && $element->kind === 'image')
        <craft-tab slot="tab">Image Analysis</craft-tab>
        <div slot="panel">
            @php($analysis = $element->analysis)

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
        </div>
    @endif

    <craft-tab slot="tab">Raw</craft-tab>
    <div slot="panel">
        @if(count($relations))
            <table class="data fullwidth">
                <thead>
                <tr>
                    <th>Relation ID</th>
                    <th>Source ID</th>
                    <th>Target ID</th>
                    <th>Field ID</th>
                    <th>Sort Order</th>
                </tr>
                </thead>
                <tbody>
                @foreach($relations as $relation)
                    <tr>
                        <td>{{ $relation->id }}</td>
                        <td>{{ $relation->sourceId }}</td>
                        <td>{{ $relation->targetId }}</td>
                        <td>{{ $relation->fieldId }}</td>
                        <td>{{ $relation->sortOrder }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <p>No relations found.</p>
        @endif
    </div>
</craft-tabs>



