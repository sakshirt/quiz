<?php
class Payment extends Public_Controller { //CI_Controller 
	 
	public function __construct()
	{
		parent::__construct();
		$this->load->library("paypal");
        $this->load->model('Payment_model');
		$this->load->helper("url");

		if(empty($this->user['id']))
		{
			$this->session->set_flashdata('error', 'Plz Login First');
			return redirect(base_url("login"));
		}
	}


	public function payment_mode($quiz_id)
	{

		// $quiz_last_paymetn_status = $this->Payment_model->get_quiz_last_paypal_status($quiz_id);
		
		// if($quiz_last_paymetn_status)
		// {
		// 	return redirect(base_url("instruction/$quiz_id"));
		// }

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
		$payment_pending_status = $this->Payment_model->get_paymetn_status($quiz_id,$this->user['id']);
		

        $this->set_title(sprintf(lang('home'), $this->settings->site_name));

        $this->add_external_js(base_url("/assets/themes/admin/js/payment_custome_script.js"));

        $content_data = array('Page_message' => lang('welcome_to_online_quiz'), 'page_title' => lang('home'),'quiz_id' => $quiz_id, 'quiz_data' => $quiz_data,'payment_pending_status'=>$payment_pending_status,);

        $data = $this->includes;

        // $data['content'] = $this->load->view('index', $content_data, TRUE); 
        $data['content'] = $this->load->view('payment_method', $content_data, TRUE);
        
        $this->load->view($this->template, $data);

	}

	public function index($quiz_id)
	{

		// $quiz_last_paymetn_status = $this->Payment_model->get_quiz_last_paypal_status($quiz_id);
		// if($quiz_last_paymetn_status)
		// {
		// 	return redirect(base_url("instruction/$quiz_id"));
		// }

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

        $this->set_title(sprintf(lang('home'), $this->settings->site_name));
        $content_data = array('Page_message' => lang('welcome_to_online_quiz'), 'page_title' => lang('home'),'quiz_id' => $quiz_id, 'quiz_data' => $quiz_data);

        $data = $this->includes;

        // $data['content'] = $this->load->view('index', $content_data, TRUE);
        $data['content'] = $this->load->view('paypal_payment', $content_data, TRUE);
        
        $this->load->view($this->template, $data);
	}


	public function subscribe($quiz_id)
	{

		// $quiz_last_paymetn_status = $this->Payment_model->get_quiz_last_paypal_status($quiz_id);
		// if($quiz_last_paymetn_status)
		// {
		// 	return redirect(base_url("instruction/$quiz_id"));
		// }


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

		if ($_POST["plan_name"]) 
		{
			// $definition = "Regular Payments";
			$definition = $quiz_data->title;
			$type       = "REGULAR";
			$frequency  = "MONTH";
			$frequncy_interval = '1';
			$cycles = 0;
			$price = $quiz_data->price;


			$line1 = "Street - 1, Sector - 1";
			$city  = "Dhaka";
			$state = "Dhaka";
			$postalcode = "12345";
			$country = "AU";

			// $agreement_name = "Payment Agreement Name";
			// $agreement_description = "Payment Agreement Description";
			$agreement_name = $quiz_data->title;
			$agreement_description = $quiz_data->description;

			$return_url = base_url("paypal/quiz-pay/payment-success/$quiz_id");
			$cancel_url = base_url("paypal/quiz-pay/payment-fail/$quiz_id");
			
			$this->paypal->set_api_context();
			$this->paypal->set_plan( $_POST["plan_name"], $_POST["plan_description"], "INFINITE" );
			$this->paypal->set_billing_plan_definition( $definition, $type, $frequency, $frequncy_interval, $cycles, $price );
			$this->paypal->set_merchant_preferences($return_url, $cancel_url );
			$this->paypal->set_shipping_address( $line1, $city, $state, $postalcode, $country );
			$this->paypal->create_and_activate_billing_plan( $agreement_name, $agreement_description );

		}
		else
		{
			$this->session->set_flashdata('error', 'Someting Went Wrong !');
			return redirect(base_url("quiz-pay/payment-mode/$quiz_id"));
		}

	}


	public function create_payment($quiz_id)
	{
		
		// $quiz_last_paymetn_status = $this->Payment_model->get_quiz_last_paypal_status($quiz_id);
		// if($quiz_last_paymetn_status)
		// {
		// 	return redirect(base_url("instruction/$quiz_id"));
		// }

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




		$payment_method = "paypal";
		$return_url     = base_url("paypal/quiz-pay/pay-successfuly/$quiz_id");
		$cancel_url     = base_url("paypal/quiz-pay/payment-fail/$quiz_id");
		$total          = $quiz_data->price;
		$description    = $quiz_data->title;
		$intent         = "SALE";


		$this->paypal->set_api_context();
		$this->paypal->create_payment($payment_method, $return_url, $cancel_url, 
        $total, $description, $intent);

        return;

	}
	//After creating a payment successfully we will be redirected here
	

	public function cancel($quiz_id)
	{
		// $this->index($quiz_id);
		// return;

		$quiz_data =  $this->Payment_model->get_paid_quiz_by_id($quiz_id);
		if(empty($quiz_data))
		{
			return redirect(base_url('404_override'));
		}


		if ($_GET['token'] )
		{
		    $payment_exist = $this->Payment_model->check_payment_by_token($_GET['token']);
		    if(empty($payment_exist))
		    {

			    $payment_data = array();
			    $payment_data['user_id'] = $this->user['id'];
			    $payment_data['quiz_id'] = $quiz_id;
			    $payment_data['name'] = $this->user['first_name'].' '.$this->user['last_name'];
			    $payment_data['email'] = $this->user['email'];
			    $payment_data['item_price'] = $quiz_data->price;
			    $payment_data['item_price_currency'] = $this->settings->paid_currency;
			    $payment_data['token_no'] = $_GET['token'];
			    $payment_data['payment_status'] = 'fail';
			    $payment_data['created'] = date("Y-m-d H:i:s");

			    $payment_id = $this->Payment_model->insert_payment($payment_data);
			}
			else
			{
				$payment_id = $payment_exist->id;
			}

			return redirect(base_url("paypal/quiz-pay/payment-status/$quiz_id/$payment_id"));

		}
		else
		{
			$this->session->set_flashdata('error', 'Someting Went Wrong !');
			return redirect(base_url("quiz-pay/payment-mode/$quiz_id"));
		}

	}

	//After successfully create an agreement we will be redirected to this function
	public function success($quiz_id)
	{
			
		// $quiz_last_paymetn_status = $this->Payment_model->get_quiz_last_paypal_status($quiz_id);
		// if($quiz_last_paymetn_status)
		// {
		// 	return redirect(base_url("instruction/$quiz_id"));
		// }

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

		if ($_GET['token'] ) 
		{

		    $token = $_GET['token'];
		    $this->paypal->execute_agreement( $token );

		    $payment_exist = $this->Payment_model->check_payment_by_token($token);
		    if(empty($payment_exist))
		    {

			    $payment_data = array();
			    $payment_data['user_id'] = $this->user['id'];
			    $payment_data['quiz_id'] = $quiz_id;
			    $payment_data['name'] = $this->user['first_name'].' '.$this->user['last_name'];
			    $payment_data['email'] = $this->user['email'];
			    $payment_data['item_price'] = $quiz_data->price;
			    $payment_data['item_price_currency'] = $this->settings->paid_currency;
			    $payment_data['token_no'] = $token;
			    $payment_data['payment_status'] = 'succeeded';
			    $payment_data['created'] = date("Y-m-d H:i:s");
			    $payment_data['payment_gateway'] = 'paypal';

			    $payment_id = $this->Payment_model->insert_paypal_detailll($payment_data);
			}
			else
			{
				$payment_id = $payment_exist->id;
			}

			return redirect(base_url("paypal/quiz-pay/payment-status/$quiz_id/$payment_id"));


		   // $this->index($quiz_id);

		}
		else
		{
			$this->session->set_flashdata('error', 'Someting Went Wrong !');
			return redirect(base_url("quiz-pay/payment-mode/$quiz_id"));
		}

		return;

	}

	public function success_payment($quiz_id)
	{

		$quiz_data =  $this->Payment_model->get_paid_quiz_by_id($quiz_id);

		if(empty($quiz_data))
		{
			return redirect(base_url('404_override'));
		}

		// $quiz_last_paymetn_status = $this->Payment_model->get_quiz_last_paypal_status($quiz_id);

		// if($quiz_last_paymetn_status)
		// {
		// 	return redirect(base_url("instruction/$quiz_id"));
		// }

		$quiz_last_paymetn_status = $this->Payment_model->get_quiz_last_paymetn_status($quiz_id);

		if($quiz_last_paymetn_status)
		{
			return redirect(base_url("instruction/$quiz_id"));
		}

		if ( !empty( $_GET['paymentId'] ) && !empty( $_GET['PayerID'] ) ) 
		{

		    $this->paypal->execute_payment( $_GET['paymentId'], $_GET['PayerID'] );

		    $payment_exist = $this->Payment_model->check_payment_by_txn_id($_GET['paymentId']);

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

		    if(empty($payment_exist))
		    {

			    $payment_data = array();
			    $payment_data['user_id'] = $this->user['id'];
			    $payment_data['payer_id'] = $_GET['PayerID'];
			    $payment_data['quiz_id'] = $quiz_id;
			    $payment_data['name'] = $this->user['first_name'].' '.$this->user['last_name'];
			    $payment_data['email'] = $this->user['email'];
			    $payment_data['item_name'] = $quiz_data->title;
			    $payment_data['item_price'] = $quiz_data->price;
			    $payment_data['item_price_currency'] = $this->settings->paid_currency;
			    $payment_data['txn_id'] = $_GET['paymentId'];
			    $payment_data['payment_status'] = 'succeeded';
			    $payment_data['created'] = date("Y-m-d H:i:s");
			    $payment_data['modified'] = date("Y-m-d H:i:s");
			    $payment_data['payment_gateway'] = 'paypal';
			    $payment_data['invoice_no'] = $invoice_no;

			    $payment_id = $this->Payment_model->insert_paypal_detail($payment_data);
					   
			}
			else
			{
				$payment_id = $payment_exist->id;
			}

			return redirect(base_url("paypal/quiz-pay/payment-status/$quiz_id/$payment_id"));

		}
		else
		{
			$this->session->set_flashdata('error', 'Someting Went Wrong !');
			return redirect(base_url("quiz-pay/payment-mode/$quiz_id"));
		}

		return;

	}


	public function paypal_payment_view($quiz_id, $payment_id)
	{

		$payment_data = $this->Payment_model->get_paypal_payment_by_id($payment_id);

		if(empty($payment_data))
		{
			return redirect(base_url('404_override'));
		}

		$quiz_data =  $this->Payment_model->get_paid_quiz_by_id($quiz_id);
		if(empty($quiz_data))
		{
			return redirect(base_url('404_override'));
		}

        $this->set_title(sprintf(lang('home'), $this->settings->site_name));
        $content_data = array('Page_message' => lang('welcome_to_online_quiz'), 'page_title' => lang('home'),'quiz_id' => $quiz_id, 'payment_id' => $payment_id, 'quiz_data' => $quiz_data, 'payment_data' => $payment_data,);

        $data = $this->includes;

        if($payment_data->payment_status == 'succeeded')
        {
        	$data['content'] = $this->load->view('paypal_success', $content_data, TRUE);
        }
        else
        {
        	$data['content'] = $this->load->view('paypal_error', $content_data, TRUE);
        }
        
        $this->load->view($this->template, $data);
	}

	public function save_bank_transfer()
	{
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

		$transaction_no = $_POST['transaction_no'];
		$response = array();
		$user_id = isset($this->user['id']) ? $this->user['id'] : 0;

		if($transaction_no)
		{
			$transaction_data = array();
			$transaction_data['user_id'] = $user_id;
			$transaction_data['quiz_id'] = $_POST['quiz_id'];
			$transaction_data['name'] = $this->user['first_name'].' '.$this->user['last_name'];
			$transaction_data['email'] = $this->user['email'];
			$transaction_data['item_name'] = $_POST['title'];
			$transaction_data['item_price'] = $_POST['price'];
			$transaction_data['item_price_currency'] = $this->settings->paid_currency;
			$transaction_data['payment_status'] = 'pending';
			$transaction_data['created'] = date("Y-m-d H:i:s");
			$transaction_data['modified'] = date("Y-m-d H:i:s");
			$transaction_data['payment_gateway'] = 'bank-transfer';
			$transaction_data['token_no'] = $transaction_no;
			$transaction_data['invoice_no'] = $invoice_no;
			
			$inserted_id = $this->Payment_model->insertTransaction($transaction_data);

			if($inserted_id)
			{
				$this->session->set_flashdata('message', lang('bank_transfer_added_successfully'));
				$response['success'] = 	lang('bank_transfer_added_successfully');
			}
		}
		else 
		{
			$response['empty'] = 'error';
		}
		echo json_encode($response);
	}

	function update_bank_transfer()
	{
		$transaction_no = $_POST['transaction_no'];
		$response = array();
		$user_id = isset($this->user['id']) ? $this->user['id'] : 0;
		if($transaction_no)
		{
			$transaction_data = array();
			$transaction_data['token_no'] = $transaction_no;
			$transaction_data['modified'] = date("Y-m-d H:i:s");
			$update_id = $this->Payment_model->update_bank_transfer_token($_POST['quiz_id'],$user_id,$transaction_data);
			if($update_id)
			{
				$this->session->set_flashdata('message', lang('bank_transfer_updated_successfully'));
				$response['success'] = lang('bank_transfer_updated_successfully');
			}
		}
		else
		{
			$response['empty'] = 'error';
		}	
		echo json_encode($response);
	}

}