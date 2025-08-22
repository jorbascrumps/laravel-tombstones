> [!WARNING]
> This package is in **early development** and should be considered **experimental**. Expect frequent changes, incomplete features, and breaking updates.
>
> Contributions and feedback are welcome, but use in production is **not recommended** until a stable version is tagged.
>
> Track progress and open issues here: [GitHub Issues](https://github.com/jorbascrumps/laravel-tombstones/issues)

# Laravel Tombstones

Track and identify dead code in your Laravel applications.

## Overview

Laravel Tombstones helps you identify potentially dead code by placing markers (tombstones) in your codebase and tracking whether they're executed. This makes it safer to remove unused code paths and keep your application lean.

## Installation

Install via Composer:

```bash
composer require jorbascrumps/laravel-tombstones
```

## Basic Usage

### Placing Tombstones

Add tombstone markers to code you suspect might be dead:

```php
class UserController extends Controller
{
    public function legacyMethod()
    {
        tombstone('legacy-user-method');
        
        // Your potentially dead code here
        return view('legacy.user');
    }
}
```

### Adding Context

Provide additional context when placing tombstones:

```php
tombstone('checkout-flow-v1', [
    'user_id' => auth()->id(),
]);
```

### Generating Reports

Run the report command to see which tombstones are alive or dead:

```bash
php artisan tombstone:report
```

> [!TIP]
> Start by adding tombstones to code paths you're unsure about, then monitor them over several weeks of normal application usage.

## Basic Configuration

You can define configuration variables in your `.env` file:

| Variable | Default | Description |
|---|---|---|
| `TOMBSTONE_DRIVER` | `local` | Storage driver for tombstone logs |
| `TOMBSTONE_PATH` | `storage_path('tombstones')` | Path where tombstone logs are stored |
| `TOMBSTONE_FILENAME` | `tombstones.jsonl` | Log filename |
| `TOMBSTONE_TRACE_DEPTH` | `1` | Stack trace depth for context |

## Advanced Usage

### Custom Readers and Writers

First publish the configuration file:

```bash
php artisan vendor:publish --provider="Jorbascrumps\LaravelTombstone\ServiceProvider"
```

Implement the provided reader/ writer contracts:

```php
use Jorbascrumps\LaravelTombstone\Contracts\TombstoneWriterContract;

class DatabaseWriter implements TombstoneWriterContract
{
    public function write(Tombstone $tombstone): void
    {
        // Your custom storage logic
    }
}
```

Register your custom implementation in:

```php
'writer' => YourCompany\YourApplication\DatabaseWriter::class,
```
