<?php
/*Created for /forms
 * Added  on 04 -07-2013
 * */
App::uses('FormBuildersAppController', 'FormBuilders.Controller');
require_once APP. '/Plugin/FormBuilders/Context/FormBuildersContext.php';
require_once APP. '/Plugin/Projects/Context/FieldValuesContext.php';
require_once APP. 'Plugin/Campaigns/Context/CampaignsContext.php';
require_once APP. '/Service/Access/IViewAccessService.php';
require_once APP. '/Service/Access/IViewElementRulesGenerator.php';
class FormRequestsController extends FormBuildersAppController {
	var $name = 'FormRequests';
private $service;
private $fieldValueService;
protected $formDefaultTeam;
		public function setUp() {
			$this->service = FormBuildersContext::getInstance()->get(FormBuildersContext::FORM_REQUEST_MODEL);
			$this->fieldValueService = FieldValuesContext::getInstance()->get(FieldValuesContext::FIELD_VALUE_MODEL);
			$this->formDefaultTeam= CampaignsContext::getInstance()->get(CampaignsContext::FORM_DEFAULT_TEAM_SERVICE);
		}
	public function beforeFilter(){
			parent::beforeFilter();
			$this->setUp();
			
		}
	public $uses=array( 'FormBuilders.FormRequest',
						'FormBuilders.Form',
						'FormBuilders.FormCategory',
						'FormBuilders.FormAssociation',
						'AccessControls.HierarchyValue',
						'AccessControls.Action',
						'AccessControls.HidUserRoleJoin',
						'AccessControls.LogicalRole',
						'AccessControls.LogicalRoleGroup',
						'AccessControls.User',
						'AccessControls.ConfigurationSetting',
						'Files.File',
						'Workflow.StateEngine',
						'Projects.FieldValue');

	public $helpers=array('App','FormBuilders.FormRequestsFile','Campaigns.CampaignActionShare');
	public $components	= array('Projects.ProjectExportToExcel','Files.Files','Comments.CommentService','FormBuilders.FormAcl');
	

	/**
	 * The below action is used for displaying form request created by the logged in users
	 * @params integer $typeId
	 * @version 5.2
	 * @ref REDDEV-4830
	 */

	public function index($typeId=NULL){
		$this->pageTitle = 'Forms';
		$UserDetails = $this->Session->read('loggedUserData');
		$userId = $UserDetails['User']['id'];
		$clientList = $this->HierarchyValue->getClientsByUser($userId);
		$clientListStr = implode(',',array_keys($clientList));
		$this->isAllowed("view_forms");//ACL for forms		
		$categoryNames = "'Survey','Checklist'";
		$formRequestData=$this->FormRequest->getFormRequest($categoryNames,NULL,$clientListStr);
		/*Show Form builder button according to ACL @refREDDEV-4901*/
		$loggedInUserArray = $this->Session->read('loggedUserData');
        $LoggedInUserId    = $loggedInUserArray['User']['id'];
        $this->Session->write('fileupload', '');
		$this->set('isPriviledge',$this->isAllowed("manage_formbuilder_with_full_access",FALSE));
		
		/*Ends Here*/
		$this->set('formRequestData',$formRequestData);

	}
	/*
	 * Function to create new checklist
	 * @params 
	 * @version 5.2
	 * @ref REDDEV-4830
	 * 
	 */

	function add_checklist(){
		$UserDetails = $this->Session->read('loggedUserData');
		$userId = $UserDetails['User']['id'];
		$hierarchyValueData=$this->HierarchyValue->getAssociatedClientsDetailsWithActiveFormAssociationsByUserId($userId);
		$this->set('hierarchyValueData',$hierarchyValueData);

	}
	/*
	 * Function to create new survey
	 * @params 
	 * @version 5.2
	 * @ref REDDEV-4830
	 */
	function add_survey(){
		$UserDetails = $this->Session->read('loggedUserData');
		$userId = $UserDetails['User']['id'];
		$hierarchyValueData=$this->HierarchyValue->getAssociatedClientsDetailsWithActiveFormAssociationsByUserId($userId);
		$this->set('hierarchyValueData',$hierarchyValueData);

	}
	/*
	 * This function is used for viewing particular form type
	 * @param integer $type (this is the column that will be used to search form type example client_hid etc)
	 * @param integer $typeValue (this is the value that will be used for searching )
	 * @version 5.2
	 * @ref REDDEV-4830
	 */
	function view($type = 'false', $typeValue = false){
		
		try{
			if($type && $typeValue){
				$this->autoRender=false;
				$returnArray = array();
				$categoryId = $this->FormCategory->getFormCategory($type);
				$data=$this->Form->getFormAssociationData($categoryId, $typeValue);
				if(count($data)>0){
					$returnArray['success'] = true;
					$returnArray['Forms'] = $data;
					
				}
				echo json_encode($returnArray);
			}
		}
		catch(exception $e){
				$this->Session->setFlash($e->getMessage(), 'default', array('class'=>'alert alert-error'));
				$this->redirect($this->referer());
		}
	}
	/**
	 * The below action is used for export form request created by the logged in users 
     * @param string $fileName (export file name)
     * @param array $headerArray (excel header column)
     * @param array $visibleColumns (column that will be displayed from the header array)
     * @param array $campaignRequestData (data that would be populated in the excel)
     * @version 5.2
	 * @ref REDDEV-4830
 
	 */
	 
	public function export($formRequestId = false){
		try{
			$headerArray=array(	
    						0 => 'ID',
    						1 => 'Client',
    						2 => 'Name',
    						3 => 'Form Type',
    						4 => 'Category',
    						5 => 'Status',
    						6 => 'Submitted By',
    						7 => 'Submitted Date'
    					   );
    		
    		/**
    		 * The below variable is hardoded as we do not have column option at this time.
    		 * Once we have column options introduced the below array will be dynamice and will be generated from colum option itself 
    		 */
    		$visibleColumns	=array(0,1,2,3,4,5,6,7);
    				   
			$this->autoRender=false;
			$UserDetails = $this->Session->read('loggedUserData');
			$userId = $UserDetails['User']['id'];
			$formRequestData=$this->FormRequest->getFormRequest("'Survey','Checklist'",$formRequestId);
						
			$fileName='MyFormRequest';
			$this->ProjectExportToExcel->processMyFormRequestExcel($fileName,$headerArray,$visibleColumns,$formRequestData);
			
		}
		catch(exception $e){
		  		$this->Session->setFlash($e->getMessage(), 'default', array('class'=>'alert alert-error'));
				$this->redirect($this->referer());
		}
	}
/*
	 * This function is used for deleting files from a form request
	 * @param integer $fileId ( Based on this the File table would delete its entry)
	 * @param string $path ( Based on this the File would be physicall removed, the path also contains the file name)
	 */
	function remove(){
		try{
			if($this->RequestHandler->isAjax()){	
				if($this->RequestHandler->isPost()){
					$this->autoRender=false;
					$this->service->removeAssociatedFilesData($this->request->data);
				}
			}
		 }
		 catch (exception $e){
		 		$this->Session->setFlash($e->getMessage(), 'default', array('class'=>'alert alert-error'));
				$this->redirect($this->referer());
		 }			
	}
	
	/*
	 * This function is used for updating campaign request
	 * @param string $type (Based on this what kind of update be made on campaign request like $type=statusUpdate means only status would be updated)
	 * @param integere $campaignRequestId (Based on this which campaign request to update is identified)
	 * @param string $statusCode (This value will be the status of the said campaign after update)
	 */
	function edit($type='statusUpdate',$formRequestId = false,$nextStatusCode = false,$currentStatusCode = false,$workFlowId = false,$statusType = false){
		try{
			$userDetails = $this->Session->read('loggedUserData');
			$loggedInUserEmail = $userDetails['User']['email'];
			if($type=='statusUpdate'){
				$this->autoRender=false;
				$this->layout='ajax';
				if($formRequestId && $nextStatusCode && $currentStatusCode){
					$statusCheck=$this->StateEngine->canTransitionToForms($currentStatusCode, $nextStatusCode,$workFlowId);
					$nextStatusCode="'$nextStatusCode'";
					$returnArray=array();
						if($statusCheck){
							$currentStatusOfFormRequest = $this->FormRequest->findById($formRequestId);
						if($this->FormRequest->updateAll(array('status'=>$nextStatusCode),array('id'=>$formRequestId))){
							$returnArray['success']='Status Updated Succesfully.';
							$formStatus=$this->StateEngine->getAvialableStatusForForm($workFlowId, $formRequestId, $statusType);
							$returnArray['campaignStatus']=$formStatus;
							$formRequestSavedData = $this->FormRequest->findById($formRequestId);
							$returnArray['currentStatus'] = $formRequestSavedData['FormRequest']['status'];
							$formData         = $this->FormAssociation->getFormTypeAlongWithClientDetails($formRequestSavedData['FormRequest']['form_associations_id']);
							/*find all approvers*/
							if(isset($formData) && $formData['Formcategory']['categoryname'] == 'Brief'){
								$emailList =    	 $formData['Form']['distribution_list'];
			                    $rolesArr = array("Brief Approver");
			                    $arrayForApprover = $this->formDefaultTeam->getMembersOfRequestedIdByRole('Brief' ,$formRequestId, $rolesArr);
				                    if(isset($arrayForApprover) && !empty($arrayForApprover)){
				                    	$approverArr = implode(",", $arrayForApprover);
				                    	$emailList = $approverArr.','.$emailList;
				                    }
			                    }
							
							//if(isset($formData) && $formData['Formcategory']['categoryname'] == 'Brief'){
									$result = $this->FormRequest->sendEmailForActiveStatusBriefs($formRequestSavedData,$currentStatusOfFormRequest['FormRequest']['status'],$emailList,$loggedInUserEmail);//TODO mail
								//}
							}
						else{
							$returnArray['error']='Error Updating Status';
						}
					}	
					else{
						$returnArray['error']='You Don\'t Have Access To Update To This Status';
					}
					echo json_encode($returnArray);	
				}
			}
		}
		catch(exception $e){
				$this->Session->setFlash($e->getMessage(), 'default', array('class'=>'alert alert-error'));
				$this->redirect($this->referer());
		}
	}
	
	/**
	 * add_rejection_note()  is used to display popup to add rejection note 
     * 
     * @param string $status 
     * @param integer $formReqId for which to add rejection note
     * @param integer $formRequestAssociation
     * @param string $formLabel 
     * @version 5.2
	 * @ref REDDEV-4781
	 */
	
	function add_rejection_note($status = 'cr_rejected', $formReqId = NULL, $formRequestAssociation = NULL,$formLabel=NULL){
		
		if($this->RequestHandler->isAjax()){ 
			$UserDetails = $this->Session->read('loggedUserData');
			$userId = $UserDetails['User']['id'];
			$this->set('status',urldecode($status));
			$this->set('formRequestId',$formReqId);
			$this->set('formRequestAssociation',$formRequestAssociation);
			$this->set('formLabel',urldecode($formLabel));
		}
	
	}
	/**
	 * add_rejection_note is used to save rejection note  
     * @return boolean
     * @version 5.2
     * @ref REDDEV-4781
 
	 */
	
	function save_rejection_note(){
		$this->autoRender = false;
		if($this->RequestHandler->isAjax()){	
				if($this->RequestHandler->isPost()){
					if(!empty($this->data)){
						$data = $this->data;
						$loggedInUserArray = $this->Session->read('loggedUserData');
						$rejectionNoteSavedData = $this->service->saveRejectionNoteService($data);
						$this->CommentService->addCommentOnStatusChangeOfForm($loggedInUserArray['User']['id'], $data['fr_id'], $data['formLabel'], $data['status']);
						if($rejectionNoteSavedData){
						//get form category by exploding the status in order to chk if it is brief and append the approver email string
						$formCategoryLabel =	explode('_',$data['currentStatus']);
						/*find all approvers*/
						if($formCategoryLabel[0] == 'brief'){  
	                    $rolesArr = array("Brief Approver");
	                    $arrayForApprover = $this->formDefaultTeam->getMembersOfRequestedIdByRole('Brief' ,$data['fr_id'], $rolesArr);
		                    if(isset($arrayForApprover) && !empty($arrayForApprover)){
		                    	$approverArr = implode(",", $arrayForApprover);
		                    	$emailList = $approverArr.','.$data['distributionList'];
		                    }
	                    }
						$mailSent = $this->service->sendFormEmails($rejectionNoteSavedData,$data['currentStatus'],$emailList,$loggedInUserArray['User']['email']);							
						$return['msg'] = true;
						}
						$return['redirect'] = $this->referer();
						echo json_encode($return);
						
					}
				}
		}
		
	}
	/**
	 * viewRequestedForm()  add new form request
	 * @param integer $formId
	 * @return Array
	 * @ref REDDEV-5127
	 * @version 5.3
	 */
	public function viewRequestedForm($formId){
		$this->pageTitle = 'Forms';
		/**
		 * if post request for now it contains the data which is required to save into form_project_item_join
		 * in order to keep url clean the data is sent trough post
		 */
		$postData = array();
		if (!empty($this->data)) {
			//save into form request
            if($this->RequestHandler->isAjax()){
            	$this->set('ajaxPop',true);		
            	if ($this->RequestHandler->isPost()) { 
            		$postData = $this->data; 
            		
            	}
            }
		}    
		$loggedInUserArray = $this->Session->read('loggedUserData');
        $formData = $this->service->viewRequestedForm($loggedInUserArray, $formId);
		$this->set('formId',(isset($formId))? $formId:NULL);
		$this->set('formType',(isset($formData['form']['FormCategory']['label']))? $formData['form']['FormCategory']['label']:array());
		$this->set('workFlowId',(isset($formData['form']['FormCategory']['workflow_id']))? $formData['form']['FormCategory']['workflow_id']:array());	
		$this->set('formLabel',(isset($formData['form']['Form']['label']))? $formData['form']['Form']['label']:array());
		$this->set('fieldsData',(isset($formData['fields']['fields']))? $formData['fields']['fields']:array());
		$this->set('descriptionForm',(isset($formData['form']['Form']['description']))? $formData['form']['Form']['description']:array());
		$this->set('fileArray',(isset($formData['fields']['file']))? $formData['fields']['file']:array());
		$this->set('fileChildArray',(isset($formData['fields']['fileChild']))? $formData['fields']['fileChild']:array());
		$this->set('childArray',(isset($formData['fields']['child']))? $formData['fields']['child']:array());
		$this->set('formRequestStatus', array());
		$this->set('postData',$postData);
		/*TODO :uncomment following while implementing the form module througly
		//$this->set('viewAddButton', $formData['viewAddButton']);
		//$this->set('isAddFeature',$formData['isAddFeature']);
        //$this->set('isClient', $formData['isClient']);*/
        $this->set('clientLabel','');
        $this->set('client_id','');
	}
	/**
	 * editRequestedForm()  add new form request
	 * @param integer $frId
	 * @return Array
	 * @ref REDDEV-5127
	 * @version 5.3
	 */
	public function editRequestedForm($frId){
		$this->pageTitle = 'Forms';
		 if($this->RequestHandler->isAjax()){
            	$this->set('ajaxPop',true);	
		 }
		$postData = array();
		$loggedInUserArray = $this->Session->read('loggedUserData');
        $formData = $this->service->editRequestedForm($loggedInUserArray, $frId);
		$this->set('formId',(isset($formData['formRequest']['FormRequest']['form_id']))?$formData['formRequest']['FormRequest']['form_id']:NULL);
		if($frId != NULL && isset($formData['formRequest']['FormRequest']['form_id'])){
		$return = $this->FormAcl->formAcl($formData['formRequest']['FormRequest']['form_id'],$frId);
        if(!$return)
        $this->redirect('/errors/4');
        }
		//debug($formData['formRequest']['FormRequest']['form_id']);
		$this->set('formType',(isset($formData['form']['FormCategory']['label']))? $formData['form']['FormCategory']['label']:array());
		$this->set('workFlowId',(isset($formData['form']['FormCategory']['workflow_id']))? $formData['form']['FormCategory']['workflow_id']:array());	
		$this->set('formLabel',(isset($formData['form']['Form']['label']))? $formData['form']['Form']['label']:array());
		$this->set('fieldsData',(isset($formData['fields']['fields']))? $formData['fields']['fields']:array());
		$this->set('fileArray',(isset($formData['fields']['file']))? $formData['fields']['file']:array());
		$this->set('fileChildArray',(isset($formData['fields']['fileChild']))? $formData['fields']['fileChild']:array());
		$this->set('childArray',(isset($formData['fields']['child']))? $formData['fields']['child']:array());
		$this->set('formRequestData',(isset($formData['formRequest']))?$formData['formRequest']:NULL);
		$this->set('formRequestStatus', array());
		$this->set('formStatus',(isset($formData['formStatus']))?$formData['formStatus']:NULL);
		
		/*TODO :uncomment following while implementing the form module througly
		$this->set('viewAddButton', $formData['viewAddButton']);
		$this->set('isAddFeature',$formData['isAddFeature']);
        $this->set('isClient', $formData['isClient']);
        */
        $this->set('formRequestId',$frId);
        $this->set('clientLabel','');
        $this->set('client_id','');
        $this->render('view_requested_form');
	}
	/**
	 * add() will save the data
	 * @param integer $frId 
	 * @ref REDDEV-5127
	 * @version 5.3
	 */
	public function add($frId = NULL){
		if (!empty($this->data)) {
			//save into form request
            	
            	if ($this->RequestHandler->isPost()) { 
            		$loggedInUserArray  = $this->Session->read('loggedUserData');
            		//assign file data into a variable
                	$filesData = $this->Session->read('fileupload');
                	//unset session
                	$this->Session->write('fileupload', '');
            		$result = $this->service->save($this->data, $loggedInUserArray, $filesData, $frId);
            		if($result['isSaved']){
            			//below service will add task on reject of checklist
            			$this->CommentService->addCommentOnStatusChangeOfForm($loggedInUserArray['User']['id'], $frId, $result['formLabel'], $result['formStatus']);
            			//$this->redirect('/form_requests/edit/'.$result['formRequestId']); TODO:uncomment if we needed to redirect in edit mode
            			$this->Session->setFlash('Form data succsessfully saved.', 'default', array('class'=>'alert alert-success'));
						$this->redirect($this->referer());
						
					
            		}else{
            			//error_log(print_r($errors,true));
						throw new Exception("Errors happened");
            		}
            	} 
			
		}
	}
	/**
	 * 
	 * editStatus will update status
	 * @param string $type
	 * @param integer $formRequestId
	 * @param string $nextStatusCode
	 * @param string $currentStatusCode
	 * @param integer $workFlowId
	 * @param string $statusType
	 * @param string $formLabel
	 * @param integer $projectId
	 * @param integer $projectItemId
	 * @ref REDDEV-5127
	 * @version 5.3
	 */
	function editStatus($type='statusUpdate',$formRequestId = false,$nextStatusCode = false,$currentStatusCode = false,$workFlowId = false,$statusType = false ,$formLabel= false, $projectId = false, $projectItemId = false){
		try{
			$this->autoRender=false;
			$this->layout='ajax';
			$result = $this->service->editStatusService($type, $formRequestId, $nextStatusCode, $currentStatusCode, $workFlowId, $statusType);
			if(!isset($result['error'])){
				$loggedInUserArray = $this->Session->read('loggedUserData');
				$this->CommentService->addCommentOnStatusChangeOfForm($loggedInUserArray['User']['id'], $formRequestId, $formLabel, $result['currentStatus']);
				
			}
			echo json_encode($result);	
		}catch(exception $e){
				$this->Session->setFlash($e->getMessage(), 'default', array('class'=>'alert alert-error'));
				$this->redirect($this->referer());
		}
	}
}
