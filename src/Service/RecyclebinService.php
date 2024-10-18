<?php

namespace HowMAS\CoreMSBundle\Service;

use Pimcore\Model\Element;
use Pimcore\Model\Element\Recyclebin;

class RecyclebinService
{
    public static function pushToBin($id, $type)
    {
        // try {
            $element = Element\Service::getElementById($type, $id);

            if ($element) {
                $list = $element::getList(['unpublished' => true]);
                $list->setCondition('`path` LIKE ' . $list->quote($list->escapeLike($element->getRealFullPath()) . '/%'));
                $children = $list->getTotalCount();

                if ($children <= 100) {
                    Recyclebin\Item::create($element, \Pimcore\Tool\Admin::getCurrentUser());
                }
            }
        // } catch (\Exception $e) {
        // }
    }
}
