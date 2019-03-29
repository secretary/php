<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */

use Secretary\Adapter\Hashicorp\Vault\HashicorpVaultAdapter;

require_once __DIR__.'/vendor/autoload.php';

$manager = new \Secretary\Manager(new HashicorpVaultAdapter());

try {
    $manager->putSecret('foo', 'bar');
} catch (\Exception $e) {
    // Throws an exception because hashicopr vault requires key/value secrets
}
$manager->putSecret('baz', ['foo' => 'foobar']);

var_dump(
    $manager->getSecret('baz'),
    $manager->getSecret('baz')['foo']
);

$manager->deleteSecret('baz');