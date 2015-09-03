<?php
interface IFormWorkflowStatusJoinService {
	/** 
	 * This function will fetch category Id
	 * @params string $formCategoryLabel
	 * @version 5.3
	 * @ref REDDEV-5127
	 */
	function getFormCategoryByLabel($formCategoryLabel);
	/** 
	 * This function will list all forms for given form category.
	 * @params Integer $formCategoryId
	 * @version 5.3
	 * @ref REDDEV-5127
	 */
	function getForms($formCategoryId);
	/**
	 * This function will list all statuses belonging to given workflow
	 * @version 5.3
	 * @ref REDDEV-5127
	 */
	function getWorkflowStatuses($workflowId);
	/**
	 * 
	 */
	function getProjectItemWorkflowsStatusesChecklistsService();
	/** associateFormsToRateCardsService will associate a form with selected ratecard prices, status & workflow id
	 * @params Array $data
	 * @version 5.3
	 * @ref REDDEV-5127
	 */
	function associateFormsToRateCardsService($data);
	/**
	 * dissociateFormsToRateCardsService will dissociate a form with selected ratecard prices, status & workflow id
	 * @params Array $data
	 * @version 5.3
	 * @ref REDDEV-5127
	 */
	function dissociateFormsToRateCardsService($data);
	/**
	 * forArrayForAssociationOrDiassociation() will form an array
	 * @params integer $valueRateCardPriceId
	 * @params integer $workflowId
	 * @params integer $curStatus
	 * @params integer $formId
	 * @return array 
	 * @version 5.3
	 * @ref REDDEV-5127
	 */
	public function __formArrayForAssociationOrDiassociation($valueRateCardPriceId,$workflowId,$curStatus,$formId);
	/**
	 * getAssociatedFormsByRatecardService will return list of associated form with rateCards
	 * @param integer $rateCardPriceId
	 * @version 5.3
	 * @ref REDDEV-5127
	 */
	public function  getAssociatedFormsByRatecardService($rateCardPriceId);
}
?>