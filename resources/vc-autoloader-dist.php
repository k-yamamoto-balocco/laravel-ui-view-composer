<?php
// config / view-composer-autoloader
return [
    /*
    |--------------------------------------------------------------------------
    | enable
    |--------------------------------------------------------------------------
    | bool
    | 有効化フラグ。 trueの場合プラグイン有効。
    | デフォルト設定値は false
    */
    'enable' => false,

    /*
    |--------------------------------------------------------------------------
    | suffix
    |--------------------------------------------------------------------------
    | string
    | ViewComposer実装クラスを検出する際にフィルタとなるクラス名の接尾辞。
    | ファイル拡張子の指定は不可。検出対象は .php で固定。
    | 設定値が 'Composer' の場合、ファイル名末尾が Composer.php であるファイルが検出対象となる
    | デフォルト設定値は 'Composer'
    */
    'suffix' => 'Composer',

    /*
    |--------------------------------------------------------------------------
    | interface
    |--------------------------------------------------------------------------
    | string
    | ViewComposerが実装してなければならないインターフェース/基底クラス名。
    | viewに対する割り当てを行うかどうかの判断条件となる。
    | この項目で設定した名称のインターフェース/基底クラス のサブクラスを実装していない場合、viewに対する割り当てが行われない。
    | デフォルト設定値は \GitBalocco\LaravelUiViewComposer\Contract\ViewComposerInterface::class
    */
    'interface' => \GitBalocco\LaravelUiViewComposer\Contract\ViewComposerInterface::class,

    /*
    |--------------------------------------------------------------------------
    | settings
    |--------------------------------------------------------------------------
    | array
    | 自動検出の対象となるViewComposer格納ディレクトリと、viewファイル配置の対応関係。
    | 複数設定可能。
    |
    */
    'settings' => [
        [
            /*
            |--------------------------------------------------------------------------
            | composer-path
            |--------------------------------------------------------------------------
            | string
            | 検出対象のViewComposerクラスが配置されているディレクトリのパス。
            | 末尾に / を付けないよう注意すること。
            | デフォルト設定値は app_path('Http/View/Composers/Contents')
            */
            'composer-path' => app_path('Http/View/Composers/Contents'),

            /*
            |--------------------------------------------------------------------------
            | composer-namespace
            |--------------------------------------------------------------------------
            | string
            | composer-pathに対応する名前空間名。
            | 末尾に \\ を付けないよう注意すること。
            | デフォルト設定値は '\\App\\Http\\View\\Composers\\Contents'
            */
            'composer-namespace' => '\\App\\Http\\View\\Composers\\Contents',

            /*
            |--------------------------------------------------------------------------
            | view-path
            |--------------------------------------------------------------------------
            | string
            | composer-pathに対応するviewファイルの位置。ドット記法で指定する。
            | デフォルト設定値は 'contents'
            */
            'view-path' => 'contents',
        ],
    ]
];
