<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return function(ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->private()
    ;

    $services->load('App\\', '../src/*')
        ->exclude([
            '../src/{tests}',
        ]);

    $services->load('App\\Booking\\UI\\Controller\\', '../src/Booking/UI/Controller/')->public();

};