<?php
App::uses('OgilvyControllerTestCase', 'Vendor/Ogilvy/test/');
App::uses('ClassRegistry', 'Utility');

/**
 * FormBuildersController Test Case
 *
 */
class FormsControllerTestCase extends OgilvyControllerTestCase {

	protected $return_flag = '';
	
	
	public function __construct()
	{
		
		$this->db_model = ClassRegistry::init('Form');
		
	}

	public function test_add() {
		//need to do a separate generic function to add/delete dummy data to a table 
		$this->db_model = ClassRegistry::init('Form');
		$dummydata=$this->_getParams($this->db_model);
		//$this->db_model->save($dummydata);
		$return_array=array('data'=>$dummydata,'lastId'=>$this->db_model->id);
		//need to do a separate generic function to add/delete dummy data to a table
		

		//need to do a separate generic function to add/delete dummy data to a table 
		$this->db_model_assiciation = ClassRegistry::init('FormAssociation');
		$dummydata_form_association=$this->_getParams($this->db_model_assiciation);
		//$this->db_model->save($dummydata);
		$return_array_association=array('data'=>$dummydata_form_association,'lastId'=>$this->db_model_assiciation->id);
		//need to do a separate generic function to add/delete dummy data to a table

		//checking save
		$data_save = array('forms'=>$return_array['data'][$this->db_model->name],'clientId'=>$return_array_association['data'][$this->db_model_assiciation->name]['hid']);
		
		$result_save = $this->testAction('form_builders/Forms/add/'.$data_save['clientId'],array('data' => $data_save['forms'], 'method' => 'post'));
    }
 
	
	public function test_edit() {
	
		
		//for ajax checking
		$_ENV['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
		
		//get the last inser form details
		$this->return_flag='array';
		$data_save[$this->db_model->name]=$this->get_lastInsertId($this->db_model);
		
		//creating data for edit form
		
		$data_save[$this->db_model->name]['label']='test form'.rand(0,9);

		$result_save = $this->testAction('form_builders/Forms/edit/'.$data_save[$this->db_model->name]['id'],array('data' => $data_save, 'method' => 'post'));
		
		
		
		
	}

	
	public function test_searchForms() {
		
		//for ajax checking
		$_ENV['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
		
		//get the last inser form details
		$this->return_flag='array';
		$lastInsertedData[$this->db_model->name]=$this->get_lastInsertId($this->db_model);
		
		$data_for_search=array('searchexp'=>$lastInsertedData[$this->db_model->name]['label'],'selectedforms'=>$lastInsertedData[$this->db_model->name]['id'],'categoryId'=>$lastInsertedData[$this->db_model->name]['category_id']);
		
		$result_save = $this->testAction('form_builders/Forms/searchForms/',array('data' => $data_for_search, 'method' => 'post'));
	}
	
	
	public function test_getFormDetail() {
		
		//for ajax checking
		$_ENV['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
		
		//get the last inser form details
		$this->return_flag='array';
		$lastInsertedData[$this->db_model->name]=$this->get_lastInsertId($this->db_model);
		
		$result_save = $this->testAction('form_builders/Forms/getFormDetail/'.$lastInsertedData[$this->db_model->name]['id']);
	}
	
	
	public function test_getFormStatus() {
		
		//for ajax checking
		$_ENV['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
		
		//get the last inser form details
		$this->return_flag='array';
		$lastInsertedData[$this->db_model->name]=$this->get_lastInsertId($this->db_model);
		
		$data_for_get_status=array('categoryId'=>$lastInsertedData[$this->db_model->name]['category_id']);
		
		$result_save = $this->testAction('form_builders/Forms/getFormStatus/',array('data'=>$data_for_get_status));
	}
	
	
	
	
	
	private function get_lastInsertId($obj)
    {
    	if($this->return_flag=='array')
    	$fields=array('*');
    	else 
    	$fields=array('id');
    	
    	$result=$obj->find('first', array('order' => array('id DESC'),'fields'=>$fields));
 		
    	if($this->return_flag=='array')
    	return $result[$obj->name];
    	else 
    	return $result[$obj->name]['id'];
    }
	


}