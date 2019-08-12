<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */


namespace Secretary\Adapter\Chain;


use Psr\Cache\CacheItemPoolInterface;
use Secretary\Adapter\AbstractAdapter;
use Secretary\Adapter\AdapterInterface;
use Secretary\Exception\SecretNotFoundException;
use Secretary\Helper\ArrayHelper;
use Secretary\Secret;

/**
 * Class ChainAdapter
 *
 * @package Secretary\Adapter\Chain
 */
final class ChainAdapter extends AbstractAdapter
{
    /**
     * @var AdapterInterface[]
     */
    private $adapters;

    /**
     * CacheAdapter constructor.
     *
     * @param AdapterInterface[] $adapters
     */
    public function __construct(array $adapters)
    {
        $this->adapters = $adapters;
    }

    /**
     * Note: $options is a 0-indexed array of options. Each index corresponds to the index of the adapters
     * {@inheritdoc}
     */
    public function getSecret(string $key, ?array $options = []): Secret
    {
        foreach ($this->adapters as $key => $adapter) {
            try {
                return $adapter->getSecret($key, $options[$key]);
            } catch (SecretNotFoundException $ignored) {
            }
        }

        throw new SecretNotFoundException($key);
    }

    /**
     * {@inheritdoc}
     */
    public function putSecret(Secret $secret, ?array $options = []): Secret
    {
        foreach ($this->adapters as $key => $adapter) {
            $adapter->putSecret($secret, $options[$key]);
        }

        return $secret;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteSecret(Secret $secret, ?array $options = []): void
    {
        foreach ($this->adapters as $key => $adapter) {
            $adapter->deleteSecret($secret, $options[$key]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteSecretByKey(string $key, ?array $options = []): void
    {
        $success = false;
        foreach ($this->adapters as $key => $adapter) {
            try {
                $adapter->deleteSecret($adapter->getSecret($key), $options[$key]);
                $success = true;
            } catch (SecretNotFoundException $ignored) {
            }
        }

        if (!$success) {
            throw new SecretNotFoundException($key);
        }
    }
}
