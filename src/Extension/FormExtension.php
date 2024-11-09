<?php

namespace HowMAS\CoreMSBundle\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use HowMAS\CoreMSBundle\Service\ClassService;
use HowMAS\CoreMSBundle\Service\DocumentService;
use HowMAS\CoreMSBundle\Model\HClass;
use Pimcore\Model\Document;
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
            new TwigFunction('hcore_form_link_document_options', [$this, 'getDocumentOptionForLink']),
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

    public function getDocumentOptionForLink($document)
    {
        $language = $document->getProperties()['language']->getData();
        $langDocument = Document::getByPath("/$language");

        $options = [];
        if ($langDocument) {
            $hompage = $langDocument instanceof Document\Link ? Document::getByPath($langDocument->getHref()) : $langDocument;
            if ($hompage) {
                $name = DocumentService::getName($hompage);
                $link = $hompage->getFullPath();

                $options[] = [
                    'id' => $hompage->getId(),
                    'name' => $link . " ($name)",
                    'value' => $link,
                ];

                $documentOfLangs = $langDocument->getChildren();
                foreach ($documentOfLangs as $documentOfLang) {
                    // only Page or Snippet
                    if (!(
                        $documentOfLang instanceof Document\Page
                    )) {
                        continue;
                    }

                    $name = DocumentService::getName($documentOfLang);
                    $link = $documentOfLang->getPrettyUrl() ?: $documentOfLang->getFullPath();

                    $options[] = [
                        'id' => $documentOfLang->getId(),
                        'name' => $link . " ($name)",
                        'value' => $link,
                    ];
                }
            }
        }

        return $options;
    }
}