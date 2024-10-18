<?php

namespace HowMAS\CoreMSBundle\Component\Sidebar\Item;

class SubLink extends Link
{
    public function __construct(
        string $name,
        ?string $link = null,
        ?string $icon = null
    )
    {
        parent::__construct(
            $name,
            $link,
            $icon
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
