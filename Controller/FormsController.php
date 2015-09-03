<?php
App::uses('FormBuildersAppController', 'FormBuilders.Controller');
require_once APP. '/Plugin/FormBuilders/Context/FormBuildersContext.php';
/**
 * FormBuilders Controller
 *
 */
class FormsController extends FormBuildersAppController {
	var $name = 'Forms';
	public $uses = array('FormBuilders.FormCategory',
						 'FormBuilders.Form',
						 'FormBuilders.FormAssociation',
						 'Workflow.StateEngine',
						  'Workflow.StatusCode');
		private $service;
		public function setUp() {
			$this->service = FormBuildersContext::getInstance()->get(FormBuildersContext::FORM_MODEL);
			
		}
	public function beforeFilter(){
			parent::beforeFilter();
			$this->setUp();
		}
	public function index(){ 
		$this->autoRender = false;
	//Set Page Title
		$this->pageTitle = 'Form Builder';
		$conditions=array();
		if($this->RequestHandler->isAjax()){
			$typeId = $this->request->data['category'];
			$status = $this->request->data['status'];
			$formString = $this->request->data['formString'];
		//list the forms and set variable 
				if(!empty($typeId)){
					$conditions[] = 'Form.category_id = '. $typeId;
				}
				if(!empty($formString)){
					$conditions[] = 'Form.label LIKE "%'. $formString.'%"';
				}
				if(!empty($status)){
					
					if(empty($typeId)){
						
						$arr = array("'brief_form_".$status."'" ,"'checklist_form_".$status."'", "'survey_form_".$status."'");
						
						$arr =implode(',',$arr);
						
						$conditions[] = 'Form.status IN ( '. $arr.')';
					}else{
						$categoryData = $this->FormCategory->findById($typeId);
						if(!empty($categoryData)){
						$categoryLabel = strtolower($categoryData['FormCategory']['label']).'_form_'.$status;
						$conditions[] = 'Form.status = "'.$categoryLabel.'"';
						}
					}
					
				}
				$listOfForm = $this->Form->getFormData('all',array('Form.id','Form.label'),$conditions);
				echo json_encode($listOfForm);
				
		//ends here
		}
		
	}
	/*
	 * function getFormDetail will get the form details
	 * @params integer $formId
	 * @return array 
	 * @version 5.2
	 */
	public function getFormDetail($formId = NULL, $fromWhere = 'form_builder'){
		$this->autoRender = false;
	//Set Page Title
		$this->pageTitle = 'Form Builder';
		$conditions=array();
		if($this->RequestHandler->isAjax()){
		//list the forms and set variable 
				if(!empty($formId)){
					$conditions=array('Form.id'=>$formId);
					$returnArray['form'] = $this->Form->getFormData('first',NULL,$conditions);
					//get and set list of form categories
					$returnArray['category'] = $this->FormCategory->getFormCategories('list',array('FormCategory.label'));
					//ends here
					//get work flow id
					if(!empty($returnArray['form']['Form']['category_id'])){
						$categoryData = $this->FormCategory->findById($returnArray['form']['Form']['category_id']);
						/*setting status type TODO fetch from database*/
						$statusType = $categoryData['FormCategory']['label']."_admin";
					} 
					//ends here
					$returnArray['status'] = $this->getStatusFromStateEngine($categoryData['FormCategory']['form_workflow_id'],$returnArray['form']['Form']['id'],$statusType);
					$curStatusData =  $this->StatusCode->getStatusLabelByCode($returnArray['form']['Form']['status']);
					$returnArray['currentStatus'] = array($returnArray['form']['Form']['status'] => $curStatusData);
					echo json_encode($returnArray);
				}
				
		//ends here
			
		}
		
		
	}
	
	/* getStatusFromStateEngine() will fetch statuses from stateengine
	 * @params integer $workflowId
	 * @params integer $formId
	 * return Array
	 */
	public function getStatusFromStateEngine($workflowId = NULL , $formId = '' ,$statusType =''){
		return $this->StateEngine->getAvialableStatusForFormUser($workflowId, $formId, $statusType);
	}
	/* getStatusFromStateEngine() will fetch statuses from stateengine
	 * return Array
	 */
	public function getFormStatus(){
		$this->autoRender = false;
		if($this->RequestHandler->isAjax()){
			$categoryData = $this->FormCategory->findById($this->request->data['categoryId']);
			
			/*$statusType = $categoryData['FormCategory']['label']."_admin";*/
			$return = array();
			if(!empty($categoryData['FormCategory']['workflow_id'])){
			$return['status'] = $this->getStatusFromStateEngine($categoryData['FormCategory']['form_workflow_id'],'');
			}
		}
		echo json_encode($return);
		
	}
	
	
	/**
	 * This function is used for adding new form
	 * @param integer $clientId( cliend id for whom this new form need to be created)
	 * @returns true if saved in json encoded format
	 * 
	 */
	public function add($clientId = NULL){
		try{
				$this->layout = 'ajax';
				$this->set('edit',false);
				$this->set('url','/form_builders/forms/add/'.$clientId);
				//get and set list of form categories
				$listOfCategories = $this->FormCategory->getFormCategories('list',array('FormCategory.label'));
				$this->set('listOfCategories',$listOfCategories);
				//ends here
				if($this->RequestHandler->isPost()){
					$this->autoRender = false;
					$temp = array('label' => array(
												'required' => array(
										            'rule' => 'notEmpty',
										        	'required'=>true,
										        	'message'=>'You Must Enter a Label'
										        	)
									         	),
								'status' => array(
												'required' => array(
										            'rule' => 'notEmpty',
										        	'required'=>true,
										        	'message'=>'You Must Enter a Status'
										        	)
									         	),
								'category_id' => array(
												'required' => array(
										            'rule' => 'notEmpty',
										        	'required'=>true,
										        	'message'=>'You Must Enter a Status'
										        	)
									         	),
								);
					
					if($this->request->data){
						$validationRules=$this->Form->validate;
						$validationRules = array_merge($validationRules, $temp);	
						$this->Form->validate = $validationRules;
						$val_result	= $this->ajax_form_validate(false);
						if(isset($val_result['success'])){
							try{
							$this->Form->saveFormData($this->request->data);
							$val_result['add'] = 'global';
							}catch(exception $e){
								debug($e);	
							}
							
							/*if form added from client profile then do following*/
							if($clientId != NULL ){ 
								$formAssociationData['form_id'] = $this->Form->getLastInsertID();
								$formAssociationData['hid'] = $clientId;
								try{
									$this->FormAssociation->save($formAssociationData);//saving data into formassociation table if added from client profile
								}catch(exception $e){
									debug($e);	
								}
								$val_result['add'] = 'clientProfile';
							}
							/*ends here*/
						}
						
						echo json_encode($val_result);
					
					}
				}	
		}
		catch(exception $e){
			$this->Session->setFlash($e->getMessage(), 'default', array('class'=>'alert alert-error'));
			$this->redirect($this->referer());
		}
	}

	/**	
	 * searchForms() will search the form
	 * @returns boolean false
	 * @version 5.2
	 * @ref REDDEV-4736	
	 */
	public function searchForms(){
		if($this->request->is('ajax')&& $this->request->data){
			$this->autoRender = false;
			$searchexpression = $this->request->data['searchexp'];
			$selectedforms = $this->request->data['selectedforms'];
			$categoryId = $this->request->data['categoryId'];
			/*searches form for queried search expressions*/
			if(isset($searchexpression)){
				$results['all'] = $this->Form->searchForms($searchexpression,$selectedforms, $categoryId);
			}
			/*searches form for already selected forms*/
			$results['selected'] = $this->Form->searchSelectedForm($selectedforms, $categoryId);
			echo json_encode($results);
			return false;
		}
	}
	
	/**
	 * 
	 * edit() This function is used for editing form
	 * @param integer $formId( form id which we are editing)
	 * @version 5.2
	 *  
	 */
	public function edit($formId = NULL){
		
				$this->layout = 'ajax';
				$this->set('edit',true);
				$this->set('url','/form_builders/forms/add/');
				$this->set('id',$formId);
				$conditions=array('Form.id'=>$formId);
				$formData = $this->Form->getFormData('first',NULL,$conditions);
				
				$this->set('label',$formData['Form']['label']);
				$this->set('dl',$formData['Form']['distribution_list']);
				$this->set('is_public',$formData['Form']['is_public']);
				//get and set list of form categories
				$listOfCategories = $this->FormCategory->getFormCategories('list',array('FormCategory.label'));
				$this->set('listOfCategories',$listOfCategories);
				
					if(!empty($formData['Form']['category_id'])){
						$categoryData = $this->FormCategory->findById($formData['Form']['category_id']);
						$statusType = $categoryData['FormCategory']['label']."_admin";
						$this->set('categoryData',$categoryData['FormCategory']['id']);
						/*setting status type TODO fetch from database*/
						$listOfStatus = $this->getStatusFromStateEngine($categoryData['FormCategory']['workflow_id'],$formData['Form']['id'],$statusType);
						$curStatusData =  $this->StatusCode->getStatusLabelByCode($formData['Form']['status']);
						
						$this->set('listOfStatus',array_merge(array($formData['Form']['status'] =>$curStatusData),$this->arrangeStatusCodes($listOfStatus)));
						$this->set('currentStatus',$formData['Form']['status']);
					} 
								
	}
	
	/* arrangeStatusCodes will arrange array
	 * @params array $allStatuses
	 */
	public function arrangeStatusCodes($allStatuses){
		
		$listOfStatuses = array();
		foreach($allStatuses as $keyStatus => $valStatus){
			$listOfStatuses[$valStatus['StateEngine']['status_code']] = $valStatus['StatusCode']['label'];  
			
		}
		return $listOfStatuses;
	}
	
}
