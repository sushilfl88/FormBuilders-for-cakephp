<?php
App::uses('FormBuildersAppModel','FormBuilders.Model');

class FormCategory extends FormBuildersAppModel {
	var $name = 'FormCategory';
	var $validate = array('distribution_list' => array(
										'rule' => array('email'),
										'message' => 'Please supply a valid email address.'
								),
						 'status'=>array(
									 			'required' => array(
									            'rule' => 'notEmpty',
									        	'required'=>true,
									        	'message'=>'You Must Select A Status'
									            // extra keys like on, required, etc. go here...
									        	)
									       )
	);
	/*getFormCategories will list the form categories
	 * @params string $fetchType the fetching type either list,first or all
	 * @params array $fields contains set of fields need to be fetched acc. to requirement
	 * @params array $conditions contains set of conditions need to be apply 
	 * @returns true if save
	 * @version 5.2 
	 * @ref REDDEV-4718,4736
	 */
	public function getFormCategories($fetchType = 'first',$fields=NULL,$conditions=NULL){
		try{
			return $result = $this->find($fetchType,
												array('fields'=>$fields,
											  		  'conditions'=>$conditions));
			}
		catch(Exception $e){
			debug($e->getMessage());
			return 0;
		}
	}
	/*getFormCategory will list the forms from categories
	 * @params string $categoryLabel the name of category
	 * @returns Category id 
	 * @version 5.2 
	 * @ref REDDEV-4830
	 */
	
	public function getFormCategory($categoryLabel){
		
		$data = $this->find('first',array('fields'=> array('FormCategory.id'),
										 'conditions' => array('FormCategory.label' => $categoryLabel)));
		
		return $data['FormCategory']['id'];
		
	
	}
	
	
}