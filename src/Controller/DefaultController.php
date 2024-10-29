<?php

namespace HowMAS\CoreMSBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;

use HowMAS\CoreMSBundle\Service\ClassService;

class DefaultController extends BaseController
{
    /**
     * @Route("/index", name="index")
     */
    public function indexAction()
    {
        $listing = ClassService::listing();
        return $this->view();
    }

    public function redirectAction()
    {
        return $this->redirectToRoute('hcore-index');
    }
}
