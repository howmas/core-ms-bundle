<?php

namespace HowMAS\CoreMSBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Pimcore\Model\Document;
use HowMAS\CoreMSBundle\Service\DocumentService;

/**
 * @Route("/document", name="document-")
 */
class DocumentController extends BaseController
{
    /**
     * @Route("/pages", name="pages", methods={"GET"})
     */
    public function pages()
    {
        $data['title'] = 'Trang';
        $data['headers'] = [
            [
                'title' => 'Name',
                'name' => 'name',
            ],
            [
                'title' => 'Language',
                'name' => 'language',
            ],
        ];

        $root = Document::getById(1);
        $langDocuments = $root->getChildren();

        $items = [];
        foreach ($langDocuments as $langDocument) {
            // only Page or Link
            if (!(
                $langDocument instanceof Document\Page
                || $langDocument instanceof Document\Link
            )) {
                continue;
            }

            // publish
            if (!$langDocument->getPublished()) {
                continue;
            }

            // has children
            if (!$langDocument->hasChildren()) {
                continue;
            }

            // hompage (Root)
            $hompage = $langDocument instanceof Document\Link ? Document::getByPath($langDocument->getHref()) : $langDocument;
            if (!$hompage) {
                continue;
            }

            // main language
            $userLocale = 'vi';
            $language = $langDocument->getProperties()['language']->getData();
            $languageName = \Locale::getDisplayName($language, $userLocale);

            $items[] = [
                'id' => $hompage->getId(),
                'name' => DocumentService::getName($hompage),
                'language' => $language,
                'languageName' => $languageName,
            ];

            $documentOfLangs = $langDocument->getChildren();
            foreach ($documentOfLangs as $documentOfLang) {
                // only Page or Snippet
                if (!(
                    $documentOfLang instanceof Document\Page
                    || $documentOfLang instanceof Document\Snippet
                )) {
                    continue;
                }

                $items[] = [
                    'id' => $documentOfLang->getId(),
                    'name' => DocumentService::getName($documentOfLang),
                    'language' => $language,
                    'languageName' => $languageName,
                ];
            }
        }
        $data['data'] = $items;

        return $this->view($data);
    }

    /**
     * @Route("/detail/{id}", name="detail", methods={"POST", "GET", "PUT", "DELETE"})
     */
    public function detail($id)
    {
        $item = Document::getById($id);

        if (!$item) {
            return $this->goView('document-pages');
        }

        // only Page or Snippet
        if (!(
            $item instanceof Document\Page ||
            $item instanceof Document\Snippet
        )) {
            return $this->goView('document-pages');
        }

        $method = $this->request->getMethod();
        if ($method == $this->request::METHOD_GET) {
            $controller = $item->getController();
            $controllerMethod = explode('::', $controller);
            list($class, $function) = $controllerMethod;
            $data = \Starfruit\BuilderBundle\Controller\Document\EditableBaseController::getEditablesFromClass($class, $function);

            $data['item'] = $item;

            return $this->view($data);
        } elseif ($method == $this->request::METHOD_POST) {
            $data = $this->request->get('data');
            $data = json_decode($data, true);

            // convert block type to single editable type
            $dataOfBlock = [];
            $block = !empty($data['fieldCollection']) ? $data['fieldCollection'] : [];
            if (!empty($block)) {
                foreach ($block as $blockField => $typeValues) {
                    $blockData = [];

                    $fieldName = $typeValues['allowType'];
                    $itemCount = $typeValues['itemCount'];
                    unset($typeValues['allowType']);
                    unset($typeValues['itemCount']);

                    for ($i = 0; $i < $itemCount; $i++) {
                        $key = $i + 1;
                        $blockData[] = (string) $key;
                        foreach ($typeValues as $type => $fieldValues) {
                            foreach ($fieldValues as $field => $values) {
                                $data[$type][$blockField .':'. $key .'.'. $field] = $values[$i];
                            }
                        }
                    }

                    $dataOfBlock[$blockField] = $blockData;
                }
            }

            $data['block'] = $dataOfBlock;
            unset($data['fieldCollection']);

            if (!empty($data)) {
                foreach ($data as $type => $params) {
                    if (!empty($params)) {
                        foreach ($params as $field => $value) {
                            $component = "\\HowMAS\\CoreMSBundle\\Component\\Form\\" . ucfirst($type);
                            if (class_exists($component)) {
                                $editable = $item->getEditable($field);
                                $form = new $component($value);
                                $item->setRawEditable($field, $type, $form->formatEditable($editable));
                            }
                        }
                    }
                }  
            }

            $item->save();
            return $this->sendResponse();
        } elseif ($method == $this->request::METHOD_PUT) {
            $item->setPublished(!$item->getPublished());
            $item->save();

            return $this->goView('object-detail', compact('classId', 'id'));
        } elseif ($method == $this->request::METHOD_DELETE) {
            $item->delete();

            return $this->goView('object-listing', compact('classId'));
        }
    }
}
