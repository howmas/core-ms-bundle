<?php

namespace HowMAS\CoreMSBundle\Component\Sidebar\Item;

class SubLink extends Link
{
    public function __construct(
        string $name,
        ?string $link = null,
        bool $active = false,
        ?string $icon = null,
        bool $iconImage = false,
    )
    {
        parent::__construct(
            $name,
            $link,
            $active,
            $icon,
            $iconImage
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
