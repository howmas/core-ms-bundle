<?php

namespace HowMAS\CoreMSBundle\Component\Form;

class Number extends Input
{
    public function format()
    {
        if (!$this->value) {
            return null;
        }

        return (int) str_replace(",", "", $this->value);
    }

    public function formatEditable()
    {
        return (string) $this->format();
    }
}
