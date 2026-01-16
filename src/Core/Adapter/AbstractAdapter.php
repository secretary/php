<?php

declare(strict_types=1);

/*
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   https://opensource.org/licenses/MIT
 */

namespace Secretary\Adapter;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AbstractAdapter.
 *
 * @package Secretary\Adapter
 */
abstract class AbstractAdapter implements AdapterInterface
{
    #[\Override]
    public function configureSharedOptions(OptionsResolver $resolver): void
    {
    }

    #[\Override]
    public function configureGetSecretOptions(OptionsResolver $resolver): void
    {
    }

    #[\Override]
    public function configurePutSecretOptions(OptionsResolver $resolver): void
    {
    }

    #[\Override]
    public function configureDeleteSecretOptions(OptionsResolver $resolver): void
    {
    }
}
