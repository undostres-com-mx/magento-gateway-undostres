<?php

namespace Undostres\PaymentGateway\Api;

/**
 * @api
 */
interface UndostresApiInterface
{
    /**
     * @param $paymentId
     * @param $status
     * @return mixed
     */
    public function callback($paymentId, $status);

    /**
     * @param $orderId
     * @return mixed
     */
    public function redirect($orderId);
}
