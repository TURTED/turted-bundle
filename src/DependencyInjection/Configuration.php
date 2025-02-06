<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 19.04.2016
 * Time: 11:14.
 */

namespace Turted\TurtedBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('turted');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
            ->scalarNode('url')->defaultValue('http://127.0.0.1:7117/push/')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('password')->defaultValue('turtedpasswd_to_change')->isRequired()->end()
            ->integerNode('timeout')->min(0)->defaultValue(5)->end()
            ->end();

        return $treeBuilder;
    }
}
