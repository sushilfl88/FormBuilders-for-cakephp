<?php
	require_once APP. '/Service/Logging/ILogService.php';
	require_once APP. '/Plugin/FormBuilders/Service/FormDefaultTeam/IFormDefaultTeam.php';
	class FormDefaultTeam implements IFormDefaultTeam {
	
	private $formTemplateAssignment;
	private $formRequestAssignment;
	private $formRequest;
	private $logicalRole;
	private $logicalRolesActionsJoin;
	private $emailMessage;
	private $email;
	private $user;
	private $hidUserRoleJoin;
	private $campaignRequest;
	private $logger;
	private $queryVirtualFields=array('primary_action_id','assign_action_id','all_actions','label','hide');
	
	public function __construct(
			FormTemplateAssignment $formTemplateAssignment,
			FormAssignment $formRequestAssignment,
			FormRequest $formRequest,
			LogicalRole $logicalRole,
			LogicalRolesActionsJoin $logicalRolesActionsJoin,
			EmailMessage $emailMessage,
			Email $email,
			User $user,
			HidUserRoleJoin $hidUserRoleJoin,
			CampaignRequest $campaignRequest,
			ILogService $logger) {  
				$this->formTemplateAssignment = $formTemplateAssignment;
				$this->formRequestAssignment = $formRequestAssignment;
				$this->formRequest = $formRequest;
				$this->logicalRole = $logicalRole;
				$this->logicalRolesActionsJoin = $logicalRolesActionsJoin;
				$this->emailMessage = $emailMessage;
				$this->email = $email;
				$this->user = $user;
				$this->hidUserRoleJoin = $hidUserRoleJoin;
				$this->campaignRequest = $campaignRequest;
				$this->logger = $logger;
			}
	
			public function getRelatedModel($isDefaultTeam=false)
			{
				$model=array();
				
				if($isDefaultTeam)
				$mdoel=$this->formTemplateAssignment;
				else 
				$mdoel=$this->formRequestAssignment;
				
				return $mdoel;
			}
			
			public function getAllShareRelatedActions($roles,$isPrimary=false)
			{
				
				$logicalRolesActions=array();
				$primaryCondition='';
				$groupBy=array();
				$type="all";
				if(isset($roles) && !empty($roles))
				{
				
				$conditions[]=array('label'=>$roles);
				
				if($isPrimary)
				{
				$this->logicalRole->virtualFields[$this->queryVirtualFields[0]]='LogicalRolesActionsJoin.action_id';
				$conditions[]=array('LogicalRolesActionsJoin.is_primary'=>1);
				}
				else 
				{
					
				$groupBy='LogicalRole.label';
				$this->logicalRole->virtualFields[$this->queryVirtualFields[0]]='group_concat(LogicalRolesActionsJoin.action_id)';
				}
				
				
				if(!is_array($roles))
				$type="first";
				
				
					
				$joins[] = array (
								'table'		=>	'logical_roles_actions_join',
								'alias'		=>	'LogicalRolesActionsJoin',
								'type'		=>	'inner',
								'conditions'=>	array('LogicalRolesActionsJoin.logical_role_id=LogicalRole.id')
								);
				
				//$this->LogicalRole->virtualFields = array("$virtualFields[0]" => 'LogicalRolesActionsJoin.action_id');
				
				$logicalRolesActions = $this->logicalRole->find($type,
						array('fields'=>array('LogicalRole.*'),'joins'		=>	$joins,
								'conditions'=>	$conditions,
								'group'=>$groupBy));
				}
				$this->unsetVirtualFields($this->logicalRole);
				
				//pr($logicalRolesActions);die('ggg');
				
				return $logicalRolesActions;
			}
			
			 public function getForRequestIds($formId=null,$requestId=null)
			 {
	 				$condition=array();
	 				$fields=array('id');
	 				if(!empty($formId))
					$condition['form_id']=$formId;
					
					if(!empty($requestId))
					$condition['id']=$requestId;
					
					
					return $this->formRequest->find('all',array('conditions'=>$condition,'fields'=>$fields));
					
	 		} 
	 		
			/**
			 * 
			 * Enter description here ...
			 * @param boolean $isDefaultTeam
			 */
			public function applyJoinsForFindingMemberTeamWise($requestedId,$userId = NULL,$isDefaultTeam = false, $identificationFlag = false){
					
					$return = array();
					$formAssignmentData = array();
					if(!empty($requestedId)){
					if($isDefaultTeam){
						$joinsForAssignment[] = array (
										'table'		=>	'form_template_assignments',
										'alias'		=>	'FormTemplateAssignment',
										'type'		=>	'left',
										'conditions'=>	array('User.id=FormTemplateAssignment.user_id')
										);
						if($identificationFlag)
						$conditions[]=array('FormTemplateAssignment.campaign_type_id'=>$requestedId);
						else				
						$conditions[]=array('FormTemplateAssignment.form_id'=>$requestedId);
						
						if(!empty($userId))
						$conditions[]=array('FormTemplateAssignment.user_id'=>$userId);
						
						//$this->user->virtualFields = array($this->virtualFields[1]=> 'group_concat(FormTemplateAssignment.action_id)','hide'=>'group_concat(FormTemplateAssignment.is_hide)');
						$this->user->virtualFields[$this->queryVirtualFields[1]]='group_concat(FormTemplateAssignment.action_id)';
						$this->user->virtualFields[$this->queryVirtualFields[4]]='group_concat(FormTemplateAssignment.is_hide)';
						$formAssignmentData = $this->user->find('all',array('fields'=>array('User.*'),'joins'=>	$joinsForAssignment,'conditions'=>	$conditions,'group'=>array('FormTemplateAssignment.user_id','FormTemplateAssignment.form_id')));
						
						$this->unsetVirtualFields($this->user);

					}else{
						$joinsForAssignment[] = array (
										'table'		=>	'form_assignments',
										'alias'		=>	'FormAssignment',
										'type'		=>	'left',
										'conditions'=>	array('User.id=FormAssignment.user_id')
										);
						if($identificationFlag)
						$conditions[]=array('FormAssignment.campaign_request_id'=>$requestedId);
						else				
						$conditions[]=array('FormAssignment.form_request_id'=>$requestedId);
						
						if(!empty($userId))
						$conditions[]=array('FormAssignment.user_id'=>$userId);
						
						//$this->user->virtualFields = array($this->virtualFields[1]=> 'group_concat(FormAssignment.action_id)','hide'=>'group_concat(FormAssignment.is_hide)');
						$this->user->virtualFields[$this->queryVirtualFields[1]]='group_concat(FormAssignment.action_id)';
						$this->user->virtualFields[$this->queryVirtualFields[4]]='group_concat(FormAssignment.is_hide)';
						
					    $formAssignmentData = $this->user->find('all',array('fields'=>array('User.*'),'joins'=>	$joinsForAssignment,'conditions'=>	$conditions,'group'=>array('FormAssignment.user_id','FormAssignment.form_request_id')));
					    
						$this->unsetVirtualFields($this->user);
					}
					$return= $formAssignmentData;
					}
					
					return $return;
			}
	 		
			private function sendEmailForAssignment($formId, $userId, $roleType){
					//get email for assigment
					//try {
						$emailMessageData = $this->emailMessage->findByLabel('Form Assignment');
						$getUserData = $this->user->findById($userId);
						$emailId = $getUserData['User']['email'];
						$this->email->send($emailId, $formId, $emailMessageData['EmailMessage']['id'], 'form_request');
					/*} 
					catch (Exception $e) {
						error_log($e->getMessage());
						error_log(print_r($e,true));
					}*/
				}
	
			public function addTeamMembers($actions,$userIds,$formId,$roleType=null,$isCampaign=false,$isDefaultTeam=false,$isHide=null)
			{
				try {
								if(!empty($actions) && isset($actions))
									{
										$model=$this->getRelatedModel($isDefaultTeam);
										
										foreach($userIds as $valueUserId)
										{	
											
												$assignData=$this->buildSaveData($formId,$isCampaign,$isDefaultTeam);
												
												if(isset($assignData['assign']) && !empty($assignData['assign']))
												$dataFormAssignment=$assignData['assign'];
												
												if(isset($assignData['hidForHURJ']) && !empty($assignData['hidForHURJ']))
												$hidForHURJ=$assignData['hidForHURJ'];
												
												
												if($this->user->isClientUser($valueUserId) == 1){
													$clientRoleData = $this->logicalRole->findByLabel('Client User');
													$roleIdForHURJ = $clientRoleData['LogicalRole']['id'];
												}											
												else{
													$corporateRoleData = $this->logicalRole->findByLabel('HID Access');
													$roleIdForHURJ = $corporateRoleData['LogicalRole']['id'];
												}
											foreach($actions as $key=>$value)
											{
												$dataFormAssignment[$model->name]['user_id']=$valueUserId;
												
												if(!empty($isHide[$key]) && isset($isHide[$key]))
												$dataFormAssignment[$model->name]['is_hide']=$isHide[$key];
												
																								
												if(is_array($value))
												$action_id=$value['LogicalRolesActionsJoin']['action_id'];
												else 
												$action_id=$value;
												
												$dataFormAssignment[$model->name]['action_id']=$action_id;
												$checkDuplicate=$model->find('count',array('conditions'=>$dataFormAssignment[$model->name]));
												if($checkDuplicate==0)
												{
													$model->create();
													$model->save($dataFormAssignment);
												}
											}
											
											if(!$isDefaultTeam)
											$saveToHURJ = $this->hidUserRoleJoin->addClientAccessToUser( $roleIdForHURJ, $valueUserId, $hidForHURJ);
											
											if(!$isDefaultTeam)
											$sendMail = $this->sendEmailForAssignment($formId, $valueUserId, $roleType);
										}
									}
				}catch (Exception $e) {
								throw $e;
							}
			}
			
			private function buildSaveData($formId,$isCampaign=false,$isDefaultTeam=false)
			{
				$returnData=array();
				$model=$this->getRelatedModel($isDefaultTeam);
		
				if($isDefaultTeam)
				{
					if($isCampaign){
					$returnData['assign'][$model->name]['campaign_type_id']=$formId;
					}else{
					$returnData['assign'][$model->name]['form_id']=$formId;
					}
				}
				else {
					if($isCampaign){
					$returnData['assign'][$model->name]['campaign_request_id']=$formId;
					$hidData = $this->campaignRequest->findById($formId);
					$returnData['hidForHURJ'] = $hidData['CampaignRequest']['client_hid'];
					}else{
					$returnData['assign'][$model->name]['form_request_id']=$formId;
					$hidData = $this->formRequest->findById($formId);
					$returnData['hidForHURJ'] = $hidData['FormRequest']['hid'];
					}
				}
				
				return $returnData;
				
			}
			
			private function unsetVirtualFields($modelObj)
			{
					foreach ($this->queryVirtualFields as $var) {
						if(isset($modelObj->virtualFields[$var]) && !empty($modelObj->virtualFields[$var]))
						{
							unset($modelObj->virtualFields[$var]);	
						}
 
					}
			}
			
			public function getRequestedIdData($requestedId,$identificationFlag=false)
			{
				$formRequestData=array();
				if(isset($requestedId) && !empty($requestedId))
				{
					if($identificationFlag)
						{
							$conditions=array('CampaignRequest.id'=>$requestedId);
							$formRequestData = $this->campaignRequest->find('first',array('fields'=>array('CampaignRequest.campaign_type_id,CampaignRequest.id,CampaignRequest.label'),'conditions'=>	$conditions));
						}
						else
						{
							$joinsForAssociation[] = array (
											'table'		=>	'form_associations',
											'alias'		=>	'FormAssociation',
											'type'		=>	'left',
											'conditions'=>	array('FormRequest.form_associations_id=FormAssociation.id')
											);
							$conditions=array('FormRequest.id'=>$requestedId);
							$formRequestData = $this->formRequest->find('first',array('fields'=>array('FormAssociation.form_id,FormRequest.id,FormRequest.label,FormRequest.requested_by_id'),'joins'=>	$joinsForAssociation,'conditions'=>	$conditions));
						}
				}
				
				return $formRequestData;
			}
			
			
			public function buildTransactionValue($formAssignmentData,$logicalRoleData,$requestedData=null,$isDefaultTeam=false)
			{
				$isHide=false;
				$searchActionId='';
				$getAllRolesDetails=array();
				$logicalRoleLabel='';
				
				if(!empty($formAssignmentData) && !empty($logicalRoleData))// && !empty($formRequestData)
					{
						
							foreach($formAssignmentData as $assigenmentKey=>$assigenmentValue)
							{
								if(!empty($assigenmentValue['User'][$this->queryVirtualFields[1]]))
								{
									foreach($logicalRoleData as $key=>$value)
									{
										if(isset($value['LogicalRole']) && !empty($value['LogicalRole']))
										{
											if(!empty($value['LogicalRole'][$this->queryVirtualFields[0]]))
											{
												$searchActionId=$value['LogicalRole'][$this->queryVirtualFields[0]];
												$logicalRoleLabel=$value['LogicalRole']['label'];
											}
										}
										else 
										{
											if(!empty($value[$this->queryVirtualFields[0]]))
											{
												$searchActionId=$value[$this->queryVirtualFields[0]];
												$logicalRoleLabel=$value['label'];
											}
										}

										$actionIds=explode(',',$assigenmentValue['User'][$this->queryVirtualFields[1]]);
										if(isset($assigenmentValue['User']['hide']))
										$hide=explode(',',$assigenmentValue['User']['hide']);
													
										if(in_array($searchActionId,$actionIds))
										{
											if(isset($hide) && !empty($hide))
											$isHide=$hide[array_search($searchActionId, $actionIds)];
											
											
											
											$buildTeamData=array("name"=>$assigenmentValue['User']['given_name']." ".$assigenmentValue['User']['surname'],
																													'userId'=>$assigenmentValue['User']['id'],
																													'actionId'=>$searchActionId,
																													'given_name' => $assigenmentValue['User']['given_name'],
																													'surname' => $assigenmentValue['User']['surname'],
																													'email' => $assigenmentValue['User']['email'],
																													'isHide'=>$isHide
																													);
																									
											if((isset($requestedData['FormRequest']) && $requestedData['FormRequest']['requested_by_id'] == $assigenmentValue['User']['id'] && !$isDefaultTeam))// &&($requestedData['FormRequest']['requested_by_id']!=$user_id)
											$buildTeamData['deleteCreator']=true;
											
											
											if(isset($requestedData['FormRequest']) && 	($requestedData['FormRequest']['requested_by_id']==$assigenmentValue['User']['id']))//($formRequestData['FormRequest']['requested_by_id']!=$user_id)
													$buildTeamData['teamTransaction']=false;
												else 
													$buildTeamData['teamTransaction']=true;
													
											if($isDefaultTeam){
											$getAllRolesDetails[str_replace(" ","_",$logicalRoleLabel)][]=$buildTeamData;
											}else{
											$getAllRolesDetails[$logicalRoleLabel][]=$buildTeamData;
											}		
														
											break;
										}
										
									}
								}
							}
					}
					return $getAllRolesDetails;
			} 
	
			
			public function getMembersOfRequestedIdByRole($section, $requestedId, $rolesArr,$identificationFlag = false){
					$getAllRolesDetails = array();
					$roles=$rolesArr;
					$userEmails=array();
				
					if(!empty($roles))
					$logicalRoleData=$this->getAllShareRelatedActions($roles,true);

					if(isset($requestedId))
					/*apply joins according to team*/
					$formAssignmentData = $this->applyJoinsForFindingMemberTeamWise($requestedId,NULL,false, $identificationFlag);
					/*ends here*/
					$getAllRolesDetails=$this->buildTransactionValue($formAssignmentData,$logicalRoleData);
					if(isset($getAllRolesDetails) && !empty($getAllRolesDetails))
					{
						foreach($roles as $key=>$value)
						{
							if(isset($getAllRolesDetails[$value]) && !empty($getAllRolesDetails[$value]))
							{
								foreach($getAllRolesDetails[$value] as $key=>$value){
									$userEmails[]=$value['email'];
								}
							}
						}
					}
					
					return $userEmails;
			}

	}
	?>