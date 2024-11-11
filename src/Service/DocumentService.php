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

    public static function getSidebarMenu($router, $userLocale = 'vi')
    {
        $root = Document::getById(1);
        $langDocuments = $root->getChildren();

        $subMenus = [];
        $subMenus[] = new Item\SubLink(
            'Tất cả trang',
            $router->generate(CoreService::getRoute('document-pages')),
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

            $subMenus[] = new Item\SubLink(
                $languageName,
                $router->generate(CoreService::getRoute('document-pages'), ['lang' => $language]),
                CoreService::getFlag($language),
                true,
            );
        }

        $menu = new Item\Menu(self::DEFAULT_TITLE, $subMenus, '/bundles/pimcoreadmin/img/flat-color-icons/text.svg', true);

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
