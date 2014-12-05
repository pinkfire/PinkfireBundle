SosSoaBundle
============

ABOUT
-----

Symfony bundle to integrate support of [sos-soa](https://github.com/iamluc/sos-soa).

sos-soa is a small tool to help debugging SOA (Service Oriented Architecture) by centralizing logs.

INSTALL
-------

### Install with composer

```
composer.phar require "gloomy/sossoa-bundle" "~0.2.0"
```

### Update your app/AppKernel.php

``` php
<?php
    //...
    if (in_array($this->getEnvironment(), array('dev', 'test'))) {
        //...
        $bundles[] = new Gloomy\SosSoaBundle\GloomySosSoaBundle();
    }
```

### Update your config (app/config/config_dev.yml)

``` yaml
gloomy_sos_soa:
    application : "my-application" # required
    host: "localhost:3000"         # Optional
```