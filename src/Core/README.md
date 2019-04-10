# Secretary - Secrets Manager for PHP

Secrets are an important aspect of most applications you can build. How you store them, and keep them "secret" is a challenge.
Luckily, there are tools you can use to keep them all safe. 

Secretary is a tool to integrate your PHP application with these tools.

## Table of Contents

1. [Installation](#installation)
2. [Api Documentation](#api-documentation)
    1. [Initializing](#constructor)
    1. [getSecret](#getSecret)
    1. [putSecret](#putSecret)
    1. [deleteSecret](#deleteSecret)

### Installation

```bash
$ composer require secretary/core
```

By itself, the core is useless. You will also need to add at least one adapter:

| Storage Engine | Badges |
| -------------- | -------- |
| [AWS Secrets Manager][aws-secrets-manager-adapter] | [![Latest Stable Version](https://poser.pugx.org/secretary/php-aws-secrets-manager-adapter/version)](https://packagist.org/packages/secretary/php-aws-secrets-manager-adapter) [![Total Downloads](https://poser.pugx.org/secretary/php-aws-secrets-manager-adapter/downloads)](https://packagist.org/packages/secretary/php-aws-secrets-manager-adapter) |
| [HashiCorp Vault][hashicorp-vault-adapter] | [![Latest Stable Version](https://poser.pugx.org/secretary/php-hashicorp-vault-adapter/version)](https://packagist.org/packages/secretary/php-hashicorp-vault-adapter) [![Total Downloads](https://poser.pugx.org/secretary/php-hashicorp-vault-adapter/downloads)](https://packagist.org/packages/secretary/php-hashicorp-vault-adapter) |

There are also miscellaneous packages that add on to Secretary 

| Package | Purpose | Badges |
| ------- | ------- | ------ |
| [PSR-6 Cache Adapter][psr-6-cache-adapter] | Allows for caching secrets using a PSR-6 Cache Interface | [![Latest Stable Version](https://poser.pugx.org/secretary/php-psr-6-cache-adapter/version)](https://packagist.org/packages/secretary/php-psr-6-cache-adapter) [![Total Downloads](https://poser.pugx.org/secretary/php-psr-6-cache-adapter/downloads)](https://packagist.org/packages/secretary/php-psr-6-cache-adapter) |
| [PSR-16 Cache Adapter][psr-16-cache-adapter] | Allows for caching secrets using a PSR-16 Cache Interface | [![Latest Stable Version](https://poser.pugx.org/secretary/php-psr-16-cache-adapter/version)](https://packagist.org/packages/secretary/php-psr-16-cache-adapter) [![Total Downloads](https://poser.pugx.org/secretary/php-psr-16-cache-adapter/downloads)](https://packagist.org/packages/secretary/php-psr-16-cache-adapter) |
| [Secretary Bundle][secretary-bundle] | Allows for integrating with the Symfony Framework | [![Latest Stable Version](https://poser.pugx.org/secretary/php-secretary-bundle/version)](https://packagist.org/packages/secretary/php-secretary-bundle) [![Total Downloads](https://poser.pugx.org/secretary/php-secretary-bundle/downloads)](https://packagist.org/packages/secretary/php-secretary-bundle) |

### Api Documentation

There's only one class you interface with in Secretary: [`Secretary\Manager`][Secretary\Manager::class]

<a name="constructor" />

##### Secretary\Manager->__construct(AdapterInterface $adapter)

Pass in your desired adapter.

```php
<?php
use Secretary\Manager;
use Secretary\Adapter\AWS\SecretsManager\AWSSecretsManagerAdapter;

$manager = new Manager(
    new AWSSecretsManagerAdapter([
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
use Secretary\Adapter\AWS\SecretsManager\AWSSecretsManagerAdapter;

use Secretary\Adapter\Cache\PSR6CacheAdapter;
use Cache\Adapter\Apc\ApcCachePool;

$manager = new Manager(
    new PSR6CacheAdapter(
        new AWSSecretsManagerAdapter([
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

<a name="getSecret" />

##### Secretary\Manager->getSecret(string $key, ?array $options): Secret

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

<a name="putSecret" />

##### Secretary\Manager->putSecret(string $key, string|array $value, ?array $options): void

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

<a name="deleteSecret" />

##### Secretary\Manager->deleteSecret(string $key, ?array $options): void

Deletes a secret from the storage engine using the given `$key`.

Again, some adapters allow passing in custom options to send along with the request.

```php
$manager->deleteSecret('database/redis');
```

##### Secretary\Manager->getAdapter(): AdapterInterface

Will return the adapter that was passed to this manager during construction.

[aws-secrets-manager-adapter]: https://github.com/secretary/php-aws-secrets-manager-adapter 
[hashicorp-vault-adapter]: https://github.com/secretary/php-hashicorp-vault-adapter 
[psr-6-cache-adapter]: https://github.com/secretary/php-psr-6-cache-adapter 
[psr-16-cache-adapter]: https://github.com/secretary/php-psr-16-cache-adapter 
[secretary-bundle]: https://github.com/secretary/php-secretary-bundle
[Secretary\Manager::class]: https://github.com/secretary/php/blob/master/src/Core/src/Manager.php