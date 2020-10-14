<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Backup extends Admin_Controller {
    function __construct() {
        parent::__construct();
        $this->add_css_theme('all.css');
        $this->add_js_theme('jquery.multi-select.min.js');
        $this->add_js_theme('backup_restore.js');
        $this->add_js_theme('plugin/taginput/bootstrap-tagsinput.min.js');
        $this->add_css_theme('plugin/taginput/bootstrap-tagsinput.css');
        // load the language files 
        // load the category model
        $this->load->model('BackupModel');
        //load the form validation
        $this->load->library('form_validation');
        // set constants
        define('REFERRER', "referrer");
        define('THIS_URL', base_url('admin/backup'));
        define('DEFAULT_LIMIT', 10);
        define('DEFAULT_OFFSET', 0);
        define('DEFAULT_SORT', "last_name");
        define('DEFAULT_DIR', "asc");
        // Load zip library
        $this->load->library('zip');
        //load directory helper
        $this->load->helper('directory');
    }

    function index()
    {
        
        $list = directory_map('./assets/uploads/backup/');
        arsort($list);
     
        $this->add_css_theme('sweetalert.css')->add_js_theme('sweetalert-dev.js')->add_js_theme('bootstrap-notify.min.js')->set_title(lang('admin_backup_restore_list'));

        $data = $this->includes;
        $content_data = array('backup_list' => $list,);
        // load views
        $data['content'] = $this->load->view('admin/backup/list', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }

    function database_backup()
    {
        $name=$this->db->database;
        $this->load->dbutil();
        $prefs = array(
            'format' => 'zip',
            'filename' => 'db-backup_' . date('Y-m-d H-i')
        );

        $backup =$this->dbutil->backup($prefs);

        $db_name = $prefs['filename'].'.zip';
        $save = './assets/uploads/backup/'.$db_name;
        $this->load->helper('file');
        $file_status = write_file($save, $backup);
        if($file_status)
        {
            $this->session->set_flashdata('message', lang('admin_backup_successfully'));
            redirect(base_url('admin/backup'));
        }
        else
        {
            $this->session->set_flashdata('error', lang('admin_backup_error'));
            redirect(base_url('admin/backup'));   
        }
    }

    // function backup_list()
    // {
    //     $list = directory_map('./assets/uploads/');
    //     $data = array();
    //     $no = $_POST['start'];
    //     foreach ($list as $backup) {
            
    //         $no++;

    //         $row = array();
    //         $row[] = $backup;
    //         $row[] = '<a href="' . base_url("admin/blog/blog_category_form/") . '" data-toggle="tooltip" title="'.lang("admin_edit_record").'" class="btn btn-primary btn-action mr-1"><i class="fas fa-pencil-alt"></i></a>

    //         <a href="' . base_url("admin/blog/blog_category_delete/") . '" data-toggle="tooltip"  title="'.lang("admin_delete_record").'" class="btn btn-danger btn-action mr-1 blog_cat_delete"><i class="fas fa-trash"></i></a>';
            
    //     }
        
    //     $output = $row;
    //     //output to json format
    //     echo json_encode($output);    
    // }

    function download($file = NULL)
    {
        $this->load->helper('download');
        $name = $file;
        $url = base_url().'assets/uploads/backup/'.$file;
        $data = file_get_contents($url); 
        $download_status = force_download($name, $data);

    }

    function restore($file = NULL)
    {
        
    }

    function delete($file = NULL)
    {
        
        if(!empty($file)) {
            p($file);
            $path = "./assets/uploads/backup/".$file;
            unlink($path);
        }

        $this->session->set_flashdata('message', lang('admin_record_delete_successfully'));
        redirect(base_url('admin/backup'));
    }
}
