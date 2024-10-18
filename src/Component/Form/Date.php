<?php

namespace HowMAS\CoreMSBundle\Component\Form;

use Carbon\Carbon;

class Date extends Input
{
    public function format()
    {
        if (!$this->value) {
            return null;
        }

        return Carbon::createFromFormat('d/m/Y', $this->value);
    }

    public function formatEditable()
    {
        $value = $this->format();
        return $value ? $value->format('Y-m-d') : null;
    }
}
