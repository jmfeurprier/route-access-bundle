<?php

declare(strict_types=1);

use Jmf\RouteAccess\Twig\RouteAccessExtension;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;

return static function (DefinitionConfigurator $definition): void {
    $definition->rootNode()
        ->children()
            ->arrayNode('policy')
                ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('public_routes')
                        ->defaultValue(['*'])
                        ->scalarPrototype()->end()
                    ->end()
                    ->arrayNode('roles')
                        ->defaultValue([])
                        ->useAttributeAsKey('name')
                        ->arrayPrototype()
                            ->scalarPrototype()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->scalarNode('twig_functions_prefix')
                ->info('Twig functions prefix.')
                ->defaultValue(RouteAccessExtension::PREFIX_DEFAULT)
            ->end()
        ->end()
    ;
};
