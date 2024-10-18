<?php

namespace HowMAS\CoreMSBundle\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use HowMAS\CoreMSBundle\Service\ClassService;
use HowMAS\CoreMSBundle\Model\HClass;
use Pimcore\Model\Document\Editable;
use Pimcore\Model\DataObject;

class FormExtension extends AbstractExtension
{
    /**
     * @inheritDoc
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('hcore_form_relation_config', [$this, 'getRelationConfig']),
            new TwigFunction('hcore_form_relation_option_class', [$this, 'getRelationGetOptionsFromClass']),
            new TwigFunction('hcore_form_editable_data', [$this, 'getEditableData']),
            new TwigFunction('hcore_form_video_convert', [$this, 'convertDelimiterVideo']),
            new TwigFunction('hcore_form_fieldcollection', [$this, 'getFieldCollectionLayout']),
            new TwigFunction('hcore_form_fieldcollection_init', [$this, 'getFieldCollectionItemInit']),
            new TwigFunction('hcore_form_check_type', [$this, 'checkType']),
        ];
    }

    public function getRelationConfig(array $definition)
    {
        $config = [
            'classes' => [],
            'assetTypes' => [],
            'documentTypes' => [],
        ];
        if ($definition) {
            if ($definition['objectsAllowed']) {
                $config['classes'] = $definition['classes'];
            }
        }

        return $config;
    }

    public function getRelationGetOptionsFromClass(array $classes)
    {
        $options = [];
        if (!empty($classes)) {
            foreach ($classes as $class) {
                $hClass = HClass::getByName($class);

                if ($hClass) {
                    $listing = ClassService::getList($hClass);
                    $listing->setUnpublished(true);
                    $data = $listing->getData();
                    
                    $options[] = [
                        'class' => $hClass,
                        'data' => $data,
                    ];
                }  
            }
        }

        return $options;
    }

    public function getEditableData($item, $name)
    {
        $editable = $item->getEditable($name);

        if (!$editable) {
            return null;
        }

        if ($editable instanceof Editable\Image) {
            return $editable->getImage();
        } elseif ($editable instanceof Editable\Relation) {
            return $editable->getElement();
        } elseif ($editable instanceof Editable\Relations) {
            return $editable->getElements();
        } elseif ($editable instanceof Editable\Checkbox) {
            return $editable->isChecked();
        } elseif ($editable instanceof Editable\Video) {
            
            $videoType = $editable->getVideoType();
            $video = $videoType == "youtube" ? $editable->getId() : $editable->getVideoAsset();
            return compact('videoType', 'video');
        }

        return $editable->getData();
    }

    public function convertDelimiterVideo($type, $data = '')
    {
        $delimiter = \HowMAS\CoreMSBundle\Component\Form\Video::TYPE_DELIMITER;

        return $type . $delimiter . $data;
    }

    public function getFieldCollectionLayout(array $definition)
    {
        $allowedTypes = $definition['allowedTypes'];

        if (empty($allowedTypes)) {
            return null;
        }

        foreach ($allowedTypes as $allowedType) {
            $class = '\\Pimcore\\Model\\DataObject\\Fieldcollection\\Data\\' . ucfirst($allowedType);
            $field = new $class;

            // dd($field->getDefinition());

            return ClassService::initFieldCollection($field->getDefinition());

            // $fieldDefinition = $field->getDefinition();
            // dd($fieldDefinition->getFieldDefinitions());
        }
    }

    public function checkType($definition)
    {
        if (is_object($definition)) {
            if ($definition instanceof DataObject\ClassDefinition\Data) {
                $class = get_class($definition);
                $classes = explode("\\", $class);
                $type = end($classes);

                return strtolower($type);
            }
        }

        return null;
    }

    public function getFieldCollectionItemInit($allowedType)
    {
        $class = '\\Pimcore\\Model\\DataObject\\Fieldcollection\\Data\\' . ucfirst($allowedType);
        $field = new $class;

        return $field;
    }
}