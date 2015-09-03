<?php
	require_once APP. '/Service/Logging/ILogService.php';
	require_once APP. 'Plugin/FormBuilders/Service/FormWorkflowStatusJoin/IFormWorkflowStatusJoinService.php';
	class FormWorkflowStatusJoinService implements IFormWorkflowStatusJoinService {
	Private $formWorkflowStatusJoinModel;		
	private $workflowModel;
	private $statusCodeModel;
	private $stateEngineModel;
	private $formModel;
	private $rateCardPriceModel;
	private $logger;
	Protected $formChecklist; 
	
	public function __construct(
			FormWorkflowStatusJoin $formWorkflowStatusJoinModel,
			Workflow $workflowModel,
			StatusCode $statusCodeModel,
			StateEngine $stateEngineModel,
			Form $formModel,
			FormCategory $formCategoryModel,
			RateCardPrice $rateCardPriceModel,
			ILogService $logger) {
							$this->formWorkflowStatusJoinModel = $formWorkflowStatusJoinModel;
							$this->workflowModel = $workflowModel;
							$this->statusCodeModel = $statusCodeModel;
							$this->stateEngineModel = $stateEngineModel;
							$this->formModel = $formModel;
							$this->formCategoryModel = $formCategoryModel;
							$this->rateCardPriceModel = $rateCardPriceModel;
							$this->logger = $logger;
							$this->formChecklist = 'Checklist';
						}
	/** 
	 * This function will fetch all categories of form
	 * @params string $formCategoryLabel
	 * @version 5.3
	 * @ref REDDEV-5127
	 */
	function getFormCategoryByLabel($formCategoryLabel){
		return $this->formCategoryModel->getFormCategories('first',array('id','label'),array('label' => $formCategoryLabel));
	}
	/** 
	 * This function will list all forms for given form category.
	 * @params Integer $formCategoryId
	 * @version 5.3
	 * @ref REDDEV-5127
	 */
	public function getForms($formCategoryId){
		$fields = array('id','label');
		$conditions = array('category_id' => $formCategoryId,'(Form.status = "survey_form_active" OR Form.status = "checklist_form_active" OR Form.status = "brief_form_active")');
		
		return $this->formModel->getFormData('list',$fields,$conditions);
	}
	/**
	 * This function will list all statuses belonging to given workflow
	 * @version 5.3
	 * @ref REDDEV-5127
	 */
	function getWorkflowStatuses($workflowId){
		return $this->statusCodeModel->getWorkflowListForDigitalPrintCreativeBroadcast($workflowId);
		
	}
	
	/** getProjectItemWorkflowsStatusesChecklistsService will list all checklists,workflows,& statuses
	 * @version 5.3
	 * @ref REDDEV-5127
	 */
	function getProjectItemWorkflowsStatusesChecklistsService(){
		try{
		$listOfWorkflows = $this->workflowModel->getWorkflowListForDigitalPrintCreativeBroadcast('all'); 
		$workflowList = array();
		$statusList = array();
		$returnArray = array();
		$counter = 0;
		foreach($listOfWorkflows as $key => $value){
			$workflowList[$key]["id"] = $value['Workflow']['id'] ;
			$workflowList[$key]["label"] = $value['Workflow']['label'] ;
			//get all statuses workflow wise
				$listOfStatusCodes = $this->statusCodeModel->getReviewStatusesByWorkflowId($value['Workflow']['id']);
				foreach($listOfStatusCodes as $statusKey => $statusVaue){
					$statusList[$counter]['status_code'] = $statusVaue['StatusCode']['status_code'];
					$statusList[$counter]['status_label'] = $statusVaue['StatusCode']['label'];
					$statusList[$counter]['workflowId'] = $value['Workflow']['id'];
					$counter++;
				}
			//ends here
		}
		
		$returnArray['workflowList'] = $workflowList;
		$returnArray['statusList'] = $statusList;
		$formCategoryData = $this->getFormCategoryByLabel($this->formChecklist);
		$returnArray['form']  = $this->getForms($formCategoryData['FormCategory']['id']);
		return $returnArray;
		}catch(Exception $e){
			debug($e);
			exit;
		}
	}
	/** associateFormsToRateCardsService will associate a form with selected ratecard prices, status & workflow id
	 * @params Array $data
	 * @version 5.3
	 * @ref REDDEV-5127
	 */
	function associateFormsToRateCardsService($data){
		try{
		$dataToSave = array();
		$flag = false;//Flag will be true if combination exists
		$counter = 0;
		if(!empty($data)){ 
			if(!empty($data['rateCardIds'])){
				$rateCardPriceIds = explode( ',', $data['rateCardIds'] );
				if(!empty($rateCardPriceIds)){
					foreach($rateCardPriceIds as $keyPrice => $valueRateCardPriceId){
						$workflowId =  (isset($data['workflowId']))? $data['workflowId']:NULL;
						$curStatus =  (isset($data['statusCode']))? $data['statusCode']:NULL;
						$formId = (isset($data['formId']))? $data['formId']:NULL;
						$dataToSave[$counter] = $this->__formArrayForAssociationOrDiassociation($valueRateCardPriceId,$workflowId,$curStatus,$formId);
						//ends here
						//chk if combination exists if yes then delete entry & add new entry
						$existingFormWorkflowStatusJoinId = $this->formWorkflowStatusJoinModel->isExists($dataToSave[$counter]['state_engine_id'],$dataToSave[$counter]['ratecard_price_id'],$dataToSave[$counter]['form_id']);
						if($existingFormWorkflowStatusJoinId){
							$flag = true;
							$conditions = array('FormWorkflowStatusJoin.id'=>$existingFormWorkflowStatusJoinId);	
						     //delete from form workflow status
							$dataDeleted = $this->formWorkflowStatusJoinModel->deleteDataInFormWorkflowStatusJoin($conditions);
							
						}
						//ends here
						$counter++;
					}
					if(!empty($dataToSave)){	
					     //saveData into form workflow status
							     $dataSaved = $this->formWorkflowStatusJoinModel->saveDataInFormWorkflowStatusJoin($dataToSave);
							     return $dataSaved;
							}else{
								return false;
							}
					     
				}
			}
		}
		}catch(Exception $e){
			debug($e);
			exit;
		}
	}
	/**
	 * dissociateFormsToRateCardsService will dissociate a form with selected ratecard prices, status & workflow id
	 * @params Array $data
	 * @version 5.3
	 * @ref REDDEV-5127
	 */
	function dissociateFormsToRateCardsService($data){
		try{
		$dataToDelete = array();
		$counter = 0;
		if(!empty($data)){ 
			if(!empty($data['rateCardIds'])){
				$rateCardPriceIds = explode( ',', $data['rateCardIds'] );
				if(!empty($rateCardPriceIds)){
					foreach($rateCardPriceIds as $keyPrice => $valueRateCardPriceId){
						/**
						 * $valueRateCardPriceId contains ratecard price id
						 */
						$ratecardPriceData = $this->rateCardPriceModel->getRatecardPriceData($valueRateCardPriceId);
						//To fetch state engine Id for given combination of workflow and status
						$workflowId =  (isset($data['workflowId']))? $data['workflowId']:NULL;
						$curStatus =  (isset($data['statusCode']))? $data['statusCode']:NULL;
						$formId = (isset($data['formId']))? $data['formId']:NULL;
						$dataToDelete[$counter] = $this->__formArrayForAssociationOrDiassociation($valueRateCardPriceId,$workflowId,$curStatus,$formId);
						if($workflowId != NULL && $curStatus != NULL){
							$dataToDelete[$counter]['state_engine_id'] = $this->stateEngineModel->getStateEngineIdByWorkflowAndCurrentStatus($workflowId,$curStatus);
						}
						
						//ends here
						//chk if combination exists if yes then unset that array key
						$isCombinationExists = $this->formWorkflowStatusJoinModel->getFormWorkflowStatusJoinIdByStateEngineIdRatecardPriceIdAndFormId($dataToDelete[$counter]['state_engine_id'],$dataToDelete[$counter]['ratecard_price_id'],$dataToDelete[$counter]['form_id']);
						if($isCombinationExists){
							$deletArrayFormWorkflowStatusJoinId[] = $isCombinationExists;
						}
						//ends here
						$counter++;
					}
					
						if(!empty($deletArrayFormWorkflowStatusJoinId)){
							$conditions = array('FormWorkflowStatusJoin.id'=>$deletArrayFormWorkflowStatusJoinId);	
					     //saveData into form workflow status
							     $dataDeleted = $this->formWorkflowStatusJoinModel->deleteDataInFormWorkflowStatusJoin($conditions);
							     return $dataDeleted;
							}else{
								return false;
							}
					     
				}
			}
		}
		}catch(Exception $e){
				debug($e);
				exit;
			}
	}
	/**
	 * formArrayForAssociationOrDiassociation() will form an array
	 * @params integer $valueRateCardPriceId
	 * @params integer $counter
	 * @return array 
	 * @version 5.3
	 * @ref REDDEV-5127
	 */
	public function __formArrayForAssociationOrDiassociation($valueRateCardPriceId,$workflowId,$curStatus,$formId){
						$data = array();
						/**
						 * $valueRateCardPriceId contains ratecard price id
						 */
						$ratecardPriceData = $this->rateCardPriceModel->getRatecardPriceData($valueRateCardPriceId);
						$data['master_deliverable_id'] = $ratecardPriceData['RateCardPrice']['master_deliverable_id'];
						$data['rate_card_id'] = $ratecardPriceData['RateCardPrice']['rate_card_id'];
						$data['ratecard_price_id']= $valueRateCardPriceId;
						$data['form_id'] = $formId;
						//To fetch state engine Id for given combination of workflow and status
						
						if($workflowId != NULL && $curStatus != NULL){
							$data['state_engine_id'] = $this->stateEngineModel->getStateEngineIdByWorkflowAndCurrentStatus($workflowId,$curStatus);
						}
						if(!empty($data)){
							return $data;
						}else{
							return false;
						}
		
	}
	/**
	 * 
	 * getAssociatedFormsByRatecardService will return list of associated form with rateCards
	 * @param integer $rateCardPriceId
	 * @version 5.3
	 * @ref REDDEV-5127
	 */
	public function  getAssociatedFormsByRatecardService($rateCardPriceId){
		try{
			$result = $this->formWorkflowStatusJoinModel->getAssociatedFormsWithStatusByRatecard('all',$rateCardPriceId);
			return $result; 
		}catch(Exception $e){
			return false;
		}
		
	}
	public function disassociateByFormWorkflowStatusJoinIdService($formWorkflowStatusJoinId = false){
		try{
			$conditions = array('FormWorkflowStatusJoin.id'=>$formWorkflowStatusJoinId);
			$result = $this->formWorkflowStatusJoinModel->deleteDataInFormWorkflowStatusJoin($conditions);
			return $result; 
		}catch(Exception $e){
			return false;
		}
	}
	}
?>