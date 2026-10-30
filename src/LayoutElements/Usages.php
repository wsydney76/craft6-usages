<?php

declare(strict_types=1);

namespace wsydney76\usages\LayoutElements;

use CraftCms\Cms\FieldLayout\FieldLayoutElementContext;
use CraftCms\Cms\FieldLayout\LayoutElements\BaseUiElement;
use CraftCms\Cms\Ui\Contracts\Node;
use CraftCms\Cms\Ui\Nodes\TemplateContent;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Override;
use wsydney76\usages\Services\UsageService;
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

        $html = template('_craft6-usages/usages', [
            'relatedElements' => UsageService::getRelatedElementsForElement($context->element),
            'relationsRecordCount' => DB::table('relations')->where('targetId', $context->element->id)->count(),
        ]);

        return TemplateContent::make($this->uid, $html)->trusted(true)->width($this->width);
    }
}
