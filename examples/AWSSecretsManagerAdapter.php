<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */

use Secretary\Adapter\AWS\SecretsManager\AWSSecretsManagerAdapter;

require_once __DIR__.'/../vendor/autoload.php';

$manager = new \Secretary\Manager(
    new AWSSecretsManagerAdapter(
        [
            'region'  => 'us-east-1',
            'version' => '2017-10-17',
        ]
    )
);

$manager->putSecret('foo', 'bar');
$manager->putSecret('baz', ['foo' => 'foobar']);

var_dump(
    $manager->getSecret('foo'),
    $manager->getSecret('baz'),
    $manager->getSecret('baz')['foo']
);

$manager->deleteSecret('foo', ['ForceDeleteWithoutRecovery' => true]);
$manager->deleteSecret('baz', ['ForceDeleteWithoutRecovery' => true]);