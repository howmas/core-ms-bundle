<?php

namespace HowMAS\CoreMSBundle\Component\Form;

use Pimcore\Model\DataObject;

class FieldCollection extends Input
{
    public function format()
    {
        if (!$this->value) {
            return null;
        }

        $allowType = $this->value['allowType'];
        $itemCount = $this->value['itemCount'];

        if (!$allowType || !$itemCount) {
            return null;
        }

        unset($this->value['allowType']);
        unset($this->value['itemCount']);

        $items = new DataObject\Fieldcollection();

        for ($i = 0; $i < $itemCount; $i++) { 
            $fieldCollectionData = "\\Pimcore\\Model\\DataObject\\Fieldcollection\\Data\\" . ucfirst($allowType);
            $item = new $fieldCollectionData;

            foreach ($this->value as $type => $fieldValues) {
                $component = "\\HowMAS\\CoreMSBundle\\Component\\Form\\" . ucfirst($type);

                if (class_exists($component)) {
                    foreach ($fieldValues as $field => $values) {
                        $value = $values[$i];
                        $form = new $component($value);

                        $fieldAndLanguage = explode('_', $field);
                        $field = $fieldAndLanguage[0];
                        $language = isset($fieldAndLanguage[1]) ? $fieldAndLanguage[1] : null;

                        $item->{'set' . ucfirst($field)}($form->format(), $language);
                    }
                }
            }

            $items->add($item);
        }

        return $items;
    }
}
