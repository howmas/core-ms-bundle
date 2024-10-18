<?php

namespace HowMAS\CoreMSBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Pimcore\Model\Document;
use Pimcore\Model\Document\Page;
use Starfruit\BuilderBundle\Controller\Document\EditableBaseController;

/**
 * @Route("/document", name="document-")
 */
class DocumentController extends BaseController
{
    /**
     * @Route("/listing/page", name="listing", methods={"POST", "GET"})
     */
    public function listingPage()
    {
        $hClass = HClass::getById($classId);

        if (!$hClass?->getActive()) {
            return null;
        }

        $data['classId'] = $classId;
        $data['title'] = $hClass->getTitle();
        $data['headers'] = json_decode($hClass->getGridFields(), true);

        $listing = ClassService::getList($hClass);
        // $listing->setLocale($request->get('_locale', \Pimcore\Tool::getDefaultLanguage()));
        $listing->setUnpublished(true);

        // $pagination = $this->paginator($listing, 1, 10);
        // $data['pagination'] = $pagination;
        // $data['data'] = $pagination->getItems();

        $data['count'] = $listing->count();
        $data['data'] = $listing->getData();

        return $this->view($data);
    }

    /**
     * @Route("/detail/{id}", name="detail", methods={"POST", "GET", "PUT", "DELETE"})
     */
    public function detail($id)
    {
        $item = Document::getById($id);

        if (!$item) {
            return null;
        }

        $method = $this->request->getMethod();
        if ($method == $this->request::METHOD_GET) {
            $controller = $item->getController();
            $controllerMethod = explode('::', $controller);
            list($class, $function) = $controllerMethod;
            $data = EditableBaseController::getEditablesFromClass($class, $function);

            $data['item'] = $item;

            return $this->view($data);
        } elseif ($method == $this->request::METHOD_POST) {
            $data = $this->request->get('data');
            $data = json_decode($data, true);

            $editables = $item->getEditables();

            if (!empty($data)) {
                foreach ($data as $type => $params) {
                    if (!empty($params)) {
                        foreach ($params as $field => $value) {
                            $component = "\\HowMAS\\CoreMSBundle\\Component\\Form\\" . ucfirst($type);
                            if (class_exists($component)) {
                                $editable = $item->getEditable($field);
                                if (!$editable) {
                                    $editable = new call_user_func("\\Pimcore\\Model\\Document\\Editable\\" . ucfirst($type));
                                    $editable->setDocument($item);
                                    $editable->setName($field);
                                }

                                $form = new $component($value);
                                $editable->setDataFromEditmode($form->formatEditable($editable));

                                $editables = array_merge($editables, [$field => $editable]);
                            }
                        }
                    }
                }  
            }

            $item->setEditables($editables);
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
