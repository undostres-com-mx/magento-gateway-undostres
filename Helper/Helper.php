<?php

namespace Undostres\PaymentGateway\Helper;

use UDT\SDK\SASDK;
use Undostres\PaymentGateway\Gateway\Config;
use \Magento\Framework\App\Action\Context;
use \Magento\Sales\Model\OrderFactory;
use Magento\Store\Model\StoreManagerInterface;
use \Magento\Checkout\Model\Session;
use \Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

/* PHP CLASS WHIT COMMON FUNCTIONS */

class Helper
{
    private $_logger;
    private $_orderFactory;
    private $_session;
    private $_messager;
    private $_storeManager;

    public function __construct(Context $context, LoggerInterface $logger, Session $session, OrderFactory $orderFactory, StoreManagerInterface $storeManager)
    {
        $this->_messager = $context->getMessageManager();
        $this->_logger = $logger;
        $this->_session = $session;
        $this->_orderFactory = $orderFactory;
        $this->_storeManager = $storeManager;
        SASDK::init('36wqV4OcrAa1/Sq9LJ7ARcclXqRhBJsVTZEFR4eo8Htxn6o4nKPrfpW/9rmP3SxPMNCSIfel+507CLU1HIknbSq242/YXNeun/Kwyhqp47LqdiSEUrlwNhBezHSiQwjx6c58W0NUne+IvfKl255TE4qn5Upf1AYoo4CzWClNkfN4vftn/FNOTahWZR6nL46IkzhQqTbNkWDjApP3NXhiBpVaUsci1f9JXaC9WlMR4mWV1FsghFgvPSpCUac+1T/O+pdkHORk0borVbQqBtzox+iZlqkgwjy2TyBpIVwgDhVer5IwhzSaA6Bz4uWULpPMIf3nAqtxShmNwNCnAX5Z1lPrhSdH3j+5hClk47kWCkqHU7sGC+LllD2yOeZtD5YFp2BHdAmlNJHh0p5EClLbcryWaYRRSiOOgZWC7zObOVU=', 'https://nobugs.undostres.com.mx');
    }
}
