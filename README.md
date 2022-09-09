# UnDosTres Payment Module For Magento

## Plugin information

This plugin has been tested on Magento Open Source 2.4.3 - 2.4.5, version recommended 2.4.5.

The composer.json have the dependencies of the plugin.

---

## Installation

To install through composer it's only necessary to execute the following on the magento's root folder.

```
php bin/magento maintenance:enable && composer require undostres-com-mx/magento-gateway-undostres && php bin/magento module:enable Undostres_PaymentGateway && php bin/magento setup:upgrade && php bin/magento setup:static-content:deploy -f && php bin/magento setup:di:compile && php bin/magento cache:flush && php bin/magento maintenance:disable
``` 

---

## Update

To update through composer it's only necessary to execute the following on the magento's root folder.

```
php bin/magento maintenance:enable && composer update undostres-com-mx/magento-gateway-undostres && php bin/magento setup:upgrade && php bin/magento setup:static-content:deploy -f && php bin/magento setup:di:compile && php bin/magento cache:flush && php bin/magento maintenance:disable
``` 

---

## Uninstall

To delete the plugin:

```
php bin/magento maintenance:enable && php bin/magento module:disable Undostres_PaymentGateway && composer remove undostres-com-mx/magento-gateway-undostres && php bin/magento setup:upgrade && php bin/magento setup:static-content:deploy -f && php bin/magento setup:di:compile && php bin/magento cache:flush && php bin/magento maintenance:disable
``` 

---

## Configuration

All the configuration needed it's on the payment methods page located on admin site.

---

## Logs

The logging system logs into a folder inside magento root directory.

```
nano var/log/Undostres_Gateway.log
``` 
