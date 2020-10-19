<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Profile extends Private_Controller {
    /**
     * Constructor
     */
    function __construct() {
        parent::__construct();
        // load the users model
        $this->load->model('UsersModel');
        $this->load->library('form_validation');
        $this->add_css_theme('quiz_box.css');
        $this->add_css_theme('set2.css');
        $this->add_css_theme('table-main.css');
        $this->add_js_theme('quiz.js');
        $this->load->library('encrypt');
    }
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/
    /**
     * Profile Editor
     */
    
    function index() { 
        // validators

        $this->form_validation->set_error_delimiters($this->config->item('error_delimeter_left'), $this->config->item('error_delimeter_right'));
        $this->form_validation->set_rules('mobile', 'Mobile', 'required|trim|min_length[8]|max_length[15]|numeric|callback__check_mobile');
        $this->form_validation->set_rules('first_name','First Name', 'required|trim|min_length[2]|max_length[32]');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required|trim|min_length[2]|max_length[32]');
        $this->form_validation->set_rules('email', 'Email', 'trim|max_length[128]|valid_email|callback__check_email');
        $this->form_validation->set_rules('language', 'Language', 'required|trim');
        $this->form_validation->set_rules('password_repeat', 'Password Repeat', 'min_length[5]');
        $this->form_validation->set_rules('password', 'Password', 'min_length[5]|matches[password_repeat]');
        $this->form_validation->set_error_delimiters('', '');
        if ($this->form_validation->run() == TRUE) {
            action_not_permitted();
            // save the changes
            $saved = $this->UsersModel->edit_profile($this->input->post(), $this->user['id']);
            if ($saved) {
                // reload the new user data and store in session
                $user_id = isset($this->user['id']) ? $this->user['id'] : 0;
                $this->user = $this->UsersModel->get_user($user_id);
                unset($this->user['password']);
                unset($this->user['salt']);
                $this->session->set_userdata('logged_in', $this->user);
                $this->session->language = $this->user['language'];
                $this->lang->load('users', $this->user['language']);
                $this->session->set_flashdata('message', lang('front_record_edited_successfully'));
            } else {
                $this->session->set_flashdata('error', lang('front_record_edited_during_error'));
            }
            // reload page and display message
            redirect('profile');
        }

        $user_id = isset($this->user['id']) ? $this->user['id'] : 0;

        //get purchased quiz by user login
        $purchased_quiz = $this->UsersModel->get_purchase_quiz_by_userid($user_id);

        //get like quiz by user login
        $session_quiz_data = array();
        $session_quiz_question_data = array();

        if($this->session->quiz_session)
        {
            $get_quiz_session = $this->session->quiz_session;
            $session_quiz_data = $get_quiz_session['quiz_data'];
            $session_quiz_question_data = $get_quiz_session['quiz_question_data'];
        }

        $like_quiz = $this->UsersModel->get_quiz_by_userid($this->user['id']);
        
        $quiz_data = array();
        foreach ($like_quiz as $like_key => $like_value) 
        {
            $question_count = $this->UsersModel->get_question_count_by_quiz_id($like_value->id);
            if($like_value->number_questions < $question_count )
            {
              $quiz_data[] = $like_value;
            }
        }

        //get post like by user login
        $like_post = $this->UsersModel->get_like_post_by_loggedin_user($this->user['id']);

        //get user payment status list
        $payment_list = $this->UsersModel->get_payment_status_by_loggedin_user($this->user['id']);

        // setup page header data
        $this->set_title(lang('user_profile'));
        $this->add_js_theme('quiz.js');

        // set content data
        $content_data = array('cancel_url' => base_url(), 'user' => $this->user, 'password_required' => FALSE, 'session_quiz_data' =>$session_quiz_data, 'session_quiz_question_data' => $session_quiz_question_data, 'quiz_data' => $quiz_data,'purchased_quiz'=>$purchased_quiz,'like_post'=>$like_post,'payment_list' => $payment_list,);

        // load views
        $data = $this->includes;
        
        $data['content'] = $this->load->view('user/profile_update_form', $content_data, TRUE);        

        $this->load->view($this->template, $data);
    }

    /**************************************************************************************
     * PRIVATE VALIDATION CALLBACK FUNCTIONS
     **************************************************************************************/
    /**
     * Make sure username is available
     *
     * @param  string $username
     * @return int|boolean
     */
    function _check_username($username) {
        if (trim($username) != $this->user['username'] && $this->UsersModel->username_exists($username)) {
            $this->form_validation->set_message('_check_username', sprintf(lang('username_exists'), $username));
            return FALSE;
        } else {
            return $username;
        }
    }

    /**
     * Make sure mobile is available
     *
     * @param  string $mobile
     * @return int|boolean
     */
    function _check_mobile($mobile) {
        if (trim($mobile) != $this->user['mobile'] && $this->UsersModel->mobile_exists($mobile)) {
            $this->form_validation->set_message('_check_mobile', sprintf(lang('mobile_exists'), $mobile));
            return FALSE;
        } else {
            return $mobile;
        }
    }

    /**
     * Make sure email is available
     *
     * @param  string $email
     * @return int|boolean
     */
    function _check_email($email) {
        if (trim($email) != $this->user['email'] && $this->UsersModel->email_exists($email)) {
            $this->form_validation->set_message('_check_email', sprintf(lang('email_exists'), $email));
            return FALSE;
        } else {
            return $email;
        }
    }

    function remove_from_favourite($product_id = NULL) {
        $status = $this->UsersModel->remove_from_fav_product($this->user['id'], $product_id);
        if ($status) {
            $this->session->set_flashdata('message', 'Product Removed From Favourite List');
            return redirect(base_url('profile'));
        } else {
            $this->session->set_flashdata('error', 'Invalid Request');
            return redirect(base_url('profile'));
        }
    }

    function view_payment_detail()
    {
        $id = $_POST['payment_id'];
        $payment_data = $this->UsersModel->get_payment_detail_by_id($id);
        
        $data = $this->includes;
        $content_data = array('payment_data'=>$payment_data,);
        // load views
        $modal_data = $this->load->view('admin/payment/payment_detail', $content_data, TRUE);
        echo json_encode($modal_data);
    }

    function invoice($payment_id = NULL)
    {   
        $payment_id = $this->encrypt->decode($payment_id);
        $payment_data = $this->UsersModel->get_payment_detail_by_id($payment_id);
        
        $data = $this->includes;
        $content_data['title'] = lang('invoice');
        $content_data['payment_data'] = $payment_data;
        // load views
        $modal_data = $this->load->view('admin/payment/invoice', $content_data);
    }
}
