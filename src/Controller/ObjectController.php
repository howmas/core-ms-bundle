<?php

namespace HowMAS\CoreMSBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use HowMAS\CoreMSBundle\Service\ClassService;
use HowMAS\CoreMSBundle\Component\Form;
use HowMAS\CoreMSBundle\Model\HClass;
use HowMAS\CoreMSBundle\Service\RecyclebinService;
use HowMAS\CoreMSBundle\Service\EcommerceService;

/**
 * @Route("/object", name="object-")
 */
class ObjectController extends BaseController
{
    const SORT_DEFAULT = 'desc';
    const SORT_RAND = 'rand';

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
        $title = $hClass->getTitle();
        $data['title'] = $title;
        $data['layoutPageTitle'] = $title;

        // check ecommerce layout: Category
        $category = EcommerceService::getCategoryClassName();
        if ($category && $hClass->getName() == $category) {
            $data['categoryFolder'] = EcommerceService::getCategoryFolder();

            if ($this->request->getMethod() == $this->request::METHOD_POST) {
                $treeData = $this->request->get('data');
                foreach ($treeData as $treeItem) {
                    EcommerceService::updateCategoryTree($treeItem['child'], $treeItem['parent']);
                }

                return $this->sendResponse();
            }

            return $this->view($data, "/ecommerce/category/listing.html.twig");
        }

        $data['headers'] = array_merge([['title' => 'Tên gợi nhớ', 'name' => 'key']], json_decode($hClass->getGridFields(), true));

        $listing = ClassService::getList($hClass);

        // config
        $config = $this->getConfig();
        $queryString =
        isset($config['object']['listing']['condition'][$classId]['query_string'])
            ? $config['object']['listing']['condition'][$classId]['query_string']
            : null;

        // search
        $search = $this->request->get('search');
        $data['search'] = $search;
        if ($search) {
            $queryString = ($queryString ? $queryString . " AND " : "") . "searchData LIKE :search";
        }

        if (!empty($queryString)) {
            $conditionArray =
            isset($config['object']['listing']['condition'][$classId]['condition_array'])
                ? $config['object']['listing']['condition'][$classId]['condition_array']
                : [];

            // search
            if ($search) {
                $conditionArray['search'] = "%" . $search . "%";
            }

            $listing->setCondition($queryString, $conditionArray);
        }

        // sort
        $order =
        isset($config['object']['listing']['condition'][$classId]['order'])
            ? $config['object']['listing']['condition'][$classId]['order']
            : 'desc';
        if ($order == self::SORT_RAND) {
            $listing->setOrderKey("RAND()", false);
        } else {
            $orderKey =
            isset($config['object']['listing']['condition'][$classId]['orderKey'])
                ? $config['object']['listing']['condition'][$classId]['orderKey']
                : 'id';
            $listing->setOrderKey($orderKey);
            $listing->setOrder($order);
        }
            
        // $listing->setLocale($request->get('_locale', \Pimcore\Tool::getDefaultLanguage()));
        $listing->setUnpublished(true);

        $pagination = $this->paginator($listing, $this->request->get('page', 1), $this->request->get('limit', 10));
        $data['pagination'] = $pagination;
        $data['data'] = $pagination->getItems();

        $data['count'] = $listing->count();
        // $data['data'] = $listing->getData();

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
        $title = $hClass->getTitle();
        $data['title'] = $title;
        $data['item'] = $item;

        $method = $this->request->getMethod();
        if ($method == $this->request::METHOD_GET) {
            $keyField = $hClass->getKeyField();
            $key = $item->{'get' . ucfirst($keyField)}();
            $data['key'] = $key;
            $data['layoutPageTitle'] = "[$title] $key";

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
            $action = $this->request->get('action');

            if (!$action) {
                $item->setPublished(!$item->getPublished());
            } else {
                if ($action == 'lock') {
                    $item->setLocked(true);
                } else if ($action == 'unlock') {
                    $item->setLocked(false);
                } else {
                    return $this->sendResponse();
                }
            }

            $item->save();

            return $this->sendResponse();
        } elseif ($method == $this->request::METHOD_DELETE) {
            if ($item->getLocked()) {
                return $this->sendError('Không thể xóa');
            }

            RecyclebinService::pushToBin($id, 'object');
            $item->delete();

            return $this->sendResponse();
        }
    }

    /**
     * @Route("/create/{classId}", name="create", methods={"POST"})
     */
    public function create($classId)
    {
        $hClass = HClass::getById($classId);
        if (!$hClass?->getActive()) {
            $redirect = $this->redirectToRoute('hcore-index');
            $url = $redirect->getTargetUrl();

            return $this->sendResponse(compact('url'));
        }

        $key = $this->request->get('key');
        if (!$key) {
            return $this->sendError('Tên gợi nhớ không hợp lệ');
        }

        $existKey = ClassService::getByKey($hClass, $key);
        if ($existKey) {
            return $this->sendError('Tên gợi nhớ đã tồn tại');
        }

        $item = ClassService::create($hClass, $key);
        if ($item) {
            $id = $item->getId();
            $redirect = $this->redirectToRoute('hcore-object-detail', compact('classId', 'id'));
            $url = $redirect->getTargetUrl();

            return $this->sendResponse(compact('url'));
        }

        return $this->sendError('Không thể tạo mới, hãy thử lại!');
    }
}
