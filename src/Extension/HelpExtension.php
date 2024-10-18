<?php

namespace HowMAS\CoreMSBundle\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\TwigFilter;
use Pimcore\Model\Asset;
use Starfruit\BuilderBundle\Tool\LanguageTool;

class HelpExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('hcore_languages_list', [$this, 'getLanguageList']),
        ];
    }

    public function getLanguageList()
    {
        return LanguageTool::getList();
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('hcore_fullpath', [$this, 'getFullPath']),
            new TwigFilter('hcore_asset', [$this, 'getAsset']),
            new TwigFilter('hcore_asset_type', [$this, 'getAssetType']),
            new TwigFilter('hcore_asset_preview', [$this, 'getAssetPreview']),
        ];
    }

    public function getFullPath(string $path)
    {
        return '@HowMASCoreMS' . $path;
    }

    public function getAsset(string $path)
    {
        return '/bundles/howmascorems' . $path;
    }

    public function getAssetType(Asset $asset)
    {
        $mimetype = $asset->getMimetype();

        if (str_contains($mimetype, 'image/')) {
            return "image";
        }

        if (str_contains($mimetype, 'video/')) {
            return "video";
        }

        return "file";
    }

    public function getAssetPreview(Asset $asset)
    {
        $mimetype = $asset->getMimetype();

        if (str_contains($mimetype, 'image/')) {
            return $asset->getFullPath();
        }

        if (str_contains($mimetype, 'video/')) {
            return $this->getAsset('/img/component/video.svg');
        }

        if ($mimetype == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
            return $this->getAsset('/img/component/word.svg');
        }

        if ($mimetype == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
            return $this->getAsset('/img/component/excel.svg');
        }

        if ($mimetype == 'application/pdf') {
            return $this->getAsset('/img/component/pdf.svg');
        }

        if ($mimetype == 'text/plain') {
            return $this->getAsset('/img/component/text.svg');
        }

        return $this->getAsset('/img/component/unknow.svg');
    }
}