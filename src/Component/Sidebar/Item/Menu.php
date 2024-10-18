<?php

namespace HowMAS\CoreMSBundle\Component\Sidebar\Item;

class Menu extends Link
{
    public function __construct(
        string $name,
        protected array $items,
        ?string $icon = null
    )
    {
        parent::__construct(
            $name,
            null,
            $icon
        );

        $this->items = $items;
    }

    public function render(): string
    {
        $html = '<li class="navbar-vertical-aside-has-menu ">
            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle " href="javascript:;" title="$name">
                <$iconTag class="$iconName $iconClass mr-1"></$iconTag>
                <span class="$className">$name</span>
            </a>
            <ul class="js-navbar-vertical-aside-submenu nav nav-sub">
                $itemContent
            </ul>
        </li>';

        $itemContent = '';
        foreach ($this->items as $item) {
            if ($item instanceof Link) {
                $itemContent .= $item->render();
            }
        }

        $html = str_replace(
            ['$name', '$iconName', '$iconTag', '$iconClass', '$className', '$itemContent'],
            [$this->name, $this->icon, $this->getIconTag(), $this->getIconClass(), $this->getNameClass(), $itemContent],
            $html
        );

        return $html;
    }
}
