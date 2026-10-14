# CakePHP 2.x Application Skeleton

[![GitHub License](https://img.shields.io/github/license/pieceofcake2/app?label=License)](LICENSE)
[![Packagist Version](https://img.shields.io/packagist/v/pieceofcake2/app?label=Packagist)](https://packagist.org/packages/pieceofcake2/app)
![PHP](https://img.shields.io/packagist/dependency-v/pieceofcake2/app/php?logo=php&logoColor=%23FFFFFF&label=PHP&labelColor=%23777BB4&color=%23FFFFFF)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%205-brightgreen?label=PHPStan)](https://github.com/pieceofcake2/app/actions/workflows/CI.yml)
![CakePHP](https://img.shields.io/packagist/dependency-v/pieceofcake2/app/pieceofcake2/cakephp?logo=cakephp&logoColor=%23FFFFFF&label=CakePHP&labelColor=%23D33C43&color=%23FFFFFF)

The application skeleton for CakePHP 2 applications running on
[pieceofcake2/cakephp](https://github.com/pieceofcake2/cakephp), with a directory layout
compatible with CakePHP 5 (`config/`, `src/`, `templates/`, `tests/`, `webroot/`) and `.env`
support.

> [!WARNING]
> CakePHP 2.x is for the maintenance of existing applications. For new projects, use
> the current major version of CakePHP instead.

## Requirements

- PHP 8.0+
- Composer
- MySQL 5.6+, PostgreSQL 9.4+, SQLite 3 or SQL Server 2022+

## Installation

```bash
composer create-project pieceofcake2/app my_app
```

Then copy `config/database.php.default` to `config/database.php` (or use `config/.env`) and
change the security salts in `config/core.php`.

The Bake and DebugKit plugins are development dependencies. `config/bootstrap.php` loads
them when they are installed, so `bin/cake bake` works out of the box and
`composer install --no-dev` still works in production.

## Documentation

Documentation: https://pieceofcake2.github.io/app/

It covers the installation, the directory layout, the configuration and `.env` files,
running tests and PHPStan, and migrating an existing application to the modern layout.

## Contributing

See the [contribution guidelines](https://github.com/pieceofcake2/.github/blob/master/CONTRIBUTING.md).

## License

Licensed under the MIT License. See [LICENSE](LICENSE).
