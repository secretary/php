# Secretary - JSON File Adapter

JSON File Adapter for [Secretary](https://github.com/secretary/php)

## Table of Contents

1. [Installation](#installation)
2. [Options](#options)

### Installation

```bash
$ composer require secretary/core secretary/local-json-file-adapter
```

### Secrets File Structure

```json
[
    {
        "key": "my-secret-key",
        "value": "some secret"    
    },
    {
        "key": "some-other-secret",
        "value": {
            "a": "b"
        },
        "metadata": {"foo":  "bar"}
    }
]
```
