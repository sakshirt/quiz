<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Razorpay\Api\Api;

class Razorpay extends  Public_Controller{ //CI_Controller

	function __construct() {
		    	
        parent::__construct();
        
        $this->load->model('Payment_model');

		if(empty($this->user['id']))
		{
			$this->session->set_flashdata('error', 'Plz Login First');
			return redirect(base_url("login"));
		}
	}

    public function index($quiz_id)
	{

		$quiz_last_paymetn_status = $this->Payment_model->get_quiz_last_paymetn_status($quiz_id);
		if($quiz_last_paymetn_status)
		{
			return redirect(base_url("instruction/$quiz_id"));
		}

		$quiz_data =  $this->Payment_model->get_paid_quiz_by_id($quiz_id);

		if(empty($quiz_data))
		{
			return redirect(base_url('404_override'));
		}

		$razor_key_id = $this->settings->razorpay_key;
		$razor_secret = $this->settings->razorpay_secret_key;
		$api = new Api($razor_key_id, $razor_secret);

		$orderData = [
		    'receipt'         => time(), 
		    'amount'          => $quiz_data->price * 100, // 2000 rupees in paise
		    'currency'        => $this->settings->paid_currency,
		    'payment_capture' => 1 // auto capture
		];

		$razorpayOrder = $api->order->create($orderData);
		
		
		$get_max_invoice = $this->Payment_model->find_max_invoice_no();

	    if($get_max_invoice)
	    {
	    	$invoice_no = $get_max_invoice[0]->invoice_no+1;
	    	if($invoice_no < get_admin_setting('invoice_start_number'))
	    	{
	    		$invoice_no = get_admin_setting('invoice_start_number')+1;
	    	}
	    	else
	    	{
	    		$invoice_no = $invoice_no;
	    	}
	    	
	    }
	    else
	    {
	    	$invoice_no = get_admin_setting('invoice_start_number')+1;
	    }

		$razorpayOrderId = $razorpayOrder['id'];
		// insert into DB and customer_id = $razorpayOrderId 
		if($razorpayOrderId)
	    {
		    $payment_data = array();
		    $payment_data['user_id'] = $this->user['id'];
		    $payment_data['quiz_id'] = $quiz_id;
		    $payment_data['name'] = $this->user['first_name'].' '.$this->user['last_name'];
		    $payment_data['email'] = $this->user['email'];
		    $payment_data['item_name'] = $quiz_data->title;
		    $payment_data['item_price'] = $quiz_data->price;
		    $payment_data['item_price_currency'] = $this->settings->paid_currency;
		    $payment_data['customer_id'] = $razorpayOrderId;
		    $payment_data['payment_status'] = 'pending';
		    $payment_data['created'] = date("Y-m-d H:i:s");
		    $payment_data['modified'] = date("Y-m-d H:i:s");
		    $payment_data['payment_gateway'] = 'razorpay';
		    $payment_data['invoice_no'] = $invoice_no;

		    $payment_id = $this->Payment_model->insert_paypal_detail($payment_data);
		    return redirect(base_url('razorpay/quiz-payment/'.$payment_id));	   
		}

	}

	public function quiz_payment($payment_id)
	{
		$payment_data =  $this->Payment_model->get_payment_data_by_paymentid($payment_id);
		$quiz_data_by_payment = $this->Payment_model->get_paid_quiz_by_id($payment_data->quiz_id);
		
		if(isset($payment_data) && !empty($payment_data))
		{


			$razor_key_id = $this->settings->razorpay_key;
			$razor_secret = $this->settings->razorpay_secret_key;

			$form_data = [
			    "key"               => $razor_key_id,
			    "amount"            => $payment_data->item_price,
			    "name"              => $quiz_data_by_payment->title,
			    "description"       => $quiz_data_by_payment->title,
			    "image"             => base_url('/assets/images/logo/'.$this->settings->site_logo),
			    "prefill"           => [
			    "name"              => $this->user['first_name'].' '.$this->user['last_name'],
			    "email"             => $this->user['email'],
			    "contact"           => "",
			    ],
			    "notes"             => [
			    "address"           => "Hello World",
			    "merchant_order_id" => $payment_id,
			    ],
			    "theme"             => [
			    "color"             => "#7f67bb"
			    ],
			    "order_id"          => $payment_data->customer_id,
			];

			// $displayCurrency = "";
			// if ($displayCurrency !== 'INR')
			// {
			//     $form_data['display_currency']  = $displayCurrency;
			//     $form_data['display_amount']    = $orderData['amount'];
			// }

			// $json = json_encode($data);

			$content_data = array('data'=>$form_data, 'payment_id'=>$payment_id,'quiz_data' => $quiz_data_by_payment,'Page_message' => lang('welcome_to_online_quiz'), 'page_title' => lang('home'),);

	        $data = $this->includes;
			$data['content'] = $this->load->view('razor_payment', $content_data, TRUE);
	        
	        $this->load->view($this->template, $data);

		}
		else
		{
			$this->session->set_flashdata('error', lang('payment_not_done'));
			return redirect($_SERVER['HTTP_REFERER']);
		}
	}

	function verify($payment_id)
	{
		$payment_data =  $this->Payment_model->get_paypal_payment_by_id($payment_id);

		$quiz_data =  $this->Payment_model->get_paid_quiz_by_id($payment_data->quiz_id);

		if(empty($quiz_data))
		{
			return redirect(base_url('404_override'));
		}

		$success = true;

		$error = "Payment Failed";

		if (empty($_POST['razorpay_payment_id']) === false)
		{

			$keyId = $this->settings->razorpay_key;
			$keySecret = $this->settings->razorpay_secret_key;

		    $api = new Api($keyId, $keySecret);

		    try
		    {
		        // Please note that the razorpay order ID must
		        // come from a trusted source (session here, but
		        // could be database or something else)
		        $attributes = array(
		            'razorpay_order_id' => $payment_data->customer_id,
		            'razorpay_payment_id' => $_POST['razorpay_payment_id'],
		            'razorpay_signature' => $_POST['razorpay_signature']
		        );
		        

		        $api->utility->verifyPaymentSignature($attributes);
		    }
		    catch(SignatureVerificationError $e)
		    {
		        $success = false;
		        $error = 'Razorpay Error : ' . $e->getMessage();
		    }
		}

		$content_data = array('Page_message' => lang('welcome_to_online_quiz'), 'page_title' => lang('home'), 'payment_id' => $payment_id, 'quiz_data' => $quiz_data, 'payment_data' => $payment_data, 'quiz_id' => $quiz_data->id);

		 $data = $this->includes;
		if ($success === true)
		{
			// update payment table and set txn_id and payment_status
			$update_razorpay_payment = $this->Payment_model->update_razorpay_payment_detail($payment_id,$attributes['razorpay_payment_id']);

	        $data['content'] = $this->load->view('payment_success', $content_data, TRUE);
		}
		else
		{
		    $data['content'] = $this->load->view('payment_error', $content_data, TRUE);
		}

		$this->load->view($this->template, $data);
	}
}