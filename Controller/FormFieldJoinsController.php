<?php
App::uses('FormBuildersAppController', 'FormBuilders.Controller');
class FormFieldJoinsController  extends FormBuildersAppController { 
	var $name = 'FormFieldJoins';
	public $uses=array(//'FormBuilders.CampaignTypeFieldsetJoin',
					   'FormBuilders.FormFieldJoin',
					   'FormBuilders.CampaignFieldset',
					   'FormBuilders.CampaignType',
					   'Projects.Field',
					   'AccessControls.HierarchyValue');
	/*view() will render the view
	 * @params string $type
	 * @params string $from
	 */
	public function view($type,$from='clients',$ctId){
		$FilteredFieldDataByType=$this->Field->getNotSelectedHidExtendedDataByType($type,$ctId);
		$extData='form';
		
		$this->set('from',$from);
		$this->set('type',$type);
		$this->set('extData',$extData);
		$this->set('ctId',$ctId);
		$this->set('FilteredFieldDataByType',$FilteredFieldDataByType);
	}
	
	/* This function is used to add data into 'FormFieldJoins' table
	 * @return false
	 * @version 5.2
	 */
	public function add() { 
		if($this->RequestHandler->isAjax()){			
			if(!empty($this->data)){
				$this->autoRender = false;
				$this->layout = 'ajax';
				
				foreach($this->data['fields'] as $fieldVal){
						$projectType = $this->Field->getProjectType($fieldVal); 
						$maxWeight = $this->FormFieldJoin->getMaxWeight($this->data['ctId'],$projectType);
						$weight = $maxWeight[0]['maxweight']+100;// to add weight value
						$this->FormFieldJoin->saveCampaignTypeFieldJoin($this->data['ctId'],$fieldVal,$weight);							
					}
					
				return false;
			} 
		} 
	} // add
	/* delete() will delete association and field entry from table
	 * @return false
	 * @version 5.2
	 * 
	 */
   	public function delete() { 
   		if($this->RequestHandler->isAjax()){
			if(isset($this->data)){
				$this->autoRender = false;
				$this->layout = 'ajax';
				if($this->data["from"] == "central"){
					$deleteList = $this->data["field_val_id"];
					foreach ($deleteList as $deleteListVal) {	
						$recordToDelete = $this->FormFieldJoin->findById($deleteListVal);
						$weight = $recordToDelete["FormFieldJoin"]["weight"];
						/*delete entry from fields*/
						//$this->Field->deleteAll(array('id' => $recordToDelete['FormFieldJoin']['field_id']));	//TODO confirm weather to delete or not commenting
						/*ends here*/
						$this->FormFieldJoin->deleteAll(array('id' => $deleteListVal));	
						$this->FormFieldJoin->updateAll(array('parent_fieldset_id'=>'-1'),array('parent_fieldset_id' => $deleteListVal));	
						$listOfFieldsHavingGreaterWeight = $this->FormFieldJoin->updateFieldsHavingGreaterWeight($recordToDelete["FormFieldJoin"]["form_id"],$this->data["type"],$weight);
								foreach($listOfFieldsHavingGreaterWeight as $key=>$val){
									$this->FormFieldJoin->id = $key;
									$val = $val-100; 
									$this->FormFieldJoin->saveField('weight', $val);
								}	
					} 
				}

				return false;
			} 
		} 
	} // delete
	/* edit() the weight or status will update
	 * @return echo 1
	 * @version 5.2
	 */
	public function edit(){
		if($this->request->is('ajax')){
		if(!empty($this->data)){
				$this->autoRender = false;
				$newWeight="";
				$prevWeight="";
				if(!empty($this->data['arrayallval'])){
					$updateList = $this->data['arrayallval'];
			    	$updateTo=$this->data['updateTo'];
					foreach ($updateList as $updateId) {
							$this->FormFieldJoin->id = $updateId; 
							$this->FormFieldJoin->saveField('status', $updateTo);
														}
				}else if(!empty($this->data['modify'])){
					$arrayFoeSelField = $this->FormFieldJoin->findById($this->data['hfid']);
					$weight = $arrayFoeSelField['FormFieldJoin']['weight'];
					//for fieldset chk parent id
					if($arrayFoeSelField['FormFieldJoin']['parent_fieldset_id'] == -1){
						if($this->data['modify'] == 'up'){
							if($weight > 1){
								$prevWeight= $weight;
								$newWeight = $weight-100 ;
								}
						}else if($this->data['modify'] == 'down'){
							$maxWeight = $this->FormFieldJoin->getMaxWeight($arrayFoeSelField['FormFieldJoin']['form_id'],$this->data['type']); 
							if($weight < $maxWeight[0]['maxweight']){
									$prevWeight= $weight;
									$newWeight = $weight+100 ;
								}
							}
						    $getUpperRowid = $this->FormFieldJoin->getIdByWeightAndcatId($arrayFoeSelField['FormFieldJoin']['form_id'],$newWeight,$this->data['type']);
							$this->FormFieldJoin->id = $getUpperRowid['FormFieldJoin']['id'];
							$this->FormFieldJoin->saveField('weight', $prevWeight);
							$this->FormFieldJoin->id = $arrayFoeSelField['FormFieldJoin']['id'];
							$this->FormFieldJoin->saveField('weight', $newWeight);
							$this->FormFieldJoin->reOrderingAssociatedFields($this->data['hfid']);//reorder associated fields if fieldset
					}elseif($arrayFoeSelField['FormFieldJoin']['parent_fieldset_id'] > 0){
						/* logic for members of fieldsets to move up and down
						 * */
						$listOfFields = $this->FormFieldJoin->find('list',array("fields" => array("FormFieldJoin.weight"),"conditions" => array("FormFieldJoin.parent_fieldset_id" =>$arrayFoeSelField['FormFieldJoin']['parent_field_id'])));
						$noOfAssociatedFields = count($listOfFields);
						if($this->data['modify'] == 'up'){
							if($weight > 1){
								//chk for lowest value
								if($weight-1 >= min($listOfFields))
								{
									$prevWeight= $weight;
									$newWeight = $weight-1 ;
								}
							}
						}else if($this->data['modify'] == 'down'){
						if($weight < max($listOfFields)){
							$prevWeight= $weight;
							$newWeight = $weight+1 ;
							}
							}
						    $getUpperRowid = $this->FormFieldJoin->getIdByWeightAndcatId($arrayFoeSelField['FormFieldJoin']['campaign_type_id'],$newWeight,$this->data['type']);
							if($newWeight!="" && $prevWeight!=""){
							    $this->FormFieldJoin->id = $getUpperRowid['FormFieldJoin']['id'];
								$this->FormFieldJoin->saveField('weight', $prevWeight);
								
								$this->FormFieldJoin->id = $arrayFoeSelField['FormFieldJoin']['id'];
								$this->FormFieldJoin->saveField('weight', $newWeight);
							}
					}//end of chk for parent
					echo 1;
					return;	
				}
	    	}
		}
	}
	
	/*
	 * associateToFieldsets() is used to associate fields to fieldsets
	 * @return false
	 * @version 5.2
	 */
	public function associateToFieldsets(){
		if($this->request->is('ajax')){
			if(!empty($this->data)){
					$this->autoRender = false;
					$hidFieldJoinId=array();
					if(isset($this->data['FormFieldJoinIds'])&&!empty($this->data['FormFieldJoinIds'])){
					foreach($this->data['FormFieldJoinIds'] as $key=>$values){
						$hidFieldJoinId[] = $values['id'];
					}//end of foreach
					//de-associate the fields
					$result = $this->FormFieldJoin->updateAll(array('FormFieldJoin.parent_fieldset_id' => -1),
												   			 array('FormFieldJoin.parent_fieldset_id' => $this->data['formPriId'],
												   			 'FormFieldJoin.form_id' => $this->data['formId']));
											   			 
					$resultAssociate = $this->FormFieldJoin->updateAll(array('FormFieldJoin.parent_fieldset_id' => $this->data['formPriId']),
												   			 array('FormFieldJoin.id' => $hidFieldJoinId,
												   		 			'FormFieldJoin.form_id' => $this->data['formId']));
					//find weight and reorder
					
					$this->FormFieldJoin->reOrderingWeight($this->data['formId'],$this->data['type']);
					//die;
					$this->FormFieldJoin->reOrderingAssociatedFields($this->data['formPriId']);													   		
												   			 
					echo $resultAssociate; 
					return ;
					}
					return false;
			}
		}
	}
/*
	 * associateExisitingFields() is used to associate fields to fieldsets
	 * Note:Assumption :while choosing fieldset from existing fields only fieldset will get associated not its fields as it may differ from form to form
	 * @return 1
	 * @version 5.2
	 */
	public function associateExisitingFields(){ 
		if($this->request->is('ajax')){ 
			if(!empty($this->data)){
					$this->autoRender = false;
					
					$hidFieldJoinId=array();
					if(isset($this->data['FieldIds'])&&!empty($this->data['FieldIds'])){
						$result = $this->FormFieldJoin->saveFieldsData($this->data['FieldIds'],$this->data['formId'],'fieldExist');
					
						/*find weight and reorder*/
						$this->FormFieldJoin->reOrderingWeight($this->data['formId'],$this->data['type']);
					}
//					if(isset($this->data['FieldSetIds'])&&!empty($this->data['FieldSetIds'])){
//						foreach($this->data['FieldSetIds'] as $key => $values){
//							/*find fields associated with fieldsets and associate to form*/
//								$this->__associateExistingFieldsets($values,$this->data['formId']);
//							/*ends here*/
//						}
//					//$this->FormFieldJoin->saveFieldsData($this->data['FieldSetIds']);
//					}
					if($result){
					return 1;
					}else{
					return false;
					}
			}
		}
	}
//	public function __associateExistingFieldsets($fieldId = null ,$formId = null){debug($fieldId);debug($formId);die;
//		//find associated fields with fieldsets
//		$associatedFieldList = $this->FormFieldJoin->find('list',array('fields' => array('id'),'conditions' => array('form_id' => $formId , 'field_id' => $fieldId)));
//		debug($associatedFieldList);die;
//	}

}