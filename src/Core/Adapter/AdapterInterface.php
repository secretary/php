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
     * @param string $key
     * @param array  $options
     *
     * @return Secret
     * @throws SecretNotFoundException
     */
    public function getSecret(string $key, ?array $options = []): Secret;

    /**
     * Add \ Update a secret by a key.
     *
     * @param Secret $secret
     * @param array  $options
     *
     * @return Secret
     */
    public function putSecret(Secret $secret, ?array $options = []): Secret;

    /**
     * Delete a secret by a key.
     *
     *
     * @param Secret $secret
     * @param array  $options
     *
     * @return void
     */
    public function deleteSecret(Secret $secret, ?array $options = []): void;

    /**
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    public function configureSharedOptions(OptionsResolver $resolver): void;

    /**
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    public function configureGetSecretOptions(OptionsResolver $resolver): void;

    /**
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    public function configurePutSecretOptions(OptionsResolver $resolver): void;

    /**
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    public function configureDeleteSecretOptions(OptionsResolver $resolver): void;
}
