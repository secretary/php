<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */


require_once __DIR__.'/vendor/autoload.php';
use Secretary\Adapter\Hashicorp\Vault\HashicorpVaultAdapter;

$fooSecret = new \Secretary\Secret('foo', 'bar');
$bazSecret = new \Secretary\Secret('baz', ['foo' => 'foobar']);

$manager = new \Secretary\Manager(new HashicorpVaultAdapter());

try {
    $manager->putSecret($fooSecret);
} catch (\Exception $e) {
    // Throws an exception because hashicopr vault requires key/value secrets
}
$manager->putSecret($bazSecret);

var_dump(
    $manager->getSecret('baz'),
    $manager->getSecret('baz')['foo']
);

$manager->deleteSecret($bazSecret);