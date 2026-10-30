<?php

declare(strict_types=1);

namespace wsydney76\usages\LayoutElements;

use CraftCms\Cms\Asset\Elements\Asset;
use CraftCms\Cms\Cp\Html\ElementHtml;
use CraftCms\Cms\Element\Contracts\ElementInterface;
use CraftCms\Cms\Entry\Elements\Entry;
use CraftCms\Cms\FieldLayout\FieldLayoutElementContext;
use CraftCms\Cms\FieldLayout\LayoutElements\BaseUiElement;
use CraftCms\Cms\Ui\Contracts\Node;
use CraftCms\Cms\Ui\Nodes\TemplateContent;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Override;
use function app;
use function CraftCms\Cms\t;
use function CraftCms\Cms\template;

/**
 * Renders a list of related elements that point to the current element.
 */
class Usages extends BaseUiElement
{
    public static function make(string $template): static
    {
        return app(static::class);
    }

    protected function selectorLabel(): string
    {
        return t('Usages Plugin');
    }

    protected function selectorIcon(): ?string
    {
        return 'arrow-right-arrow-left';
    }

    #[Override]
    public function hasCustomWidth(): bool
    {
        return true;
    }

    #[Override]
    public function hasSettings(): bool
    {
        return false;
    }

    #[Override]
    public function uiNode(FieldLayoutElementContext $context): ?Node
    {
        if (!$this->uid) {
            throw new InvalidArgumentException(
                'Persisted Template FieldLayout elements require stable UIDs.',
            );
        }

        // $html = $this->renderUsages($this->getRelatedElements($context->element));

        $html = template('_craft6-usages/usages', [
            'relatedElements' => $this->getRelatedElements($context->element),
            'relationsRecordCount' =>  DB::table('relations')->where('targetId', $context->element->id)->count(),
        ]);

        return TemplateContent::make($this->uid, $html)->trusted(true)->width($this->width);
    }

    protected function getRelatedElements(ElementInterface $element): Collection
    {
        return collect()
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
    }

    protected function renderUsages(Collection $relatedElements): string
    {
        if ($relatedElements->isEmpty()) {
            return '<p>No usages found</p>';
        }

        $elementHtml = app(ElementHtml::class);
        $items = $relatedElements
            ->map(
                fn(ElementInterface $element): string => sprintf(
                    '<li class="element p-2">%s</li>',
                    $elementHtml->elementCardHtml($element, [
                        'hyperlink' => true,
                    ]),
                ),
            )
            ->implode('');

        return sprintf('<ul class="card-grid">%s</ul>', $items);
    }
}
