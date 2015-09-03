<?php
/**
 * Created on Aug 24, 2012
 */
 
App::uses('AppController', 'Controller');

class FormAclComponent extends AppComponent {
	   
	public $components = array('Session');
	
	function formAcl($formId,$requestId=0){
		$deliverableViewable = false;
		$loggedUserData = $this->Session->read('loggedUserData');
		$user_id = $loggedUserData['User']['id'];
		$hid = $loggedUserData['User']['hierarchy_value_id'];
		$forms = ClassRegistry::init("FormBuilders.Form"); 		
		$result = $forms->getFormData("first",array('is_public'),array('id' => $formId));
		if($result['Form']['is_public']){
			return true;
		}else{
			$formassignments = ClassRegistry::init("Campaigns.FormAssignment");
			$resultAssign = $formassignments->getFormAssignmentsData("first",array("id"),array("form_request_id" =>$requestId ,'user_id' => $user_id));
			if(isset($resultAssign['FormAssignment']['id']) && $resultAssign['FormAssignment']['id'] != NULL){
				return true;
			}else{
				return false;
			}
		}
		
	}
}
?>
