<?php
/* Users Test cases generated on: 2012-04-07 17:16:49 : 1333799209*/
App::uses('OgilvyControllerTestCase', 'Vendor/Ogilvy/test/');
App::uses('ClassRegistry', 'Utility');

/**
 * FormAssociationsController Test Case
 *
 */
class FormAssociationsControllerTestCase extends OgilvyControllerTestCase {

	protected $return_flag = '';
	
	
	public function __construct()
	{
		
		$this->db_model = ClassRegistry::init('Form');
		
	}
	
	public function test_associateFormClient () {
		
 		//need to do a separate generic function to add/delete dummy data to a table 
		$this->db_model = ClassRegistry::init('Form');
		$dummydata=$this->_getParams($this->db_model);
		$this->db_model->save($dummydata);
		$return_array=array('data'=>$dummydata,'lastId'=>$this->db_model->id);
		//need to do a separate generic function to add/delete dummy data to a table

		//for ajax checking
		$_ENV['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
		
		//checking category_id
		$data = array('categoryId' => $return_array['data'][$this->db_model->name]['category_id']);
		$result = $this->testAction('form_builders/form_associations/associateFormClient/',array('data' => $data, 'method' => 'post'));
		
		
		//need to do a separate generic function to add/delete dummy data to a table 
		$this->db_model = ClassRegistry::init('FormAssociation');
		$dummydata_form_association=$this->_getParams($this->db_model);
		$this->db_model->save($dummydata_form_association);
		$return_array_association=array('data'=>$dummydata_form_association,'lastId'=>$this->db_model->id);
		//need to do a separate generic function to add/delete dummy data to a table
		
		//checking forms and clients and save operation
		$data_save = array('forms' => array('form_id'=>$return_array_association['data'][$this->db_model->name]['form_id']),'clients'=>array('hid'=>$return_array_association['data'][$this->db_model->name]['hid']),'associate'=>'save');
		$result_save = $this->testAction('form_builders/form_associations/associateFormClient/',array('data' => $data_save, 'method' => 'post'));
		
		//checking forms and clients and delete operation
		$data_delete = array('forms' => array('form_id'=>$return_array_association['data'][$this->db_model->name]['form_id']),'clients'=>array('hid'=>$return_array_association['data'][$this->db_model->name]['hid']),'associate'=>'delete');
		$result_delete = $this->testAction('form_builders/form_associations/associateFormClient/',array('data' => $data_delete, 'method' => 'post'));
    }
    
    public function test_getFormAssociation () {
    	
    	//for special case
    	$client_id='509';
    	
    	//with argument
    	$result = $this->testAction('form_builders/form_associations/getFormAssociation/'.$client_id);
    	
    	//without argument
    	$result = $this->testAction('form_builders/form_associations/getFormAssociation/');
    }

    //this function is to delete the test data
   	/*public function test_delete()
    {
    	$lastInsertId=$this->get_lastInsertId();
    	$result = $this->testAction('access_controls/actions/delete/');
    	
    	$result = $this->testAction('access_controls/actions/delete/'.$lastInsertId);
    	
    	
    	
    }*/
    
	private function get_lastInsertId()
    {
    	if($this->return_flag=='array')
    	$fields=array('*');
    	else 
    	$fields=array('id');
    	
    	$result=$this->db_model->find('first', array('order' => array('id DESC'),'fields'=>$fields));
 		
    	if($this->return_flag=='array')
    	return $result[$this->db_model->name];
    	else 
    	return $result[$this->db_model->name]['id'];
    }

}
