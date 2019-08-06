<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */


require_once __DIR__.'/../vendor/autoload.php';

use Secretary\Adapter\AWS\SecretsManager\LocalJSONFileAdapter;

$manager = new \Secretary\Manager(
    new LocalJSONFileAdapter(
        [
            'region'  => 'us-east-1',
            'version' => '2017-10-17',
        ]
    )
);

$fooSecret = new \Secretary\Secret('foo', 'bar');
$bazSecret = new \Secretary\Secret('baz', ['foo' => 'foobar']);

$manager->putSecret($fooSecret);
$manager->putSecret($bazSecret);

var_dump(
    $manager->getSecret('foo'),
    $manager->getSecret('baz'),
    $manager->getSecret('baz')['foo']
);

$manager->deleteSecret($fooSecret, ['ForceDeleteWithoutRecovery' => true]);
$manager->deleteSecret($bazSecret, ['ForceDeleteWithoutRecovery' => true]);
