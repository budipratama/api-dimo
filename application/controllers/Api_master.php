<?php
require(APPPATH.'/libraries/REST_Controller.php');
 
class Api_master extends REST_Controller
{
    function user_get()
    {
        if(!$this->get('id'))
        {
            $this->response(NULL, 400);
        }
 
        $user = $this->user_model->get( $this->get('id') );
         
        if($user)
        {
            $this->response($user, 200); // 200 being the HTTP response code
        }
 
        else
        {
            $this->response(NULL, 404);
        }
    }
     
    function user_post()
    {
        $this->load->model('user_model');
        $result = $this->user_model->view($this->post('id'));
         
        if($result === FALSE)
        {
            $this->response(array('status' => 'failed'));
        }
         
        else
        {
            $this->response(array('status' => 'success','result'=>$result));
        }
         
    }
     
    function users_get()
    {
        $this->load->model('user_model');
        $users = $this->user_model->get_all();
        if($users)
        {
            $this->response(array("status"=>"success",$users, 200));
        }
 
        else
        {
            $this->response(NULL, 404);
        }
    }
}
?>