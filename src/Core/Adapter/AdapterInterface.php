<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */

namespace Secretary\Adapter;

use Secretary\Exception\SecretNotFoundException;
use Secretary\Secret;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Interface AdapterInterface
 *
 * @package Secretary\Adapter
 */
interface AdapterInterface
{
    /**
     * Get a secret by a key.
     *
     * @throws SecretNotFoundException
     */
    public function getSecret(string $key, ?array $options = []): Secret;

    /**
     * Add \ Update a secret by a key.
     */
    public function putSecret(Secret $secret, ?array $options = []): Secret;

    /**
     * Delete a secret by a key.
     */
    public function deleteSecret(Secret $secret, ?array $options = []): void;

    public function configureSharedOptions(OptionsResolver $resolver): void;

    public function configureGetSecretOptions(OptionsResolver $resolver): void;

    public function configurePutSecretOptions(OptionsResolver $resolver): void;

    public function configureDeleteSecretOptions(OptionsResolver $resolver): void;
}
