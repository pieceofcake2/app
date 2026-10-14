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
   values that must not be used in a real application. See
   [Generating the Security Salts](#generating-the-security-salts).
3. If you send email, copy `config/email.php.default` to `config/email.php` and configure
   your transport.
4. Make sure that the web server can write to `tmp/` and `logs/`.

`config/database.php`, `config/email.php` and the `.env` files are ignored by Git.

### Loading the Development Plugins

The plugins installed by Composer are not loaded automatically. Load them in
`config/bootstrap.php`:

```php
CakePlugin::loadAll();
```

`CakePlugin::loadAll()` loads the plugins that are installed, so it also works when the
development plugins are not installed (`composer install --no-dev`). To load the plugins
one by one, make sure that `CakePlugin::load()` only runs where the plugin is installed,
because loading a missing plugin throws a `MissingPluginException`.

Then add the DebugKit toolbar to your `AppController` as described in the
[DebugKit documentation](https://pieceofcake2.github.io/debug_kit/), and run Bake with
`bin/cake bake`.

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

Use the `'aes'` type with its own key of 32 random bytes or more instead. The skeleton does
not use `CookieComponent` and does not read `COOKIE_KEY`, so wire it in
`src/Controller/AppController.php` when you add cookies:

```php
public $components = [
    'Cookie' => ['type' => 'aes'],
];

public function beforeFilter()
{
    parent::beforeFilter();
    $this->Cookie->key = env('COOKIE_KEY');
}
```

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

In production, point the document root of your web server to `webroot/`. The
`webroot/.htaccess` file rewrites the requests to `index.php` for Apache with
`mod_rewrite`.

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
