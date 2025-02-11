<?php

namespace HowMAS\CoreMSBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Finder\Finder;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\TableOfContents\TableOfContentsExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\Attributes\AttributesExtension;
use League\CommonMark\Extension\TaskList\TaskListExtension;
use League\CommonMark\MarkdownConverter;

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
        $config = [
            // 'table_of_contents' => [
            //     'html_class' => 'table-of-contents',
            //     'position' => 'top',
            //     'style' => 'bullet',
            //     'min_heading_level' => 1,
            //     'max_heading_level' => 6,
            //     'normalize' => 'relative',
            //     'placeholder' => null,
            // ],
        ];

        $config = [
            'table' => [
                'wrap' => [
                    'enabled' => true,
                    'tag' => 'div',
                    'attributes' => ['class' => 'bang_du_lieu'],
                ],
            ],
        ];
        $environment = new Environment($config);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new TableExtension());
        $environment->addExtension(new AttributesExtension());
        $environment->addExtension(new TaskListExtension());
        // $environment->addExtension(new HeadingPermalinkExtension());
        // $environment->addExtension(new TableOfContentsExtension());
        $converter = new MarkdownConverter($environment);

        $finder = new Finder();
        $finder->files()->in(__DIR__ . "/../../cms_docs")->exclude('images');
        $finder->sortByName();
        if ($finder->hasResults()) {
            $frontMatterExtension = new FrontMatterExtension();
            $frontMatterParser = $frontMatterExtension->getFrontMatterParser();

            foreach ($finder as $file) {
                $content = file_get_contents($file);

                if (!empty($content)) {
                    $content = str_replace(['![]('], ['![](/bundles/howmascorems/img/cms_docs/'], $content);

                    $result = $frontMatterParser->parse($content);
                    $content = $result->getContent();
                    $metadata = $result->getFrontMatter();

                    if (!empty($metadata) && isset($metadata['name']) && !empty($content)) {
                        $content = $converter->convertToHtml($content);
                        $guideContents[] = compact('metadata', 'content');
                    }
                }
            }
        }

        $config = $this->getConfig();
        if (isset($config['guide']['path']) && is_dir($config['guide']['path'])) {
            $finder = new Finder();
            $finder->files()->in($config['guide']['path']);
            if ($finder->hasResults()) {
                $frontMatterExtension = new FrontMatterExtension();
                $frontMatterParser = $frontMatterExtension->getFrontMatterParser();

                foreach ($finder as $file) {
                    $content = file_get_contents($file);

                    if (!empty($content)) {
                        $result = $frontMatterParser->parse($content);
                        $content = $result->getContent();
                        $metadata = $result->getFrontMatter();
                        $content = $converter->convertToHtml($content);

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
