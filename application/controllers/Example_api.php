<?php
require(APPPATH.'libraries/REST_Controller.php');

class Example_api extends REST_Controller{

 	public function user_get()
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
        /*
        $data = array('returned: '. $this->get('id'));
        $this->response($data);*/
    }
 
    public function user_post()
    {      
        $data = array('returned: '. $this->post('id'));
        $this->response($this->db->get('f_employee')->result()); 
        /*$result = $this->user_model->update( $this->post('id'), array(
            'name' => $this->post('name'),
            'email' => $this->post('email')
        ));
         
        if($result === FALSE)
        {
            $this->response(array('status' => 'failed'));
        }
         
        else
        {
            $this->response(array('status' => 'success'));
        }*/
    }
 
    public function user_put()
    {       
        $data = array('returned: '. $this->put('id'));
        $this->response($data);
    }
 
    public function user_delete()
    {
        $data = array('returned: '. $this->delete('id'));
        $this->response($data);
    }

}