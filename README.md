# CakePHP 2.x Application Skeleton

[![GitHub License](https://img.shields.io/github/license/pieceofcake2/app?label=License)](LICENSE)
[![Packagist Version](https://img.shields.io/packagist/v/pieceofcake2/app?label=Packagist)](https://packagist.org/packages/pieceofcake2/app)
![PHP](https://img.shields.io/packagist/dependency-v/pieceofcake2/app/php?logo=php&logoColor=%23FFFFFF&label=PHP&labelColor=%23777BB4&color=%23FFFFFF)
![CakePHP](https://img.shields.io/packagist/dependency-v/pieceofcake2/app/pieceofcake2/cakephp?logo=cakephp&logoColor=%23FFFFFF&label=CakePHP&labelColor=%23D33C43&color=%23FFFFFF)

A reference implementation for CakePHP 2.x applications using a modern, CakePHP 5.x-compatible directory structure.

> [!WARNING]
> **CakePHP 2.x is for legacy maintenance only.**
> For new projects, use [CakePHP 5.x](https://cakephp.org/) instead.

## Planning to migrate to CakePHP 5.x?

If you're planning to upgrade to CakePHP 5.x in the future, you can **prepare now** by adopting the modern directory structure while still on CakePHP 2.x:

**Traditional migration approach (harder):**
```
CakePHP 2.x → CakePHP 5.x
(change everything at once: code + folder structure + APIs)
```

**New gradual migration approach (easier):**
```
Step 1: CakePHP 2.x with traditional structure
        ↓ (modernize folder structure only)
Step 2: CakePHP 2.x with CakePHP 5.x-style structure ← You can stop here
        ↓ (upgrade code only)
Step 3: CakePHP 5.x with CakePHP 5.x-style structure
```

**Benefits:**
- ✅ **Smaller, manageable changes**: Separate folder restructuring from code changes
- ✅ **Test incrementally**: Verify each step works before moving to the next
- ✅ **Reduced risk**: You can stay on Step 2 indefinitely if needed
- ✅ **Team-friendly**: Easier for teams to understand and review smaller changes

## Restructuring to Modern Layout (Step 2)

This skeleton shows you how to achieve **Step 2** - running CakePHP 2.x with a modern folder structure.

### Directory Structure Comparison

**Before: Traditional CakePHP 2.x**
```
your-project/
├── app/
│   ├── Config/          (uppercase, nested)
│   ├── Controller/
│   ├── Model/
│   ├── View/           (templates + helpers mixed)
│   ├── Test/
│   ├── Plugin/
│   ├── Vendor/
│   └── tmp/
│       └── logs/
├── vendors/
└── ...
```

**After: Modern Structure (CakePHP 5.x Ready)**
```
your-project/
├── bin/
│   └── cake
├── config/             (lowercase, top-level)
├── src/                (all PHP code)
│   ├── Controller/
│   ├── Model/
│   └── View/           (Helper classes only)
├── templates/          (all .ctp files, separated)
├── resources/
│   └── locales/
├── tests/              (lowercase, top-level)
├── plugins/            (Composer-managed)
├── vendor/             (standard Composer)
├── logs/               (separated from tmp/)
├── tmp/
└── webroot/
```

### File Migration Map

Use this table to migrate your files from traditional structure to modern structure:

| From (Traditional)           | To (Modern)                |
|------------------------------|----------------------------|
| `app/Config/*`               | `config/*`                 |
| `app/Controller/*`           | `src/Controller/*`         |
| `app/Model/*`                | `src/Model/*`              |
| `app/View/**/*.ctp`          | `templates/**/*.ctp`       |
| `app/View/Helper/*`          | `src/View/Helper/*`        |
| `app/Console/*`              | `src/Console/*`            |
| `app/Console/cake`           | `bin/cake`                 |
| `app/Lib/*`                  | `src/Lib/*`                |
| `app/Locale/*`               | `resources/locales/*`      |
| `app/Test/*`                 | `tests/*`                  |
| `app/Plugin/*`               | `plugins/*` (use Composer) |
| `app/Vendor/*`               | `vendor/*` (use Composer)  |
| `app/tmp/logs/*`             | `logs/*`                   |
| `app/tmp/*`                  | `tmp/*`                    |
| `app/webroot/*`              | `webroot/*`                |

### Migration Steps

#### 1. Update composer.json

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

#### 2. Update Bootstrap Path Definitions

Copy or reference this skeleton's `webroot/index.php`, `webroot/test.php`, and `bin/cake` to update the path definitions in your project:

```php
define('ROOT', dirname(__DIR__));
define('APP_DIR', 'src');                     // Changed from 'app'
define('APP', ROOT . DS . APP_DIR . DS);
define('CONFIG', ROOT . DS . 'config' . DS);  // Top-level, lowercase
define('TESTS', ROOT . DS . 'tests' . DS);    // Top-level, lowercase
define('TMP', ROOT . DS . 'tmp' . DS);        // Top-level
define('LOGS', ROOT . DS . 'logs' . DS);      // Separated from tmp/
define('VENDORS', ROOT . DS . 'vendor' . DS); // Standard Composer
```

Refer to this skeleton's implementation files for complete examples.

#### 3. Migrate Files

Move files according to the File Migration Map above. You can do this gradually:

1. Configuration files: `app/Config/*` → `config/*`
2. PHP code: `app/Controller/*`, `app/Model/*` → `src/`
3. Templates: `app/View/**/*.ctp` → `templates/`
4. Tests: `app/Test/*` → `tests/`
5. Dependencies: Use Composer for plugins and vendors

#### 4. Verify Your Application Still Works

The [pieceofcake2/cakephp](https://github.com/pieceofcake2/cakephp) core automatically supports this modern structure with `App::uses()` and class loading, so your existing CakePHP 2.x code should work without modifications.

#### 5. Leverage Composer Autoloading (Optional)

Once Composer's autoload is configured in `composer.json`, you can remove `App::uses()` calls from your code:

```json
"autoload": {
    "classmap": ["src/"]
}
```

After running `composer dump-autoload`, you can safely remove `App::uses()` statements from your controllers, models, helpers, and shells.

> [!IMPORTANT]
> You must run `composer dump-autoload` every time you create a new class file. Composer's classmap autoloader needs to be regenerated to recognize new classes.

For example:

```php
// Before
App::uses('AppController', 'Controller');

class UsersController extends AppController {
    // ...
}

// After (with Composer autoload)
class UsersController extends AppController {
    // ...
}
```

**Note:** Plugins that don't support Composer autoloading will still require `App::uses()` or `CakePlugin::load()` to function properly.

## Upgrading to CakePHP 5.x (Step 3)

Once you've completed Step 2 (modern folder structure with CakePHP 2.x), upgrading to CakePHP 5.x becomes much simpler:

**What's already done:**
- ✅ Directory structure is already correct - No need to reorganize files
- ✅ Templates already separated - No need to move `.ctp` files
- ✅ Modern Composer setup - Already using `vendor/` and `plugins/`

**What you need to do:**
- Update `composer.json` to require CakePHP 5.x
- Update code for CakePHP 5.x API changes
- Test and fix compatibility issues

**The key advantage:** You can focus 100% on code changes, not structural changes.

Refer to the [CakePHP 5.x Migration Guide](https://book.cakephp.org/5/en/appendices/5-0-migration-guide.html) for framework-specific changes.

## Requirements

- PHP 8.0, 8.1, 8.2, 8.3, 8.4, 8.5
- Composer
- Database: MySQL 5.6+, PostgreSQL 9.4+, SQLite 3, or SQL Server 2022+
- PHP Extensions: `mbstring`, `intl`, `openssl`, PDO driver for your database

See [pieceofcake2/cakephp requirements](https://github.com/pieceofcake2/cakephp#requirements--compatibility) for details.

## Technical Implementation

This modern directory structure works with CakePHP 2.x through custom path configuration in bootstrap files (`webroot/index.php`, `webroot/test.php`, `bin/cake`).

The [pieceofcake2/cakephp](https://github.com/pieceofcake2/cakephp) core has been enhanced to:
- Automatically load classes from `src/Controller/`, `src/Model/`, etc. with `App::uses()`
- Find templates in the `templates/` directory
- Support both modern and traditional file locations during migration

**You don't need to modify your application code** - the framework handles the path mapping automatically.

## Environment Variables (.env)

Settings such as the debug level, security salt and database credentials can be read from environment variables. For local development, copy the example file and edit it:

```bash
cp config/.env.example config/.env
```

`config/.env` is loaded at the top of `config/core.php` (so it applies to web requests, `bin/cake` and tests) using [josegonzalez/dotenv](https://github.com/josegonzalez/php-dotenv). It is ignored by git. Values are then available through `env()`, `getenv()`, `$_ENV` and `$_SERVER`.

| Variable | Setting | Default |
|----------|---------|---------|
| `DEBUG` | `debug` (`0`, `1`, `2`, `true` = 2, `false` = 0) | `2` |
| `SECURITY_SALT` | `Security.salt` | value in `config/core.php` |
| `SECURITY_CIPHER_SEED` | `Security.cipherSeed` (digits only) | value in `config/core.php` |
| `DB_DATASOURCE`, `DB_HOST`, `DB_PORT`, `DB_USERNAME`, `DB_PASSWORD`, `DB_DATABASE` | `default` connection in `config/database.php` | values in `config/database.php` |
| `DB_TEST_DATASOURCE`, `DB_TEST_HOST`, `DB_TEST_PORT`, `DB_TEST_USERNAME`, `DB_TEST_PASSWORD`, `DB_TEST_DATABASE` | `test` connection in `config/database.php` | values in `config/database.php` |

- Unset or empty variables keep the defaults, so the application behaves exactly as before without a `.env` file (an empty `DB_PASSWORD` / `DB_TEST_PASSWORD` is allowed).
- Real environment variables (web server, shell, container, CI) take precedence; `config/.env` never overwrites them.
- Quote values in `config/.env` (e.g. `DEBUG="0"`). Unquoted numbers are normalized by the parser (`007` becomes `7`, and long digit strings such as a cipher seed overflow).
- `config/.env` is meant for development. In production, set real environment variables instead.
- The database variables are read by `config/database.php.default`. If you created `config/database.php` before this was added, copy the `__construct()` and `applyEnv()` methods into it.

## Development

### Running Tests

```bash
./bin/cake test app AllTests
```

Or with PHPUnit:

```bash
./vendor/bin/phpunit
```

### Code Standards

Check your code against CakePHP coding standards:

```bash
./vendor/bin/phpcs
```

## Documentation

- [CakePHP 2.x Documentation](https://book.cakephp.org/2.0/en/)
- [CakePHP 5.x Migration Guide](https://book.cakephp.org/5/en/appendices/5-0-migration-guide.html)

## License

MIT License. See [LICENSE](LICENSE) file for details.

## Support

This is a community-maintained fork of CakePHP 2.x. For issues and questions:

- [pieceofcake2/cakephp Issues](https://github.com/pieceofcake2/cakephp/issues)
- [pieceofcake2/app Issues](https://github.com/pieceofcake2/app/issues)
