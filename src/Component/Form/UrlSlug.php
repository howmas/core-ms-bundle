<?php

namespace HowMAS\CoreMSBundle\Component\Form;

use Pimcore\Model\DataObject\Data;

class UrlSlug extends Input
{
    public function format()
    {
        if (!$this->value) {
            return null;
        }

        $value = new Data\UrlSlug($this->value);
        return [$value];
    }
}
