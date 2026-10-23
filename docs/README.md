# Application Skeleton Documentation

pieceofcake2/app is the application skeleton for CakePHP 2 applications running on
[pieceofcake2/cakephp](https://github.com/pieceofcake2/cakephp). It uses a directory layout
compatible with CakePHP 5 (`config/`, `src/`, `templates/`, `tests/`, `webroot/`) and
loads CakePHP and all dependencies with Composer. Its application classes are in the `App`
namespace, as in CakePHP 5, and its templates are `.php` files in the directories of CakePHP 5
(`layout/`, `element/`, `email/`, `Error/`). For global classes and `.ctp` templates, use
[pieceofcake2/app-legacy](https://github.com/pieceofcake2/app-legacy).

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

To move an existing application to this layout and to namespaced classes, see
[Migrating to the Modern Layout](migrating-to-the-modern-layout.md): the `layout` and `namespace`
commands of [pieceofcake2/upgrade](https://pieceofcake2.github.io/upgrade/) do it.

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
and the CakePHP coding standard) are installed as development dependencies, with the Markdown
docs of the CakePHP 2 book for AI coding tools (`pieceofcake2/docs`, in
`vendor/pieceofcake2/docs/docs/`).

Then finish the configuration:

1. Copy `config/database.php.default` to `config/database.php`, and set the connection with
   `DATABASE_URL` in `config/.env.local` (see [Environment Variables (.env)](#environment-variables-env)
   and [Database and Mailer DSNs](#database-and-mailer-dsns)) or enter it in the file. Set the
   test database in `config/.env.test.local` (see [Running Tests](#running-tests)).
2. Change `Security.salt` and `Security.cipherSeed` in `config/core.php`, or set
   `SECURITY_SALT` and `SECURITY_CIPHER_SEED` in `config/.env.local`. The skeleton ships with well-known example
   values that must not be used in a real application. See
   [Generating the Security Salts](#generating-the-security-salts).
3. If you send email, copy `config/email.php.default` to `config/email.php`, and set the
   transport with `MAILER_DSN` or configure it in the file.
4. Make sure that the web server can write to `tmp/` and `logs/`.

### Version Control

The `.gitignore` file of the skeleton is meant for your application, so you can run
`git init` right after `create-project`. It ignores:

- `vendor/` and the plugins that Composer installs into `plugins/` (`plugins/Bake/` and
  `plugins/DebugKit/`). If you install more plugins with Composer, add them to
  `.gitignore`; the plugins of your own application in `plugins/` are committed.
- the contents of `tmp/` and `logs/`. The `empty` files that keep the directories are
  committed.
- `config/database.php` and `config/email.php`, and the `.local` files of `.env`
  (`config/.env.local` and `config/.env.*.local`), which hold the
  settings and credentials of each machine. `config/.env` and `config/.env.test` are
  committed, as in Symfony; keep real secrets in the `.local` files or in real environment
  variables.
- `phpunit.xml` (a local copy of `phpunit.xml.dist`) and `.phpunit.result.cache`.

Commit `composer.lock`, so that every environment installs the same versions of the
dependencies.

### Development Plugins

`config/bootstrap.php` loads the [Bake](https://pieceofcake2.github.io/bake/) and
[DebugKit](https://pieceofcake2.github.io/debug_kit/) plugins when they are installed:

```php
use Cake\Core\CakePlugin;
use Cake\Error\MissingPluginException;

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
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Core\CakePlugin;

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
├── src/                PHP code (the App namespace)
│   ├── Console/        shells, tasks and bake templates
│   ├── Controller/
│   ├── Model/
│   └── View/           helpers
├── templates/          view templates (.php)
│   ├── Error/          error pages
│   ├── Pages/          pages of PagesController
│   ├── element/        elements (Flash/ for the flash messages)
│   ├── email/          email templates (html/, text/)
│   └── layout/         layouts (email/ for the email layouts)
├── tests/              tests (the App\Test namespace)
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

### Namespace

The classes of `src/` are in the `App` namespace, which follows their directories:
`App\Controller\PostsController` in `src/Controller/PostsController.php`,
`App\Model\Post` in `src/Model/Post.php`. Composer loads them with PSR-4, `App\` from `src/`
and `App\Test\` from `tests/`, so a new class file needs no `composer dump-autoload`:

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

`config/core.php` tells CakePHP the namespace, so that it finds the controllers, models,
components, helpers, behaviors and shells of the application in it:

```php
Configure::write('App.namespace', 'App');
```

The classes of CakePHP are in the `Cake` namespace and are imported with `use` statements, also
in `config/` and in the templates, which stay without a namespace:

```php
namespace App\Controller;

use Cake\Controller\Controller;

class AppController extends Controller
{
}
```

The settings that name a class use `::class`, e.g. `'renderer' => ExceptionRenderer::class` in
`config/core.php`. The names of CakePHP 2 (`'File'`, `'Auth'`, `'DebugKit.Toolbar'`) keep
working.

## Configuration

| File | Contents |
| --- | --- |
| `config/core.php` | Debug level, error and exception handling, the namespace of the application, caching, session, security salts. |
| `config/bootstrap.php` | Cache configuration, plugin loading, logging and other application setup. |
| `config/routes.php` | URL routing. |
| `config/database.php` | Database connections (`default`, and `test` with `APP_ENV=test`). Copy it from `database.php.default`. |
| `config/email.php` | Email transports and profiles. Copy it from `email.php.default`. |
| `config/acl.php`, `config/acl.ini.php` | Configuration of `IniAcl` and `PhpAcl`. |
| `config/define.php` | Path constants of the modern layout. |
| `config/Schema/` | Schema files of the DbAcl, i18n and session tables. |

## Environment Variables (.env)

Settings such as the debug level, security salt and database credentials are read from
environment variables. As in Symfony, `config/core.php` loads them from `.env` files in
`config/` with [symfony/dotenv](https://symfony.com/doc/current/configuration.html#configuring-environment-variables-in-env-files),
so they apply to web requests, `bin/cake` and tests:

```php
// config/core.php

use Symfony\Component\Dotenv\Dotenv;

(new Dotenv())->usePutenv()->loadEnv(CONFIG . '.env');
```

The files are loaded in this order and merged; a later file wins over an earlier one:

| File | Loaded | In Git |
| --- | --- | --- |
| `config/.env` | always (required) | committed: the defaults of the application |
| `config/.env.local` | except when `APP_ENV` is `test` | ignored: your machine |
| `config/.env.{APP_ENV}` (e.g. `config/.env.test`) | always | committed: the defaults of an environment |
| `config/.env.{APP_ENV}.local` | always | ignored: your machine, for that environment |

- A variable that is set in the real environment (web server, shell, container, CI) is never
  overwritten, so real environment variables win over all files. The values are written to
  `$_SERVER`, `$_ENV` and `putenv()`, so `env()`, `getenv()`, `$_SERVER` and `$_ENV` return
  them.
- `APP_ENV` comes from the real environment, otherwise from the files: `config/.env` sets
  `APP_ENV=dev`. `vendor/bin/phpunit`, `bin/cake test` and `webroot/test.php` set
  `APP_ENV=test` when it is not set (see [Running Tests](#running-tests)). In production, set
  `APP_ENV=prod` as a real environment variable.
- `config/.env` must exist; the skeleton ships it. `config/.env.local` and the other `.local`
  files hold only what differs on your machine, such as `DATABASE_URL` and the salts. Never put
  production secrets in a committed file.
- Values do not need quotes unless they contain spaces. Inside double quotes, `$NAME` and
  `${NAME}` are replaced with the value of a variable; single-quote a value that contains `$`.

| Variable | Setting | Default |
| --- | --- | --- |
| `APP_ENV` | the environment: `dev`, `test` or `prod`; selects `config/.env.{APP_ENV}` | `dev` (from `config/.env`) |
| `APP_DEBUG` | `debug` (`0`, `1`, `2`) | `0` |
| `SECURITY_SALT` | `Security.salt` | value in `config/core.php` |
| `SECURITY_CIPHER_SEED` | `Security.cipherSeed` (1 to 4294967295, legacy `'cipher'` cookies only) | value in `config/core.php` |
| `COOKIE_KEY` | `Cookie.key`, the `CookieComponent` key for the `'aes'` type | `null` (`Security.salt` is used) |
| `DATABASE_URL` | `default` connection in `config/database.php` (also `test` with `APP_ENV=test`), as a DSN | values in `config/database.php` |
| `MAILER_DSN` | transport of the `default` profile in `config/email.php`, as a Symfony Mailer DSN | values in `config/email.php` |

- Unset or empty variables keep the defaults.
- In production, set `APP_ENV=prod` and the other settings as real environment variables.
- `DATABASE_URL` is read by `config/database.php.default`, and `MAILER_DSN` by
  `config/email.php.default`. If you created `config/database.php` or `config/email.php` from
  an earlier skeleton, copy the `__construct()` method into it (and remove the `applyEnv()`
  method of the former `DB_*` variables and the former `APP_ENV` branch). Copy the `APP_ENV`
  default of `tests/bootstrap.php`, `bin/cake` and `webroot/test.php` from the skeleton too.

### Migrating from an Earlier Skeleton

Applications created from pieceofcake2/app 2.4.x load the `.env` files with
josegonzalez/dotenv. To switch to the loading above:

1. `composer remove josegonzalez/dotenv` and `composer require symfony/dotenv`.
2. Replace the `.env` loading at the top of `config/core.php` with the two lines above, and
   the `Configure::write('debug', ...)` line with
   `Configure::write('debug', env('APP_DEBUG') ?? 0);`.
3. Move a root `.env` to `config/`: the root `.env` files are no longer read. Commit
   `config/.env` with the defaults and `APP_ENV=dev` (see the skeleton's file), and move the
   values of your machine and the secrets to `config/.env.local`.
4. Keep only what the tests change in `config/.env.test`, and only what differs on your
   machine in `config/.env.test.local`: the files are merged now.
5. In `.gitignore`, ignore `/config/.env.local` and `/config/.env.*.local` instead of
   `config/.env` and the root `.env` files.
6. In production, set `APP_ENV=prod` (and the other settings) as real environment variables.

### Database and Mailer DSNs

A DSN sets a whole connection in one string, as in CakePHP 3.x and later and in Symfony. The
`'dsn'` key of a connection in `config/database.php` and of a profile in `config/email.php`
takes the DSN, and the skeleton sets it from the environment variables of Symfony
(`DATABASE_URL`, `MAILER_DSN`):

```php
// config/database.php
$this->default['dsn'] = env('DATABASE_URL');
if (env('APP_ENV') === 'test') {
    $this->test = $this->default;
}

// config/email.php
$this->default['dsn'] = env('MAILER_DSN');
```

There is one variable for the database, as in Symfony: `DATABASE_URL` is the database of the
current environment, and both connections use it. In the tests (`APP_ENV=test`) it is the test
database of `config/.env.test`.

```dotenv
# MySQL
DATABASE_URL="mysql://user:password@localhost:3306/my_app?encoding=utf8mb4"
# PostgreSQL
DATABASE_URL="postgres://user:password@localhost:5432/my_app?schema=public"
# SQLite: three slashes for a path relative to the working directory, four for an absolute path
DATABASE_URL="sqlite:////var/db/my_app.sqlite"
# SQLite in memory (e.g. in config/.env.test.local)
DATABASE_URL="sqlite:///:memory:"
# SQL Server
DATABASE_URL="sqlserver://user:password@localhost:1433/my_app?encoding=utf8"

# SMTP with STARTTLS on port 587
MAILER_DSN="smtp://user:password@smtp.example.com:587"
# SMTP over SSL/TLS (port 465)
MAILER_DSN="smtps://user:password@smtp.example.com"
# Local SMTP server for development (e.g. Mailpit)
MAILER_DSN="smtp://localhost:1025"
# PHP mail()
MAILER_DSN="sendmail://default"
# Do not send (Debug transport)
MAILER_DSN="null://null"
```

- The scheme selects the datasource (`mysql`, `postgres`, `sqlite`, `sqlserver`) or the
  transport (`smtp` / `smtps` for `Smtp`, `sendmail` / `native` for `Mail`, `null` for `Debug`).
- Query parameters set the other keys: `encoding`, `schema`, `prefix`, `persistent=true` or
  `flags[1002]=...` for a database, `timeout`, `client` or `tls=1` for SMTP.
- Encode reserved characters of a password: `%40` for `@`, `%3F` for `?`, `%23` for `#`,
  `%25` for `%`. For example, `p@ss?` is `p%40ss%3F`.
- The keys in the DSN replace the same keys in `config/database.php` or `config/email.php`,
  and the other keys are kept. The keys of the skeleton files are defaults: a DSN without a
  password (`mysql://root@localhost/my_app`) keeps `'password' => 'password'` of the file, so
  write `mysql://root:@localhost/my_app` for an empty password.
- An unset or empty variable is ignored, and the keys of the file are used.
- Plugins can add schemes (e.g. `ses+api://` of an Amazon SES plugin) with
  `CakeEmail::setDsnClassMap()` and `ConnectionManager::setDsnClassMap()` in their bootstrap.
- See [Datasources](https://pieceofcake2.github.io/docs/models/datasources.html) and
  [Email](https://pieceofcake2.github.io/docs/core-utility-libraries/email.html) in the
  book for all the rules.

#### Docker and CI

Pass the DSNs as environment variables. With Docker Compose:

```yaml
services:
  app:
    environment:
      DATABASE_URL: "mysql://my_app:secret@db:3306/my_app?encoding=utf8mb4"
      MAILER_DSN: "smtp://mailpit:1025"
  db:
    image: mysql:8.4
    environment:
      MYSQL_DATABASE: my_app
      MYSQL_USER: my_app
      MYSQL_PASSWORD: secret
      MYSQL_RANDOM_ROOT_PASSWORD: "yes"
  mailpit:
    image: axllent/mailpit
```

In GitHub Actions, set `DATABASE_URL` to the test database for the test step. Real environment
variables win over `config/.env.test`:

```yaml
      - name: Run tests
        env:
          DATABASE_URL: mysql://root:root@127.0.0.1:3306/test_app?encoding=utf8mb4
        run: |
          cp config/database.php.default config/database.php
          vendor/bin/phpunit
```

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

To append both to `config/.env.local`, which is ignored by Git (the last line wins when a
variable is set twice):

```bash
php -r 'printf("SECURITY_SALT=\"%s\"\nSECURITY_CIPHER_SEED=\"%d\"\n", bin2hex(random_bytes(32)), random_int(1, 4294967295));' >> config/.env.local
```

or without PHP:

```bash
printf 'SECURITY_SALT="%s"\nSECURITY_CIPHER_SEED="%s"\n' "$(openssl rand -hex 32)" "$(od -An -N4 -tu4 /dev/urandom | tr -dc 0-9)" >> config/.env.local
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

`config/core.php` sets `Cookie.type` and `Cookie.key`, next to the salts, so no controller
changes are needed:

```php
Configure::write('Cookie.type', 'aes');
Configure::write('Cookie.key', env('COOKIE_KEY') ?: null);
```

`CookieComponent` uses an explicit `'type'`/`'key'` setting (or `type()` and
`$this->Cookie->key`) first, then `Cookie.type`/`Cookie.key`, then `'cipher'` and
`Security.salt`.

The `'aes'` type needs a key of at least 32 bytes. When `COOKIE_KEY` is unset, `Cookie.key` is
`null` and the key is `Security.salt`; if the salt is shorter than 32 bytes, writing or reading
an encrypted cookie throws a `CakeException`.

Generate the key with `php -r 'echo bin2hex(random_bytes(32)), PHP_EOL;'` or
`openssl rand -hex 32`, and set it as `COOKIE_KEY` in `config/.env.local` (or as a real environment
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
and `.env` files. Set `APP_ENV=prod` and the other settings as real environment
variables (see [Environment Variables (.env)](#environment-variables-env)). The
`webroot/.htaccess` file rewrites the requests to
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

- `templates/layout/default.php`: the top navigation bar and the content container. While
  `debug` is 2, the SQL log is shown below the content.
- `templates/layout/error.php`: a minimal layout for the error pages in production.
- `templates/layout/flash.php`: the page rendered by `Controller::flash()`.
- `templates/element/Flash/`: `default`, `success`, `error`, `warning` and `info` flash
  messages (`$this->Flash->success('Saved.')` and so on). Messages are escaped; pass
  `['params' => ['escape' => false]]` to output HTML. Click a message to hide it.

Views baked with [pieceofcake2/bake](https://pieceofcake2.github.io/bake/) use the same
styles.

### Home Page

`templates/Pages/home.php` is shown at `/` while `debug` is enabled. It checks the
environment of the application: the PHP version and extensions (mbstring, intl, openssl,
PDO and the driver of the `default` datasource), the `tmp/` and `logs/` directories, the
cache, the database connection (with the error message when it fails), the
security salt and cipher seed, and the DebugKit and Bake plugins. Replace it with your own
page before turning `debug` off: in production it throws a `NotFoundException`.

![Home page](img/home.png)

On a phone:

![Home page on a phone](img/home-mobile.png)

### Error Pages

When `debug` is 0, `templates/Error/error400.php` and `error500.php` render a short
message in the `error` layout:

![Error 400 in production](img/error400.png)

![Error 500 in production](img/error500.png)

In debug mode, they use the default layout and show the stack trace, like the other error
pages of CakePHP.

### Flash Messages

![Flash messages](img/flash-messages.png)

### Existing Applications

Applications created from an earlier version of the skeleton are not affected: they own
their templates and `webroot/css/`. To use the new look, copy `templates/layout/`,
`templates/element/Flash/`, `templates/Error/`, `webroot/css/`, `webroot/font/` and
`webroot/img/cake.logo.svg` into your application (or the `.ctp` templates of
[pieceofcake2/app-legacy](https://github.com/pieceofcake2/app-legacy), with `Layouts/`, `Elements/`
and `Errors/`). Views that you baked for
`cake.generic.css` need to be baked again (or restyled), see
[Baked Views and the Legacy Theme](https://pieceofcake2.github.io/bake/#baked-views-and-the-legacy-theme).

## Development

### Running Tests

Put your test cases in `tests/TestCase/` and your fixtures in `tests/Fixture/`, in the
`App\Test\TestCase` and `App\Test\Fixture` namespaces (e.g.
`App\Test\TestCase\Controller\PostsControllerTest` in
`tests/TestCase/Controller/PostsControllerTest.php`).
`vendor/bin/phpunit` and `bin/cake test` run with `APP_ENV=test` by default: `tests/bootstrap.php`
and `bin/cake` set it before CakePHP loads the `.env` files when it is not set (`webroot/test.php`,
the browser test runner, does the same). So the tests load `config/.env.test` after
`config/.env`, without `config/.env.local`. `config/.env.test` is committed and changes only
what the tests need: `DATABASE_URL` is the test database (the `default` connection, which
`config/database.php` copies to the `test` connection with `APP_ENV=test`) and `MAILER_DSN` is `null://null`. Set your test database in
`config/.env.test.local`, which is ignored by Git, or as a real environment variable, which
wins over the files, as CI does:

```dotenv
# config/.env.test.local
DATABASE_URL="mysql://my_app:secret@localhost:3306/test_my_app?encoding=utf8mb4"
```

Your development `DATABASE_URL` stays in `config/.env.local`, which the tests do not load. A
real environment variable wins over the files, so do not export your development `DATABASE_URL` or another `APP_ENV` in the shell where
you run the tests: the fixtures drop and create tables.

Then run the tests with PHPUnit:

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
[pieceofcake2/panther](https://pieceofcake2.github.io/panther/)
([Markdown](https://github.com/pieceofcake2/panther/blob/1.x/docs/README.md)), which runs
them the way Symfony Panther does: the application starts with PHP's built-in web server and
headless Chrome drives it. To use them:

1. Install the package and a chromedriver that matches your Chrome:

   ```bash
   composer require --dev "pieceofcake2/panther:^1.0.1" dbrekelmans/bdi
   vendor/bin/bdi detect drivers
   ```

2. Uncomment the `ServerExtension` in `phpunit.xml.dist`. It keeps the web server and the
   browser running for the whole run; nothing starts until an End-to-End test runs.
3. Write tests that extend `PieceOfCake2\Panther\TestSuite\PantherTestCase` in
   `tests/TestCase/EndToEnd/`, and run them with `vendor/bin/phpunit --testsuite EndToEnd`.

The web server that Panther starts inherits the environment of PHPUnit, including
`APP_ENV=test`. It loads `config/.env.test` (and `config/.env.test.local`) too, where `DATABASE_URL`
(the `default` connection of the pages) is the test database, so the pages read the fixtures
of the tests. No other setting is needed. Panther's `PANTHER_APP_ENV` is only needed to give
the web server a different `APP_ENV` than the tests.

The tests are configured with Panther's `PANTHER_*` variables. `phpunit.xml.dist` sets
`PANTHER_ERROR_SCREENSHOT_DIR=./tmp/error-screenshots` for the screenshots of the failed tests.

Set per-machine values, such as `PANTHER_NO_HEADLESS=1` (to watch the browser), in your
shell or in a `phpunit.xml` copy of `phpunit.xml.dist`. In CI, run the End-to-End tests in a
separate job; see the panther documentation for the variables, the test database and a
GitHub Actions example.

### Code Standards

Check your code against the CakePHP coding standard:

```bash
vendor/bin/phpcs
```

`phpcs.xml` uses the `CakePHP2Namespace` standard of
[pieceofcake2/phpcs-cakephp2](https://pieceofcake2.github.io/phpcs-cakephp2/): the classes are
declared in a namespace, in a file named after the class, and the classes that they use are
imported with `use` statements. `config/` keeps its global classes (`DATABASE_CONFIG`,
`EmailConfig` and the schema classes).

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
