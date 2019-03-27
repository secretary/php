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

var_dump($manager->getSecrets(['path' => 'bot']));
