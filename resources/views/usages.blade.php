@php
    use CraftCms\Cms\Asset\Elements\Asset;use CraftCms\Cms\Element\Contracts\ElementInterface;use CraftCms\Cms\Entry\Elements\Entry;
    /** @var ElementInterface $element */



@endphp

{{ $relationsRecordCount }} relations records found in total (includes revisions and trashed elements).


@forelse($relatedElements as $element)
    @php
        if($element instanceof Entry) {
            $parts = ['<b>Entry</b>'];
            $parts[] =  $element->section ? ('Section: '. $element->section->name) : 'Nested Element';
            $parts[] = 'Type: ' . $element->type->name;
            $label=implode(' ', $parts);

            $type = EntryTypes::getEntryTypeByHandle($element->type->handle);
            $thumbHtml = $type->getFieldLayout()->getThumbForElement($type->getFieldLayout()->thumbSources[0], $element)?->getHtml(120) ?? '';
            $cssVar = sprintf('var(--color-%s-50)', $type->color->value);
        }

        if($element instanceof Asset) {
            $parts = ['<b>Asset</b>'];
            $parts[] = 'Volume: ' . $element->volume->name;
            $parts[] = 'Folder Path: ' . $element->folderPath;
            $label=implode(' ', $parts);

            $volume = Volumes::getVolumeByHandle($element->volume->handle);
            $thumbHtml = $element->getImg(['width' => 120, 'height' => 120]) ;
            $cssVar = '';
        }
    @endphp


    <craft-card style="margin: 12px 0; background-color: {{$cssVar}}" thumb-alignment="end">
        <span style="display: inline-flex; align-items: center; gap: 0.5em;" slot="label">{!!   $label !!}</span>

        {{-- href has ugly red borders, avoid that by using script --}}
        <craft-button onclick="window.location.href='{{$element->cpEditUrl}}'" icon="pencil" variant="plain"
                      size="small" aria-label="Edit" slot="actions"></craft-button>

        @if($thumbHtml)
            <div slot="thumbnail">
                {!! $thumbHtml !!}
            </div>
        @endif

        <b>{{ $element->title ?? $element->getRootOwner()->title }}</b>

        <div style="display: flex; justify-content: space-between; inline-size: 100%;" slot="footer">
            <span></span>
            <span>{{ $element->getIsDraft() ? ('Draft ' . $element->getDraftName()) : 'Canonical' }}</span>
        </div>
    </craft-card>
@empty
    <p>No usages found.</p>
@endforelse