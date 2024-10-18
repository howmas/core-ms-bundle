<?php

namespace HowMAS\CoreMSBundle\Component\Form;

use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Data;

class Video extends Input
{
    const TYPE_DELIMITER = '***';
    const ALLOW_TYPES = ['asset', 'youtube'];

    protected $type;
    protected $data;
    protected $editablePath;

    public function __construct(
        protected $value
    )
    {
        $this->type = null;
        $this->data = null;
        $this->editablePath = '';

        $this->convert();

        parent::__construct($value);
    }

    public function format()
    {
        if (!$this->type || !$this->data) {
            return null;
        }

        $value = new Data\Video();
        $value->setType($this->type);
        $value->setData($this->data);

        return $value;
    }

    public function formatEditable()
    {
        $arguments = func_get_args();
        $editable = $arguments[0];

        return [
            'id' => $this->data,
            'type' => $this->type ?: $editable?->getType(),
            'allowedTypes' => self::ALLOW_TYPES,
            'title' => $editable?->getTitle(),
            'description' => $editable?->getDescription(),
            'path' => $this->editablePath,
            'poster' => (string) $editable?->getPoster(),
        ];
    }

    private function convert()
    {
        if (!$this->value) {
            return;
        }

        list($this->type, $id) = explode(self::TYPE_DELIMITER, $this->value);
        if ($this->type == 'asset') {
            $this->data = Asset\Video::getById((int) $id);
            $this->editablePath = $this->data?->getPath();
        }
        if ($this->type == 'youtube') {
            $this->data = $id;
            $this->editablePath = $id;
        }
    }
}
