# Migrating to the Modern Layout

If you plan to upgrade to CakePHP 5 in the future, you can prepare now by adopting the
CakePHP 5-style directory layout of this skeleton while you stay on CakePHP 2.

See also [Migrating to the Modern Layout](https://pieceofcake2.github.io/docs/appendices/migrating-to-the-modern-layout.html)
in the CakePHP 2 book.

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
        ↓ (modernize folder structure only)
Step 2: CakePHP 2.x with CakePHP 5.x-style structure ← You can stop here
        ↓ (upgrade code only)
Step 3: CakePHP 5.x with CakePHP 5.x-style structure
```

- **Smaller, manageable changes**: the folder restructuring is separate from the code
  changes.
- **Test incrementally**: verify each step before moving to the next.
- **Reduced risk**: you can stay on Step 2 indefinitely.
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

## File Migration Map

| From (traditional) | To (modern) |
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
| `app/Plugin/*` | `plugins/*` (use Composer) |
| `app/Vendor/*` | `vendor/*` (use Composer) |
| `app/tmp/logs/*` | `logs/*` |
| `app/tmp/*` | `tmp/*` |
| `app/webroot/*` | `webroot/*` |

## Migration Steps

### 1. Update composer.json

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
3. Templates: `app/View/**/*.ctp` → `templates/`
4. Tests: `app/Test/*` → `tests/`
5. Dependencies: use Composer for plugins and vendors

### 4. Verify Your Application Still Works

The [pieceofcake2/cakephp](https://github.com/pieceofcake2/cakephp) core supports the modern
layout with `App::uses()` and its class loading, and finds templates in `templates/`, so
your existing CakePHP 2 code works without modifications. The traditional file locations
keep working during the migration.

### 5. Leverage Composer Autoloading (Optional)

With the `classmap` autoloading of `src/` in `composer.json`, you can remove the
`App::uses()` calls from your code after running `composer dump-autoload`:

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
> Run `composer dump-autoload` every time you create a new class file. Composer's classmap
> must be regenerated before new classes can be found.

Plugins that don't support Composer autoloading still require `App::uses()` or
`CakePlugin::load()`.

## Upgrading to CakePHP 5 (Step 3)

Once your application runs on CakePHP 2 with the modern layout, the upgrade to CakePHP 5 is
limited to code changes:

- The directory structure is already correct, so there is no need to reorganize files.
- The templates are already separated, so there is no need to move `.ctp` files.
- Composer is already set up with `vendor/` and `plugins/`.

What is left:

- Update `composer.json` to require CakePHP 5.x.
- Update the code for the CakePHP 5 API changes.
- Test and fix compatibility issues.
