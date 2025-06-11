<?php

namespace App\Http;

use App\Invoice;
use Carbon\Carbon;

set_time_limit(300);

// Demo: fiken-demo-nordisk-og-tidlig-rytme-enk
// Forfatterskolen: forfatterskolen-as

class FikenInvoiceOrig
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

    public function __construct()
    {
        $fiken_company = 'https://fiken.no/api/v1/companies/forfatterskolen-as';
        // Demo: fiken-demo-nordisk-og-tidlig-rytme-enk
        // Forfatterskolen: forfatterskolen-as
        // DemoAS: fiken-demo-glede-og-bil-as2

        $this->fiken_contacts = $fiken_company.'/contacts';
        $this->fiken_document_sending_service = $fiken_company.'/document-sending-service';
        $this->fiken_create_invoice_service = $fiken_company.'/create-invoice-service';
        $this->fiken_bank_account = $fiken_company.'/bank-accounts/55204077'; // Demo: 313581398  Forfatterskolen: 55204077 DemoAS: 279632077
        $this->fiken_product = $fiken_company.'/products/';
        $this->fiken_sales = $fiken_company.'/sales/';

        $this->headers[] = 'Accept: application/hal+json, application/vnd.error+json';
        $this->headers[] = 'Content-Type: application/hal+json';
    }

    public function create_invoice($post_fields)
    {
        $customer = $this->customer($post_fields);
        // if an issue date is set and not empty then use it else use today
        $fields = [
            'issueDate' => isset($post_fields['issueDate']) && $post_fields['issueDate']
                ? Carbon::parse($post_fields['issueDate'])->format('Y-m-d') : date('Y-m-d'),
            'dueDate' => $post_fields['dueDate'],
            'lines' => [[
                'unitNetAmount' => $post_fields['netAmount'],
                'description' => $post_fields['description'],
                'productUrl' => $this->fiken_product.$post_fields['productID'],
                'comment' => $post_fields['comment'],
            ]],
            'customer' => [
                'url' => $customer['href'],
                'name' => $customer['name'],
            ],
            'bankAccountUrl' => $this->fiken_bank_account,
        ];
        $field_string = json_encode($fields, true);

        $ch = curl_init($this->fiken_create_invoice_service);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $field_string);
        curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        $data = curl_exec($ch);

        // get the http code response
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (! in_array($http_code, [200, 201])) { // 200 - get success, 201 - post success
            abort($http_code); // display error page instead of the Whoops page
        }

        curl_close($ch);

        // print_r($data);

        $headers = $this->get_headers_from_curl_response($data);
        $location = isset($headers['location']) ? $headers['location'] : $headers['Location'];
        $fiken_url = $this->get_weblink_from_api($location);
        $fiken_url = $fiken_url->_links->alternate->href;
        $pdf_url = $this->get_pdf_url($location);
        $fikenInvoice = $this->get_invoice_data($location);

        if (! empty($fiken_url)) {
            $invoice = new Invoice;
            $invoice->user_id = $post_fields['user_id'];
            $invoice->fiken_url = $fiken_url;
            $invoice->pdf_url = $pdf_url;
            $invoice->gross = $fikenInvoice->gross;
            $invoice->kid_number = isset($fikenInvoice->kid) ? $fikenInvoice->kid : null;
            $invoice->invoice_number = isset($fikenInvoice->invoiceNumber) ? $fikenInvoice->invoiceNumber : null;
            $invoice->fiken_issueDate = isset($fikenInvoice->issueDate) ? $fikenInvoice->issueDate : null;
            $invoice->fiken_dueDate = isset($fikenInvoice->dueDate) ? $fikenInvoice->dueDate : null;
            $invoice->fiken_balance = $fikenInvoice->gross / 100;
            $invoice->save();
        }

        if (isset($post_fields['payment_mode']) && $post_fields['payment_mode'] === 'Faktura') {
            $this->send_invoice($location);
        }

        $this->invoiceID = $invoice->id;
        $this->invoice_number = $invoice->invoice_number;
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

    public function send_invoice($location)
    {
        $fields = [
            'resource' => $location,
            'method' => 'auto',
        ];
        $field_string = json_encode($fields, true);

        $ch = curl_init($this->fiken_document_sending_service);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $field_string);
        curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        $data = curl_exec($ch);
    }

    public function customer($data)
    {
        $fields = [
            'name' => $data['first_name'].' '.$data['last_name'],
            'email' => $data['email'],
            'phoneNumber' => $data['telephone'],
            'address' => [
                'address1' => $data['address'],
                'postalPlace' => $data['postalPlace'],
                'postalCode' => $data['postalCode'],
            ],
            'customer' => true,
        ];

        return $this->get_customer($fields);
    }

    public function get_customer($fields)
    {
        $ch = curl_init($this->fiken_contacts);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        $data = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($data);
        $contacts = $data->_embedded->{'https://fiken.no/api/v1/rel/contacts'};
        $item = null;
        foreach ($contacts as $struct) {
            if (isset($struct->email) && $fields['email'] == $struct->email) {
                $item = $struct;
                break;
            }
        }
        if ($item) {
            $updateData['name'] = $item->name;
            $updateData['email'] = $item->email;
            $updateData['address'] = [
                'address1' => $fields['address']['address1'],
                'postalPlace' => $fields['address']['postalPlace'],
                'postalCode' => $fields['address']['postalCode'],
            ];
            $this->update_customer($item->_links->self->href, $updateData);

            return [
                'href' => $item->_links->self->href,
                'name' => $item->name,
            ];
        } else {
            return $this->create_customer($fields);
        }

    }

    public function create_customer($fields)
    {
        $field_string = json_encode($fields, true);
        $ch = curl_init($this->fiken_contacts);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $field_string);
        curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        $data = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($data);

        return $this->get_customer($fields);
    }

    /**
     * Update the customer info
     * url is the url of the contact in fiken
     *
     * @return mixed
     */
    public function update_customer($url, $fields)
    {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }
}
