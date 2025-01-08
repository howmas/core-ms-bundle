<?php

namespace HowMAS\CoreMSBundle\Component\Form;

class Input
{
    public function __construct(
        protected $value
    )
    {}

    public function format()
    {
        return $this->value;
    }

    public function formatEditable()
    {
        return $this->format();
    }

    // lowercase class name
    public function getEditableType()
    {
        return strtolower(substr(strrchr(get_class($this), "\\"), 1));
    }
}
