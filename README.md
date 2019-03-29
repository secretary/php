# Secretary - Secrets Manager for PHP

Secrets are an important aspect of most applications you can build. How you store them, and keep them "secret" is a challenge.
Luckily, there are tools you can use to keep them all safe. 

Secretary is a tool to integrate your PHP application with these tools.

Right now, Secretary supports:

* AWS Secrets Manager
* Hashicorp Vault

It also has support for Caching with any PSR6 or PSR16 compliant library.

### General Idea:

```php
use Secretary\Manager;
use Secretary\Adapter\AWS\SecretsManager\AWSSecretsManagerAdapter;
use Secretary\Adapter\Cache\PSR6CacheAdapter;
use Cache\Adapter\Apc\ApcCachePool;

$manager = new Manager(
    new PSR6CacheAdapter(
        new AWSSecretsManagerAdapter([
            'region'      => 'us-east-1',
            'credentials' => [
                'accessKeyId'     => 'asdjsdg;asdgfsadfk',
                'secretAccessKey' => 'adsgasdgasfgasdfsadgjasdfsljdf'
            ]
        ]),
        new ApcCachePool()
    )
);

$manager->putSecret('database/redis', ['dsn' => 'redis://localhost:6379', 'password' => 'my_super_strong_password']);
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

Can also use single-secret paths (Paths that are just one secret, instead of a map of secrets).

```php

$manager->putSecret('database/redis', 'postgres://localhost:5432');
$secret = $manager->getSecret('databases/postgres/dsn'); // If you chose to use single-secret paths
/*
Secret {
    "path" = "databases/postgres/",
    "value" = "postgres://localhost:5432"
}
*/
```

# Installation
It is up to the adapter to work out how these two input values are implemented. 