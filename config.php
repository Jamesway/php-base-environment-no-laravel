<?php

use Interop\Container\ContainerInterface;

return [
    /* this container */
    'Interop\Container\ContainerInterface' => function (Interop\Container\ContainerInterface $container) {
        return $container;
    },

    /*'app.ns' => 'RDCloud\GmailWatch\App',
    'infrastructure.ns' => 'RDCloud\Infrastructure',*/

    /* controllers namespace */
    'cli.ns' => 'RDCloud\UI\Cli',
    'web.ns' => 'RDCloud\UI\Web',


    /* Router */
    'FastRoute\RouteParser' => DI\object('FastRoute\RouteParser\Std'),
    'FastRoute\DataGenerator' => DI\object('FastRoute\DataGenerator\GroupCountBased'),
    'FastRoute\RouteCollector' => DI\object('FastRoute\RouteCollector')
        ->method('addRoute', 'GET', '/', 'DefaultController')
        ->method('addRoute', 'GET', '/gae_test', 'GaeTestController')
        ->method('addRoute', 'GET', '/doi/{doi_prefix:10\.[^\s]+}/{doi_suffix:.+}', 'ViewArticleMetaController')
        ->method('addRoute', 'GET', '/setkv/{timestamp:\d+}/{enc_secret:[A-Za-z0-9_\-]+}/{token:[A-Za-z0-9_\-]+}', 'SetSecretController')
        ->method('addRoute', 'GET', '/404', 'NotFoundController'),
    'FastRoute\Dispatcher' => function (FastRoute\RouteCollector $collector) {
        return new FastRoute\Dispatcher\GroupCountBased($collector->getData());
    }
];

?>
