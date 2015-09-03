<?php
App::uses('FormBuildersAppController', 'FormBuilders.Controller');
/**
 * FormBuilders Controller
 *
 */
class FormAssociationsController extends FormBuildersAppController {
	var $name = 'FormAssociations';
	public $uses = array('FormBuilders.FormCategory',
						 'FormBuilders.Form',
						 'FormBuilders.FormAssociation',
						 'Campaigns.CampaignType');
	
	/**
	 * associateFormClient() will associate the forms with clients
	 * @returns false
	 * @version 5.2
	 * @ref REDDEV-4736	
	 */
	public function associateFormClient(){
		if($this->RequestHandler->isAjax()){ 
		//for listing of classification workflowtype id wise
			if(isset($this->request->data['categoryId']))
				{
					$listOfcategory = $this->Form->find('list',array('fields' => array('label'),'conditions'=>array('category_id' => $this->request->data['categoryId'])));
					echo json_encode($listOfcategory);
					$this->autoRender = false;
		    		return false;
				}
			$forms = '';
			$clients = '';
			if(isset($this->request->data['forms']) && !empty($this->request->data['forms'])){
						$forms = $this->request->data['forms'];
        	}
			if(isset($this->request->data['clients']) && !empty($this->request->data['clients'])){
						$clients = $this->request->data['clients'];
			}	
			if(!empty($forms) && !empty($clients)){		
				if($this->request->data['associate'] == 'save'){
        			$result  = $this->FormAssociation->saveAssociations($forms,$clients);
				}elseif($this->request->data['associate'] == 'delete'){ 
        			$result  = $this->FormAssociation->deleteAssociations($forms,$clients);
				}
				//if($result){/*@ref:REDDEV-5534*/
					echo 1;
					$this->autoRender = false;
			    	return false;
				//}
			}
			
			$categories = $this->FormCategory->getFormCategories('list',array('FormCategory.label'));
			$listOfForms = $this->Form->getFormData('list',array('Form.label'),'Form.status IN ("brief_form_active","survey_form_active","checklist_form_active")');
			$this->HierarchyValue->recursive = 0;  
        	$listOfClients = $this->HierarchyValue->find('list', array('fields' => array('HierarchyValue.label'),
        															   'conditions' => array('HierarchyValue.type' => 'client','HierarchyValue.status' => 'active'),
        															   'order' => 'HierarchyValue.label'));
        	
			$this->set('listOfForms', $listOfForms);	
			$this->set('categories', $categories);	
			$this->set('listOfClients', $listOfClients);
					
			
		}
	}
	/* getFormAssociation will get associated form
	*  @params integer clientId
	*  @return json encoded array
	*/
	public function getFormAssociation($clientId = Null){
		$this->autoRender=false;
		$result['campaignType'] = "";
		$result['form'] = "";
		if($clientId == 509){
			$result['campaignType'] = $this->CampaignType->findAllByClientHid($clientId);
		}
			$result['form'] = $this->FormAssociation->getFormAssociation($clientId);
			echo json_encode(array('formData'=>$result,'clientId'=>$clientId));
	}
	
}
