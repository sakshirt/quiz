<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Blog_Controller extends Public_Controller {
    /** 
     * Constructor
     */
    function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('BlogModel');
        $this->add_css_theme('set2.css');
    }

    function index() 
    {

        return redirect('blog/list/all');
    }

    function list($category_slug) 
    {
        if($category_slug == 'all' OR $category_slug == 'ALL')
        {
            $category_id = NULL;
        }
        else
        {
            $blog_category_data = $this->BlogModel->get_blog_category_by_slug($category_slug);
            if(empty($blog_category_data)) 
            {
                return redirect(base_url("404_override"));
            }
            $category_id = $blog_category_data->id;        
        }

    	$blog_data_array = $this->BlogModel->get_blog_total_record($category_id);

    	$count_blog_post = count($blog_data_array);

    	$this->load->library('pagination');

        $config['base_url'] = base_url('blog/list/').$category_slug;
        $config['total_rows'] = $count_blog_post;
        $config['per_page'] = 10;
        $config['uri_segment'] = 4;
        $config['use_page_numbers'] = TRUE;
        $config['page_query_string'] = FALSE;
        $config['reuse_query_string'] = TRUE;
        $config['first_link'] = 'First';
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['attributes'] = ['class' => 'page-link'];
        $config['first_link'] = false;
        $config['last_link'] = false;
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['prev_link'] = '&laquo';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['next_link'] = '&raquo';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a href="#" class="page-link">';
        $config['cur_tag_close'] = '<span class="sr-only">(current)</span></a></li>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';

        $this->pagination->initialize($config);
        $pro_per_page = $config['per_page'];

        $page = $this->uri->segment(4) > 0 ? (($this->uri->segment(4) - 1) * $pro_per_page) : $this->uri->segment(4);
        $page_links = $this->pagination->create_links();

        $blog_post_data = $this->BlogModel->get_blog_post_per_page($category_id, $pro_per_page, $page);
        
        $blog_category = $this->BlogModel->all_blog_category();
        $title = $category_id ?  $blog_category_data->title : lang('all_recent_blogs');
    	$this->set_title($title);

        $content_data = array('title' => $title,'category_slug' => $category_slug,'pagination' => $page_links, 'blog_post_data' => $blog_post_data, 'blog_category' => $blog_category,);
    	
        $data = $this->includes;
        $data['content'] = $this->load->view('blog_list', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }



    function detail($post_slug = NULL)
    {   
    	$post_detail_data = $this->BlogModel->get_post_by_slug($post_slug);
        
        if(empty($post_detail_data))
        {
           $this->session->set_flashdata('error', 'Sorry Wrong Uri Paramiter This Blog Is Not Exist ...!'); 
           redirect(base_url());
        }
            
        $current_date = date('Y-m-d');
        $post_view_data = $this->BlogModel->get_post_view($post_detail_data->id,$this->input->ip_address(),$current_date);
        
        $savedata = array();
        $savedata['post_id'] = $post_detail_data->id;
        $savedata['ip_address'] = $this->input->ip_address();
        $savedata['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        
        if(empty($post_view_data))
        {
           $inserted_data = $this->BlogModel->save_post_view_data($savedata);
        }        
         
        $page_title = $post_detail_data->post_title;
        $this->set_title($page_title);
        $this->add_js_theme('blog.js');
        $blog_category = $this->BlogModel->all_blog_category();

        $meta_data = array('meta_title' => $post_detail_data->meta_title, 'meta_keyword' => $post_detail_data->meta_keywords, 'meta_description' =>  $post_detail_data->meta_description,'title' => $post_detail_data->post_title,'description' => $post_detail_data->post_description,'image' => $post_detail_data->post_image,);

        $content_data = array('Page_message' => $page_title, 'page_title' => $page_title,'post_detail_data' => $post_detail_data,'blog_category' => $blog_category,);
        
        $data = $this->includes;
        $data['content'] = $this->load->view('post_detail', $content_data, TRUE);
        $data['meta_data'] = $meta_data;
        $this->load->view($this->template, $data);
    }

    function like_post()
    {
        $response = [];    
        if($this->session->userdata('logged_in')) 
        {
            $save_post_like['post_id'] = $_POST['post_id'];
            $save_post_like['user_id'] = $this->user['id'];

            $inserted_data = $this->BlogModel->insert_post_like($save_post_like);
            
            if($inserted_data)
            {
                $response['success'] = 'successfull';
            }
            else
            {
                $response['error'] = 'unsuccessfull';
            }
        }
        else
        {
            $response['status'] = 'redirect';
        }
        echo json_encode($response);

    }

    function delete_post()
    {
        $response = [];
        if($this->session->userdata('logged_in')) 
        {
            $post_id = $_POST['post_id'];
            $user_id = isset($this->user['id']) ? $this->user['id'] : 0;
            
            $delete_data = $this->BlogModel->delete_like_post_through_postid($post_id, $user_id);
            
            if($delete_data)
            {
                $response['success'] = 'successfull';
            }
            else
            {
                $response['error'] = 'unsuccessfull';
            }
        }
        else
        {
            $response['status'] = 'redirect';
        }
        echo json_encode($response);
    }

}