<?php
App::uses('FormBuildersAppModel','FormBuilders.Model');

class Form extends FormBuildersAppModel {
	var $name = 'Form';
	/* saveFormData is use to save the data into form table
	 * @params array data to save
	 * @return true if saved
	 * @version 5.2
	 * @ref REDDEV-4736
	 */
	public function saveFormData($dataToSave=NULL){
		try{
			if(!empty($dataToSave) && isset($dataToSave)){
			//$dataToSave['Form']['name'] = str_replace(" ","_",$dataToSave['Form']['label']);
				 if($this->save($dataToSave)){
				 	return 1;
				}
			}
		}catch(Exception $e){
			debug($e->getMessage());
			return 0;
		}
		
	}
	/*getFormData will list the form categories
	 * @params string $fetchType the fetching type either list,first or all
	 * @params array $fields contains set of fields need to be fetched acc. to requirement
	 * @params array $conditions contains set of conditions need to be apply 
	 * @returns true if save
	 * @version 5.2 
	 * @ref REDDEV-4736
	 */
	public function getFormData($fetchType = 'first',$fields=NULL,$conditions=NULL){
		try{
			return $result = $this->find($fetchType,
												array('fields'=>$fields,
											  		  'conditions'=>$conditions,
													  'order' => array('Form.label')));
			}
		catch(Exception $e){
			debug($e->getMessage());
			return 0;
		}
	}
	/**
     * searchForms will list the forms according to search query
     * @params string $searchExp the string to be search
     * @params string $selectedforms
     * @params integer $categoryId
     * @return list of array of forms
     * @version 5.2
     * @ref REDDEV 4736
     */
	function searchForms( $searchExp = false, $selectedforms = 0, $categoryId = false){
		$conditions = "";
		if((isset($searchExp)) && !empty($searchExp))
			$conditions[] = '(Form.label like "%'.$searchExp.'%")';
		if((isset($selectedforms)) && !empty($selectedforms))
			$conditions[] = "Form.id not in ($selectedforms,0)";
		if((isset($categoryId)) && !empty($categoryId))
			$conditions[] = "Form.category_id = ".$categoryId ;		
		return $this->find('list',array('fields' => array('Form.id','Form.label'),
										'conditions' => $conditions,
										'order' => array('Form.label ASC')
		                    ));
		
	}
	
	/**
	 * searchSelectedForm will list selected forms
	 * @param string $selectedform
	 * @param integer $categoryId
	 * return array of form/bolean
	 * @version 5.2
	 * @ref 4736
	 */
	function searchSelectedForm( $selectedform=false, $categoryId=false){
		$conditions = "";
		if(0 != strlen($selectedform)){
		if((isset($categoryId) && !empty($categoryId)))
		$conditions[] = "Form.category_id = ".$categoryId;
		return $this->find('list',array('fields' => array('Form.id','Form.label'),
											   		'conditions' => array("Form.id in ($selectedform,0)",$conditions),
											   		'order' => array('Form.label ASC')
		                    ));
		}else{
			return true;
		}
	}
	
	/**
	 * getFormAssociationData will list forms
	 * @param integer $type
	 * @params integer $hid
	 * return array of formassociationid and form labels
	 * @version 5.2 ,5.4
	 * @ref 4830 ,REDDEV-5535
	 */
	public function getFormAssociationData($type = NULL, $hid = NULL){
		try{	
		return $this->find('all',array('fields'=>array('DISTINCT FormAssociation.id','Form.label'),
										'joins'=> array(array('table'=>'form_associations',
												  			  'alias'=>'FormAssociation',
												  			  'type'=>'inner',
												  			 'conditions'=>array('FormAssociation.form_id = Form.id'))
														,array('table'=>'form_field_joins',/*@REDDEV-5535 list will list only those forms which ll be having fields*/
												  			  'alias'=>'FormFieldJoins',
												  			  'type'=>'inner',
												  			 'conditions'=>array('FormFieldJoins.form_id = Form.id'))
														),
										'conditions' => array("Form.category_id" => $type,"FormAssociation.hid"=> $hid ,'(Form.status = "survey_form_active" OR Form.status = "checklist_form_active" OR Form.status = "brief_form_active"  )'),
										'order' => array('Form.label ASC')));
		}catch(Exception $e){
			debug($e->getMessage());
			return array();
		}
		
	}
	/**
	 * getFormDataWithCategoryByFormId will get all active forms
	 * @param string $fetchType
	 * @param integer $formId
	 * @return Array
	 */
	public function getFormDataWithCategoryByFormId($fetchType = 'first',$formId = NULL){
		try{
			return $this->find($fetchType,array('fields'=>array('Form.label','Form.description','FormCategory.label','FormCategory.workflow_id'),
												'joins' => array(array('table'=>'form_categories',
												  			  			'alias'=>'FormCategory',
												  			  			'type'=>'inner',
												  			 			'conditions'=>array('FormCategory.id = Form.category_id'))
																),
												'conditions' => array('Form.id' =>$formId)
																));
			
		}catch(Exception $e){
			debug($e->getMessage());
			return 0;
		}
	}
	
	
public function  getFormCategoryByFormId($formId){
		try{
		$result = $this->find('first',array('fields'=>array('FormCategory.label'),
								   'joins'=>array(array('table'		=>	'form_categories',
												  'alias'		=>	'FormCategory',
												  'type'		=>	'inner',
												  'conditions'=>	array('FormCategory.id = Form.category_id'))
													),
									'conditions'=>array('Form.id' => $formId)));
													return $result['FormCategory']['label'];
		}catch(Exception $e){
			debug($e->getMessage());
			return 0;
		}
	}
	
	
	
	
}