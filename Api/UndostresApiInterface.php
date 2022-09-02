<?php

namespace Undostres\PaymentGateway\Api;

interface UndostresApiInterface
{
    public function callback($paymentId, $status);
    public function redirect($orderId);
}
