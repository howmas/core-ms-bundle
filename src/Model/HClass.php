<?php

namespace HowMAS\CoreMSBundle\Model;

use Pimcore\Model\AbstractModel;
use Pimcore\Model\DataObject;
use Pimcore\Model\Document;
use Pimcore\Model\Asset\Image;
use Pimcore\Model\Exception\NotFoundException;

class HClass extends AbstractModel
{
    public ?string $id = null;
    public ?string $name = null;
    public ?string $title = null;
    public ?string $icon = null;
    public ?bool $active = false;
    public ?string $layout = null;
    public ?string $fields = null;
    public ?string $gridFields = null;
    public ?string $keyField = null;

    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setIcon(?string $icon): void
    {
        $this->icon = $icon;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setActive(?bool $active): void
    {
        $this->active = $active;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setLayout(?string $layout): void
    {
        $this->layout = $layout;
    }

    public function getLayout(): ?string
    {
        return $this->layout;
    }

    public function setFields(?string $fields): void
    {
        $this->fields = $fields;
    }

    public function getFields(): ?string
    {
        return $this->fields;
    }

    public function setGridFields(?string $gridFields): void
    {
        $this->gridFields = $gridFields;
    }

    public function getGridFields(): ?string
    {
        return $this->gridFields;
    }

    public function setKeyField(?string $keyField): void
    {
        $this->keyField = $keyField;
    }

    public function getKeyField(): ?string
    {
        return $this->keyField;
    }

    public static function getById(string $id): ?self
    {
        try {
            $obj = new self;
            $obj->getDao()->getById($id);
            return $obj;
        }
        catch (NotFoundException $ex) {
            \Pimcore\Logger::warn("HClass with id $id not found");
        }

        return null;
    }

    public static function getByName(string $name): ?self
    {
        try {
            $obj = new self;
            $obj->getDao()->getByName($name);
            return $obj;
        }
        catch (NotFoundException $ex) {
            \Pimcore\Logger::warn("HClass with name $name not found");
        }

        return null;
    }

    public static function getOrCreate(string $id, string $name): ?self
    {
        try {
            $obj = self::getById($id);

            if (!$obj) {
                $obj = new self;
                $obj->setId($id);
                $obj->setName($name);

                $obj->save(true);
            }

            return $obj;
        }
        catch (NotFoundException $ex) {
            \Pimcore\Logger::warn("HClass can not get or create");
        }

        return null;
    }
}