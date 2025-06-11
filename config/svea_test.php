<?php

namespace Config;

/**
 * This is Svea Test credentials, and you can use this parameters during developing and testing
 * For production parameters you have to change config_prod.php file
 */

use Svea\WebPay\Config\ConfigurationProvider;

return [
    'integrationParams' => [
        'integrationcompany' => 'myIntegrationCompany',
        'integrationversion' => 'myIntegrationVersion',
        'integrationplatform' => 'myIntegrationPlatform',
    ],
    'commonCredentials' => [
        'merchantId' => '1200',
        'secret' => '27f18bfcbe4d7f39971cb3460fbe7234a82fb48f985cf22a068fa1a685fe7e6f93c7d0d92fee4e8fd7dc0c9f11e2507300e675220ee85679afa681407ee2416d',
    ],
    'checkoutCredentials' => [
        'checkoutMerchantId' => '124842',
        'checkoutSecret' => '1NDxpT2WQ4PW6Ud95rLWKD98xVr45Q8O9Vd52nomC7U9B18jp7lHCu7nsiTJO1NWXjSx26vE41jJ4rul7FUP1cGKXm4wakxt3iF7k63ayleb1xX9Di2wW46t9felsSPW',
    ],
    'defaultCountryCode' => 'SE',
    'credentials' => [
        'SE' => [
            ConfigurationProvider::INVOICE_TYPE => [
                'username' => 'sverigetest',
                'password' => 'sverigetest',
                'clientNumber' => 79021,
            ],
            ConfigurationProvider::PAYMENTPLAN_TYPE => [
                'username' => 'sverigetest',
                'password' => 'sverigetest',
                'clientNumber' => 59999,
            ],
            ConfigurationProvider::ACCOUNTCREDIT_TYPE => [
                'username' => 'sverigetest',
                'password' => 'sverigetest',
                'clientNumber' => 58702,
            ],
            ConfigurationProvider::CHECKOUT => [
                'checkoutMerchantId' => '124842', // Swedish test merchant
                'checkoutSecret' => '1NDxpT2WQ4PW6Ud95rLWKD98xVr45Q8O9Vd52nomC7U9B18jp7lHCu7nsiTJO1NWXjSx26vE41jJ4rul7FUP1cGKXm4wakxt3iF7k63ayleb1xX9Di2wW46t9felsSPW',
            ],
            ConfigurationProvider::HOSTED_TYPE => [
                // swap these for your actual merchant id and secret word
                'merchantId' => 1200,
                'secret' => '27f18bfcbe4d7f39971cb3460fbe7234a82fb48f985cf22a068fa1a685fe7e6f93c7d0d92fee4e8fd7dc0c9f11e2507300e675220ee85679afa681407ee2416d',
            ],
        ],
        'NO' => [
            ConfigurationProvider::INVOICE_TYPE => [
                'username' => 'norgetest2',
                'password' => 'norgetest2',
                'clientNumber' => 33308,
            ],
            ConfigurationProvider::PAYMENTPLAN_TYPE => [
                'username' => 'norgetest2',
                'password' => 'norgetest2',
                'clientNumber' => 32503,
            ],
            ConfigurationProvider::ACCOUNTCREDIT_TYPE => [
                'username' => '',
                'password' => '',
                'clientNumber' => '',
            ],
            ConfigurationProvider::CHECKOUT => [
                'checkoutMerchantId' => '124941', // Norwegian test merchant
                'checkoutSecret' => 'XDyrnJnhbvmOch6brKPbF6mVx4NG7Wqzzhm92tsrx3H2IB3m82QxqwM4EUz5Cq9X8kEPpfZxzayB4pfkVEAC2uemgEikIUTf3v1pHxAuRlGuycWt6XyKkjBm9oQxR6pG',
            ],
            ConfigurationProvider::HOSTED_TYPE => [
                // swap these for your actual merchant id and secret word
                'merchantId' => 1200,
                'secret' => '27f18bfcbe4d7f39971cb3460fbe7234a82fb48f985cf22a068fa1a685fe7e6f93c7d0d92fee4e8fd7dc0c9f11e2507300e675220ee85679afa681407ee2416d',
            ],
        ],
        'FI' => [
            ConfigurationProvider::INVOICE_TYPE => [
                'username' => 'finlandtest2',
                'password' => 'finlandtest2',
                'clientNumber' => 26136,
            ],
            ConfigurationProvider::PAYMENTPLAN_TYPE => [
                'username' => 'finlandtest2',
                'password' => 'finlandtest2',
                'clientNumber' => 27136,
            ],
            ConfigurationProvider::ACCOUNTCREDIT_TYPE => [
                'username' => '',
                'password' => '',
                'clientNumber' => '',
            ],
            ConfigurationProvider::CHECKOUT => [
                'checkoutMerchantId' => '', // No finnish test merchant yet
                'checkoutSecret' => '',
            ],
            ConfigurationProvider::HOSTED_TYPE => [
                // swap these for your actual merchant id and secret word
                'merchantId' => 1200,
                'secret' => '27f18bfcbe4d7f39971cb3460fbe7234a82fb48f985cf22a068fa1a685fe7e6f93c7d0d92fee4e8fd7dc0c9f11e2507300e675220ee85679afa681407ee2416d',
            ],
        ],
        'DK' => [
            ConfigurationProvider::INVOICE_TYPE => [
                'username' => 'danmarktest2',
                'password' => 'danmarktest2',
                'clientNumber' => 62008,
            ],
            ConfigurationProvider::PAYMENTPLAN_TYPE => [
                'username' => 'danmarktest2',
                'password' => 'danmarktest2',
                'clientNumber' => 64008,
            ],
            ConfigurationProvider::ACCOUNTCREDIT_TYPE => [
                'username' => '',
                'password' => '',
                'clientNumber' => '',
            ],
            ConfigurationProvider::HOSTED_TYPE => [
                // swap these for your actual merchant id and secret word
                'merchantId' => 1200,
                'secret' => '27f18bfcbe4d7f39971cb3460fbe7234a82fb48f985cf22a068fa1a685fe7e6f93c7d0d92fee4e8fd7dc0c9f11e2507300e675220ee85679afa681407ee2416d',
            ],
        ],
        'NL' => [
            ConfigurationProvider::INVOICE_TYPE => [
                'username' => 'hollandtest',
                'password' => 'hollandtest',
                'clientNumber' => 85997,
            ],
            ConfigurationProvider::PAYMENTPLAN_TYPE => [
                'username' => 'hollandtest',
                'password' => 'hollandtest',
                'clientNumber' => 86997,
            ],
            ConfigurationProvider::ACCOUNTCREDIT_TYPE => [
                'username' => '',
                'password' => '',
                'clientNumber' => '',
            ],
            ConfigurationProvider::HOSTED_TYPE => [
                // swap these for your actual merchant id and secret word
                'merchantId' => 1200,
                'secret' => '27f18bfcbe4d7f39971cb3460fbe7234a82fb48f985cf22a068fa1a685fe7e6f93c7d0d92fee4e8fd7dc0c9f11e2507300e675220ee85679afa681407ee2416d',
            ],
        ],
        'DE' => [
            ConfigurationProvider::INVOICE_TYPE => [
                'username' => 'germanytest',
                'password' => 'germanytest',
                'clientNumber' => 14997,
            ],
            ConfigurationProvider::PAYMENTPLAN_TYPE => [
                'username' => 'germanytest',
                'password' => 'germanytest',
                'clientNumber' => 16997,
            ],
            ConfigurationProvider::ACCOUNTCREDIT_TYPE => [
                'username' => '',
                'password' => '',
                'clientNumber' => '',
            ],
            ConfigurationProvider::HOSTED_TYPE => [
                // swap these for your actual merchant id and secret word
                'merchantId' => 1200,
                'secret' => '27f18bfcbe4d7f39971cb3460fbe7234a82fb48f985cf22a068fa1a685fe7e6f93c7d0d92fee4e8fd7dc0c9f11e2507300e675220ee85679afa681407ee2416d',
            ],
        ],
    ],
];
