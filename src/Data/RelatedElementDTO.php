<?php

namespace wsydney76\usages\Data;

use CraftCms\Cms\Element\Contracts\ElementInterface;

class RelatedElementDTO
{
    public function __construct(
        public ElementInterface $element,
        public string $labelHtml,
        public ?string $thumbnailHtml,
        public string $backgroundColor,
        public string $title,
        public string $statusLabel,
        public string $bodyHtml = '',
    ) {}
}