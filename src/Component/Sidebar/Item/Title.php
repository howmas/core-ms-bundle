<?php

namespace HowMAS\CoreMSBundle\Component\Sidebar\Item;

use HowMAS\CoreMSBundle\Component\Sidebar\Item;

class Title extends Item
{
    const DEFAULT_ICON = 'tio-more-horizontal';

    protected $name;
    protected $icon;

    public function __construct(
        ?string $name = null,
        ?string $icon = null
    )
    {
        $this->name = $name;
        $this->icon = $icon ?: self::DEFAULT_ICON;
    }

    public function render(): string
    {
        $html = '<li class="nav-item">
            ' . ($this->name ? '<small class="nav-subtitle" title="$name">$name</small>' : '') . '
            <small class="$icon nav-subtitle-replacer"></small>
        </li>';

        $html = str_replace(
            ['$name', '$icon'],
            [$this->name, $this->icon],
            $html
        );

        return $html;
    }
}
