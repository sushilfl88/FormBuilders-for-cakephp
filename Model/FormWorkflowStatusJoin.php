<?php
App::uses('FormBuildersAppModel','FormBuilders.Model');

class FormWorkflowStatusJoin extends FormBuildersAppModel {
	
	/**
	 * saveDataInFormWorkflowStatusJoin will save the givenData into the  form_workflow_status_joins table
	 * @params Array $dataToSave
	 * @ref REDDEV-5127
	 * @version 5.3
	 */
	public function saveDataInFormWorkflowStatusJoin($dataToSave){
		if($this->saveAll($dataToSave)){
			return true;
		}else{
			return false;
		}
		
	}
	/**
	 * isExists will check if entry is already exists & returns the id
	 * @params integer $stateEngineId
	 * @params integer $ratecardPriceId
	 * @return integer
	 * @ref REDDEV-5127
	 * @version 5.3
	 * 
	 */
	public function isExists($stateEngineId = NULL ,$ratecardPriceId = NULL ){ 
		try{
		$result = $this->find('first',array(	'fields' => array('FormWorkflowStatusJoin.id'),
													'conditions' => array('FormWorkflowStatusJoin.state_engine_id' => $stateEngineId,
													  					  'FormWorkflowStatusJoin.ratecard_price_id' => $ratecardPriceId,
																			)));
		
		return $result['FormWorkflowStatusJoin']['id'];
		}catch(Exception $e){
			debug($e);
			return false;
			
		}
	}
	/**
	 * isExists will check if entry is already exists
	 * @params integer $stateEngineId
	 * @params integer $ratecardPriceId
	 * @params integer $formId
	 * @return integer
	 * @ref REDDEV-5127
	 * @version 5.3
	 * 
	 */
	public function getFormWorkflowStatusJoinIdByStateEngineIdRatecardPriceIdAndFormId($stateEngineId = NULL ,$ratecardPriceId = NULL ,$formId = NULL){
		$data = $this->find('first',array('fields' => array('FormWorkflowStatusJoin.id'),
												  'conditions' => array('FormWorkflowStatusJoin.state_engine_id' => $stateEngineId,
																		'FormWorkflowStatusJoin.ratecard_price_id' => $ratecardPriceId,
																		'FormWorkflowStatusJoin.form_id' => $formId)));
		if(!empty($data)){
			return $data['FormWorkflowStatusJoin']['id'];
		}else{
			return false;
		}
		
	}
	/** deleteDataInFormWorkflowStatusJoin
	 * @params Array $conditions
	 */
	public function deleteDataInFormWorkflowStatusJoin($conditions){
		if($this->deleteAll($conditions,false)){
			return true;
		}else{
			return false;
		}
		
	}
	/**
     * getDataFromFormWorkflowStatusJoinByGivenFields() will return ratecard id
     * @params string $fetchType
     * @params Array $fields
     * @params Array $conditions
     * @return integer
	 * @version 5.3
	 * @ref REDDEV-5127 
     */
    public function getDataFromFormWorkflowStatusJoinByGivenFields($fetchType = 'list',$fields,$conditions){
    	
    	$returnData = $this->find($fetchType,array('fields' => $fields,
    									          'conditions' => $conditions));
    	return $returnData;
    }
	public function getAssociatedFormsWithStatusByRatecard($fetchType = 'all',$rateCardId = NULL){
		
		$returnData = $this->find($fetchType,array('fields' => array('Form.label','StatusCode.label','Form.id','FormWorkflowStatusJoin.id'),
												    'joins' => array(array('table'	=>	'state_engine',
																			'alias'		=>	'StateEngine',
																			'type'		=>	'inner',
																			'conditions'	=>	array('StateEngine.id = FormWorkflowStatusJoin.state_engine_id'))
																	   ,array('table'=>'status_codes',
																			'alias'=>'StatusCode',
																			'type'=>'inner',
																			'conditions'=>array('StatusCode.status_code  = StateEngine.cur_status_code')
																		 ),
																		 array('table'=>'forms',
																			'alias'=>'Form',
																			'type'=>'inner',
																			'conditions'=>array('Form.id  = FormWorkflowStatusJoin.form_id')
																		 ),
																		),
													'conditions' => array('FormWorkflowStatusJoin.ratecard_price_id' => $rateCardId)));
		return 	$returnData;																
																		
	}
}
?>