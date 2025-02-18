<?php

namespace HowMAS\CoreMSBundle\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Carbon\Carbon;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\Asset\Image;
use HowMAS\CoreMSBundle\Component\Sidebar\Item;
use HowMAS\CoreMSBundle\Component\Sidebar\Form;
use HowMAS\CoreMSBundle\Service\ClassService;
use HowMAS\CoreMSBundle\Service\CoreService;
use HowMAS\CoreMSBundle\Service\DocumentService;
use HowMAS\CoreMSBundle\Service\EcommerceService;

class LayoutExtension extends AbstractExtension
{
    protected $request;

    public function __construct(
        private UrlGeneratorInterface $router,
        private RequestStack $requestStack, 
    ) {
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * @inheritDoc
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('hcore_layout_sidebar_menu', [$this, 'getSidebarMenu']),
            new TwigFunction('hcore_layout_path', [$this, 'getPath']),
            new TwigFunction('hcore_layout_render_table_value', [$this, 'renderTableValueFromField']),
            new TwigFunction('hcore_layout_preivew_image', [$this, 'getPreviewImagePath']),
        ];
    }

    public function getSidebarMenu()
    {
        $pathInfor = $this->request->getPathInfo();
        $documentMenu = DocumentService::getSidebarMenu($this->router, $pathInfor);

        $indexRoute = $this->getRoute('index');
        $guideRoute = $this->getRoute('guide');
        $assetRoute = $this->getRoute('asset-listing');

        $sidebarMenu = [
            new Item\Link('Tổng quan', $indexRoute, CoreService::isMatchRoute($pathInfor, $indexRoute), 'tio-home-vs-1-outlined'),
            new Item\Link('Hướng dẫn', $guideRoute, CoreService::isMatchRoute($pathInfor, $guideRoute), 'tio-book-outlined'),
            new Item\Title('Quản trị dữ liệu'),
            $documentMenu,
            new Item\Link('Thư viện', $assetRoute, CoreService::isMatchRoute($pathInfor, $assetRoute), '/bundles/pimcoreadmin/img/flat-color-icons/asset.svg', true),
        ];

        $classItems = [];
        $ecommerceClasses = EcommerceService::getClassnames();
        $ecomerceItems = [];
        $listing = ClassService::listing();
        foreach ($listing as $item) {
            $route = $this->getRoute('object-listing', ['classId' => $item['id']]);
            $active = CoreService::isMatchRoute($pathInfor, $route);

            if (in_array($item['name'], $ecommerceClasses)) {
                $itemLink = new Item\Link(
                    $item['title'],
                    $route,
                    $active,
                    $item['icon'] ?: 'tio-cube',
                    (bool) $item['icon'],
                );
                $ecomerceItems[] = $itemLink;
                continue;
            }

            $itemLink = new Item\SubLink(
                $item['title'],
                $route,
                $active,
                $item['icon'] ?: 'tio-cube',
                (bool) $item['icon'],
            );
            $classItems[] = $itemLink;
        }

        if (!empty($classItems)) {
            $sidebarMenu[] = new Item\Menu(
                'Dữ liệu',
                $classItems,
                str_contains($pathInfor, '/hcore/object/'),
                '/bundles/pimcoreadmin/img/flat-color-icons/object.svg',
                true);
        }
        // $sidebarMenu[] = new Item\Title('Dữ liệu');
        // $sidebarMenu = array_merge($sidebarMenu, $classItems);

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

    public function renderTableValueFromField($element, string $field)
    {
        $function = 'get' . ucfirst($field);
        if (!method_exists($element, $function)) {
            return '';
        }
        $value = $element->{$function}();

        $fieldDefinition = $element->getClass()->getFieldDefinition($field);
        if ($fieldDefinition && !empty($value)) {
            if ($fieldDefinition instanceof Data\Select) {
                $fieldOptions = DataObject\Service::getOptionsForSelectField($element, $field);
                $value = $fieldOptions[$value];
            } elseif ($fieldDefinition instanceof Data\Datetime) {
                $value = $value->format('Y-m-d H:i');
            } elseif ($fieldDefinition instanceof Data\Date) {
                $value = $value->format('Y-m-d');
            }
        }

        if ($fieldDefinition instanceof Data\Checkbox) {
            return [
                'type' => 'checkbox',
                'data' => (bool) $value,
            ];
        }

        if (is_string($value) || is_numeric($value)) {
            return $value;
        }

        if ($value instanceof Image) {
            return [
                'type' => 'image',
                'data' => $this->getPreviewImagePath($value),
            ];
        }

        return '';
    }

    public function getPreviewImagePath($asset)
    {
        $id = $asset->getId();
        if (!$this->isSVG($asset)) {
            $path = '/admin/asset/get-image-thumbnail?id=' . $id . '&treepreview=1';
        } else {
            $path = '/admin/asset/get-asset?id=' . $id;
        }

        return $path;
    }

    private function getRoute(string $routePart, array $params = [])
    {
        return $this->router->generate(CoreService::getRoute($routePart), $params);
    }

    private function isSVG($asset)
    {
        if ($asset instanceof Image) {
            try {
                $fileExtension = $asset->getCustomSettings()['embeddedMetaData']['FileTypeExtension'];
            } catch (\Throwable $e) {
                $fileExtension = '';        
            }

            $mimetype = $asset->getMimeType();

            return $fileExtension == 'svg' || $mimetype == 'image/svg+xml';
        }

        return false;
    }
}