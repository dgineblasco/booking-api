<?php
namespace App;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

final readonly class Bootstrap
{
    public static function createContainer(): ContainerBuilder
    {
        $containerBuilder = new ContainerBuilder();
        $loader = new PhpFileLoader(
            $containerBuilder,
            new FileLocator(__DIR__ . '/../config')
        );

        $loader->load('services.php');
        $containerBuilder->compile();

        return $containerBuilder;
    }
}
