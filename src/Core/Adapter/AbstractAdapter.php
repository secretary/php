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
    public function configureSharedOptions(OptionsResolver $resolver): void
    {
    }

    public function configureGetSecretOptions(OptionsResolver $resolver): void
    {
    }

    public function configurePutSecretOptions(OptionsResolver $resolver): void
    {
    }

    public function configureDeleteSecretOptions(OptionsResolver $resolver): void
    {
    }
}
