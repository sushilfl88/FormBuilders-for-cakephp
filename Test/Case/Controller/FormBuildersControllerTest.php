<?php
App::uses('OgilvyControllerTestCase', 'Vendor/Ogilvy/test/');
App::uses('ClassRegistry', 'Utility');

/**
 * FormBuildersController Test Case
 *
 */
class FormBuildersControllerTestCase extends OgilvyControllerTestCase {

	protected $return_flag = '';
	
	
	public function __construct()
	{
		
		$this->db_model = ClassRegistry::init('FormCategory');
		
	}
	
	public function test_index() {
		//checking forms and clients and delete operation
		$result = $this->testAction('form_builders/form_builders/index/');
    }


}
