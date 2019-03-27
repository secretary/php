<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */


namespace Secretary\Adapter;

use Secretary\Exception\NotFoundException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AbstractPathAdapter
 *
 * @package Secretary\Adapter
 */
abstract class AbstractPathAdapter extends AbstractAdapter
{
    /**
     * @var string
     */
    protected $pathRegex = "/^(?!\/)[A-Za-z\/_-]+(?<!\/)$/";

    /**
     * @param string $key
     * @param array  $options
     *
     * @return Secret
     * @throws NotFoundException
     */
    public function getSecret(string $key, array $options): Secret
    {
        $result = $this->getSecrets($options);

        foreach ($result as $secret) {
            if ($secret->getKey() === $key) {
                return $secret;
            }
        }

        throw new NotFoundException($key, $options['path']);
    }

    public function configureSharedOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('path')
            ->setAllowedTypes('path', 'string')
            ->setAllowedValues(
                'path',
                function ($value) {
                    return preg_match($this->pathRegex, $value) === 1;
                }
            );
    }

    public function configurePutSecretsOptions(OptionsResolver $resolver): void
    {
        parent::configurePutSecretsOptions($resolver);

        $this->updateSecretsDefault($resolver);
    }

    public function configureDeleteSecretsOptions(OptionsResolver $resolver): void
    {
        parent::configurePutSecretsOptions($resolver);

        $this->updateSecretsDefault($resolver);
    }

    private function updateSecretsDefault(OptionsResolver $resolver): void
    {
        $resolver->remove('path');
        $resolver->setDefault(
            'secrets',
            function (OptionsResolver $options) {
                $options->setRequired('path')
                    ->setAllowedTypes('path', 'string')
                    ->setAllowedValues(
                        'path',
                        function ($value) {
                            return preg_match($this->pathRegex, $value) !== 1;
                        }
                    );
            }
        );
    }
}
