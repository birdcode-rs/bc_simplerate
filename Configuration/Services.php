<?php

declare(strict_types=1);

use BirdCode\BcSimplerate\Hooks\ItemsProcFunc;
use BirdCode\BcSimplerate\Hooks\PluginPreviewRenderer;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use TYPO3\CMS\Core\DependencyInjection\SingletonPass;

return function (ContainerConfigurator $container, ContainerBuilder $containerBuilder): void {
    $containerBuilder->registerForAutoconfiguration(ItemsProcFunc::class)->addTag('bcsimplerate.ItemsProcFunc');
    $containerBuilder->registerForAutoconfiguration(PluginPreviewRenderer::class)->addTag('bcsimplerate.PageLayoutView');

    $containerBuilder->addCompilerPass(new SingletonPass('bcsimplerate.ItemsProcFunc'));
    $containerBuilder->addCompilerPass(new SingletonPass('bcsimplerate.PageLayoutView')); 
};
