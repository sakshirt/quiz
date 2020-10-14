<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stripe extends  Public_Controller{ //CI_Controller

    function __construct() {
    	
        parent::__construct();
        
        $this->load->model('Payment_model');
        $this->stripe_key  =  array(
			  "secret_key"      => $this->settings->stripe_secret_key,
			  "publishable_key" => $this->settings->stripe_key
			);

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

        $this->add_js_theme('stripepayment.js');
        $this->set_title(sprintf(lang('home'), $this->settings->site_name));
        $content_data = array('Page_message' => lang('welcome_to_online_quiz'), 'page_title' => lang('home'),'stripe_key' => $this->stripe_key,'quiz_id' => $quiz_id, 'quiz_data' => $quiz_data);

        $data = $this->includes;
        $data['stripe_key'] = $this->stripe_key;

        $data['content'] = $this->load->view('product_form', $content_data, TRUE);
        
        $this->load->view($this->template, $data);
	}

	public function check($quiz_id) 
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

		//check whether stripe token is not empty
		if(!empty($_POST['stripeToken']))
		{

			$user_id = isset($this->user['id']) ? $this->user['id'] : 0;
			//get token, card and user info from the form
			$token  = $_POST['stripeToken'];
			$name = $_POST['name'];
			$email = $_POST['email'];
			$card_num = $_POST['card_num'];
			$card_cvc = $_POST['cvc'];
			$card_exp_month = $_POST['exp_month'];
			$card_exp_year = $_POST['exp_year'];
			
			//include Stripe PHP library
			require_once APPPATH."third_party/stripe/init.php";
			
			
			
			\Stripe\Stripe::setApiKey($this->stripe_key['secret_key']);

			
			//add customer to stripe


			try 
			{ 
 				$customer = \Stripe\Customer::create(array(
					    'name' => $name,
					    'description' => $quiz_data->title.' (Stripe Payment)',
					    'email' => $email,
					    'source' => $token,
					    "address" => [
								    'line1' => 'Dummy line',
								    'postal_code' => '342021',
								    'city' => 'Pink City',
								    'state' => 'CA',
								    'country' => 'US',
								  ],));
			}   
			catch(Exception $e) 
			{
  				//alert the user.
  			    $this->session->set_flashdata('error', 'Invalid Try');
            	redirect(base_url());
			}



			//item information
			$itemName = $quiz_data->title;
			$itemNumber = $quiz_data->id;
			$itemPrice = $quiz_data->price*100;
			$currency = $this->settings->paid_currency;
			$orderID = $quiz_data->id;
			

			//charge a credit or a debit card
			$charge = \Stripe\Charge::create(array(
				'customer' => $customer->id,
				'amount'   => $itemPrice,
				'currency' => $currency,
				'description' => $itemNumber,
				// 'address'	=> ["city" => 'jodhpur', "country" => 'india', "line1" => 'Line 1', "line2" => "", "postal_code" => 342021, "state" => 'rajasthan'],
				'metadata' => array(
					'item_id' => $itemNumber
				)
			));
			
			//retrieve charge details
			$chargeJson = $charge->jsonSerialize();

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

			//check whether the charge is successful
			if($chargeJson['amount_refunded'] == 0 && empty($chargeJson['failure_code']) && $chargeJson['paid'] == 1 && $chargeJson['captured'] == 1)
			{
				//order details 
				$amount = $chargeJson['amount'];
				$balance_transaction = $chargeJson['balance_transaction'];
				$currency = $chargeJson['currency'];
				$status = $chargeJson['status'];
				$date = date("Y-m-d H:i:s");
			
				
				//insert tansaction data into the database
				$dataDB = array(
					'user_id' => $user_id,
					'quiz_id' => $quiz_id,
					'name' => $name,
					'email' => $email, 
					'customer_id' => $customer->id, 
					'item_name' => $itemName, 
					 
					'item_price' => $quiz_data->price, 
					'item_price_currency' => $currency, 
					'txn_id' => $balance_transaction, 
					'payment_status' => $status,
					'created' => $date,
					'modified' => $date,
					'payment_gateway' => 'stripe',
					'invoice_no' => $invoice_no,
				);

				$inserted_id = $this->Payment_model->insert_stripe_detail($dataDB);
				
				if($inserted_id && $status == 'succeeded')
				{
					$payment_id = $inserted_id;
					return redirect(base_url("stripe/quiz-pay/payment-success/$quiz_id/$payment_id"));
				}
				else
				{
					
					$this->session->set_flashdata('error', 'Transaction has been failed');
					return redirect(base_url("quiz/payment-fail/$quiz_id"));
				}
			}
			else
			{
				
				$statusMsg = "";

  			    $this->session->set_flashdata('error', 'Invalid Payment Details Plz Try Again Later');
				return redirect(base_url("quiz/payment-fail/$quiz_id"));

			}
		}
		else
		{
  			    $this->session->set_flashdata('error', 'Some thing Went Wrong Plz Check Your Network Connection And Try AGain Later');
            	return redirect(base_url());
		}
	}

	public function payment_success($quiz_id, $payment_id)
	{

		$quiz_data =  $this->Payment_model->get_paid_quiz_by_id($quiz_id);
		if(empty($quiz_data))
		{
			return redirect(base_url('404_override'));
		}

		$payment_data =  $this->Payment_model->get_payment_data_by_id($payment_id);
		if(empty($payment_data))
		{
			return redirect(base_url('404_override'));
		}

        $this->set_title(sprintf(lang('home'), $this->settings->site_name));
        $content_data = array('Page_message' => lang('welcome_to_online_quiz'), 'page_title' => lang('home'), 'payment_id' => $payment_id, 'quiz_data' => $quiz_data, 'payment_data' => $payment_data, 'quiz_id' => $quiz_id);
        $data = $this->includes;
        $data['content'] = $this->load->view('payment_success', $content_data, TRUE);
        
        $this->load->view($this->template, $data);
		
	}

	public function payment_error($quiz_id)
	{

		$quiz_data =  $this->Payment_model->get_paid_quiz_by_id($quiz_id);
		if(empty($quiz_data))
		{
			return redirect(base_url('404_override'));
		}

        $this->set_title(sprintf(lang('home'), $this->settings->site_name));
        $content_data = array('Page_message' => lang('welcome_to_online_quiz'), 'page_title' => lang('home'));
        $data = $this->includes;
        $data['content'] = $this->load->view('payment_error', $content_data, TRUE);
        
        $this->load->view($this->template, $data);

		
	}

	public function help()
	{

        $this->set_title(sprintf(lang('home'), $this->settings->site_name));
        $content_data = array('Page_message' => lang('welcome_to_online_quiz'), 'page_title' => lang('home'));
        $data = $this->includes;
        $data['content'] = $this->load->view('help', $content_data, TRUE);
        
        $this->load->view($this->template, $data);

		
	}
}