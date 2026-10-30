<?php

namespace wsydney76\usages\Services;

use CraftCms\Cms\Asset\Elements\Asset;
use CraftCms\Cms\Element\Contracts\ElementInterface;
use CraftCms\Cms\Entry\Elements\Entry;
use wsydney76\usages\Data\RelatedElementDTO;

class UsageService
{
    /**
     * @return list<RelatedElementDTO>
     */
    public static function getRelatedElementsForElement(ElementInterface $element): array
    {
        $relatedElements = collect()
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
            );

        return $relatedElements
            ->map(static fn (ElementInterface $relatedElement): RelatedElementDTO => self::toDto($relatedElement))
            ->all();
    }

    protected static function toDto(ElementInterface $element): RelatedElementDTO
    {
        if ($element instanceof Entry) {
            $parts = ['<b>Entry</b>'];
            $parts[] = $element->section ? ('Section: ' . $element->section->name) : 'Nested Element';
            $parts[] = 'Type: ' . $element->type->name;

            $thumbHtml = null;
            $thumbSource = $element->type->getFieldLayout()->thumbSources[0] ?? null;
            if ($thumbSource) {
                $thumbHtml = $element->type->getFieldLayout()->getThumbForElement($thumbSource, $element)?->getHtml(120);
            }

            return new RelatedElementDTO(
                element: $element,
                labelHtml: implode(' ', $parts),
                thumbnailHtml: $thumbHtml,
                backgroundColor: sprintf('var(--color-%s-50)', $element->type->color->value),
                title: $element->title ?? $element->getRootOwner()->title,
                statusLabel: $element->getIsDraft() ? ('Draft ' . $element->getDraftName()) : 'Canonical',
            );
        }

        if ($element instanceof Asset) {
            $parts = ['<b>Asset</b>'];
            $parts[] = 'Volume: ' . $element->volume->name;
            $parts[] = 'Folder Path: ' . $element->folderPath;

            return new RelatedElementDTO(
                element: $element,
                labelHtml: implode(' ', $parts),
                thumbnailHtml: $element->getImg(['width' => 120, 'height' => 120]),
                backgroundColor: '',
                title: $element->title ?? $element->getRootOwner()->title,
                statusLabel: $element->getIsDraft() ? ('Draft ' . $element->getDraftName()) : 'Canonical',
            );
        }

        return new RelatedElementDTO(
            element: $element,
            labelHtml: '<b>Element</b>',
            thumbnailHtml: null,
            backgroundColor: '',
            title: $element->title ?? $element->getRootOwner()->title,
            statusLabel: $element->getIsDraft() ? ('Draft ' . $element->getDraftName()) : 'Canonical',
        );
    }
}