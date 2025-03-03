
# Api Error

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Github Actions][ico-gh-actions]][link-gh-actions]
[![Codecov][ico-codecov]][link-codecov]
[![Total Downloads][ico-downloads]][link-downloads]
[![Software License][ico-license]](LICENSE.md)

This package provides an implementation for API error formatting. It can be integrated throughout your code and should result in a standardized error response format for HTTP APIs.

Since handling the exceptions is up to the framework, here are a list of supported integrations:

## Symfony Foundation Based Framework
- [Symfony](https://github.com/jenky/api-error-bundle)
- [Laravel](https://github.com/jenky/hades)

## Bring Your Own

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

