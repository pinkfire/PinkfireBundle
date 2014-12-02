SosSoaBundle
============

ABOUT
-----

Symfony bundle to integrate support of [sos-soa](https://github.com/iamluc/sos-soa).

sos-soa is a small tool to help debugging SOA (Service Oriented Architecture) by centralizing logs.

INSTALL
-------

### Install with composer

    composer.phar require "gloomy/sossoa-bundle" "~0.1.0"

### Update your app/AppKernel.php

``` php
<?php
    //...
    if (in_array($this->getEnvironment(), array('dev', 'test'))) {
        //...
        $bundles[] = new Gloomy\SosSoaBundle\GloomySosSoaBundle();
    }
```