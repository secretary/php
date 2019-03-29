<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */

use Secretary\Adapter\AWS\SecretsManager\AWSSecretsManagerAdapter;
use Secretary\Adapter\Hashicorp\Vault\HashicorpVaultAdapter;

require_once __DIR__.'/vendor/autoload.php';

$manager = new \Secretary\Manager(
    new AWSSecretsManagerAdapter(
        [
            'region'  => 'us-east-1',
            'version' => '2017-10-17',
        ]
    )
);

var_dump(
    $manager->getSecret('databases/redis/main'),
    $manager->getSecret('test/foo'),
    $manager->getSecret('test/foo')['bar']
);

$manager = new \Secretary\Manager(new HashicorpVaultAdapter());
var_dump(
    $manager->getSecret('test/foo'),
    $manager->getSecret('test/foo')['bar'],
    $manager->getSecret('test/foo/foobar'),
    $manager->getSecret('test/foo/foobar')['baz']
);