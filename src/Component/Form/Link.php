<?php

namespace HowMAS\CoreMSBundle\Component\Form;

use Pimcore\Model\Document;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Data;

class Link extends Input
{
    public function format()
    {
        if (!$this->value) {
            return null;
        }

        $value = new Data\Link();
        $value->setPath($this->value);

        return $value;
    }

    public function formatEditable()
    {
        if (!$this->value) {
            return null;
        }

        $dataInternal = [
            "internalType" => null,
            "linktype" => "direct",
            "internal" => false,
            "internalId" => null,
        ];

        if ($element = Document::getByPath($this->value)) {
            $id = $element->getId();
            $dataInternal = [
                "internalType" => "document",
                "linktype" => "internal",
                "internal" => true,
                "internalId" => $id,
            ];
        }
        // elseif ($element = Asset::getByPath($this->value)) {
        //     $id = $element->getId();
        //     $dataInternal = [
        //         "internalType" => "asset",
        //         "linktype" => "internal",
        //         "internal" => true,
        //         "internalId" => $id,
        //     ];
        // }
        // elseif ($element = DataObject::getByPath($this->value)) {
        //     $id = $element->getId();
        //     $dataInternal = [
        //         "internalType" => "object",
        //         "linktype" => "internal",
        //         "internal" => true,
        //         "internalId" => $id,
        //     ];
        // }

        return array_merge($dataInternal, ['path' => $this->value]);
    }
}
