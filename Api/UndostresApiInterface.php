<?php

namespace Undostres\PaymentGateway\Api;

/**
 * @api
 */
interface UndostresApiInterface
{
    /**
     * @param string $paymentId
     * @param string $status
     * @return mixed
     */
    public function callback(string $paymentId, string $status);

    /**
     * @param string $orderid
     * @return mixed
     */
    public function redirect(string $orderid);
}
