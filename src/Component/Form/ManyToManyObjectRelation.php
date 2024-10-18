<?php

namespace HowMAS\CoreMSBundle\Component\Form;

use Pimcore\Model\DataObject;

class ManyToManyObjectRelation extends Input
{
    public function format()
    {
        if (!$this->value) {
            return null;
        }

        $values = [];
        foreach ($this->value as $value) {
            $values[] = DataObject::getById($value);
        }

        return $values;
    }

    public function formatEditable()
    {
        $values = $this->format();

        if (empty($values)) {
            return null;
        }

        $newValues = [];
        foreach ($values as $value) {
            $newValues[] = [
                'id' => $value->getId(),
                'type' => $value->getType(),
                'subtype' => $value->getClassName()
            ];
        }

        return $newValues;
    }
}
