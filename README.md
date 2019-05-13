# LaravelUp (Laravel 5.7)

[![Downloads](https://poser.pugx.org/movor/laravel-up/downloads)](https://packagist.org/packages/movor/laravel-up)
[![License](https://poser.pugx.org/movor/laravel-up/license)](https://packagist.org/packages/movor/laravel-up)

### Laravel framework packed with tools to get started fast

Laravel framework packed with tools, packages and assets necessary for most of the projects we're doing in Movor.
Maybe some of you will find it interesting.

---

## Docker

### Require php (composer) package

```bash
docker-compose run --no-deps composer require provider/package
# Sometimes we need to ignore platform requirements
docker-compose run --no-deps composer require provider/package --ignore-platform-reqs
```

### Build assets for production

```bash
docker-compose run --no-deps node yarn prod
```