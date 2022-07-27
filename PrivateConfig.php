<?php

namespace unDosTres\paymentGateway;

/* PRIVATE KEYS */
class PrivateConfig
{
    const SDK = array(
        'appKey' => 'magento_key',
        'appToken' => 'magento_token',
        'urlCancel' => '/api/v1/superapp/{paymentId}/cancellations',
        'urlRefund' => '/api/v1/superapp/{paymentId}/refunds',
        'urlPayment' => '/api/v1/superapp/payments',
        'host' => 'https://test.undostres.com.mx',
    );

    const KEY = 'wLRwmn/EY8cR9daYGvZ9zPF5UqRyCPtfmvFf2eo3G7CWqLlw1Y0UK1vRyfrug2lFun3/z3H9+S9I+5GIRx9vtxStT1/xXRgoq3K6JVvZ/eRDQb0ZM3i6/Z0GsFocoQrk+vl3kBEPyeG1pbtVm5KJd4zmTNcvMMtGtVLnblu3UU8LfDO2fSo/bA411sRrdrhRilV56iYBWN+5m0ozFIdNsH0xsgxLn0pd/q7/OFqfkwjwJBEKz/PPgNg2z/jwV64/93V7tetBQQ9tG3io9frnXe9ZceDPJ41WHSM+izhBMTKF7aiZvXvYmkUuu0Ey1VTeBLJj+UJDjdrpmu4dz+FaMCa9FpZiQBGiZw4lEoXiUTKMSYp/8dcc+EcF1B59UX+XenlRb/KjXZD1mpI1Z73P8S5bCboKCGrFPiXw5NtoDOHJz04V3nbnPWUb67mxNcrEbAwbTR8HduuTlOcqglIn8OM7QJ0C3ghoUkHInexoxhfTynxtYf/N+AhMuq1ZUVNx/mDJ85XkLD7mpZtDfowcg5EKzqtzI0eeqzMSXLNPyc8=';
}
