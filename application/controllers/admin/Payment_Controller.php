<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Payment_Controller extends Admin_Controller {
    function __construct() {
        parent::__construct();
        $this->add_css_theme('all.css');
        $this->add_js_theme('jquery.multi-select.min.js');
        $this->add_js_theme('payment_custome_script.js');
        $this->add_js_theme('plugin/taginput/bootstrap-tagsinput.min.js');
        $this->add_css_theme('plugin/taginput/bootstrap-tagsinput.css');
        // load the language files 
        // load the category model
        $this->load->model('Payment_model');
        //load the form validation
        $this->load->library('form_validation');
        // set constants
        define('REFERRER', "referrer");
        define('THIS_URL', base_url('admin/payment'));
        define('DEFAULT_LIMIT', 10);
        define('DEFAULT_OFFSET', 0);
        define('DEFAULT_SORT', "id");
        define('DEFAULT_DIR', "asc");
    }

    function index() {

        $this->add_css_theme('sweetalert.css')->add_js_theme('sweetalert-dev.js')->add_js_theme('bootstrap-notify.min.js')->set_title(lang('payment_list'));

        $data = $this->includes;
        $content_data = array();
        // load views
        $data['content'] = $this->load->view('admin/payment/list', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }

    function payment_list()
    {
        $list = $this->Payment_model->get_payment();
        
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $payment) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = ucfirst($payment->first_name .' '.$payment->last_name);
            $row[] = ucfirst($payment->title);
            $row[] = ucfirst($payment->item_price_currency.' '.$payment->item_price);
            $row[] = ucfirst($payment->payment_gateway);

            $success = $fail = $pending ="";
            if($payment->payment_status == 'succeeded')
            {
                $success = 'selected';
            }
            elseif($payment->payment_status == 'fail')
            {
                $fail = 'selected';
            }
            elseif($payment->payment_status == 'pending')
            {
                $pending = 'selected';
            }
            
            $payment_status = '<select class="form-control w-75 float-left pay-change-'.$payment->id.'"  name="pay_status">
                                    <option '.$fail.' value="fail">Fail</option>
                                    <option '.$pending.' value="pending">Pending</option>
                                    <option '.$success.' value="succeeded">Success</option>
                                </select>
                                <i class="fas fa-check btn btn-info  pay-status" data-payment_id="'.$payment->id.'"></i>';
            $row[] = $payment_status;
            $row[] = '<button id="myBtn-'.$payment->id.'" data-toggle="modal" data-target="#myModal" title="View Detail" class="btn btn-warning myBtn btn-action mr-1" data-payment_id="'.$payment->id.'">View Detail</button>
                <a href="'.base_url('admin/payment/invoice/'.$payment->id).'" target="_blank" class="btn btn-info text-white">'.lang('invoice').'</a>';
            $data[] = $row;
            
        }
        $output = array("draw" => $_POST['draw'], "recordsTotal" => $this->Payment_model->count_all(), "recordsFiltered" => $this->Payment_model->count_filtered(), "data" => $data);

        //output to json format
        echo json_encode($output);
    }

    function update_status()
    {
        $id = $_POST['payment_id'];
        $status = $_POST['status_value'];
        $this->Payment_model->updatestatus($id, $status);
        $success = array('status' => $status, 'messages' => lang('admin_record_updated_successfully'));
        echo json_encode($success);
    }

    function payment_detail()
    {
        $id = $_POST['payment_id'];
        $payment_data = $this->Payment_model->get_payment_detail_by_id($id);
        
        $data = $this->includes;
        $content_data = array('payment_data'=>$payment_data,);
        // load views
        $modal_data = $this->load->view('admin/payment/payment_detail', $content_data, TRUE);
        echo json_encode($modal_data);
        
    }

    function invoice($payment_id = NULL)
    {

        $payment_data = $this->Payment_model->get_payment_detail_by_id($payment_id);
        
        $data = $this->includes;
        $content_data['title'] = lang('invoice');
        $content_data['payment_data'] = $payment_data;
        // load views
        $modal_data = $this->load->view('admin/payment/invoice', $content_data);
    }
}