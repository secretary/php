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
# config/packages/secretary.yamlg
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

### Resolving secrets in env vars

```yaml
parameters:
    db_pass: '%env(secretary:default:DB_PASS)%'          # scalar secret
    db_conf: '%env(secretaryArray:default:DB_CONFIG)%'   # array secret
```

### Missing secrets

By default, a missing secret throws Symfony's `EnvNotFoundException` (with the manager name and
secret key in the message, and the original `SecretNotFoundException` as `previous`).

To resolve `null` instead — with a warning logged through the default logger — you can either
opt in per reference with the `secretaryOptional:` / `secretaryArrayOptional:` prefixes:

```yaml
parameters:
    feature_key: '%env(secretaryOptional:default:FEATURE_KEY)%'
```

or globally via the bundle config:

```yaml
secretary:
    allow_missing_secrets: true # defaults to false
```

Symfony's built-in `default:` processor also composes, if you want a fallback parameter instead:

```yaml
parameters:
    fallback: 'some-default'
    db_pass:  '%env(default:fallback:secretary:default:DB_PASS)%'
```

Only missing secrets are handled this way — network, auth, and other adapter errors always bubble up.