<?php

namespace HowMAS\CoreMSBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Pimcore\Model\Asset;
use Pimcore\Model\Asset\Folder;
use Pimcore\Model\Asset\Image;
use Pimcore\Model\Asset\Video;
use HowMAS\CoreMSBundle\Service\RecyclebinService;
use Starfruit\BuilderBundle\Tool\AssetTool;

/**
 * @Route("/asset", name="asset-")
 */
class AssetController extends BaseController
{
    /**
     * @Route("/listing", name="listing", methods={"GET"})
     */
    public function listing()
    {
        $assetId = (int) $this->request->get('assetId');
        $asset = Asset::getById($assetId);

        if ($asset) {
            $parent = $asset->getParent();
            $parentId = $parent->getId();
        } else {
            $parentId = (int) $this->request->get('parentId', 1);
            $parent = Folder::getById($parentId);
        }

        if (!$parent) {
            return null;
        }

        $condStr = "parentId = ?";
        $condArr = [$parentId];

        $selectedAsset = $asset;
        $listing = new Asset\Listing();
        $listing->setCondition($condStr, $condArr);
        $listing->setOrderKey(["creationDate", "mimetype", "path", "filename"]);
        $listing->setOrder(["DESC", "ASC", "ASC", "ASC"]);

        $listing = $listing->getData();
        $folders = array_filter($listing, fn($e) => $e instanceof Folder);
        $files = array_filter($listing, fn($e) => !($e instanceof Folder));

        $type = $this->request->get('type');
        if ($type == 'image') {
            $files = array_filter($files, fn($e) => $e instanceof Image);
        } else if ($type == 'video') {
            $files = array_filter($files, fn($e) => $e instanceof Video);
        }

        $folderCount = count($folders);
        $fileCount = count($files);

        $breadcrumbs = array_reverse($this->getBreadcrumb($parent));
        unset($breadcrumbs[0]);
        
        // check while using ckeditor
        $isCkeditor = (bool) $this->request->get('isCkeditor');

        if ($this->isXmlHttpRequest) {

            return $this->view(compact('parent', 'folders', 'files', 'folderCount', 'fileCount', 'breadcrumbs', 'selectedAsset', 'type', 'isCkeditor'), '/asset/modal-content.html.twig');
        }

        $showSetting = true;
        return $this->view(compact('parent', 'folders', 'files', 'folderCount', 'fileCount', 'breadcrumbs', 'showSetting', 'isCkeditor'));
    }

    private function getBreadcrumb($folder, $breadcrumb = [])
    {
        $breadcrumb[] = [
            'id' => $folder->getId(),
            'name' => $folder->getKey(),
        ];
        if ($folder->getParent() instanceof Folder) {
            $breadcrumb = $this->getBreadcrumb($folder->getParent(), $breadcrumb);
        }

        return $breadcrumb;
    }

    /**
     * @Route("/detail/{id}", name="detail", methods={"POST", "GET", "PUT", "DELETE"})
     */
    public function detail($id)
    {
        $id = (int) $id;
        $item = Asset::getById($id);

        if (!$item) {
            // code...
        }

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
                                $item->{'set' . ucfirst($field)}($form->format());
                            }
                        }
                    }
                }  
            }

            $item->save();

            return $this->goView('object-detail', compact('classId', 'id'));
        } elseif ($method == $this->request::METHOD_PUT) {
            $item->setPublished(!$item->getPublished());
            $item->save();

            return $this->goView('object-detail', compact('classId', 'id'));
        } elseif ($method == $this->request::METHOD_DELETE) {
            RecyclebinService::pushToBin($id, 'asset');
            $item->delete();

            return $this->sendResponse(compact('id'));
        }
    }

    /**
     * @Route("/create", name="create", methods={"POST"})
     */
    public function create()
    {
        $parent = Folder::getById((int) $this->request->get('parent'));
        if ($parent) {
            $parentPath = $parent->getFullPath();

            $type = $this->request->get('type');
            if ($type == 'folder') {
                $name = $this->request->get('name');
                if ($name) {
                    $path = $parentPath . "/" . $name;
                    $exist = Folder::getByPath($path);
                    if (!$exist) {
                        $item = Asset\Service::createFolderByPath($path);
                        $showSetting = true;

                        return $this->view(compact('item', 'showSetting'), '/asset/component/folder.html.twig');
                    }
                }
            } else {
                $files = $this->request->files->get('files');

                if (!empty($files)) {
                    foreach ($files as $file) {
                        $name = $file->getClientOriginalName();
                        $name = explode('.', $name);
                        $name = reset($name);
                        $asset = AssetTool::createFromFile($file, $name, $parentPath);
                    }
                } else {
                    $url = $this->request->get('url');

                    if ($url) {
                        $name = explode('/', $url);
                        $name = end($name);
                        $name = explode('.', $name);
                        $name = reset($name);
                        
                        $asset = AssetTool::createFromUrl($url, $name, $parentPath);
                    }
                }

                return $this->sendResponse();
            }
        }

        return $this->sendError(null);
    }
}
