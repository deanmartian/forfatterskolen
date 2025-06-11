<?php

namespace App\Helpers;

use Svea\WebPay\Config\ConfigurationProvider;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Config\SveaConfigurationProvider;

class SveaConfig extends ConfigurationService
{
    const SWP_TEST_URL = 'https://webpaypaymentgatewaystage.svea.com/webpay/payment';

    const SWP_PROD_URL = 'https://webpaypaymentgateway.svea.com/webpay/payment';

    const SWP_TEST_WS_URL = 'https://webpaywsstage.svea.com/SveaWebPay.asmx?WSDL';

    const SWP_PROD_WS_URL = 'https://webpayws.svea.com/SveaWebPay.asmx?WSDL';

    const SWP_TEST_HOSTED_ADMIN_URL = 'https://webpaypaymentgatewaystage.svea.com/webpay/rest/'; // ends with "/" as we need to add request method

    const SWP_PROD_HOSTED_ADMIN_URL = 'https://webpaypaymentgateway.svea.com/webpay/rest/'; // ends with "/" as we need to add request method

    const SWP_TEST_ADMIN_URL = 'https://webpayadminservicestage.svea.com/AdminService.svc/backward';

    const SWP_PROD_ADMIN_URL = 'https://webpayadminservice.svea.com/AdminService.svc/backward';

    const SWP_TEST_PREPARED_URL = 'https://webpaypaymentgatewaystage.svea.com/webpay/preparedpayment/';

    const SWP_PROD_PREPARED_URL = 'https://webpaypaymentgateway.svea.com/webpay/preparedpayment/';

    public static function getCustomConfig($isProd)
    {
        [$config, $urls] = self::retrieveConfigFile($isProd);

        $credentialParams = [];
        $credentials = $config['credentials'];

        $commonCredentials = $config['commonCredentials'];
        $checkoutCredentials = $config['checkoutCredentials'];
        $credentialParams['common'] = [];
        foreach ($credentials as $countryCode => $configPerCountry) {
            $credentialParams[$countryCode] = ['auth' => []];
            foreach ($configPerCountry as $paymentType => $configPerType) {
                if ($paymentType === ConfigurationProvider::CHECKOUT && ($countryCode == 'DE' || $countryCode == 'NL')) {
                    $configPerType = array_merge($configPerType, $checkoutCredentials);
                }
                $credentialParams[$countryCode]['auth'][$paymentType] = $configPerType;
            }
            $credentialParams[$countryCode]['auth'][ConfigurationProvider::HOSTED_TYPE] = $commonCredentials;
        }

        $credentialParams['common']['auth'][ConfigurationProvider::HOSTED_TYPE] = $commonCredentials;
        $credentialParams['common']['auth'][ConfigurationProvider::CHECKOUT] = $checkoutCredentials;

        $integrationProperties = $config['integrationParams'];

        return new SveaConfigurationProvider(['url' => $urls, 'credentials' => $credentialParams, 'integrationproperties' => $integrationProperties]);
    }

    private static function retrieveConfigFile($isProd)
    {
        if ($isProd === true) {
            $config = require base_path('/config/svea_prod.php');
            $urls = self::getProdUrls();
        } else {
            $config = require base_path('/config/svea_test.php');
            $urls = self::getTestUrls();
        }

        return [$config, $urls];
    }

    private static function getTestUrls()
    {
        return [
            ConfigurationProvider::HOSTED_TYPE => self::SWP_TEST_URL,
            ConfigurationProvider::INVOICE_TYPE => self::SWP_TEST_WS_URL,
            ConfigurationProvider::PAYMENTPLAN_TYPE => self::SWP_TEST_WS_URL,
            ConfigurationProvider::ACCOUNTCREDIT_TYPE => self::SWP_TEST_WS_URL,
            ConfigurationProvider::HOSTED_ADMIN_TYPE => self::SWP_TEST_HOSTED_ADMIN_URL,
            ConfigurationProvider::ADMIN_TYPE => self::SWP_TEST_ADMIN_URL,
            ConfigurationProvider::PREPARED_URL => self::SWP_TEST_PREPARED_URL,
            ConfigurationProvider::CHECKOUT => self::CHECKOUT_TEST_BASE_URL,
            ConfigurationProvider::CHECKOUT_ADMIN => self::CHECKOUT_ADMIN_TEST_BASE_URL,
        ];
    }

    private static function getProdUrls()
    {
        return [
            ConfigurationProvider::HOSTED_TYPE => self::SWP_PROD_URL,
            ConfigurationProvider::INVOICE_TYPE => self::SWP_PROD_WS_URL,
            ConfigurationProvider::PAYMENTPLAN_TYPE => self::SWP_PROD_WS_URL,
            ConfigurationProvider::ACCOUNTCREDIT_TYPE => self::SWP_PROD_WS_URL,
            ConfigurationProvider::HOSTED_ADMIN_TYPE => self::SWP_PROD_HOSTED_ADMIN_URL,
            ConfigurationProvider::ADMIN_TYPE => self::SWP_PROD_ADMIN_URL,
            ConfigurationProvider::PREPARED_URL => self::SWP_PROD_PREPARED_URL,
            ConfigurationProvider::CHECKOUT => self::CHECKOUT_PROD_BASE_URL,
            ConfigurationProvider::CHECKOUT_ADMIN => self::CHECKOUT_ADMIN_PROD_BASE_URL,
        ];
    }

    public static function cardConfig()
    {
        $cardConfig = [
            'common' => ['auth' => [
                ConfigurationProvider::INVOICE_TYPE => ['username' => '', 'password' => '', 'clientNumber' => ''],
                ConfigurationProvider::PAYMENTPLAN_TYPE => ['username' => '', 'password' => '', 'clientNumber' => ''],
                ConfigurationProvider::HOSTED_TYPE => [
                    // swap these for your actual merchant id and secret word
                    'merchantId' => 1200,
                    'secret' => '27f18bfcbe4d7f39971cb3460fbe7234a82fb48f985cf22a068fa1a685fe7e6f93c7d0d92fee4e8fd7dc0c9f11e2507300e675220ee85679afa681407ee2416d',
                ],
            ],
            ],
        ];

        $url = [
            ConfigurationProvider::HOSTED_TYPE => self::SWP_TEST_URL,
            ConfigurationProvider::INVOICE_TYPE => self::SWP_TEST_WS_URL,
            ConfigurationProvider::PAYMENTPLAN_TYPE => self::SWP_TEST_WS_URL,
            ConfigurationProvider::HOSTED_ADMIN_TYPE => self::SWP_TEST_HOSTED_ADMIN_URL,
            ConfigurationProvider::ADMIN_TYPE => self::SWP_TEST_ADMIN_URL,
        ];

        return new SveaConfigurationProvider(['url' => $url, 'credentials' => $cardConfig]);
    }
}
