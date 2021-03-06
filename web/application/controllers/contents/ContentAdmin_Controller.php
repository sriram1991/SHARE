<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ContentAdmin_Controller extends CW_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->helper("file");
        $this->load->helper('download');
        $this->load->library('form_validation');
        $this->load->model('resource_model');
        $this->load->model('user_model');
    }

    public function index(){
    	$data['data'] = $this->session->all_userdata();
        $data['flash_msg'] = $this->session->flashdata('success_msg');
        $data['user_home'] = "/mentor_home";
        $data['role_view'] = "content_admin/ca_body_leftpan";
        $this->load->view('user_header',$data);
        $this->load->view('user_body_leftpan');
        $this->load->view('user_body_rightpan');
        $this->load->view('user_footer');
        log_message('debug', 'Registrar home');
    }

    // Action : content admin dashboard 
    public function dashboard(){
        if($this->input->server('REQUEST_METHOD') == 'GET') {
            // Load Resource view according to USER GM / Content Director
            $user_role = $this->session->userdata('user_role');
            $user_id   = $this->session->userdata('user_id');
            $data['user_details'] = $this->session->all_userdata();
            $data['user_role'] = $user_role;
            // ---------------------------------------------------------------
            // User Roles : 6 - mentor or sme 7- super admin 8 - content director admin 
            switch ($user_role) {
                case '6':
                    $data['director_details']          = $this->resource_model->get_admin_subject($user_id);
                    $director_details                  = $this->resource_model->get_admin_subject($user_id);
                    // log_message('debug','Mentor/SME Subject :'.$director_details['subject_name']);
                    $data['resource_count']            = $this->resource_model->get_resource_count();
                    $data['assessment_count']          = $this->resource_model->get_assessment_count();
                    // $data['subject_resource_count']    = $this->resource_model->get_subject_resource_count($director_details['subject_name']);
                    // $data['subject_assessment_count']  = $this->resource_model->get_subject_assessment_count($director_details['subject_name']);
                   
                    // Sriram  -> Operation : Get the count of number of subjects.
                    $data['no_of_subject']   = $this->resource_model->get_sme_subjects($user_id);
                    $no_of_subject = $data['no_of_subject'];

                    $data['subject_resource_count']     = $this->resource_model->get_this_mentor_resource_count($user_id);
                    $data['subject_assessment_count']   = $this->resource_model->get_this_assessment_count($user_id);
                    // Used to Bring Subjects Name and Resource and Assessment count
    
                    $data['sme_subject_resource_count']   = $this->resource_model->get_sme_subjects_with_resource_count($user_id);
                    $data['sme_subject_assessment_count'] = $this->resource_model->get_sme_subjects_with_assessment_count($user_id);

                    // Action : Get no of mentor course and their subscriber count
                    // $data['course_count'] = $this->resource_model->get_sme_course_count($user_id);
                    // log_message('debug','No of courseses >>>>>>>>>>>>>>>>>>>>>>>>> '.print_r($data['course_count'],tr));
                    $data['user_count']    = $this->resource_model->get_sme_course_and_subsc_count($user_id);

                    $this->load->view('content_admin/mentor_sme/mentor_dashboard',$data);
                    break;
                case '7':
                    $this->load->view('content_admin/contentadmin_dashboard',$data);
                    break;
                case '8':
                    $this->load->view('content_admin/contentadmin_dashboard',$data);
                    break;
                default:
                    log_message('debug','Something Went Wrong in mentor_resource_view ');
                    break;
            }
            // ---------------------------------------------------------------
        }
    }

    // Action : Open Course Resource Management
    public function open_content_management(){
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            log_message('debug','********** Course Resource Management **********');
            $this->load->view('content_admin/content_management');
            log_message('debug','************************************************');
        }
    }

    // Action : Open Mentor Resource Management
    public function open_mentor_content_management(){
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            log_message('debug','********** Course Resource Management **********');
            $this->load->view('content_admin/mentor_sme/resource_management');
            log_message('debug','************************************************');
        }
    }

    // Action : Option Course Dashboard
    public function ca_dashboard() {
        if($this->input->server('REQUEST_METHOD') == 'GET') {
            $this->load->view('content_admin/ca_dashboard');
        }
    }

    public function course_management() {

        if($this->input->server('REQUEST_METHOD') == 'GET') {
            $data['user_details'] = $this->session->all_userdata();
            $this->load->view('content_admin/course_dashboard',$data);
        }

    }

    public function batch_view() {
        if($this->input->server('REQUEST_METHOD') == 'POST') {
            //$data['resource_details'] = $this->resource_model->get_all_resources();
            $this->load->view('content_admin/batch_view');
        }
    }

    //---------------------------------------------------------------------------------------//
    // Validation for Name's Resource Assessment Subject Course
    //---------------------------------------------------------------------------------------//

    // Action : Check Resource Name Exist
    public function isResourceNamePresent() {
        if($this->input->server('REQUEST_METHOD') == 'POST') {
            $resource_name = $this->input->post('edit_resource_name');
            if($resource_name != null) {
                $resource_name = $this->input->post('edit_resource_name');
                $resource_id   = $this->input->post('resource_id');
                log_message('debug','Edit resource name :'.$resource_name.' resource id :'.$resource_id);
                $res = $this->resource_model->check_resource_name_id($resource_id,$resource_name);
                if($res != null){
                    echo "false";
                    log_message('debug','resource Name Present DB output: '.$res);
                } else {
                    echo "true";
                    log_message('debug','resource Name Not Prenent or Same DB output'.$res);
                }

            } else {
                $resource_name = $this->input->post('resource_name');
                log_message('debug','resource name :'.$resource_name);
                $res = $this->resource_model->get_resource_name($resource_name);
                if(isset($res)){
                    echo "false";
                    log_message('debug','resource Name Present DB output: '.$res);
                } else {
                    echo "true";
                    log_message('debug','resource Name Not Prenent DB output'.$res);
                }
            }
        }
    }

    // Action : Check Assessment Name Exist
    public function isAssessmentNoPresent() {
        if($this->input->server('REQUEST_METHOD') == 'POST') {
            $test_no = $this->input->post('test_no');
            log_message('debug',"Check Assessment No : ".$test_no);
            $res = $this->resource_model->get_assessment_no($test_no);
            if(isset($res)){
                echo "false";
                log_message('debug','Resource Name Present DB output: '.$res);
            } else {
                echo "true";
                log_message('debug','Resource Name Not Prenent DB output'.$res);
            }
        }
    }

    // Action : Check Subject Name Exist
    public function isSubjectNamePresent() {
        if($this->input->server('REQUEST_METHOD') == 'POST') {
            $subject_name = $this->input->post('edit_subject_name');
            if($subject_name != null) {
                $subject_name = $this->input->post('edit_subject_name');
                $subject_id   = $this->input->post('subject_id');
                log_message('debug','Edit subject name :'.$subject_name.' subject id :'.$subject_id);
                $res = $this->resource_model->check_subject_name_id($subject_id,$subject_name);
                if($res != null){
                    echo "false";
                    log_message('debug',' Edit subject Name Present DB output: '.$res);
                } else {
                    echo "true";
                    log_message('debug','Edit subject Name Not Prenent or Same DB output'.$res);
                }

            } else {
                $subject_name = $this->input->post('subject_name');
                log_message('debug','subject name :'.$subject_name);
                $res = $this->resource_model->get_subject_name($subject_name);
                if(isset($res)){
                    echo "false";
                    log_message('debug','subject Name Present DB output: '.$res);
                } else {
                    echo "true";
                    log_message('debug','subject Name Not Prenent DB output'.$res);
                }
            }
        }
    }

    // Action : Check Course Name Exist
    public function isCourseNamePresent() {
        if($this->input->server('REQUEST_METHOD') == 'POST') {
            $course_name = $this->input->post('edit_course_name');
            if($course_name != null) {
                $course_name = $this->input->post('edit_course_name');
                $course_id   = $this->input->post('course_id');
                log_message('debug','Edit course name :'.$course_name.' Course id :'.$course_id);
                $res = $this->resource_model->check_course_name_id($course_id,$course_name);
                if($res != null){
                    echo "false";
                    log_message('debug','Course Name Present DB output: '.$res);
                } else {
                    echo "true";
                    log_message('debug','Course Name Not Prenent or Same DB output'.$res);
                }

            } else {
                $course_name = $this->input->post('course_name');
                log_message('debug',' course name :'.$course_name);
                $res = $this->resource_model->get_course_name($course_name);
                if(isset($res)){
                    echo "false";
                    log_message('debug','Course Name Present DB output: '.$res);
                } else {
                    echo "true";
                    log_message('debug','Course Name Not Prenent DB output'.$res);
                }
            }
        }
    }
   
    //---------------------------------------------------------------------------------------//




    //---------------------------------------------------------------------------------------//
    // Resource Management 
    //---------------------------------------------------------------------------------------//

    // By sriram -> Action : Load Resource View Page uploaded by
    public function resource_view1(){
        if($this->input->server('REQUEST_METHOD') == 'POST') {
            // Load Resource view according to USER GM / Content Director
            $user_role = $this->session->userdata('user_role');
            $user_id   = $this->session->userdata('user_id');
            $data['user_role'] = $user_role;
            if($user_role == '6'){
                $data['subject_list']  = $this->resource_model->get_admin_subject_details($user_id);
                $director_details      = $this->resource_model->get_admin_subject($user_id);    
                // log_message('debug','Content Director Subject :'.$director_details['subject_name']);
                $data['resource_details'] = $this->resource_model->get_all_subject_resources($director_details['subject_name']);
            }
            if($user_role == '7'){

                // $data['subject_list']     = $this->resource_model->get_all_subjects();
                // $data['resource_details'] = $this->resource_model->get_all_resources();   

                $data['subject_list']     = $this->resource_model->get_all_subjects();
                $data['resource_details'] = $this->resource_model->get_all_admin_resource_list();   
            }
            if($user_role == '8'){

                // $data['subject_list']     = $this->resource_model->get_all_subjects();
                // $data['resource_details'] = $this->resource_model->get_all_resources();   

                $data['subject_list']     = $this->resource_model->get_all_subjects();
                $data['resource_details'] = $this->resource_model->get_all_admin_resource_list();   
            }
            $this->load->view('content_admin/resource_view',$data);
        }
    }

    // Action : Load Resource View Page
    public function resource_view() {
        if($this->input->server('REQUEST_METHOD') == 'POST') {
            // Load Resource view according to USER GM / Content Director
            $user_role = $this->session->userdata('user_role');
            $user_id   = $this->session->userdata('user_id');
            $data['user_role'] = $user_role;
            if($user_role == '6'){
                $data['subject_list']  = $this->resource_model->get_admin_subject_details($user_id);
                $director_details      = $this->resource_model->get_admin_subject($user_id);    
                log_message('debug','Content Director Subject :'.$director_details['subject_name']);
                $data['resource_details'] = $this->resource_model->get_all_subject_resources($director_details['subject_name']);
            }
            if($user_role == '7'){
                $data['subject_list']    = $this->resource_model->get_all_subjects();
                $data['resource_details']   = $this->resource_model->get_all_resources();   
            }
            if($user_role == '8'){
                $data['subject_list']    = $this->resource_model->get_all_subjects();
                $data['resource_details']   = $this->resource_model->get_all_resources();   
            }
            $this->load->view('content_admin/resource_view',$data);
        }
    }

    // Action : Load Resource View Page
    public function mentor_resource_view() {
        if($this->input->server('REQUEST_METHOD') == 'POST') {
            // Load Resource view according to USER GM / Content Director
            $user_role = $this->session->userdata('user_role');
            $user_id   = $this->session->userdata('user_id');
            $data['user_role'] = $user_role;
            // User Roles : 6 - mentor or sme 7- super admin 8 - content director admin 
            switch ($user_role) {
                case '6':
                    $data['subject_list']  = $this->resource_model->get_admin_subject_details($user_id);
                    $director_details      = $this->resource_model->get_admin_subject($user_id);    
                    // log_message('debug','Mentor Area of Intrest :'.$director_details['subject_name']);
                    // $data['resource_details'] = $this->resource_model->get_all_subject_resources($director_details['subject_name']);      
                    $data['resource_details'] = $this->resource_model->get_this_mentor_resources($user_id);      
                    $this->load->view('content_admin/mentor_sme/mentor_resource_view',$data);
                    break;
                case '7':
                    $data['subject_list']    = $this->resource_model->get_all_subjects();
                    $data['resource_details']   = $this->resource_model->get_all_resources();
                    $this->load->view('content_admin/resource_view',$data);
                    break;
                case '8':
                    $data['subject_list']    = $this->resource_model->get_all_subjects();
                    $data['resource_details']   = $this->resource_model->get_all_resources();
                    log_message('debug','Content Director Subject :'.$director_details['subject_name']);
                    $this->load->view('content_admin/resource_view',$data);
                    break;
                default:
                    log_message('debug','Something Went Wrong in mentor_resource_view ');
                    break;
            }
        }
    }

    // Action : Load All Mentor Resource List By USK
    public function all_mentor_resource_list(){
        if($this->input->server('REQUEST_METHOD') == 'POST') {
            // Load Resource view according to USER GM / Content Director
            $user_role = $this->session->userdata('user_role');
            $user_id   = $this->session->userdata('user_id');
            $data['user_role'] = $user_role;
            if($user_role == '6'){
                $data['subject_list']  = $this->resource_model->get_admin_subject_details($user_id);
                $director_details      = $this->resource_model->get_admin_subject($user_id);    
                log_message('debug','Content Director Subject :'.$director_details['subject_name']);
                $data['resource_details'] = $this->resource_model->get_all_subject_resources($director_details['subject_name']);
            }
            if($user_role == '7'){
                $data['subject_list']    = $this->resource_model->get_all_subjects();
                $data['resource_details']   = $this->resource_model->get_all_mentor_resource_list();   
            }
            if($user_role == '8'){
                $data['subject_list']    = $this->resource_model->get_all_subjects();
                $data['resource_details']   = $this->resource_model->get_all_mentor_resource_list();   
            }
            $this->load->view('content_admin/mentor_resource_list',$data);
        }
    }

    // -----------------------------------------------------------------------------------
    //     sriram -> Action : Need to display Resources uploaded by mentor/SME  
    // -----------------------------------------------------------------------------------

    public function all_mentor_resource_list1(){
        if($this->input->server('REQUEST_METHOD') == 'POST') {
            // Load Resource view according to USER GM / Content Director
            $user_role = $this->session->userdata('user_role');
            $user_id   = $this->session->userdata('user_id');
            $data['user_role'] = $user_role;
            if($user_role == '6'){
                $data['subject_list']  = $this->resource_model->get_admin_subject_details($user_id);
                $director_details      = $this->resource_model->get_admin_subject($user_id);    
                log_message('debug','Content Director Subject :'.$director_details['subject_name']);
                $data['resource_details'] = $this->resource_model->get_only_mentor_resource_list($director_details['subject_name']);
            }
            if($user_role == '7'){
                $data['subject_list']    = $this->resource_model->get_all_subjects();
                $data['resource_details']   = $this->resource_model->get_all_mentor_resource_list1();   
            }
            if($user_role == '8'){
                $data['subject_list']    = $this->resource_model->get_all_subjects();
                $data['resource_details']   = $this->resource_model->get_all_mentor_resource_list1();   
            }
            $this->load->view('content_admin/mentor_resource_list',$data);
        }
    }


    // Action : Load Captiva Model View
    public function ajax_add_captiva(){
        log_message('debug','****************** Content Admin AJAX Captiva REQ View START ******************');
        // Load Resource view according to USER GM / Content Director
        $user_role = $this->session->userdata('user_role');
        $user_id   = $this->session->userdata('user_id');
        if($user_role == '6'){
            $data['subject_list']  = $this->resource_model->get_admin_subject_details($user_id);
            $director_details      = $this->resource_model->get_admin_subject($user_id);    
            log_message('debug','Content Director Subject :'.$director_details['subject_name']);
            //$data['resource_details'] = $this->resource_model->get_all_subject_resources($director_details['subject_name']);
            // $this->load->view('content_admin/ajax_captiva_view',$data);
        }
        if($user_role == '7'){
            $data['subject_list']    = $this->resource_model->get_all_subjects();
            // $data['resource_details']   = $this->resource_model->get_all_resources();   
            $this->load->view('content_admin/ajax_captiva_view',$data);
        }
        if($user_role == '8'){
            $data['subject_list']    = $this->resource_model->get_all_subjects();
            $data['resource_details']   = $this->resource_model->get_all_resources();   
            $this->load->view('content_admin/ajax_captiva_view',$data);
        }
        log_message('debug','****************** Content Admin AJAX Captiva REQ View ENDED ******************');
    }

    // Action : Load Captiva Model View
    public function ajax_add_captiva_quiz(){
        log_message('debug','****************** Content Admin AJAX Captiva Quiz REQ View START ******************');
        // Load Resource view according to USER GM / Content Director
        $user_role = $this->session->userdata('user_role');
        $user_id   = $this->session->userdata('user_id');
        if($user_role == '6'){
            $data['subject_list']  = $this->resource_model->get_admin_subject_details($user_id);
            $director_details      = $this->resource_model->get_admin_subject($user_id);    
            log_message('debug','Content Director Subject :'.$director_details['subject_name']);
            //$data['resource_details'] = $this->resource_model->get_all_subject_resources($director_details['subject_name']);
            // $this->load->view('content_admin/ajax_captiva_view',$data);
        }
        if($user_role == '7'){
            $data['subject_list']    = $this->resource_model->get_all_subjects();
            // $data['resource_details']   = $this->resource_model->get_all_resources();   
            $this->load->view('content_admin/ajax_captiva_quiz_view',$data);
        }
        if($user_role == '8'){
            $data['subject_list']    = $this->resource_model->get_all_subjects();
            $data['resource_details']   = $this->resource_model->get_all_resources();   
            $this->load->view('content_admin/ajax_captiva_quiz_view',$data);
        }
        log_message('debug','****************** Content Admin AJAX Captiva Quiz REQ View ENDED ******************');
    }

    // Action : Ajax Captiva File Upload 
    public function ajax_captiva_upload() {
        log_message('debug','****************** AJAX Captiva Upload START ******************');
        $status = "";
        $msg    = "";
        $file_element_name = 'resource_captiva_link';
        $resource['user_id']        = $this->session->userdata('user_id');
        $resource['subject_name']   = $this->input->post('subject_name');
        $resource['resource_name']  = $this->input->post('resource_name');
        $resource['resource_tag']   = $this->input->post('resource_tag');
        $resource['file_type']      = $this->input->post('file_type');

        log_message('debug',' Mentor resource_name :'.$resource['resource_name']);
        log_message('debug',' Mentor resource_tag  :'.$resource['resource_tag']);
        log_message('debug',' Mentor File Type     :'.$resource['file_type']);

        if ($status != "error") {
            $config['upload_path']      = $_SERVER['DOCUMENT_ROOT'].'/static/resource/captiva_zip';
            $config['allowed_types']    = 'zip'; // OLD 'gif|jpg|png|doc|txt';
            $config['max_size']         = 1024 * 200; // only 200 MB file is allowed
            $config['encrypt_name']     = FALSE;
            $captiva_file_name          = "CAPTIVA_".date('MHisa');
            $config['file_name']        = $captiva_file_name;

            $this->load->library('upload', $config);
            if (!$this->upload->do_upload($file_element_name)) {
                $status = 'error';
                $msg = $this->upload->display_errors('', '');
            } else {
                $this->load->library('unzip');
                $captiva_zip = $this->upload->data();
                $image_path  = $captiva_zip['full_path'];
                log_message('debug','file uploaded : '.$captiva_zip['file_name']);

                if(file_exists($image_path)) {
                    // $resource['resource_link'] = $captiva_zip['file_name'];
                    $source_path = $_SERVER['DOCUMENT_ROOT'].'/static/resource/captiva_zip/';
                    $dest_path   = $_SERVER['DOCUMENT_ROOT'].'/static/resource/captiva/';
                    $this_file_name = $captiva_zip['file_name'];
                    $top_dir_name = '';
                    // Create a folder inside captiva 
                    if(mkdir($dest_path.$this_file_name)){
                        $file_name     = $captiva_zip['file_name'];
                        $new_dest_path = $_SERVER['DOCUMENT_ROOT'].'/static/resource/captiva/'.$this_file_name.'/';
                        $file_path   = $source_path.$file_name;
                        log_message('debug','Captiva Zip Uploaded File Name : '.$this_file_name);
                        // Find thie zip file top directory 
                        $top_dir_name = shell_exec("unzip -qql ".$source_path.$this_file_name." | head -n1 | tr -s ' ' | cut -d' ' -f5- ");
                        // $top_dir_name = str_replace('/ar/', '', $top_dir_name);
                        $top_dir_name = strstr($top_dir_name, '/', true);
                        log_message('debug','Top Dir Name : '.$top_dir_name);
                        // Give it one parameter and it will extract to the same folder
                        // or specify a destination directory
                        // if not extracted successfully then throw this error
                        if(!$this->unzip->extract($file_path,$new_dest_path)){
                            $status = "error";
                            $msg = "Extracting Unsuccessful.Please re-upload";       
                            unlink($source_path.$file_name);
                            // exit();
                        } else {
                            $resource['resource_link'] = $this_file_name.'/'.$top_dir_name;
                            $this->resource_model->mentor_upload_file($resource);
                            $status = "success";
                            $msg    = "File successfully uploaded";
                            log_message('debug','File successfully uploaded and unziped');
                            log_message('debug','File successfully unziped @ '.$new_dest_path);
                        }   
                    } else {
                        $status = "error";    
                        $msg = "Something went wrong when saving the file, please try again.";
                    }
                } else {
                    $status = "error";
                    $msg = "Something went wrong when saving the file, please try again.";
                }
            }
            // @unlink($_FILES[$file_element_name]);
        }
        echo json_encode(array('status' => $status, 'msg' => $msg));
        log_message('debug','******************  AJAX Captiva Upload ENDED ******************');
    }

    // Action : Load PDF Model View
    public function ajax_add_pdf(){
        log_message('debug','****************** Content Admin AJAX PDF REQ View START ******************');
        // Load Resource view according to USER GM / Content Director
        $user_role = $this->session->userdata('user_role');
        $user_id   = $this->session->userdata('user_id');
        if($user_role == '6'){
            $data['subject_list']  = $this->resource_model->get_admin_subject_details($user_id);
            $director_details      = $this->resource_model->get_admin_subject($user_id);    
            log_message('debug','Content Director Subject :'.$director_details['subject_name']);
            //$data['resource_details'] = $this->resource_model->get_all_subject_resources($director_details['subject_name']);
        }
        if($user_role == '7'){
            $data['subject_list']    = $this->resource_model->get_all_subjects();
            // $data['resource_details']   = $this->resource_model->get_all_resources();   
        }
        if($user_role == '8'){
            $data['subject_list']    = $this->resource_model->get_all_subjects();
            $data['resource_details']   = $this->resource_model->get_all_resources();   
        }
        $this->load->view('content_admin/ajax_upload_view',$data);
        log_message('debug','****************** Content Admin AJAX PDF REQ View ENDED ******************');
    }

    // Action : Upload PDF Resource 
    public function ajax_upload_file() {
        log_message('debug','****************** Content Admin AJAX PDF Upload START ******************');
        $status = "";
        $msg    = "";
        $file_element_name = 'resource_link';
        $resource['user_id']        = $this->session->userdata('user_id');
        $resource['subject_name']   = $this->input->post('subject_name');
        $resource['resource_name']  = $this->input->post('resource_name');
        $resource['resource_tag']   = $this->input->post('resource_tag');

        log_message('debug',' resource_name :'.$resource['resource_name']);
        log_message('debug',' resource_tag  :'.$resource['resource_tag']);

        if ($status != "error") {
            $config['upload_path']      = $_SERVER['DOCUMENT_ROOT'].'/static/resource/pdf';
            $config['allowed_types']    = 'pdf'; // OLD 'gif|jpg|png|doc|txt';
            $config['max_size']         = 1024 * 200;
            $config['encrypt_name']     = FALSE;

            $this->load->library('upload', $config);
            if (!$this->upload->do_upload($file_element_name)) {
                $status = 'error';
                $msg = $this->upload->display_errors('', '');
            } else {
                $data = $this->upload->data();
                $image_path = $data['full_path'];
                log_message('debug','file uploaded : '.$data['file_name']);

                if(file_exists($image_path)) {
                    $status = "success";
                    $resource['resource_link'] = $data['file_name'];
                    $this->resource_model->add_pdf($resource);
                    $msg = "File successfully uploaded";
                } else {
                    $status = "error";
                    $msg = "Something went wrong when saving the file, please try again.";
                }
            }
            // @unlink($_FILES[$file_element_name]);
        }
        echo json_encode(array('status' => $status, 'msg' => $msg));
        log_message('debug','****************** Content Admin AJAX PDF Upload ENDED ******************');
    }

    // Action : Load Ajax Mentor PDF Model View
    public function ajax_mentor_add_pdf(){
        log_message('debug','*******************************************************************************');
        // Load Resource view according to USER GM / Content Director
        $user_role = $this->session->userdata('user_role');
        $user_id   = $this->session->userdata('user_id');
        if($user_role == '6'){
            $data['subject_list']  = $this->resource_model->get_admin_subject_details($user_id);
            $director_details      = $this->resource_model->get_admin_subject($user_id);    
            // log_message('debug','Content Director Subject :'.$director_details['subject_name']);
            log_message('debug','****************** Mentor/SME AJAX PDF REQ View ENDED ******************');
            //$data['resource_details'] = $this->resource_model->get_all_subject_resources($director_details['subject_name']);
        }
        if($user_role == '7'){
            $data['subject_list']    = $this->resource_model->get_all_subjects();
            // $data['resource_details']   = $this->resource_model->get_all_resources();
            log_message('debug','****************** Super Admin AJAX PDF REQ View ENDED ******************');   
        }
        if($user_role == '8'){
            $data['subject_list']    = $this->resource_model->get_all_subjects();
            $data['resource_details']   = $this->resource_model->get_all_resources();   
            log_message('debug','****************** Content Admin AJAX PDF REQ View ENDED ******************');
        }
        $this->load->view('content_admin/mentor_sme/mentor_ajax_upload_pdf_view',$data);
        log_message('debug','*******************************************************************************');
    }

    // Action : Ajax Mentor PDF file upload
    public function ajax_mentor_pdf_upload() {
        log_message('debug','****************** AJAX Mentor PDF Upload START ******************');
        $status = "";
        $msg    = "";
        $file_element_name = 'resource_pdf_link';
        $resource['user_id']        = $this->session->userdata('user_id');
        $resource['subject_name']   = $this->input->post('subject_name');
        $resource['resource_name']  = $this->input->post('resource_name');
        $resource['resource_tag']   = $this->input->post('resource_tag');
        $resource['file_type']      = $this->input->post('file_type');

        log_message('debug',' Mentor resource_name :'.$resource['resource_name']);
        log_message('debug',' Mentor resource_tag  :'.$resource['resource_tag']);
        log_message('debug',' Mentor File Type     :'.$resource['file_type']);

        if ($status != "error") {
            $config['upload_path']      = $_SERVER['DOCUMENT_ROOT'].'/static/resource/pdf';
            $config['allowed_types']    = 'pdf'; // OLD 'gif|jpg|png|doc|txt';
            $config['max_size']         = 1024 * 200;
            $config['encrypt_name']     = FALSE;

            $this->load->library('upload', $config);
            if (!$this->upload->do_upload($file_element_name)) {
                $status = 'error';
                $msg = $this->upload->display_errors('', '');
            } else {
                $data = $this->upload->data();
                $image_path = $data['full_path'];
                log_message('debug','file uploaded : '.$data['file_name']);

                if(file_exists($image_path)) {
                    $status = "success";
                    $resource['resource_link'] = $data['file_name'];
                    $this->resource_model->mentor_upload_file($resource);
                    $msg = "File successfully uploaded";
                } else {
                    $status = "error";
                    $msg = "Something went wrong when saving the file, please try again.";
                }
            }
            // @unlink($_FILES[$file_element_name]);
        }
        echo json_encode(array('status' => $status, 'msg' => $msg));
        log_message('debug','****************** AJAX Mentor PDF Upload ENDED ******************');
    }

    // Action : Load Ajax Mentor PPT Model View
    public function ajax_mentor_add_ppt(){
        log_message('debug','*******************************************************************************');
        // Load Resource view according to USER GM / Content Director
        $user_role = $this->session->userdata('user_role');
        $user_id   = $this->session->userdata('user_id');
        if($user_role == '6'){
            $data['subject_list']  = $this->resource_model->get_admin_subject_details($user_id);
            $director_details      = $this->resource_model->get_admin_subject($user_id);    
            // log_message('debug','Content Director Subject :'.$director_details['subject_name']);
            log_message('debug','****************** Mentor/SME AJAX PPT REQ View ENDED ******************');
            //$data['resource_details'] = $this->resource_model->get_all_subject_resources($director_details['subject_name']);
        }
        if($user_role == '7'){
            $data['subject_list']    = $this->resource_model->get_all_subjects();
            // $data['resource_details']   = $this->resource_model->get_all_resources();   
            log_message('debug','****************** Super Admin AJAX PPT REQ View ENDED ******************');
        }
        if($user_role == '8'){
            $data['subject_list']     = $this->resource_model->get_all_subjects();
            $data['resource_details'] = $this->resource_model->get_all_resources();   
            log_message('debug','****************** Content Admin AJAX PPT REQ View ENDED ******************');
        }
        $this->load->view('content_admin/mentor_sme/mentor_ajax_upload_ppt_view',$data);
        log_message('debug','*******************************************************************************');
    }

    // Action : Ajax Mentor PPT File Upload 
    public function ajax_mentor_ppt_upload() {
        log_message('debug','****************** AJAX Mentor PPT Upload START ******************');
        $status = "";
        $msg    = "";
        $file_element_name = 'resource_ppt_link';
        $resource['user_id']        = $this->session->userdata('user_id');
        $resource['subject_name']   = $this->input->post('subject_name');
        $resource['resource_name']  = $this->input->post('resource_name');
        $resource['resource_tag']   = $this->input->post('resource_tag');
        $resource['file_type']      = $this->input->post('file_type');

        log_message('debug',' Mentor resource_name :'.$resource['resource_name']);
        log_message('debug',' Mentor resource_tag  :'.$resource['resource_tag']);
        log_message('debug',' Mentor File Type     :'.$resource['file_type']);

        if ($status != "error") {
            $config['upload_path']      = $_SERVER['DOCUMENT_ROOT'].'/static/resource/ppt';
            $config['allowed_types']    = 'ppt|pptx'; // OLD 'gif|jpg|png|doc|txt';
            $config['max_size']         = 1024 * 200;
            $config['encrypt_name']     = FALSE;

            $this->load->library('upload', $config);
            if (!$this->upload->do_upload($file_element_name)) {
                $status = 'error';
                $msg = $this->upload->display_errors('', '');
            } else {
                $data = $this->upload->data();
                $image_path = $data['full_path'];
                log_message('debug','file uploaded : '.$data['file_name']);

                if(file_exists($image_path)) {
                    $status = "success";
                    $resource['resource_link'] = $data['file_name'];
                    $this->resource_model->mentor_upload_file($resource);
                    $msg = "File successfully uploaded";
                } else {
                    $status = "error";
                    $msg = "Something went wrong when saving the file, please try again.";
                }
            }
            // @unlink($_FILES[$file_element_name]);
        }
        echo json_encode(array('status' => $status, 'msg' => $msg));
        log_message('debug','******************  AJAX Mentor PPT Upload ENDED ******************');
    }

    // Action : Load Ajax Mentor Audio Model View
    public function ajax_mentor_add_audio(){
        log_message('debug','*******************************************************************************');
        // Load Resource view according to USER GM / Content Director
        $user_role = $this->session->userdata('user_role');
        $user_id   = $this->session->userdata('user_id');
        if($user_role == '6'){
            $data['subject_list']  = $this->resource_model->get_admin_subject_details($user_id);
            $director_details      = $this->resource_model->get_admin_subject($user_id);    
            // log_message('debug','Content Director Subject :'.$director_details['subject_name']);
            log_message('debug','****************** Mentor/SME AJAX Audio REQ View ENDED ******************');
            //$data['resource_details'] = $this->resource_model->get_all_subject_resources($director_details['subject_name']);
        }
        if($user_role == '7'){
            $data['subject_list']    = $this->resource_model->get_all_subjects();
            // $data['resource_details']   = $this->resource_model->get_all_resources();   
            log_message('debug','****************** Super Admin AJAX Audio REQ View ENDED ******************');
        }
        if($user_role == '8'){
            $data['subject_list']     = $this->resource_model->get_all_subjects();
            $data['resource_details'] = $this->resource_model->get_all_resources();   
            log_message('debug','****************** Content Admin AJAX Audio REQ View ENDED ******************');
        }
        $this->load->view('content_admin/mentor_sme/mentor_ajax_upload_audio',$data);
        log_message('debug','*******************************************************************************');
    }

    // Action : Ajax Mentor Audio File Upload
    public function ajax_mentor_audio_upload() {
        log_message('debug','****************** AJAX Mentor AUDIO Upload START ******************');
        $status = "";
        $msg    = "";
        $file_element_name = 'resource_audio_link';
        $resource['user_id']        = $this->session->userdata('user_id');
        $resource['subject_name']   = $this->input->post('subject_name');
        $resource['resource_name']  = $this->input->post('resource_name');
        $resource['resource_tag']   = $this->input->post('resource_tag');
        $resource['file_type']      = $this->input->post('file_type');

        log_message('debug',' Mentor resource_name :'.$resource['resource_name']);
        log_message('debug',' Mentor resource_tag  :'.$resource['resource_tag']);
        log_message('debug',' Mentor File Type     :'.$resource['file_type']);

        if ($status != "error") {
            $config['upload_path']      = $_SERVER['DOCUMENT_ROOT'].'/static/resource/audio';
            $config['allowed_types']    = 'mp3'; // OLD 'gif|jpg|png|doc|txt';
            $config['max_size']         = 1024 * 200;
            $config['encrypt_name']     = FALSE;

            $this->load->library('upload', $config);
            if (!$this->upload->do_upload($file_element_name)) {
                $status = 'error';
                $msg = $this->upload->display_errors('', '');
            } else {
                $data = $this->upload->data();
                $image_path = $data['full_path'];
                log_message('debug','file uploaded : '.$data['file_name']);

                if(file_exists($image_path)) {
                    $status = "success";
                    $resource['resource_link'] = $data['file_name'];
                    $this->resource_model->mentor_upload_file($resource);
                    $msg = "File successfully uploaded";
                } else {
                    $status = "error";
                    $msg = "Something went wrong when saving the file, please try again.";
                }
            }
            // @unlink($_FILES[$file_element_name]);
        }
        echo json_encode(array('status' => $status, 'msg' => $msg));
        log_message('debug','******************  AJAX Mentor AUDIO Upload ENDED ******************');
    }

    // Action : Load Ajax Mentor Video Model View
    public function ajax_mentor_add_video(){
        log_message('debug','*******************************************************************************');
        // Load Resource view according to USER GM / Content Director
        $user_role = $this->session->userdata('user_role');
        $user_id   = $this->session->userdata('user_id');
        if($user_role == '6'){
            $data['subject_list']  = $this->resource_model->get_admin_subject_details($user_id);
            $director_details      = $this->resource_model->get_admin_subject($user_id);    
            // log_message('debug','Content Director Subject :'.$director_details['subject_name']);
            log_message('debug','****************** Mentor/SME AJAX Video REQ View ENDED ******************');
            //$data['resource_details'] = $this->resource_model->get_all_subject_resources($director_details['subject_name']);
        }
        if($user_role == '7'){
            $data['subject_list']    = $this->resource_model->get_all_subjects();
            // $data['resource_details']   = $this->resource_model->get_all_resources();   
            log_message('debug','****************** Super Admin AJAX Video REQ View ENDED ******************');
        }
        if($user_role == '8'){
            $data['subject_list']     = $this->resource_model->get_all_subjects();
            $data['resource_details'] = $this->resource_model->get_all_resources();   
            log_message('debug','****************** Content Admin AJAX Video REQ View ENDED ******************');
        }
        $this->load->view('content_admin/mentor_sme/mentor_ajax_upload_video_file',$data);
        log_message('debug','*******************************************************************************');
    }

    // Action : Ajax Mentor Video File Upload 
    public function ajax_mentor_video_upload() {
        log_message('debug','****************** AJAX Mentor Video Upload START ******************');
        $status = "";
        $msg    = "";
        $file_element_name = 'resource_video_link';
        $resource['user_id']        = $this->session->userdata('user_id');
        $resource['subject_name']   = $this->input->post('subject_name');
        $resource['resource_name']  = $this->input->post('resource_name');
        $resource['resource_tag']   = $this->input->post('resource_tag');
        $resource['file_type']      = $this->input->post('file_type');

        log_message('debug',' Mentor resource_name :'.$resource['resource_name']);
        log_message('debug',' Mentor resource_tag  :'.$resource['resource_tag']);
        log_message('debug',' Mentor File Type     :'.$resource['file_type']);

        if ($status != "error") {
            $config['upload_path']      = $_SERVER['DOCUMENT_ROOT'].'/static/resource/video';
            $config['allowed_types']    = 'mp4'; // OLD 'gif|jpg|png|doc|txt';
            $config['max_size']         = 1024 * 200;
            $config['encrypt_name']     = FALSE;

            $this->load->library('upload', $config);
            if (!$this->upload->do_upload($file_element_name)) {
                $status = 'error';
                $msg = $this->upload->display_errors('', '');
            } else {
                $data = $this->upload->data();
                $image_path = $data['full_path'];
                log_message('debug','file uploaded : '.$data['file_name']);

                if(file_exists($image_path)) {
                    $status = "success";
                    $resource['resource_link'] = $data['file_name'];
                    $this->resource_model->mentor_upload_file($resource);
                    $msg = "File successfully uploaded";
                } else {
                    $status = "error";
                    $msg = "Something went wrong when saving the file, please try again.";
                }
            }
            // @unlink($_FILES[$file_element_name]);
        }
        echo json_encode(array('status' => $status, 'msg' => $msg));
        log_message('debug','******************  AJAX Mentor Video Upload ENDED ******************');
    }

    // Action : Load Video Modal View
    public function add_video_modal(){
        log_message('debug','****************** Content Admin Video Modal REQ View START ******************');
        // Load Resource view according to USER GM / Content Director
        $user_role = $this->session->userdata('user_role');
        $user_id   = $this->session->userdata('user_id');
        if($user_role == '6'){
            $data['subject_list']  = $this->resource_model->get_admin_subject_details($user_id);
            $director_details      = $this->resource_model->get_admin_subject($user_id);    
            // log_message('debug','Content Director Subject :'.$director_details['subject_name']);
            //$data['resource_details'] = $this->resource_model->get_all_subject_resources($director_details['subject_name']);
        }
        if($user_role == '7'){
            $data['subject_list']    = $this->resource_model->get_all_subjects();
            //$data['resource_details']   = $this->resource_model->get_all_resources();   
        }
        if($user_role == '8'){
            $data['subject_list']    = $this->resource_model->get_all_subjects();
            //$data['resource_details']   = $this->resource_model->get_all_resources();   
        }
        $this->load->view('content_admin/add_video_modal',$data);
        log_message('debug','****************** Content Admin Video Modal REQ View ENDED ******************');
    }
    
    // ---------------------------------------------------------------------------
    // Mentor Download File option
    // ---------------------------------------------------------------------------
    public function mentor_download_file(){
        if($this->input->server('REQUEST_METHOD') == 'GET'){
            $user_id = $this->session->userdata('user_id');
            $res_id  = $this->input->get('res_id');
            $resource_details = $this->resource_model->get_resource_details($res_id);
            $user_details     = $this->resource_model->who_uploaded_this_resource($res_id);
            log_message('debug','Mentor File Download Res ID: '.$res_id);
            log_message('debug','Mentor File Resource Details: '.print_r($user_details),true);
            switch ($resource_details['resource_type']) {
                case 'PDF':
                    $Location  = $_SERVER['DOCUMENT_ROOT'].'/static/resource/pdf';
                    $file_name = $resource_details['resource_link'];
                    $file_location = $Location.'/'.$file_name;
                    $download_as = $user_details['registration_no'].'_'.$file_name;
                    $mime_type = '';
                    log_message('debug','PDF Download link : '.$file_location.' Name'.$file_name);
                    $this->pptDownload($file_location,$file_name,$mime_type,$download_as);
                    break;
                case 'PPT':
                    $Location  = $_SERVER['DOCUMENT_ROOT'].'/static/resource/ppt';
                    $file_name = $resource_details['resource_link'];
                    $file_location = $Location.'/'.$file_name;
                    $download_as = $user_details['registration_no'].'_'.$file_name;
                    $mime_type = '';
                    log_message('debug','PPT Download link : '.$file_location.' Name '.$file_name);
                    $this->pptDownload($file_location,$file_name,$mime_type,$download_as);
                    break;
                case 'VIDEO_FILE':
                    $Location  = $_SERVER['DOCUMENT_ROOT'].'/static/resource/video';
                    $file_name = $resource_details['resource_link'];
                    $file_location = $Location.'/'.$file_name;
                    $download_as = $user_details['registration_no'].'_'.$file_name;
                    $mime_type = '';
                    log_message('debug','Video Download link : '.$file_location.' Name'.$file_name);
                    $this->pptDownload($file_location,$file_name,$mime_type,$download_as);
                    break;
                case 'AUDIO':
                    $Location  = $_SERVER['DOCUMENT_ROOT'].'/static/resource/audio';
                    $file_name = $resource_details['resource_link'];
                    $file_location = $Location.'/'.$file_name;
                    $download_as = $user_details['registration_no'].'_'.$file_name;
                    $mime_type = '';
                    log_message('debug','Audio Download link : '.$file_location.' Name'.$file_name);
                    $this->pptDownload($file_location,$file_name,$mime_type,$download_as);
                    break;
                case 'CAPTIVA':
                    $Location   = $_SERVER['DOCUMENT_ROOT'].'/static/resource/captiva_zip';
                    $file_name  = $resource_details['resource_link'];
                    $file_name  = explode('/', $file_name);
                    $file_location = $Location.'/'.$file_name[0];
                    $download_as = $user_details['registration_no'].'_'.$file_name[0];
                    $mime_type = '';
                    log_message('debug','CAPTIVA Download link : '.$file_location.' Name'.$file_name);
                    $this->pptDownload($file_location,$file_name[0],$mime_type,$download_as);
                    break;
                case 'CAPTIVA_QUIZ':
                    $Location   = $_SERVER['DOCUMENT_ROOT'].'/static/resource/captiva_zip';
                    $file_name  = $resource_details['resource_link'];
                    $file_name  = explode('/', $file_name);
                    $file_location = $Location.'/'.$file_name[0];
                    $download_as = $user_details['registration_no'].'_'.$file_name[0];
                    $mime_type = '';
                    log_message('debug','CAPTIVA QUIZ Download link : '.$file_location.' Name'.$file_name);
                    $this->pptDownload($file_location,$file_name[0],$mime_type,$download_as);
                    break;
                default:
                    log_message('debug','During File Download Something Went Wrong !');
                    break;
            }
        }
    }

    // sriram : operation : Download button in Content admin download file
    public function mentor_assessment_download_file(){
        if($this->input->server('REQUEST_METHOD') == 'GET'){
            $user_id = $this->session->userdata('user_id');
            $test_id  = $this->input->get('file_id');
            $assessment_details = $this->resource_model->get_assessment_details($test_id);
            $user_details     = $this->resource_model->who_uploaded_this_assessment($test_id);
            log_message('debug','Mentor File Download Res ID: '.$test_id);
            log_message('debug','Mentor File Resource Details: '.print_r($user_details),true);
            switch ($assessment_details['test_type']) {
                case 'PDF':
                    $Location  = $_SERVER['DOCUMENT_ROOT'].'/static/resource/questions';
                    $file_name = $assessment_details['upload_ques_paper'];
                    $file_location = $Location.'/'.$file_name;
                    $download_as = $user_details['registration_no'].'_'.$file_name;
                    $mime_type = '';
                    log_message('debug','PDF Download link : '.$file_location.' Name'.$file_name);
                    $this->pptDownload($file_location,$file_name,$mime_type,$download_as);
                    break;
                case 'CAPTIVA_QUIZ':
                    $Location   = $_SERVER['DOCUMENT_ROOT'].'/static/resource/captiva_zip';
                    $file_name  = $assessment_details['upload_ques_paper'];
                    $file_name  = explode('/', $file_name);
                    $file_location = $Location.'/'.$file_name[0];
                    $download_as = $user_details['registration_no'].'_'.$file_name[0];
                    $mime_type = '';
                    log_message('debug','CAPTIVA QUIZ Download link : '.$file_location.' Name'.$file_name);
                    $this->pptDownload($file_location,$file_name[0],$mime_type,$download_as);
                    break;
                default:
                    // By default it will download PDF files .....
                    $Location  = $_SERVER['DOCUMENT_ROOT'].'/static/resource/questions';
                    $file_name = $assessment_details['upload_ques_paper'];
                    $file_location = $Location.'/'.$file_name;
                    $download_as = $user_details['registration_no'].'_'.$file_name;
                    $mime_type = '';
                    log_message('debug','PDF Download link : '.$file_location.' Name'.$file_name);
                    $this->pptDownload($file_location,$file_name,$mime_type,$download_as);
                    log_message('debug','During File Download Something Went Wrong !');
                    break;
            }
        }
    }

    // Action : For downloading ppt 
    function pptDownload($file, $name, $mime_type='',$download_as){
        log_message('debug','pptDownload file called ');
        //Check the file premission
        if(!is_readable($file)) die('File not found or inaccessible!');
        $size = filesize($file);
        $name = rawurldecode($name);
        $user_role = $this->session->userdata('user_role');
        switch ($user_role) {
            case '6':
                log_message('debug','mentor_sme is downloading : '.$name);
                $download_as = $name;
                break;
            case '7':
                $download_as = $download_as;
                break;
            case '8':
                $download_as = $download_as;
                break;
            default:
                $download_as = $download_as;
                break;
        }
         
        /* Figure out the MIME type | Check in array */
        $known_mime_types=array(
            "pdf" => "application/pdf",
            "html" => "text/html",
            "exe" => "application/octet-stream",
            "mp3" => "audio/mpeg3",
            "mp4" => "video/mp4",
            "doc" => "application/msword",
            "xls" => "application/vnd.ms-excel",
            "ppt" => "application/vnd.ms-powerpoint",
            "pptx" => "application/vnd.openxmlformats-officedocument.presentationml.presentation; charset=binary",
            "gif" => "image/gif",
            "png" => "image/png",
            "jpeg"=> "image/jpg",
            "jpg" =>  "image/jpg",
            "zip" => "application/zip"
        );
     
        if($mime_type==''){
            $file_extension = strtolower(substr(strrchr($file,"."),1));
            if(array_key_exists($file_extension, $known_mime_types)){
                $mime_type=$known_mime_types[$file_extension];
            } else {
                $mime_type="application/force-download";
            };
        };
     
        //turn off output buffering to decrease cpu usage
        @ob_end_clean(); 
     
        // required for IE, otherwise Content-Disposition may be ignored
        if(ini_get('zlib.output_compression'))
        ini_set('zlib.output_compression', 'Off');
         
        header('Content-Type: ' . $mime_type);
        // header('Content-Disposition: attachment; filename="'.$name.'"');
        header('Content-Disposition: attachment; filename="'.$download_as.'"');
        header("Content-Transfer-Encoding: binary");
        header('Accept-Ranges: bytes');
         
        /* The three lines below basically make the 
           download non-cacheable */
        header("Cache-control: private");
        header('Pragma: private');
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
         
        // multipart-download and download resuming support
        if(isset($_SERVER['HTTP_RANGE']))
        {
           list($a, $range) = explode("=",$_SERVER['HTTP_RANGE'],2);
           list($range) = explode(",",$range,2);
           list($range, $range_end) = explode("-", $range);
           $range=intval($range);
           if(!$range_end) {
               $range_end=$size-1;
           } else {
               $range_end=intval($range_end);
           }
           /*
           ------------------------------------------------------------------------------------------------------
           //This application is developed by www.webinfopedia.com
           //visit www.webinfopedia.com for PHP,Mysql,html5 and Designing tutorials for FREE!!!
           ------------------------------------------------------------------------------------------------------
           */
           $new_length = $range_end-$range+1;
           header("HTTP/1.1 206 Partial Content");
           header("Content-Length: $new_length");
           header("Content-Range: bytes $range-$range_end/$size");
        } else {
           $new_length=$size;
           header("Content-Length: ".$size);
        }
         
        /* Will output the file itself */
        $chunksize = 1*(1024*1024); //you may want to change this
        $bytes_send = 0;
        if ($file = fopen($file, 'r'))
        {
           if(isset($_SERVER['HTTP_RANGE']))
           fseek($file, $range);
        
           while(!feof($file) && (!connection_aborted()) && ($bytes_send<$new_length))
           {
               $buffer = fread($file, $chunksize);
               print($buffer); //echo($buffer); // can also possible
               flush();
               $bytes_send += strlen($buffer);
           }
           fclose($file);
        } else
        //If no permissiion
        die('Error - can not open file.');
    }
    // ---------------------------------------------------------------------------
    

    // ---------------------------------------------------------------------------
    // Delete Captiva Files 
    // ---------------------------------------------------------------------------
    function delete_captiva_files($file_location){
        log_message('debug','CAPTIVA File Delete Called');
        log_message('debug','CAPTIVA File Location :'.$file_location);
        shell_exec("rm -rf ".escapeshellarg($file_location));
        return true;
    }
    // ---------------------------------------------------------------------------

    // ---------------------------------------------------------------------------
    //  Multiple file type RND : for Mentor Resource Upload
    // ---------------------------------------------------------------------------
    // Action : Upload PPT Resource 
    public function ajax_mentor_upload_file() {
        log_message('debug','****************** Content Admin AJAX PPT Upload START ******************');
        $status = "";
        $msg    = "";
        $file_element_name = ''; // resource_file_link
        $resource['user_id']        = $this->session->userdata('user_id');
        $resource['subject_name']   = $this->input->post('subject_name');
        $resource['resource_name']  = $this->input->post('resource_name');
        $resource['resource_tag']   = $this->input->post('resource_tag');
        $resource['file_type']      = $this->input->post('file_type');
        
        log_message('debug',' resource_name :'.$resource['resource_name']);
        log_message('debug',' File Type     :'.$resource['file_type']);
        log_message('debug',' resource_tag  :'.$resource['resource_tag']);
        
        // Configure Upload Destination Based on File Type 
        switch ($resource['file_type']) {
            case 'PDF':
                $config['upload_path'] = $_SERVER['DOCUMENT_ROOT'].'/static/resource/pdf';
                $file_element_name = 'resource_pdf_link';
                log_message('debug','PDF link :'.$file_element_name);
                break;
            case 'PPT':
                $config['upload_path'] = $_SERVER['DOCUMENT_ROOT'].'/static/resource/ppt';
                $file_element_name = 'resource_ppt_link';
                break;
            case 'VIDEO_FILE':
                $config['upload_path'] = $_SERVER['DOCUMENT_ROOT'].'/static/resource/video';
                $file_element_name = 'resource_video_link';
                break;
            case 'AUDIO':
                $config['upload_path'] = $_SERVER['DOCUMENT_ROOT'].'/static/resource/audio';
                $file_element_name = 'resource_audio_link';
                break;
            default:
                $status = "error";
                $msg = "Something went wrong when saving the file, please try again.";
                log_message('debug','During File Upload Something Went Wrong !');
                break;
        }

        if ($status != "error") {
            // $config['upload_path']      = $_SERVER['DOCUMENT_ROOT'].'/static/resource/ppt';
            $config['allowed_types']    = 'pdf|ppt|mp3|mp4'; // OLD 'gif|jpg|png|doc|txt';
            $config['max_size']         = 1024 * 200;
            $config['encrypt_name']     = FALSE;

            $this->load->library('upload', $config);
            if (!$this->upload->do_upload($file_element_name)) {
                $status = 'error';
                $msg = $this->upload->display_errors('', '');
            } else {
                $data = $this->upload->data();
                $image_path = $data['full_path'];
                log_message('debug','file uploaded : '.$data['file_name']);

                if(file_exists($image_path)) {
                    $status = "success";
                    $resource['resource_link'] = $data['file_name'];
                    $this->resource_model->mentor_upload_file($resource);
                    $msg = "File successfully uploaded";
                } else {
                    $status = "error";
                    $msg = "Something went wrong when saving the file, please try again.";
                }
            }
            // @unlink($_FILES[$file_element_name]);
        }
        echo json_encode(array('status' => $status, 'msg' => $msg));
        log_message('debug','****************** Content Admin AJAX PPT Upload ENDED ******************');
    }
    // ---------------------------------------------------------------------------

    // Action : Add Video Resource Link
    public function add_video_resource() {

        log_message('debug','Content Admin Add Video Resource Called');
        if($this->input->server('REQUEST_METHOD') == 'POST') {
            log_message('debug',"Content Admin POST Video Resource Called");
            $resource['user_id']        = $this->session->userdata('user_id');
            $resource['subject_name']   = $this->input->post('subject_name');
            $resource['resource_name']  = $this->input->post('resource_name');
            $resource['resource_link']  = $this->input->post('resource_link');
            $resource['resource_tag']   = $this->input->post('resource_tag');
            // add data in resource master table 
            $result = $this->resource_model->add_video($resource);

            log_message('debug',"Video Resource result ".$result);

            if($result == 1) { 
                echo "true";
            } else { echo "false"; }
        }
    }

    //Action: Load Edit Res Modal View
    public function edit_res_modal(){
        if($this->input->server('REQUEST_METHOD') == 'POST') {
            $resource_id = $this->input->post('res_id');
            $data['resource_details'] = $this->resource_model->get_resource_details($resource_id);
            log_message('debug','****************** Content Admin Edit Res Modal REQ View START ******************');
            $this->load->view('content_admin/edit_res_modal',$data);
            log_message('debug','****************** Content Admin Edit Res Modal REQ View ENDED ******************');
        }
        
    }

    // Action : Check Weather Assessment is Mapped and Punlished 
    public function isAssessmentNotPublished($test_id){
        $result = $this->resource_model->isAssessmentNotPublished($test_id);
        if($result != true){
            return true;
        }
        return false;
    }
    // Action : Load Edit Assessment Modal View
    public function edit_assessment_modal(){
        log_message('debug','****************** Content Admin Edit Assessment Modal REQ View START ******************');
        $user_id   = $this->session->userdata('user_id');
        $user_role = $this->session->userdata('user_role');
        $test_id   = $this->input->post('test_id');
        $assessment_status = $this->isAssessmentNotPublished($test_id);
        if($assessment_status){
            if($user_role == '6'){
                $data['assessment_details'] = $this->resource_model->get_assessment_details($test_id);
                $data['subject_list']  = $this->resource_model->get_admin_subject_details($user_id);
                $director_details      = $this->resource_model->get_admin_subject($user_id);
            }
            if($user_role == '7'){
                $data['assessment_details'] = $this->resource_model->get_assessment_details($test_id);
                $data['subject_list'] = $this->resource_model->get_all_subjects();
                log_message('debug','-- Question Mapped By Admin --');
            }
            if($user_role == '8'){
                $data['assessment_details'] = $this->resource_model->get_assessment_details($test_id);
                $data['subject_list'] = $this->resource_model->get_all_subjects();
                log_message('debug','-- Question Mapped By Admin --');
            }
            $this->load->view('content_admin/edit_assessment_modal',$data);
        } else {
            $this->load->view('content_admin/cannot_edit_assessment_modal');
        }

        log_message('debug','****************** Content Admin Edit Assessment Modal REQ View ENDED ******************');
    }

    // Action : Edit Resource Link
    public function update_resource() {

        log_message('debug','Content Admin Update Resource Called');
        if($this->input->server('REQUEST_METHOD') == 'POST') {
            log_message('debug',"Content Admin POST Update Resource Called");
            $resource_name  = $this->input->post('resource_name');
            $resource_tag   = $this->input->post('resource_tag');
            $resource_id    = $this->input->post('resource_id');
            // Edit data in resource master table 
            $result = $this->resource_model->update_resource_details($resource_id,$resource_name,$resource_tag);

            log_message('debug',"Update Resource result ".$result);

            if($result == 1) { 
                echo "true";
            } else { echo "false"; }
        }
    }



    // Action : Delete Resource Link
    public function delete_resource() {
        log_message('debug','Content Admin Delete Resource Called');
        
        if($this->input->server('REQUEST_METHOD') == 'POST') {
            log_message('debug',"Content Admin POST delete Resource Called");

            $resource_id  = $this->input->post('resource_id');
            $res_details  = $this->resource_model->get_resource_details($resource_id);
            
            switch ($res_details['resource_type']) {
                case 'PDF':
                    $pdf_file   = $res_details['resource_link'];
                    $file_path  = $_SERVER['DOCUMENT_ROOT'].'/static/resource/pdf';
                    $this->deleteFiles($file_path,$pdf_file);
                    $result = $this->resource_model->delete_resource($resource_id); 
                    if($result == 1) { 
                        log_message('debug','PDF Resource Deleted ');
                        echo "true";
                    } else { 
                        echo "false"; 
                    } 
                    break;
                case 'PPT':
                    $pdf_file   = $res_details['resource_link'];
                    $file_path  = $_SERVER['DOCUMENT_ROOT'].'/static/resource/ppt';
                    $this->deleteFiles($file_path,$pdf_file);
                    $result = $this->resource_model->delete_resource($resource_id); 
                    if($result == 1) { 
                        log_message('debug','PPT Resource Deleted ');
                        echo "true";
                    } else { 
                        echo "false"; 
                    } 
                    break;
                case 'VIDEO': 
                    $result = $this->resource_model->delete_resource($resource_id);
                    if($result == 1) {
                        log_message('debug','Video Resource Deleted ');
                        echo "true";  
                    } else {
                        echo "false";
                    } 
                    break;
                case 'TEST':
                    $pdf_file   = $res_details['resource_link'];
                    $file_path  = $_SERVER['DOCUMENT_ROOT'].'/static/resource/questions';
                    $this->deleteFiles($file_path,$pdf_file);
                    log_message('debug','Deleted test ');
                    $result = $this->resource_model->delete_resource($resource_id);
                    if($result == 1) {
                        log_message('debug','TEST Resource Deleted ');
                        echo "true";  
                    } else {
                        echo "false";
                    } 
                    break;
                case 'VIDEO_FILE':
                    $pdf_file   = $res_details['resource_link'];
                    $file_path  = $_SERVER['DOCUMENT_ROOT'].'/static/resource/video';
                    $this->deleteFiles($file_path,$pdf_file);
                    $result = $this->resource_model->delete_resource($resource_id); 
                    if($result == 1) { 
                        log_message('debug','VIDEO_FILE Resource Deleted ');
                        echo "true";
                    } else { 
                        echo "false"; 
                    } 
                    break;
                case 'AUDIO':
                    $pdf_file   = $res_details['resource_link'];
                    $file_path  = $_SERVER['DOCUMENT_ROOT'].'/static/resource/audio';
                    $this->deleteFiles($file_path,$pdf_file);
                    $result = $this->resource_model->delete_resource($resource_id); 
                    if($result == 1) { 
                        log_message('debug','AUDIO Resource Deleted ');
                        echo "true";
                    } else { 
                        echo "false"; 
                    } 
                    break;
                case 'CAPTIVA':
                    $captiva_file  = $res_details['resource_link'];
                    $captiva_zip   = explode('/', $captiva_file);
                    $file_path     = $_SERVER['DOCUMENT_ROOT'].'/static/resource/captiva_zip';               // Captiva Zip Location
                    $file_location = $_SERVER['DOCUMENT_ROOT'].'/static/resource/captiva/'.$captiva_zip[0];  // Captiva Folder Location 
                    $this->deleteFiles($file_path,$captiva_zip[0]);
                    if($this->delete_captiva_files($file_location)){
                        // Deleting Captiva Folder deleted
                        $result = $this->resource_model->delete_resource($resource_id); 
                        if($result == 1) { 
                            log_message('debug','CAPTIVA File Removed & Resource Record Deleted successfully !');
                            echo "true";
                        } else { 
                            log_message('debug','DB Error : Unable to Delete Record !');
                            echo "false"; 
                        }     
                    } else {
                        log_message('debug','CAPTIVA Resource Deleting Problem @ '.$captiva_file);
                        echo "false";
                    }
                    break;
                case 'CAPTIVA_QUIZ':
                    $captiva_file  = $res_details['resource_link'];
                    $captiva_zip   = explode('/', $captiva_file);
                    $file_path     = $_SERVER['DOCUMENT_ROOT'].'/static/resource/captiva_zip';               // Captiva Zip Location
                    $file_location = $_SERVER['DOCUMENT_ROOT'].'/static/resource/captiva/'.$captiva_zip[0];  // Captiva Folder Location 
                    $this->deleteFiles($file_path,$captiva_zip[0]);
                    if($this->delete_captiva_files($file_location)){
                        // Deleting Captiva Folder deleted
                        $result = $this->resource_model->delete_resource($resource_id); 
                        if($result == 1) { 
                            log_message('debug','CAPTIVA QUIZ File Removed & Resource Record Deleted successfully !');
                            echo "true";
                        } else { 
                            log_message('debug','DB Error : Unable to Delete Record !');
                            echo "false"; 
                        }     
                    } else {
                        log_message('debug','CAPTIVA QUIZ Resource Deleting Problem @ '.$captiva_file);
                        echo "false";
                    }
                    break;

                default:
                    log_message('debug','UNKnown Resource ');
                    break;
            }
        }
    }

    // Action : Delete File @Server
    function deleteFiles($path,$file_name){
        unlink($path.'/'.$file_name); 
        log_message('debug','File Deleted @ '.$path.'/'.$file_name);
    }
    //---------------------------------------------------------------------------------------//



    //---------------------------------------------------------------------------------------//
    // Assessment Management  
    //---------------------------------------------------------------------------------------//

    // Action : Load Assessment View Page
    public function assessment_list1() {
        if($this->input->server('REQUEST_METHOD') == 'POST') {
            $user_id   = $this->session->userdata('user_id');         
            $user_role = $this->session->userdata('user_role');
            $data['user_role'] = $user_role;
            if($user_role == '6'){
                // $data['subject_list']  = $this->resource_model->get_admin_subject_details($user_id);
                $director_details      = $this->resource_model->get_admin_subject($user_id);    
                // log_message('debug','Content Director Subject :'.$director_details['subject_name']);
                // $data['assessment_details'] = $this->resource_model->get_all_subject_assessments($director_details['subject_name']);
                $data['assessment_details'] = $this->resource_model->get_this_mentor_assessments($user_id);
            }
            if($user_role == '7'){
                $data['assessment_details'] = $this->resource_model->get_all_admins_assessment();   
                log_message('debug','-- Question Mapped By Admin --');
            }
            if($user_role == '8'){
                $data['assessment_details'] = $this->resource_model->get_all_admins_assessment();   
                log_message('debug','-- Question Mapped By Admin --');
            }
            // $data['assessment_details'] = $this->resource_model->get_all_assessment();
            $this->load->view('content_admin/assessment_list',$data);
        }
    }

    // Action : Load Assessment View Page
    public function assessment_list() {
        if($this->input->server('REQUEST_METHOD') == 'POST') {
            $user_id   = $this->session->userdata('user_id');         
            $user_role = $this->session->userdata('user_role');
            $data['user_role'] = $user_role;
            if($user_role == '6'){
                // $data['subject_list']  = $this->resource_model->get_admin_subject_details($user_id);
                $director_details      = $this->resource_model->get_admin_subject($user_id);    
                // log_message('debug','Content Director Subject :'.$director_details['subject_name']);
                // $data['assessment_details'] = $this->resource_model->get_all_subject_assessments($director_details['subject_name']);
                $data['assessment_details'] = $this->resource_model->get_this_mentor_assessments($user_id);

            }
            if($user_role == '7'){
                $data['assessment_details'] = $this->resource_model->get_all_admin_assessment_list();   
                log_message('debug','-- Question Mapped By Admin --');
            }
            if($user_role == '8'){
                $data['assessment_details'] = $this->resource_model->get_all_admin_assessment_list();   
                log_message('debug','-- Question Mapped By Admin --');
            }
            // $data['assessment_details'] = $this->resource_model->get_all_assessment();
            $this->load->view('content_admin/assessment_list',$data);
        }
    }

    // Action : Load All Mentors Assessment List Admin Side
    public function all_mentor_assessment_list(){
        if($this->input->server('REQUEST_METHOD') == 'POST') {
            $user_id   = $this->session->userdata('user_id');         
            $user_role = $this->session->userdata('user_role');
            $data['user_role'] = $user_role;
            if($user_role == '6'){
                // $data['subject_list']  = $this->resource_model->get_admin_subject_details($user_id);
                $director_details      = $this->resource_model->get_admin_subject($user_id);    
                // log_message('debug','Content Director Subject :'.$director_details['subject_name']);
                // $data['assessment_details'] = $this->resource_model->get_all_subject_assessments($director_details['subject_name']);
                $data['assessment_details'] = $this->resource_model->get_this_mentor_assessments($user_id);
            }
            if($user_role == '7'){
                $data['assessment_details'] = $this->resource_model->get_all_mentor_assessment_list();   
                log_message('debug','-- Question Mapped By Admin --');
            }
            if($user_role == '8'){
                $data['assessment_details'] = $this->resource_model->get_all_mentor_assessment_list();   
                log_message('debug','-- Question Mapped By Admin --');
            }
            // $data['assessment_details'] = $this->resource_model->get_all_assessment();
            $this->load->view('content_admin/mentor_assessment_list',$data);
        }
    }

    // sriram : Action : Load All Mentors Assessment List Admin Side
    public function all_mentor_assessment_list1(){
        if($this->input->server('REQUEST_METHOD') == 'POST') {
            $user_id   = $this->session->userdata('user_id');         
            $user_role = $this->session->userdata('user_role');
            $data['user_role'] = $user_role;
            if($user_role == '6'){
                // $data['subject_list']  = $this->resource_model->get_admin_subject_details($user_id);
                $director_details      = $this->resource_model->get_admin_subject($user_id);    
                // log_message('debug','Content Director Subject :'.$director_details['subject_name']);
                // $data['assessment_details'] = $this->resource_model->get_all_subject_assessments($director_details['subject_name']);
                $data['assessment_details'] = $this->resource_model->get_this_mentor_assessments($user_id);
            }
            if($user_role == '7'){
                $data['assessment_details'] = $this->resource_model->get_all_mentor_assessment_list1();   
                log_message('debug','-- Question Mapped By Admin --');
            }
            if($user_role == '8'){
                $data['assessment_details'] = $this->resource_model->get_all_mentor_assessment_list1();   
                log_message('debug','-- Question Mapped By Admin --');
            }
            // $data['assessment_details'] = $this->resource_model->get_all_assessment();
            $this->load->view('content_admin/mentor_assessment_list',$data);
        }
    }

    // Action : Load Mentor Assessemnt View Page
    public function mentor_assessment_list() {
        if($this->input->server('REQUEST_METHOD') == 'POST') {
            $user_id   = $this->session->userdata('user_id');         
            $user_role = $this->session->userdata('user_role');
            $data['user_role'] = $user_role;
            // --------------------------------------------------------
            // User Roles : 6 - mentor or sme 7- super admin 8 - content director admin 
            switch ($user_role) {
                case '6':
                    $data['subject_list']  = $this->resource_model->get_admin_subject_details($user_id);
                    $director_details      = $this->resource_model->get_admin_subject($user_id);    
                    // log_message('debug','Mentor Area of Intrest :'.$director_details['subject_name']);
                    // $data['assessment_details'] = $this->resource_model->get_all_subject_assessments($director_details['subject_name']);
                    $data['assessment_details'] = $this->resource_model->get_this_mentor_assessments($user_id);
                    $this->load->view('content_admin/mentor_sme/mentor_assessment_list',$data);
                    break;
                case '7':
                    $data['assessment_details'] = $this->resource_model->get_all_admins_assessment();   
                    log_message('debug','-- Question Mapped By Admin --');
                    $this->load->view('content_admin/assessment_list',$data);
                    break;
                case '8':
                    $data['assessment_details'] = $this->resource_model->get_all_admins_assessment();   
                    log_message('debug','-- Question Mapped By Admin --');
                    $this->load->view('content_admin/assessment_list',$data);
                    break;
                default:
                    log_message('debug','Something Went Wrong in mentor_resource_view ');
                    break;
            }
            // --------------------------------------------------------

        }
    }

    // Action : Load Assessment Modal View
    public function add_assessment_modal(){
        log_message('debug','****************** Content Admin Assessment Modal REQ View START ******************');
        $user_id   = $this->session->userdata('user_id');
        $user_role = $this->session->userdata('user_role');
        if($user_role == '6'){
            $data['subject_list']  = $this->resource_model->get_admin_subject_details($user_id);
            $director_details      = $this->resource_model->get_admin_subject($user_id);
            // log_message('debug','Content Director Subject :'.$director_details['subject_name']);
        }
        if($user_role == '7'){
            $data['subject_list'] = $this->resource_model->get_all_subjects();
            log_message('debug','-- Question Mapped By Admin --');
        }
        if($user_role == '8'){
            $data['subject_list'] = $this->resource_model->get_all_subjects();
            log_message('debug','-- Question Mapped By Admin --');
        }
        $this->load->view('content_admin/add_assessment_modal',$data);
        log_message('debug','****************** Content Admin Assessment Modal REQ View ENDED ******************');
    }

    // Action : Ajax Mentor Assessment Upload Modal 
    public function ajax_mentor_assessment_modal(){
        log_message('debug','****************** Ajax Mentor Assessment Modal REQ View START ******************');
        $user_id   = $this->session->userdata('user_id');
        $user_role = $this->session->userdata('user_role');
        if($user_role == '6'){
            $data['subject_list']  = $this->resource_model->get_admin_subject_details($user_id);
            $director_details      = $this->resource_model->get_admin_subject($user_id);
            // log_message('debug','Content Director Subject :'.$director_details['subject_name']);
            $this->load->view('content_admin/mentor_sme/ajax_mentor_assessment_modal',$data);
        }
        if($user_role == '7'){
            $data['subject_list'] = $this->resource_model->get_all_subjects();
            log_message('debug','-- Question Mapped By Super Admin --');
            $this->load->view('content_admin/add_assessment_modal',$data);
        }
        if($user_role == '8'){
            $data['subject_list'] = $this->resource_model->get_all_subjects();
            log_message('debug','-- Question Mapped By Content Admin --');
            $this->load->view('content_admin/add_assessment_modal',$data);
        }
        log_message('debug','****************** Ajax Mentor Assessment Modal REQ View ENDED ******************');
    }

    // Action : Add Assessment Data and uploading PDF File in DB
    public function assessment_file_upload() {
        log_message('debug','****************** Content Admin AJAX Assessment PDF Upload START ******************');
        $status = "";
        $msg    = "";
        $file_element_name = 'upload_ques_paper';
        $assessment['user_id']          = $this->session->userdata('user_id');
        //collecting details
        $assessment['test_no']          = $this->input->post('test_no');
        $assessment['test_name']        = $this->input->post('test_name');
        $assessment['test_subject']     = $this->input->post('test_subject');
        $assessment['test_description'] = $this->input->post('test_description');
        $assessment['no_of_questions']  = $this->input->post('no_of_questions');
        $assessment['test_type']        = $this->input->post('test_type');
        $assessment['test_date']        = $this->input->post('test_date');
        $assessment['test_duration']    = $this->input->post('test_duration');
        $assessment_file                = $_FILES['upload_ques_paper'];
       // $assessment['start_time']       = $this->input->post('start_time');
       // $assessment['end_time']         = $this->input->post('end_time');


        log_message('debug',' test_no :'.$assessment['test_no']);
        log_message('debug',' test_name  :'.$assessment['test_name']);
        log_message('debug',' test_subject  :'.$assessment['test_subject']);
        log_message('debug',' test_description  :'.$assessment['test_description']);
        log_message('debug',' no_of_questions  :'.$assessment['no_of_questions']);
        log_message('debug',' test_type  :'.$assessment['test_type']);
        log_message('debug',' test_date  :'.$assessment['test_date']);
        log_message('debug',' test_duration  :'.$assessment['test_duration']);
        log_message('debug',' upload_ques_paper  :'.$assessment_file['name']);
       // log_message('debug',' start_time  :'.$assessment['start_time']);
       // log_message('debug',' end_time  :'.$assessment['end_time']);

       if ($status != "error") {
            $config['upload_path']      = $_SERVER['DOCUMENT_ROOT'].'/static/resource/questions';
            $config['allowed_types']    = 'pdf'; // OLD 'gif|jpg|png|doc|txt';
            $config['max_size']         = 1024 * 8;
            $config['encrypt_name']     = FALSE;

            $this->load->library('upload', $config);
            if (!$this->upload->do_upload($file_element_name)) {
                $status = 'error';
                $msg = $this->upload->display_errors('', '');
            } else {
                $data = $this->upload->data();
                $image_path = $data['full_path'];
                log_message('debug','file uploaded : '.$data['file_name']);

                if(file_exists($image_path)) {
                    $status = "success";
                    $assessment['upload_ques_paper'] = $data['file_name'];
                    $this->resource_model->add_assessment_pdf($assessment);
                    $msg = "File successfully uploaded";
                } else {
                    $status = "error";
                    $msg = "Something went wrong when saving the file, please try again.";
                }
            }
            // @unlink($_FILES[$file_element_name]);
        }
        echo json_encode(array('status' => $status, 'msg' => $msg));
        log_message('debug','****************** Content Admin AJAX Assessment PDF Upload ENDED ******************');
    }


    //Action: Load Edit Res Modal View
    public function ans_key_modal(){
        if($this->input->server('REQUEST_METHOD') == 'POST') {
            $test_id = $this->input->post('test_id');
            $data['assessment_detail'] = $this->resource_model->get_assessment_details($test_id);
            log_message('debug','****************** Content Admin Answer key Modal REQ View START ******************');
            $this->load->view('content_admin/ans_key_modal',$data);
            log_message('debug','****************** Content Admin Answer key Modal REQ View ENDED ******************');
        }
        
    }


    // Action : save assessment answers in db
    public function save_answer(){
        log_message('debug',"Content Admin update Answer key called");
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            log_message('debug',"Content Admin POST Update Answer key called");
            $test_id    = $this->input->post('test_id');
            log_message('debug',"test id " . $test_id);
            $answer_key = $this->input->post('answer_key');
             log_message('debug',"answer key " . $answer_key);
            //update answers in assessment master table
            $result = $this->resource_model->update_answer_key($test_id,$answer_key);

            log_message('debug',"Update answer key" .$result);

            if($result ==1){
                echo "true";
            }else { echo "false";}
        }
    }  


    // Action : Delete Assessment
    public function delete_assessment() {
        log_message('debug','Content Admin Delete Assessment Called');
        
        if($this->input->server('REQUEST_METHOD') == 'POST') {
            log_message('debug',"Content Admin POST Delete Assessment Called");

            $test_id    = $this->input->post('test_id');
            $pdf_file   = $assessment_detail['upload_ques_paper'];
            $file_path  = $_SERVER['DOCUMENT_ROOT'].'/static/resource/questions';
            $this->deletepdfFiles($file_path,$pdf_file);
            $result = $this->resource_model->delete_assessment_file($test_id);
            
            if($result == 1) {
                log_message('debug','Assessment file Deleted ');
                echo "true";  
            } else {
                echo "false";
            }

            log_message('debug',"Assessment file Deleted test_id ".$test_id." result: ".$result);
        }
    }

    
    // Action : Delete File @Server
    function deletepdfFiles($path,$file_name){
        unlink($path.'/'.$file_name); 
        log_message('debug','File Deleted @ '.$path.'/'.$file_name);
    }

    //---------------------------------------------------------------------------------------//



    
    //---------------------------------------------------------------------------------------//
    // Subjects Management  
    //---------------------------------------------------------------------------------------//

    // Action : Load Subject View Page
    public function subject_view() {
        if($this->input->server('REQUEST_METHOD') == 'POST') {
            $data['subject_details'] = $this->resource_model->get_all_subjects();
            $this->load->view('content_admin/subject_view',$data);
        }
    }


    // Action : Load Subject Modal View
    public function add_subject_modal(){
        log_message('debug','****************** Content Admin Subject Modal REQ View START ******************');
        $this->load->view('content_admin/add_subject_modal');
        log_message('debug','****************** Content Admin Subject Modal REQ View ENDED ******************');
    }

    public function add_subject()
    {
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $subjects['subject_name'] = $this->input->post('subject_name');
            $subjects['description']  = $this->input->post('subject_description');

            // add data in resource master table 
            $result = $this->resource_model->add_subject($subjects);

            log_message('debug',"Subjects Resource result ".$result);

            if($result == 1) { 
                echo "true";
            } else { echo "false"; }

        }
    }

  //Action: Load Edit Sub Modal View
    public function edit_sub_modal(){
        if($this->input->server('REQUEST_METHOD') == 'POST') {
            $subject_id = $this->input->post('sub_id');
            $data['subject_details'] = $this->resource_model->get_subject_details($subject_id);
            log_message('debug','****************** Content Admin Edit Sub Modal REQ View START ******************');
            $this->load->view('content_admin/edit_sub_modal',$data);
            log_message('debug','****************** Content Admin Edit Sub Modal REQ View ENDED ******************');
        }
        
    }


    // Action : Edit Subject Link
    public function update_subject() {

        log_message('debug','Content Admin Update Subject Called');
        if($this->input->server('REQUEST_METHOD') == 'POST') {
            log_message('debug',"Content Admin POST Update Subject Called");
            $subjects_details['subject_name']  = $this->input->post('subject_name');
            $subjects_details['subject_description']   = $this->input->post('subject_description');
            $subjects_details['subject_id']   = $this->input->post('subject_id');
            // Edit data in subject master table 
            $result = $this->resource_model->update_subject_details($subjects_details);
            log_message('debug',"Edit Subject result ".$result);

            if($result == 1) { 
                echo "true";
            } else { echo "false"; }
        }
    }

    //Action: Update Assessment 
    public function update_assessment(){
        if($this->input->server('REQUEST_METHOD') == 'POST'){

            log_message('debug','Content Admin Update Assesment Start');

            $assessment_details['test_subject']     = $this->input->post('test_subject');
            $assessment_details['test_type']        = $this->input->post('test_type');
            $assessment_details['test_name']        = $this->input->post('test_name');
            $assessment_details['test_description'] = $this->input->post('test_description');
            $assessment_details['no_of_questions']  = $this->input->post('no_of_questions');
            $assessment_details['test_duration']    = $this->input->post('test_duration');
            $assessment_details['test_date']        = $this->input->post('test_date');
            $assessment_details['test_id']          = $this->input->post('test_id');

            //Edit data in assessment_master table
            $result = $this->resource_model->update_test_details($assessment_details);

            log_message('debug',"Edit Assessment result".$result);
            if($result ==1){
                echo "true";
            }else { echo "false"; }
        }
    }



    // Action : Delete Subject
    public function delete_subject() {
        log_message('debug','Content Admin Delete Delete Subject Called');
        
        if($this->input->server('REQUEST_METHOD') == 'POST') {
            log_message('debug',"Content Admin POST Delete Subject Called");

            $subject_id  = $this->input->post('subject_id');
            $result = $this->resource_model->delete_subject($subject_id);
            
            if($result == 1) {
                log_message('debug','Subject Resource Deleted ');
                echo "true";  
            } else {
                echo "false";
            }

            log_message('debug',"Subject Resource Deleted sub_id ".$subject_id." result: ".$result);
        }
    }

    //---------------------------------------------------------------------------------------//



    //---------------------------------------------------------------------------------------//
    // Course Management  
    //---------------------------------------------------------------------------------------//

    // Action : Load Course View Page
    public function course_view() {
        if($this->input->server('REQUEST_METHOD') == 'POST') {
            $data['course_details'] = $this->resource_model->get_all_courses();
            $this->load->view('content_admin/course_view',$data);
        }
    }

    // Action : Load Course Modal View
    public function add_course_modal(){
        log_message('debug','****************** Content Admin Course Modal REQ View START ******************');
        $this->load->view('content_admin/add_course_modal');
        log_message('debug','****************** Content Admin Course Modal REQ View ENDED ******************');
    }

    // Action : Add Course Data in DB
    public function add_course()
    {
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $courses['course_name']         = $this->input->post('course_name');
            $courses['course_description']  = $this->input->post('course_description');
            $courses['course_duration']     = $this->input->post('course_duration');
            $courses['course_type']         = $this->input->post('course_type');
            $courses['course_fee']          = $this->input->post('course_fee');
            $courses['course_status']       = $this->input->post('course_status');           
            // add data in course master table 
            $result = $this->resource_model->add_course($courses);

            log_message('debug',"course Resource result ".$result);

            if($result == 1) { 
                echo "true";
            } else { echo "false"; }

        }
    }

    //Action: Load Edit Course Modal View
    public function edit_course_modal(){
        if($this->input->server('REQUEST_METHOD') == 'POST') {
            $course_id = $this->input->post('course_id');
            $data['course_details'] = $this->resource_model->get_courses_details($course_id);
            log_message('debug','****************** Content Admin Edit Course Modal REQ View START ******************');
            $this->load->view('content_admin/edit_course_modal',$data);
            log_message('debug','****************** Content Admin Edit Course Modal REQ View ENDED ******************');
        }
        
    }

   
    // Action : Edit Subject Link
    public function update_course() {

        log_message('debug','Content Admin Update course Called');
        if($this->input->server('REQUEST_METHOD') == 'POST') {
            log_message('debug',"Content Admin POST Update course Called");
            $course_details['course_id']            = $this->input->post('course_id');
            $course_details['course_name']          = $this->input->post('course_name');
            $course_details['course_description']   = $this->input->post('course_description');
            $course_details['course_duration']      = $this->input->post('course_duration');
            $course_details['course_type']          = $this->input->post('course_type');
            $course_details['course_fee']           = $this->input->post('course_fee');
            $course_details['course_status']        = $this->input->post('course_status');
            log_message('debug','Course ID '.$course_details['course_id']);
            log_message('debug','Course Name '.$course_details['course_name']);
            log_message('debug','Course Desc '.$course_details['course_description']);
            log_message('debug','Course Duration '.$course_details['course_duration']);
            log_message('debug','Course Type  '.$course_details['course_type']);
            log_message('debug','Course Fee  '.$course_details['course_fee']);
            log_message('debug','Course status  '.$course_details['course_status']);

            // Edit data in subject master table 
            $result = $this->resource_model->update_course_details($course_details);
            log_message('debug',"Edit course result ".$result);

            if($result == 1) { 
                echo "true";
            } else { echo "false"; }
        }
    }



    
    // Action : Delete course
    public function delete_course() {
        log_message('debug','Content Admin Delete Delete course Called');
        
        if($this->input->server('REQUEST_METHOD') == 'POST') {
            log_message('debug',"Content Admin POST Delete course Called");

            $course_id  = $this->input->post('course_id');
            $result = $this->resource_model->delete_course($course_id);
            
            if($result == 1) {
                log_message('debug','Course Resource Deleted ');
                echo "true";  
            } else {
                echo "false";
            }

            log_message('debug',"Course Resource Deleted course_id ".$course_id." result: ".$result);
        }
    }

    //---------------------------------------------------------------------------------------//

    //---------------------------------------------------------------------------------------//
    // Map Course & Resource Management 
    //---------------------------------------------------------------------------------------//
        // Action : Get Course Resource Map View
        public function course_resource_mapview(){
            if($this->input->server('REQUEST_METHOD') == 'POST'){
                $data['course_details'] = $this->resource_model->get_all_published_courses();
                log_message('debug','********* Content Admin Course Resource Map View *********');
                $this->load->view('content_admin/course_resource_mapview',$data);
                log_message('debug','**********************************************************');
            }
        } 

        // Action : Get Course Resource Map List 
        public function course_resource_maplist($value=''){
            if($this->input->server('REQUEST_METHOD') == 'POST'){
                $course_id =  $this->input->post('course_id');
                log_message('debug','Get Course Resource Map List with Course ID: '.$course_id);
                $data['user_details']   = $this->session->all_userdata();
                $data['course_details'] = $this->resource_model->get_courses_details($course_id);
                $user_id                = $this->session->userdata('user_id');
                $user_role              = $this->session->userdata('user_role');
                if($user_role == '6'){
                    $director_details       = $this->resource_model->get_admin_subject($user_id);    
                    // log_message('debug','Content Director Subject :'.$director_details['subject_name']);
                    $data['syllabus_list']  = $this->resource_model->get_director_course_resource_maplist($course_id,$director_details['subject_name']);
                }
                if($user_role == '7'){
                    $data['syllabus_list']  = $this->resource_model->get_course_resource_maplist($course_id);
                }
                if($user_role == '8'){
                    $data['syllabus_list']  = $this->resource_model->get_course_resource_maplist($course_id);
                }
                $this->load->view('content_admin/course_resource_map_list',$data);
            }
        }

        //Action: Load view pdf modal
        public function view_resource_modal(){
            if($this->input->server('REQUEST_METHOD')=='POST'){
                log_message('debug','******************view pdf modal start*************');
                $syllabus_type = $this->input->post('syllabus_type');
                $resource_type = $this->input->post('res_type');
                $resource_id   = $this->input->post('res_id');
                $resource_details = $this->resource_model->get_resource_details($resource_id);
                log_message('debug','syllabus_type '.$syllabus_type);
                switch ($resource_type) {
                    case 'PDF':
                        $data['resource_details']  = $resource_details;
                        $resource_link = $this->input->post('resource_link');
                        log_message('debug','resource_id '.$resource_id);
                        log_message('debug','resource_type '.$resource_type);
                        log_message('debug','resource link' .$resource_details['resource_link']);
                        $this->load->view('content_admin/view_pdf_modal',$data);


                        # code...
                        break;
                    case 'VIDEO':
                        log_message('debug','resource_id '.$resource_id);
                        log_message('debug','resource_type '.$resource_type);
                        $data['resource_details']  = $resource_details;
                        $data['resource_link']  = $resource_details['resource_link'];
                        $this->load->view('content_admin/view_video_modal',$data);
                        # code...
                        break;
                    default:
                        # code...
                        break;
                }
                log_message('debug','***************view pdf modal end******************');
            }
        }

        //Action -> Load View Assessment Pdf Modal
        public function view_assessment_pdf(){
            if($this->input->server('REQUEST_METHOD') =='POST'){
                log_message('debug','***************view assessment pdf modal start******************');
                $syllabus_type      = $this->input->post('syllabus_type');
                $test_id            = $this->input->post('test_id');
                $upload_ques_paper  = $this->input->post('upload_ques_paper');
                log_message('debug','Syllabus Type '.$syllabus_type);
                log_message('debug','Test ID '.$test_id);

                $assessment_details = $this->resource_model->get_assessment_details($test_id);
                $data['assessment_details'] = $assessment_details;
                $this->load->view('content_admin/view_assessment_pdf',$data);
                log_message('debug','***************view assessment pdf modal end******************');
            }
        }
        
        // Action -> CURL TO GET URL DATA
        public function curl_get($url) {
            // $this->load->library('curl');
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_TIMEOUT, 30);
            $return = curl_exec($curl);
            curl_close($curl);
            log_message('debug',"CURL Result ".$return);
            return $return;
        } 

        // Action -> Generate embeded IFRAME Code   
        public function embed_video() {
            // $this->layout = "default";
            $url = $this->input->get('url');
            $url = base64_decode($url);
            $json = "";
             if(strstr($url,"youtube"))
                $json = $this->curl_get("https://www.youtube.com/oembed?url=".rawurlencode($url)."&format=json&callback=foo");
            else if(strstr($url,"vimeo"))
                $json = $this->curl_get("https://vimeo.com/api/oembed.json?url=".rawurlencode($url));
            if(is_object(json_decode($json)) == 1) {
                echo $json;
                log_message('debug','------------------------------------------------------');
                log_message('debug','| JSON Result : '.$json);
                log_message('debug','------------------------------------------------------');
            } else {
                log_message('debug','------------------------------------------------------');
                log_message('debug','| JSON Result : '.$json);
                log_message('debug','------------------------------------------------------');
                $json = json_encode(array("html" => "<h3>Video not available. Please try later.</h3>"));
                echo $json;
            }
        }

        // Action : Load Course Resource Map Modal View
        public function load_cres_map_modal(){
            log_message('debug','****************** Content Admin Course Syllabus Resource Modal REQ View START ******************');
            $user_id = $this->session->userdata('user_id');
            $data['module_list']   = $this->resource_model->get_module_list();
            $data['group_list']    = $this->resource_model->get_group_list();
            $user_role             = $this->session->userdata('user_role');
            if($user_role == '6'){
                $data['subject_list']  = $this->resource_model->get_admin_subject_details($user_id);
                $director_details      = $this->resource_model->get_admin_subject($user_id);    
                // log_message('debug','Content Director Subject :'.$director_details['subject_name']);
                $data['resource_list'] = $this->resource_model->get_all_subject_resources($director_details['subject_name']);
            }
            if($user_role == '7'){
                $data['subject_list']    = $this->resource_model->get_all_subjects();
                $data['resource_list']   = $this->resource_model->get_admins_all_resources();   
            }
            if($user_role == '8'){
                $data['subject_list']    = $this->resource_model->get_all_subjects();
                $data['resource_list']   = $this->resource_model->get_admins_all_resources();   
            }
            // $data['subject_list']  = $this->resource_model->get_all_subjects();
            $this->load->view('content_admin/load_cres_map_modal',$data);
            log_message('debug','****************** Content Admin Course Syllabus Resource Modal REQ View ENDED ******************');
        }

        // Action : Add Map B/W Course and Resource 
        public function add_course_resource(){
            if($this->input->server('REQUEST_METHOD') == 'POST'){
                $CRES_mapdata['course_id']      = $this->input->post('course_id');
                $CRES_mapdata['module_name']    = $this->input->post('module_name');
                // $CRES_mapdata['group_name']     = $this->input->post('group_name');
                $CRES_mapdata['group_name']     = "Group I";
                $CRES_mapdata['subject_name']   = $this->input->post('subject_name');
                $CRES_mapdata['syllabus_type']  = $this->input->post('syllabus_type');
                $CRES_mapdata['resource_id']    = $this->input->post('resource_id');
                $CRES_mapdata['schedule']       = $this->input->post('schedule');
                $CRES_mapdata['resource_status']= 'UnPublished';  
                
                // add data in course subject resource map table 
                $result = $this->resource_model->add_syllabus($CRES_mapdata);    
                log_message('debug',"ADD Syllabus result ".$result);
                if($result == 1) { 
                    echo "true";
                } else { echo "false"; }
            }
        }

        // Action : Edit Course Resource Map Modal View
        public function edit_cres_map_modal(){
            if($this->input->server('REQUEST_METHOD')=='POST'){
                log_message('debug','****************** Content Admin Course Syllabus Resource Modal REQ View START ******************');
                $user_id = $this->session->userdata('user_id');
                $map_id  = $this->input->post('map_id');
                log_message('debug','MAP ID : '.$map_id);
                $data['module_list']   = $this->resource_model->get_module_list();
                $data['group_list']    = $this->resource_model->get_group_list();
                $user_role             = $this->session->userdata('user_role');
                if($user_role == '6'){
                    $data['subject_list']  = $this->resource_model->get_admin_subject_details($user_id);
                    $director_details      = $this->resource_model->get_admin_subject($user_id);    
                    // log_message('debug','Content Director Subject :'.$director_details['subject_name']);
                    // Need to Write Code in Modal to Bring only Mentor Resources 
                    $data['resource_list'] = $this->resource_model->get_all_resources();
                }
                if($user_role == '7'){
                    $data['subject_list']    = $this->resource_model->get_all_subjects();
                    $data['resource_list']   = $this->resource_model->get_admins_all_resources();   
                }
                if($user_role == '8'){
                    $data['subject_list']    = $this->resource_model->get_all_subjects();
                    $data['resource_list']   = $this->resource_model->get_admins_all_resources();   
                }
                $data['cres_map_details']  = $this->resource_model->get_course_resource_map_details($map_id);
                // log_message('debug','cres details '.print_r($data['cres_map_details']));
                $this->load->view('content_admin/edit_cres_map_modal',$data);
                log_message('debug','****************** Content Admin Course Syllabus Resource Modal REQ View ENDED ******************');
            }
        }

        // Action : Update Map B/W Course and Resource 
        public function update_course_resource(){
            if($this->input->server('REQUEST_METHOD') == 'POST'){
                $update_CRES_mapdata['map_id']         = $this->input->post('map_id');
                $update_CRES_mapdata['module_name']    = $this->input->post('module_name');
                // $update_CRES_mapdata['group_name']     = $this->input->post('group_name');
                $update_CRES_mapdata['group_name']     = "Group I";
                $update_CRES_mapdata['subject_name']   = $this->input->post('subject_name');
                $update_CRES_mapdata['syllabus_type']  = $this->input->post('syllabus_type');
                $update_CRES_mapdata['resource_id']    = $this->input->post('resource_id');
                $update_CRES_mapdata['schedule']       = $this->input->post('schedule');
                $update_CRES_mapdata['resource_status']= 'UnPublished';    
                
                // add data in course subject resource map table 
                $result = $this->resource_model->update_syllabus($update_CRES_mapdata);    
                log_message('debug',"ADD Syllabus result ".$result);
                if($result == 1) { 
                    echo "true";
                } else { echo "false"; }
            }
        }

    //---------------------------------------------------------------------------------------//

    
    //---------------------------------------------------------------------------------------//
    // Map Course & assessment Management 
    //---------------------------------------------------------------------------------------//
        // Action : Get Course Assessment Map View
        public function course_assessment_mapview(){
            if($this->input->server('REQUEST_METHOD') == 'POST'){
                $data['course_details'] = $this->resource_model->get_all_published_courses();
                log_message('debug','********* Content Admin Course Resource Map View *********');
                $this->load->view('content_admin/course_assessment_mapview',$data);
                log_message('debug','**********************************************************');
            }
        }

        // Action : Get Course Test Map List 
        public function course_test_maplist($value=''){
            if($this->input->server('REQUEST_METHOD') == 'POST'){
                $course_id =  $this->input->post('course_id');
                log_message('debug','Get Course Resource Map List with Course ID: '.$course_id);
                $data['user_details'] = $this->session->all_userdata();
                $data['course_details'] = $this->resource_model->get_courses_details($course_id);
                // List Mapping Detils accouring to User
                $user_id                = $this->session->userdata('user_id');
                $user_role              = $this->session->userdata('user_role');
                if($user_role == '6'){
                    $director_details      = $this->resource_model->get_admin_subject($user_id);    
                    // log_message('debug','Content Director Subject :'.$director_details['subject_name']);
                    $data['syllabus_list']  = $this->resource_model->get_director_course_assessment_maplist($course_id,$director_details['subject_name']);
                }
                if($user_role == '7'){
                    $data['syllabus_list']  = $this->resource_model->get_course_assessment_maplist($course_id);
                }
                if($user_role == '8'){
                    $data['syllabus_list']  = $this->resource_model->get_course_assessment_maplist($course_id);
                }
                $this->load->view('content_admin/course_assessment_map_list',$data);
            }
        }

        // Action : Load Course Test Map Modal View
        public function load_ctest_map_modal(){
            log_message('debug','****************** Content Admin Course Course Test Map Modal REQ View START ******************');
            $user_id = $this->session->userdata('user_id');
            $data['module_list']   = $this->resource_model->get_module_list();
            $data['group_list']    = $this->resource_model->get_group_list();
            $user_role             = $this->session->userdata('user_role');
            if($user_role == '6'){
                $data['subject_list']  = $this->resource_model->get_admin_subject_details($user_id);
                $director_details      = $this->resource_model->get_admin_subject($user_id);    
                // log_message('debug','Content Director Subject :'.$director_details['subject_name']);
                // $data['assessment_list'] = $this->resource_model->get_all_subject_assessments_with_key($director_details['subject_name']);
                $data['assessment_list'] = $this->resource_model->get_this_mentor_assessments($user_id);
            }
            if($user_role == '7'){
                $data['subject_list']    = $this->resource_model->get_all_subjects();
                $data['assessment_list'] = $this->resource_model->get_all_admins_assessment_with_key();   
                log_message('debug','-- Question Mapped By Admin --');
            }
            if($user_role == '8'){
                $data['subject_list']    = $this->resource_model->get_all_subjects();
                $data['assessment_list'] = $this->resource_model->get_all_admins_assessment_with_key();
                log_message('debug','-- Question Mapped By Content Admin --');
            }
            // $data['subject_list']  = $this->resource_model->get_all_subjects();
            $this->load->view('content_admin/load_ctest_map_modal',$data);
            log_message('debug','****************** Content Admin Course Course Test Map Modal REQ View ENDED ******************');
        }

        // Action : Add Map B/W Course and Test 
        public function add_course_test(){
            if($this->input->server('REQUEST_METHOD') == 'POST'){
                $CTEST_mapdata['course_id']      = $this->input->post('course_id');
                $CTEST_mapdata['module_name']    = $this->input->post('module_name');
                // $CTEST_mapdata['group_name']     = $this->input->post('group_name');
                $CTEST_mapdata['group_name']     = "Group I";
                $CTEST_mapdata['subject_name']   = $this->input->post('subject_name');
                $CTEST_mapdata['syllabus_type']  = $this->input->post('syllabus_type');
                $CTEST_mapdata['resource_id']    = $this->input->post('resource_id');
                $CTEST_mapdata['schedule']       = $this->input->post('schedule');   
                $CTEST_mapdata['resource_status']= 'UnPublished';
                // Add data in course subject resource map table 
                $result = $this->resource_model->add_syllabus($CTEST_mapdata);    
                log_message('debug',"ADD Syllabus result ".$result);
                if($result == 1) { 
                    echo "true";
                } else { echo "false"; }
            }
        }

        // Action : Edit Course Test Map Modal View
        public function edit_ctest_map_modal(){
            if($this->input->server('REQUEST_METHOD') == 'POST'){
                log_message('debug','****************** Content Admin Course Course Test Map Modal REQ View START ******************');
                $user_id = $this->session->userdata('user_id');
                $map_id  = $this->input->post('map_id');
                $data['module_list']   = $this->resource_model->get_module_list();
                $data['group_list']    = $this->resource_model->get_group_list();
                $user_role             = $this->session->userdata('user_role');
                if($user_role == '6'){
                    $data['subject_list']  = $this->resource_model->get_admin_subject_details($user_id);
                    $director_details      = $this->resource_model->get_admin_subject($user_id);    
                    // log_message('debug','Content Director Subject :'.$director_details['subject_name']);
                    // $data['assessment_list'] = $this->resource_model->get_all_subject_assessments($director_details['subject_name']);
                    $data['assessment_list'] = $this->resource_model->get_this_mentor_assessments($user_id);
                }
                if($user_role == '7'){
                    $data['subject_list']    = $this->resource_model->get_all_subjects();
                    $data['assessment_list'] = $this->resource_model->get_all_admins_assessment_with_key();   
                    log_message('debug','-- Question Mapped By Admin --');
                }
                if($user_role == '8'){
                    $data['subject_list']    = $this->resource_model->get_all_subjects();
                    $data['assessment_list'] = $this->resource_model->get_all_admins_assessment_with_key();   
                    log_message('debug','-- Question Mapped By Admin --');
                }
                $data['ctest_map_details']  = $this->resource_model->get_course_assessment_map_details($map_id);
                $this->load->view('content_admin/edit_ctest_map_modal',$data);
                log_message('debug','****************** Content Admin Course Course Test Map Modal REQ View ENDED ******************');
            }
        }

        // Action : Update Map B/W Course and Test 
        public function update_course_test(){
            if($this->input->server('REQUEST_METHOD') == 'POST'){
                $update_CTEST_mapdata['map_id']      = $this->input->post('map_id');
                $update_CTEST_mapdata['module_name']    = $this->input->post('module_name');
                // $update_CTEST_mapdata['group_name']     = $this->input->post('group_name');
                $update_CTEST_mapdata['group_name']     = "Group I";
                $update_CTEST_mapdata['subject_name']   = $this->input->post('subject_name');
                $update_CTEST_mapdata['syllabus_type']  = $this->input->post('syllabus_type');
                $update_CTEST_mapdata['resource_id']    = $this->input->post('resource_id');
                $update_CTEST_mapdata['schedule']       = $this->input->post('schedule');
                $update_CTEST_mapdata['resource_status']='UnPublished';    
                
                // Update data in course subject resource map table 
                $result = $this->resource_model->update_syllabus($update_CTEST_mapdata);    
                log_message('debug',"ADD Syllabus result ".$result);
                if($result == 1) { 
                    echo "true";
                } else { echo "false"; }
            }
        }
        

    //---------------------------------------------------------------------------------------//

    //---------------------------------------------------------------------------------------//
    // Course Syllabus Management 
    //---------------------------------------------------------------------------------------//
    
    // Action : Get Course syllabus view
    public function course_syllabus_view() {
        if($this->input->server('REQUEST_METHOD') == 'POST') {
            $data['course_details'] = $this->resource_model->get_all_courses();
            $this->load->view('content_admin/course_syllabus_view',$data);
        }
    }

    // Action : Get Syllabus List for The Course 
    public function syllabus_list(){
        if($this->input->server('REQUEST_METHOD') == 'POST') {
            $course_id =  $this->input->post('course_id');
            log_message('debug','Syllabus List Course ID: '.$course_id);
            $data['course_details'] = $this->resource_model->get_courses_details($course_id);
            $data['syllabus_list']  = $this->resource_model->get_syllabus_list($course_id);
            $this->load->view('content_admin/syllabus_list',$data); 
        }
    }

    // Action : Load Course Syllabus Resource Modal View
    public function load_csr_modal(){
        log_message('debug','****************** Content Admin Course Syllabus Resource Modal REQ View START ******************');
        $user_id = $this->session->userdata('user_id');
        $data['module_list']   = $this->resource_model->get_module_list();
        $data['group_list']    = $this->resource_model->get_group_list();
        $data['admin_details'] = $this->resource_model->$CI($user_id);
        $data['resource_list'] = $this->resource_model->get_all_resources();
        $data['subject_list']  = $this->resource_model->get_all_subjects();
        $this->load->view('content_admin/load_csr_modal',$data);
        log_message('debug','****************** Content Admin Course Syllabus Resource Modal REQ View ENDED ******************');
    }

    // Action : Load Course Syllabus Assessment Modal View
    public function load_csa_modal(){
        log_message('debug','****************** Content Admin Course Syllabus Assessment Modal REQ View START ******************');
        $data['assessment_list'] = $this->resource_model->get_all_resources();
        $data['subject_list']    = $this->resource_model->get_all_subjects();
        $this->load->view('content_admin/load_csa_modal',$data);
        log_message('debug','****************** Content Admin Course Syllabus Assessment Modal REQ View ENDED ******************');
    }

    // Action : Add Syllabus Data in DB
    public function add_syllabus(){
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $syllabus['course_id']      = $this->input->post('course_id');
            $syllabus['subject_name']   = $this->input->post('subject_name');
            $syllabus['syllabus_type']  = $this->input->post('syllabus_type');
            $syllabus['resource_id']    = $this->input->post('resource_id');
            $syllabus['schedule']       = $this->input->post('schedule');    
            $syllabus['module_name']    = 'Model 1';
            $syllabus['group_name']     = 'Group I';
            // add data in course subject resource map table 
            $result = $this->resource_model->add_syllabus($syllabus);    
            log_message('debug',"ADD Syllabus result ".$result);
            if($result == 1) { 
                echo "true";
            } else { echo "false"; }
        }
    }

    function change_order_res() {

        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $orderlist = explode(',', $_POST['order']);

            foreach ($orderlist as $order=>$id) {
                $this->resource_model->change_order_res($order+1,$id);
            }
            //print_r($orderlist) ;
        }
    }

    // Action : Delete course syllabus
    public function delete_syllabus() {
        log_message('debug','Content Admin Delete course syllabus Called');
        
        if($this->input->server('REQUEST_METHOD') == 'POST') {
            log_message('debug',"Content Admin POST Delete course syllabus Called");

            $map_id  = $this->input->post('map_id');
            $result = $this->resource_model->delete_syllabus($map_id);
            
            if($result == 1) {
                log_message('debug','Course Syllabus Deleted ');
                echo "true";  
            } else {
                echo "false";
            }

            log_message('debug',"Course Syllabus Deleted map_id ".$map_id." result: ".$result);
        }
    }

    // Action : Get Course list view 

    //---------------------------------------------------------------------------------------//
}
