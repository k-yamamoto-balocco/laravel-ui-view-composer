<?php

namespace GitBalocco\LaravelUiViewComposer\Config;

use Illuminate\Support\Facades\App;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

class SettingItem
{
    /** @var string $composerPath */
    private $composerPath;
    /** @var string $composerNamespace */
    private $composerNamespace;
    /** @var string $viewPath */
    private $viewPath;

    /**
     * SettingItem constructor.
     * @param array $setting
     */
    public function __construct(array $setting)
    {
        $this->composerPath = $setting['composer-path'];
        $this->composerNamespace = $setting['composer-namespace'];
        $this->viewPath = $setting['view-path'];
    }

    /**
     * @param string $suffix
     * @return Finder
     */
    public function createFinder(string $suffix): Finder
    {
        $finder = App::make(Finder::class);
        $finder->files()->in($this->getComposerPath())->name(['*' . $suffix . '.php']);
        return $finder;
    }

    /**
     * @return string
     */
    public function getComposerPath(): string
    {
        return $this->composerPath;
    }

    /**
     * @param SplFileInfo $fileInfo
     * @param string $suffix
     * @return string
     */
    public function viewComposerNamespace(SplFileInfo $fileInfo, string $suffix): string
    {
        return $this->getComposerNamespace() . implode('\\', $this->pathAsArray($fileInfo, $suffix)) . $suffix;
    }

    /**
     * @return string
     */
    public function getComposerNamespace(): string
    {
        return $this->composerNamespace;
    }

    /**
     * @param SplFileInfo $fileInfo
     * @param string $suffix
     * @return array
     */
    public function pathAsArray(SplFileInfo $fileInfo, string $suffix): array
    {
        $composerDir = str_replace($this->getComposerPath(), '', $fileInfo->getPath()) . '/';
        $baseName = $fileInfo->getBasename($suffix . '.php');
        return explode(DIRECTORY_SEPARATOR, $composerDir . $baseName);
    }

    /**
     * @param SplFileInfo $fileInfo
     * @param string $suffix
     * @return string
     */
    public function viewPathAsDotNotation(SplFileInfo $fileInfo, string $suffix): string
    {
        return $this->getViewPath() . implode('.', $this->pathAsArray($fileInfo, $suffix));
    }

    /**
     * @return string
     */
    public function getViewPath(): string
    {
        return $this->viewPath;
    }
}
