<?php

namespace HowMAS\CoreMSBundle\EventListener;

use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Twig\Environment;
use Pimcore\Bundle\AdminBundle\Security\CsrfProtectionHandler;

class ControllerListener
{
    const LOADING_CLASS = 'hcore-loading-click';

    public function __construct(
        protected Environment $twig,
        protected CsrfProtectionHandler $csrfProtection
    ) {
    }

    public function onKernelController(ControllerEvent $event): void
    {
        try {
            $isHcoreRequest = isset($event->getController()[0])
                && $event->getController()[0] instanceof \HowMAS\CoreMSBundle\Controller\BaseController;
            if (!$isHcoreRequest) {
                return;
            }
        } catch (\Throwable $e) {
            return;
        }

        $request = $event->getRequest();
        $hcoreUser = \Pimcore\Tool\Admin::getCurrentUser();
        $this->twig->addGlobal('hcoreUser', $hcoreUser);
        $hcoreUserToken = $this->csrfProtection->getCsrfToken($request->getSession());
        $this->twig->addGlobal('hcoreUserToken', $hcoreUserToken);

        $this->twig->addGlobal('HCoreLayout', "@HowMASCoreMS/layout/layout.html.twig");
        $this->twig->addGlobal('HCoreFormLayout', "@HowMASCoreMS/layout/form/layout.html.twig");
        $this->twig->addGlobal('HCoreFormSelectLayout', "@HowMASCoreMS/layout/form/select/layout.html.twig");
        $this->twig->addGlobal('HCoreAssets', "/bundles/howmascorems/theme/assets");

        // click class to listing asset by ajax
        $this->twig->addGlobal('hCoreAssetListing', "hcore-asset-listing");
        // click class to show asset library modal
        $this->twig->addGlobal('hCoreAssetOpenModal', "hcore-asset-open-modal");
        // click class to show asset library modal with only video type
        $this->twig->addGlobal('hCoreAssetVideoOpenModal', "hcore-asset-video-open-modal");

        // base layout of form
        $basePaths = [
            'inputBasePath' => '@HowMASCoreMS/layout/form/input/base.html.twig',
            'imageBasePath' => '@HowMASCoreMS/layout/form/image/base.html.twig',
            'dateBasePath' => '@HowMASCoreMS/layout/form/date/base.html.twig',
            'selectBasePath' => '@HowMASCoreMS/layout/form/select/base.html.twig',
        ];

        foreach ($basePaths as $key => $value) {
            $this->twig->addGlobal($key, $value);
        }

        $domain = \Starfruit\BuilderBundle\Tool\SystemTool::getDomain();
        $this->twig->addGlobal('domain', $domain);

        $appData = [
            'appName' => 'HowMAS',
            'appBundle' => '@HowMASCoreMS',
            'appLogo' => '/bundles/howmascorems/img/logo.jpg',
            'appLogoMini' => '/bundles/howmascorems/img/logo1x1.png',
        ];

        foreach ($appData as $key => $value) {
            $this->twig->addGlobal($key, $value);
        }

        $formClassScript = "const hcoreUserToken = '$hcoreUserToken'; ";
        $formClass = [
            'hcoreMultiLangCls' => 'hcore-localizedfields',
            'hcoreInputCls' => 'hcore-input',
            'hcoreTextareaCls' => 'hcore-textarea',
            'hcoreWysiwygCls' => 'hcore-wysiwyg',
            'hcorePasswordCls' => 'hcore-password',
            'hcoreNumberCls' => 'hcore-number',
            'hcoreDateCls' => 'hcore-date',
            'hcoreDaterangeCls' => 'hcore-date-range',
            'hcoreDatetimeCls' => 'hcore-date-time',
            'hcoreSelectCls' => 'hcore-select',
            'hcoreMultiSelectCls' => 'hcore-multiselect',
            'hcoreCheckboxCls' => 'hcore-checkbox',
            'hcoreImageCls' => 'hcore-image',
            'hcoreImageGalleryCls' => 'hcore-image-gallery',
            'hcoreVideoCls' => 'hcore-video',
            'hcoreRelationCls' => 'hcore-relation',
            'hcoreRelationsCls' => 'hcore-relations',
            'hcoreFieldCollectionCls' => 'hcore-field-collection',
            'hcoreLinkCls' => 'hcore-link',
            'hcoreLoadingCls' => self::LOADING_CLASS,
        ];

        foreach ($formClass as $key => $value) {
            $this->twig->addGlobal($key, $value);
            $formClassScript .= "var $key = '.$value'; ";
        }

        $this->twig->addGlobal('hcoreBtnSaveId', 'hcore-btn-save');
        $this->twig->addGlobal('hcoreBtnChangePublishId', 'hcore-btn-change-publish');
        $this->twig->addGlobal('hcoreBtnDeleteId', 'hcore-btn-delete');

        // field collection: delimiter of FC `name` and name of each field in FC
        // dấu phân cách giữ tên trường FC và các trường con bên trong nó
        $fcDelimiter = '-';
        $this->twig->addGlobal('hcoreFieldCollectionDelimiter', $fcDelimiter);

        $formClassScript .= "var hcoreWysiwygContent = [];
            var hcoreBtnSaveId = '#hcore-btn-save';
            var hcoreBtnChangePublishId = '#hcore-btn-change-publish';
            var hcoreBtnDeleteId = '#hcore-btn-delete';
            var hcoreFieldCollectionDelimiter = '$fcDelimiter'";
        $this->twig->addGlobal('formClassScript', $formClassScript);
    }
}