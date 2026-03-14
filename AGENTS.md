# Agent Guidelines

This document describes the conventions and guidelines for contributing to this repository.

## Requirements

- **PHP**: `>=8.2`
- **Laravel**: `^11.0 | ^12.0 | ^13.0`
  - Laravel 11 and 12 require PHP 8.2+
  - Laravel 13 requires PHP 8.3+

## Code Style

Code style is enforced with [Laravel Pint](https://github.com/laravel/pint) using the `laravel` preset. Run the linter with:

```bash
vendor/bin/pint
```

### Imports and Docblocks

- **Docblocks must use fully qualified class names (FQCNs)** — e.g. `@param \Google\Client $client`, not `@param GoogleClient $client`.
- **`use` statements must only be added for actual code usage** — never add an import solely for a docblock reference. Use the FQCN directly in the docblock instead.

```php
// Good
use Google\Client as GoogleClient;

/**
 * @return \Google\Client
 */
public function getClient(): GoogleClient
{
    return $this->client;
}

// Bad — import added only for docblock
use Google\Client;

/**
 * @return Client
 */
public function getClient(): GoogleClient
{
    return $this->client;
}
```

## Testing

Tests are written with [Pest](https://pestphp.com/) (`^3.7`). Run the test suite with:

```bash
composer test
# or directly:
vendor/bin/pest
```

Run with coverage (requires Xdebug):

```bash
vendor/bin/pest --coverage
```

## Dependencies

- [`google/apiclient`](https://github.com/google/google-api-php-client): `^2.19`
- [`mockery/mockery`](https://github.com/mockery/mockery): `^1.6` (dev)
- [`pestphp/pest`](https://pestphp.com/): `^3.7` (dev)

## CI

The GitHub Actions workflow (`.github/workflows/run-tests.yml`) tests against:

| PHP   | Laravel        |
|-------|----------------|
| 8.2   | 11, 12         |
| 8.3   | 11, 12, 13     |
| 8.4   | 11, 12, 13     |

Both `prefer-lowest` and `prefer-stable` dependency versions are tested.
