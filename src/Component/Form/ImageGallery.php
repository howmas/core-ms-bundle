<?php

namespace HowMAS\CoreMSBundle\Component\Form;

use Pimcore\Model\Asset\Image;
use Pimcore\Model\DataObject\Data\Hotspotimage;

class ImageGallery extends Input
{
    public function format()
    {
        if (!$this->value || !is_array($this->value) || empty($this->value)) {
            return null;
        }

        $items = [];
        foreach ($this->value as $value) {
            $image = Image::getById($value);
            if ($image) {
                $advancedImage = new Hotspotimage();
                $advancedImage->setImage($image);
                $items[] = $advancedImage;
            }
        }

        return new \Pimcore\Model\DataObject\Data\ImageGallery($items);
    }
}
