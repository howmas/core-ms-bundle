<?php

namespace HowMAS\CoreMSBundle\Component\Form;

use Pimcore\Model\Asset;

class Image extends Input
{
    public function format()
    {
        if (!$this->value) {
            return null;
        }

        return Asset\Image::getById($this->value);
    }

    public function formatEditable()
    {
        $value = $this->format();
        return $value ? [
            'id' => $value->getId(),
            "alt" => "",
            "cropPercent" => false,
            "cropWidth" => 0.0,
            "cropHeight" => 0.0,
            "cropTop" => 0.0,
            "cropLeft" => 0.0,
            "hotspots" => [],
            "marker" => [],
            "thumbnail" => null
        ] : null;
    }
}
