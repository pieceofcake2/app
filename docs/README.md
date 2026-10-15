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
- [Pages and Styles](#pages-and-styles)
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
   values that must not be used in a real application. See
   [Generating the Security Salts](#generating-the-security-salts).
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
| `SECURITY_CIPHER_SEED` | `Security.cipherSeed` (1 to 4294967295, legacy `'cipher'` cookies only) | value in `config/core.php` |
| `COOKIE_KEY` | `Cookie.key`, the `CookieComponent` key for the `'aes'` type (read by the component from 2.12.6) | `null` (`Security.salt` is used) |
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

### Generating the Security Salts

Generate `Security.salt` and `Security.cipherSeed` with a cryptographically secure random
generator. These commands work in any POSIX shell on Linux and macOS (the cipher seed is
only for the legacy `'cipher'` cookie type; see
[cipherSeed and the 'cipher' cookie type](#cipherseed-and-the-cipher-cookie-type)):

```bash
# SECURITY_SALT: 64 hexadecimal characters
php -r 'echo bin2hex(random_bytes(32)), PHP_EOL;'
openssl rand -hex 32                               # without PHP

# SECURITY_CIPHER_SEED: a number from 1 to 4294967295
php -r 'echo random_int(1, 4294967295), PHP_EOL;'
od -An -N4 -tu4 /dev/urandom | tr -dc 0-9; echo    # without PHP
```

To append both to `config/.env` after copying `config/.env.example` (the last line wins
when a variable is set twice):

```bash
php -r 'printf("export SECURITY_SALT=\"%s\"\nexport SECURITY_CIPHER_SEED=\"%d\"\n", bin2hex(random_bytes(32)), random_int(1, 4294967295));' >> config/.env
```

or without PHP:

```bash
printf 'export SECURITY_SALT="%s"\nexport SECURITY_CIPHER_SEED="%s"\n' "$(openssl rand -hex 32)" "$(od -An -N4 -tu4 /dev/urandom | tr -dc 0-9)" >> config/.env
```

- Keep the salt at least 32 characters long: it is the default key of `CookieComponent`,
  and the `'aes'` cookie type rejects shorter keys.
- Do not use a longer cipher seed: only 1 to 4294967295 is meaningful (see below).
- Use different values in each environment, and do not commit them. In production, set
  real environment variables.
- Changing `Security.salt` later logs users out, invalidates open forms, makes encrypted
  cookies unreadable, breaks data encrypted with `Security::encrypt()` without an explicit
  HMAC salt, and breaks passwords hashed with `SimplePasswordHasher` (bcrypt hashes are not
  affected). Changing `Security.cipherSeed` only breaks values encrypted with
  `Security::cipher()`, such as cookies of the default `'cipher'` type.

See [Generating the Security Salts](https://pieceofcake2.github.io/docs/development/configuration#security-salts)
in the book for details and for how to rotate the values safely.

#### cipherSeed and the 'cipher' cookie type

`Security.cipherSeed` is used only by `Security::cipher()`, that is, by the `'cipher'`
encryption type of `CookieComponent`, which is the default type and is insecure.

- `Security::cipher()` calls `mt_srand((int)(float)$seed)`, so only 1 to 4294967295 is
  meaningful. Values with 27 or more digits, and about 95% of 26-digit values, end up as
  seed `0`. This includes the 29-digit default of `config/core.php` in the original
  CakePHP 2 and in earlier versions of this skeleton, the blog tutorial example, and values
  generated by the command in earlier versions of `config/.env.example`. The fallback in
  `config/core.php` is now an in-range example value; replace it all the same.
- An in-range seed does not make `'cipher'` secure: there are only about 4.3 billion seeds,
  and the keystream is a non-cryptographic Mersenne Twister XOR, so it can be brute-forced.
- Changing the seed makes existing `'cipher'` cookies unreadable.

Use the `'aes'` type with its own key of 32 random bytes or more instead, and make sure
`Security.useOpenSsl` is `true`. The skeleton sets it in `config/core.php`; without it,
`Security::encrypt()`/`decrypt()` call mcrypt, which does not exist on PHP 8, and `'aes'`
cookies fail (see
[Enable OpenSSL](https://pieceofcake2.github.io/docs/appendices/migrating-from-cakephp-2-10#enable-openssl)).

From pieceofcake2/cakephp 2.12.6, `Cookie.type` and `Cookie.key` can be set in configuration.

**2.12.6 and later:** `config/core.php` already sets both, next to the salts, so no controller
changes are needed:

```php
Configure::write('Cookie.type', 'aes');
Configure::write('Cookie.key', env('COOKIE_KEY') ?: null);
```

`CookieComponent` uses an explicit `'type'`/`'key'` setting (or `type()` and
`$this->Cookie->key`) first, then `Cookie.type`/`Cookie.key`, then `'cipher'` and
`Security.salt`.

**2.12.5 and earlier:** `CookieComponent` does not read `Cookie.type` and `Cookie.key`, so
the lines above have no effect. The skeleton does not use `CookieComponent`; when you add
cookies, pass the type and the key in `src/Controller/AppController.php`:

```php
public $components = [
    'Cookie' => ['type' => 'aes'],
];

public function beforeFilter()
{
    parent::beforeFilter();
    $key = Configure::read('Cookie.key');
    if ($key) {
        $this->Cookie->key = $key;
    }
}
```

Assign the key only when it is set, because a `null` key would replace the default key and
encryption would fail. This code keeps working on 2.12.6 and later.

The `'aes'` type needs a key of at least 32 bytes. When `COOKIE_KEY` is unset, `Cookie.key` is
`null` and the key is `Security.salt`; if the salt is shorter than 32 bytes, writing or reading
an encrypted cookie throws a `CakeException`.

Generate the key with `php -r 'echo bin2hex(random_bytes(32)), PHP_EOL;'` or
`openssl rand -hex 32`, and set it as `COOKIE_KEY` in `config/.env` (or as a real environment
variable in production). Switching an existing application from `'cipher'` to `'aes'` loses
the existing cookie values once (for example, remember-me logins). See
[cipherSeed and the 'cipher' Cookie Type](https://pieceofcake2.github.io/docs/development/configuration#cipher-seed)
in the book for the impact, which option fits your application, and the migration steps.

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

## Pages and Styles

The layouts, the home page, the error pages and the flash messages are ported from the
[CakePHP 5 application skeleton](https://github.com/cakephp/app) and use its stylesheets in
`webroot/css/`:

| File | Description |
| --- | --- |
| `normalize.min.css` | [normalize.css](https://github.com/necolas/normalize.css) 8.0.1 |
| `milligram.min.css` | [Milligram](https://milligram.io) 1.4.1 |
| `fonts.css` | The Raleway font, served from `webroot/font/` (no request to a font service) |
| `cake.css` | The application styles: layout, flash messages, forms, tables, paginator, error pages and debug output |
| `home.css` | The styles of the home page only |
| `cake.generic.css` | The stylesheet of the earlier skeleton. The templates no longer load it; it is kept for views that still use its classes (for example views baked with the `legacy` bake theme) and is loaded by the fallback layouts of the core |

Templates:

- `templates/Layouts/default.ctp`: the top navigation bar and the content container. While
  `debug` is 2, the SQL log is shown below the content.
- `templates/Layouts/error.ctp`: a minimal layout for the error pages in production.
- `templates/Layouts/flash.ctp`: the page rendered by `Controller::flash()`.
- `templates/Elements/Flash/`: `default`, `success`, `error`, `warning` and `info` flash
  messages (`$this->Flash->success('Saved.')` and so on). Messages are escaped; pass
  `['params' => ['escape' => false]]` to output HTML. Click a message to hide it.

Views baked with [pieceofcake2/bake](https://pieceofcake2.github.io/bake/) use the same
styles.

### Home Page

`templates/Pages/home.ctp` is shown at `/` while `debug` is enabled. It checks the
environment of the application: the PHP version and extensions (mbstring, intl, openssl,
PDO and the driver of the `default` datasource), the `tmp/` and `logs/` directories, the
cache, the database connection (with the error message when it fails), `config/.env`, the
security salt and cipher seed, and the DebugKit and Bake plugins. Replace it with your own
page before turning `debug` off: in production it throws a `NotFoundException`.

![Home page](img/home.png)

On a phone:

![Home page on a phone](img/home-mobile.png)

### Error Pages

When `debug` is 0, `templates/Errors/error400.ctp` and `error500.ctp` render a short
message in the `error` layout:

![Error 400 in production](img/error400.png)

![Error 500 in production](img/error500.png)

In debug mode, they use the default layout and show the stack trace, like the other error
pages of CakePHP.

### Flash Messages

![Flash messages](img/flash-messages.png)

### Existing Applications

Applications created from an earlier version of the skeleton are not affected: they own
their templates and `webroot/css/`. To use the new look, copy `templates/Layouts/`,
`templates/Elements/Flash/`, `templates/Errors/`, `webroot/css/`, `webroot/font/` and
`webroot/img/cake.logo.svg` into your application. Views that you baked for
`cake.generic.css` need to be baked again (or restyled), see
[Baked Views and the Legacy Theme](https://pieceofcake2.github.io/bake/#baked-views-and-the-legacy-theme).

## Development

### Running Tests

Put your test cases in `tests/TestCase/` and your fixtures in `tests/Fixture/`. Configure the
`test` connection in `config/database.php` (or with the `DB_TEST_*` variables), then run the
tests with PHPUnit:

```bash
vendor/bin/phpunit
```

or a single test case with the test shell:

```bash
bin/cake test app Controller/PostsController
```

`phpunit.xml.dist` has two test suites:

| Suite | Directory | Run with |
| --- | --- | --- |
| `App` | `tests/TestCase/`, without `EndToEnd/` | `vendor/bin/phpunit` (the default suite) |
| `EndToEnd` | `tests/TestCase/EndToEnd/` | `vendor/bin/phpunit --testsuite EndToEnd` |

`vendor/bin/phpunit --testsuite App,EndToEnd` runs both. `defaultTestSuite="App"` in
`phpunit.xml.dist` keeps `vendor/bin/phpunit` fast and free of a browser. **Delete that one
attribute to make `vendor/bin/phpunit` run everything**, as a Symfony application does.

#### End-to-End Tests

`tests/TestCase/EndToEnd/` is for browser End-to-End tests with
[pieceofcake2/e2e-test](https://pieceofcake2.github.io/e2e-test/): they start the
application with PHP's built-in web server and drive it with headless Chrome. To use them:

1. Install the package and a chromedriver that matches your Chrome:

   ```bash
   composer require --dev pieceofcake2/e2e-test dbrekelmans/bdi
   vendor/bin/bdi detect drivers
   ```

2. Uncomment the `E2EExtension` in `phpunit.xml.dist`. It stops the server and the browsers
   after the last test; they start only when an End-to-End test runs.
3. Write tests that extend `Pieceofcake2\E2ETest\TestSuite\E2ETestCase` in
   `tests/TestCase/EndToEnd/`, and run them with `vendor/bin/phpunit --testsuite EndToEnd`.

The End-to-End tests are configured with environment variables. `phpunit.xml.dist` sets two:

- `E2E_SERVER_DATASOURCE=test`: the application server uses the `test` connection, where
  the fixtures are. It sets `E2E_DATASOURCE=test` in the server, and
  `config/database.php.default` then uses the `test` connection as `default`. If you created
  `config/database.php` before this was added, copy the `E2E_DATASOURCE` lines of the
  constructor into it.
- `E2E_SERVER_ENV_DEBUG=0`: `DEBUG=0` in the application server.

Set per-machine values, such as `PANTHER_CHROME_DRIVER_BINARY` or `PANTHER_NO_HEADLESS=1`
(to watch the browser), in your shell, in `config/.env`, or in a `phpunit.xml` copy of
`phpunit.xml.dist`. Failure screenshots, the page HTML and the server logs are saved to
`tmp/e2e-artifacts/`. In CI, run the End-to-End tests in a separate job; see the
[e2e-test documentation](https://pieceofcake2.github.io/e2e-test/) for the variables, the
test database and a GitHub Actions example.

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

## Origin

This package is based on the `app/` directory of
[cakephp/cakephp](https://github.com/cakephp/cakephp) (`cakephp/cakephp` on Packagist, last upstream
version 2.10.24), originally by the CakePHP community (Cake Software Foundation, Inc.). It was
extracted into a separate package and is maintained for CakePHP 2 on modern PHP by
[pieceofcake2](https://github.com/pieceofcake2). Changes from the original are listed in the
[commit history](https://github.com/pieceofcake2/app/commits/2.x).
