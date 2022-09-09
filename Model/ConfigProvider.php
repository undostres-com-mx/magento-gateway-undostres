<?php

namespace Undostres\PaymentGateway\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\App\RequestInterface;

/* THIS CONFIG IS PASSED TO JS TO CONSTRUCT PAYMENT UI */

final class ConfigProvider implements ConfigProviderInterface
{
    protected $gatewayConfig;
    protected $assetRepo;
    protected $request;

    /**
     * @param Config $gatewayConfig
     * @param Repository $assetRepo
     * @param RequestInterface $request
     */
    public function __construct(Config $gatewayConfig, Repository $assetRepo, RequestInterface $request)
    {
        $this->gatewayConfig = $gatewayConfig;
        $this->assetRepo = $assetRepo;
        $this->request = $request;
    }

    /**
     * @return void
     */
    public function getConfig(): array
    {
        $logo = $this->assetRepo->getUrlWithParams('Undostres_PaymentGateway::images/undostres_logo.png', array_merge(['_secure' => $this->request->isSecure()], []));
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
