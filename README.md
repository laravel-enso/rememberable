# Rememberable

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/2eba208ec82d485786715915ec75f8bf)](https://www.codacy.com/app/laravel-enso/Rememberable?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=laravel-enso/Rememberable&amp;utm_campaign=Badge_Grade)
[![StyleCI](https://styleci.io/repos/90758167/shield?branch=master)](https://styleci.io/repos/90758167)
[![License](https://poser.pugx.org/laravel-enso/rememberable/license)](https://packagist.org/packages/laravel-enso/rememberable)
[![Total Downloads](https://poser.pugx.org/laravel-enso/rememberable/downloads)](https://packagist.org/packages/laravel-enso/rememberable)
[![Latest Stable Version](https://poser.pugx.org/laravel-enso/rememberable/version)](https://packagist.org/packages/laravel-enso/rememberable)

Model caching for Laravel

This package can work independently of the [Enso](https://github.com/laravel-enso/Enso) ecosystem.

For live examples and demos, you may visit [laravel-enso.com](https://www.laravel-enso.com)

## Installation

Comes pre-installed in Enso.

To install outside of Enso: `composer require laravel-enso/rememberable`

## Features

- comes with 2 traits that provide helper methods for quick and easy caching usage (setting and retrieving)
- the cache lifetime may be set per model, else, if not set, the per-project setting is used, finally falling back to a default of 60 minutes if neither option is available
- uses the Laravel `cache()` helper method so is transparent to the cache mechanism/implementation

### Configuration & Usage

Be sure to check out the full documentation for this package available at [docs.laravel-enso.com](https://docs.laravel-enso.com/backend/rememberable.html)

### Contributions

are welcome. Pull requests are great, but issues are good too.

### License

This package is released under the MIT license.
