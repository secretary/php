# Secretary - Secrets Manager for PHP

### General Idea:

```php
use Secretary\Manager;
use Secretary\Adapter\AWS\SecretsManager\AWSSecretsManagerAdapter;
use Cache\Adapter\Apc\ApcCachePool;

$manager = new Manager(
    new AWSSecretsManagerAdapter([
        'region'      => 'us-east-1',
        'credentials' => [
            'accessKeyId'     => 'asdjsdg;asdgfsadfk',
            'secretAccessKey' => 'adsgasdgasfgasdfsadgjasdfsljdf'
        ]
    ]),
);

$redisHost = $manager->getSecret('dsn', ['path' => 'databases/redis']);
// redis://redis.localhost:6379
```