
# Api Error

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Github Actions][ico-gh-actions]][link-gh-actions]
[![Codecov][ico-codecov]][link-codecov]
[![Total Downloads][ico-downloads]][link-downloads]
[![Software License][ico-license]](LICENSE.md)

This package provides an implementation for API error formatting. It can be integrated throughout your code and should result in a standardized error response format for HTTP APIs.

## Quick start

Since handling the exceptions is up to the framework, here are a list of supported integrations:

### Symfony HTTP Foundation Based Framework

- [Symfony](https://github.com/jenky/api-error-bundle)
- [Laravel](https://github.com/jenky/hades)

### Bring Your Own

You can install the package via composer:

```bash
composer require jenky/api-error
```

The usage may vary depending on your project. Typically, you should handle it in your global exception handler. Here is a minimal example:

```php
use Jenky\ApiError\Formatter\GenericErrorFormatter;
use Jenky\ApiError\Transformer\ChainTransformer;


$transformer = new ChainTransformer([
    // ... list of transformers
])
$formatter = new GenericErrorFormatter(true, $transformer);

// or simply without transformer and debug is off
$formatter = new GenericErrorFormatter();

/** @var \Throwable $exception */
return \json_encode($formatter->format($exception));
```

## Building Blocks

### Error Formatter

The error formatter is the main entry point of the package. It formats the `Throwable` exception into a serializable version, allowing the data structure to be used as the response body, typically in `JSON`. An error formatter must implement [`ErrorFormatter`](https://github.com/jenky/api-error/blob/main/src/Formatter/ErrorFormatter.php).

Internally, the error formatter transforms the exception into a [`Problem`](https://github.com/jenky/api-error/blob/main/src/Problem.php), which should return an array as context data for the given exception. This array can contain anything, and when combined with the predefined response error format, will be used to replace placeholders with contextual data.

You can always create your own `Problem` using the [Exception Transformer](#exception-transformations).

`GenericErrorFormatter`

```js
{
    'message' => '{message}', // The exception message
    'status' => '{status_code}', // The corresponding HTTP status code, defaults to 500
    'code' => '{code}' // The exception int code
    'debug' => '{debug}', // The debug information
}
```

`Rfc7807ErrorFormatter`

```js
{
    'type' => '{type}',
    'title' => '{status_text}', // The corresponding HTTP status text
    'detail' => '{message}',
    'status' => '{status_code}', // The corresponding HTTP status code, defaults to 500
    'invalid-params' => '{invalid_params}',
    'debug' => '{debug}', // The debug information
}
```

- Placeholder names MUST correspond to keys in the context array.
- Placeholder names MUST be delimited with a single opening brace `{` and a single closing brace `}`. There MUST NOT be any whitespace between the delimiters and the placeholder name.
- Placeholder names SHOULD be composed only of the characters `A-Z`, `a-z`, `0-9`, underscore `_`, and period `.`.

**Custom Error Format**

Create your own custom formatter that implements [`ErrorFormatter`](https://github.com/jenky/api-error/blob/main/src/Formatter/ErrorFormatter.php). Alternatively, you can extend the [`AbstractErrorFormatter`](https://github.com/jenky/api-error/blob/main/src/Formatter/AbstractErrorFormatter.php), provided for the sake of convenience, and define your own error format in the `getFormat` method.

### Exception Transformations

An exception transformer is used to customize the transformation of a `Throwable` exception into a [`Problem`](https://github.com/jenky/api-error/blob/main/src/Problem.php), allowing you to add or modify the context data. If you want to add custom transformations, you should create a new class that implements the [`ExceptionTransformer`](https://github.com/jenky/api-error/blob/main/src/Transformer/ExceptionTransformer.php) and pass it as a second argument of the error formatter.

```php
new GenericFormatter(transformer: new MyExceptionTransformer);
// or
new Rfc7807ErrorFormatter(transformer: new MyExceptionTransformer);
```

If you have multiple transformers, use the [`ChainTransformer`](https://github.com/jenky/api-error/blob/main/src/Transformer/ChainTransformer.php) to run all of them.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email contact@lynh.me instead of using the issue tracker.

## Credits

- [Lynh](https://github.com/jenky)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/jenky/api-error.svg?style=for-the-badge
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=for-the-badge
[ico-gh-actions]: https://img.shields.io/github/actions/workflow/status/jenky/api-error/testing.yml?branch=main&label=actions&logo=github&style=for-the-badge
[ico-codecov]: https://img.shields.io/codecov/c/github/jenky/api-error?logo=codecov&style=for-the-badge
[ico-downloads]: https://img.shields.io/packagist/dt/jenky/api-error.svg?style=for-the-badge

[link-packagist]: https://packagist.org/packages/jenky/api-error
[link-gh-actions]: https://github.com/jenky/api-error
[link-codecov]: https://codecov.io/gh/jenky/api-error
[link-downloads]: https://packagist.org/packages/jenky/api-error

