<?php

namespace HowMAS\CoreMSBundle\Component\Sidebar\Item;

use HowMAS\CoreMSBundle\Component\Sidebar\Item;
use HowMAS\CoreMSBundle\EventListener\ControllerListener;

class Link extends Item
{
    public function __construct(
        protected string $name,
        protected ?string $link = null,
        protected ?string $icon = null,
        protected bool $iconImage = false,
    )
    {
        $this->name = $name;
        $this->link = $link;
        $this->icon = $icon ?: $this->getIcon();
        $this->iconImage = $iconImage;
    }

    public function getIcon()
    {
        return 'tio-chevron-right';
    }

    public function getIconTag()
    {
        return 'i';
    }

    public function getIconClass()
    {
        return 'nav-icon';
    }

    public function getNameClass()
    {
        return 'navbar-vertical-aside-mini-mode-hidden-elements text-truncate';
    }

    public function render(): string
    {
        $html = '<li class="nav-item ">
            <a class="js-nav-tooltip-link nav-link $loadingClass" href="$link" title="$name" data-placement="left">' .
                ($this->iconImage
                ? '<img src="$iconName" class="mr-1" width="20px" />'
                : '<$iconTag class="$iconName $iconClass mr-1"></$iconTag>')
                . '<span class="$className">$name</span>
            </a>
        </li>';

        $html = str_replace(
            ['$loadingClass', '$name', '$link', '$iconName', '$iconTag', '$iconClass', '$className'],
            [ControllerListener::LOADING_CLASS, $this->name, $this->link, $this->icon, $this->getIconTag(), $this->getIconClass(), $this->getNameClass()],
            $html
        );

        return $html;
    }
}
