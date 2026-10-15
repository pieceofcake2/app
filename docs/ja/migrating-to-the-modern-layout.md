# モダン構成への移行

将来 CakePHP 5 にアップグレードする予定があるなら、CakePHP 2 のまま、このスケルトンの CakePHP 5 スタイルの
ディレクトリ構成を取り入れることで、今のうちに準備しておけます。

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
        ↓ (modernize folder structure only)
Step 2: CakePHP 2.x with CakePHP 5.x-style structure ← You can stop here
        ↓ (upgrade code only)
Step 3: CakePHP 5.x with CakePHP 5.x-style structure
```

- **小さく扱いやすい変更**: フォルダの再編成をコードの変更から切り離せます。
- **段階的なテスト**: 次に進む前に各ステップを検証できます。
- **リスクの軽減**: Step 2 にいつまでもとどまれます。
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
├── src/                 (all PHP code)
│   ├── Controller/
│   ├── Model/
│   └── View/            (Helper classes only)
├── templates/           (all .ctp files, separated)
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
| `app/View/**/*.ctp` | `templates/**/*.ctp` |
| `app/View/Helper/*` | `src/View/Helper/*` |
| `app/Console/*` | `src/Console/*` |
| `app/Console/cake` | `bin/cake` |
| `app/Lib/*` | `src/Lib/*` |
| `app/Locale/*` | `resources/locales/*` |
| `app/Test/*` | `tests/*` |
| `app/Plugin/*` | `plugins/*` (Composer を使用) |
| `app/Vendor/*` | `vendor/*` (Composer を使用) |
| `app/tmp/logs/*` | `logs/*` |
| `app/tmp/*` | `tmp/*` |
| `app/webroot/*` | `webroot/*` |

<a id="migration-steps"></a>

## 移行の手順

<a id="1-update-composerjson"></a>

### 1. composer.json を更新する

```json
{
    "require": {
        "php": ">=8.0",
        "pieceofcake2/cakephp": "^2.12"
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
3. テンプレート: `app/View/**/*.ctp` → `templates/`
4. テスト: `app/Test/*` → `tests/`
5. 依存パッケージ: プラグインと vendor には Composer を使います

<a id="4-verify-your-application-still-works"></a>

### 4. アプリケーションが引き続き動作することを確認する

[pieceofcake2/cakephp](https://github.com/pieceofcake2/cakephp) のコアは、`App::uses()` とそのクラスの読み込みで
モダン構成をサポートし、テンプレートを `templates/` から探すので、既存の CakePHP 2 のコードは変更せずに動作します。
移行の間も、従来のファイルの場所は引き続き使えます。

<a id="5-leverage-composer-autoloading-optional"></a>

### 5. Composer のオートロードを活用する (任意)

`composer.json` で `src/` を `classmap` でオートロードすれば、`composer dump-autoload` を実行した後で、
コードから `App::uses()` の呼び出しを削除できます。

```php
// Before
App::uses('AppController', 'Controller');

class UsersController extends AppController
{
    // ...
}
```

```php
// After (with Composer autoload)
class UsersController extends AppController
{
    // ...
}
```

> [!IMPORTANT]
> 新しいクラスのファイルを作成するたびに `composer dump-autoload` を実行してください。新しいクラスが見つかるようにするには、
> Composer のクラスマップを再生成する必要があります。

Composer のオートロードに対応していないプラグインには、引き続き `App::uses()` か `CakePlugin::load()` が必要です。

<a id="upgrading-to-cakephp-5-step-3"></a>

## CakePHP 5 へのアップグレード (Step 3)

アプリケーションがモダン構成の CakePHP 2 で動作するようになれば、CakePHP 5 へのアップグレードはコードの変更だけになります:

- ディレクトリ構成はすでに正しいので、ファイルを整理し直す必要はありません。
- テンプレートはすでに分離されているので、`.ctp` ファイルを移動する必要はありません。
- Composer は `vendor/` と `plugins/` ですでにセットアップされています。

残っている作業:

- CakePHP 5.x を要求するように `composer.json` を更新します。
- CakePHP 5 の API の変更に合わせてコードを更新します。
- テストして、互換性の問題を修正します。
