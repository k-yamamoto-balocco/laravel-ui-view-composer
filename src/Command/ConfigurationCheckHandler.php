<?php

namespace GitBalocco\LaravelUiViewComposer\Command;

use GitBalocco\LaravelUiViewComposer\Config\SettingItem;
use GitBalocco\LaravelUiViewComposer\Config\VcAutoLoaderConfig;
use Illuminate\View\Factory;
use Illuminate\View\ViewFinderInterface;
use SplFileInfo;

class ConfigurationCheckHandler
{
    /** @var VcAutoLoaderConfig $config */
    private $config;
    /** @var ViewFinderInterface $viewFinder */
    private $viewFinder;

    /**
     * ConfigurationCheckHandler constructor.
     * @param VcAutoLoaderConfig $config
     * @param Factory $viewFactory
     */
    public function __construct(VcAutoLoaderConfig $config, Factory $viewFactory)
    {
        $this->config = $config;
        $this->viewFinder = $viewFactory->getFinder();
    }

    /**
     * @return iterable
     */
    public function details(): iterable
    {
        /** @var SettingItem $settingItem */
        foreach ($this->getConfig()->getValidDirectory() as $settingItem) {
            $result = [];
            $details = [];

            $finder = $settingItem->createFinder($this->getConfig()->getSuffix());

            /** @var SplFileInfo $fileInfo */
            foreach ($finder as $fileInfo) {
                $details[] = $this->detail($settingItem, $fileInfo);
            }

            //設定1件に対する返却項目を整理
            $result['details'] = $details;
            $result['composerPath'] = $settingItem->getComposerPath();

            //返却
            yield $result;
        }
    }

    /**
     * @return VcAutoLoaderConfig
     */
    public function getConfig(): VcAutoLoaderConfig
    {
        return $this->config;
    }

    /**
     * @param SettingItem $settingItem
     * @param SplFileInfo $fileInfo
     * @return array
     */
    protected function detail(SettingItem $settingItem, SplFileInfo $fileInfo): array
    {
        $nameSpace = $settingItem->viewComposerNamespace($fileInfo, $this->getConfig()->getSuffix());
        $viewPath = $settingItem->viewPathAsDotNotation($fileInfo, $this->getConfig()->getSuffix());

        $result = [];
        $result[0] = $nameSpace;
        $result[1] = $this->getConfig()->namespaceImplementsInterface($nameSpace);
        $result[2] = $viewPath;
        $result[3] = $this->foundWithViewFinder($viewPath);
        $result[4] = ($result[1] && $result[3]);

        return $result;
    }

    /**
     * @param string $viewPath
     * @return bool
     */
    protected function foundWithViewFinder(string $viewPath)
    {
        try {
            $this->getViewFinder()->find($viewPath);
            return true;
        } catch (\InvalidArgumentException $e) {
        }
        return false;
    }

    /**
     * @return ViewFinderInterface
     */
    protected function getViewFinder(): ViewFinderInterface
    {
        return $this->viewFinder;
    }
}
