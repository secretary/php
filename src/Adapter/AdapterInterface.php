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
 * Interface AdapterInterface
 *
 * @package Secretary\Adapter
 */
interface AdapterInterface
{
	/**
	 * Get a secret. Different adapters will handle this differently.
	 *
	 * For example:
	 *     Credstash can get a secret by context
	 *     AWS Secrets Manager must get a secrets by path
	 *
	 * @param string $key
	 * @param array  $options
	 *
	 * @return Secret
	 */
	public function getSecret(string $key, array $options): Secret;

	/**
	 * Get a group of secrets. Different adapters will handle this differently.
	 *
	 * For example:
	 *     Credstash can get secrets by context
	 *     AWS Secrets Manager must get secrets by path
	 *
	 * @param array $options
	 *
	 * @return Secret[]
	 */
	public function getSecrets(array $options): array;

	/**
	 * Add \ Update a secret. Different adapters will handle this differently.
	 *
	 * For example:
	 *     Credstash can add a secret by context
	 *     AWS Secrets Manager must add a secret by path
	 *
	 * @param string $key
	 * @param string $value
	 * @param array  $options
	 *
	 * @return void
	 */
	public function putSecret(string $key, string $value, array $options): void;

	/**
	 * Add \ Update a group of secrets. Different adapters will handle this differently.
	 *
	 * For example:
	 *     Credstash can add secrets by context
	 *     AWS Secrets Manager will add secrets by path
	 *
	 * @param array $options
	 *
	 * @return void
	 */
	public function putSecrets(array $options): void;

	/**
	 * Delete a secrets. Different adapters will handle this differently.
	 *
	 * For example:
	 *     Credstash can delete secrets by context
	 *     AWS Secrets Manager must delete secrets by path
	 *
	 * @param string $key
	 * @param array  $options
	 *
	 * @return void
	 */
	public function deleteSecret(string $key, array $options): void;

	/**
	 * Delete a group of secrets. Different adapters will handle this differently.
	 *
	 * For example:
	 *     Credstash will delete secrets by context
	 *     AWS Secrets Manager will delete secrets by path
	 *
	 * @param array $options
	 *
	 * @return void
	 */
	public function deleteSecrets(array $options): void;

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
    public function configureGetSecretsOptions(OptionsResolver $resolver): void;

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
    public function configurePutSecretsOptions(OptionsResolver $resolver): void;

    /**
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    public function configureDeleteSecretOptions(OptionsResolver $resolver): void;

    /**
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    public function configureDeleteSecretsOptions(OptionsResolver $resolver): void;
}
