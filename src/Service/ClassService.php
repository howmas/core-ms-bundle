<?php

namespace HowMAS\CoreMSBundle\Service;

use Pimcore\Db;
use Pimcore\Model\DataObject\ClassDefinition\Data\Localizedfields;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Fieldcollection\Definition;
use HowMAS\CoreMSBundle\Model\HClass;

class ClassService
{
    public static function listing()
    {
        $listing = DatabaseService::listingClass();
        return $listing;
    }

    public static function getList($hClass)
    {
        $className = '\\Pimcore\\Model\\DataObject\\' . ucfirst($hClass->getName());
        return call_user_func_array($className . '::getList', []);
    }

    public static function getById($hClass, $id)
    {
        $className = '\\Pimcore\\Model\\DataObject\\' . ucfirst($hClass->getName());
        return call_user_func_array($className . '::getById', [(int) $id]);
    }

    public static function getLayout($classDefinition, $isFieldCollection = false)
    {
        $layoutDefinitions = $classDefinition->getLayoutDefinitions();
        $tabPanels = $layoutDefinitions->children;

        if (empty($tabPanels)) {
            return null;
        }

        $layout = [];
        $tabPanel = reset($tabPanels);

        if (!$isFieldCollection) {
            foreach ($tabPanel->getChildren() as $panel) {
                $panelItem = [
                    'title' => $panel->getTitle() ?: $panel->getName(),
                    'icon' => method_exists($panel, 'getIcon') ? $panel->getIcon() : '',
                    'fields' => [],
                ];

                if (method_exists($panel, 'getChildren')) {
                    foreach ($panel->getChildren() as $key => $definition) {
                        if (method_exists($definition, 'jsonSerialize')) {
                            $panelItem['fields'][] = $definition?->jsonSerialize();
                        }
                    }
                }

                $layout[] = $panelItem;
            }
        } else {
            $panelItem = [];

            if (method_exists($tabPanel, 'getChildren')) {
                foreach ($tabPanel->getChildren() as $key => $definition) {
                    if (method_exists($definition, 'jsonSerialize')) {
                        $panelItem[] = $definition?->jsonSerialize();
                    }
                }
            }

            $layout = $panelItem;
        }
            

        return $layout;
    }

    private static function getConstructFromDefinition($fieldDefinitions, $construct = [])
    {
        foreach ($fieldDefinitions as $name => $definition) {
            if ($definition instanceof Localizedfields) {
                $construct = self::getConstructFromDefinition($definition->getChildren(), $construct);
            } else {
                $construct[] = $definition->jsonSerialize();
            }
        }

        return $construct;
    }

    // setup
    public static function init($classDefinition): void
    {
        if ($classDefinition instanceof ClassDefinition) {
            $hClass = HClass::getOrCreate($classDefinition->getId(), $classDefinition->getName());

            if ($hClass) {
                $fieldDefinitions = $classDefinition->getFieldDefinitions();
                $fields = self::getConstructFromDefinition($fieldDefinitions);
                $gridFields = array_filter($fields, fn($e) => $e['visibleGridView']);
                $layout = self::getLayout($classDefinition);

                $hClass->setIcon($classDefinition->getIcon());
                $hClass->setTitle($classDefinition->getTitle() ?: $classDefinition->getName());
                $hClass->setLayout(json_encode($layout));
                $hClass->setFields(json_encode($fields));
                $hClass->setGridFields(json_encode($gridFields));

                // key field: input + indexed
                $inputIndexedFields = array_filter($fields, fn($e) => $e['index'] && $e['fieldtype'] == 'input');
                $keyField = empty($inputIndexedFields) ? 'key' : reset($inputIndexedFields)['name'];
                $hClass->setKeyField($keyField);

                $hClass->save();
            }
        }
    }

    public static function initFieldCollection($classDefinition)
    {
        // if ($classDefinition instanceof Definition) {
        //     $hClass = HClass::getOrCreate('field_' . $classDefinition->getKey(), $classDefinition->getKey());

        //     if ($hClass) {
                $fieldDefinitions = $classDefinition->getFieldDefinitions();
                $fields = self::getConstructFromDefinition($fieldDefinitions);
                $gridFields = array_filter($fields, fn($e) => $e['visibleGridView']);
                $layout = self::getLayout($classDefinition, true);

                return $layout;

        //         $hClass->setIcon($classDefinition->getIcon());
        //         $hClass->setTitle($classDefinition->getTitle() ?: $classDefinition->getName());
        //         $hClass->setLayout(json_encode($layout));
        //         $hClass->setFields(json_encode($fields));
        //         $hClass->setGridFields(json_encode($gridFields));

        //         // key field: input + indexed
        //         $inputIndexedFields = array_filter($fields, fn($e) => $e['index'] && $e['fieldtype'] == 'input');
        //         $keyField = empty($inputIndexedFields) ? 'key' : reset($inputIndexedFields)['name'];
        //         $hClass->setKeyField($keyField);

        //         $hClass->save();
        //     }
        // }
    }

    public static function delete($classDefinition): void
    {
        if ($classDefinition instanceof ClassDefinition) {
            $hClass = HClass::getById($classDefinition->getId());

            if ($hClass) {
                $hClass->delete();
            }
        }
    }
}
