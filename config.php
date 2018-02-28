<?php


return [

    'Psr\Container\ContainerInterface' => function (Psr\Container\ContainerInterface $container) {
        return $container;
    },

    /* Route to Controllers */
    'FastRoute\RouteParser' => DI\autowire('FastRoute\RouteParser\Std'),
    'FastRoute\DataGenerator' => DI\autowire('FastRoute\DataGenerator\GroupCountBased'),
    'FastRoute\RouteCollector' => DI\autowire('FastRoute\RouteCollector')
        /* add more routes - no comma */
        /* ->method('addRoute', 'POST', '/sample/route', 'NameSpaced\Controller')*/
        ->method('addRoute', 'GET', '/', 'MyProject\Controller\DefaultController'),
    'FastRoute\Dispatcher' => function (FastRoute\RouteCollector $collector) {
        return new FastRoute\Dispatcher\GroupCountBased($collector->getData());
    },


    /* Secrets */
    'mail.key' => DI\env('MAIL_API_KEY'),

    'db.conn' => [
        "connection" => DI\env('DB_CONNECTION'),
        "host" => DI\env('DB_HOST'),
        "port" => DI\env('DB_PORT'),
        "database" => DI\env('DB_DATABASE'),
        "username" => DI\env('DB_USERNAME'),
        "password" => DI\env('DB_PASSWORD')
    ],

    'redis.conn' => [
        "scheme" => DI\env('REDIS_SCHEME'),
        "host" => DI\env('REDIS_HOST'),
        "port" => DI\env('REDIS_PORT'),
        "password" => DI\env('REDIS_PASSWORD')
    ],


    /* Defined Application Interfaces */

    /* Mailer */
    'IMailer' => DI\create('Infrastructure\ElasticEmail')->constructor(DI\get('mail.key')),

    /* Redis */
    'Predis\Client' => DI\create()->constructor(DI\get('redis.conn')),
    'Redis' => DI\create('Infrastructure\RepoRedis'),

    /* Mysql/MariaDB */
    'Mysql' => DI\create('Infrastructure\RepoMariaDB')->constructor(DI\get('db.conn')),


    /* Examples
    'InterfaceX' => DI\create('Concrete\Class\That\Satisfies\InterfaceX'),
    'InterfaceY' => DI\create('Concrete\Class\That\Satisfies\InterfaceY')->constructor('constructor_param'),
    'InterfaceX' => DI\factory(

        function(Object\We\Are\Making $object, $param1, $param2) {

            $object->set1($param1);
            $object->set2($param2);

            return $object;
        })
        ->parameter('param1', 'value for param1')
        ->parameter('param2', DI\get('defined value')),

    'App\IEventBus' => DI\create('Infrastructure\EventBusWithContainer')
        ->method('addListener', 'NewUserWasCreated'), 'SendWelcomeEmailListener'))
        ->method('addListener', 'NameWasChanged'), 'TestEventListener'))
        ->method('addListener', 'EmailWasChanged'), 'SendChangedEmailListener'))
        ->method('addListener', 'PasswordWasChanged'), 'SendPasswordChangedEmailListener'))
        ->method('addListener', 'ChangedGuestUserToRegisteredUser'), 'TestEventListener'))
    */

];