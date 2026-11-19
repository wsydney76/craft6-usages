<?php

namespace wsydney76\usages;

use CraftCms\Cms\Asset\Elements\Asset;
use CraftCms\Cms\Entry\Elements\Entry;
use CraftCms\Cms\FieldLayout\Events\FieldLayoutUIElementsResolving;
use CraftCms\Cms\FieldLayout\Events\FieldLayoutUiResolving;
use CraftCms\Cms\FieldLayout\FieldLayoutElementContext;
use CraftCms\Cms\Plugin\Plugin as BasePlugin;
use Illuminate\Support\Facades\Event;
use wsydney76\usages\LayoutElements\Usages;

class Plugin extends BasePlugin
{

    public string $schemaVersion = '1.0.0';

    public bool $hasCpSettings = false;


    public function boot(): void
    {
        Event::listen(function (FieldLayoutUIElementsResolving $e) {
            $e->elements[] = new Usages();
        });


        Event::listen(function (FieldLayoutUiResolving $event): void {
            if (! ($event->element instanceof Entry || $event->element instanceof Asset)) {
                return;
            }

            $node = (new Usages)->uiNode(
                new FieldLayoutElementContext($event->element, $event->context),
            );

            if ($node !== null) {
                $event->ui->addTab(
                    t('Usages', category: '_craft6-usages'),
                    [$node],
                    icon: 'arrow-right-arrow-left',
                    sidebar: true,
                );
            }
        });

    }
}
