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
}
