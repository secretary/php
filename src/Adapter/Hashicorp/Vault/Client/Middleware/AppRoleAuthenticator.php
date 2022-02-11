<?php

declare(strict_types=1);

/*
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   https://opensource.org/licenses/MIT
 */

namespace Secretary\Adapter\Hashicorp\Vault\Client\Middleware;

use GuzzleHttp\Client;
use GuzzleHttp\Utils;
use Psr\Http\Message\RequestInterface;

/**
 * Class AppRoleAuthenticator.
 *
 * @package Secretary\Adapter\Hashicorp\Vault\Client\Middleware
 */
class AppRoleAuthenticator
{
    public const authRoute = 'v1/auth/approle/login';

    private Client $client;

    private string $roleId;

    private string $secretId;

    private ?string $token = null;

    private ?int $tokenExpiration = null;

    /**
     * AppRoleAuthenticator constructor.
     */
    public function __construct(Client $client, string $roleId, string $secretId)
    {
        $this->client   = $client;
        $this->roleId   = $roleId;
        $this->secretId = $secretId;
    }

    public function __invoke(callable $handler): callable
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            if (empty($this->token) || time() > $this->tokenExpiration) {
                $this->authenticate();
            }

            $request = $request->withHeader('X-Vault-Token', $this->token);

            return $handler($request, $options);
        };
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function authenticate(): void
    {
        $response = $this->client->post(
            static::authRoute,
            [
                'json' => [
                    'role_id'   => $this->roleId,
                    'secret-id' => $this->secretId,
                ],
                'handler' => Utils::chooseHandler(),
            ]
        );

        $response              = json_decode($response->getBody()->getContents(), true);
        $this->token           = (string) $response['auth']['client_token'];
        $this->tokenExpiration = time() + (int) $response['auth']['lease_duration'];
    }
}
