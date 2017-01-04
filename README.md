# PinkfireBundle

Symfony bundle to integrate support of [Pinkfire](https://github.com/pinkfire/pinkfire).

Pinkfire is a great tool to help debugging SOA (Service Oriented Architecture) by centralizing logs.

## Install

### Add the bundle to your `composer.json`

```
composer.phar require "pinkfire/pinkfire-bundle"
```

### Update your `app/AppKernel.php`

``` php
<?php
    //...
    if (in_array($this->getEnvironment(), array('dev', 'test'))) {
        //...
        $bundles[] = new Pinkfire\PinkfireBundle\PinkfireBundle();
    }
```

### Update your config (`app/config/config_dev.yml`)

``` yaml
pinkfire:
    application : "my-application" # required
    host: "localhost"              # Optional
    port: 3000                     # Optional
    log_max_length: -1             # Optional, max length of read input data
    url_blacklist: [ ]             # Optional, array of URIs patterns to ignore
    url_debug: [ "_.*" ]           # Optional, array of URIs patterns to mark as debug
    log_level: warning             # Optional, log level used by the Monolog handler
    enabled: true                  # optional, allow to disable the request/response listener
```

*About `enabled`*: This bundle will not throw exception or crash if it can connect the the pinkfire host.
But it adds a small overhead.
So if your pinkfire server is not running and you care about performance, you should disable it.

### Test it

Open pinkfire in you browser then visit your website (in dev environment).
You should see all master requests !

## Go further

### Monolog

You can forward your logs to pinkfire by updating your monolog config in file `app/config/config_dev.yml`

``` yaml
monolog:
    handlers:
        pinkfire:
            type: service
            id: pinkfire.monolog_handler
```

### Guzzle

With Guzzle 6 (and `csa/guzzle-bundle`), a middleware is automatically registered to add the Pinkfire headers (path and channel)
to every requests.

With older version of Guzzle, use our Guzzle subscriber

```php
$client = new GuzzleHttp\Client();
$emitter = $client->getEmitter();
$emitter->attach($this->get('pinkfire.guzzle_subscriber'));
```

### Log all the things !

Use the service `pinkfire.request_aware.client` to send everything you want:

```php
// ...
$client = $this->get('pinkfire.request_aware.client');
$client->push('message', 'level', ['my_context' => 'context'], ['link_1' => 'https://github.com/pinkfire/PinkfireBundle']);
$client->patch('message', 'level', ['my_context' => 'context updated'], ['link_1' => 'https://github.com/pinkfire/PinkfireBundle']);
```

The RequestAwareClient will automatically push/patch to the path and the channel of the master request.
