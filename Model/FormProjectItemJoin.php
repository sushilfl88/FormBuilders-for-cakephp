<?php
App::uses('FormBuildersAppModel','FormBuilders.Model');

class FormProjectItemJoin extends FormBuildersAppModel {
	/**
	 * 
	 * getIdByStateEngineAndProjectItemId will fetch the table data for given stateEngine Id and project Item Id
	 * @param integer $stateEngineId
	 * @param integer $projectItemId
	 * @ref REDDEV-5127
	 * @version 5.3
	 */
	public function getIdByStateEngineAndProjectItemId($stateEngineId = NULL,$projectItemId = NULL){
		try{
			return $this->find('first',array('fields' => array('FormProjectItemJoin.form_request_id'),
											 'conditions'=>array('FormProjectItemJoin.state_engine_id' => $stateEngineId, 'FormProjectItemJoin.project_item_id' => $projectItemId )));
		}catch(Exception $e){
			debug($e);
			return False;
		}
	}
	/**
	 * 
	 * getCountByformRequestId will return count for requested form_request_id
	 * @param integer $formRequestId
	 * @ref REDDEV-5127
	 * @version 5.3
	 */
	public function getCountByformRequestId($formRequestId = NULL){
		return $this->find('count',array('conditions' => array('FormProjectItemJoin.form_request_id' => $formRequestId)));
	}
	/**
	 * 
	 * getCountByProjectItemId will return count for requested project_item_id
	 * @param integer $projectItemId
	 * @ref REDDEV-5127
	 * @version 5.3
	 */
	public function getDataByProjectItemId($projectItemId = NULL){
		return $this->find('all',array('conditions' => array('FormProjectItemJoin.project_item_id' => $projectItemId)));
	}
	/**
	 * 
	 * getDataByformRequestId will get the data for requested form request id
	 * @param integer $formRequestId
	 * @ref REDDEV-5127
	 * @version 5.3
	 */
	public function getDataByformRequestId($formRequestId = NULL){
		return $this->find('first',array('conditions' => array('FormProjectItemJoin.form_request_id' => $formRequestId)));
	}
	
}
?>