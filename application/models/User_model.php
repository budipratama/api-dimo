<?php
class User_model extends CI_Model{
	public $makan= "ayam";
	public function __construct()
    {
        // Call the CI_Model constructor
    	parent::__construct();
    	$this->load->database();
    }
	public function get_all()
	{
		$query = $this->db->get('f_employee'); 
		return $query->result();
	}

	public function view($id)
	{
		$query = $this->db->get_where('f_employee',array("EM_ID"=>$id)); 
		return $query->row();
	}
}