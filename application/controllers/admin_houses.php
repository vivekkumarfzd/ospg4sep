<?php

class Admin_houses extends CI_Controller {

    /**
     * Responsable for auto load the model
     * @return void
     */
    public function __construct() {
        parent::__construct();
        $this->load->model('houses_model');
        $this->load->library('upload');
        $this->load->library('image_lib');
        $this->load->helper('custom_functions_helper');
        if (!$this->session->userdata('is_logged_in')) {
            redirect('admin/login');
        }
    }

// <editor-fold defaultstate="collapsed" desc="/*************House Details *************/">
    public function index() {

        //all the posts sent by the view
        $search_string = $this->input->post('search_string');
        //pagination settings
        $config['per_page'] = 25;
        $config['base_url'] = base_url() . 'admin/houses/';
        $config['use_page_numbers'] = TRUE;
        $config['num_links'] = 20;
        $config['full_tag_open'] = '<ul>';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a>';
        $config['cur_tag_close'] = '</a></li>';

        //limit end
        $page = $this->uri->segment(3);

        //math to get the initial record to be select in the database
        $limit_end = ($page * $config['per_page']) - $config['per_page'];
        if ($limit_end < 0) {
            $limit_end = 0;
        }

        //filtered && || paginated
        if ($search_string !== '' || $this->uri->segment(3) == true) {
            if ($search_string) {
                $filter_session_data['search_string_selected'] = $search_string;
            } else {
                $search_string = $this->session->userdata('search_string_selected');
            }
            $data['search_string_selected'] = $search_string;
            //save session data into the session
            $this->session->set_userdata(@$filter_session_data);

            //fetch manufacturers data into arrays
            $data['count_houses'] = $this->houses_model->count_houses($search_string);
            $config['total_rows'] = $data['count_houses'];

            //fetch sql data into arrays
            if ($search_string) {
                $data['houses'] = $this->houses_model->get_houses($search_string, $config['per_page'], $limit_end);
            } else {
                $data['houses'] = $this->houses_model->get_houses('', $config['per_page'], $limit_end);
            }
        } else {
           //clean filter data inside section
            $filter_session_data['search_string_selected'] = null;
            $this->session->set_userdata($filter_session_data);

            //pre selected options
            $data['search_string_selected'] = '';
            $data['count_houses'] = $this->houses_model->count_houses();
            $data['houses'] = $this->houses_model->get_houses('', $config['per_page'], $limit_end);
            $config['total_rows'] = $data['count_houses'];
        }
        //initializate the panination helper 
        $this->pagination->initialize($config);
       
        //load the view
        $data['main_content'] = 'admin/houses/list';
        $this->load->view('includes/template', $data);
    }

    public function addhouse() {
        //if save button was clicked, get the data sent via post
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            //form validation
            $this->form_validation->set_rules('house_no', 'house_no', 'required');
            $this->form_validation->set_rules('house_address', 'house_address', 'required');
            $this->form_validation->set_rules('house_rooms', 'house_rooms', 'required|numeric');
            $this->form_validation->set_rules('owner_name', 'owner_name', 'required');
            $this->form_validation->set_rules('owner_address', 'owner_address', 'required');
            

            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>', '</strong></div>');

            //if the form has passed through the validation
            if ($this->form_validation->run()) {
                
                $data_to_house = array(
                    'house_no' => $this->input->post('house_no'),
                    'house_address' => $this->input->post('house_address'),
                    'house_rooms' => $this->input->post('house_rooms'),
                    'house_acco_type' => $this->input->post('house_acco_type'),
                    'owner_name' => $this->input->post('owner_name'),
                    'owner_address' => $this->input->post('owner_address'),
                    'owner_email' => $this->input->post('owner_email'),
                    'owner_pan' => $this->input->post('owner_pan'),
                    'owner_mobile' => $this->input->post('owner_mobile'),
                    'owner_mobile2' => $this->input->post('owner_mobile2'),
                    'owner_landline' => $this->input->post('owner_landline'),
                    'owner_landline2' => $this->input->post('owner_landline2'),
                    'acquisition_date' => $this->input->post('acquisition_date'),
                    'agreement_from' => $this->input->post('agreement_from'),
                    'agreement_to' => $this->input->post('agreement_to'),
                    'rent_amount' => $this->input->post('rent_amount'),
                    'rent_date' => $this->input->post('rent_date'),
                   // 'document_scan' => $this->input->post('document_scan'),
                    'status' => 0
                );
                
                    
                        $file_status=1;
                foreach($_FILES as $field => $file)
                    {
                       if( $file['name']!=''){
                          $fileData =upload_document($field);
                          if($fileData['status']==1){
                        $data_to_house[$field]= $fileData['name'];
                          }else{
                              $file_status=0;
                              $this->session->set_flashdata('flash_message', 'file_size_excide');
                        break;
                        }
                       }
                    }
                   
                //if the insert has returned true then we show the flash message
                    if($file_status==1 ){
                if ( $this->houses_model->addhouse( $data_to_house) == TRUE) {
                    $this->session->set_flashdata('flash_message', 'updated');
                } else {
                    $this->session->set_flashdata('flash_message', 'not_updated');
                }
                redirect('admin/houses');
                }
            }
        }
        $data['main_content'] = 'admin/houses/addhouse';
        $this->load->view('includes/template', $data);
    }

    public function updatehouse() {
        //product id 
        $id = $this->uri->segment(4);
        //if save button was clicked, get the data sent via post
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            //form validation
           
            $this->form_validation->set_rules('house_no', 'house_no', 'required');
            $this->form_validation->set_rules('house_address', 'house_address', 'required');
            $this->form_validation->set_rules('house_rooms', 'house_rooms', 'required|numeric');
            $this->form_validation->set_rules('owner_name', 'owner_name', 'required');
            $this->form_validation->set_rules('owner_address', 'owner_address', 'required');
            //if the form has passed through the validation
            if ($this->form_validation->run()) {
                $data_to_house = array(
                    'house_no' => $this->input->post('house_no'),
                    'house_address' => $this->input->post('house_address'),
                    'house_rooms' => $this->input->post('house_rooms'),
                    'house_acco_type' => $this->input->post('house_acco_type'),
                    'owner_name' => $this->input->post('owner_name'),
                    'owner_address' => $this->input->post('owner_address'),
                    'owner_email' => $this->input->post('owner_email'),
                    'owner_pan' => $this->input->post('owner_pan'),
                    'owner_mobile' => $this->input->post('owner_mobile'),
                    'owner_mobile2' => $this->input->post('owner_mobile2'),
                    'owner_landline' => $this->input->post('owner_landline'),
                    'owner_landline2' => $this->input->post('owner_landline2'),
                    'acquisition_date' => $this->input->post('acquisition_date'),
                    'agreement_from' => $this->input->post('agreement_from'),
                    'agreement_to' => $this->input->post('agreement_to'),
                    'rent_amount' => $this->input->post('rent_amount'),
                    'rent_date' => $this->input->post('rent_date'),
//                    'document_scan' => $this->input->post('document_scan'),
                    'status' => 0
                );      
                $file_status=1;
                foreach($_FILES as $field => $file)
                    {
                       if( $file['name']!=''){
                          $fileData =upload_document($field);
                          if($fileData['status']==1){
                        $data_to_house[$field]= $fileData['name'];
                          }else{
                              $file_status=0;
                              $this->session->set_flashdata('flash_message', 'file_size_excide');
                        break;
                        }
                       }
                    }
                   
                //if the insert has returned true then we show the flash message
                    if($file_status==1 ){
                if ( $this->houses_model->update_house($id, $data_to_house) == TRUE) {
                    $this->session->set_flashdata('flash_message', 'updated');
                } else {
                    $this->session->set_flashdata('flash_message', 'not_updated');
                }
                redirect('admin/houses/1');
                }
                
                
            }//validation run
        }
        //if we are updating, and the data did not pass trough the validation
        //the code below wel reload the current data
        //product data 
        $data['house'] = $this->houses_model->get_house_by_id($id);
        //fetch manufactures data to populate the select field
        //$data['manufactures'] = $this->manufacturers_model->get_manufacturers();
        //load the view
        $data['main_content'] = 'admin/houses/edithouse';
        $this->load->view('includes/template', $data);
    }
    
        public function deletehouse() {
        $id = $this->uri->segment(4);
        $this->houses_model->delete_house($id);
        redirect('admin/houses/');
    }
	
        function showdoc($a='') {
        $file=$this->uri->segment(4);
        $filePath=site_url()."assets/img/house/documents/".trim($file);
        $doc=$filePath;//( trim($file)!='' && file_exists($filePath) ) ? $filePath :'';
        $data['doc_img']=$doc;
        $data['main_content'] = 'admin/houses/housedoc';
        $this->load->view('includes/template', $data);
    }
    // </editor-fold>
   
// <editor-fold defaultstate="collapsed" desc="/*************Room Details *************/">
    public function showroom() {
        $search_string = $this->input->post('search_string');
        //pagination settings
        $config['per_page'] = 25;
        $config['base_url'] = base_url() . 'admin/houses/showroom';
        $config['use_page_numbers'] = TRUE;
        $config['uri_segment']=4;
        $config['num_links'] = 20;
        $config['full_tag_open'] = '<ul>';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a>';
        $config['cur_tag_close'] = '</a></li>';

        //limit end
        $page = $this->uri->segment(4);

        //math to get the initial record to be select in the database
        $limit_end = ($page * $config['per_page']) - $config['per_page'];
        if ($limit_end < 0) {
            $limit_end = 0;
        }
        //if order type was changed
        if ($search_string !== '' || $this->uri->segment(4) == true) {
            
            if ($search_string) {
                $filter_session_data['search_string_selected'] = $search_string;
            } else {
                $search_string = $this->session->userdata('search_string_selected');
            }
            $data['search_string_selected'] = $search_string;
            //save session data into the session
            $this->session->set_userdata(@$filter_session_data);
            $data['count_rooms'] = $this->houses_model->count_rooms($search_string);
            $config['total_rows'] = $data['count_rooms'];
            //fetch sql data into arrays
            
            if ($search_string) {
                $data['rooms'] = $this->houses_model->get_rooms($search_string, $config['per_page'], $limit_end);
            } else {
                $data['rooms'] = $this->houses_model->get_rooms('', $config['per_page'], $limit_end);
            }

        } else {
            
            $filter_session_data['search_string_selected'] = null;
            $this->session->set_userdata($filter_session_data);

            //pre selected options
            $data['search_string_selected'] = '';
            $data['count_rooms'] = $this->houses_model->count_rooms();
            $data['rooms'] = $this->houses_model->get_rooms('', $config['per_page'], $limit_end);
        }
        
        $this->pagination->initialize($config);
        $data['main_content'] = 'admin/houses/listroom';
        $this->load->view('includes/template', $data);
    }

    public function addroom() {
        //if save button was clicked, get the data sent via post
        if ($this->input->server('REQUEST_METHOD') === 'POST') {

            //form validation
            $this->form_validation->set_rules('house_id', 'house_id', 'required');
            $this->form_validation->set_rules('room_no', 'room_no', 'required');
            $this->form_validation->set_rules('floor', 'floor', 'required');
            $this->form_validation->set_rules('share_type', 'share_typex', 'required');


            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>', '</strong></div>');

            //if the form has passed through the validation
            if ($this->form_validation->run()) {
                $data_to_houseroom = array(
                    'house_id' => $this->input->post('house_id'),
                    'room_no' => $this->input->post('room_no'),
                    'floor' => $this->input->post('floor'),
                    'share_type' => $this->input->post('share_type'),
                    'status' => 0
                );
                //if the insert has returned true then we show the flash message
                if ($this->houses_model->addhouseroom($data_to_houseroom)) {
                    $data['flash_message'] = TRUE;
                } else {
                    $data['flash_message'] = FALSE;
                }
                 redirect('admin/houses/showroom' );
            }
        }
        $data['houses'] = $this->houses_model->get_house_number();
        $data['main_content'] = 'admin/houses/addroom';
        $this->load->view('includes/template', $data);
    }

    public function updateroom() {

        //product id 
        $id = $this->uri->segment(4);

        //if save button was clicked, get the data sent via post
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            //form validation
            //form validation
            $this->form_validation->set_rules('house_id', 'house_id', 'required');
            $this->form_validation->set_rules('room_no', 'room_no', 'required');
            $this->form_validation->set_rules('floor', 'floor', 'required');
            $this->form_validation->set_rules('share_type', 'share_typex', 'required');

            //if the form has passed through the validation
            if ($this->form_validation->run()) {

                $data_to_houseroom = array(
                    'house_id' => $this->input->post('house_id'),
                    'room_no' => $this->input->post('room_no'),
                    'floor' => $this->input->post('floor'),
                    'share_type' => $this->input->post('share_type'),
                    'status' => 0
                );      //if the insert has returned true then we show the flash message

                if ($this->houses_model->update_room($id, $data_to_houseroom) == TRUE) {
                    $this->session->set_flashdata('flash_message', 'updated');
                } else {
                    $this->session->set_flashdata('flash_message', 'not_updated');
                }

                redirect('admin/houses/showroom' );
            }//validation run
        }



        //if we are updating, and the data did not pass trough the validation
        //the code below wel reload the curroom data
        //product data 
        $data['house_room'] = $this->houses_model->get_house_room_by_id($id);

        //fetch manufactures data to populate the select field
        //$data['manufactures'] = $this->manufacturers_model->get_manufacturers();
        //load the view
        $data['main_content'] = 'admin/houses/editroom';
        $this->load->view('includes/template', $data);
    }
 public function deleteroom() {
        $id = $this->uri->segment(4);
        $this->houses_model->delete_room($id);
        redirect('admin/houses/showroom/');
    }
    // </editor-fold>

// <editor-fold defaultstate="collapsed" desc="/*************Rent Details *************/">
    public function showrent() {
  
        echo$search_string = $this->input->post('search_string');
        //pagination settings
        $config['per_page'] = 25;
        $config['base_url'] = base_url() . 'admin/houses/showrent';
        $config['use_page_numbers'] = TRUE;
        $config['uri_segment']=4;
        $config['num_links'] = 20;
        $config['full_tag_open'] = '<ul>';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a>';
        $config['cur_tag_close'] = '</a></li>';

        //limit end
        $page = $this->uri->segment(4);

        //math to get the initial record to be select in the database
        $limit_end = ($page * $config['per_page']) - $config['per_page'];
        if ($limit_end < 0) {
            $limit_end = 0;
        }

        //if order type was changed
        //filtered && || paginated
        if ($search_string !== '' || $this->uri->segment(4) == true) {
            if ($search_string) {
                $filter_session_data['search_string_selected'] = $search_string;
            } else {
                $search_string = $this->session->userdata('search_string_selected');
            }
            $data['search_string_selected'] = $search_string;
            //save session data into the session
            $this->session->set_userdata(@$filter_session_data);
            //fetch manufacturers data into arrays
            $data['count_rents'] = $this->houses_model->count_rents($search_string);
            $config['total_rows'] = $data['count_rents'];
            //fetch sql data into arrays
            if ($search_string) {
                $data['rents'] = $this->houses_model->get_rents($search_string, $config['per_page'], $limit_end);
            } else {
                $data['rents'] = $this->houses_model->get_rents('', $config['per_page'], $limit_end);
            }
        } else {
            //clean filter data inside section
            $filter_session_data['search_string_selected'] = null;
            $this->session->set_userdata($filter_session_data);
            //pre selected options
            $data['search_string_selected'] = '';
            $data['count_rents'] = $this->houses_model->count_rents();
            $data['rents'] = $this->houses_model->get_rents('', $config['per_page'], $limit_end);
            $config['total_rows'] = $data['count_rents'];
        }
   
        //initializate the panination helper 
        $this->pagination->initialize($config);
        $data['main_content'] = 'admin/houses/listrent';
        $this->load->view('includes/template', $data);
    }

    public function addrent() {
        //if save button was clicked, get the data sent via post
        if ($this->input->server('REQUEST_METHOD') === 'POST') {

            //form validation
            $this->form_validation->set_rules('amount', 'amount', 'required|numeric');
             $this->form_validation->set_rules('rent_date', 'rent_date', 'required');
            if($this->input->post('pay_mode')=='cheque'){
                $this->form_validation->set_rules('cheque_no', 'cheque_no', 'required');
                $this->form_validation->set_rules('bank_name', 'bank_name', 'required');
                $this->form_validation->set_rules('account_holder', 'account_holder', 'required');
            }


            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>', '</strong></div>');

            //if the form has passed through the validation
            if ($this->form_validation->run()) {
               
                    $account_holder=($this->input->post('pay_mode')=='cheque') ? $this->input->post('account_holder') : '';
                    $cheque_no=($this->input->post('pay_mode')=='cheque') ? $this->input->post('cheque_no') : '';
                    $bank_name=($this->input->post('pay_mode')=='cheque') ? $this->input->post('bank_name') : '';
                    
                    $data_to_houserent = array(
                    'house_id' => $this->input->post('house_id'),
                    'pay_mode' => $this->input->post('pay_mode'),
                    'amount' => $this->input->post('amount'),
                    'rent_date' => $this->input->post('rent_date'),
                    'cheque_no' => $cheque_no,
                    'bank_name' => $bank_name,
                    'account_holder' => $account_holder,
                    'added_by' =>2,
                    'added_date'=>@date('Y-m-d')
                    
                );
                //if the insert has returned true then we show the flash message
                if ($this->houses_model->addhouserent($data_to_houserent)) {
                    $data['flash_message'] = TRUE;
                } else {
                    $data['flash_message'] = FALSE;
                }
                redirect('admin/houses/showrent' );
            }
        }
        //$data['houses'] = $this->houses_model->get_house_number();
        $data['houses'] = get_house_number();

        $data['main_content'] = 'admin/houses/addrent';
        $this->load->view('includes/template', $data);
    }

    public function updaterent() {
        //product id 
        $id = $this->uri->segment(4);
        //if save button was clicked, get the data sent via post
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            //form validation
            $this->form_validation->set_rules('amount', 'amount', 'required|numeric');
             $this->form_validation->set_rules('rent_date', 'rent_date', 'required');
            if($this->input->post('pay_mode')=='cheque'){
                $this->form_validation->set_rules('cheque_no', 'cheque_no', 'required');
                $this->form_validation->set_rules('bank_name', 'bank_name', 'required');
                $this->form_validation->set_rules('account_holder', 'account_holder', 'required');
            }
            //if the form has passed through the validation
            if ($this->form_validation->run()) {
                $account_holder=($this->input->post('pay_mode')=='cheque') ? $this->input->post('account_holder') : '';
                    $cheque_no=($this->input->post('pay_mode')=='cheque') ? $this->input->post('cheque_no') : '';
                    $bank_name=($this->input->post('pay_mode')=='cheque') ? $this->input->post('bank_name') : '';
                    
                    $data_to_houserent = array(
                    'house_id' => $this->input->post('house_id'),
                    'pay_mode' => $this->input->post('pay_mode'),
                    'amount' => $this->input->post('amount'),
                    'rent_date' => $this->input->post('rent_date'),
                    'cheque_no' => $cheque_no,
                    'bank_name' => $bank_name,
                    'account_holder' => $account_holder,
                    'added_by' =>2,
                    'added_date'=>@date('Y-m-d')
                    
                );            //if the insert has returned true then we show the flash message
                if ($this->houses_model->update_rent($id, $data_to_houserent) == TRUE) {
                    $this->session->set_flashdata('flash_message', 'updated');
                } else {
                    $this->session->set_flashdata('flash_message', 'not_updated');
                }
                redirect('admin/houses/showrent' );
            }//validation run
        }
        //if we are updating, and the data did not pass trough the validation
        //the code below wel reload the current data
        //product data 
        $data['house_rent'] = $this->houses_model->get_house_rent_by_id($id);
        //fetch manufactures data to populate the select field
        //$data['manufactures'] = $this->manufacturers_model->get_manufacturers();
        //load the view
        $data['main_content'] = 'admin/houses/editrent';
        $this->load->view('includes/template', $data);
    }
      public function deleterent() {
        $id = $this->uri->segment(4);
        $this->houses_model->delete_house_rent($id);
        redirect('admin/houses/showrent/');
    }

    // </editor-fold>
}