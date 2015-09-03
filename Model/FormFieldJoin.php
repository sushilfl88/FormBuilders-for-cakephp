 <?php
App::uses('FormBuildersAppModel', 'FormBuilders.Model');
App::uses('Field', 'Projects.Model');
class FormFieldJoin extends FormBuildersAppModel
{
    var $name = 'FormFieldJoin';
	/*
	 * 
	 */
    public function getFieldsbyHid($formId, $type = "form")
    {
        
        return $this->find('all', array(
            "fields" => array(
                'Field.id'
            ),
            "joins" => array(
                array(
                    'table' => 'forms',
                    'type' => 'INNER',
                    'alias' => 'Form',
                    'conditions' => array(
                        'FormFieldJoin.form_id = Form.id'
                    )
                ),
                array(
                    'table' => 'fields',
                    'type' => 'INNER',
                    'alias' => 'Field',
                    'conditions' => array(
                        'FormFieldJoin.field_id = Field.id'
                    )
                )
            ),
            "conditions" => array(
                "FormFieldJoin.form_id" => $formId,
                'Field.project_type' => $type
            )
        ));
        
    }
//    
	/* function updateFieldsHavingGreaterWeight() will update weight of fields
	 * @params integer $formId
	 * @params string $type
	 * @params integer  $weight
	 * @params integer $parentId
	 * @returns array 
	 * @version 5.2
	 */
    public function updateFieldsHavingGreaterWeight($formId, $type, $weight, $parentId = -1)
    {
        return $this->find('list', array(
            "fields" => array(
                'FormFieldJoin.id',
                'FormFieldJoin.weight'
            ),
            "joins" => array(
                array(
                    'table' => 'fields',
                    'type' => 'INNER',
                    'alias' => 'Field',
                    'conditions' => array(
                        'FormFieldJoin.field_id = Field.id'
                    )
                )
            ),
            "conditions" => array(
                "FormFieldJoin.form_id" => $formId,
                "Field.project_type" => $type,
                "FormFieldJoin.weight >" => $weight,
                'FormFieldJoin.parent_fieldset_id' => $parentId
            )
        ));
    }
    /* reOrderingWeight will order the weight
     * @params integer $fId
     * @params integer $type
     * @params integer $parentId
     * @version 5.2
     */
    public function reOrderingWeight($fId, $type, $parentId = -1)
    {
        $listOfWeight  = $this->updateFieldsHavingGreaterWeight($fId, $type, 0, $parentId); //returns list of fields having weight greater than zero //debug($listOfWeight);
        $countOfWeight = count($listOfWeight);
        if ($countOfWeight > 0) {
            $c = 1;
            while ($c <= $countOfWeight) {
                $arrayToCompare[] = $c * 100;
                ++$c;
            }
            
            $arrayDiff = array_diff($arrayToCompare, $listOfWeight); 
            $counter   = 100;
            if (count($arrayDiff) > 0) {
                foreach ($listOfWeight as $listOfWeightkey => $listOfWeightval) {
                    $this->id = $listOfWeightkey;
                    $this->saveField('weight', $counter);
                    $this->reOrderingAssociatedFields($listOfWeightkey);
                    $counter = $counter + 100;
                }
            }
            
            
        }
    }
    /*
     * reOrderingAssociatedFields() orders the field according to weight
     * @params integer $fieldSetId
     * @version 5.2 
     */
    public function reOrderingAssociatedFields($fieldSetId = false)
    {
        $result           = $this->findById($fieldSetId);
        $weightOfFieldset = $result['FormFieldJoin']['weight'];
        //find associated fields with fieldset
        $listOfFields     = $this->find('all', array(
            "conditions" => array(
                "FormFieldJoin.parent_fieldset_id" => $fieldSetId
            )
        ));
        if (isset($listOfFields) && !empty($listOfFields) && count($listOfFields) > 0) {
            foreach ($listOfFields as $key => $value) {
                $weightOfFieldset++;
                $this->id = $value['FormFieldJoin']['id'];
                $this->saveField('weight', $weightOfFieldset);
            }
        }
    }
    /*getMaxWeight() will get the max weight amongst associated fields
     * @params integer $formId
     * @params string $type
     * @params integer $parentFieldId
     * @returns array
     * @version 5.2
	 */
    public function getMaxWeight($formId, $type, $parentFieldId = -1)
    {
        return $this->find('first', array(
            "fields" => array(
                'MAX(FormFieldJoin.weight) as maxweight'
            ),
            "joins" => array(
                array(
                    'table' => 'fields',
                    'type' => 'INNER',
                    'alias' => 'Field',
                    'conditions' => array(
                        'FormFieldJoin.field_id = Field.id'
                    )
                )
            ),
            "conditions" => array(
                "FormFieldJoin.form_id" => $formId,
               // "Field.project_type" => $type,
                "FormFieldJoin.parent_fieldset_id" => $parentFieldId
            )
        ));
    }
    /*saveFormFieldJoin saves data
     * @params integer $formId
     * @params integer $fieldId
     * @params integer $weight
     * @version 5.2
     */
    public function saveFormFieldJoin($formId, $fieldId, $weight)
    {
        $add[] = array(
            'form_id' => $formId,
            'field_id' => $fieldId,
            'weight' => $weight
        );
        try{
        	return $this->saveAll($add);
        }catch(Exception $e){
			debug($e->getMessage());
			return 0;
		}
    }
    /*getIdByWeightAndcatId will get the id
     * @params integer $formId
     * @params integer $weight
     * @params string $type
     * @return array
     * @version 5.2
     */
    public function getIdByWeightAndcatId($formId, $weight, $type)
    {
        return $this->find('first', array(
            "fields" => array(
                'FormFieldJoin.id'
            ),
            "joins" => array(
                array(
                    'table' => 'fields',
                    'type' => 'INNER',
                    'alias' => 'Field',
                    'conditions' => array(
                        'FormFieldJoin.field_id = Field.id'
                    )
                )
            ),
            "conditions" => array(
                "FormFieldJoin.form_id" => $formId,
                "Field.project_type" => $type,
                'weight' => $weight
            )
        ));
    }
    
    /**
     * saveFieldsData()  it will  associate fields to form
     * @params array $fieldToAssociate  of fields to be associated
     * $params integer $formId form id
     * @version 5.2
     * 
     */
    public function saveFieldsData($fieldToAssociate, $formId ,$fromWhere='field')
    {
        $field= ClassRegistry::init('Projects.Field');
		foreach ($fieldToAssociate as $fieldVal) {
			if($fromWhere == 'field'){
        		$projectType = $field->getProjectType($fieldVal);
			}else{
				$projectType = 'form';
			}
            $maxWeight   = $this->getMaxWeight($formId, $projectType);
            $weight      = $maxWeight[0]['maxweight'] + 100; // to add weight value
             $this->saveFormFieldJoin($formId, $fieldVal, $weight);
        }
        return true;
    }
    
    
    
}
?> 