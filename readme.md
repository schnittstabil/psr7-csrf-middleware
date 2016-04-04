# Psr7\Csrf\Middleware [![Build Status](https://travis-ci.org/schnittstabil/psr7-csrf-middleware.svg?branch=master)](https://travis-ci.org/schnittstabil/psr7-csrf-middleware) [![Coverage Status](https://coveralls.io/repos/github/schnittstabil/psr7-csrf-middleware/badge.svg?branch=master)](https://coveralls.io/github/schnittstabil/psr7-csrf-middleware?branch=master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/schnittstabil/psr7-csrf-middleware/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/schnittstabil/psr7-csrf-middleware/?branch=master) [![Code Climate](https://codeclimate.com/github/schnittstabil/psr7-csrf-middleware/badges/gpa.svg)](https://codeclimate.com/github/schnittstabil/psr7-csrf-middleware)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/189bdd73-5c9b-489f-bdd9-a4d139250502/big.png)](https://insight.sensiolabs.com/projects/189bdd73-5c9b-489f-bdd9-a4d139250502)

> Stateless PSR-7 CSRF (Cross-Site Request Forgery) protection middleware - simple Slim Framework 3 integration.


## Install

```sh
$ composer require schnittstabil/psr7-csrf-middleware
```


## Usage

```php
<?php
require __DIR__.'/vendor/autoload.php';

use Schnittstabil\Psr7\Csrf\MiddlewareBuilder as CsrfMiddlewareBuilder;

/*
 * Shared secret key used for generating and validating CSRF tokens:
 */
$key = 'This key is not so secret - change it!';

/*
 * Build a stateless Synchronizer Token Pattern CSRF proptection middleware.
 */
$csrfMiddleware = CsrfMiddlewareBuilder::create($key)
    ->buildSynchronizerTokenPatternMiddleware();

/*
 * Build a (AngularJS compatible) stateless Cookie-To-Header CSRF proptection middleware.
 *
 * Requires additional dependency:
 *     composer require dflydev/fig-cookies
 */
$csrfMiddleware = CsrfMiddlewareBuilder::create($key)
    ->buildCookieToHeaderMiddleware();
?>
```


### Slim Example

```php
<?php
/*
 * Requires additional dependency:
 *     composer require slim/slim
 */

require __DIR__.'/vendor/autoload.php';

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\App;
use Schnittstabil\Psr7\Csrf\MiddlewareBuilder as CsrfMiddlewareBuilder;

$app = new App();

/*
 * CSRF protection setup
 */
$app->getContainer()['csrf_token_name'] = 'X-XSRF-TOKEN';
$app->getContainer()['csrf'] = function ($c) {
    $key = 'This key is not so secret - change it!';

    return CsrfMiddlewareBuilder::create($key)
        ->buildSynchronizerTokenPatternMiddleware($c['csrf_token_name']);
};
$app->add('csrf');

/*
 * GET routes are not protected (by default)
 */
$app->get('/', function (RequestInterface $request, ResponseInterface $response) {
    $name = $this->csrf_token_name;
    $token = $this->csrf->getTokenService()->generate();

    // render HTML...
    $response = $response->write("<input type=\"hidden\" name=\"$name\" value=\"$token\" />");

    return $response->write('successfully GET!');
});

/*
 * POST routes are protected (by default; same applies to PUT, DELETE and PATCH)
 */
$app->post('/', function (RequestInterface $request, ResponseInterface $response) {
    return $response->write('successfully POST');
});

/*
 * Run application
 */
$app->run();
?>
```


## Related

* [schnittstabil/csrf-tokenservice](https://github.com/schnittstabil/csrf-tokenservice) – the underlying (stateless) token service
* [schnittstabil/csrf-twig-helpers](https://github.com/schnittstabil/csrf-twig-helpers) – Twig helpers for token rendering
* [Slim-Csrf](https://github.com/slimphp/Slim-Csrf) – stateful (session based) CSRF protection


## License

MIT © [Michael Mayer](http://schnittstabil.de)
