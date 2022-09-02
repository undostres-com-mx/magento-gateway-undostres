<?php

namespace Undostres\PaymentGateway\Helper;

use \Magento\Framework\App\Action\Action;
use \Magento\Framework\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;
use \Magento\Sales\Model\OrderFactory;
use \Magento\Checkout\Model\Session;
use \Psr\Log\LoggerInterface;

/* EXTENDER FOR UTILITY TOOLS IN CONTROLLERS */

abstract class AbstractAction extends Action
{
    
    private $_helper;

    public function __construct(Context $context, LoggerInterface $logger, Session $session, OrderFactory $orderFactory, StoreManagerInterface $storeManager)
    {
        parent::__construct($context);
        $this->_helper = new HelperOld($context, $logger, $session, $orderFactory, $storeManager);
    }

    /* LOG INTO MAGENTO LOGS */
    protected function log($message)
    {
        $this->_helper->log($message);
    }

    /* ADD MESSAGE ON MAGENTO FRONTEND */
    protected function addFrontMesage($type, $msg)
    {
        $this->_helper->addFrontMesage($type, $msg);
    }

    /* REDIRECT TO SUCCESS PAGE */
    protected function redirectToCheckoutOnePageSuccess()
    {
        $this->_redirect('checkout/onepage/success');
    }

    /* REDIRECT TO CART PAGE */
    protected function redirectToCheckoutCart()
    {
        $this->_redirect('checkout/cart');
    }

    /* GET ORDER FROM MAGENTO */
    protected function getOrder()
    {
        return $this->_helper->getOrder();
    }

    /* GET ORDER JSON TO SEND UDT */
    protected function getOrderJSON($order)
    {
        return $this->_helper->getOrderJSON($order);
    }

    /* RESTORE CART */
    public function restoreCart()
    {
        return $this->_helper->restoreCart();
    }

    /* REDIRECT USING JS */
    public function redirectPage($redirectUrl)
    {
        $this->_helper->redirectPage($redirectUrl);
    }

    /* GENERETE DE URL TO PAY ON UDT */
    public function getPaymentUrl($json)
    {
        return $this->_helper->getPaymentUrl($json);
    }

    /* CANCEL ORDER */
    public function cancelOrder($order)
    {
        return $this->_helper->cancelOrder($order);
    }

    /* CHECK IS ORDER PENDING */
    public function isOrderPending($order)
    {
        return $this->_helper->isOrderPending($order);
    }

    /* CHECK IS ORDER CENCELED */
    public function isOrderCanceled($order)
    {
        return $this->_helper->isOrderCanceled($order);
    }

    /* CHECK IS ORDER PROCESING */
    public function isOrderProcesing($order)
    {
        return $this->_helper->isOrderProcesing($order);
    }
}
