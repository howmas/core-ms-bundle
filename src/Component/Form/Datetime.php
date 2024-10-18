<?php

namespace HowMAS\CoreMSBundle\Component\Form;

use Carbon\Carbon;

class Datetime extends Input
{
    public function format()
    {
        if (!$this->value) {
            return null;
        }

        return Carbon::createFromFormat('d/m/Y H:i', $this->value);
    }
}
