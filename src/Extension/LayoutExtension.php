<?php

namespace HowMAS\CoreMSBundle\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use HowMAS\CoreMSBundle\Component\Sidebar\Item;
use HowMAS\CoreMSBundle\Component\Sidebar\Form;
use HowMAS\CoreMSBundle\Service\ClassService;
use HowMAS\CoreMSBundle\Service\CoreService;
use HowMAS\CoreMSBundle\Service\DocumentService;

class LayoutExtension extends AbstractExtension
{
    public function __construct(
        private UrlGeneratorInterface $router,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('hcore_layout_sidebar_menu', [$this, 'getSidebarMenu']),
            new TwigFunction('hcore_layout_path', [$this, 'getPath']),
        ];
    }

    public function getSidebarMenu()
    {
        $documentMenu = DocumentService::getSidebarMenu($this->router);

        $sidebarMenu = [
            new Item\Link('Tổng quan', $this->getRoute('index'), 'tio-home-vs-1-outlined'),
            new Item\Title('Quản trị dữ liệu'),
            $documentMenu,
            new Item\Link('Thư viện', $this->getRoute('asset-listing'), 'tio-folders-outlined'),
        ];

        $classItems = [];
        $listing = ClassService::listing();
        foreach ($listing as $item) {
            $classItems[] = new Item\SubLink(
                $item['title'],
                $this->getRoute('object-listing', ['classId' => $item['id']]),
                'tio-cube'
            );
        }
        $sidebarMenu[] = new Item\Menu('Dữ liệu', $classItems, 'tio-cube');

        $sidebarMenu[] = new Item\Title();

        return $sidebarMenu;
    }

    public function getPath(string $routePart, array $params = [])
    {
        return $this->getRoute($routePart, $params);
    }

    private function getRoute(string $routePart, array $params = [])
    {
        return $this->router->generate(CoreService::getRoute($routePart), $params);
    }
}