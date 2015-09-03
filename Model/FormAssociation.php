<?php
App::uses('FormBuildersAppModel', 'FormBuilders.Model');
App::uses('Field', 'Projects.Model');
class FormAssociation extends FormBuildersAppModel
{
    var $name = 'FormAssociation';
    /** 
     * saveAssociations will save associated data for client and forms
     * @params array $forms
     * @params array $clients
     * @returns boolean
     * @version 5.2.0
     * @ref REDDEV 4736
     */
    public function saveAssociations( $forms, $clients)
    {
        $dataToSave = array();
        $countCombo = 0;
        if (isset($forms) && isset($clients)) {
            //loop for association
            foreach ($forms as $formKey => $formVal) {
                //loop for specs
                foreach ($clients as $clientsKey => $clientsVal) {
                    $isCombinationsExists = $this->isExists($clientsVal, $formVal);
                    if (0 == $isCombinationsExists) {
                        $dataToSave[$countCombo]['FormAssociation']['form_id'] = $formVal;
                        $dataToSave[$countCombo]['FormAssociation']['hid']     = $clientsVal;
                        $countCombo++;
                    }
                    
                } //loop for specs ends here
                
            } //loops for association
        }
        if (isset($dataToSave) && !empty($dataToSave)) {
            if ($this->saveAll($dataToSave)) {
                return true;
            }
        }
        return false;
    }
    /** 
     * deleteAssociations will save associated data for client and forms
     * @params array $forms
     * @params array $clients
     * @version 5.2
     * @ref REDDEV 4736
     */
    public function deleteAssociations($forms, $clients)
    {
        $dataToSave = array();
        $countCombo = 0;
        if (isset($forms) && isset($clients)) {
            //loop for association
            foreach ($forms as $formKey => $formVal) {
                //loop for specs
                foreach ($clients as $clientsKey => $clientsVal) {
                    $dataToSave['form_id'][] = $formVal;
                    $dataToSave['hid'][]     = $clientsVal;
                    $countCombo++;
                }
                
            } //loop for specs ends here
            
        }
        
        
        $conditions = array(
            "FormAssociation.form_id" => $dataToSave['form_id'],
            "FormAssociation.hid" => $dataToSave['hid']
        );
        if (!empty($conditions)) {
            if ($this->deleteAll($conditions)) {
                $message = 1; //message for data added
                return false;
            }
        }
    }
    /**
     * isExists() will check if already exists
     * @return true if exists
     * @version 5.2
     * @ref REDDEV 4736
     */
    public function isExists($clienId = false, $formId = false)
    {
        return $this->find('count', array(
            'conditions' => array(
                'form_id' => $formId,
                'hid' => $clienId
            )
        ));
    }
    /*
	 * This function is used for fetching  client details
	 * @param integer $formRequestId (Based on the $formRequestId other details would be fetched)
	 * @version 5.2
	 * @ref REDDEV-4830
	 */
	function getFormTypeAlongWithClientDetails($formAssociationId){
		return $this->find('first',array('fields'=>array('HierarchyValue.label','Form.label as formname','Form.description','Form.id','Formcategory.id','Formcategory.label as categoryname','FormAssociation.hid','Form.distribution_list','Form.is_public'),
																	'joins'=>array(
																				array(
																					'table'=>'hierarchy_values',
									        										'alias'=>'HierarchyValue',
									        										'type'=>'inner',
									        										'conditions'=>array('HierarchyValue.id=FormAssociation.hid')
																					),
																				array(
																				 	'table'=>'forms',
																					'alias'=>'Form',
																					'type'=>'inner',
																					'conditions'=>array('Form.id=FormAssociation.form_id')
																				),
																				array(
																				 	'table'=>'form_categories',
																					'alias'=>'Formcategory',
																					'type'=>'inner',
																					'conditions'=>array('Formcategory.id=Form.category_id ')
																				),
																	),
															'conditions'=>array('FormAssociation.id'=>$formAssociationId)));
	}
	/*
	 * function getFormAssociation will get associated forms with selected clients
	 * @params integer $clienHid
	 * @return Array
	 */
    function getFormAssociation($clienHid = NULL){
    	return $this->find('all',array( 'fields' => array('Form.id','Form.label','Form.status','FormAssociation.id','StatusCode.label','FormAssociation.hid','Formcategory.id'),
    									'joins' => array(
    													array(
    														'table'=>'forms',
															'alias'=>'Form',
															'type'=>'inner',
															'conditions'=>array('Form.id = FormAssociation.form_id')
																				),
														array(
															'table'=>'form_categories',
															'alias'=>'Formcategory',
															'type'=>'inner',
															'conditions'=>array('Formcategory.id = Form.category_id')
																				),
													  array(
															'table'=>'status_codes',
															'alias'=>'StatusCode',
															'type'=>'inner',
															'conditions'=>array('StatusCode.status_code = Form.status')
																				),),
									     'conditions' => array('FormAssociation.hid' => $clienHid)));
    	
    }
    /** This function will list associated clients for provided form id
     * 
     */
    function getFormAssociationData($fetchType = 'list', $fields = NULL, $conditions = NULL ){ 
    	return $this->find($fetchType,array('fields' => $fields,
    										  'conditions' => $conditions));
    	
    }
    
}
?>  