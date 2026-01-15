# Secretary - GCP Secrets Manager Adapter

GCP Secrets Manager Adapter for [Secretary](https://github.com/secretary/php)

## Table of Contents

1. [Installation](#installation)
2. [Configuration](#configuration)
3. [Options](#options)

### Installation

```bash
$ composer require secretary/core secretary/gcp-secret-manager-adapter
```

### Configuration

```php
use Secretary\Adapter\GCP\SecretsManager\GCPSecretsManagerAdapter;

$adapter = new GCPSecretsManagerAdapter([
    'project_id' => 'your-gcp-project-id',
    // Optional: path to credentials file or credentials array
    // If not provided, uses Application Default Credentials (ADC)
    'credentials' => '/path/to/service-account.json',
]);
```

### Options

#### Constructor Options

| Option | Type | Required | Description |
|--------|------|----------|-------------|
| `project_id` | string | Yes | Your GCP project ID |
| `credentials` | string\|array | No | Path to service account JSON file or credentials array. Uses ADC if not provided. |

#### getSecret Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `version` | string | `"latest"` | Secret version to retrieve |

