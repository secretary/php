# Secretary - Secrets Manager for PHP
[![Latest Stable Version](https://poser.pugx.org/secretary/core/version)](https://packagist.org/packages/secretary/core) [![Total Downloads](https://poser.pugx.org/secretary/core/downloads)](https://packagist.org/packages/secretary/core)

Secrets are an important aspect of most applications you can build. How you store them, and keep them "secret" is a challenge.
Luckily, there are tools you can use to keep them all safe. 

Secretary is a tool to integrate your PHP application with these tools.

## Table of Contents

1. [Installation](#installation)
2. [Api Documentation](#api-documentation)
    1. [Secretary\Manager](#manager-class)
        1. [Initializing](#manager-constructor)
        2. [getSecret](#manager-getSecret)
        3. [putSecret](#manager-putSecret)
        4. [deleteSecret](#manager-deleteSecret)
        5. [getAdapter](#manager-getAdapter)
    2. [Secretary\Secret](#secret-class)
        1. [getKey](#secret-getKey)
        1. [getValue](#secret-getValue)

## Installation

```bash
$ composer req secretary/core
```

##### Choose the version you need

| Version (X.Y.Z) |    PHP     |  Symfony   | Comment             |
|:---------------:|:----------:|:----------:|:--------------------|
|      `2.*`      | `>= 8.1.0` | `5.4, 6.0` | **Current version** |
|      `1.*`      | `>= 7.4.0` |   `5.4`    | Previous version    |

By itself, the core is useless. You will also need to add at least one adapter:

| Storage Engine | Badges |
| -------------- | -------- |
| [AWS Secrets Manager][aws-secrets-manager-adapter] | [![Latest Stable Version](https://poser.pugx.org/secretary/aws-secrets-manager-adapter/version)](https://packagist.org/packages/secretary/aws-secrets-manager-adapter) [![Total Downloads](https://poser.pugx.org/secretary/aws-secrets-manager-adapter/downloads)](https://packagist.org/packages/secretary/aws-secrets-manager-adapter) |
| [HashiCorp Vault][hashicorp-vault-adapter] | [![Latest Stable Version](https://poser.pugx.org/secretary/hashicorp-vault-adapter/version)](https://packagist.org/packages/secretary/hashicorp-vault-adapter) [![Total Downloads](https://poser.pugx.org/secretary/hashicorp-vault-adapter/downloads)](https://packagist.org/packages/secretary/hashicorp-vault-adapter) |
| [JSON File][json-file-adapter] | [![Latest Stable Version](https://poser.pugx.org/secretary/local-json-file-adapter/version)](https://packagist.org/packages/secretary/local-json-file-adapter) [![Total Downloads](https://poser.pugx.org/secretary/local-json-file-adapter/downloads)](https://packagist.org/packages/secretary/local-json-file-adapter) |

There are also miscellaneous packages that add on to Secretary 

| Package | Purpose | Badges |
| ------- | ------- | ------ |
| [PSR-6 Cache Adapter][psr-6-cache-adapter] | Allows for caching secrets using a PSR-6 Cache Interface | [![Latest Stable Version](https://poser.pugx.org/secretary/psr-6-cache-adapter/version)](https://packagist.org/packages/secretary/psr-6-cache-adapter) [![Total Downloads](https://poser.pugx.org/secretary/psr-6-cache-adapter/downloads)](https://packagist.org/packages/secretary/psr-6-cache-adapter) |
| [PSR-16 Cache Adapter][psr-16-cache-adapter] | Allows for caching secrets using a PSR-16 Cache Interface | [![Latest Stable Version](https://poser.pugx.org/secretary/psr-16-cache-adapter/version)](https://packagist.org/packages/secretary/psr-16-cache-adapter) [![Total Downloads](https://poser.pugx.org/secretary/psr-16-cache-adapter/downloads)](https://packagist.org/packages/secretary/psr-16-cache-adapter) |
| [Secretary Bundle][secretary-bundle] | Allows for integrating with the Symfony Framework | [![Latest Stable Version](https://poser.pugx.org/secretary/secretary-bundle/version)](https://packagist.org/packages/secretary/secretary-bundle) [![Total Downloads](https://poser.pugx.org/secretary/secretary-bundle/downloads)](https://packagist.org/packages/secretary/secretary-bundle) |

## Api Documentation

There's two classes you interface with in Secretary:

* [`Secretary\Manager`][Secretary\Manager::class]
* [`Secretary\Secret`][Secretary\Secret::class]

<a name="manager-class" />

### Secretary\Manager

<a name="manager-constructor" />

#### Secretary\Manager->__construct(AdapterInterface $adapter)

Pass in your desired adapter.

```php
<?php
use Secretary\Manager;
use Secretary\Adapter\AWS\SecretsManager\LocalJSONFileAdapter;

$manager = new Manager(
    new LocalJSONFileAdapter([
        'region'      => 'us-east-1',
        'credentials' => [
            'accessKeyId'     => 'myAccessKeyId',
            'secretAccessKey' => 'mySecretAccessKey'
        ]
    ])
);
```

Optionally, you may wrap your adapter, with one of the two cache adapters.

```php
<?php
use Secretary\Manager;
use Secretary\Adapter\AWS\SecretsManager\LocalJSONFileAdapter;

use Secretary\Adapter\Cache\PSR6Cache\ChainAdapter;
use Cache\Adapter\Apc\ApcCachePool;

$manager = new Manager(
    new ChainAdapter(
        new LocalJSONFileAdapter([
            'region'      => 'us-east-1',
            'credentials' => [
                'accessKeyId'     => 'myAccessKeyId',
                'secretAccessKey' => 'mySecretAccessKey'
            ]
        ]),
        new ApcCachePool()
    )
);
```

For mor information on the arguments and options for the adapters, view their respective documentation.

<a name="manager-getSecret" />

#### Secretary\Manager->getSecret(string $key, ?array $options): Secret

Fetches a secret from the configured adapter. `$key` is the name of the secret (or path) you are trying to get.

Certain adapters will take custom options as well, like VersionId and VersionStage for the AWS SecretsManager Adapter

This will throw a `Secretary\SecretNotFoundException` if the secret cannot be found

```php
$secret = $manager->getSecret('databases/redis/dsn');
/*
Secret {
    "path" = "databases/redis/dsn",
    "value" = "redis://localhost:6379"
}
*/
```

Some adapters also support storing a key/value map as a secret's value.

```php
$secret = $manager->getSecret('databases/redis');
/*
Secret {
    "path" = "databases/redis",
    "value" = [
        "dsn" => "redis://localhost:6379",
        "password" => "my_super_strong_password" 
    ]
}
*/
```

<a name="manager-putSecret" />

#### Secretary\Manager->putSecret(string $key, string|array $value, ?array $options): void

Puts a secret with the given `$value`, into the storage engine, under the given `$key`.

If the current adapter doesn't support arrays, and you pass one it, it will throw a `Secretary\ValueNotSupportedException`.

Again, some adapters allow passing in custom options to send along with the request.

```php
$manager->putSecret('database/redis', 'postgres://localhost:5432');
```

And for adapters that support a key/value map as a value: 

```php
$manager->putSecret('database/redis', ['dsn' => 'redis://localhost:6379', 'password' => 'my_super_strong_password']);
```

<a name="manager-deleteSecret" />

#### Secretary\Manager->deleteSecret(string $key, ?array $options): void

Deletes a secret from the storage engine using the given `$key`.

Again, some adapters allow passing in custom options to send along with the request.

```php
$manager->deleteSecret('database/redis');
```

<a name="manager-getAdapter" />

#### Secretary\Manager->getAdapter(): AdapterInterface

Will return the adapter that was passed to this manager during construction.

<a name="secret-class" />

### Secretary\Secret

This class implements ArrayAccess, so if your secret supports passing a key/value map, you can grab straight from the map:

Secrets are immutable, so attempting to change a value will throw an Exception.

```php
$secret = $manager->getSecret('database/redis');

$dsn = $secret['dsn'];
```

<a name="secret-getKey" />

#### Secretary\Secret->getKey(): string

Returns the key for the secret

```php
$secret = $manager->getSecret('dabase/redis');

$secret->getKey() === 'database/redis'; // true
```

<a name="secret-getValue" />

#### Secretary\Secret->getValue(): string | array

Returns the value for the secret. If the secret is a key/value map, its an array

```php
$secret = $manager->getSecret('dabase/redis/dsn');

$secret->getValue() === 'redis://localhost:6379'; // true

// Or

$secret = $manager->getSecret('dabase/redis');

print_r($secret->getValue()); 
/*
[
    "dsn" => "redis://localhost:6379",
    "password" => "my_super_strong_password" 
]
*/
```


[aws-secrets-manager-adapter]: https://github.com/secretary/php-aws-secrets-manager-adapter 
[hashicorp-vault-adapter]: https://github.com/secretary/php-hashicorp-vault-adapter 
[json-file-adapter]: https://github.com/secretary/php-json-file-adapter 
[psr-6-cache-adapter]: https://github.com/secretary/php-psr-6-cache-adapter 
[psr-16-cache-adapter]: https://github.com/secretary/php-psr-16-cache-adapter 
[secretary-bundle]: https://github.com/secretary/php-secretary-bundle
[Secretary\Manager::class]: https://github.com/secretary/php/blob/master/src/Core/src/Manager.php
[Secretary\Secret::class]: https://github.com/secretary/php/blob/master/src/Core/src/Secret.php
