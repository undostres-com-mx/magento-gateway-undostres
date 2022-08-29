# UnDosTres Payment Module For Magento

## Plugin information

This plugin has been tested on magento 2.4.X.

The composer.json have the dependencies of the plugin.

---

## Installation

To install through composer it's only necessary to execute the following on the magento's root folder.

```
composer require undostres-com-mx/magento-gateway-undostres
php bin/magento maintenance:enable 
php bin/magento module:enable magento-gateway-undostres
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy -f
php bin/magento setup:di:compile
php bin/magento cache:flush
php bin/magento maintenance:disable
``` 

---

## Update

To update through composer it's only necessary to execute the following on the magento's root folder.

```
composer update undostres-com-mx/magento-gateway-undostres
php bin/magento maintenance:enable
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy -f
php bin/magento setup:di:compile
php bin/magento cache:flush
php bin/magento maintenance:disable
``` 

---

## Uninstall

To delete the plugin:

```
php bin/magento maintenance:enable
php bin/magento module:disable magento-gateway-undostres
composer remove undostres-com-mx/magento-gateway-undostres
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy -f
php bin/magento setup:di:compile
php bin/magento cache:flush
php bin/magento maintenance:disable
``` 

---

## Configuration

All the configuration needed it's on the payment methods page located on admin site.

---

## Logs

The logging system logs into a folder inside magento root directory.

```
nano var/log/system.log
``` 
