# Application Skeleton Documentation

pieceofcake2/app is the application skeleton for CakePHP 2 applications running on
[pieceofcake2/cakephp](https://github.com/pieceofcake2/cakephp). It uses a directory layout
compatible with CakePHP 5 (`config/`, `src/`, `templates/`, `tests/`, `webroot/`) and
loads CakePHP and all dependencies with Composer.

> [!WARNING]
> CakePHP 2.x is for the maintenance of existing applications. For new projects, use
> the current major version of CakePHP instead.

- [Requirements](#requirements)
- [Installation](#installation)
- [Directory Layout](#directory-layout)
- [Configuration](#configuration)
- [Environment Variables (.env)](#environment-variables-env)
- [Running the Application](#running-the-application)
- [Development](#development)

To move an existing application with the legacy `app/` layout to this layout, see
[Migrating to the Modern Layout](migrating-to-the-modern-layout.md).

## Requirements

- PHP 8.0, 8.1, 8.2, 8.3, 8.4 or 8.5
- [Composer](https://getcomposer.org/)
- Database: MySQL 5.6+, PostgreSQL 9.4+, SQLite 3 or SQL Server 2022+
- PHP extensions: `mbstring`, `intl`, `openssl` and the PDO driver of your database

See the [requirements of pieceofcake2/cakephp](https://github.com/pieceofcake2/cakephp#requirements--compatibility)
for details.

## Installation

Create a new application with Composer's `create-project` command:

```bash
composer create-project pieceofcake2/app my_app
```

This creates the `my_app` directory and installs CakePHP (`pieceofcake2/cakephp`) into
`vendor/`. The [Bake](https://pieceofcake2.github.io/bake/) and
[DebugKit](https://pieceofcake2.github.io/debug_kit/) plugins and the development tools
(PHPUnit, PHPStan with [pieceofcake2/phpstan-cakephp2](https://pieceofcake2.github.io/phpstan-cakephp2/),
and the CakePHP coding standard) are installed as development dependencies.

Then finish the configuration:

1. Copy `config/database.php.default` to `config/database.php` and enter your database
   connection settings, or set them with [environment variables](#environment-variables-env).
2. Change `Security.salt` and `Security.cipherSeed` in `config/core.php`, or set
   `SECURITY_SALT` and `SECURITY_CIPHER_SEED`. The skeleton ships with well-known example
   values that must not be used in a real application.
3. If you send email, copy `config/email.php.default` to `config/email.php` and configure
   your transport.
4. Make sure that the web server can write to `tmp/` and `logs/`.

### Version Control

The `.gitignore` file of the skeleton is meant for your application, so you can run
`git init` right after `create-project`. It ignores:

- `vendor/` and the plugins that Composer installs into `plugins/` (`plugins/Bake/` and
  `plugins/DebugKit/`). If you install more plugins with Composer, add them to
  `.gitignore`; the plugins of your own application in `plugins/` are committed.
- the contents of `tmp/` and `logs/`. The `empty` files that keep the directories are
  committed.
- `config/database.php`, `config/email.php`, `config/.env` and `.env`, which contain the
  settings and credentials of each environment.
- `phpunit.xml` (a local copy of `phpunit.xml.dist`) and `.phpunit.result.cache`.

Commit `composer.lock`, so that every environment installs the same versions of the
dependencies.

### Development Plugins

`config/bootstrap.php` loads the [Bake](https://pieceofcake2.github.io/bake/) and
[DebugKit](https://pieceofcake2.github.io/debug_kit/) plugins when they are installed:

```php
foreach (['Bake', 'DebugKit'] as $plugin) {
    try {
        CakePlugin::load($plugin);
    } catch (MissingPluginException) {
        // The plugin is not installed.
    }
}
```

They are development dependencies, so they are skipped when the application is installed
with `composer install --no-dev`. Run Bake with:

```bash
bin/cake bake
```

To show the DebugKit toolbar, add the `DebugKit.Toolbar` component to your `AppController`
(see the [DebugKit documentation](https://pieceofcake2.github.io/debug_kit/)). Add it only
when the plugin is loaded, so that the application still works without the development
dependencies:

```php
class AppController extends Controller
{
    public function __construct($request = null, $response = null)
    {
        if (CakePlugin::loaded('DebugKit')) {
            $this->components[] = 'DebugKit.Toolbar';
        }
        parent::__construct($request, $response);
    }
}
```

The toolbar is only shown when `debug` is greater than 0.

Load the other plugins in `config/bootstrap.php` with `CakePlugin::load()` (or
`CakePlugin::loadAll()`). `CakePlugin::load()` throws a `MissingPluginException` when the
plugin is not installed, so wrap the plugins that are development dependencies as shown
above.

## Directory Layout

```
my_app/
├── bin/
│   └── cake            the console
├── config/             configuration (core.php, bootstrap.php, routes.php, database.php, ...)
├── logs/               log files
├── plugins/            plugins installed by Composer
├── resources/
│   └── locales/        translation files
├── src/                PHP code
│   ├── Console/        shells, tasks and bake templates
│   ├── Controller/
│   ├── Lib/
│   ├── Model/
│   └── View/           helpers
├── templates/          view templates, layouts, elements and email templates
├── tests/
│   ├── Fixture/
│   └── TestCase/
├── tmp/                cache, sessions and test files
├── vendor/             Composer packages, including CakePHP
└── webroot/            document root (index.php, test.php, css/, js/, img/)
```

`webroot/index.php`, `webroot/test.php` and `bin/cake` load `config/define.php`, which sets
the root directory (`ROOT`), the application directory (`APP_DIR`, `src`), the document root
(`WWW_ROOT`) and the Composer vendor directory (`VENDORS`). CakePHP finds the other
directories by itself: `config/`, `tests/`, `tmp/`, `logs/`, `templates/` and
`resources/locales/` are used when they exist.

The legacy layout with an `app/` directory (`app/Config/`, `app/Controller/`,
`app/View/`, ...) is supported as well, but the skeleton only ships the modern layout.

## Configuration

| File | Contents |
| --- | --- |
| `config/core.php` | Debug level, error and exception handling, caching, session, security salts. |
| `config/bootstrap.php` | Cache configuration, plugin loading, logging and other application setup. |
| `config/routes.php` | URL routing. |
| `config/database.php` | Database connections (`default` and `test`). Copy it from `database.php.default`. |
| `config/email.php` | Email transports and profiles. Copy it from `email.php.default`. |
| `config/acl.php`, `config/acl.ini.php` | Configuration of `IniAcl` and `PhpAcl`. |
| `config/define.php` | Path constants of the modern layout. |
| `config/Schema/` | Schema files of the DbAcl, i18n and session tables. |

## Environment Variables (.env)

Settings such as the debug level, security salt and database credentials can be read from
environment variables. For local development, copy the example file and edit it:

```bash
cp config/.env.example config/.env
# or, in the project root:
cp config/.env.example .env
```

`config/.env` and `.env` in the project root are loaded at the top of `config/core.php` (so
they apply to web requests, `bin/cake` and tests) using
[josegonzalez/dotenv](https://github.com/josegonzalez/php-dotenv). Both are ignored by Git.
Values are then available through `env()`, `getenv()`, `$_ENV` and `$_SERVER`.

This follows the `.env` support of the CakePHP 3+/5 application skeleton
([cakephp/app](https://github.com/cakephp/app) loads `config/.env` with josegonzalez/dotenv in
`config/bootstrap.php`), backported for CakePHP 2 and extended to also accept a `.env` in the
project root.

| Variable | Setting | Default |
| --- | --- | --- |
| `DEBUG` | `debug` (`0`, `1`, `2`, `true` = 2, `false` = 0) | `2` |
| `SECURITY_SALT` | `Security.salt` | value in `config/core.php` |
| `SECURITY_CIPHER_SEED` | `Security.cipherSeed` (digits only) | value in `config/core.php` |
| `DB_DATASOURCE`, `DB_HOST`, `DB_PORT`, `DB_USERNAME`, `DB_PASSWORD`, `DB_DATABASE` | `default` connection in `config/database.php` | values in `config/database.php` |
| `DB_TEST_DATASOURCE`, `DB_TEST_HOST`, `DB_TEST_PORT`, `DB_TEST_USERNAME`, `DB_TEST_PASSWORD`, `DB_TEST_DATABASE` | `test` connection in `config/database.php` | values in `config/database.php` |

- Precedence: real environment variables (web server, shell, container, CI) > `config/.env` >
  root `.env`. `config/.env` is loaded first and the root `.env` second; a variable that is
  already set is never overwritten.
- Unset or empty variables keep the defaults, so the application behaves exactly as before
  without a `.env` file (an empty `DB_PASSWORD` / `DB_TEST_PASSWORD` is allowed).
- Quote values in `.env` files (e.g. `DEBUG="0"`). Unquoted numbers are normalized by the
  parser (`007` becomes `7`, and long digit strings such as a cipher seed overflow).
- `.env` files are meant for development. In production, set real environment variables
  instead.
- The database variables are read by `config/database.php.default`. If you created
  `config/database.php` before this was added, copy the `__construct()` and `applyEnv()`
  methods into it.

## Running the Application

For development, start PHP's built-in web server with:

```bash
bin/cake server
```

and open `http://localhost:8765/`. Use `--port` (`-p`) to choose another port.

In production, point the document root of your web server to `webroot/`, never
to the project directory, which contains `config/`, `logs/`, `tmp/`, `vendor/`
and `.env` files. The `webroot/.htaccess` file rewrites the requests to
`index.php` for Apache with `mod_rewrite`. For Apache and nginx examples, see
[Web Server Configuration](https://pieceofcake2.github.io/docs/installation/url-rewriting.html)
in the book.

## Development

### Running Tests

Put your test cases in `tests/TestCase/` and your fixtures in `tests/Fixture/`. Configure the
`test` connection in `config/database.php` (or with the `DB_TEST_*` variables), then run all
tests with PHPUnit:

```bash
vendor/bin/phpunit
```

or a single test case with the test shell:

```bash
bin/cake test app Controller/PostsController
```

### Code Standards

Check your code against the CakePHP coding standard:

```bash
vendor/bin/phpcs
```

### Static Analysis

Run PHPStan (level 5) on `src/` and `config/`:

```bash
vendor/bin/phpstan analyse
```

The configuration is in `phpstan.neon` and uses the
[pieceofcake2/phpstan-cakephp2](https://pieceofcake2.github.io/phpstan-cakephp2/) extension.
