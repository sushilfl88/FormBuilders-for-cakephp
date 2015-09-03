<?php
	require_once APP. '/Service/Logging/ILogService.php';
	require_once APP. 'Plugin/FormBuilders/Service/FormRequest/IFormRequestService.php';
	require_once APP. 'Plugin/Files/Service/File/IFileService.php';
	
	require_once APP. '/Service/AppService.php';
	class FormRequestService extends AppService  implements IFormRequestService {
	
	private $fileModel;
	private $fieldValueModel;
	private $userModel;
	private $logger;
	private $formRequsetModel;
	private $fieldModel; 
	private $formModel;
	private $formFieldJoinModel;
	private $actionModel;
	private $configurationSettingModel;
	private $stateEngineModel;
	private $fileService;
	Private $formProjectItemJoinModel;
	
	public function __construct(
			FormRequest $formRequsetModel,
			FormFieldJoin $formFieldJoinModel,
			Field $fieldModel,
			Form $formModel,
			Action $actionModel,
			ConfigurationSetting $configurationSettingModel,
			User $userModel,
			FieldValue $fieldValueModel,
			StateEngine $stateEngineModel,
			File $fileModel,
			FormProjectItemJoin $formProjectItemJoinModel,
			ILogService $logger,
			IFileService $fileService ) {
									parent::__construct();
									$this->formRequsetModel = $formRequsetModel;
									$this->formFieldJoinModel = $formFieldJoinModel;
									$this->fieldModel = $fieldModel;
									$this->formModel = $formModel;
									$this->actionModel = $actionModel;
									$this->configurationSettingModel = $configurationSettingModel;
									$this->userModel = $userModel;
									$this->fieldValueModel = $fieldValueModel;
									$this->stateEngineModel = $stateEngineModel;
									$this->fileModel = $fileModel;
									$this->formProjectItemJoinModel  = $formProjectItemJoinModel;
									$this->logger = $logger;
									$this->fileService = $fileService;
								}
	/**
	 * viewRequestedForm
	 * @param Array $loggedInUserArray
	 * @param integer $formId
	 * @returns Array
	 * @ref REDDEV-5127
	 * @version 5.3
	 */
	public function viewRequestedForm($loggedInUserArray, $formId){ 
		$returnArray = array();
		$fields = $this->fieldModel->avlFieldListWithFieldIdForFormRequestPreview('form',$formId,array());
		$returnArray['form'] = $this->formModel->getFormDataWithCategoryByFormId('first',$formId);
		$transArray      = array();
        $fileArray = array();
        $fileChildArray = array();
        $returnArray['fields'] = $this->__processFieldData($fields);
        $LoggedInUserId    = $loggedInUserArray['User']['id'];
         //ACL for create and associate button TODO :uncomment following while implementing the form module througly
        //$returnArray['isClient']               = $this->userModel->isClientUser($LoggedInUserId);
        //$returnArray['viewAddButton'] = $this->actionModel->checkUserAction($LoggedInUserId, 'request_proj');
		//$returnArray['isAddFeature'] = $this->configurationSettingModel->getPreferenceByConfigurationCode($loggedInUserArray['BusinessUnit']['id'],'quick_add');
        //ends here
        return $returnArray;
	}
	/**
	 * editRequestedForm
	 * @param Array $loggedInUserArray
	 * @param integer $frId
	 * @return Array
	 * @ref REDDEV-5127
	 * @version 5.3
	 */
	public function editRequestedForm($loggedInUserArray, $frId){
		$returnArray = array();
		$elementval = array();
		//first find form id for requested form request id
		$formRequestData = $this->formRequsetModel->getFormRequestData($frId);
		if(isset($formRequestData) && !empty($formRequestData)){
			$formId = $formRequestData['FormRequest']['form_id'];
		}
		$fields = $this->fieldValueModel->getFieldValueFormRequestList('form', $frId, $formId);
		foreach ($fields as $data) {
            $elementval[] = $data['Field']['id'];
        }
        $remainingFields = $this->fieldModel->avlFieldListWithFieldIdForFormRequestPreview('form',$formId,$elementval);
       	$fields    = array_merge($fields, $remainingFields);
		$formData = $this->formModel->getFormDataWithCategoryByFormId('first',$formId);
		$workFlowId = $formData['FormCategory']['workflow_id'];
		$transArray      = array();
        $fileArray = array();
        $fileChildArray = array();
        $returnArray['fields'] = $this->__processFieldData($fields);
        //Note: For now as this functionality is of checklist getting available statuses for checklist for brief its need to be handeled seperatly
        $formStatus = $this->stateEngineModel->getAvialableStatusForForm($workFlowId, $frId, $formData['FormCategory']['label']);
        $returnArray['formStatus'] = $formStatus;
        //ends here
        $returnArray['form'] = $formData;
        $returnArray['formRequest'] = $formRequestData;
        $LoggedInUserId    = $loggedInUserArray['User']['id'];
        /**check if data exists in formProjectJoin table so if exists then set the data as it will be required in case anyone reject the form
         * to add task
         * */ 
       // $returnArray['formProjectItemJoin'] = $this->formProjectItemJoinModel->getDataByformRequestId($frId);
        
         //ACL for create and associate button TODO :uncomment following while implementing the form module througly
        //$returnArray['isClient']               = $this->userModel->isClientUser($LoggedInUserId);
        //$returnArray['viewAddButton'] = $this->actionModel->checkUserAction($LoggedInUserId, 'request_proj');
		//$returnArray['isAddFeature'] = $this->configurationSettingModel->getPreferenceByConfigurationCode($loggedInUserArray['BusinessUnit']['id'],'quick_add');
        //ends here
        return $returnArray;
	}
	
	/**
	 * __processFieldData will convert given array into desired form of array required for rendering of form
	 * @params Array $fieldsData
	 * @return Array
	 * @ref REDDEV-5127
	 * @version 5.3 
	 * 
	 */	
	public function __processFieldData($fieldsData){
		$returnArray = array();
		$fileArray = array();
        $fileChildArray = array();
        $transArray      = array();
        $childArray      = array();
		foreach ($fieldsData as $data) {
	            
	                if ($data['FormFieldJoin']['parent_fieldset_id'] == -1)
	                    $transArray[$data['FormFieldJoin']['weight']] = $data;
	                else
	                    $childArray[$data['FormFieldJoin']['parent_fieldset_id']][$data['FormFieldJoin']['weight']] = $data;
	                 if (isset($data['FieldValue'])) {    
	            		$path = $this->fileModel->findById($data['FieldValue']['value']); 
	                    if (!empty($path) && isset($path)) {
	                    	
	                        if ($data['FormFieldJoin']['parent_fieldset_id'] == -1) {
	                        	$fileArray[$data['FormFieldJoin']['weight']][$data['FieldValue']['id']] = 	$path['File']['id'];
								
	                        } else {
	                        	$fileChildArray[$data['FormFieldJoin']['parent_fieldset_id']][$data['FormFieldJoin']['weight']][$data['FieldValue']['id']] = $path['File']['id'];
	                           
	                        }
	                    }
	              
	            }
	            
	        }
			$returnArray['fields'] = $transArray;
			$returnArray['file'] = $fileArray;
			$returnArray['fileChild'] = $fileChildArray;
			$returnArray['child'] = $childArray;
			return $returnArray;
		}		
	/**
	 * save will save the form request data into the table
	 * @param Array $data
	 * @params Array $loggedInUserArray
	 * @params Array $filesData
	 * @param integer $frId
	 * @return boolean
	 * @ref REDDEV-5127
	 * @version 5.3
	 */
	public function save($data, $loggedInUserArray, $filesData, $frId = NULL){
		$this->logger->debug("Saving data in form request and fieldvalues table.");
		$return = array();
		//$this->transaction->setModelForTransaction($this->fieldValueModel);
		//$this->transaction->begin();
		try{
		 $LoggedInUserId    = $loggedInUserArray['User']['id'];
		 if(!empty($data)){ 
		 	//save form request data
		 	$formRequestData = $this->formRequsetModel->saveFormRequestData($data['FormRequest'], $loggedInUserArray, $frId);
		 	$formRequestId = $formRequestData['FormRequest']['id'];
		 	//requestData Saved
		 	//save form fields into fieldvalue
		 	$dataToProcess = $data['FormRequestData'];
			if(isset($data['FormRequestData']['file'])){
                    unset($dataToProcess['file']);
            }
            if (isset($dataToProcess)) {
            	/*second parameter is hierarchy value for now it is 0 or else HV*/
            	$isDataSaved = $this->fieldValueModel->saveUpdateFormRequestExtendedData($dataToProcess, 0, $formRequestId, $loggedInUserArray, $LoggedInUserId);
            	//file saving is seperatly handeled
            	if($isDataSaved){
            		/*first time when the form associated with ratecard price id store info into the form_project_item_join*/
            		if(isset($data['FormProjectItemJoin'])){
            		$isExists = $this->formProjectItemJoinModel->getCountByformRequestId($formRequestId);
            			if($isExists == 0){
            				$data['FormProjectItemJoin']['form_request_id'] = $formRequestId;
            				$this->formProjectItemJoinModel->save($data);
            			}
            		}
            	
            		if(isset($filesData) && $filesData != ""){
            			$this->fileService->saveFileData($filesData, $formRequestId, $LoggedInUserId);
            		}
            		
            	} 
            }
		 	//ends here
		 }
		}catch(Exception $e){
			//$this->transaction->rollback();
			$this->logger->error("Error while saving field value data.",$e);
			$this->logger->error($e->getMessage());
			//$result['errors']=$e->getMessage();
			return false;
		}
		//$this->transaction->commit();
					$return['isSaved'] = $isDataSaved;
					$return['formRequestId'] = $formRequestId;
					$return['formStatus'] = $data['FormRequest']['status'];
					$return['formLabel'] = $data['Form']['label'];
					return $return;
	}
	
	/**
	 * saveRejectionNoteService will save the rejection note
	 * @param array $data
	 * @return boolean
	 * @ref REDDEV-5127
	 * @version 5.3 
	 */
	public function saveRejectionNoteService($data){
		 //Data to save
			$dataSave = array();
			$dataSave['id'] = $data['fr_id'];
			$dataSave['rejection_notes'] = $data['rejection_note'];
			$dataSave['status'] = $data['status'];
			try{
				$saveData = $this->formRequsetModel->save($dataSave);
				return $saveData;
			}catch(Exception $e){
				debug($e);
				return false;
			}
			
			
		}
	/**
	 * 
	 * removeAssociatedFilesData will delete associations
	 * @param Array $data
	 */
	public function removeAssociatedFilesData($data){
	 				try{
	 				$this->fileModel->deleteAll(array('File.id'=>$data['fileId'],false));
					unlink($data['path']);
					//also delete fieldvalue entry for file
					$this->fieldValueModel->deleteAll(array('FieldValue.value' => $data['fileId'],'FieldValue.form_request_id' =>$data['formRequestId'] , 'project_item_id'=> 0,'project_id'=>0),false);
					//ends here
	 				}catch(Exception $e){
						debug($e);exit;
					}
	 }
	 /**
	  * 
	  * editStatusService will change the status
	  * @param string $type
	  * @param integer $formRequestId
	  * @param string $nextStatusCode
	  * @param string $currentStatusCode
	  * @param integer $workFlowId
	  * @param string $statusType
	  */
	 public function editStatusService($type='statusUpdate',$formRequestId = false,$nextStatusCode = false,$currentStatusCode = false,$workFlowId = false,$statusType = false){
	 	try{
	 	if($type=='statusUpdate'){
				
				if($formRequestId && $nextStatusCode && $currentStatusCode){
					$statusCheck=$this->stateEngineModel->canTransitionToForms($currentStatusCode, $nextStatusCode,$workFlowId);
					$nextStatusCode="'$nextStatusCode'";
					$returnArray=array();
						if($statusCheck){
							$currentStatusOfFormRequest = $this->formRequsetModel->findById($formRequestId);
						if($this->formRequsetModel->updateAll(array('status'=>$nextStatusCode),array('id'=>$formRequestId))){
							$returnArray['success']='Status Updated Succesfully.';
							$formStatus=$this->stateEngineModel->getAvialableStatusForForm($workFlowId, $formRequestId, $statusType);
							$returnArray['formRequestStatus']=$formStatus;
							$formRequestSavedData = $this->formRequsetModel->findById($formRequestId);
							$returnArray['currentStatus'] = $formRequestSavedData['FormRequest']['status'];
								
							}
						else{
							$returnArray['error']='Error Updating Status';
						}
					}	
					else{
						$returnArray['error']='You Don\'t Have Access To Update To This Status';
					}
					return $returnArray;	
				}
			}
	 	}
		catch(exception $e){
				debug($e);
				return false;
		}
	 }
	/**
	  * sendFormEmails will send emails
	  * @param array $currentData
	  * @param string $priStatus
	  * @param string $distributionList
	  * @param string $loggedInUserEmail
	  * @version 5.3.3
	  */
	 function sendFormEmails($currentData,$priStatus,$distributionList,$loggedInUserEmail){
	 	return $this->formRequsetModel->sendEmailForActiveStatusBriefs($currentData, $priStatus, $distributionList, $loggedInUserEmail);
	 }
	}
	?>
