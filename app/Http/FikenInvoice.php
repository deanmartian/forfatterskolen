<?php

namespace App\Http;

use App\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

set_time_limit(300);

// Demo: fiken-demo-nordisk-og-tidlig-rytme-enk
// Forfatterskolen: forfatterskolen-as

class FikenInvoice
{
    protected $username = 'cleidoscope@gmail.com';

    protected $password = 'moonfang';

    protected $fiken_contacts;

    protected $fiken_document_sending_service;

    protected $fiken_create_invoice_service;

    protected $fiken_bank_account;

    protected $fiken_product;

    protected $fiken_sales;

    protected $headers = [];

    public $invoiceID;

    public $invoice_number;

    public $fikenUrl;

    protected $mobile_number;

    public $fiken_invoice_id;

    protected $fiken_bank_account_code;

    public function __construct()
    {
        $fiken_company = 'https://api.fiken.no/api/v2/companies/forfatterskolen-as';
        // Demo: fiken-demo-nordisk-og-tidlig-rytme-enk
        // Forfatterskolen: forfatterskolen-as
        // DemoAS: fiken-demo-glede-og-bil-as2

        $this->fiken_contacts = $fiken_company.'/contacts';
        $this->fiken_document_sending_service = $fiken_company.'/document-sending-service';
        $this->fiken_create_invoice_service = $fiken_company.'/invoices';
        // Demo: 313581398  Forfatterskolen: 55204077 DemoAS: 279632077
        $this->fiken_bank_account = $fiken_company.'/bank-accounts/55204077';
        $this->fiken_product = $fiken_company.'/products/';
        $this->fiken_sales = $fiken_company.'/sales/';

        $this->headers[] = 'Accept: application/json';
        $this->headers[] = 'Authorization: Bearer '.config('services.fiken.personal_api_key');
        $this->headers[] = 'Content-Type: Application/json';
        // Accept: application/hal+json, application/vnd.error+json
        // $this->headers[] = 'Content-Type: application/hal+json';

        // Forfatterskolen: DemoAS: 1920:10001
        $this->fiken_bank_account_code = '1920:10001';

        $this->username = config('services.fiken.username');
        $this->password = config('services.fiken.password');
    }

    public function create_invoice($post_fields, $has_vat = false)
    {
        Log::info('inside create invoice');
        $customer = $this->customer($post_fields);
        Log::info('after getting customer');

        // if an issue date is set and not empty then use it else use today
        $fields = [
            'issueDate' => isset($post_fields['issueDate']) && $post_fields['issueDate']
                ? Carbon::parse($post_fields['issueDate'])->format('Y-m-d') : date('Y-m-d'),
            'dueDate' => $post_fields['dueDate'],
            'lines' => [[
                'net' => $post_fields['netAmount'],
                'description' => $post_fields['description'],
                'productId' => $post_fields['productID'], //279633092 //9288957242
                'comment' => $post_fields['comment'],
                'quantity' => 1,
                'vatType' => 'OUTSIDE',
                'unitPrice' => $post_fields['netAmount'],
            ]],
            'customerId' => $customer->contactId,
            'bankAccountCode' => $this->fiken_bank_account_code,
            'cash' => false,
            'currency' => 'NOK',
        ];

        if ($has_vat) {
            $fields = [
                'issueDate' => isset($post_fields['issueDate']) && $post_fields['issueDate']
                    ? Carbon::parse($post_fields['issueDate'])->format('Y-m-d') : date('Y-m-d'),
                'dueDate' => $post_fields['dueDate'],
                'lines' => [[
                    'net' => $post_fields['netAmount'],
                    'vat' => $post_fields['vat'],
                    'gross' => $post_fields['netAmount'] + $post_fields['vat'],
                    'vatType' => 'HIGH',
                    'vatInPercent' => 25,
                    'description' => $post_fields['description'],
                    'productId' => $post_fields['productID'],
                    'comment' => $post_fields['comment'],
                    'quantity' => 1,
                    'unitPrice' => $post_fields['netAmount'],
                ]],
                'customerId' => $customer->contactId,
                'bankAccountCode' => $this->fiken_bank_account_code,
                'cash' => false,
                'currency' => 'NOK',
            ];
        }

        $field_string = json_encode($fields, true);
        Log::info($field_string);
        $ch = curl_init($this->fiken_create_invoice_service);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $field_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        $data = curl_exec($ch);
        Log::info(\GuzzleHttp\json_encode($data));
        // get the http code response
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        Log::info('http code = '.$http_code);
        if (! in_array($http_code, [200, 201])) { // 200 - get success, 201 - post success
            abort($http_code); // display error page instead of the Whoops page
        }

        curl_close($ch);

        // print_r($data);

        $headers = $this->get_headers_from_curl_response($data);
        $fiken_url = isset($headers['location']) ? $headers['location'] : $headers['Location'];
        $this->fikenUrl = $fiken_url;
        $fikenInvoice = $this->get_invoice_data($fiken_url);
        $pdf_url = $fikenInvoice->invoicePdf->downloadUrl;

        if (! empty($fiken_url)) {
            $invoice = new Invoice;
            $invoice->user_id = $post_fields['user_id'];
            $invoice->fiken_url = $fiken_url;
            $invoice->pdf_url = $pdf_url;
            $invoice->gross = $fikenInvoice->gross;
            $invoice->kid_number = isset($fikenInvoice->kid) ? $fikenInvoice->kid : null;
            $invoice->invoice_number = isset($fikenInvoice->invoiceNumber) ? $fikenInvoice->invoiceNumber : null;
            $invoice->fiken_invoice_id = $fikenInvoice->invoiceId;
            $invoice->fiken_issueDate = isset($fikenInvoice->issueDate) ? $fikenInvoice->issueDate : null;
            $invoice->fiken_dueDate = isset($fikenInvoice->dueDate) ? $fikenInvoice->dueDate : null;
            $invoice->fiken_balance = $fikenInvoice->sale->outstandingBalance / 100; // $fikenInvoice->gross/100;
            $invoice->save();
            $this->invoiceID = $invoice->id;
            $this->invoice_number = $invoice->invoice_number;
            $this->fiken_invoice_id = $invoice->fiken_invoice_id;
        }

        if (isset($post_fields['payment_mode']) && $post_fields['payment_mode'] === 'Faktura'
            && (! isset($post_fields['index']) || $post_fields['index'] === 1)) {
            $this->send_invoice($fikenInvoice);
        }
    }

    public function get_weblink_from_api($api)
    {
        $ch = curl_init($api);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        $data = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($data);

        return $data;
    }

    public function getSales()
    {
        $params = 'date=2019-01-31';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->fiken_sales.'?'.$params); // Url together with parameters
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Return data instead printing directly in Browser
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_HEADER, 1);

        $response = curl_exec($ch);
        // Then, after your curl_exec call:
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);

        curl_close($ch);

        /* return $this->headers; */
        return json_decode($body);
    }

    public function get_pdf_url($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        $data = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($data);
        $sale = $data->sale;

        $ch = curl_init($sale.'/attachments');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        $data = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($data);
        $pdf_url = $data->_embedded->{'https://fiken.no/api/v1/rel/attachments'}[0]->downloadUrl;

        return $pdf_url;
    }

    public function get_invoice_number($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        $data = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($data);

        return $data->invoiceNumber;
    }

    /**
     * Get invoice date to be parsed and save on db to limit the CRON
     *
     * @return mixed
     */
    public function get_invoice_data($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        $data = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($data);

        return $data;
    }

    public function get_headers_from_curl_response($response)
    {
        $headers = [];

        $header_text = substr($response, 0, strpos($response, "\r\n\r\n"));

        foreach (explode("\r\n", $header_text) as $i => $line) {
            if ($i === 0) {
                $headers['http_code'] = $line;
            } else {
                [$key, $value] = explode(': ', $line);

                $headers[$key] = $value;
            }
        }

        return $headers;
    }

    public function send_invoice($invoice)
    {
        Log::info('inside send invoice');
        Log::info(json_encode($invoice));

        if (property_exists($invoice->customer, 'email')) {
            $email = $invoice->customer->email;
        } else {
            $email = $invoice->sale->customer->contactPerson[0]->email;
        }

        $fields = [
            'invoiceId' => $invoice->invoiceId,
            'method' => ['email'],
            'includeDocumentAttachments' => true,
            'recipientName' => $invoice->customer->name,
            'recipientEmail' => $email,
        ];
        $field_string = json_encode($fields, true);
        $this->headers[] = 'Content-Type: Application/json';
        $ch = curl_init($this->fiken_create_invoice_service.'/send');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $field_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        $data = curl_exec($ch);
        curl_close($ch);
    }

    public function customer($data)
    {
        $fields = [
            'name' => $data['first_name'].' '.$data['last_name'],
            'email' => $data['email'],
            'phoneNumber' => $data['telephone'],
            'address' => [
                'streetAddress' => $data['address'],
                'city' => $data['postalPlace'],
                'postCode' => $data['postalCode'],
                'country' => 'Norge',
            ],
            'contactPerson' => [
                [
                    'name' => $data['first_name'].' '.$data['last_name'],
                    'email' => $data['email'],
                    'phoneNumber' => $data['telephone'],
                    'address' => [
                        'streetAddress' => $data['address'],
                        'city' => $data['postalPlace'],
                        'postCode' => $data['postalCode'],
                        'country' => 'Norge',
                    ],
                ],
            ],
            'customer' => true,
        ];

        return $this->get_customer($fields);
    }

    public function get_customer($fields)
    {
        Log::info('inside get customer');
        $ch = curl_init($this->fiken_contacts.'?pageSize=1&email='.$fields['email']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        /*curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);;*/
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        $data = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $data = json_decode($data);
        // get the http code response

        // $contacts = $data->_embedded->{'https://fiken.no/api/v1/rel/contacts'}; - this is for v1 of fiken
        $item = $data;
        Log::info(json_encode($item));
        if ($item) {
            Log::info('inside if in get customer');
            $item = $item[0];
            Log::info(json_encode($item));

            $updateData['name'] = $item->name;

            if (property_exists($item, 'email')) {
                $email = $item->email;
            } else {
                $email = $item->contactPerson[0]->email;
            }

            $updateData['email'] = $email;
            $updateData['address'] = [
                'streetAddress' => $fields['address']['streetAddress'],
                'city' => $fields['address']['city'],
                'postCode' => $fields['address']['postCode'],
                'country' => $fields['address']['country'],
            ];
            $updateData['contactPerson'] = [
                [
                    'streetAddress' => $fields['address']['streetAddress'],
                    'city' => $fields['address']['city'],
                    'postCode' => $fields['address']['postCode'],
                    'country' => $fields['address']['country'],
                ],
            ];

            $this->update_customer($item->contactId, $updateData);

            return $item;
        } else {
            Log::info('inside else in get customer');

            return $this->create_customer($fields);
        }

    }

    public function create_customer($fields)
    {
        $field_string = json_encode($fields, true);
        $this->headers[] = 'Content-Type: Application/json';
        $ch = curl_init($this->fiken_contacts);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $field_string);
        /*curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);;*/
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        $data = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($data);
        Log::info('inside create customer');
        Log::info(json_encode($data));

        return $this->get_customer($fields);
    }

    /**
     * Update the customer info
     * url is the url of the contact in fiken
     *
     * @param  $url
     * @return mixed
     */
    public function update_customer($contact_id, $fields)
    {
        $ch = curl_init($this->fiken_contacts.'/'.$contact_id);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        /*curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);;*/
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        $data = curl_exec($ch);
        curl_close($ch);
        Log::info('inside update customer');
        Log::info(json_encode($data));

        return $data;
    }

    public function setMobileNumber($mobile_number)
    {
        $this->mobile_number = $mobile_number;
    }

    public function setFikenInvoiceId($fiken_invoice_id)
    {
        $this->fiken_invoice_id = $fiken_invoice_id;
    }

    public function vippsEFaktura($user)
    {

        $fields = [
            'method' => ['efaktura'], // vipps
            'includeDocumentAttachments' => false,
            // "mobileNumber" => $this->mobile_number,
            'recipientName' => $user->full_name,
            'recipientEmail' => $user->email,
            'invoiceId' => $this->fiken_invoice_id,
        ];

        $field_string = json_encode($fields, true);
        $ch = curl_init($this->fiken_create_invoice_service.'/send');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $field_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        $data = curl_exec($ch);
        $decoded_response = json_decode($data);

        $response = [
            'code' => 200,
        ];

        // get the http code response
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (! in_array($http_code, [200, 201])) { // 200 - get success, 201 - post success
            $response = [
                'code' => $http_code,
                'message' => is_array($decoded_response) ? $decoded_response[0]->message
                    : $decoded_response->error_description,
            ];
        }

        curl_close($ch);

        return $response;
    }
}
