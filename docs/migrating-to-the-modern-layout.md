# Migrating to the Modern Layout

If you plan to upgrade to CakePHP 5 in the future, you can prepare now by adopting the
CakePHP 5-style directory layout of this skeleton while you stay on CakePHP 2: the
directories of CakePHP 5, `.php` templates in `layout/`, `element/`, `email/` and `Error/`, and
the application classes in the `App` namespace.

The `layout` and `namespace` commands of [pieceofcake2/upgrade](https://pieceofcake2.github.io/upgrade/)
make these changes (see [Migrating with pieceofcake2/upgrade](#migrating-with-pieceofcake2upgrade)).
To keep the classes global, run only `layout`.
[pieceofcake2/app-legacy](https://github.com/pieceofcake2/app-legacy) is the skeleton of the
modern layout with global classes and `.ctp` templates.

See also [Migrating to the Modern Layout](https://pieceofcake2.github.io/docs/appendices/migrating-to-the-modern-layout.html)
in the CakePHP 2 book
([Markdown](https://github.com/pieceofcake2/docs/blob/2.x/docs/en/appendices/migrating-to-the-modern-layout.md)).

## Gradual Migration

Moving from CakePHP 2 to CakePHP 5 in one step means changing the code, the folder
structure and the APIs at the same time:

```
CakePHP 2.x → CakePHP 5.x
(change everything at once: code + folder structure + APIs)
```

The modern layout lets you split this into smaller steps:

```
Step 1: CakePHP 2.x with traditional structure
        ↓ (modernize folder structure only: upgrade layout)
Step 2: CakePHP 2.x with CakePHP 5.x-style structure ← You can stop here
        ↓ (move the classes into a namespace: upgrade namespace)
Step 3: CakePHP 2.x with namespaced classes ← or here
        ↓ (upgrade code only)
Step 4: CakePHP 5.x with CakePHP 5.x-style structure
```

- **Smaller, manageable changes**: the folder restructuring is separate from the code
  changes.
- **Test incrementally**: verify each step before moving to the next.
- **Reduced risk**: you can stay on Step 2 or Step 3 indefinitely.
- **Team-friendly**: smaller changes are easier to understand and review.

## Directory Structure Comparison

**Before: traditional CakePHP 2.x layout**

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

**After: modern layout (CakePHP 5.x ready)**

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

## File Migration Map

| From (traditional) | To (modern) |
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
| `app/Lib/<Type>/*` (e.g. `app/Lib/Error/`) | `src/<Type>/*` (`src/Error/`), with namespaced classes |
| `app/Lib/*` | `src/Lib/*` |
| `app/Locale/*` | `resources/locales/*` |
| `app/Test/*` | `tests/*` |
| `app/Plugin/*` | `plugins/*` (use Composer) |
| `app/Vendor/*` | `vendor/*` (use Composer) |
| `app/tmp/logs/*` | `logs/*` |
| `app/tmp/*` | `tmp/*` |
| `app/webroot/*` | `webroot/*` |

## Migrating with pieceofcake2/upgrade

[pieceofcake2/upgrade](https://pieceofcake2.github.io/upgrade/) moves the files with `git mv` and
rewrites the code with Rector. Commit your application before you run it, and check and commit
the result of each command. The changed code needs pieceofcake2/cakephp 2.13 or later.

1. Install the plugin as a development dependency and load it in `app/Config/bootstrap.php`
   (see [Installation](https://pieceofcake2.github.io/upgrade/#installation) of pieceofcake2/upgrade):

   ```bash
   composer require --dev pieceofcake2/upgrade
   ```

   ```php
   CakePlugin::load('Upgrade');
   ```

2. Move the application to the modern layout, with the templates as `.php` files in the
   directories of CakePHP 5:

   ```bash
   app/Console/cake upgrade layout --dry-run
   app/Console/cake upgrade layout
   composer dump-autoload
   ```

3. Move the classes into the `App` namespace. The command also adds `App.namespace` to
   `config/core.php` and loads `App\` from `src/` and `App\Test\` from `tests/` with PSR-4:

   ```bash
   bin/cake upgrade namespace --dry-run
   bin/cake upgrade namespace
   composer dump-autoload
   ```

4. Do what the commands list for you to do by hand, run your tests, and review the changes
   with `git diff`.
5. Remove the plugin: `composer remove --dev pieceofcake2/upgrade`, and the
   `CakePlugin::load('Upgrade');` line, which is now in `config/bootstrap.php`.

See [Layout and Namespace](https://pieceofcake2.github.io/upgrade/#layout-and-namespace) in the
documentation of pieceofcake2/upgrade for what the commands change, the applications without
`app/`, and the plugins.

## Migration Steps

To migrate by hand, follow the steps below.

### 1. Update composer.json

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

If your plugins were installed into `app/Plugin/`, the `installer-paths` setting now points
to `plugins/`. Remove the plugins from `app/Plugin/` and run `composer install` to install
them into `plugins/`.

### 2. Update the Dispatcher Files

Copy `config/define.php`, `webroot/index.php`, `webroot/test.php` and `bin/cake` from this
skeleton into your project. `config/define.php` sets the path constants of the modern
layout, and the other files load it:

```php
define('ROOT', dirname(__DIR__));
define('APP_DIR', 'src'); // Changed from 'app'
define('WEBROOT_DIR', 'webroot');
define('WWW_ROOT', ROOT . DS . WEBROOT_DIR . DS);
define('VENDORS', ROOT . DS . 'vendor' . DS); // Standard Composer
```

CakePHP derives the other paths from the directories that exist: `CONFIG` is `config/`,
`TESTS` is `tests/`, `TMP` is `tmp/` and `LOGS` is `logs/` when these directories exist in
the project root, and templates and locales are also looked up in `templates/` and
`resources/locales/`.

### 3. Migrate Files

Move the files according to the [file migration map](#file-migration-map). You can do this
gradually:

1. Configuration files: `app/Config/*` → `config/*`
2. PHP code: `app/Controller/*`, `app/Model/*` → `src/`
3. Templates: `app/View/**/*.ctp` → `templates/**/*.php`, with `Layouts/`, `Elements/`,
   `Emails/` and `Errors/` renamed to `layout/`, `element/`, `email/` and `Error/`, and the email
   layouts moved from `Layouts/Emails/` to `layout/email/`. The scaffold templates
   (`Scaffolds/`) keep `.ctp`.
4. Tests: `app/Test/*` → `tests/`
5. Dependencies: use Composer for plugins and vendors

### 4. Verify Your Application Still Works

The [pieceofcake2/cakephp](https://github.com/pieceofcake2/cakephp) core supports the modern
layout and finds templates in `templates/` (`.php` templates and the directories of CakePHP 5
since 2.13), so your existing CakePHP 2 code works without modifications. The traditional file
locations keep working during the migration.

With the `classmap` autoloading of `src/` in `composer.json`, you can remove the `App::uses()`
calls from your code after running `composer dump-autoload`. `App::uses()` is deprecated as of
CakePHP 2.13. Run `composer dump-autoload` every time you create a new class file, until you
use PSR-4 in the next step.

### 5. Move the Classes into a Namespace (Optional)

Declare the namespace that follows the directory of each class (`App\Controller` for
`src/Controller/`, `App\Test\TestCase\Controller` for `tests/TestCase/Controller/`), import the
classes of CakePHP and of the application with `use` statements, and remove the
`App::uses()` calls:

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

Tell CakePHP the namespace in `config/core.php`, before the `App.encoding` setting (it must come
before `Cache::config('_cake_core_')`):

```php
/**
 * The namespace of the classes of the application
 */
Configure::write('App.namespace', 'App');
```

Then load the classes with PSR-4 instead of the classmap, and run `composer dump-autoload`:

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

Namespaced code has no `Lib/`: move `src/Lib/<Type>/` to `src/<Type>/`, e.g.
`src/Error/AppExceptionRenderer.php` for `App\Error\AppExceptionRenderer`. `config/` and the
templates stay without a namespace and import the classes that they name with `use`
statements. Clear the caches in `tmp/cache/`, which hold the old class names.

## Upgrading to CakePHP 5 (Step 4)

Once your application runs on CakePHP 2 with the modern layout, the upgrade to CakePHP 5 is
limited to code changes:

- The directory structure is already correct, so there is no need to reorganize files.
- The templates are already `.php` files in the directories of CakePHP 5, so there is no need
  to move them.
- The classes are already in the `App` namespace, as in CakePHP 5.
- Composer is already set up with `vendor/` and `plugins/`.

What is left:

- Update `composer.json` to require CakePHP 5.x.
- Update the code for the CakePHP 5 API changes.
- Test and fix compatibility issues.
