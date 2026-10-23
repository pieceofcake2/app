# モダン構成への移行

将来 CakePHP 5 にアップグレードする予定があるなら、CakePHP 2 のまま、このスケルトンの CakePHP 5 スタイルの
ディレクトリ構成を取り入れることで、今のうちに準備しておけます。CakePHP 5 のディレクトリ、`layout/`、`element/`、
`email/`、`Error/` にある `.php` のテンプレート、そして `App` 名前空間にあるアプリケーションのクラスです。

これらの変更は [pieceofcake2/upgrade](https://pieceofcake2.github.io/upgrade/ja/) の `layout` コマンドと
`namespace` コマンドで行えます ([pieceofcake2/upgrade で移行する](#migrating-with-pieceofcake2upgrade) を参照)。
クラスをグローバルのままにするなら、`layout` だけを実行します。
[pieceofcake2/app-legacy](https://github.com/pieceofcake2/app-legacy) は、モダン構成でグローバルなクラスと
`.ctp` のテンプレートを使うスケルトンです。

CakePHP 2 のクックブックの [モダン構成への移行](https://pieceofcake2.github.io/docs/ja/appendices/migrating-to-the-modern-layout.html)
([Markdown](https://github.com/pieceofcake2/docs/blob/2.x/docs/ja/appendices/migrating-to-the-modern-layout.md))
も参照してください。

<a id="gradual-migration"></a>

## 段階的な移行

CakePHP 2 から CakePHP 5 に一度に移行すると、コード、フォルダ構成、API を同時に変更することになります。

```
CakePHP 2.x → CakePHP 5.x
(change everything at once: code + folder structure + APIs)
```

モダン構成を使えば、これを小さなステップに分けられます。

```
Step 1: CakePHP 2.x with traditional structure
        ↓ (modernize folder structure only: upgrade layout)
Step 2: CakePHP 2.x with CakePHP 5.x-style structure ← You can stop here
        ↓ (move the classes into a namespace: upgrade namespace)
Step 3: CakePHP 2.x with namespaced classes ← or here
        ↓ (upgrade code only)
Step 4: CakePHP 5.x with CakePHP 5.x-style structure
```

- **小さく扱いやすい変更**: フォルダの再編成をコードの変更から切り離せます。
- **段階的なテスト**: 次に進む前に各ステップを検証できます。
- **リスクの軽減**: Step 2 や Step 3 にいつまでもとどまれます。
- **チームにやさしい**: 小さな変更は理解しやすく、レビューしやすくなります。

<a id="directory-structure-comparison"></a>

## ディレクトリ構成の比較

**移行前: 従来の CakePHP 2.x の構成**

```
your-project/
├── app/
│   ├── Config/          (uppercase, nested)
│   ├── Controller/
│   ├── Model/
│   ├── View/            (templates + helpers mixed)
│   ├── Test/
│   ├── Plugin/
│   ├── Vendor/
│   └── tmp/
│       └── logs/
├── vendors/
└── ...
```

**移行後: モダン構成 (CakePHP 5.x 対応)**

```
your-project/
├── bin/
│   └── cake
├── config/              (lowercase, top-level)
├── src/                 (all PHP code, App namespace)
│   ├── Controller/
│   ├── Model/
│   └── View/            (Helper classes only)
├── templates/           (all templates as .php files, separated)
│   ├── Error/
│   ├── element/
│   ├── email/
│   └── layout/
├── resources/
│   └── locales/
├── tests/               (lowercase, top-level)
├── plugins/             (Composer-managed)
├── vendor/              (standard Composer)
├── logs/                (separated from tmp/)
├── tmp/
└── webroot/
```

<a id="file-migration-map"></a>

## ファイルの移行対応表

| 移行元 (従来) | 移行先 (モダン) |
| --- | --- |
| `app/Config/*` | `config/*` |
| `app/Controller/*` | `src/Controller/*` |
| `app/Model/*` | `src/Model/*` |
| `app/View/**/*.ctp` | `templates/**/*.php` |
| `app/View/Layouts/*` | `templates/layout/*` |
| `app/View/Layouts/Emails/*` | `templates/layout/email/*` |
| `app/View/Elements/*` | `templates/element/*` |
| `app/View/Emails/*` | `templates/email/*` |
| `app/View/Errors/*` | `templates/Error/*` |
| `app/View/Helper/*` | `src/View/Helper/*` |
| `app/Console/*` | `src/Console/*` |
| `app/Console/cake` | `bin/cake` |
| `app/Lib/<Type>/*` (例: `app/Lib/Error/`) | `src/<Type>/*` (`src/Error/`)。名前空間付きのクラスの場合 |
| `app/Lib/*` | `src/Lib/*` |
| `app/Locale/*` | `resources/locales/*` |
| `app/Test/*` | `tests/*` |
| `app/Plugin/*` | `plugins/*` (Composer を使用) |
| `app/Vendor/*` | `vendor/*` (Composer を使用) |
| `app/tmp/logs/*` | `logs/*` |
| `app/tmp/*` | `tmp/*` |
| `app/webroot/*` | `webroot/*` |

<a id="migrating-with-pieceofcake2upgrade"></a>

## pieceofcake2/upgrade で移行する

[pieceofcake2/upgrade](https://pieceofcake2.github.io/upgrade/ja/) は `git mv` でファイルを移動し、Rector で
コードを書き換えます。実行する前にアプリケーションをコミットし、コマンドごとに結果を確認してコミットしてください。
変更後のコードには pieceofcake2/cakephp 2.13 以降が必要です。

1. プラグインを開発用の依存パッケージとしてインストールし、`app/Config/bootstrap.php` で読み込みます
   (pieceofcake2/upgrade の [インストール](https://pieceofcake2.github.io/upgrade/ja/#installation) を参照)。

   ```bash
   composer require --dev pieceofcake2/upgrade
   ```

   ```php
   CakePlugin::load('Upgrade');
   ```

2. アプリケーションをモダン構成に移します。テンプレートは CakePHP 5 のディレクトリにある `.php` ファイルになります。

   ```bash
   app/Console/cake upgrade layout --dry-run
   app/Console/cake upgrade layout
   composer dump-autoload
   ```

3. クラスを `App` 名前空間に移します。このコマンドは `config/core.php` に `App.namespace` を追加し、
   PSR-4 で `src/` から `App\` を、`tests/` から `App\Test\` を読み込むようにもします。

   ```bash
   bin/cake upgrade namespace --dry-run
   bin/cake upgrade namespace
   composer dump-autoload
   ```

4. コマンドが手作業で行うように示したことを済ませ、テストを実行して、`git diff` で変更を確認します。
5. プラグインを削除します。`composer remove --dev pieceofcake2/upgrade` を実行し、`config/bootstrap.php` に
   移った `CakePlugin::load('Upgrade');` の行を削除します。

コマンドが何を変更するか、`app/` のないアプリケーションやプラグインの場合については、pieceofcake2/upgrade の
ドキュメントの [構成と名前空間](https://pieceofcake2.github.io/upgrade/ja/#layout-and-namespace) をご覧ください。

<a id="migration-steps"></a>

## 移行の手順

手作業で移行するには、次の手順に従います。

<a id="1-update-composerjson"></a>

### 1. composer.json を更新する

```json
{
    "require": {
        "php": ">=8.0",
        "pieceofcake2/cakephp": "^2.13"
    },
    "autoload": {
        "classmap": ["src/"]
    },
    "config": {
        "vendor-dir": "vendor/",
        "sort-packages": true,
        "allow-plugins": {
            "composer/installers": true
        }
    },
    "extra": {
        "installer-paths": {
            "plugins/{$name}/": ["type:cakephp-plugin"]
        }
    }
}
```

プラグインを `app/Plugin/` にインストールしていた場合、`installer-paths` の設定は `plugins/` を指すようになります。
`app/Plugin/` からプラグインを削除し、`composer install` を実行して `plugins/` にインストールしてください。

<a id="2-update-the-dispatcher-files"></a>

### 2. ディスパッチャーのファイルを更新する

このスケルトンから `config/define.php`、`webroot/index.php`、`webroot/test.php` と `bin/cake` を
プロジェクトにコピーします。`config/define.php` はモダン構成のパス定数を設定し、他のファイルはこれを読み込みます。

```php
define('ROOT', dirname(__DIR__));
define('APP_DIR', 'src'); // Changed from 'app'
define('WEBROOT_DIR', 'webroot');
define('WWW_ROOT', ROOT . DS . WEBROOT_DIR . DS);
define('VENDORS', ROOT . DS . 'vendor' . DS); // Standard Composer
```

その他のパスは、存在するディレクトリから CakePHP が導き出します。プロジェクトルートに `config/`、`tests/`、`tmp/`、
`logs/` のディレクトリがあれば、`CONFIG` は `config/`、`TESTS` は `tests/`、`TMP` は `tmp/`、`LOGS` は `logs/`
になります。また、テンプレートとロケールは `templates/` と `resources/locales/` からも探されます。

<a id="3-migrate-files"></a>

### 3. ファイルを移行する

[ファイルの移行対応表](#file-migration-map) に従ってファイルを移動します。これは少しずつ進められます:

1. 設定ファイル: `app/Config/*` → `config/*`
2. PHP のコード: `app/Controller/*`、`app/Model/*` → `src/`
3. テンプレート: `app/View/**/*.ctp` → `templates/**/*.php`。`Layouts/`、`Elements/`、`Emails/`、`Errors/` は
   `layout/`、`element/`、`email/`、`Error/` に名前を変え、メールのレイアウトは `Layouts/Emails/` から
   `layout/email/` に移します。スキャフォールドのテンプレート (`Scaffolds/`) は `.ctp` のままにします。
4. テスト: `app/Test/*` → `tests/`
5. 依存パッケージ: プラグインと vendor には Composer を使います

<a id="4-verify-your-application-still-works"></a>

### 4. アプリケーションが引き続き動作することを確認する

[pieceofcake2/cakephp](https://github.com/pieceofcake2/cakephp) のコアはモダン構成をサポートし、テンプレートを
`templates/` から探す (2.13 からは `.php` のテンプレートと CakePHP 5 のディレクトリも) ので、既存の CakePHP 2 の
コードは変更せずに動作します。移行の間も、従来のファイルの場所は引き続き使えます。

`composer.json` で `src/` を `classmap` でオートロードすれば、`composer dump-autoload` を実行した後で、
コードから `App::uses()` の呼び出しを削除できます。`App::uses()` は CakePHP 2.13 で非推奨になりました。
次の手順で PSR-4 を使うまでは、新しいクラスのファイルを作成するたびに `composer dump-autoload` を実行してください。

<a id="5-move-the-classes-into-a-namespace-optional"></a>

### 5. クラスを名前空間に移す (任意)

各クラスにディレクトリに対応する名前空間を宣言し (`src/Controller/` なら `App\Controller`、
`tests/TestCase/Controller/` なら `App\Test\TestCase\Controller`)、CakePHP とアプリケーションのクラスを `use` 文で
インポートして、`App::uses()` の呼び出しを削除します。

```php
// Before
App::uses('AppController', 'Controller');

class UsersController extends AppController
{
    // ...
}
```

```php
// After
namespace App\Controller;

class UsersController extends AppController
{
    // ...
}
```

`config/core.php` の `App.encoding` の設定より前で、CakePHP に名前空間を設定します
(`Cache::config('_cake_core_')` より前である必要があります)。

```php
/**
 * The namespace of the classes of the application
 */
Configure::write('App.namespace', 'App');
```

それから、classmap の代わりに PSR-4 でクラスを読み込むようにして、`composer dump-autoload` を実行します。

```json
"autoload": {
    "psr-4": {
        "App\\": "src/"
    }
},
"autoload-dev": {
    "psr-4": {
        "App\\Test\\": "tests/"
    }
},
```

名前空間付きのコードでは `Lib/` を使いません。`src/Lib/<Type>/` は `src/<Type>/` に移します
(例: `App\Error\AppExceptionRenderer` は `src/Error/AppExceptionRenderer.php`)。`config/` とテンプレートは
名前空間を持たないままで、使うクラスを `use` 文でインポートします。古いクラス名が残っているので、
`tmp/cache/` のキャッシュを削除してください。

<a id="upgrading-to-cakephp-5-step-4"></a>

## CakePHP 5 へのアップグレード (Step 4)

アプリケーションがモダン構成の CakePHP 2 で動作するようになれば、CakePHP 5 へのアップグレードはコードの変更だけになります:

- ディレクトリ構成はすでに正しいので、ファイルを整理し直す必要はありません。
- テンプレートはすでに CakePHP 5 のディレクトリにある `.php` ファイルなので、移動する必要はありません。
- クラスは CakePHP 5 と同じく、すでに `App` 名前空間にあります。
- Composer は `vendor/` と `plugins/` ですでにセットアップされています。

残っている作業:

- CakePHP 5.x を要求するように `composer.json` を更新します。
- CakePHP 5 の API の変更に合わせてコードを更新します。
- テストして、互換性の問題を修正します。
