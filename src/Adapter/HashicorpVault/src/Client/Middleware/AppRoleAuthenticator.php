<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */


namespace Secretary\Adapter\Hashicorp\Vault\Client\Middleware;


use function GuzzleHttp\choose_handler;
use function GuzzleHttp\json_decode;
use GuzzleHttp\Client;
use function GuzzleHttp\Psr7\modify_request;
use Psr\Http\Message\RequestInterface;

/**
 * Class AppRoleAuthenticator
 *
 * @package Secretary\Adapter\Hashicorp\Vault\Client\Middleware
 */
class AppRoleAuthenticator
{
    const authRoute = 'v1/auth/approle/login';

    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $roleId;

    /**
     * @var string
     */
    private $secretId;

    /**
     * @var
     */
    private $token;

    /**
     * @var
     */
    private $tokenExpiration;

    /**
     * AppRoleAuthenticator constructor.
     *
     * @param Client $client
     * @param string $roleId
     * @param string $secretId
     */
    public function __construct(Client $client, string $roleId, string $secretId)
    {
        $this->client   = $client;
        $this->roleId   = $roleId;
        $this->secretId = $secretId;
    }

    /**
     * @param callable $handler
     *
     * @return callable
     */
    public function __invoke(callable $handler): callable
    {
        return function (RequestInterface $request) use ($handler) {
            if (empty($this->token) || time() > $this->tokenExpiration) {
                $this->authenticate();
            }

            return modify_request($request, ['set_headers' => ['X-Vault-Token' => $this->token]]);
        };
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function authenticate()
    {
        $response = $this->client->request(
            'POST',
            static::authRoute,
            [
                'json'    => ['role_id' => $this->roleId, 'secret-id' => $this->secretId],
                'handler' => choose_handler(),
            ]
        );

        $response              = json_decode($response->getBody()->getContents(), true);
        $this->token           = $response['auth']['client_token'];
        $this->tokenExpiration = time() + $response['auth']['lease_duration'];
    }
}