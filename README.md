# laravel-ui-view-composer
# インストール方法
composer を利用してインストールして下さい。

依存関係で問題が生じた場合、composer.json に以下を追記します。
~~~composer.json
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/k-yamamoto-balocco/laravel-ui-utils.git"
        }
        {
            "type": "vcs",
            "url": "https://github.com/k-yamamoto-balocco/common-structures.git"
        }
    ],
~~~

# このパッケージが提供する機能
- AutoLoadServiceProvider : ViewComposerを自動的にviewに割り当てるサービス・プロバイダ  
- BasicComposer : テンプレートメソッドパターンで実装されている、シンプルなViewComposerの雛形
- FormComposer : フォーム入力要素を持つ画面に適用すると便利な ViewComposer の雛形

## AutoLoadServiceProvider

### 概要

ViewComposerを自動的にviewに割り当てるサービス・プロバイダです。設定ファイルを介して、ViewComposerの実装クラスとviewファイルの対応関係を自動的に検出して割り当てを行います。この機能を利用する場合、設定ファイル vc-autoloader.php を config ディレクトリ以下に配置して下さい。  

### 利用方法

利用開始時に、php artisan vendor:publish コマンドで初期状態の設定ファイルをconfigディレクトリにコピーします。

~~~ 
php artisan vendor:publish
~~~

選択肢が表示された場合、下記を選択します。
~~~
Provider: GitBalocco\LaravelUiViewComposer\ServiceProvider
~~~

初期設定ではこの機能は無効（enable : false）になっていますので、機能を利用する場合は設定ファイルを環境にあわせて修正した上で、trueに変更して下さい。

※配置される初期設定ファイルの内容については、[vc-autoloader-dist.php](./resources/vc-autoloader-dist.php) を参照。

### 設定について

vc-autoloader.php で設定可能な項目について以下に詳述します。

| 設定値名  | 型     | 内容                                                         | インストール初期値                                           |
| --------- | ------ | ------------------------------------------------------------ | ------------------------------------------------------------ |
| enable    | bool   | AutoLoadServiceProviderの有効化フラグ。                      | false                                                        |
| suffix    | string | ViewComposer実装クラスを検出する際にフィルタとなるクラス名の接尾辞。<br />※ファイル拡張子の指定は不可（検出対象は .php で固定。設定値が 'Composer' の場合、ファイル名末尾が Composer.php であるファイルが検出対象となる ） | 'Composer'                                                   |
| interface | string | ViewComposerが実装してなければならないインターフェース/基底クラス名。viewに対する割り当てを行うかどうかの判断条件となる。<br />この項目で設定した名称のインターフェース/基底クラス のサブクラスを実装していない場合、viewに対する割り当てが行われない。 | \GitBalocco\LaravelUiViewComposer\Contract\ViewComposerInterface::class |
| settings  | array  | 設定本体。複数指定可能。<br />自動検出の対象となるViewComposer格納ディレクトリと名前空間、およびviewファイル配置の対応関係。詳細は後述する。 | 後述                                                         |



| settings 項目名    | 型     | 内容                                                         | インストール初期値                       |
| ------------------ | ------ | ------------------------------------------------------------ | ---------------------------------------- |
| composer-path      | string | 検出対象のViewComposerクラスが配置されているディレクトリのパス。<br />末尾に / を付けないよう注意すること。 | app_path('Http/View/Composers/Contents') |
| composer-namespace | string | composer-pathに対応する名前空間名。<br />末尾に \\ を付けないよう注意すること。 | '\\App\\Http\\View\\Composers\\Contents' |
| view-path          | string | composer-pathに対応するviewファイルの位置。ディレクトリの区切りを「.」で表現するドット記法で指定すること。 | 'contents'                               |

### 設定状態の確認

vc-autoloader.php 設定内容で自動的に検出されるViewComposerクラスと、その適用状態の一覧を表示します。

~~~ sh
php artisan laravel-ui-view-composer:config-check
~~~

コマンドの結果として表示される内容は以下の通りです。

| 項目名              | 内容                                                         |
| ------------------- | ------------------------------------------------------------ |
| Found ViewComposers | 検出されたViewComposerクラスの名前空間                       |
| implements          | インターフェースを実装しているかどうか。                     |
| view name           | ViewComposerのクラス名から自動的に決定されるviewファイル名。<br />クラスの名前空間ルールに従って、キャメルケースとなっている点に注意してください。 |
| view exists         | view が実際に存在しているかどうか。                          |
| status              | 適用状態。<br />インターフェースが実装されていて、かつviewファイルが実際に存在する場合のみViewComposerが適用されます。<br />OK：適用される<br />NG：適用されない |

※composer-path に存在しないディレクトリ名を設定しているなど、設定値に誤りがある場合は結果の一覧に表示されません。検出結果、適用状態が想定通りの結果となっていることを、このコマンドを利用して確認して下さい。



## BasicComposer

テンプレートメソッドパターンを利用し、所定のインターフェースを実装していることを強制するシンプルなViewComposerです。この機能を利用する場合、ViewComposerを作成し、GitBalocco\LaravelUiViewComposer を継承した上で、createParameter() メソッドにviewにアサインする内容を定義してください。



### 利用サンプル

~~~ php
<?php

namespace App\Http\View\Composers\Contents\Web;

use GitBalocco\LaravelUiViewComposer\BasicComposer;

/**
 * ExampleComposer
 *
 * @package App\Http\View\Composers\Contents\Web
 */
class ExampleComposer extends BasicComposer
{
    /**
     * ViewComposerからviewにアサインしたい内容を配列で返却する。
     * @return array
     */
    public function createParameter(): array
    {
        return [
            'test' => 'てすとだよ！'
        ];
    }
}

~~~

### 注意事項、補足

- Controllerでviewにアサインされた内容とcreateParameter() メソッドに定義した内容が競合する場合、Controllerでアサインされた内容が優先されます。
- createParameter() メソッド内でControllerからアサインされた変数内容にアクセスしたい場合、getView() メソッドを利用します。
- getView() メソッドで、Illuminate\View\View クラスのインスタンスを取得することが可能です。
- アサイン内容は $this->getView()->getData() で取得可能。
- getView() は、compose() が実行されるまではNullを返却するため、BasicComposerのコンストラクタ内でControllerからアサインされた内容にアクセスすることはできません。コンストラクタインジェクションを利用する際など、この点に注意してください。 



## FormComposer

テンプレートメソッドパターンを利用し、フォーム入力値のハンドリングを行う便利なViewComposerです。この機能を利用する場合、ViewComposerを作成し、GitBalocco\LaravelUiViewComposer\FormComposer を継承した上で、init() メソッドにForm入力値制御のためのロジックを実装します。

### 主要な機能

FormComposerの機能は、Collection型の変数 $formValues をviewにアサインすることだけです。

viewにアサインする変数名を変更したい場合は、setFormValueParameterName() メソッドを使用して変数名をFormComposerに設定することができます。

~~~php
class FormSampleComposer extends FormComposer
{
    protected function init(Request $request, View $view): void
    {
        //下記セッタ実行により、viewファイル側では $myVariableName が利用可能となる。
        $this->setFormValueParameterName('myVariableName');
    }
}
~~~

viewファイル側では $formValuesを利用してフォーム要素を実装することで、画面利用の状況（エラー発生による再入力なのか、画面の初期表示状態なのか、データの更新を行う場合の初期表状態なのか、など）の違いを意識せず、テンプレートファイルを実装することが可能です。

### $formValues の制御と実装サンプル

init()メソッドの実装は、Applier（FormValueApplier）、Builder（FormValueBuilder）の2種類のコンポーネントで構成されています。Builderはフォームに渡される変数の内容を定義する役割、ApplierはBuilderを適用する条件を定義する役割を担当します。init() メソッド内では、 $formValues の内容を制御するためにaddFormValuesApplier() メソッド、または setFormValuesAppliers() メソッドを使用して、実装者の意図する内容となるよう、Applierを登録してください。

デフォルトの表示内容として ArrayFormValueBuilder、

以下のサンプルでは、

- 更新処理かどうかを判定する OnUpdateApplier

  ※更新処理時の$formValues内容の定義として EloquentFormValueBuilder

- デフォルト表示であるかどうかを判定する OnDefaultApplier

  ※デフォルト状態での$formValues内容の定義として ArrayFormValueBuilder

の2つのApplierインスタンスを生成し、addFormValuesApplier() でFormComposerに登録しています。

~~~ php
<?php

namespace App\Http\View\Composers\Contents\Web;

use GitBalocco\LaravelUiUtils\Http\IdentityHandler;
use GitBalocco\LaravelUiViewComposer\FormComposer;
use GitBalocco\LaravelUiViewComposer\FormValue\Applier\OnDefaultApplier;
use GitBalocco\LaravelUiViewComposer\FormValue\Applier\OnUpdateApplier;
use GitBalocco\LaravelUiViewComposer\FormValue\Builder\ArrayFormValueBuilder;
use GitBalocco\LaravelUiViewComposer\FormValue\Builder\EloquentFormValueBuilder;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Package\Infrastructure\Eloquent\User;

/**
 * FormSampleComposer
 *
 * @package App\Http\View\Composers\Contents\Web
 */
class FormSampleComposer extends FormComposer
{
    protected function init(Request $request, View $view): void
    {
        //OnUpdateApplierの準備
        $identityHandler = new IdentityHandler($request, $view);
        $onUpdateBuilder = new EloquentFormValueBuilder($identityHandler->retrieveIdentity(), new User());
        //Applierをaddする順番がそのまま優先度になるため、先にOnUpdateApplierを追加。
        $this->addFormValuesApplier(new OnUpdateApplier($onUpdateBuilder, $identityHandler));
        
        //OnDefaultApplierの準備
        $defaultBuilder = new ArrayFormValueBuilder(
            [
                'name' => 'nameの初期値',
                'email' => 'emailの初期値',
                'check' => ['']
            ]
        );
        //Applierをaddする順番がそのまま優先度になるため、OnDefaultApplierは一番最後に追加。
        $this->addFormValuesApplier(new OnDefaultApplier($defaultBuilder));

    }
}

~~~

※Applierは先に登録したものから順に評価されるため、addFormValuesApplier() で登録を行う順序に注意して下さい。

※FormCoposerは、コンストラクタ内で OnErrorApplier を登録しています。したがって、このサンプルの場合  

（1）OnErrorApplier（2）OnUpdateApplier（3）OnDefaultApplier  

の順に評価が行われます。  

※すべてのApplierの適用条件を満たさない場合、$formValues の値は空のCollectionとなります。



###  Applierコンポーネントの詳細

#### 実装済Applier一覧

前述の通り、ApplierはBuilderを適用する条件を表します。定型的な処理を行うために、GitBalocco\LaravelUiViewComposer\FormValue\Applier 以下に、3点のApplierクラスが実装済みです。

| Applier名        | 適用条件                                   | 評価式                                           | 備考                                                 |
| ---------------- | ------------------------------------------ | ------------------------------------------------ | ---------------------------------------------------- |
| OnErrorApplier   | old() になにか入っている場合に適用         | (bool)$this->request->old()                      | FormComposerのコンストラクタ内で自動的に登録される。 |
| OnUpdateApplier  | 何らかの方法でIDが指定されている場合に適用 | (bool)$this->identityHandler->retrieveIdentity() | IdentityHandler を利用。                             |
| OnDefaultApplier | 必ず適用                                   | true                                             |                                                      |

※デフォルト状態でApplierとして追加されているOnErrorApplierを利用したくない場合は、setFormValuesAppliers()を利用し、init() メソッド内で独自に初期化したStackableArrayのインスタンスを登録し直して下さい。



#### カスタマイズ

 独自に適用条件を定義したい場合、FormValueApplier インターフェースを実装したApplierクラスを作成してください。

~~~php
<?php

namespace App\Http\View\Composers\Contents\Web;

use GitBalocco\LaravelUiViewComposer\Contract\FormValueApplier;
use GitBalocco\LaravelUiViewComposer\Contract\FormValueBuilder;
use GitBalocco\LaravelUiViewComposer\FormValue\Builder\ArrayFormValueBuilder;

/**
 * ExampleCustomApplier カスタムApplierの実装サンプル
 */
class ExampleCustomApplier implements FormValueApplier
{
    /**
     * 必要な依存関係があれば、コンストラクタで外部から引き渡す。
     * このサンプルでは、コンストラクタでBuilderを外部から受け取っている。
     * ExampleCustomApplier constructor.
     */
    public function __construct(ArrayFormValueBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * shouldApply
     * Applier 適用条件の実装。
     * @return bool
     */
    public function shouldApply(): bool
    {
        return (bool)rand(0, 1);
    }

    /**
     * getBuilder
     * $formValuesの内容定義。固定の内容であればメソッド内でインスタンス生成して返却。
     * 外部に依存している場合、コンストラクタ経由で受け取った
     * @return FormValueBuilder
     */
    public function getBuilder(): FormValueBuilder
    {
        return $this->builder;
    }
}

~~~

### Builderコンポーネントの詳細

#### 実装済Builder一覧

前述の通り、Builderはフォームに渡される変数の内容を定義します。定型的な処理を行うために、GitBalocco\LaravelUiViewComposer\FormValue\Builder 以下に、3点のBuilderクラスが実装済みです。

| Builder名                | 内容                                                         |
| ------------------------ | ------------------------------------------------------------ |
| ArrayFormValueBuilder    | 単純な配列から$formValuesの内容を生成する。                  |
| EloquentFormValueBuilder | EloquentModelのfindOrFail() の結果から$formValuesの内容を生成する。 |
| ErrorFormValueBuilder    | old() の結果から$formValuesの内容を生成する。                |

#### カスタマイズ

 独自にBuilderを定義したい場合、FormValueBuilder インターフェースを実装したBuilderクラスを作成してください。

~~~ php
namespace App\Http\View\Composers\Contents\Web;

use GitBalocco\LaravelUiViewComposer\Contract\FormValueBuilder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Package\Domain\Model\User;

/**
 * Class ExampleCustomBuilder カスタムBuilderの実装サンプル
 */
class ExampleCustomBuilder implements FormValueBuilder
{
    /**
     * 必要な依存関係はコンストラクタを介してBuilderに引き渡す。
     * ExampleCustomBuilder constructor.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * $formValuesの内容を生成する。このサンプルでは、データベースの列名とviewファイルのフォーム名のミスマッチを解決するために
     * 内容に変換する処理をイメージしている。
     * @return Collection
     */
    public function build(): Collection
    {
        $formValues = [
            'full_name' => $this->user->first_name . '' . $this->user->last_name,
            'address' => $this->user->address01 . ' ' . $this->user->address02
        ];

        return App::make(Collection::class, ['items' => $formValues]);
    }
}

~~~

### $formValues 以外の変数をアサインする

$formValues 以外にviewにアサインしたい変数がある場合、ViewParameterCreatorインターフェースをimplementしてください。

#### サンプル

~~~ php
<?php
    
namespace App\Http\View\Composers\Contents\Web;

use GitBalocco\LaravelUiViewComposer\Contract\ViewParameterCreator;
use GitBalocco\LaravelUiViewComposer\FormComposer;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class FormSampleComposer
 * ViewParameterCreator を implementする。
 */
class FormSampleComposer extends FormComposer implements ViewParameterCreator
{
    public function createParameter(): array
    {
        //$formValues以外にviewにアサインしたい内容
    }


    protected function init(Request $request, View $view): void
    {
        //$formValuesの制御
    }
}

~~~

#### 注意事項

- BasicComposer と同様に、Controllerでviewにアサインされた内容とcreateParameter() メソッドに定義した内容が競合する場合、Controllerでアサインされた内容が優先されます。

- createParameter() メソッドが返却する配列のキーに、'formValues' は利用できません。

- setFormValueParameterName() メソッドによって変数名を変更している場合、その変数名をcreateParameter() メソッドが返却する配列のキーに利用することはできません。

# 注意事項
予告なく実装を変更することがあります。

production 環境で利用する場合などは、バージョンを厳密に指定して、意図しない更新が行われないよう自己責任で管理してください。

本パッケージの更新に伴い、利用アプリケーションにバグ、障害等が発生しても一切責任は負いません。

