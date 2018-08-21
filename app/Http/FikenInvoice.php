<?php
namespace App\Http;
use App\Invoice;
set_time_limit(300);


// Demo: fiken-demo-nordisk-og-tidlig-rytme-enk 
// Forfatterskolen: forfatterskolen-as

class FikenInvoice
{
	protected $username = "cleidoscope@gmail.com";
	protected $password = "moonfang";

	protected $fiken_contacts;
	protected $fiken_document_sending_service;
	protected $fiken_create_invoice_service;
	protected $fiken_bank_account;
	protected $fiken_product;

	
	protected $headers = [];

	public $invoiceID;

	public function __construct()
	{
		$fiken_company = "https://fiken.no/api/v1/companies/forfatterskolen-as";
		// Demo: fiken-demo-nordisk-og-tidlig-rytme-enk 
		// Forfatterskolen: forfatterskolen-as
        // DemoAS: fiken-demo-glede-og-bil-as2

		$this->fiken_contacts = $fiken_company."/contacts";
		$this->fiken_document_sending_service = $fiken_company."/document-sending-service";
		$this->fiken_create_invoice_service = $fiken_company."/create-invoice-service";
		$this->fiken_bank_account = $fiken_company."/bank-accounts/55204077"; // Demo: 313581398  Forfatterskolen: 55204077 DemoAS: 279632077
		$this->fiken_product = $fiken_company."/products/";

		$this->headers[] = 'Accept: application/hal+json, application/vnd.error+json';
		$this->headers[] = 'Content-Type: application/hal+json';
	}






	public function create_invoice($post_fields)
	{
		$customer = $this->customer($post_fields);
		$fields = [
			'issueDate' => date('Y-m-d'), 
			'dueDate' => $post_fields['dueDate'],
			'lines' => [[
				'unitNetAmount' => $post_fields['netAmount'],
				'description'=> $post_fields['description'],
				'productUrl' => $this->fiken_product . $post_fields['productID'],
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
		curl_close($ch);

		//print_r($data);

		$headers = $this->get_headers_from_curl_response($data);
		$location = $headers['Location'];
		$fiken_url = $this->get_weblink_from_api($location);
		$fiken_url = $fiken_url->_links->alternate->href;
		$pdf_url = $this->get_pdf_url($location);

		if(!empty($fiken_url)) :
			$invoice = new Invoice;
			$invoice->user_id = $post_fields['user_id'];
			$invoice->fiken_url = $fiken_url;
			$invoice->pdf_url = $pdf_url;
			$invoice->save();
		endif;
		
		$this->send_invoice($location);
		$this->invoiceID = $invoice->id;
	}




	public function get_weblink_from_api($api)
	{
        $ch = curl_init($api); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);;
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
		$data = curl_exec($ch);
		curl_close($ch);
		$data = json_decode($data);
		return $data;
	}


	public function get_pdf_url($url)
	{
        $ch = curl_init($url); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);;
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
		$data = curl_exec($ch);
		curl_close($ch);
		$data = json_decode($data);
		$sale = $data->sale;

		$ch = curl_init($sale."/attachments"); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);;
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
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        $data = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($data);
        return $data->invoiceNumber;
	}






	public function get_headers_from_curl_response($response)
	{
	    $headers = [];

	    $header_text = substr($response, 0, strpos($response, "\r\n\r\n"));

	    foreach (explode("\r\n", $header_text) as $i => $line)
	        if ($i === 0)
	            $headers['http_code'] = $line;
	        else
	        {
	            list ($key, $value) = explode(': ', $line);

	            $headers[$key] = $value;
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
			'name' => $data['first_name'] . ' ' . $data['last_name'], 
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
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);;
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
		$data = curl_exec($ch);
		curl_close($ch);
		$data = json_decode($data);
		$contacts = $data->_embedded->{'https://fiken.no/api/v1/rel/contacts'};
		$item = null;
		foreach($contacts as $struct) {
		    if ( isset($struct->email) && $fields['email'] == $struct->email ) :
		        $item = $struct;
		        break;
		    endif;
		}
		if( $item ) :
			return [
				'href' => $item->_links->self->href,
				'name' => $item->name,
			];
		else :
			return $this->create_customer($fields);
		endif;

	}







	public function create_customer($fields)
	{
		$field_string = json_encode($fields, true);
		$ch = curl_init($this->fiken_contacts); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $field_string);
		curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);;
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
		$data = curl_exec($ch);
		curl_close($ch);
		$data = json_decode($data);
		return $this->get_customer($fields);
	}
}



?>