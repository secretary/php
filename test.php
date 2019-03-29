<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */

require_once __DIR__.'/vendor/autoload.php';


$manager = new \Secretary\Manager(
    new \Secretary\Adapter\AWS\SecretsManager\AWSSecretsManagerAdapter(
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

/*
object(Secretary\Adapter\Secret)#125 (2) {
  ["key":"Secretary\Adapter\Secret":private]=>
  string(20) "databases/redis/main"
  ["value":"Secretary\Adapter\Secret":private]=>
  array(3) {
    ["dsn"]=>
    string(37) "redis://localhost:6379"
    ["auth"]=>
    string(26) "asdasdasdasd"
    ["port"]=>
    string(4) "6379"
  }
}
object(Secretary\Adapter\Secret)#128 (2) {
  ["key":"Secretary\Adapter\Secret":private]=>
  string(8) "test/foo"
  ["value":"Secretary\Adapter\Secret":private]=>
  array(1) {
    ["bar"]=>
    string(3) "baz"
  }
}
string(3) "baz"
*/