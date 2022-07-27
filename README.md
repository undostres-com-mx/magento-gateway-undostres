# UnDosTres Payment Module For Magento

## Plugin information

This plugin has been tested on magento 2.3 up to 2.4.2.

On the composer.json are the dependencies linked.

## Installation

### Uploading files installation

It's needed to make the following dir structure:
`app/code/unDosTres/paymentGateway`

Upload the plugin files there and then do the steps of **Updating magento core** section.

To update the plugin it's necesary to delete all the files inside the plugin folder and upload there the latest version of plugin, then do  **Updating magento core** section.

### Composer installation

To install throught composer it's only necesary to add the requeriment inside the root of magento installation.

```
composer require undostres-com-mx/paymentGateway
``` 

Then do the steps of **Updating magento core** section.

To update the plugin execute inside the root of magento installation:

```
composer update
``` 

Then do the steps of **Updating magento core** section.

## Configuration

All the configuration needed its inside:
`app/code/unDosTres/paymentGateway/registration.php`

The logging system logs into:
`var/log/system.log`

## Updating magento core

It's needed to run the following code (*Inside the magento root installation directory*) after installing or update to properly update the core of magento, enabling the gateway.

```
php bin/magento maintenance:enable 
php bin/magento module:enable unDosTres_paymentGateway
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
php bin/magento setup:di:compile
php bin/magento cache:clean
php bin/magento cache:flush
php bin/magento maintenance:disable
``` 
