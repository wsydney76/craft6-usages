<?php

namespace wsydney76\usages;

use CraftCms\Cms\FieldLayout\Events\FieldLayoutUIElementsResolving;
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
    }
}
