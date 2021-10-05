<?php

namespace GitBalocco\LaravelUiViewComposer;

use GitBalocco\LaravelUiViewComposer\Config\SettingItem;
use GitBalocco\LaravelUiViewComposer\Config\VcAutoLoaderConfig;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use SplFileInfo;

/**
 * Class AutoLoadServiceProvider
 * @package GitBalocco\LaravelUiViewComposer
 */
class AutoLoadServiceProvider extends ServiceProvider
{
    /**
     * 全アプリケーションサービスの初期起動
     *
     * @return void
     */
    public function boot()
    {
        /** @var VcAutoLoaderConfig $config */
        $config = App::make(VcAutoLoaderConfig::class);

        //設定ファイルチェック、有効化されていない、設定ファイルが無い場合などは何もせず終了
        if (!$config->isEnable()) {
            return;
        }
        //登録処理開始
        foreach ($config->getValidDirectory() as $settingItem) {
            $this->registerViewComposer($settingItem);
        }
    }

    /**
     * @param SettingItem $settingItem
     */
    protected function registerViewComposer(SettingItem $settingItem)
    {
        /** @var VcAutoLoaderConfig $config */
        $config = App::make(VcAutoLoaderConfig::class);

        $finder = $settingItem->createFinder($config->getSuffix());

        /** @var SplFileInfo $fileInfo */
        foreach ($finder as $fileInfo) {
            //発見されたファイル1点ごとに登録を実施
            $this->registerByFileInfo($fileInfo, $settingItem);
        }
    }

    /**
     * @param SplFileInfo $fileInfo
     * @param SettingItem $settingItem
     */
    protected function registerByFileInfo(
        SplFileInfo $fileInfo,
        SettingItem $settingItem
    ) {
        /** @var VcAutoLoaderConfig $config */
        $config = App::make(VcAutoLoaderConfig::class);
        //名前空間に変換
        $nameSpace = $settingItem->viewComposerNamespace($fileInfo, $config->getSuffix());
        //ドット記法のViewファイルパスに変換
        $viewPathAsDotNotation = $settingItem->viewPathAsDotNotation($fileInfo, $config->getSuffix());

        //検出されたViewComposerのインターフェースをチェック
        if (!$config->namespaceImplementsInterface($nameSpace)) {
            return ;
        }

        //ViewComposerを適用
        View::composer($viewPathAsDotNotation, $nameSpace);
    }
}
