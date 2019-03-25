<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */


namespace Secretary\Configuration\Adapter\Path;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

abstract class AbstractOptionsConfiguration extends \Secretary\Configuration\Adapter\AbstractOptionsConfiguration
{
    /**
     * @var string
     */
    private $pathRegex;

    /**
     * AbstractOptionsConfiguration constructor.
     *
     * @param string $pathRegex
     */
    public function __construct(string $pathRegex)
    {
        $this->pathRegex = $pathRegex;
    }

    protected function validatePath(NodeDefinition $node): self
    {
        $regex = $this->pathRegex;

        $node->validate()
            ->ifTrue(
                function ($v) use ($regex) {
                    return preg_match($regex, $v['path']) === 1;
                }
            )
            ->thenInvalid('Path must match the pattern: ' . $regex)
            ->end();

        return $this;
    }
}
