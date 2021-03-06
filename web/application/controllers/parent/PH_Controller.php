<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class PH_Controller extends PG_Controller {    
    public function __construct(){
    	parent::__construct();
        $this->load->model('user_model');
        $this->load->model('batch_model');
        $this->load->model('report_model');
    }

    public function index(){
    	$data['data'] = $this->session->all_userdata();
        $data['load_view'] = $this->session->flashdata('load_view');
        $data['user_home'] = "/parent_home";
        $data['role_view'] = "/parent/parent_body_leftpan";
        $this->load->view('user_header',$data);
        $this->load->view('user_body_leftpan');
        $this->load->view('user_body_rightpan');
        $this->load->view('user_footer');
        log_message('debug', 'Parent home');
    }

    public function dashboard(){
        if($this->input->server('REQUEST_METHOD') == 'GET'){
            $data['user_details'] = $this->session->all_userdata();
            $this->load->view('parent/dashboard_rightpan',$data);
        }
    }

    // 
    public function child_of_parents() {  // this is over dont tuch it  
        if($this->input->server('REQUEST_METHOD') == 'POST') {
            $parent_id                 = $this->session->userdata('user_id');
            $data['user_details']      = $this->session->all_userdata();
            $data['students_details']  = $this->report_model->get_parent_children($parent_id);
            $this->load->view('parent/child_details',$data);
        }
    }

    // Make DRY : Action : Parents Management
    public function parent_child_details() {
        if($this->input->server('REQUEST_METHOD') == 'POST') {
            $child_id = $this->input->post('user_id');
            $this->session->set_userdata('CR_child_id',$child_id);
            $child_details = $this->user_model->get_userid_details($child_id);
            $this->session->set_userdata('CR_child_batch',$child_details['user_state']);
            // log_message('debug','current child id is '.$child_id);
            $this->load->view('parent/parent_child_details');
        }
    }

    // Action : Parents List
    public function parent_child_details_list(){
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $user_id = $this->session->userdata('CR_child_id');
            // $data['user_details']    = $this->session->all_userdata();
            $data['parents_details'] = $this->user_model->get_parent_child_list_view($user_id);
            // log_message('debug','PArent details are '.$parents_details);
            $this->load->view('parent/parent_child_details_list',$data);
        }
    }

    public function student_assessment_rank(){
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $test_no    = $this->input->post('test_no');
            $user_id    = $this->session->userdata('CR_child_id');
            $course_id  = $this->session->userdata('CRS_course_id');
            $SUser_details = $this->user_model->get_userid_details($user_id);
            $user_country = $SUser_details['user_country'];
            $user_state   = $SUser_details['user_state'];

            log_message('debug',"+++++++++++ Country Name ".$user_country);

            // Inter National Level Rank List
            $search_filter['test_no']       = $test_no;
            $search_filter['course_id']     = $course_id;
            $total_in_rank_list = $this->report_model->get_new_student_ranks($search_filter);
            $search_filter['user_id']       = $user_id;
            $data['rank_list'] = $this->report_model->get_new_student_ranks($search_filter);
            $data['in_rank_count'] = count($total_in_rank_list);
            $in_rank_list = $total_in_rank_list;
            $count = 0;
            if($data['in_rank_count'] > 0){
                foreach ($in_rank_list as $res) {
                    $count++;
                    if($res->user_id == $user_id){
                        $user_in_rank_count = $count;
                        break;
                    }
                }
            } else {
                $user_in_rank_count = 0;
            }
            $data['user_in_rank_count'] = $user_in_rank_count;
            
            // National / Country Level Rank List
            $search_filter['user_country']  = $user_country;
            $data['country_rank_list'] = $this->report_model->get_new_student_ranks($search_filter);
            unset($search_filter['user_id']);
            $total_na_rank_list = $this->report_model->get_new_student_ranks($search_filter);
            $data['na_rank_count'] = count($total_na_rank_list);
            $na_rank_list = $total_na_rank_list;
            $count = 0;
            if($data['na_rank_count'] > 0){
                foreach ($na_rank_list as $res) {
                    $count++;
                    if($res->user_id == $user_id){
                        $user_na_rank_count = $count;
                        break;
                    }
                }
            } else {
                $user_na_rank_count = 0;
            }
            $data['user_na_rank_count'] = $user_na_rank_count;

            // State Level Rank List
            $search_filter['user_state']    = $user_state;
            $search_filter['user_id']       = $user_id;
            $data['batch_rank_list'] = $this->report_model->get_new_student_ranks($search_filter);
            unset($search_filter['user_id']);
            $total_st_rank_list = $this->report_model->get_new_student_ranks($search_filter);
            $data['st_rank_count'] = count($total_st_rank_list);
            $st_rank_count = $total_st_rank_list;
            $count = 0;
            if($data['st_rank_count'] > 0){
                foreach ($st_rank_count as $res) {
                    $count++;
                    if($res->user_id == $user_id){
                        $user_st_rank_count = $count;
                        break;
                    }
                }
            } else {
                $user_st_rank_count = 0;
            }
            $data['user_st_rank_count'] = $user_st_rank_count;

            $this->load->view('parent/student_rank_details',$data);

        }
    }

    // Action : Parents List
    public function parents_list(){
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $data['parents_details'] = $this->user_model->get_users_list_view(2);
            $this->load->view('registrar/parents_list',$data);
        }
    }

    // Action : 
    public function course_of_test(){
        if ($this->input->server('REQUEST_METHOD') == 'POST'){
            $child_id = $this->session->userdata('CR_child_id');
            // $data['course_of_test_details'] = $this->user_model->get_course_of_test();
            $data['course_of_test_details'] = $this->batch_model->get_my_paid_courses($child_id);
            $this->load->view('parent/course_names',$data);
        }
    }

    // Action : loading subscribed course of his child   
    public function load_child_subscribed_course(){
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
          
            $child_id = $this->session->userdata('CR_child_id');
            $data['all_mychild_courses'] = $this->batch_model->get_my_paid_courses($child_id);

            $this->load->view('parent/student_graph',$data);
            // $data['get_my_montly_score'] = $this->user_model->get_my_montly_score($child_id,'percentage');
        }
    }
    
    // Action : load subjects under selected course
    public function get_subject_of_course(){
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $course_id  = $this->input->post('course_id');
            $child_id = $this->session->userdata('CR_child_id');
            $data['list_subjects'] = $this->user_model->get_subject_under_course($course_id,$child_id);
             
            $this->load->view('parent/load_subject_of_course',$data);
        }
    }

    // Action : Plot graph for each subject
    function plot_child_graph(){
        if ($this->input->server('REQUEST_METHOD')) {
            $child_id = $this->session->userdata('CR_child_id');
            $subject_name    = $this->input->post('subject_name');
            $course_id       = $this->input->post('course_id');
            $data['load_graph_data'] = $this->user_model->get_subject_details($child_id,$subject_name,$course_id);

            $this->load->view('student/plot_student_graph',$data);
        }
    }

    // ACTION : get his his child tests
    public function get_students_test(){
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
           
            $course_id = $this->input->post('course_id');
            $student_id = $this->session->userdata('CR_child_id'); 
            $this->session->set_userdata('CRS_course_id',$course_id);
            log_message('debug','Child Course ID: '.$course_id);
            log_message('debug','CR Child - ID: '.$student_id);
            
            // $data['course_of_test_details'] = $this->user_model->get_course_of_test();
            $data['get_students_test'] = $this->user_model->get_students_test($course_id,$student_id);
            $this->load->view('parent/test_names',$data);
        }
    }

}