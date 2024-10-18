<?php

namespace HowMAS\CoreMSBundle\Service;

class CoreService
{
    public static function getRoute(string $routePart)
    {
        return 'hcore-' . $routePart;
    }
}
