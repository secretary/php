# Secretary - AWS SSM Parameter Store Adapter

AWS SSM Parameter Store Adapter for [Secretary](https://github.com/secretary/php)

## Table of Contents

1. [Installation](#installation)
2. [Options](#options)

### Installation

```bash
$ composer require secretary/core secretary/aws-ssm-parameter-store-adapter
```

### Options

Client options (the `config` passed to the constructor) match the
[AWS PHP SDK client options](https://docs.aws.amazon.com/aws-sdk-php/v3/api/class-Aws.AwsClient.html#___construct).

Secrets are written as `SecureString` parameters by default. Pass `Type => 'String'` (or a
custom `KeyId`) through the put options to change that.
