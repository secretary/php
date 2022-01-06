<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */


namespace Secretary\Adapter\Local\JSONFile;


use Secretary\Adapter\AbstractAdapter;
use Secretary\Exception\SecretNotFoundException;
use Secretary\Secret;

/**
 * Class LocalJSONFileAdapter
 *
 * @package Secretary\Adapter\Local\JSONFile
 */
class LocalJSONFileAdapter extends AbstractAdapter
{
    private string $secretsFile;

    private int $jsonOptions;

    /**
     * LocalJSONFileAdapter constructor.
     *
     * @throws \Exception
     */
    public function __construct(array $config)
    {
        if ($config === []) {
            throw new \Exception('Configuration is required.');
        }

        if (!isset($config['file'])) {
            throw new \Exception('`file` is a required config.');
        }
        if (!isset($config['jsonOptions'])) {
            $config['jsonOptions'] = JSON_PRETTY_PRINT;
        }

        $this->secretsFile = $config['file'];
        $this->jsonOptions = $config['jsonOptions'];
    }

    /**
     * @param Secret   $secret
     * @param Secret[] $secrets
     *
     * @return array
     */
    private static function updateValue(Secret $secret, array $secrets): array
    {
        $keys  = array_column($secrets, 'key');
        $index = array_search($secret->getKey(), $keys, true);
        if ($index === false || $index === null) {
            $secrets[] = $secret;
        } else {
            $secrets[$index]['value'] = $secret->getValue();
        }

        return $secrets;
    }

    /**
     * {@inheritdoc}
     * @throws SecretNotFoundException
     */
    public function getSecret(string $key, ?array $options = []): Secret
    {
        $secrets = $this->loadSecrets();
        $keys    = array_column($secrets, 'key');
        $index   = array_search($key, $keys, true);
        if ($index === false || $index === null) {
            throw new SecretNotFoundException($key);
        }

        return new Secret(
            $secrets[$index]['key'],
            $secrets[$index]['value'],
            $secrets[$index]['metadata'] ?? null
        );
    }

    /**
     * {@inheritdoc}
     */
    public function putSecret(Secret $secret, ?array $options = []): Secret
    {
        $secrets = LocalJSONFileAdapter::updateValue($secret, $this->loadSecrets());
        $this->saveSecrets($secrets);

        return $secret;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteSecretByKey(string $key, ?array $options = []): void
    {
        $secrets = $this->loadSecrets();
        $keys    = array_column($secrets, 'key');
        $index   = array_search($key, $keys, true);
        if ($index === false || $index === null) {
            throw new SecretNotFoundException($key);
        }

        array_splice($secrets, $index, 1);
        $this->saveSecrets($secrets);
    }

    /**
     * {@inheritdoc}
     * @throws SecretNotFoundException
     */
    public function deleteSecret(Secret $secret, ?array $options = []): void
    {
        $this->deleteSecretByKey($secret->getKey(), $options);
    }

    /**
     * @return list<Secret>
     *
     * @throws \Exception
     */
    private function loadSecrets(): array
    {
        return json_decode(file_get_contents($this->secretsFile), true, 512, JSON_THROW_ON_ERROR);
    }

    private function saveSecrets(array $secrets): void
    {
        file_put_contents($this->secretsFile, json_encode($secrets, $this->jsonOptions));
    }
}
