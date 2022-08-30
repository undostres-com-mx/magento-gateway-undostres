<?php

namespace undostres\paymentGateway\Gateway\Config;

use \Magento\Payment\Gateway\Config\Config as MagentoConfig;

/* EXTRACTS INFO FROM MAGENTO CONFIG THROUGHT CONFIG EXTENDED CLASS - DICTIONARY */

class Config extends MagentoConfig
{
    /* KEYS INSIDE MAGENTO CONFIG */
    const CODE = 'undostres-gateway';
    const KEY_TITLE = 'title';
    const KEY_DESCRIPTION = 'description';
    const KEY_GATEWAY_LOGO = 'gateway_logo';
    const KEY_SPECIFIC_COUNTRY = 'specificcountry';
    const KEY_DATA = 'key';
    const KEY_MODE = 'mode';
    const KEY_LOGGING = 'log';

    /* PRIVATE KEYS */
    const X_VTEX_API_APPKEY = 'vtexappkey-undostres-BILMYN';
    const X_VTEX_API_APPTOKEN = 'DGHMFVTVWRQEGOJGTHMPVATZYLXSIAFJRQJZMQFCWQWNZDWIRIBAXZVCMINUXANWMGNPEJUDCJSFEFZVOOBZZEMRUGMECLFVDABTPHEHOXYNGQYGISFIZNWHTWUXWQAR';

    /* USER KEYS */
    const UDT_KEY = 'magento_key';
    const UDT_TOKEN = 'magento_token';

    /* OTHERS */
    const UDT_APP_LOG = true;           //Enable logging -> true/false
    const UDT_APP_ENVIRONMENT = 'prod'; //localhost (localhost:8081) - test (test.undostres.com.mx) - prod (undostres.com.mx)

    /* GET GATEWAY TITLE */
    public function getTitle()
    {
        return $this->getValue(self::KEY_TITLE);
    }

    /* GET GATEWAY DESCRIPTION */
    public function getDescription()
    {
        return $this->getValue(self::KEY_DESCRIPTION);
    }

    /* GET GATEWAY LOGO */
    public function getLogo()
    {
        return $this->getValue(self::KEY_GATEWAY_LOGO);
    }

    /* GET GATEWAY COUNTRIES */
    public function getSpecificCountry()
    {
        return $this->getValue(self::KEY_SPECIFIC_COUNTRY);
    }

    /* GET MARCHANT KEY */
    public function getKey(){
        return $this->getValue(self::KEY_DATA);
    }

    /* GET MODE */
    public function getMode(){
        return $this->getValue(self::KEY_MODE);
    }

    /* GET LOG */
    public function getLog(){
        return $this->getValue(self::KEY_LOGGING);
    }
}
