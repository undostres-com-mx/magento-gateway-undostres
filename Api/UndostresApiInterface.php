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
     * @param string $orderId
     * @return mixed
     */
    public function redirect(string $orderId);

    /**
     * @param string $orderId
     * @return mixed
     */
    public function status(string $orderId);

    /**
     * @return mixed
     */
    public function getLogs();

    /**
     * @return mixed
     */
    public function deleteLogs();
}
