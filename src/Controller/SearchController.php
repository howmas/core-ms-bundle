<?php

namespace HowMAS\CoreMSBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use HowMAS\CoreMSBundle\Service\ClassService;
use HowMAS\CoreMSBundle\Component\Form;
use HowMAS\CoreMSBundle\Model\HClass;
use HowMAS\CoreMSBundle\Service\RecyclebinService;
use HowMAS\CoreMSBundle\Service\EcommerceService;

/**
 * @Route("/search", name="search-")
 */
class SearchController extends BaseController
{
    /**
     * @Route("/search", name="search", methods={"POST"})
     */
    public function search()
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
