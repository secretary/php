# Secretary Bundle - Secrets Manager for Symfony

## This Bundle Experimental!

Secrets are an important aspect of most applications you can build. How you store them, and keep them "secret" is a challenge.
Luckily, there are tools you can use to keep them all safe. 

Secretary is a tool to integrate your PHP application with these tools.

You can find more information about the underlying library over at [the main docs](https://github.com/secretary/php).

### Installation

```bash
$ composer require secretary/symfony
```

### Configuration

```yaml
# config/packages/secretary.yaml

services:
    Symfony\Component\Cache\Adapter\ApcuAdapter:
        arguments: ['secrets', 300000]

secretary:
    adapters:
        json:
            adapter: Secretary\Adapter\Local\JSONFile\LocalJSONFileAdapter
            config:
                file:  '%kernel.root_dir%/config/secrets.json'
        aws:
            adapter: Secretary\Adapter\AWS\SecretsManager\AWSSecretsManagerAdapter
            config:
                region:  'us-east-1'
                version: 'latest'
                credentials:
                    key: "%env(API_AWS_ACCESS_KEY_ID)%"
                    secret: "%env(API_AWS_SECRET_ACCESS_KEY)%"
        default: # chain adapter
            adapter: Secretary\Adapter\Chain\ChainAdapter
            config:
                - @secretary.adapter.json
                - @secretary.adapter.aws
            cache:
                enabled:    true
                type:       psr6
                service_id: cache.secrets
```