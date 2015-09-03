<?php
App::uses('FormBuildersAppController', 'FormBuilders.Controller');
/**
 * FormBuilders Controller
 *
 */
class FormBuildersController extends FormBuildersAppController {
	var $name = 'FormBuilders';
	public $uses = array('FormBuilders.Form',
						 'FormBuilders.FormCategory',
						 'Workflow.StatusCode');
	/* index will set values for list of category and form
	 * 
	 */
	public function index(){
	//Set Page Title
		$this->pageTitle = 'Form Builder';
		$this->isAllowed("manage_formbuilder_with_full_access");//ACL
		//get and set list of form categories
				$listOfCategories = $this->FormCategory->getFormCategories('list',array('FormCategory.label'));
				$this->set('listOfCategories',$listOfCategories);
		//ends here
		//list of statuses
			$allStatuses = $this->StatusCode->getAllStatusForAdminForms();	
			$listOfStatus = $this->arrangeStatusCodes($allStatuses);
		//ends here
		//list the forms and set variable 
				$listOfForm = $this->Form->getFormData('list',array('Form.label'));
				$this->set('listOfForm',$listOfForm);
				$this->set('listOfStatus',$listOfStatus);
				
		//ends here

		
	}
	/* arrangeStatusCodes will arrange array
	 * @params array $allStatuses
	 */
	public function arrangeStatusCodes($allStatuses){
		$listOfStatuses = array();
		foreach($allStatuses as $keyStatus => $valStatus){
			$listOfStatuses[strtolower($valStatus['StatusCode']['label'])] = $valStatus['StatusCode']['label'];  
			
		}
		return $listOfStatuses;
	}
	
}
