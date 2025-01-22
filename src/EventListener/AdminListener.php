<?php

namespace HowMAS\CoreMSBundle\EventListener;

use Pimcore\Bundle\AdminBundle\Event\IndexActionSettingsEvent;
use HowMAS\CoreMSBundle\Service\CoreService;

class AdminListener
{
    public function settings(IndexActionSettingsEvent $event)
    {
        // config
        $config = CoreService::getConfig();
        $access_core_names =
        isset($config['system']['admin']['access_core_names'])
            ? $config['system']['admin']['access_core_names']
            : [];

        $currentAdmin = \Pimcore\Tool\Admin::getCurrentUser();
        // if can not access to core admin UI
        if (!in_array($currentAdmin->getName(), $access_core_names)) {
            $event->setTemplate('@HowMASCoreMS/default/redirect.html.twig');
        }
    }
}
