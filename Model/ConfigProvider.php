<?php

namespace Undostres\PaymentGateway\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Customer\Model\Session;
use Magento\Backend\Model\Session\Quote;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\App\RequestInterface;

final class ConfigProvider implements ConfigProviderInterface
{
    protected $gatewayConfig;
    protected $assetRepo;
    protected $request;

    public function __construct(Config $gatewayConfig, Repository $assetRepo, RequestInterface $request)
    {
        $this->gatewayConfig = $gatewayConfig;
        $this->assetRepo = $assetRepo;
        $this->request = $request;
    }

    public function getConfig(): array
    {
        $logo = $this->assetRepo->getUrlWithParams('Undostres_PaymentGateway::images/undostres_logo.png', array_merge(['_secure' => $this->request->isSecure()], []));

        /* THIS CONFIG IS PASSED TO JS */
        return [
            'payment' => [
                Config::CODE => [
                    'code' => Config::CODE,
                    'title' => $this->gatewayConfig->getTitle(),
                    'logo' => $logo,
                ]
            ]
        ];
    }
}
