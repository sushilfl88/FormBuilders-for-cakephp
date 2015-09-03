<?php
App::uses('FormBuildersAppModel','FormBuilders.Model');

class FormRequest extends FormBuildersAppModel {
	var $name = 'FormRequest';
	
	/*This function is used to list all submitted forms
	 * @params string $categoryNames
	 * @params integer $formRequestIds
	 * @version 5.2
	 * @ref REDDEV-4830
	 * */

	public function getFormRequest($categoryNames = null, $formRequestIds = null,$hids = null,$userId = null,$showTab = 'all'){

		$conditions = '';
		$joinIfMy = '';
		if(!empty($categoryNames)){
		
			$conditions = 'Formcategory.label IN ('.$categoryNames.')';
			
		
		}
		if(!empty($formRequestIds)){
			$conditions.= 'AND FormRequest.id IN ('.$formRequestIds.')'; 
		}
		if(!empty($hids)){
		$conditions.= 'AND HierarchyValue.id IN ('.$hids.')';
		}
		/**
		 * Based on this array Fiels/column will be retirived
		 */
		$fields = array('DISTINCT FormRequest.id','date_format(FormRequest.submitted_date, \'%Y-%m-%d-%h-%i-%s %p\' ) AS submitted_date','HierarchyValue.label','HierarchyValue.id','Form.label as formname','FormRequest.status','FormRequest.label','User.given_name','User.surname','FormAssociation.id','FormAssociation.form_id','Formcategory.id','Formcategory.label as categoryname','FormRequest.form_associations_id','StatusCode.label as statuslabel','Form.is_public');
			
		/**
		 * Based on this array table to use for retriving the data from database are defined
		 */
		/* sorting for my and all */
		if($showTab == 'my'){
						$joinIfMy = array(
								'table'	=> 'form_assignments',
						        'alias'	=> 'FormAssignment',
				        		'type'	=> 'inner',
						        'conditions' => array("FormAssignment.form_request_id = FormRequest.id  and FormAssignment.user_id = $userId"));
						//$conditions.= 'AND FormAssignment.form_request_id IS NOT NULL AND Form.is_public = 0';
						$conditions.= 'AND FormAssignment.form_request_id IS NOT NULL';
			}else{
				$conditions.= ' AND Form.is_public = 1';
			}
			/*ends here*/
		$joins = array(
							array(
							 	'table'=>'form_associations',
								'alias'=>'FormAssociation',
								'type'=>'inner',
								'conditions'=>array('FormAssociation.id = FormRequest.form_associations_id')
							),
							array(
							 	'table'=>'hierarchy_values',
								'alias'=>'HierarchyValue',
								'type'=>'inner',
								'conditions'=>array('HierarchyValue.type'=>'client','HierarchyValue.id=FormAssociation.hid')
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
							array(
							 	'table'=>'users',
								'alias'=>'User',
								'type'=>'inner',
								'conditions'=>array('User.id=FormRequest.requested_by_id')
							),
							$joinIfMy,
							array(
							 	'table'=>'status_codes',
								'alias'=>'StatusCode',
								'type'=>'inner',
								'conditions'=>array('StatusCode.status_code=FormRequest.status')
							)


					);
		try{
		return $this->find('all',array('fields'=>$fields,'conditions'=> $conditions,'joins'=>$joins,'order'=>array('FormRequest.id DESC')));
		}catch(exception $e){
		debug($e);exit;
		}
		//return $this->find('all');

	}
	
	/*This function is used to return client id
	 * @params integer $formId
	 * @returns integer
	 * @version 5.2
	 * @ref REDDEV-4830
	 * */
	
function getClientHidByFormId($formId){
		$data=$this->find('first',
							 array( 'fields' => array('FormAssociation.hid'),
							 'joins'=>array(
											array(
											 	'table'=>'form_associations',
												'alias'=>'FormAssociation',
												'type'=>'inner',
												'conditions'=>array('FormAssociation.id=FormRequest.form_associations_id')
											),
															),
							'conditions'=> array('FormRequest.id' => $formId)));
		return $data['FormAssociation']['hid'];
	}
/*
	 * getCampaignRequestData() is used for fetching data for corresponding camaign id
	 * @param integer $crId
	 * @param integer $campaignTypeId
	 * Based on Passed $userId the campaign request will be retrieved.
	 */
	public function getFormRequestData($frId='NULL'){
		$conditions = 'FormRequest.id ='.$frId;

			$joins=array( array(
								'table'=>'status_codes',
	        					'alias'=>'StatusCode',
	        					'type'=>'left',
	        					'conditions'=>array('StatusCode.status_code = FormRequest.status')
							 )
						);
		return $this->find('first',array('fields'=>array('*'),'joins'=>$joins,'conditions'=>array($conditions)));	
		
	}
	/*
     * sendEmailForActiveStatusBriefs will send email
     * */
	public function sendEmailForActiveStatusBriefs($crData, $previous_state, $distributionList, $loggedInUserEmail)
    {
         /*opening the mail functionality for forms @ref-REDDEV-5506*/
 		//if ($crData['FormRequest']['status'] == 'brief_active' || $crData['FormRequest']['status'] == 'brief_submitted'  ) {
            $getDetailsForMail = $this->getDetailsForMailBrief($crData, $previous_state);
            if(isset($distributionList)){
						$emailArray = explode(',',$distributionList);//for comma separated value
						array_push($emailArray ,$loggedInUserEmail);
	 		}	
            $email = ClassRegistry::init('Projects.Email');	
            $result  = $email->send($emailArray, $crData['FormRequest']['id'], $getDetailsForMail['email_id'], 'form_request');
           
       // }
        return $result;
    }
/*
	 * Fetches distribution_list and email_message_id 
	 * depending on campaign_type_id and status codes 
	 * repectively
	 *
	 * @param unknown $crData holds the data of the campaign just saved
	 * @param unknown $previous_state holds the previous state of the campaign request
	 */
	
	function getDetailsForMailBrief($crData,$previous_state){
		//$return['distribution_list'] = $distributionList;
		$stateEngine = ClassRegistry::init('Workflow.StateEngine');	
		$stateEngineOption = array('fields'=>array('StateEngine.email_message_id'), 'conditions'=>array('StateEngine.cur_status_code'=>$previous_state,'StateEngine.next_status_code'=>$crData['FormRequest']['status']));
		$stateEngineData = $stateEngine->find('first',$stateEngineOption);
		if(isset($stateEngineData['StateEngine']['email_message_id'])){
			$return['email_id'] = $stateEngineData['StateEngine']['email_message_id'];
		}else{
			$return['email_id'] = false;
		}
		return $return;
	}
	
	public function getFRDistributionUserList($formRequrestId)
	{
		$data = $this->find('first',
							 array( 'fields' => array('Form.distribution_list,(select concat(User.email,"#",group_concat(User.given_name," ", User.surname)) from users as User where User.id=FormRequest.created_by) as created_by ,(select concat(User.email,"#",group_concat(User.given_name," ", User.surname)) from users as User where User.id=FormRequest.submitted_by) as submitted_by'),
							 'joins'=>array(
											array(
											 	'table'=>'form_associations',
												'alias'=>'FormAssociation',
												'type'=>'inner',
												'conditions'=>array('FormAssociation.id=FormRequest.form_associations_id')
											),
											array(
											 	'table'=>'forms',
												'alias'=>'Form',
												'type'=>'inner',
												'conditions'=>array('Form.id=FormAssociation.form_id')
											),
															),
							'conditions'=> array('FormRequest.id' => $formRequrestId)));
		return $data;
	}
	/**
	 * saveFormRequestData() will save data
	 * @param Array $data contais $data for saving
	 * @param Array $loggedInUserArray contains currenet loggedin  user array
	 * @param integer $frId contains form request id
	 * @ref REDDEV-5127
	 * @version 5.3
	 */
	public function saveFormRequestData($data, $loggedInUserArray, $frId = NULL){
				$dataToSave = array();
				$dataToSave = $data;
		 		$LoggedInUserId        = $loggedInUserArray['User']['id'];
                $dataToSave['requested_by_id']      = $LoggedInUserId;
                $dataToSave['submitted_date']       = date('Y-m-d H:i:s');
                $dataToSave['submitted_by']      = $LoggedInUserId;
                $dataToSave['last_modified_by']      = $LoggedInUserId;
                try{
		                if($frId == NULL){
		                	 $dataToSave['created_by']      = $LoggedInUserId;
		                	 $FormRequestSavedData = $this->save($dataToSave);
		                     $FormRequestId        = $this->getInsertID();
		                }else{
		                	 $dataToSave['id']      = $frId;
		                	 $FormRequestSavedData = $this->save($dataToSave);
		                     $FormRequestId        = $frId;
		                }
		                return $FormRequestSavedData;
                }catch(Exception $e){
                		debug($e);exit;
                }
	}
	/**
	 * 
	 * saveStatus will save form request status
	 * @param integer $frId
	 * @param string $status
	 * @return boolean
	 * @ref REDDEV-5127
	 * @version 5.3
	 */
	 public function saveStatus($frId = NULL, $status = NULL){
	 	$data = array('id' => $frId , 'status' => $status );
		try{
		return $this->save($data);
		}catch(Exception $e){
			
			debug($e);
			return false;
		}
	 }
	 /**
 	 * 
 	 */
	 public function getallBriefData($clientListStr,$userId){
	 	$getAllBriefData = array();
	 	$getmyBriefData= array();
	 	$getAllBriefData = $this->getFormRequest("'Brief'",NULL,$clientListStr,$userId,'all'); //debug()
	 	$getmyBriefData = $this->getFormRequest("'Brief'",NULL,$clientListStr,$userId,'my');// debug($getmyBriefData);
	 	if(isset($getAllBriefData) && isset($getmyBriefData)){
	 		$result = array_merge_recursive($getAllBriefData,$getmyBriefData);
	 	}
	 	return $result;
	 }	
}
