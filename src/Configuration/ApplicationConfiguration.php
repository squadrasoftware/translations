<?php

namespace App\Configuration;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ApplicationConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $builder = new TreeBuilder('translations');
        $root = $builder->getRootNode();

        $root
            ->children()
                ->scalarNode('api_token')
                    ->isRequired()
                ->end()
            ->end()
            ->children()
                ->arrayNode('projects')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('project_id')->isRequired()->end()
                            ->scalarNode('source')->isRequired()->end()
                            ->scalarNode('translations')->isRequired()->end()
                            ->scalarNode('type')->isRequired()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $builder;
    }
}