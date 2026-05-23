<?php

declare(strict_types=1);

namespace Jmf\RouteAccess;

use Override;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class JmfRouteAccessBundle extends AbstractBundle
{
    protected string $extensionAlias = 'jmf_route_access';

    #[Override]
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->import('../config/definition.php');
    }

    /**
     * @param array{policy: array<string, mixed>, twig_functions_prefix: string} $config
     */
    #[Override]
    public function loadExtension(
        array $config,
        ContainerConfigurator $configurator,
        ContainerBuilder $container,
    ): void {
        $configurator->import('../config/services.yaml');

        $container->setParameter('jmf_route_access.policy_config', $config['policy']);
        $container->setParameter('jmf_route_access.twig_functions_prefix', $config['twig_functions_prefix']);
    }
}
