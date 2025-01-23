<?php

namespace HowMAS\CoreMSBundle\Service;

use Pimcore\Model\DataObject;

class EcommerceService extends CoreService
{
    const TREE_OPTION = "{
        'animation': 150,
        'group': 'listGroup'
    }";

    public static function enable()
    {
        $config = self::getConfig();

        return isset($config['ecommerce']['enable']) && $config['ecommerce']['enable'];
    }

    public static function getCategoryClassName()
    {
        $config = self::getConfig();

        $category = self::enable() && isset($config['ecommerce']['classes']['category']) && $config['ecommerce']['classes']['category'] ? $config['ecommerce']['classes']['category'] : null;

        return $category;
    }

    public static function getClassnames()
    {
        $config = self::getConfig();

        $category = isset($config['ecommerce']['classes']['category']) && $config['ecommerce']['classes']['category'] ? [$config['ecommerce']['classes']['category']] : [];
        $products = isset($config['ecommerce']['classes']['products']) && $config['ecommerce']['classes']['products'] ? $config['ecommerce']['classes']['products'] : [];
        $order = isset($config['ecommerce']['classes']['order']) && $config['ecommerce']['classes']['order'] ? [$config['ecommerce']['classes']['order']] : [];

        return array_merge($category, $products, $order);
    }

    public static function getCategoryFolder()
    {
        $categoryClassname = self::getCategoryClassName();
        if (empty($categoryClassname)) {
            return null;
        }

        $categoryFolder = DataObject\Folder::getByPath("/$categoryClassname");
        return $categoryFolder;
    }

    public static function updateCategoryTree($child, $parent)
    {
        $categoryClassname = self::getCategoryClassName();
        if (!empty($categoryClassname)) {
            $child = DataObject::getById($child);
            $parent = DataObject::getById($parent);

            if ($child->getClassName() == $categoryClassname && $parent && $child->getParent()->getId() !== $parent->getId()) {
                $child->setParent($parent);
                $child->save();
            }
        }
    }
}
