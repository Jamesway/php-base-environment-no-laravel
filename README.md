# php71 base dev environment - no framework
Installs router, dependency injection container (DIC), data mapper ORM, logger, and a BDD test framework

```


docker run --rm -itv $(pwd):/app jamesway/php71-cli-dev composer nikic/fast-route \
                                                              && php-di/php-di \
                                                              && psr/container \
                                                              && ramsey/uuid \
                                                              && doctrine/orm \
                                                              monolog/monolog

# dev
docker run --rm -itv $(pwd):/app jamesway/php71-cli-dev composer require --dev phpspec/phpspec

# optional
php-console/php-console
docker run --rm -itv $(pwd):/app jamesway/php71-cli-dev composer require --dev php-console/php-console
```
## optional

### php-console/php-console
From packagist: PHP Console allows you to handle PHP errors & exceptions, dump variables, execute PHP code remotely and many other things using Google Chrome extension PHP Console and PhpConsole server library.

Google Chrome extension PHP Console https://chrome.google.com/webstore/detail/php-console/nfhmhhlpfleoednkpnnnkolmclajemef
PhpConsole server library https://github.com/barbushin/php-console


### Docker-sync 
Rename _docker-compose-override.yml to docker-compose-override.yml (remove the underscore) to enable docker-sync.