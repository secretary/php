<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */


namespace Secretary\Adapter\Hashicorp\Vault;

use GuzzleHttp\Client as GuzzleClient;
use Secretary\Adapter\AbstractAdapter;
use Secretary\Adapter\Hashicorp\Vault\Client\Client;
use Secretary\Secret;

/**
 * Class HashicorpVaultAdapter
 *
 * @package Secretary\Adapter\Hashicorp\Vault
 */
class HashicorpVaultAdapter extends AbstractAdapter
{
    /**
     * @var Client
     */
    private $client;

    /**
     * HashicorpVaultAdapter constructor.
     *
     * @param array $config
     *
     * @throws \Exception
     */
    public function __construct(array $config = [])
    {
        if (!class_exists(GuzzleClient::class)) {
            throw new \Exception('guzzlehttp/guzzle is required to use the HashicorpVaultAdapter');
        }

        $this->client = new Client($config);
    }

    /**
     * {@inheritdoc}
     */
    public function getSecret(string $key, ?array $options = []): Secret
    {
        $response = $this->client->get('/v1/secret/'.$key);
        $json     = \GuzzleHttp\json_decode($response->getBody()->getContents(), true);

        return new Secret($key, $json['data']);
    }

    /**
     * {@inheritdoc}
     */
    public function putSecret(Secret $secret, ?array $options = []): Secret
    {
        if (!is_array($secret->getValue())) {
            throw new \Exception('Value for this adapter must be a key/value array');
        }

        $this->client->post('/v1/secret/'.$secret->getKey(), ['json' => $secret->getValue()]);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteSecret(Secret $secret, ?array $options = []): void
    {
        $this->deleteSecretByKey($secret->getKey(), $options);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteSecretByKey(string $key, ?array $options = []): void
    {
        $this->client->delete('/v1/secret/'.$key);
    }
}
