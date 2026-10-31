<?php

namespace wsydney76\usages\Services;

use CraftCms\Cms\Asset\Elements\Asset;
use CraftCms\Cms\Database\Table;
use CraftCms\Cms\Element\Contracts\ElementInterface;
use CraftCms\Cms\Entry\Elements\Entry;
use CraftCms\Cms\Support\Facades\Elements;
use Illuminate\Support\Facades\DB;
use wsydney76\usages\Data\RelatedElementDTO;

class UsageService
{
    /**
     * @return list<RelatedElementDTO>
     */
    public static function getRelatedElementsForElement(ElementInterface $element): array
    {
        $relatedElements = DB::table(Table::RELATIONS)->where('targetId', $element->id)
            ->get('sourceId')
        ->map(static fn ($row) => Elements::getElementById($row->sourceId))
        ->filter(fn($relatedElement) => $relatedElement !== null)
        ->reject(fn($relatedElement) => $relatedElement->getIsRevision() || $relatedElement->getRootOwner()?->getIsRevision()); // Filter out non-canonical elements

        // TODO: Don't show revisions for now.
        //->reject(fn($relatedElement) =>  $relatedElement->getIsRevision()
        //    && $relatedElement->getCanonical()->getCurrentRevision()?->revisionId === $relatedElement->revisionId); // Filter out the current revision.


        /*$relatedElements = collect()
            ->merge(
                Entry::find()
                    ->status(null)
                    ->drafts(null)
                    ->provisionalDrafts(null)
                    ->relatedTo(['targetElement' => $element])
                    ->all(),
            )
            ->merge(
                Asset::find()
                    ->status(null)
                    ->relatedTo(['targetElement' => $element])
                    ->all(),
            );*/

        return $relatedElements
            ->map(static fn (ElementInterface $relatedElement): RelatedElementDTO => self::toDto($relatedElement))
            ->all();
    }

    protected static function toDto(ElementInterface $element): RelatedElementDTO
    {
        if ($element instanceof Entry) {
            $thumbHtml = null;
            $thumbSource = $element->type->getFieldLayout()->thumbSources[0] ?? null;
            if ($thumbSource) {
                $thumbHtml = $element->type->getFieldLayout()->getThumbForElement($thumbSource, $element)?->getHtml(120);
            }
            if ($element->section) {
                $labelParts = ['<b>Entry</b>'];
                $labelParts[] = 'Section: ' . $element->section->name;
                $labelParts[] = 'Type: ' . $element->type->name;
                $title = $element->title;
                $statusLabel = self::getStatusLabel($element);
                $bodyHtml = self::getBodyHtml($element);
                $cpEditurl = $element->cpEditUrl;
            } else {
                $rootOwner = $element->getRootOwner();
                $labelParts = ['<b>Nested Entry</b>'];
                $labelParts[] = 'Type: ' . $element->type->name;
                $title = $rootOwner->title . ($element->title ? ' - ' . $element->title : '');
                $statusLabel = self::getStatusLabel($rootOwner);
                $bodyHtml = self::getBodyHtml($rootOwner);
                $cpEditurl = $rootOwner->cpEditUrl;
            }

            return new RelatedElementDTO(
                element: $element,
                labelHtml: implode(' ', $labelParts),
                thumbnailHtml: $thumbHtml,
                backgroundColor: sprintf('var(--color-%s-50)', ($element->type->color->value ?? '')),
                title: $title,
                statusLabel: $statusLabel,
                bodyHtml: $bodyHtml,
                cpEditUrl: $cpEditurl,
            );
        }

        if ($element instanceof Asset) {
            $labelParts = ['<b>Asset</b>'];
            $labelParts[] = 'Volume: ' . $element->volume->name;
            $labelParts[] = 'Folder Path: ' . $element->folderPath;

            return new RelatedElementDTO(
                element: $element,
                labelHtml: implode(' ', $labelParts),
                thumbnailHtml: $element->getImg(['width' => 120, 'height' => 120]),
                backgroundColor: '',
                title: $element->title ?? $element->getRootOwner()->title,
                statusLabel: self::getStatusLabel($element),
                bodyHtml: self::getBodyHtml($element),
                cpEditUrl: $element->getCpEditUrl() ?? '',
            );
        }

        return new RelatedElementDTO(
            element: $element,
            labelHtml: '<b>Element</b>',
            thumbnailHtml: null,
            backgroundColor: '',
            title: $element->title ?? $element->getRootOwner()->title,
            statusLabel: $element->getIsDraft() ? ('Draft ' . $element->getDraftName()) : 'Canonical',
            bodyHtml: self::getBodyHtml($element),
            cpEditUrl: $element->getCpEditUrl() ?? '',
        );
    }

    protected static function getStatusLabel(ElementInterface $element): string
    {
        if ($element->getIsDraft()) {

            return $element->isProvisionalDraft ? 'Provisional draft' : 'Draft' ;
        }


        if ($element->getIsRevision()) {
            return 'Revision ' . $element->revisionNum;
        }
        return 'Canonical';
    }

    protected static function getBodyHtml(ElementInterface $element): string
    {
        if($element->getIsRevision()) {
            return 'Revision Notes: ' . $element->revisionNotes;
        }

        if (!$element->getIsDraft()) {
            return '';
        }

        $parts = [];
        $parts[] = $element->getCanonicalId() == $element->id ? 'Unpublished' : 'Draft for ID: ' . $element->canonicalId;
        $parts[] = 'Name: ' . $element->getDraftName();
        $parts[] = 'Created by: ' . $element->getDraftCreator()?->fullName ?? 'Unknown';

        return  implode('<br>', $parts);
    }
}