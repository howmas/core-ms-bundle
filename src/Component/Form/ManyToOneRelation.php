<?php

namespace HowMAS\CoreMSBundle\Component\Form;

use Pimcore\Model\DataObject;

class ManyToOneRelation extends Input
{
    public function format()
    {
        if (!$this->value) {
            return null;
        }

        return DataObject::getById($this->value);
    }

    public function formatEditable()
    {
        $value = $this->format();

        if (!$value) {
            return null;
        }

        return [
            'id' => $value->getId(),
            'type' => $value->getType(),
            'subtype' => $value->getType()
        ];
    }

    public function getEditableType()
    {
        return "relation";
    }
}
