# Ramlfications-php

PHP 7 implementation of the [RAML 1.0 Specification](https://github.com/raml-org/raml-spec/blob/master/versions/raml-10/raml-10.md)
heavily influenced from the [Spotify Ramlfications](https://github.com/spotify/ramlfications) design.

## Current Status: Work in Progress
This library is a best attempt at fully implementing the [RAML 1.0 Specification](https://github.com/raml-org/raml-spec/blob/master/versions/raml-10/raml-10.md),
then focusing on providing backwards compatibility for [RAML 0.8](https://github.com/raml-org/raml-spec/blob/master/versions/raml-08/raml-08.md).

This library should not be used in a production environment at the time of writing.

## Requirements

- PHP 7.0+

## Installation

### Using [Composer](https://getcomposer.org)

To install kong-php with Composer, just add the following to your `composer.json` file:

```json
{
    "require-dev": {
        "therealgambo/ramlfications-php": "master-dev"
    }
}
```

or by running the following command:

```shell
composer require therealgambo/kong-php
```