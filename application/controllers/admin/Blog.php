<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Blog extends Admin_Controller {
    function __construct() {
        parent::__construct();
        $this->add_css_theme('all.css');
        $this->add_js_theme('jquery.multi-select.min.js');
        $this->add_js_theme('blog_custome_script.js');
        $this->add_js_theme('plugin/taginput/bootstrap-tagsinput.min.js');
        $this->add_css_theme('plugin/taginput/bootstrap-tagsinput.css');
        // load the language files 
        // load the category model
        $this->load->model('BlogModel');
        //load the form validation
        $this->load->library('form_validation');
        // set constants
        define('REFERRER', "referrer");
        define('THIS_URL', base_url('admin/blog'));
        define('DEFAULT_LIMIT', 10);
        define('DEFAULT_OFFSET', 0);
        define('DEFAULT_SORT', "last_name");
        define('DEFAULT_DIR', "asc");
    }

    function index() {
        $this->add_css_theme('sweetalert.css')->add_js_theme('sweetalert-dev.js')->add_js_theme('bootstrap-notify.min.js')->set_title(lang('admin_blog_category_list'));

        $data = $this->includes;
        $content_data = array();
        // load views
        $data['content'] = $this->load->view('admin/blog/blog_category_list', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }

    function blog_category_form($blog_category_id = null) {
        $parentcat = $this->BlogModel->allcategory($blog_category_id);
        $editData = "";
        if ($blog_category_id) {
            $editData = $this->BlogModel->getfetch($blog_category_id);
        }

        $this->form_validation->set_rules('title', 'Title', 'trim|required');
        if ($this->form_validation->run() != false) 
        {
            $content = array();
            $display_on_home = $this->input->post('display_on_home', TRUE) ? 1 : 0;
            $category_name_count = $this->BlogModel->category_name_like_this($blog_category_id, $this->input->post('title', TRUE));
            $count = $category_name_count > 0 ? '-' . $category_name_count : '';
            $content['title'] = $this->input->post('title', TRUE);
            $content['slug'] = slugify_string($this->input->post('title', TRUE) . $count);
            $content['parent_category'] = $this->input->post('parentcat', TRUE);
            $content['description'] = $this->input->post('description', TRUE);
            $content['display_on_home'] = $display_on_home;
            $name = $_FILES['image']['name'];
            if ($name) 
            {
                $checkImg = $this->BlogModel->getImage($blog_category_id);
                if ($checkImg) {
                    $path = "./assets/images/blog_image/" . $checkImg;
                    unlink($path);
                }
                $config['upload_path'] = "./assets/images/blog_image/";
                $config['allowed_types'] = 'gif|jpg|png|bmp|jpeg';
                $this->load->library('upload', $config);
                if (!$this->upload->do_upload('image')) {
                    $error = array('error' => $this->upload->display_errors());
                    $this->session->set_flashdata('error', $error['error']);
                }
                $file = $this->upload->data();
                $content['image'] = $file['file_name'];
            }
            if ($this->input->post(lang('core_button_save'), TRUE)) 
            {
                $this->BlogModel->blog_category_insert($content);
                $this->session->set_flashdata('message', lang('admin_record_added_successfully'));
                redirect(base_url('admin/blog'));
            }
            if ($this->input->post(lang('core_button_update'), TRUE)) 
            {
                $this->BlogModel->blog_category_update($content, $blog_category_id);
                $this->session->set_flashdata('message', lang('admin_record_updated_successfully'));
                redirect(base_url('admin/blog'));
            }
        } 
        else 
        {
            $fielderror = $this->form_validation->error_array();
        }
        if ($blog_category_id) {
            $this->set_title(lang('admin_edit_blog_category'));
        } else {
            $this->set_title(lang('admin_add_blog_category'));
        }
        $data = $this->includes;
        $content_data = array('editData' => $editData, 'cat_title' => $parentcat);
        // load views 
        $data['content'] = $this->load->view('admin/blog/category_form', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }

    function blog_category_list() {
        $list = $this->BlogModel->get_blog_category();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $blog_category) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = ucfirst($blog_category->title);
            if ($blog_category->image) {
                $row[] = "<img src='" . base_url('assets/images/blog_image/' . $blog_category->image) . "' class='listing_img'>";
            } else {
                $row[] = "<img src='" . base_url('assets/images/blog_image/default.jpg.jpg') . "' class='listing_img'>";
            }
            
            $row[] = '<a href="' . base_url("admin/blog/blog_category_form/" . $blog_category->id) . '" data-toggle="tooltip" title="'.lang("admin_edit_record").'" class="btn btn-primary btn-action mr-1"><i class="fas fa-pencil-alt"></i></a>

            <a href="' . base_url("admin/blog/blog_category_delete/" . $blog_category->id) . '" data-toggle="tooltip"  title="'.lang("admin_delete_record").'" class="btn btn-danger btn-action mr-1 blog_cat_delete"><i class="fas fa-trash"></i></a>';
            $data[] = $row;
        }
        $output = array("draw" => $_POST['draw'], "recordsTotal" => $this->BlogModel->count_all(), "recordsFiltered" => $this->BlogModel->count_filtered(), "data" => $data);
        //output to json format
        echo json_encode($output);
    }

    function blog_category_delete($blog_category_id = NULL) 
    {
        $findImage = $this->BlogModel->deleteimage($blog_category_id);
        if (!empty($findImage)) {
            $path = "./assets/images/blog_image/$findImage";
            unlink($path);
        }
        $this->BlogModel->category_delete($blog_category_id);
        $this->session->set_flashdata('message', lang('admin_record_delete_successfully'));
        redirect(base_url('admin/blog'));
    }

    function post()
    {
        $this->add_css_theme('sweetalert.css')->add_js_theme('sweetalert-dev.js')->add_js_theme('bootstrap-notify.min.js')->set_title(lang('admin_blog_post_list'));

        $data = $this->includes;
        $content_data = array();
        // load views
        $data['content'] = $this->load->view('admin/blog/blog_post_list', $content_data, TRUE);
        $this->load->view($this->template, $data);       
    }

    function blog_post_form($post_id = NULL)
    {
        $author = $this->BlogModel->alluser();
        $blog_category = $this->BlogModel->all_blog_category();
        
        $editData = "";
        $this->form_validation->set_rules('title', 'Title', 'trim|required');
        if ($this->form_validation->run() != false) 
        {
            $content = array();
            $post_name_count = $this->BlogModel->post_name_like_this($post_id, $this->input->post('title', TRUE));
            $count = $post_name_count > 0 ? '-' . $post_name_count : '';
            $content['post_title'] = $this->input->post('title', TRUE);
            $content['post_slug'] = slugify_string($this->input->post('title', TRUE) . $count);
            $content['post_status'] = $this->input->post('status', TRUE);
            $content['post_description'] = $this->input->post('description', TRUE);
            $content['post_author_id'] = $this->input->post('author', TRUE);
            $content['blog_category_id'] = $this->input->post('blogcategory', TRUE);
            $content['meta_title'] =  $this->input->post('metatitle');
            $content['meta_keywords'] =  $this->input->post('metakeywords');
            $content['meta_description'] =  $this->input->post('metadescription');
            
            $name = $_FILES['image']['name'];
            if ($name) 
            {
                $checkImg = $this->BlogModel->get_post_image($post_id);
                if ($checkImg) {
                    $path = "./assets/images/blog_image/post_image/" . $checkImg;
                    unlink($path);
                }
                $config['upload_path'] = "./assets/images/blog_image/post_image/";
                $config['allowed_types'] = 'gif|jpg|png|bmp|jpeg';
                $this->load->library('upload', $config);
                if (!$this->upload->do_upload('image')) {
                    $error = array('error' => $this->upload->display_errors());
                    $this->session->set_flashdata('error', $error['error']);
                }
                $file = $this->upload->data();
                $content['post_image'] = $file['file_name'];
            }
            if ($this->input->post(lang('core_button_save'), TRUE)) 
            {
                $this->BlogModel->blog_post_insert($content);
                $this->session->set_flashdata('message', lang('admin_record_added_successfully'));
                redirect(base_url('admin/blog/post'));
            }
            if ($this->input->post(lang('core_button_update'), TRUE)) 
            {
                $this->BlogModel->blog_post_update($content, $post_id);
                $this->session->set_flashdata('message', lang('admin_record_updated_successfully'));
                redirect(base_url('admin/blog/post'));
            }
        } 
        else 
        {
            $fielderror = $this->form_validation->error_array();
        }
        if ($post_id) {
            $editData = $this->BlogModel->get_post_fetch($post_id);
        }
        if ($post_id) {
            $this->set_title(lang('admin_edit_blog_post'));
        } else {
            $this->set_title(lang('admin_add_blog_post'));
        }
        $data = $this->includes;
        $content_data = array('author' => $author,'editData' => $editData, 'blog_category' => $blog_category,);
        // load views
        $data['content'] = $this->load->view('admin/blog/post_form', $content_data, TRUE);
        $this->load->view($this->template, $data);       
    }

    function blog_post_list() {
        $list = $this->BlogModel->get_blog_post();

        $data = array();
        $no = $_POST['start'];
        foreach ($list as $blog_post) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = ucfirst($blog_post->post_title);
            if ($blog_post->post_image) {
                $row[] = "<img src='" . base_url('assets/images/blog_image/post_image/' . $blog_post->post_image) . "' class='listing_img'>";
            } else {
                $row[] = "<img src='" . base_url('assets/images/blog_image/post_image/default.jpg') . "' class='listing_img'>";
            }
            
            $row[] = '<a href="' . base_url("admin/blog/blog_post_form/" . $blog_post->id) . '" data-toggle="tooltip" title="'.lang("admin_edit_record").'" class="btn btn-primary btn-action mr-1"><i class="fas fa-pencil-alt"></i></a>

            <a href="' . base_url("admin/blog/blog_post_delete/" . $blog_post->id) . '" data-toggle="tooltip"  title="'.lang("admin_delete_record").'" class="btn btn-danger btn-action mr-1 blog_cat_delete"><i class="fas fa-trash"></i></a>';
            $data[] = $row;
        }
        $output = array("draw" => $_POST['draw'], "recordsTotal" => $this->BlogModel->count_all_post(), "recordsFiltered" => $this->BlogModel->count_filtered_post(), "data" => $data);
        //output to json format
        echo json_encode($output);
    }

    function blog_post_delete($post_id = NULL)
    {
        $findImage = $this->BlogModel->delete_post_image($post_id);
        if (!empty($findImage)) {
            $path = "./assets/images/blog_image/post_image/$findImage";
            unlink($path);
        }
        $this->BlogModel->post_delete($post_id);
        $this->session->set_flashdata('message', lang('admin_record_delete_successfully'));
        redirect(base_url('admin/blog/post'));       
    }
}