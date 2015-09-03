<?php
App::uses('FormBuildersAppController', 'FormBuilders.Controller');
App::uses('UUID', 'Lib/Utility');
require_once APP. '/Plugin/FormBuilders/Context/FormBuildersContext.php';
/**
 * FormWorkflowStatusJoins Controller
 *
 */
class FormWorkflowStatusJoinsController extends FormBuildersAppController {
	var $name = 'FormWorkflowStatusJoins';
	
	private $service;
		public function setUp() {
			$this->service = FormBuildersContext::getInstance()->get(FormBuildersContext::FORM_WORKFLOW_STATUS_JOIN_MODEL);

		}
	public function beforeFilter(){
			parent::beforeFilter();
			$this->setUp();
		}
	/**
	 * index function will list of project items workflows and form categories
	 * @ref REDDEV-5127
	 * @version 5.3
	 */
	public function index(){
		$this->layout = 'ajax';
		if($this->RequestHandler->isAjax()){
			if($this->RequestHandler->isPost()){
				$ratecardPriceIds =  (isset($this->data['rateCardPricesId']))? $this->data['rateCardPricesId']:NULL;
				if($ratecardPriceIds){
				$ratecardPriceIdsStr = implode(",", $ratecardPriceIds);
				}
				$this->set('ratecardPriceIds',$ratecardPriceIdsStr);
			}
		}
	}
	/**
	 * getProjectItemWorkflowsStatusesChecklists will list all project items workflows ,statuses & checklists
	 * @ref REDDEV-5127
	 * @version 5.3
	 */
	public function getProjectItemWorkflowsStatusesChecklists(){
		if($this->RequestHandler->isAjax()){
				$returnArray = array();
				$this->autoRender = false;
				$this->layout = 'ajax';
				/* $listOfData will contain list of 
				 * a. forms
				 * b. workflows
				 * c. checklists
				 */
				$listOfData = $this->service->getProjectItemWorkflowsStatusesChecklistsService(); 
				echo json_encode($listOfData);
				return;
			}
	}
	/** associateFormsToRateCards will associate form checklist with selected ratecardpriceIds
	 *  @ref REDDEV-5127
	 *  @version 5.3
	 */
	public function associateFormsToRateCards(){
		if($this->RequestHandler->isAjax()){
			if($this->RequestHandler->isPost()){
				$this->autoRender = false;
				$this->layout = 'ajax';
				echo $this->service->associateFormsToRateCardsService($this->data);
				return;
				
			}
		}
	
	}
		/** dissociateFormsToRateCards will associate form checklist with selected ratecardpriceIds
		 *  @ref REDDEV-5127
		 *  @version 5.3
		 */
		public function dissociateFormsToRateCards(){
			if($this->RequestHandler->isAjax()){
				if($this->RequestHandler->isPost()){
					$this->autoRender = false;
					$this->layout = 'ajax';
					echo $this->service->dissociateFormsToRateCardsService($this->data);
					return;
					
				}
			}
		
		}
	/** 
	 * getFormsCategoryWise will list forms for provided categories
	 * @ref REDDEV-5127
	 * @version 5.3
	 */
	Public function getFormsCategoryWise(){
		if($this->RequestHandler->isAjax()){
			if($this->RequestHandler->isPost()){
				$formCategoryId =  (isset($this->data['formCategoryId']))? $this->data['formCategoryId']:NULL;
				$this->autoRender = false;
				$this->layout = 'ajax';
				$returnArray['forms'] = $this->service->getForms($formCategoryId);
				echo json_encode($returnArray);
			}
		}
	}
	/**
	 * 
	 * listAssociatedChecklists will list associated forms
	 * @param integer $rateCardPriceId
	 * @ref REDDEV-5127
	 * @version 5.3
	 */
	 public function listAssociatedChecklists($rateCardPriceId = NULL){
	 	if($this->RequestHandler->isAjax()){
	 		if(isset($rateCardPriceId) && $rateCardPriceId != NULL){
	 		$lists = $this->service->getAssociatedFormsByRatecardService($rateCardPriceId);
	 		$this->set('associatedData',$lists);
	 		
	 		}
	 	}
	 }
	 /**
	  * disassociateByFormWorkflowStatusJoinId will delete association of rate card price id and workflow and status
	  * @param integer $formWorkflowStatusJoinId
	  * @ref REDDEV-5127
	  * @version 5.3
	  */
	  
	public function disassociateByFormWorkflowStatusJoinId($formWorkflowStatusJoinId = false){
		if($this->RequestHandler->isAjax()){
			$this->autoRender = false;
			if(isset($formWorkflowStatusJoinId) && $formWorkflowStatusJoinId != NULL){
				$result = $this->service->disassociateByFormWorkflowStatusJoinIdService($formWorkflowStatusJoinId);
				echo json_encode($result);
			}
		}
	}
	
}
