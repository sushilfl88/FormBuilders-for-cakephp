<?php
App::uses('FormBuildersAppController', 'FormBuilders.Controller');
App::uses('UUID', 'Lib/Utility');
require_once APP. '/Plugin/FormBuilders/Context/FormBuildersContext.php';
/**
 * FormProjectItemJoinsJoins Controller
 *
 */
class FormProjectItemJoinsController extends FormBuildersAppController {
	var $name = 'FormProjectItemJoins';
	
	private $service;
		public function setUp() {
			$this->service = FormBuildersContext::getInstance()->get(FormBuildersContext::FORM_PROJECT_ITEM_JOIN_MODEL);

		}
	public function beforeFilter(){
			parent::beforeFilter();
			$this->setUp();
		}
	
	
	
	
}
?>