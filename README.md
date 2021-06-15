# React HTTP Fiber example

## Requirements

* PHP 8.1 (you can install it using [phpbuild](https://github.com/php-build/php-build) or compiling [last alpha](https://www.php.net/index.php#id2021-06-10-1))
* [trowski/react-fiber](https://github.com/trowski/react-fiber)
* [react/http](https://github.com/reactphp/http)
* [antidot-fw/react-framework](https://github.com/antidot-framework/react-framework) Using Child class to fork server in different threads. 

## Install

Using composer

```bash
git clone https://github.com/kpicaza/react-php81-fibers-test fibers
cd fibers
composer install --ignore-platform-reqs
php fiber.php
```

To use multi-thread check `fiber.php` file.
