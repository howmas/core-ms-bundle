<?php

namespace HowMAS\CoreMSBundle\Component\Form;

class Email extends Input
{
    public function format()
    {
        return filter_var($this->value, FILTER_VALIDATE_EMAIL) ? $this->value : null;
    }
}
