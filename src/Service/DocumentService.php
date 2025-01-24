<?php

namespace HowMAS\CoreMSBundle\Service;

use Pimcore\Tool;
use Symfony\Component\Intl\Languages;
use HowMAS\CoreMSBundle\Component\Sidebar\Item;
use Pimcore\Model\Document;

class DocumentService
{
    const DEFAULT_ROUTE_PART = 'document-detail';
    const DEFAULT_TITLE = 'Trang';

    private static function activeMenu($pathInfor)
    {
        return str_contains($pathInfor, '/hcore/document/');
    }

    public static function getSidebarMenu($router, $pathInfor, $userLocale = 'vi')
    {
        $root = Document::getById(1);
        $langDocuments = $root->getChildren();

        $documentRoute = $router->generate(CoreService::getRoute('document-pages'));

        $subMenus = [];
        $subMenus[] = new Item\SubLink(
            'Tất cả trang',
            $documentRoute,
            $documentRoute == $pathInfor,
            '/bundles/pimcoreadmin/img/flat-color-icons/file.svg',
            true,
        );

        foreach ($langDocuments as $langDocument) {
            // only Page or Link
            if (!(
                $langDocument instanceof Document\Page ||
                $langDocument instanceof Document\Link
            )) {
                continue;
            }

            // // publish
            // if (!$langDocument->getPublished()) {
            //     continue;
            // }

            // has children
            if (!$langDocument->hasChildren()) {
                continue;
            }

            // hompage (Root)
            $hompage = $langDocument instanceof Document\Link ? Document::getByPath($langDocument->getHref()) : $langDocument;
            if (!$hompage) {
                continue;
            }

            // main language
            $language = $langDocument->getProperties()['language']->getData();
            $languageName = \Locale::getDisplayName($language, $userLocale);

            $langRoute = $router->generate(CoreService::getRoute('document-pages'), ['lang' => $language]);

            $subMenus[] = new Item\SubLink(
                $languageName,
                $langRoute,
                $langRoute == $pathInfor,
                CoreService::getFlag($language),
                true,
            );
        }

        if (count($subMenus) > 2) {
            $menu = new Item\Menu(
                self::DEFAULT_TITLE,
                $subMenus,
                self::activeMenu($pathInfor),
                '/bundles/pimcoreadmin/img/flat-color-icons/text.svg',
                true,
            );
        } else {
            $menu = new Item\Link(
                self::DEFAULT_TITLE,
                $documentRoute,
                $documentRoute == $pathInfor,
                '/bundles/pimcoreadmin/img/flat-color-icons/file.svg',
                true,
            );
        }

        return $menu;
    }

    public static function getSidebarMenuDetail($router, $userLocale = 'vi')
    {
        $root = Document::getById(1);
        $langDocuments = $root->getChildren();

        $subMenus = [];
        foreach ($langDocuments as $langDocument) {
            // only Page or Link
            if (!(
                $langDocument instanceof Document\Page ||
                $langDocument instanceof Document\Link
            )) {
                continue;
            }

            // publish
            if (!$langDocument->getPublished()) {
                continue;
            }

            // has children
            if (!$langDocument->hasChildren()) {
                continue;
            }

            // hompage (Root)
            $hompage = $langDocument instanceof Document\Link ? Document::getByPath($langDocument->getHref()) : $langDocument;
            if (!$hompage) {
                continue;
            }

            // main language
            $language = $langDocument->getProperties()['language']->getData();
            $languageName = \Locale::getDisplayName($language, $userLocale);

            // children
            $subLinks = [];
            $subLinks[] = new Item\SubLink(
                self::getName($hompage), 
                $router->generate(CoreService::getRoute(self::DEFAULT_ROUTE_PART), [
                    'id' => $hompage->getId(),
                ]),
                self::DEFAULT_ICON
            );

            $documentOfLangs = $langDocument->getChildren();
            foreach ($documentOfLangs as $documentOfLang) {
                // only Page or Snippet
                if (!(
                    $documentOfLang instanceof Document\Page ||
                    $documentOfLang instanceof Document\Snippet
                )) {
                    continue;
                }

                $subLinks[] = new Item\SubLink(
                    self::getName($documentOfLang), 
                    $router->generate(CoreService::getRoute(self::DEFAULT_ROUTE_PART), [
                        'id' => $documentOfLang->getId(),
                    ]),
                    self::DEFAULT_ICON
                );
            }

            $subMenus[] = new Item\SubMenu($languageName, $subLinks, self::DEFAULT_ICON);
        }

        $menu = new Item\Menu(self::DEFAULT_TITLE, $subMenus, self::DEFAULT_ICON);

        return $menu;
    }

    public static function getName($document)
    {
        return $document->getId() !== 1 ? $document->getKey() : $document->getProperties()['navigation_name']->getData();
    }
}
