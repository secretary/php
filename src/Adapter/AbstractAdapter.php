<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */


namespace Secretary\Adapter;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AbstractAdapter
 *
 * @package Secretary\Adapter
 */
abstract class AbstractAdapter implements AdapterInterface
{
    /**
     * {@inheritdoc}
     */
    public function configureSharedOptions(OptionsResolver $resolver): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function configureGetSecretOptions(OptionsResolver $resolver): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function configurePutSecretOptions(OptionsResolver $resolver): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function configureDeleteSecretOptions(OptionsResolver $resolver): void
    {
    }
}
