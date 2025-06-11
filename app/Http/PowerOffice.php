<?php

namespace App\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class PowerOffice
{
    private $app_key;

    private $client_key;

    private $subscription_key;

    private $base_url;

    private $authorization;

    protected $client;

    protected $commonHeaders;

    public function __construct()
    {
        $this->app_key = env('PO_APP_KEY');
        $this->client_key = env('PO_CLIENT_KEY');
        $this->subscription_key = env('PO_SUBSCRIPTION_KEY');
        $this->base_url = 'https://goapi.poweroffice.net/v2';
        $this->authorization = 'Basic '.base64_encode($this->app_key.':'.$this->client_key);

        $this->client = new Client;
        $this->commonHeaders = [
            'Ocp-Apim-Subscription-Key' => $this->subscription_key,
        ];
    }

    public function authorize()
    {

        $data = [
            'grant_type' => 'client_credentials',
        ];

        $headers = [
            'Authorization' => $this->authorization,
        ];

        return $this->post('https://goapi.poweroffice.net/oauth/Token', $data, 'form', $headers);
    }

    public function customers()
    {
        $authorization = $this->authorize();

        $headers = [
            'Authorization' => 'Bearer '.$authorization['access_token'],
        ];

        return $this->get($this->base_url.'/Customers?Fields=EmailAddress,FirstName,LastName,LegalName,Id', $headers);
    }

    public function customer($id)
    {
        $authorization = $this->authorize();

        $headers = [
            'Authorization' => 'Bearer '.$authorization['access_token'],
        ];

        return $this->get($this->base_url.'/Customers/'.$id, $headers);
    }

    public function registerCustomer($first_name, $last_name, $email, $address = null, $city = null, $zip = null)
    {
        $authorization = $this->authorize();

        $headers = [
            'Authorization' => 'Bearer '.$authorization['access_token'],
        ];

        $data = [
            'FirstName' => $first_name,
            'LastName' => $last_name,
            'Name' => $first_name.' '.$last_name,
            'EmailAddress' => $email,
            'IsPerson' => true,
            'MailAddress' => [
                'AddressLine1' => $address,
                'City' => $city,
                'CountryCode' => 'NO',
                'ZipCode' => $zip,
            ],
        ];

        return $this->post($this->base_url.'/Customers', $data, 'json', $headers);
    }

    public function salesOrder($orderData)
    {
        $authorization = $this->authorize();
        $data = [
            'CreatedDateTimeOffset' => now(),
            'CurrencyCode' => 'NOK',
            'CustomerReference' => $orderData['reference'],
            'CustomerId' => $orderData['customer_id'],
            'PurchaseOrderReference' => $orderData['reference'],
            'SalesOrderDate' => today(),
            'SalesOrderLines' => [
                [
                    'LineType' => 'Normal',
                    'Description' => $orderData['product_description'], // not required,
                    'ProductId' => $orderData['product_id'],
                    'ProductUnitCost' => $orderData['product_unit_cost'],
                    'ProductUnitPrice' => $orderData['product_unit_price'],
                    'Quantity' => 1,
                ],
            ],
        ];

        $headers = [
            'Authorization' => 'Bearer '.$authorization['access_token'],
        ];

        return $this->post($this->base_url.'/SalesOrders/Complete', $data, 'json', $headers);
    }

    public function saleOrder($id)
    {
        $authorization = $this->authorize();
        $headers = [
            'Authorization' => 'Bearer '.$authorization['access_token'],
        ];

        return $this->get($this->base_url.'/SalesOrders/'.$id.'/Complete', $headers);
    }

    public function saleOrders()
    {
        $authorization = $this->authorize();
        $headers = [
            'Authorization' => 'Bearer '.$authorization['access_token'],
        ];

        return $this->get($this->base_url.'/SalesOrders', $headers);
    }

    public function saleOrderLines($id)
    {
        $authorization = $this->authorize();
        $headers = [
            'Authorization' => 'Bearer '.$authorization['access_token'],
        ];

        return $this->get($this->base_url.'/SalesOrders/'.$id.'/Lines', $headers);
    }

    public function products()
    {
        $authorization = $this->authorize();

        $headers = [
            'Authorization' => 'Bearer '.$authorization['access_token'],
        ];

        return $this->get($this->base_url.'/Products', $headers);
    }

    public function outgoingInvoices()
    {
        $authorization = $this->authorize();

        $headers = [
            'Authorization' => 'Bearer '.$authorization['access_token'],
        ];

        return $this->get($this->base_url.'/OutgoingInvoices', $headers);
    }

    public function outgoingInvoice($id)
    {
        $authorization = $this->authorize();

        $headers = [
            'Authorization' => 'Bearer '.$authorization['access_token'],
        ];

        return $this->get($this->base_url.'/OutgoingInvoices/'.$id, $headers);
    }

    public function outgoingInvoiceLines($invoice_id)
    {
        $authorization = $this->authorize();

        $headers = [
            'Authorization' => 'Bearer '.$authorization['access_token'],
        ];

        return $this->get($this->base_url.'/OutgoingInvoices/'.$invoice_id.'/Lines', $headers);
    }

    public function get($url, $headers = [])
    {
        return $this->request('GET', $url, ['headers' => array_merge($this->commonHeaders, $headers)]);
    }

    public function post($url, $data = [], $contentType = 'json', $headers = [])
    {
        $requestData = [];

        if ($contentType === 'json') {
            // For JSON content type
            $requestData['json'] = $data;
            $requestData['headers'] = array_merge($this->commonHeaders, $headers,
                ['Content-Type' => 'application/json']);
        } elseif ($contentType === 'form') {
            // For x-www-form-urlencoded content type
            $requestData['form_params'] = $data;
            $requestData['headers'] = array_merge($this->commonHeaders, $headers,
                ['Content-Type' => 'application/x-www-form-urlencoded']);
        } else {
            // Invalid content type
            throw new \InvalidArgumentException('Invalid content type specified.');
        }

        return $this->request('POST', $url, $requestData);
    }

    public function put($url, $data = [], $headers = [])
    {
        return $this->request('PUT', $url, [
            'form_params' => $data,
            'headers' => array_merge($this->commonHeaders, $headers),
        ]);
    }

    public function delete($url, $headers = [])
    {
        return $this->request('DELETE', $url, ['headers' => array_merge($this->commonHeaders, $headers)]);
    }

    protected function request($method, $url, $options = [])
    {
        try {
            $response = $this->client->request($method, $url, $options);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            // Handle Guzzle RequestException
            $response = $e->getResponse();

            return [
                'success' => false,
                'message' => $response ? $response->getBody()->getContents() : $e->getMessage(),
            ];
        } catch (\Exception $e) {
            // Handle other exceptions (e.g., connection error, timeout, etc.)
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
