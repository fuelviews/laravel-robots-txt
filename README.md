# Laravel sitemap package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/fuelviews/laravel-sitemap.svg?style=flat-square)](https://packagist.org/packages/fuelviews/laravel-sitemap)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/fuelviews/laravel-sitemap/run-tests.yml?label=tests&style=flat-square)](https://github.com/fuelviews/laravel-sitemap/actions/workflows/run-tests.yml)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/fuelviews/laravel-sitemap/php-cs-fixer.yml?label=code%20style&style=flat-square)](https://github.com/fuelviews/laravel-sitemap/actions/workflows/php-cs-fixer.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/fuelviews/laravel-sitemap.svg?style=flat-square)](https://packagist.org/packages/fuelviews/laravel-sitemap)

Laravel sitemap is a robust and easy-to-use solution designed to automatically generate sitemaps for your Laravel application.

## Installation

You can require the package and it's dependencies via composer:

```bash
composer require fuelviews/laravel-sitemap
```

You can manually publish the config file with:

```bash
php artisan vendor:publish --provider="Fuelviews\Sitemap\SitemapServiceProvider" --tag="sitemap-config"
```

## Usage

You can also add your models directly by implementing the Spatie\Sitemap\Contracts\Sitemapable interface. You also need to define your post_model in the fv-sitemap.php config file.

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Contracts\Sitemapable;
use Spatie\Sitemap\Tags\Url;

class Post extends Model implements Sitemapable {
    /**
     * Convert the Post model instance into a sitemap URL entry.
     *
     * @return \Spatie\Sitemap\Tags\Url
     */
    public function toSitemapUrl() {
        $url = Url::create(url("{$this->id}"))
            ->setLastModificationDate($this->updated_at)
            ->setChangeFrequency('daily')
            ->setPriority(0.8);

        return $url;
    }
}
```

You can generate the sitemap with:

```bash
php artisan sitemap:generate
```

You can link to the sitemap with:

```blade
route('sitemap')
```


## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/fuelviews/.github/blob/main/CONTRIBUTING.md) for details.

## Credits

- [Thejmitchener](https://github.com/thejmitchener)
- [Sweatybreeze](https://github.com/sweatybreeze)
- [Fuelviews](https://github.com/fuelviews)
- [Spatie](https://github.com/spatie)
- [All Contributors](../../contributors)

## Support us

Fuelviews is a web development agency based in Portland, Maine. You'll find an overview of all our projects [on our website](https://fuelviews.com).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
