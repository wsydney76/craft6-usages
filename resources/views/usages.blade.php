@php
    /** @var RelatedElementDTO $relatedElement */
        use wsydney76\usages\Data\RelatedElementDTO;
@endphp


@forelse($relatedElements as $relatedElement)
    <craft-card style="margin: 12px 0; background-color: {{ $relatedElement->backgroundColor }}" thumb-alignment="end">
        <span style="display: inline-flex; align-items: center; gap: 0.5em;" slot="label">{!! $relatedElement->labelHtml !!}</span>

        {{-- href has ugly red borders, avoid that by using script --}}
        <craft-button onclick="window.location.href='{{ $relatedElement->element->cpEditUrl }}'" icon="pencil" variant="plain"
                      size="small" aria-label="Edit" slot="actions"></craft-button>

        @if($relatedElement->thumbnailHtml)
            <div slot="thumbnail">
                {!! $relatedElement->thumbnailHtml !!}
            </div>
        @endif

        <b>{{ $relatedElement->title }}</b>

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

{{ $relationsRecordCount }} relations records found in total (includes revisions and trashed elements).