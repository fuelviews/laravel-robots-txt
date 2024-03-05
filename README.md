# Fuelviews laravel robots.txt package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/fuelviews/laravel-robots-txt.svg?style=flat-square)](https://packagist.org/packages/fuelviews/laravel-robots-txt)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/fuelviews/laravel-robots-txt/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/fuelviews/laravel-robots-txt/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/fuelviews/laravel-robots-txt/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/fuelviews/laravel-robots-txt/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/fuelviews/laravel-robots-txt.svg?style=flat-square)](https://packagist.org/packages/fuelviews/laravel-robots-txt)

The Fuelviews laravel robots.txt package is engineered to significantly accelerate the development and management of robots.txt files for laravel applications. This package seamlessly integrates into your laravel project, offering an efficient and streamlined approach to controlling how search engines interact with your website. By leveraging this package, developers can swiftly configure search engine access rules, directly from the application's configuration, thus dramatically speeding up the development process. With its focus on rapid configuration and deployment, the Fuelviews laravel robots.txt package ensures that managing your site's search engine visibility becomes a quick, hassle-free aspect of your development workflow, allowing you to focus on building and optimizing your application.

## Installation

You can require the package and it's dependencies via composer:

```bash
composer require fuelviews/laravel-robots-txt
```
You can manually publish the config file with:

```bash
php artisan vendor:publish --provider="Fuelviews\RobotsTxt\RobotsTxtServiceProvider" --tag="robots-txt-config"
```
This is the contents of the published config file:

```php
<?php

return [
    'disk' => 'public',
    'deny_development_url' => 'development.test2',
    'user_agents' => [
        '*' => [
            'Allow' => [
                '/'
            ],
            'Disallow' => [
                '/admin',
                '/dashboard',
            ],
        ],
    ],
    'sitemap' => [
        'sitemap.xml',
    ],
];
```

You can manually clear the robots.txt cache with:

```bash
php artisan robots-txt:clear
```

## Usage

To access the robots.txt, navigate to your application's URL and append /robots.txt to it.

For example, if your application is hosted at http://example.com, the sitemap can be found at http://example.com/robots.txt.
## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/fuelviews/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

If you've found a bug regarding security please mail [support@fuelviews.com](mailto:support@fuelviews.com) instead of using the issue tracker.

## Credits

- [Thejmitchener](https://github.com/thejmitchener)
- [Fuelviews](https://github.com/fuelviews)
- [Spatie](https://github.com/spatie)
- [All Contributors](../../contributors)

## Support us

Fuelviews is a web development agency based in Portland, Maine. You'll find an overview of all our projects [on our website](https://fuelviews.com).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
