# Laravel Aparser connector
[A-Parser](https://en.a-parser.com/) - a multi-threaded parser of search engines, site assessment services, keywords, content (text, links, random data) and much more (youtube, pictures, translators...). A-Parser combines over 60 parsers in total!

A-Parser version with API is available only to subscribers of Enterprise license.

This package provides the opportunity to connect to A-Parser`s native API.

## Installation
1) Install the package by running this command in your terminal/cmd:
```
composer require "seobrain/laraparser"
```

2) Optionally, you can import config file by running this command in your terminal/cmd:
```
php artisan vendor:publish --provider="Seobrain\Laraparser\LaraparserServiceProvider"
```

Available ENV variables:

* (string) APARSER_HOST => Your A-Parser instance API endpoint (make sure you have /API path at the end)
* (string) APARSER_PASSWORD => Your A-Parser instance password

```
APARSER_HOST=http://****:9091/API
APARSER_PASSWORD=****
```

Make sure you have a proper configuration values being cached:
```
php artisan config:cache
```

## How to use

```php
// Initialize $aparser class
$aparser = new Seobrain\Laraparser\Laraparser(config('laraparser.host'), config('laraparser.password'));

// Make a request
$aparser->ping(); // pong
```

Full list of API methods is available here:
https://en.a-parser.com/wiki/user-api/

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details
