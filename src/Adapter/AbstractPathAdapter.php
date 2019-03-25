<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */


namespace Secretary\Adapter;

use Secretary\Configuration\Adapter\AbstractAdapterConfiguration;
use Secretary\Configuration\Adapter\AbstractOptionsConfiguration;
use Secretary\Configuration\Adapter\Path\GetSecretOptionsConfiguration;
use Secretary\Configuration\Adapter\Path\GetSecretsOptionsConfiguration;
use Secretary\Configuration\Adapter\Path\PutSecretOptionsConfiguration;
use Secretary\Configuration\Adapter\Path\PutSecretsOptionsConfiguration;
use Secretary\Exception\NotFoundException;

abstract class AbstractPathAdapter extends AbstractAdapter
{
    public $pathRegex = "/^(?!\/)[A-Za-z\/_-]+(?<!\/)$/";

    protected function getConfiguration(): AbstractAdapterConfiguration
    {
        // TODO: Implement getConfiguration() method.
    }

    public function doGetSecret(array $options): SecretInterface
    {
        /** @var SecretInterface[] $result */
        $result = $this->memoize(
            json_encode($options),
            function () use ($options) {
                return $this->getSecrets($options);
            }
        );

        foreach ($result as $secret) {
            if ($secret->getKey()  === $options['key']) {
                return $secret;
            }
        }

        throw new NotFoundException($options['key'], $options['path']);
    }

    protected function getGetSecretsConfiguration(): AbstractOptionsConfiguration
    {
        return new GetSecretOptionsConfiguration($this->pathRegex);
    }

    protected function getGetSecretConfiguration(): AbstractOptionsConfiguration
    {
        return new GetSecretsOptionsConfiguration($this->pathRegex);
    }

    protected function getPutSecretConfiguration(): AbstractOptionsConfiguration
    {
        return new PutSecretOptionsConfiguration($this->pathRegex);
    }

    protected function getPutSecretsConfiguration(): AbstractOptionsConfiguration
    {
        return new PutSecretsOptionsConfiguration($this->pathRegex);
    }
}
