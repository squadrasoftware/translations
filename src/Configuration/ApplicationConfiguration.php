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
                            ->scalarNode('path')
                                ->isRequired()
                                ->validate()
                                    ->always(function ($value) {
                                        if (!is_dir($value)) {
                                            throw new \InvalidArgumentException(sprintf('Directory "%s" does not exist', $value));
                                        }

                                        return $value;
                                    })
                                ->end()
                            ->end()
                            ->scalarNode('format')->isRequired()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $builder;
    }
}