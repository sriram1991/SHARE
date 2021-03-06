<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class SH_Controller extends SM_Controller {    
    public function __construct(){
    	parent::__construct();
        $this->load->model('pincode_model');
        $this->load->model('profile_model');
        $this->load->model('batch_model');
        $this->load->model('resource_model');
        $this->load->model('user_model');
        $this->load->model('report_model');
        $this->load->model('payment_model');
    }

    public function index(){
    	$data['data'] = $this->session->all_userdata();
        $data['flash_msg'] = $this->session->flashdata('success_msg');
        $data['user_home'] = "/user_home";
        $data['role_view'] = "student/student_body_leftpan";
        // $data['role_view'] = "/student/left_side_tree";
        $this->load->view('user_header', $data);
        $this->load->view('user_body_leftpan');
        $this->load->view('user_body_rightpan');
        $this->load->view('user_footer');
        log_message('debug', 'Student home');
    }

    public function dashboard(){
        if($this->input->server('REQUEST_METHOD') == 'GET'){
            $data['user_details'] = $this->session->all_userdata();
            $user_id = $this->session->userdata('user_id');
            $result  = $this->batch_model->check_user_batch($user_id);
            
            // get_my_courses_for_chart();
            // $monthly_result = $this->user_model->get_my_montly_score($user_id,'percentage');

            // Action : get the course for charts... 
            $student_id = $this->session->userdata('user_id');
            $data['all_my_courses'] = $this->batch_model->get_my_paid_courses($student_id);

            // $data['get_my_montly_score']  = $monthly_result;
            if($result != null){
                // Available Area of Interest
                $data['avail_aoi'] = $this->batch_model->get_available_aoi($user_id,'Student');
                $data['paid_aoi']  = $this->batch_model->get_paid_aoi_list($user_id);
                //  Enable Following To Get only Paid Courses in Dashboard
                // $data['all_courses'] = $this->batch_model->get_available_paid_courses($user_id,'Student');
                
                $data['all_courses'] = $this->batch_model->get_available_courses($user_id,'Student');
                $data['my_courses']  = $this->batch_model->my_courses($user_id);
                
                // $this->load->view('student/dashboard_rightpan',$data);
                // log_message('debug','Student dashboard I have Courses '); 
                $this->load->view('student/user_dashboard_rightpan',$data);
            } else {
                $data['show_registration'] = "true";
                // Available Area of Interest
                // $data['paid_aoi'] = $this->batch_model->get_available_aoi($user_id,'Student');
                
                //$data['all_courses'] = $this->resource_model->get_available_courses($user_id,'Student','Published');
                $data['avail_aoi'] = $this->batch_model->get_available_aoi($user_id,'Student');
                $data['all_courses'] = $this->batch_model->get_available_courses($user_id,'Student');
                //  Enable Following To Get only Paid Courses in Dashboard
                // $data['all_courses'] = $this->batch_model->get_available_paid_courses($user_id,'Student');
                // $this->load->view('student/dashboard_rightpan',$data);
                $this->load->view('student/user_dashboard_rightpan',$data);
                log_message('debug','Student dashboard Student Not Present in any Batch');
            }
        }
    }

    // Action : New DashBoard For User with Area of Interest
    public function user_area_dashboard(){
        if($this->input->server('REQUEST_METHOD') == 'GET'){
            $data['user_details'] = $this->session->all_userdata();
            $user_id = $this->session->userdata('user_id');
            $result  = $this->batch_model->check_user_batch($user_id);

            // Action : get the course for charts... 
            $student_id = $this->session->userdata('user_id');
            $data['all_my_aoi'] = $this->batch_model->get_my_paid_aoi($student_id);

            if($result != null){
                $data['my_courses']  = $this->batch_model->my_courses($user_id);
                $data['all_aoi'] = $this->batch_model->get_available_aoi($user_id,'Student');
                //  Enable Following To Get only Paid Courses in Dashboard
                $this->load->view('student/user_dashboard_rightpan',$data);
                // log_message('debug','Student dashboard I have Courses '); 
            } else {
                $data['show_registration'] = "true";
                //$data['all_courses'] = $this->resource_model->get_available_courses($user_id,'Student','Published');
                $data['all_courses'] = $this->batch_model->get_available_courses($user_id,'Student');
                //  Enable Following To Get only Paid Courses in Dashboard
                $this->load->view('student/user_dashboard_rightpan',$data);
                log_message('debug','Student dashboard Student Not Present in any Batch');
            }
        }
    }

    // Action -> Get Free Course DashBoard
    public function free_course_dashboard(){
        if($this->input->server('REQUEST_METHOD') == 'GET'){
            $data['user_details'] = $this->session->all_userdata();
            $user_id = $this->session->userdata('user_id');
            $result = $this->batch_model->check_user_batch($user_id);
            
            // $data['all_my_courses'] = $this->batch_model->get_my_paid_courses($user_id);
            // $monthly_result = $this->user_model->get_my_montly_score($user_id,'percentage');

            if($result != null){
                $data['my_courses']  = $this->batch_model->my_courses($user_id);
                $data['all_courses'] = $this->batch_model->get_available_free_courses($user_id,'Student');
                $this->load->view('student/free_course_dashboard',$data);
                log_message('debug','User Free Course dashboard I have Courses '); 
            } else {
                $data['show_registration'] = "true";
                $data['all_courses'] = $this->batch_model->get_available_free_courses($user_id,'Student');
                //$data['all_courses'] = $this->resource_model->get_available_courses($user_id,'Student','Published');
                $this->load->view('student/free_course_dashboard',$data);
                log_message('debug','User Free Course dashboard Student Not Present in any Batch');
            }
        }
    }

    // Action -> Get Available Area of Interest Courses 
    function get_all_available_aoi_courses(){
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $user_id = $this->session->userdata('user_id');
            $area_of_interest = $this->input->post('area_of_interest');
            
            $data['all_aoi_courses'] = $this->batch_model->get_aoi_avail_courses($user_id,$area_of_interest,'Student');

            log_message('debug','-- AOI Courses Dashboard ---------------------------------');
            log_message('debug','USER ID   :'.$user_id);
            log_message('debug','AOI Name  :'.$area_of_interest);
            log_message('debug','----------------------------------------------------------');
            $this->load->view('student/available_aoi_courses',$data);
        }
    }

    // Action -> Get All Paid AOI Courses For Studying
    function get_all_paid_aoi_courses(){
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $user_details = $this->session->all_userdata();
            $user_id = $user_details['user_id'];
            $area_of_interest = $this->input->post('area_of_interest');
            
            $data['paid_aoi_courses'] = $this->batch_model->get_paid_aoi_courses($user_id,$area_of_interest);

            log_message('debug','-- PAID AOI Courses Dashboard ---------------------------------');
            log_message('debug','USER ID   :'.$user_details['user_id']);
            log_message('debug','USER Name :'.$user_details['user_fname']);
            log_message('debug','USER Email:'.$user_details['user_email']);
            log_message('debug','AOI Name  :'.$area_of_interest);
            log_message('debug','----------------------------------------------------------');
            $this->load->view('student/paid_aoi_courses',$data);
        }
    }


    // Action -> Get Course Details for selected Available course
    public function get_course_details(){
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $data['user_details'] = $this->session->all_userdata();
            $this->session->set_userdata('offline_payment_for','student_course_subscription');
            $course_id  = $this->input->post('course_id');
            $user_id    = $this->session->userdata('user_id');
            
            $data['course_details'] = $this->resource_model->get_courses_details($course_id);
            $course_details = $data['course_details'];
            $this->session->set_userdata('course_id',$course_id);
            $this->session->set_userdata('course_name',$course_details['course_name']);
            $this->session->set_userdata('course_fee',$course_details['course_fee']);
            $this->session->set_userdata('total_amount',$course_details['course_fee']);

            $data['offline_payment_for'] = "student_course_subscription";

            $data['subject_registration']='';
            $data['student_course_subscription']=True;
            $data['course_name']            = $course_details['course_name'];
            $data['course_description']     = $course_details['course_description'];
            $data['course_duration']        = $course_details['course_duration'];
            $data['course_fee']             = $course_details['course_fee'];

            //I have to change here
            $check_scholarship      = $this->payment_model->check_scholarship_status($user_id,$course_id);
            if($check_scholarship!=false){
                $scholarship_status     = $check_scholarship->status_id;
                if($scholarship_status == 6){
                    $discount_amount = $check_scholarship->discount_amount;
                    $final_amount   = $course_details['course_fee']-$discount_amount;
                    log_message('debug','FINAL AMOUNT ____________ '.$final_amount);
                   if($final_amount<0){
                        $final_amount = 0;
                    }
                    $this->session->set_userdata('total_amount',$final_amount);

                    $data['total_amount']           = $final_amount;
                    $data['subscribed_scholarship'] = true;
                    $data['scholarship_status']     = $scholarship_status;
                    $data['discount_amount']        = $discount_amount;
                    // $this->load->view('payment/summery_view',$data);
                }else if($scholarship_status == 7 || $scholarship_status == 9){
                    $data['total_amount']           = $course_details['course_fee'];
                    $data['subscribed_scholarship'] = true;
                    $data['scholarship_status']     = $scholarship_status;
                    // $this->load->view('payment/summery_view',$data);
                }
            }else{            
                $data['subscribed_scholarship'] = false;
                $data['total_amount']           = $course_details['course_fee'];
                // $this->load->view('payment/summery_view',$data);
            }
            $this->load->view('payment/summery_view',$data);
        }
    }

    // Action -> Get Batches for the Course 
    public function get_course_batch(){
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $course_id = $this->input->post('course_id');
            $result = $this->batch_model->get_batchs($course_id);
            if($result != null){
                $data['course_batches'] = $result;
                $this->load->view('student/show_course_batch_modal',$data);
                // echo var_dump($result);
            }
        }
    }

    // Action -> Join Course Batch : Note Inputs are USER ID, BATCH ID, COURSE ID
    public function subscribe_course(){
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $user_id    = $this->session->userdata('user_id');
            $batch_id   = $this->input->post('batch_id');
            $course_id  = $this->input->post('course_id');
            $result = $this->batch_model->subscribe_course($user_id,$batch_id,$course_id);
            if($result != null){
                echo "true";
            } else {
                echo "false";
            }
        }
    }

    // Action -> Join Course Offline : Note 
    public function join_course_offline(){
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $user_details              = $this->session->all_userdata();
            $course_form['course_id']  = $this->input->post('course_id');
            $course_form['course_fee'] = $this->input->post('course_fee');
            $course_form['batch_name'] = $user_details['user_state'];
            $course_form['user_id']    = $user_details['user_id'];
            log_message('debug','-- Join Course Offline------------------------------------');
            log_message('debug','Course ID          :'.$course_form['course_id']);
            log_message('debug','Course Fee         :'.$course_form['course_fee']);
            log_message('debug','Course User ID     :'.$course_form['user_id']);
            log_message('debug','Course Batch_name  :'.$course_form['batch_name']);
            log_message('debug','----------------------------------------------------------');
            $result = $this->batch_model->join_course_offline($course_form);
            if($result != null){
                echo "true";
            } else {
                echo "false";
            }
        }
    }

    // Action -> Join Free Course 
    public function join_free_course(){
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            // Capture Inputes 
            $user_details              = $this->session->all_userdata();
            $course_form['course_id']  = $this->input->post('course_id');
            $course_form['course_fee'] = $this->input->post('course_fee');
            $course_form['course_name']= $this->input->post('course_name');
            $course_form['batch_name'] = $user_details['user_state'];
            $course_form['user_id']    = $user_details['user_id'];

            // Step 1: Create Make Transcation for the new license ie req_license and req_license cost 
            // Transaction started ..................
            $free_course_transaction = array(
                'user_id'                  => $user_details['user_id'],
                'transaction_number'       => $user_details['registration_no'],
                'bank_name'                => 'ASK Analytics TR Free Course',
                'amount_paid'              => $course_form['course_fee'],
                'paid_date'                => date('Y-m-d'),
                'transaction_description'  => "USER REG_NO: ".$user_details['registration_no']." for Free Course ".$course_form['course_name'].". ",
                'payment_mode'             => 'offline',
                'payment_status'           => '2', // Note: 8-pending payment | 2-paid
                'total_amount'             => $course_form['course_fee']
            );
            log_message('debug','Transaction Here '.var_dump($free_course_transaction));
            $transaction_id = $this->payment_model->offline_payment($free_course_transaction);
            // Transaction ended ....................
            $course_form['transaction_id'] = $transaction_id;

            log_message('debug','-- Join Free Course Offline--------------------------------');
            log_message('debug','Course ID          :'.$course_form['course_id']);
            log_message('debug','Course Name        :'.$course_form['course_name']);
            log_message('debug','Course Fee         :'.$course_form['course_fee']);
            log_message('debug','Course User ID     :'.$course_form['user_id']);
            log_message('debug','Course Batch_name  :'.$course_form['batch_name']);
            log_message('debug','Transaction ID     :'.$course_form['transaction_id']);
            log_message('debug','----------------------------------------------------------');
            $result = $this->batch_model->join_course_offline($course_form);
            if($result != null){
                echo "true";
            } else {
                echo "false";
            }
        }
    }
    /*

    */

    // Action -> Get My Courses
    public function my_courses(){
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $user_id = $this->session->userdata('user_id');
            $result = $this->batch_model->check_user_batch($user_id);

            if($result != null){
                $data['paid_aoi']  = $this->batch_model->get_paid_aoi_list($user_id);
                $data['my_courses'] = $this->batch_model->my_courses($user_id);
                $this->load->view('student/mycourse_rightpan',$data);
                log_message('debug','Student dashboard I have Courses '); 
            } else {
                $data['show_registration'] = true;
                $this->load->view('student/mycourse_rightpan',$data);
                log_message('debug','Student dashboard Student Not Present in any Batch');
            }
        }
    }

    //------------------------------------------------------------------------------------
    //  Course Module Group Subject Started
    //------------------------------------------------------------------------------------
        // Action = Get Course Module
        public function get_course_module(){
            if($this->input->server('REQUEST_METHOD') == 'POST'){
                $course_id = $this->input->post('course_id');
                $data['course_details'] = $this->resource_model->get_courses_details($course_id);
                $data['course_module']  = $this->resource_model->get_course_modules($course_id);
                $course_details = $data['course_details'];
                // Setting Current Session Details : Course ID , Course Name
                $this->session->set_userdata('CR_course_id',$course_id);
                $this->session->set_userdata('CR_course_name',$course_details['course_name']);
                log_message('debug','---------------------------------------------------------');
                log_message('debug','| Current Session Details');
                log_message('debug','| Current Course ID   > '.$course_id);
                log_message('debug','| Current Course Name > '.$course_details['course_name']);
                log_message('debug','---------------------------------------------------------');
                // Loading Coure Tree View
                $this->load->view('student/course_module_grid',$data);
            }
        }
        // Action = Get Course Module Group
        public function get_course_module_group(){
            if($this->input->server('REQUEST_METHOD') == 'POST'){
                $course_id = $this->input->post('course_id');
                $module_name = $this->input->post('module_name');
                $data['course_details'] = $this->resource_model->get_courses_details($course_id);
                $data['module_name']    = $module_name;
                $data['course_group']  = $this->resource_model->get_course_module_groups($course_id,$module_name);
                $course_details = $data['course_details'];
                // Setting Current Session Details : Course ID , Course Name
                $this->session->set_userdata('CR_course_id',$course_id);
                $this->session->set_userdata('CR_course_name',$course_details['course_name']);
                $this->session->set_userdata('CR_module_name',$module_name);
                log_message('debug','---------------------------------------------------------');
                log_message('debug','| Current Session Details');
                log_message('debug','| Current Course ID     > '.$course_id);
                log_message('debug','| Current Course Name   > '.$course_details['course_name']);
                log_message('debug','| Current Course Module > '.$module_name);
                log_message('debug','---------------------------------------------------------');
                // Loading Coure Tree View
                $this->load->view('student/course_group_grid',$data);
            }
        }
        // Action = Get Course Module Group Subjects
        public function get_course_module_group_subject(){
            if($this->input->server('REQUEST_METHOD') == 'POST'){
                $course_id      = $this->input->post('course_id');
                // $module_name    = $this->session->userdata('CR_module_name');
                $module_name    = $this->input->post('module_name');
                $group_name     = $this->input->post('group_name');

                $data['course_details'] = $this->resource_model->get_courses_details($course_id);
                $data['course_subject'] = $this->resource_model->get_course_module_group_subjects($course_id,$module_name,$group_name);
                $data['module_name']    = $module_name;
                $data['group_name']     = $group_name;
                $course_details = $data['course_details'];
                // Setting Current Session Details : Course ID , Course Name
                $this->session->set_userdata('CR_course_id',$course_id);
                $this->session->set_userdata('CR_course_name',$course_details['course_name']);
                $this->session->set_userdata('CR_module_name',$module_name);
                $this->session->set_userdata('CR_group_name',$group_name);
                log_message('debug','---------------------------------------------------------');
                log_message('debug','| Current Session Details');
                log_message('debug','| Current Course ID     > '.$course_id);
                log_message('debug','| Current Course Name   > '.$course_details['course_name']);
                log_message('debug','| Current Course Module > '.$module_name);
                log_message('debug','| Current Course Group  > '.$group_name);
                log_message('debug','---------------------------------------------------------');
                // Loading Coure Tree View
                $this->load->view('student/course_subjects_grid',$data);
            }
        }
        // Action Get Course Module Group Subject Resource 
        public function get_course_module_group_subject_resources(){
            if($this->input->server('REQUEST_METHOD') == 'POST'){
                $course_id       = $this->input->post('course_id');
                $course_name     = $this->session->userdata('CR_course_name');
                $module_name     = $this->session->userdata('CR_module_name');
                $group_name      = $this->session->userdata('CR_group_name');
                $subject_name    = $this->input->post('subject_name');

                $resource_list   = $this->resource_model->get_cmgsr($course_id,$module_name,$group_name,$subject_name);
                $assessment_list = $this->resource_model->get_cmgsa($course_id,$module_name,$group_name,$subject_name);
                // Load data's
                $data['course_id']      = $course_id;
                $data['course_name']    = $course_name;
                $data['module_name']    = $module_name;
                $data['group_name']     = $group_name;
                $data['subject_name']   = $subject_name;

                if($resource_list != null){
                    $data['resource_list'] = $resource_list;
                    log_message('debug','resource list generated');
                }
                if($assessment_list != null){
                    $data['assessment_list'] = $assessment_list;
                    log_message('debug','Assessment list Generated');
                }
                // Setting Current Session Details : Course ID , Course Name, Subject Name
                $this->session->set_userdata('CR_course_id',$course_id);
                $this->session->set_userdata('CR_course_name',$course_name);
                $this->session->set_userdata('CR_module_name',$module_name);
                $this->session->set_userdata('CR_group_name',$group_name);
                $this->session->set_userdata('CR_subject_name',$subject_name);
                
                log_message('debug','---------------------------------------------------------');
                log_message('debug','| Current Session Details');
                log_message('debug','| Current Course ID     > '.$course_id);
                log_message('debug','| Current Course Name   > '.$course_name);
                log_message('debug','| Current Course Module > '.$module_name);
                log_message('debug','| Current Course Group  > '.$group_name);
                log_message('debug','| Current Subject Name  > '.$subject_name);
                log_message('debug','---------------------------------------------------------');
                $this->load->view('student/course_subjects_resource_view',$data);
            }    
        }
    //------------------------------------------------------------------------------------
    //  Course Module Group Subject codeEnd
    //------------------------------------------------------------------------------------

    // Action -> Get Course Menu 
    public function course_tree(){
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $course_id = $this->input->post('course_id');
            $data['course_details'] = $this->resource_model->get_courses_details($course_id);
            $data['course_tree'] = $this->resource_model->course_tree($course_id);
            $course_details = $data['course_details'];
            // Setting Current Session Details : Course ID , Course Name
            $this->session->set_userdata('CR_course_id',$course_id);
            $this->session->set_userdata('CR_course_name',$course_details['course_name']);
            log_message('debug','---------------------------------------------------------');
            log_message('debug','| Current Session Details');
            log_message('debug','| Current Course ID   > '.$course_id);
            log_message('debug','| Current Course Name > '.$course_details['course_name']);
            log_message('debug','---------------------------------------------------------');
            // Loading Coure Tree View
            $this->load->view('student/course_tree',$data);
        }
    }
    // Action : To Display Course Syllabus in Student Side
    public function view_course_syllabus(){
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $course_id = $this->input->post('course_id');
            $file_name = $this->input->post('file_name');
            $course_details = $this->resource_model->get_courses_details($course_id);
            $data['course_details'] = $course_details;
            $data['file_name'] = $course_details['course_syllabus_file'];
            if($course_details['course_syllabus_file'] != 'NULL'){
                // Setting Current Session Details : Course ID , Course Name
                $this->session->set_userdata('CR_course_id',$course_id);
                $this->session->set_userdata('CR_course_name',$course_details['course_name']);
                log_message('debug','---------------------------------------------------------');
                log_message('debug','| Current Session Details');
                log_message('debug','| Current Course ID   > '.$course_id);
                log_message('debug','| Current Course Name > '.$course_details['course_name']);
                log_message('debug','| Current Syllabus    > '.$course_details['course_syllabus_file']);
                $this->load->view('student/view_course_syllabus_modal',$data);
                log_message('debug','---------------------------------------------------------');
            } else {
                log_message('debug','---------------------------------------------------------');
                log_message('debug','| Current Session Details');
                log_message('debug','| Current Course ID   > '.$course_id);
                log_message('debug','| Current Course Name > '.$course_details['course_name']);
                log_message('debug','| Course Syllabus File > Not Yet Added !');
                $this->load->view('student/course_syllabus_not_found',$data);
                log_message('debug','---------------------------------------------------------');
            }
        }
    }
    // Action -> Get Course Subjects Menu Grid
    public function course_subjects_grid(){
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $course_id = $this->input->post('course_id');
            $data['course_details'] = $this->resource_model->get_courses_details($course_id);
            $data['course_tree'] = $this->resource_model->course_tree($course_id);
            //$data['schedule_count'] = $this->resource_model->get_cs_subject_schedule();
            $this->load->view('student/course_subjects_grid',$data);
        }
    }

    // Action -> Get Course Subjects Menu List
    public function course_subjects_list(){
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $course_id = $this->input->post('course_id');
            $data['course_details'] = $this->resource_model->get_courses_details($course_id);
            $data['course_tree'] = $this->resource_model->course_tree($course_id);
            $this->load->view('student/course_subjects_list',$data);
        }
    }

    // Action -> Get All Subject Resources 
    public function subject_resources(){
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $course_id       = $this->input->post('course_id');
            $course_name     = $this->session->userdata('CR_course_name');
            $subject_name    = $this->input->post('subject_name');
            $resource_list   = $this->resource_model->get_cs_resource($course_id,$subject_name);
            $assessment_list = $this->resource_model->get_cs_assessment($course_id,$subject_name);
            // Load data's
            $data['subject_name'] = $subject_name;
            if($resource_list != null){
                $data['resource_list'] = $resource_list;
                log_message('debug','resource list generated');
            }
            if($assessment_list != null){
                $data['assessment_list'] = $assessment_list;
                log_message('debug','Assessment list Generated');
            }
            // Setting Current Session Details : Course ID , Course Name, Subject Name
            $this->session->set_userdata('CR_course_id',$course_id);
            $this->session->set_userdata('CR_course_name',$course_name);
            $this->session->set_userdata('CR_subject_name',$subject_name);
            log_message('debug','---------------------------------------------------------');
            log_message('debug','| Current Session Details');
            log_message('debug','| Current Course ID   > '.$course_id);
            log_message('debug','| Current Course Name > '.$course_name);
            log_message('debug','| Current Subject Name > '.$subject_name);
            log_message('debug','---------------------------------------------------------');
            $this->load->view('student/resourse_view',$data);
        }
    }

    // Action -> open resource 
    public function open_pdf_resource(){
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $data['course_name']    = $this->session->userdata('CR_course_name');
            
            $data['course_id']      = $this->input->post('course_id');
            $data['module_name']    = $this->input->post('module_name');
            $data['group_name']     = $this->input->post('group_name');
            $data['subject_name']   = $this->input->post('subject_name');
            $data['resource_id']    = $this->input->post('resource_id');
            $data['resource_link']  = $this->input->post('resource_link');
            
            $resource_detail        = $this->resource_model->get_resource_details($data['resource_id']);
            $data['resource_detail']= $resource_detail;
            $course_id      = $data['course_id'];
            $course_name    = $data['course_name'];
            $subject_name   = $data['subject_name'];
            $resource_id    = $resource_detail['resource_id'];
            $resource_link  = $resource_detail['resource_link'];
            
            // Setting Current Session Details : Course ID , Course Name, Subject Name , Resource ID, Resource Name , Resource Type , Resource Link
            $this->session->set_userdata('CR_course_id',$course_id);
            $this->session->set_userdata('CR_course_name',$course_name);
            $this->session->set_userdata('CR_module_name',$data['module_name']);
            $this->session->set_userdata('CR_group_name',$data['group_name']);
            $this->session->set_userdata('CR_subject_name',$subject_name);
            $this->session->set_userdata('CR_resource_id',$data['resource_id']);
            $this->session->set_userdata('CR_resource_name',$subject_name);
            $this->session->set_userdata('CR_resource_link',$data['resource_link']);

            log_message('debug','---------------------------------------------------------');
            log_message('debug','| Current Session Details');
            log_message('debug','| Current Course ID    >    '.$course_id);
            log_message('debug','| Current Course Name  >    '.$course_name);
            log_message('debug','| Current Module Name  >    '.$data['module_name']);
            log_message('debug','| Current Group Name   >    '.$data['group_name']);
            log_message('debug','| Current Subject Name >    '.$subject_name);
            log_message('debug','| Current Resource ID  >    '.$data['resource_id']);
            log_message('debug','| Current Resource URL >    '.$data['resource_link']);
            log_message('debug','---------------------------------------------------------');

            $this->load->view('student/pdf_view',$data);
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

    // Action -> Generate embeded IFRAME Code  Need TOI From Kiruthiga 
    public function embed() {
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
            $json = json_encode(array("html" => "<h3>Video not available. Please try later.</h3>"));
            echo $json;
        }
    }

    // Action -> open resource 
    public function open_video_resource(){
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $data['course_name']    = $this->session->userdata('CR_course_name');
            // Gathering from post inputs
            $data['course_id']      = $this->input->post('course_id');
            $data['module_name']    = $this->input->post('module_name');
            $data['group_name']     = $this->input->post('group_name');
            $data['subject_name']   = $this->input->post('subject_name');
            $data['resource_id']    = $this->input->post('resource_id');
            $data['resource_link']  = $this->input->post('resource_link');

            $resource_detail        = $this->resource_model->get_resource_details($data['resource_id']);
            $data['resource_detail']= $resource_detail;

            // Setting Current Session Details : Course ID , Course Name, Subject Name , Resource ID, Resource Name , Resource Type , Resource Link
            $this->session->set_userdata('CR_course_id',$data['course_id']);
            $this->session->set_userdata('CR_course_name',$data['course_name']);
            $this->session->set_userdata('CR_module_name',$data['module_name']);
            $this->session->set_userdata('CR_group_name',$data['group_name']);
            $this->session->set_userdata('CR_subject_name',$data['subject_name']);
            $this->session->set_userdata('CR_resource_id',$data['resource_id']);
            $this->session->set_userdata('CR_resource_name',$resource_detail['resource_name']);
            $this->session->set_userdata('CR_resource_link',$data['resource_link']);

            log_message('debug','---------------------------------------------------------');
            log_message('debug','| Current Session Details');
            log_message('debug','| Current Course ID    >    '.$data['course_id']);
            log_message('debug','| Current Course Name  >    '.$data['course_name']);
            log_message('debug','| Current Module Name  >    '.$data['module_name']);
            log_message('debug','| Current Group Name   >    '.$data['group_name']);
            log_message('debug','| Current Subject Name >    '.$data['subject_name']);
            log_message('debug','| Current Resource ID  >    '.$data['resource_id']);
            log_message('debug','| Current Resource Name>    '.$resource_detail['resource_name']);
            log_message('debug','| Current Resource URL >    '.$data['resource_link']);
            log_message('debug','---------------------------------------------------------');

            $this->load->view('student/video_view',$data);
        }
    }

    // Action -> open captiva resource 
    public function open_captiva_resource(){
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $data['course_name']    = $this->session->userdata('CR_course_name');
            // Gathering from post inputs
            $data['course_id']      = $this->input->post('course_id');
            $data['module_name']    = $this->input->post('module_name');
            $data['group_name']     = $this->input->post('group_name');
            $data['subject_name']   = $this->input->post('subject_name');
            $data['resource_id']    = $this->input->post('resource_id');
            // $data['resource_link']  = $this->input->post('resource_link');

            $resource_detail        = $this->resource_model->get_resource_details($data['resource_id']);
            $data['resource_detail']= $resource_detail;
            $data['resource_link']  = $resource_detail['resource_link'];

            // Setting Current Session Details : Course ID , Course Name, Subject Name , Resource ID, Resource Name , Resource Type , Resource Link
            $this->session->set_userdata('CR_course_id',$data['course_id']);
            $this->session->set_userdata('CR_course_name',$data['course_name']);
            $this->session->set_userdata('CR_module_name',$data['module_name']);
            $this->session->set_userdata('CR_group_name',$data['group_name']);
            $this->session->set_userdata('CR_subject_name',$data['subject_name']);
            $this->session->set_userdata('CR_resource_id',$data['resource_id']);
            $this->session->set_userdata('CR_resource_name',$resource_detail['resource_name']);
            $this->session->set_userdata('CR_resource_link',$resource_detail['resource_link']);

            log_message('debug','---------------------------------------------------------');
            log_message('debug','| Current Session Details');
            log_message('debug','| Current Course ID    >    '.$data['course_id']);
            log_message('debug','| Current Course Name  >    '.$data['course_name']);
            log_message('debug','| Current Module Name  >    '.$data['module_name']);
            log_message('debug','| Current Group Name   >    '.$data['group_name']);
            log_message('debug','| Current Subject Name >    '.$data['subject_name']);
            log_message('debug','| Current Resource ID  >    '.$data['resource_id']);
            log_message('debug','| Current Resource Name>    '.$resource_detail['resource_name']);
            log_message('debug','| Current Resource URL >    '.$resource_detail['resource_link']);
            log_message('debug','---------------------------------------------------------');

            $this->load->view('student/captiva_view',$data);
        }
    }
    
    // Action -> open captiva quiz resource 
    public function open_captiva_quiz_resource(){
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $data['course_name']    = $this->session->userdata('CR_course_name');
            // Gathering from post inputs
            $data['course_id']      = $this->input->post('course_id');
            $data['module_name']    = $this->input->post('module_name');
            $data['group_name']     = $this->input->post('group_name');
            $data['subject_name']   = $this->input->post('subject_name');
            $data['resource_id']    = $this->input->post('resource_id');
            // $data['resource_link']  = $this->input->post('resource_link');

            $resource_detail        = $this->resource_model->get_resource_details($data['resource_id']);
            $data['resource_detail']= $resource_detail;
            $data['resource_link']  = $resource_detail['resource_link'];

            // Setting Current Session Details : Course ID , Course Name, Subject Name , Resource ID, Resource Name , Resource Type , Resource Link
            $this->session->set_userdata('CR_course_id',$data['course_id']);
            $this->session->set_userdata('CR_course_name',$data['course_name']);
            $this->session->set_userdata('CR_module_name',$data['module_name']);
            $this->session->set_userdata('CR_group_name',$data['group_name']);
            $this->session->set_userdata('CR_subject_name',$data['subject_name']);
            $this->session->set_userdata('CR_resource_id',$data['resource_id']);
            $this->session->set_userdata('CR_resource_name',$resource_detail['resource_name']);
            $this->session->set_userdata('CR_resource_link',$resource_detail['resource_link']);

            log_message('debug','---------------------------------------------------------');
            log_message('debug','| Current Session Details');
            log_message('debug','| Current Course ID    >    '.$data['course_id']);
            log_message('debug','| Current Course Name  >    '.$data['course_name']);
            log_message('debug','| Current Module Name  >    '.$data['module_name']);
            log_message('debug','| Current Group Name   >    '.$data['group_name']);
            log_message('debug','| Current Subject Name >    '.$data['subject_name']);
            log_message('debug','| Current Resource ID  >    '.$data['resource_id']);
            log_message('debug','| Current Resource Name>    '.$resource_detail['resource_name']);
            log_message('debug','| Current Resource URL >    '.$resource_detail['resource_link']);
            log_message('debug','---------------------------------------------------------');

            $this->load->view('student/captiva_quiz_view',$data);
        }
    }

    // Action -> open resource 
    public function open_assessment_resource(){
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $course_id      = $this->input->post('course_id');
            $subject_name   = $this->input->post('subject_name');
            $resource_id    = $this->input->post('resource_id');
            $resource_link  = $this->input->post('resource_link');
            $this->load->view('student/pdf_view',$data);
        }
    }


  
    // Action : Open test page
    public function open_test_page(){
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $user_id                = $this->session->userdata('user_id');  
            $data['course_name']    = $this->session->userdata('CR_course_name');
            $data['user_id']        = $user_id;
            $data['course_id']      = $this->input->post('course_id');
            $data['module_name']    = $this->input->post('module_name');
            $data['group_name']     = $this->input->post('group_name');
            $data['subject_name']   = $this->input->post('subject_name');
            $data['test_id']        = $this->input->post('test_id');
            $data['test_name']      = $this->input->post('test_name');
            $data['margin_value']   = "80";  // For Getting More than 80 % status

            $data['assessment_detail'] = $this->resource_model->get_assessment_details($data['test_id']);

            $assessment_data= $data['assessment_detail'];
            $course_id      = $data['course_id'];
            $course_name    = $data['course_name'];
            $subject_name   = $data['subject_name'];
            $test_name      = $data['test_name'];
            $data['test_no'] = $assessment_data['test_no'];
            $test_id        = $data['test_id'];
            // For Getting Answer Key Status           
            $answer_key_status = $this->resource_model->get_answer_key_status($data);
            log_message('debug','Assessment Key Status : '.$answer_key_status);
            $data['answer_key_status'] = $answer_key_status; 
            $data['attempt_details'] = $this->resource_model->get_test_attempt($user_id,$course_id,$assessment_data['test_no']);
            // Setting Current Session Details : Course ID , Course Name, Subject Name , Resource ID, Resource Name , Resource Type , Resource Link
            $this->session->set_userdata('CR_course_id',$course_id);
            $this->session->set_userdata('CR_course_name',$course_name);
            $this->session->set_userdata('CR_module_name',$data['module_name']);
            $this->session->set_userdata('CR_group_name',$data['group_name']);
            $this->session->set_userdata('CR_subject_name',$subject_name);
            $this->session->set_userdata('CR_test_id',$test_id);
            $this->session->set_userdata('CR_test_no',$assessment_data['test_no']);
            $this->session->set_userdata('CR_test_name',$test_name);
            $this->session->set_userdata('CR_assessment_details',$assessment_data);
            //$this->session->set_userdata('CR_resource_link',$data['resource_link']);

            log_message('debug','---------------------------------------------------------');
            log_message('debug','| Current Session Details');
            log_message('debug','| Current Course ID    >    '.$course_id);
            log_message('debug','| Current Course Name  >    '.$course_name);
            log_message('debug','| Current Module Name  >    '.$data['module_name']);
            log_message('debug','| Current Group Name   >    '.$data['group_name']);
            log_message('debug','| Current Subject Name >    '.$subject_name);
            log_message('debug','| Current Test ID      >    '.$data['test_id']);
            log_message('debug','| Current Test No      >    '.$assessment_data['test_no']);
            log_message('debug','| Current Test Name    >    '.$data['test_name']);
            log_message('debug','---------------------------------------------------------');

            $this->load->view('student/open_test_page',$data);
        }
    }

    // Action: start test page

    function start_test_page(){
        if($this->input->server('REQUEST_METHOD') == 'GET'){
            // Getting Session Details 
            $user_id                    = $this->session->userdata('user_id');
            $course_id                  = $this->session->userdata('CR_course_id');
            $data['course_name']        = $this->session->userdata('CR_course_name');
            $assessment_details         = $this->session->userdata('CR_assessment_details');

            // $data['course_id']          = $this->input->post('course_id');
            // $data['subject_name']       = $this->input->post('subject_name');
            // $data['test_id']            = $this->input->post('test_id');
            // $data['test_name']          = $this->input->post('test_name');
            $data['course_id']          = $course_id;
            $data['subject_name']       = $this->session->userdata('CR_subject_name');
            $data['test_id']            = $assessment_details['test_id'];
            $data['test_name']          = $assessment_details['test_name'];
            $data['assessment_details'] = $assessment_details;
            // Getting New Values 
            $course_id                  = $data['course_id'];
            $course_name                = $data['course_name'];
            $subject_name               = $data['subject_name'];
            $test_name                  = $data['test_name'];
            $test_id                    = $data['test_id'];
            $test_no                    = $assessment_details['test_no'];
            
            $isFirstAttempt = $this->resource_model->get_incomplete_attempt($user_id,$course_id,$test_no);
            if($isFirstAttempt == null){
                // Collecting Attempt Data From session
                $attempt['user_id']         = $user_id;
                $attempt['course_id']       = $course_id;
                $attempt['subject_name']    = $subject_name;
                $attempt['test_no']         = $assessment_details['test_no'];
                $attempt['test_type']       = $assessment_details['test_type'];
                $attempt['test_name']       = $assessment_details['test_name'];
                $attempt['no_of_questions'] = $assessment_details['no_of_questions'];
                $attempt['answer_key']      = $assessment_details['answer_key'];
                $data['new_attempt']        = 1;
                // Initilizing Student Answer 
                $attempt['student_answer'] = $this->init_student_answer($attempt['no_of_questions']);
                $attempt['remaining_time'] = $assessment_details['test_duration'];
                // $attempt['test_score']      = $this->calculatescore($attempt['answer_key'], $attempt['student_answer']);
                // $attempt['test_percentage'] = $this->calculate_percentage($attempt['no_of_questions'], $attempt['test_score']);
                

                log_message('debug','Attempt user_id      : '.$attempt['user_id']);
                log_message('debug','Attempt course_id    : '.$attempt['course_id']);
                log_message('debug','Attempt subject_name : '.$attempt['subject_name']);
                log_message('debug','Attempt test_no      : '.$attempt['test_no']);
                log_message('debug','Attempt test_type    : '.$attempt['test_type']);
                log_message('debug','Attempt test_name    : '.$attempt['test_name']);
                log_message('debug','Attempt no_of_questions: '.$attempt['no_of_questions']);
                log_message('debug','Attempt answer_key   : '.$attempt['answer_key']);    
                log_message('debug','Attempt remaining_time: '.$attempt['remaining_time']);    
                // log_message('debug','ANS: '.$attempt['student_answer']);
                // log_message('debug','test score: '.$attempt['test_score']);
                $result = $this->resource_model->start_test($attempt);
                if($result != null) { 
                    $data['attempt_id'] = $result;
                    $data['attempt_details'] = $this->resource_model->get_attempt_details($data['attempt_id']);
                } else { 
                    log_message('debug','First Attempt Entry Faild please Check in Start Test Function'); 
                }
            } else {
                $data['new_attempt']        = 0;
                $data['attempt_details'] = $isFirstAttempt;
                $data['attempt_id']      = $isFirstAttempt['attempt_id'];
            }
           
            // Setting Current Session Details : Course ID , Course Name, Subject Name , Resource ID, Resource Name , Resource Type , Resource Link
            $this->session->set_userdata('CR_course_id',$course_id);
            $this->session->set_userdata('CR_course_name',$course_name);
            $this->session->set_userdata('CR_subject_name',$subject_name);
            $this->session->set_userdata('CR_test_id',$test_id);
            $this->session->set_userdata('CR_test_name',$test_name);
            $this->session->set_userdata('CR_assessment_details',$assessment_details);
            $this->session->set_userdata('CR_attempt_id',$data['attempt_id']);

            log_message('debug','---------------------------------------------------------');
            log_message('debug','| Current Session Details');
            log_message('debug','| Current Course ID            >    '.$course_id);
            log_message('debug','| Current Course Name          >    '.$course_name);
            log_message('debug','| Current Subject Name         >    '.$subject_name);
            log_message('debug','| Current Test ID              >    '.$data['test_id']);
            log_message('debug','| Current Attempt ID           >    '.$data['attempt_id']);
            log_message('debug','---------------------------------------------------------');

            $this->load->view('student/start_test_page',$data);
        }
            
    }



    // Action: This View Attempt can see only inside Course Subject Test
    function view_test_attempts(){
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $data['course_name']        = $this->session->userdata('CR_course_name');
            $data['module_name']        = $this->session->userdata('CR_module_name');
            $data['group_name']         = $this->session->userdata('CR_group_name');

            $data['course_id']          = $this->input->post('course_id');
            $data['subject_name']       = $this->input->post('subject_name');
            $data['test_id']            = $this->input->post('test_id');
            $data['test_name']          = $this->input->post('test_name');
            $data['attempt_id']         = $this->input->post('attempt_id');
            
            $data['assessment_details'] = $this->resource_model->get_assessment_details($data['test_id']);
            $data['attempt_details']    = $this->resource_model->get_attempt_details($data['attempt_id']);
            $assessment_details         = $data['assessment_details'];
            $course_id                  = $data['course_id'];
            $course_name                = $data['course_name'];
            $subject_name               = $data['subject_name'];
            $test_name                  = $data['test_name'];
            $test_id                    = $data['test_id'];
           
            // Setting Current Session Details : Course ID , Course Name, Subject Name , Resource ID, Resource Name , Resource Type , Resource Link
            $this->session->set_userdata('CR_course_id',$course_id);
            $this->session->set_userdata('CR_course_name',$course_name);
            $this->session->set_userdata('CR_subject_name',$subject_name);
            $this->session->set_userdata('CR_test_id',$test_id);
            $this->session->set_userdata('CR_test_name',$test_name);
            $this->session->set_userdata('CR_assessment_details',$assessment_details);
            

            log_message('debug','---------------------------------------------------------');
            log_message('debug','| Current Session Details');
            log_message('debug','| Current Course ID            >    '.$course_id);
            log_message('debug','| Current Course Name          >    '.$course_name);
            log_message('debug','| Current Module Name          >    '.$data['module_name']);
            log_message('debug','| Current Group Name           >    '.$data['group_name']);
            log_message('debug','| Current Subject Name         >    '.$subject_name);
            log_message('debug','| Current Test ID              >    '.$data['test_id']);
            log_message('debug','| Current Test Name            >    '.$data['test_name']);
            log_message('debug','---------------------------------------------------------');


            $this->load->view('student/view_test_attempts',$data);
        }
            
    }


    // Action: This View Attempt can see only inside Course Subject Test
    function show_answer_key(){
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $data['course_name']        = $this->session->userdata('CR_course_name');
            $data['module_name']        = $this->session->userdata('CR_module_name');
            $data['group_name']         = $this->session->userdata('CR_group_name');

            $data['course_id']          = $this->input->post('course_id');
            $data['subject_name']       = $this->input->post('subject_name');
            $data['test_id']            = $this->input->post('test_id');
            $data['test_name']          = $this->input->post('test_name');
            $data['attempt_id']         = $this->input->post('attempt_id');
            
            $data['assessment_details'] = $this->resource_model->get_assessment_details($data['test_id']);
            $data['attempt_details']    = $this->resource_model->get_attempt_details($data['attempt_id']);
            $assessment_details         = $data['assessment_details'];
            $course_id                  = $data['course_id'];
            $course_name                = $data['course_name'];
            $subject_name               = $data['subject_name'];
            $test_name                  = $data['test_name'];
            $test_id                    = $data['test_id'];
           
            // Setting Current Session Details : Course ID , Course Name, Subject Name , Resource ID, Resource Name , Resource Type , Resource Link
            $this->session->set_userdata('CR_course_id',$course_id);
            $this->session->set_userdata('CR_course_name',$course_name);
            $this->session->set_userdata('CR_subject_name',$subject_name);
            $this->session->set_userdata('CR_test_id',$test_id);
            $this->session->set_userdata('CR_test_name',$test_name);
            $this->session->set_userdata('CR_assessment_details',$assessment_details);
            

            log_message('debug','---------------------------------------------------------');
            log_message('debug','| Current Session Details');
            log_message('debug','| Current Course ID            >    '.$course_id);
            log_message('debug','| Current Course Name          >    '.$course_name);
            log_message('debug','| Current Module Name          >    '.$data['module_name']);
            log_message('debug','| Current Group Name           >    '.$data['group_name']);
            log_message('debug','| Current Subject Name         >    '.$subject_name);
            log_message('debug','| Current Test ID              >    '.$data['test_id']);
            log_message('debug','| Current Test Name            >    '.$data['test_name']);
            log_message('debug','---------------------------------------------------------');


            $this->load->view('student/show_answer_key',$data);
        }
            
    }

    // Calculate Score For Test
    protected function calculatescore($answer_key,$student_answer){
        $questionanswer = explode(",",$student_answer);
        $ans_key = explode(",",$answer_key);
        $score = 0;
        $percentage = 0;
        for($i = 0;$i<sizeof($ans_key);$i++) {

            if($ans_key[$i] == $questionanswer[$i])
                $score += 1; // $score += 4; OLD Positive Calc
            else if($ans_key[$i] != $questionanswer[$i]  && $questionanswer[$i] != 'null')
                $score -= 0; // $score -= 1; OLD Negtive calc
            log_message('debug',$i." ".$score);
        }
        return $score;
    }

    protected function calculate_percentage($no_of_questions,$score)
    {
        // $percentage = ($score/($no_of_questions * 4)) * 100;
        $percentage = ($score/($no_of_questions * 1)) * 100;
        return $percentage;
    }

    protected function init_student_answer($no_of_questions){
        // Generation of Student Answer 
        $student_answer = "";
        for($i=0;$i<$no_of_questions;$i++){ 
            if($i === $no_of_questions-1) { 
                $student_answer .="null"; 
            } else { 
                $student_answer .= "null,"; 
            }  
        }
        return $student_answer;     
    }

    // Action: last answered test 
    public function last_answered_test(){
        if($this->input->server('REQUEST_METHOD') == 'POST'){

            // Collect Attempt Data From User
            $assessment_details    = $this->session->userdata('CR_assessment_details');
            $attempt['attempt_id'] = $this->session->userdata('CR_attempt_id');
            // Getting Last Student Answer
            $attempt['student_answer']  = $this->input->post('last_student_answer');
            $attempt['test_score']      = $this->calculatescore($assessment_details['answer_key'], $attempt['student_answer']);
            $attempt['test_percentage'] = $this->calculate_percentage($assessment_details['no_of_questions'], $attempt['test_score']);
            $attempt['submit_status']   = "0";
            $attempt['remaining_time'] = $this->input->post('duration');
            // $test_no                = $this->session->userdata('CR_test_no');

            log_message('debug','Assessment Last Ans Update ---------------------------------------');
            log_message('debug','Attempt id                   ---'.$attempt['attempt_id']);
            log_message('debug','Attempt last_student_answer  ---'.$attempt['student_answer']);
            log_message('debug','Attempt test_score           ---'.$attempt['test_score']);
            log_message('debug','Attempt test_percentage      ---'.$attempt['test_percentage']);
            log_message('debug','Attempt submit_status        ---'.$attempt['submit_status']);
              log_message('debug','Attempt duration   ---'.$attempt['remaining_time']);
            log_message('debug','-----------------------------------------------------------');

            $result = $this->resource_model->save_answer_sheet($attempt);
            if($result == 1) { 
                echo "true";
            } else { echo "false"; }
        }
    }

    // Action: Saving Student Assessment Answers in DB
    function save_student_answers(){
        if($this->input->server('REQUEST_METHOD') == 'POST'){

            // Collect Attempt Data From User
            $assessment_details         = $this->session->userdata('CR_assessment_details');
            $attempt['attempt_id']      = $this->session->userdata('CR_attempt_id');
            $attempt['test_subject']    = $this->session->userdata('CR_subject_name');
            $attempt['student_answer']  = $this->input->post('student_answer');
            $attempt['test_score']      = $this->calculatescore($assessment_details['answer_key'], $attempt['student_answer']);
            $attempt['test_percentage'] = $this->calculate_percentage($assessment_details['no_of_questions'], $attempt['test_score']);
            $attempt['submit_status']   = "1";
            $attempt['remaining_time'] = $this->input->post('duration');
            // $test_no         = $this->session->userdata('CR_test_no');

            log_message('debug','Assessment Submited ---------------------------------------');
            log_message('debug','Attempt id              ---'.$attempt['attempt_id']);
            log_message('debug','Attempt student_answer  ---'.$attempt['student_answer']);
            log_message('debug','Attempt test_score      ---'.$attempt['test_score']);
            log_message('debug','Attempt test_percentage ---'.$attempt['test_percentage']);
            log_message('debug','Attempt submit_status   ---'.$attempt['submit_status']);
            log_message('debug','Attempt duration        ---'.$attempt['remaining_time']);
            log_message('debug','-----------------------------------------------------------');

            $result = $this->resource_model->save_answer_sheet($attempt);
            if($result == 1) { 
                echo "true";
            } else { echo "false"; }
        }
    }

    // Action :
    public function report_dashboard(){
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $student_id = $this->session->userdata('user_id');
            $data['my_courses'] = $this->batch_model->get_my_paid_courses($student_id);
            $data['paid_aoi']   = $this->batch_model->get_paid_aoi_list($student_id);
            $this->load->view('student/student_rank_report',$data);
        }
    }


    // Action  : The below function gives the list of courses of the student based on user id
    public function get_my_courses(){
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $student_id = $this->session->userdata('user_id');
            $data['my_courses'] = $this->batch_model->get_my_paid_courses($student_id);
            $this->load->view('student/course_list',$data);
        }
    }

    // Action : The below function gives all the test numbers that i have atteneded
    public function get_my_tests(){
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $student_id = $this->session->userdata('user_id');
            $course_id  = $this->input->post('course_id');
            $data['my_tests'] = $this->user_model->get_students_test($course_id);
            $this->load->view('student/test_list',$data);
        }   
    }

    //Action : The function gives me all scores of students who has attended the test
    public function get_my_rank(){
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $test_no   = $this->input->post('test_no');
            $course_id = $this->input->post('course_id');
            // $batchname = $this->session->userdata('user_state');

            // USK -------------------------------------------------------------------------------------------
            $user_id   = $this->session->userdata('user_id');
            $batch_details = $this->batch_model->get_batch_details_userid_course_id($user_id,$course_id);
            $batchname = $batch_details['batch_name'];
            // -------------------------------------------------------------------------------------------
            // National Level Ranking
            $data['national_rank_list'] = $this->report_model->get_student_ranks($test_no,$course_id);
            $data['national_total_ranks'] = count($data['national_rank_list']);
            // log_message('debug','Total Students are '.$count);
            
            $user_id = $this->session->userdata('user_id');
            if($data['national_total_ranks'] > 0){

                $national_ranks = $data['national_rank_list'];
                $count = 0;
                $national_level_my_rank = 0 ;
                foreach ($national_ranks as $res){
                    $count++;
                    if($user_id == $res->user_id){
                        $national_level_my_rank = $count;
                        // $data['my_rank'] = $count;
                        $data['registration_no']    = $res->registration_no;
                        $data['user_fname']         = $res->user_fname;
                        $data['test_no']            = $res->test_no;
                        $data['test_name']          = $res->test_name;
                        $data['test_date']          = $res->test_date;
                        $data['total_marks']        = $res->no_of_questions *4;
                        $data['test_score']         = $res->test_score;
                        $data['test_percentage']    = $res->test_percentage;
                        break;
                    }
                }
                $data['national_level_my_rank'] = $national_level_my_rank;

                // USK -- Country Level Ranking NEW:
                // Get Course_id,Test NO, Country Name
                $search_filter['course_id'] = $course_id;
                $search_filter['test_no'] = $test_no;
                $search_filter['user_country'] = $this->session->userdata('user_country');
                $data['country_level_rank_list'] = $this->report_model->get_new_student_ranks($search_filter);
                $data['country_wise_total_ranks'] = count($data['country_level_rank_list']);
                // log_message('debug','Total Students are '.$count);
                
                $country_wise_ranks = $data['country_level_rank_list'];
                $count = 0;
                $country_level_my_rank = 0 ;
                if($data['country_wise_total_ranks']>0){
                    foreach ($country_wise_ranks as $res){
                        $count++;
                        if($user_id == $res->user_id){
                            $country_level_my_rank = $count;
                            break;
                        }
                    }
                    $data['country_level_my_rank'] = $country_level_my_rank;                    
                }
                // ----------------------------------




                // Batch Level Ranking
                $data['batchwise_rank_list'] = $this->report_model->get_batchwise_student_ranks($test_no,$batchname,$course_id);

                $data['batchwise_total_ranks'] = count($data['batchwise_rank_list']);
                // log_message('debug','Total Students are '.$count);
                
                $batchwise_ranks = $data['batchwise_rank_list'];
                $count = 0;
                $batch_level_my_rank = 0 ;
                if($data['batchwise_total_ranks']>0){
                    foreach ($batchwise_ranks as $res){
                        $count++;
                        if($user_id == $res->user_id){
                            $batch_level_my_rank = $count;
                            break;
                        }
                    }
                    $data['batch_level_my_rank'] = $batch_level_my_rank;                    
                }

            }else{
                $data['national_level_my_rank'] = 0;
                $data['batch_level_my_rank'] = 0;
                $data['country_level_my_rank'] = 0;
            }

            $this->load->view('student/student_rank_details',$data);
        }
    }

    // Action based on the course select show the graph data
    function subject_for_chart(){
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $course_id  = $this->input->post('course_id');
            $student_id = $this->session->userdata('user_id');
            $data['list_subjects'] = $this->user_model->get_subject_under_course($course_id,$student_id);
             
            $this->load->view('student/load_subject_for_graph',$data);
        }
    }

    // plot graph for each subject
    function load_subject_score_graph(){
        if ($this->input->server('REQUEST_METHOD')) {
            $student_id      = $this->session->userdata('user_id'); 
            $subject_name    = $this->input->post('subject_name');
            $course_id       = $this->input->post('course_id');
            $data['load_graph_data'] = $this->user_model->get_subject_details($student_id,$subject_name,$course_id);
            $this->load->view('student/plot_student_graph',$data);
        }
    }

    // Action  : used to get course list on select of AOI in User report menu 
    public function get_my_courses_list(){
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $area_of_int = $this->input->post('area_of_int');
            $student_id = $this->session->userdata('user_id');
            // $data['my_courses'] = $this->batch_model->get_my_paid_courses($student_id);
            $data['my_courses'] = $this->batch_model->get_my_paid_courses1($area_of_int);
            $this->load->view('student/student_rank_course',$data);
        }
    }

}