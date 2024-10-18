<?php

namespace HowMAS\CoreMSBundle\Component\Form;

use Pimcore\Model\DataObject\Data;

class Link extends Input
{
    public function format()
    {
        if (!$this->value) {
            return null;
        }

        $value = new Data\Video();
        $value->setPath($this->value);
        // $value->setText("text");
        // $value->setTitle("title");

        return $value;
    }
}
