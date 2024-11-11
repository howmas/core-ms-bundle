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
use HowMAS\CoreMSBundle\Service\EcommerceService;

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
            new Item\Link('Thư viện', $this->getRoute('asset-listing'), '/bundles/pimcoreadmin/img/flat-color-icons/asset.svg', true),
        ];

        $classItems = [];
        $ecommerceClasses = EcommerceService::getClassnames();
        $ecomerceItems = [];
        $listing = ClassService::listing();
        foreach ($listing as $item) {
            if (in_array($item['name'], $ecommerceClasses)) {
                $itemLink = new Item\Link(
                    $item['title'],
                    $this->getRoute('object-listing', ['classId' => $item['id']]),
                    $item['icon'] ?: 'tio-cube',
                    (bool) $item['icon'],
                );
                $ecomerceItems[] = $itemLink;
                continue;
            }

            $itemLink = new Item\SubLink(
                $item['title'],
                $this->getRoute('object-listing', ['classId' => $item['id']]),
                $item['icon'] ?: 'tio-cube',
                (bool) $item['icon'],
            );
            $classItems[] = $itemLink;
        }
        $sidebarMenu[] = new Item\Menu('Dữ liệu', $classItems, '/bundles/pimcoreadmin/img/flat-color-icons/object.svg', true);

        if (!empty($ecomerceItems)) {
            $sidebarMenu[] = new Item\Title('Cửa hàng');
            $sidebarMenu = array_merge($sidebarMenu, $ecomerceItems);
        }


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