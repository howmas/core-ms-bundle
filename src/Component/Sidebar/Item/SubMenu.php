<?php

namespace HowMAS\CoreMSBundle\Component\Sidebar\Item;

class SubMenu extends Menu
{
    public function __construct(
        string $name,
        array $items,
        ?string $icon = null,
        bool $iconImage = false,
    )
    {
        parent::__construct(
            $name,
            $items,
            $icon,
            $iconImage,
        );
    }

    public function getIconTag()
    {
        return 'span';
    }

    public function getIconClass()
    {
        return '';
        // return 'nav-indicator-icon';
    }

    public function getNameClass()
    {
        return 'text-truncate';
    }
}
