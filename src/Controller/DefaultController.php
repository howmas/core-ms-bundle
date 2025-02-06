<?php

namespace HowMAS\CoreMSBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Finder\Finder;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;

class DefaultController extends BaseController
{
    /**
     * @Route("/index", name="index")
     */
    public function indexAction()
    {
        return $this->view([
            'layoutPageTitle' => 'Quản trị'
        ]);
    }

    /**
     * @Route("/guide", name="guide")
     */
    public function guideAction()
    {
        $guideContents = [];

        $config = $this->getConfig();
        if (isset($config['guide']['path']) && is_dir($config['guide']['path'])) {
            $finder = new Finder();
            $finder->files()->in($config['guide']['path']);
            if ($finder->hasResults()) {
                $frontMatterExtension = new FrontMatterExtension();
                $frontMatterParser = $frontMatterExtension->getFrontMatterParser();

                foreach ($finder as $file) {
                    $absoluteFilePath = $file->getRealPath();
                    $fileNameWithExtension = $file->getRelativePathname();

                    $content = file_get_contents($file);

                    if (!empty($content)) {
                        $result = $frontMatterParser->parse($content);
                        $content = $result->getContent();
                        $metadata = $result->getFrontMatter();

                        if (!empty($metadata) && isset($metadata['name'])) {
                            $guideContents[] = compact('metadata', 'content');
                        }
                    }
                }
            }
        }

        return $this->view([
            'guideContents' => $guideContents,
            'layoutPageTitle' => 'Hướng dẫn sử dụng'
        ]);
    }
}
