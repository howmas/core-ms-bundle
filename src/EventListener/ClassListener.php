<?php

namespace HowMAS\CoreMSBundle\EventListener;

use Pimcore\Event\Model\DataObject\ClassDefinitionEvent;
use Pimcore\Event\Model\DataObject\FieldcollectionDefinitionEvent;
use HowMAS\CoreMSBundle\Service\ClassService;

class ClassListener
{
    public function postUpdate(ClassDefinitionEvent $event): void
    {
        $classDefinition = $event->getClassDefinition();
        if ($classDefinition) {
            ClassService::init($classDefinition);
        }
    }

    public function postDelete(ClassDefinitionEvent $event): void
    {
        $classDefinition = $event->getClassDefinition();
        if ($classDefinition) {
            ClassService::delete($classDefinition);
        }
    }

    public function postUpdateField(FieldcollectionDefinitionEvent $event): void
    {
    }

    public function postDeleteField(FieldcollectionDefinitionEvent $event): void
    {

    }
}
