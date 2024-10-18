<?php

namespace HowMAS\CoreMSBundle\Component\Form;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

class DateRange extends Input
{
    public function format()
    {
        if (!$this->value) {
            return null;
        }

        $dates = explode(' - ', $this->value);

        if (count($dates) !== 2) {
            return null;
        }

        $startDate = Carbon::createFromFormat('d/m/Y', $dates[0]);
        $endDate = Carbon::createFromFormat('d/m/Y', $dates[1]);

        if ($endDate->greaterThanOrEqualTo($startDate)) {
            return CarbonPeriod::create($startDate, $endDate);
        }

        return null;    
    }
}
