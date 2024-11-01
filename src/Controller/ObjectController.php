<?php

namespace HowMAS\CoreMSBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use HowMAS\CoreMSBundle\Service\ClassService;
use HowMAS\CoreMSBundle\Component\Form;
use HowMAS\CoreMSBundle\Model\HClass;

/**
 * @Route("/object", name="object-")
 */
class ObjectController extends BaseController
{
    /**
     * @Route("/listing/{classId}", name="listing", methods={"POST", "GET"})
     */
    public function listing($classId)
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
     * @Route("/detail/{classId}/{id}", name="detail", methods={"POST", "GET", "PUT", "DELETE"})
     */
    public function detail($classId, $id)
    {
        $hClass = HClass::getById($classId);

        if (!$hClass?->getActive()) {
            return $this->goView('object-listing', compact('classId'));
        }

        $item = ClassService::getById($hClass, $id);

        if (!$item) {
            return $this->goView('object-listing', compact('classId'));
        }

        $data['classId'] = $classId;
        $data['title'] = $hClass->getTitle();
        $data['item'] = $item;

        $method = $this->request->getMethod();
        if ($method == $this->request::METHOD_GET) {
            $keyField = $hClass->getKeyField();
            $key = $item->{'get' . ucfirst($keyField)}();
            $data['key'] = $key;

            $layout = json_decode($hClass->getLayout(), true);
            $data['layout'] = $layout;

            return $this->view($data);
        } elseif ($method == $this->request::METHOD_POST) {
            $data = $this->request->get('data');
            $data = json_decode($data, true);

            if (!empty($data)) {
                foreach ($data as $type => $params) {
                    if (!empty($params)) {
                        foreach ($params as $field => $value) {
                            $component = "\\HowMAS\\CoreMSBundle\\Component\\Form\\" . ucfirst($type);
                            if (class_exists($component)) {
                                $form = new $component($value);

                                $fieldAndLanguage = explode('_', $field);
                                $field = $fieldAndLanguage[0];
                                $language = isset($fieldAndLanguage[1]) ? $fieldAndLanguage[1] : null;

                                $item->{'set' . ucfirst($field)}($form->format(), $language);
                            }
                        }
                    }
                }  
            }

            $item->save();

            // return $this->goView('object-detail', compact('classId', 'id'));
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
