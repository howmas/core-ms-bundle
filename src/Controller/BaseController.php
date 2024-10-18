<?php

namespace HowMAS\CoreMSBundle\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\RequestStack;
use Pimcore\Translation\Translator;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Component\Pager\PaginatorInterface;
use HowMAS\CoreMSBundle\Service\CoreService;

class BaseController extends FrontendController
{
    protected $request;
    protected $translator;
    protected $validator;
    protected $paginator;
    protected $isXmlHttpRequest;

    public function __construct(
        RequestStack $requestStack, 
        Translator $translator, 
        ValidatorInterface $validator,
        PaginatorInterface $paginator
        )
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->translator = $translator;

        if ($this->request->headers->has('locale')) {
            $this->translator->setLocale($this->request->headers->get('locale'));
        }

        // $this->validator = new Validator($validator, $this->translator);
        $this->paginator = $paginator;

        $this->isXmlHttpRequest = $this->request->isXmlHttpRequest();
    }

    /**
     * Return a success response.
     * 
     * @param array $response
     * 
     * @return JsonResponse
     */
    public function sendResponse($response = [])
    {
        return new JsonResponse($response, Response::HTTP_OK);
    }

    /**
     * Return an error response.
     * 
     * @param mix $error
     * @param int $statusCode
     * 
     * @return JsonResponse
     */
    public function sendError($error, $statusCode = Response::HTTP_BAD_REQUEST)
    {
        // logging if status code = 500
        if ($statusCode == Response::HTTP_INTERNAL_SERVER_ERROR) {
            
        } else {
            if (is_array($error)) {
                $error = [
                    "error" => $error
                ];
            }

            if (is_string($error)) {
                $error = [
                    "error" => [
                        "message" => $this->translator->trans($error)
                    ]
                ];
            }
        }

        return new JsonResponse($error, $statusCode);
    }

    /**
     * Paginator helper.
     */
    public function paginator($listing, $page, $limit)
    {
        $pagination = $this->paginator->paginate(
            $listing,
            $page,
            $limit,
        );

        return $pagination;
    }

    /**
     * Assign language to request.
     */
    public function setLocaleRequest()
    {
        if ($this->request->get('_locale')) {
            $this->request->setLocale($this->request->get('_locale'));
        }
    }

    public function validateRequest(array $condition)
    {
        // return $this->validator->validate($condition, $this->request);
    }

    public function view(array $params = [], $template = null)
    {
        if (!$template) {
            $trace = debug_backtrace();
            $call = $trace[1];

            $classes = explode("\\", $call['class']);
            $class = strtolower(str_replace('Controller', '', end($classes)));
            $function = strtolower(str_replace('Action', '', $call['function']));

            $template = "/$class/$function.html.twig";
        }

        return $this->render("@HowMASCoreMS$template", $params);
    }

    public function goView(string $routePart, array $params = [])
    {
        return $this->redirectToRoute(CoreService::getRoute($routePart), $params);
    }
}
