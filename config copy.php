<?php

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
    },

    /* Repo */
    /* TODO move this to Docker ENV */
    /*'redis.conn' => [
        "scheme" => "tcp",
        "host" => "redis",
        "port" => "6379",
        "password" => "redis"
    ],*/

    /* Secrets */
    /*'RDCloud\App\ISecretRepo' => DI\object('RDCloud\Infrastructure\RepoGoogleDataStore');*/

    /* DEV App Key */
    /***********************************************************************/
    /* dev app key repo */
    'RDCloud\App\ISecretRepo' => DI\object('RDCloud\Infrastructure\RepoMemory')->constructor(
        [
            'AppKey' => 'FR7PsC4nMFftehPvvEaM/R8yVUXC1cPesOw6f/YJ3d4='
        ]
    ),
    /***********************************************************************/
    /*'RDCloud\App\IAppKeyStore' => DI\object('RDCloud\App\AppKeyStore'),*/
    'app.key' => function(RDCloud\App\AppKeyStore $key_store) {

        return $key_store->getAppKey();
    },

    /* Secret Store */
    /*'RDCloud\App\ISecretStore' => DI\object('RDCloud\App\SecretStore'),*/
    /***********************************************************************/
    /* dev secret store */
    /*'RDCloud\App\ISecretRepo' => DI\object('RDCloud\Infrastructure\RepoMemory')
        ->constructor([]),*/


    /*'RDCloud\App\ISecretStore' => DI\object('RDCloud\App\SecretStore')
        ->method('set', 'ElasticEmail', 'elasticemail-testkey', DI\get('app.key'))
        ->method('set', 'MariaDB',
            json_encode(
                [
                    'user' => 'rdc',
                    'pass' => 'rdctest',
                    'host' => 'mariadb',
                    'db' => 'rdcloud'
                ]
            ),
            DI\get('app.key')
        )
        ->method('set', 'Redis',
            json_encode(
                [
                    "scheme" => "tcp",
                    "host" => "redis",
                    "port" => "6379",
                    "password" => "redis"
                ]

            ),
            DI\get('app.key')),
    */
    'RDCloud\App\ISecretStore' => DI\factory(function(RDCloud\App\SecretStore $secrets, $app_key) {

        $secrets->set('ElasticEmail', 'elasticemail-testkey', $app_key);

        $secrets->set('MariaDB',
            json_encode(
                [
                    'user' => 'rdc',
                    'pass' => 'rdctest',
                    'host' => 'mariadb',
                    'db' => 'rdcloud'
                ]
            ),
            $app_key
        );

        $secrets->set('Redis',
            json_encode(
                [
                    "scheme" => "tcp",
                    "host" => "redis",
                    "port" => "6379",
                    "password" => "redis"
                ]

            ),
            $app_key
        );

        return $secrets;

    })->parameter('app_key', DI\get('app.key')),

    /*'RDCloud\App\ISecretStore' => DI\factory(function(RDCloud\App\ISecretRepo $repo, $app_key) {


        $secret_store = new \RDCloud\App\SecretStore($repo);

        //MariaDB
        $secret_store->set('MariaDB', json_encode(
            [
                'user' => 'rdc',
                'pass' => 'rdctest',
                'host' => 'mariadb',
                'db' => 'rdcloud'
            ]
        ), $app_key);

        //ElasticEmail
        $secret_store->set('ElasticEmail', 'elasticemail-testkey', $app_key);

        //Redis
        $secret_store->set('Redis', json_encode(
            [
                "scheme" => "tcp",
                "host" => "redis",
                "port" => "6379",
                "password" => "redis"
            ]
        ), $app_key);

        return $secret_store;

    })->parameter('app_key', DI\get('app.key')),*/
    /***********************************************************************/



    'RDCloud\App\IHasher' => DI\object('RDCloud\Infrastructure\HasherSha256'),
    'RDCloud\App\IEncoder' => DI\object('RDCloud\Infrastructure\EncoderBase64'),
    'RDCloud\App\IInitializationVector' => DI\object('RDCloud\Infrastructure\URandomIV'),
    'RDCloud\App\ICipher' => DI\object('RDCloud\Infrastructure\CipherRijndael256'),



    'RDCloud\App\IMailer' => DI\factory(function(RDCloud\App\ISecretStore $secret_store, $app_key) {

        return new \RDCloud\Infrastructure\ElasticEmail($secret_store->get('ElasticEmail', $app_key));

    })->parameter('app_key', DI\get('app.key')),


    'Predis\Client' => DI\object()->constructor(DI\get('redis.conn')),
    'Redis' => DI\object('RDCloud\Infrastructure\RepoRedis'),
    /*'RDCloud\Infrastructure\IKVRepo' => DI\get('Redis'),*/

    'MariaDB' => DI\factory(function(\RDCloud\App\ISecretStore $secret_store, $app_key) {

        return new \RDCloud\Infrastructure\RepoMariaDB($secret_store->get('MariaDB', $app_key));

    })->parameter('app_key', DI\get('app.key')),


    /*'mail.conn' => function(\RDCloud\App\ISecretStore $secret_store, \RDCloud\App\IAppKey $app_key) {

        return $secret_store->get('ElasticEmail', $app_key);
    },*/
    /*'redis.conn' => function(\RDCloud\App\SecretStore $secret_store, \RDCloud\App\IAppKey $app_key) {

        return $secret_store->get('Redis', $app_key);
    },*/

    /*'RDCloud\App\IMailer' => DI\object('RDCloud\Infrastructure\ElasticEmail')->constructor(DI\get('mail.conn')),
*/



/*t('RDCloud\Infrastructure\ElasticEmail')->constructor(DI\get('mail.conn')),
*/

    /* Mailer */
    /*'mail.key' => function (RDCloud\App\ProtectedRepo $repo) {
        return $repo->get("ElasticEmail");
    },*/



    /*'Serializer' => function() {
      return JMS\Serializer\SerializerBuilder::create()->build();
    },*/

    /* Id Generator */
    /*'RDCloud\GmailWatch\App\IUniqueIdGenerator' => DI\Object('RDCloud\Infrastructure\UUID4Generator'),*/

    /* Cipher Stuff */

    /*'RDCloud\GmailWatch\App\IUniqueIdGenerator' => DI\object('RDCloud\Infrastructure\UUID4Generator'),*/
    'Base64Encoder' => DI\object('RDCloud\Infrastructure\EncoderBase64'),
    'Base64UrlEncoder' => DI\object('RDCloud\Infrastructure\EncoderBase64Url'),
    'InitializationVector' => DI\object('RDCloud\Infrastructure\URandomIV'),
    /*'Hasher' => DI\object('RDCloud\Infrastructure\HasherSHA256'),
    'Cipher' => DI\object('RDCloud\Infrastructure\CipherRijndael256'),
*/

    'RDCloud\Infrastructure\IEncoder' => DI\get('Base64UrlEncoder'),
    'RDCloud\Infrastructure\IInitializationVector' => DI\get('InitializationVector'),
/*    'RDCloud\Infrastructure\IHasher' => DI\get('Hasher'),
    'RDCloud\Infrastructure\ICipher' => DI\get('Cipher'),
*/

    'user.ns' => 'RDCloud\Domain\User',
    /* User */

    'RDCloud\Domain\User\IPassKeyIdGenerator' => DI\object('RDCloud\Infrastructure\Uuid1Generator'),
    'RDCloud\Domain\User\IPasswordFactory' => DI\get('RDCloud\GmailWatch\Domain\User\PasswordFactory'),
    'RDCloud\Domain\User\IPassIV' => DI\get('InitializationVector'),
    'RDCloud\Domain\User\IPassHasher' => DI\object('RDCloud\Infrastructure\HasherPBKDF2'),
    'RDCloud\Domain\User\IPassKVRepo' => DI\get('Redis'),
    'RDCloud\Domain\User\IPassKeyRepo' => DI\object('RDCloud\GmailWatch\Domain\User\PassKeyRepo'),
    /*'RDCloud\GmailWatch\Domain\User\IInitializationVector' => DI\object('RDCloud\Infrastructure\URandomIV')
      ->constructor(DI\get('RDCloud\GmailWatch\App\User\IEncoder')),
    */

    'RDCloud\App\IUniqueIdGenerator' => DI\object('RDCloud\Infrastructure\Uuid1Generator'),
    'RDCloud\Infrastructure\IAppendOnlyRepo' => DI\get('Redis'),
    'EventStore' => DI\object('RDCloud\Infrastructure\EventStore'),
    'RDCloud\Domain\IEventStore' => DI\get('EventStore'),
    'RDCloud\Domain\User\IUserRepo' => DI\object('RDCloud\GmailWatch\Domain\User\UserEventStoreRepo'),


    /*->method('addListener', DI\string('{event.ns}\NewUserWasCreated'), DI\string('{event.ns}\SendWelcomeEmailListener'))
    'RDCloud\GmailWatch\Domain\User\NewUserWasCreated'*/
    /* Events */
    'listener.ns' => 'RDCloud\App\Listeners',
    'domain.ns' => 'RDCloud\Domain',

    'RDCloud\App\IEventBus' => DI\object('RDCloud\Infrastructure\LazyEventBusWithContainer')
        ->method('addListener', DI\string('{domain.ns}\User\NewUserWasCreated'), DI\string('{listener.ns}\SendWelcomeEmailListener'))
        ->method('addListener', DI\string('{domain.ns}\User\NameWasChanged'), DI\string('{listener.ns}\TestEventListener'))
        ->method('addListener', DI\string('{domain.ns}\User\EmailWasChanged'), DI\string('{listener.ns}\SendChangedEmailListener'))
        ->method('addListener', DI\string('{domain.ns}\User\PasswordWasChanged'), DI\string('{listener.ns}\SendPasswordChangedEmailListener'))
        ->method('addListener', DI\string('{domain.ns}\User\ChangedGuestUserToRegisteredUser'), DI\string('{listener.ns}\TestEventListener'))
        ->method('addListener', 'rdcloud.gmail_watch.SecretKeyValueWasSet', DI\string('{listener.ns}\SecretKeyValueWasSetListener')),

    /*'mariadb.conn' => [
        'user' => 'rdc',
        'pass' => 'rdctest',
        'host' => 'mariadb',
        'db' => 'rdcloud'
    ],*/

    'RDCloud\Infrastructure\IUuidToOrderedBinaryService' => DI\object('RDCloud\Infrastructure\UuidToOrderedBinaryService'),

    /*'RDCloud\Infrastructure\Projections\PDO\IProjectionRepoPDO' => DI\object('RDCloud\Infrastructure\RepoMariaDB')
        ->constructor(DI\get('mariadb.conn')),*/
    'RDCloud\App\IEventEmitter' => DI\object('RDCloud\Infrastructure\LazyEventBusWithContainer'),

    /* Projections */
    'project.ns' => 'RDCloud\Infrastructure\Projections',
    'RDCloud\GmailWatch\Domain\IEventProjector' => DI\object('RDCloud\Infrastructure\LazyEventProjector')
        /*->constructor(DI\get('RDCloud\Infrastructure\IEventEmitter'), DI\get('RDCloud\Infrastructure\IProjectionRepo'))*/
        ->method('addProjection', DI\string('{domain.ns}\User\NewUserWasCreated'), DI\string('{project.ns}\PDO\AuthSignInProjection'))
        ->method('addProjection', DI\string('{domain.ns}\User\EmailWasChanged'), DI\string('{project.ns}\PDO\AuthSignInProjection')),



    'RDCloud\App\IPdoProjectionRepo' => DI\get('MariaDB'),
    'RDCloud\App\IHttpRequest' => DI\object('RDCloud\Infrastructure\CurlRequest'),
    'RDCloud\App\ICommandBus' => DI\object('RDCloud\Infrastructure\NameCommandBus'),
    'RDCloud\App\IDoiService' => DI\object('RDCloud\App\CrossRef'),
    'RDCloud\Infrastructure\Projections\PDO\IPdoRepo' => DI\get('MariaDB')

];

?>
