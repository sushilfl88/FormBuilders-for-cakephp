<?php
interface IFormDefaultTeam {
	public function addTeamMembers($actions,$userIds,$formId,$roleType,$isCampaign=false,$isDefaultTeam=false);
	public function getRelatedModel($isDefaultTeam=false);
	public function getAllShareRelatedActions($roleType);
	public function getForRequestIds($formId=null,$requestId=null);
	public function applyJoinsForFindingMemberTeamWise($requestedId,$userId = NULL,$isDefaultTeam = false, $identificationFlag = false);

	public function getRequestedIdData($requestedId,$identificationFlag=false);

	public function getMembersOfRequestedIdByRole($section, $requestedId, $rolesArr ,$identificationFlag);

	//public function getAllShareRelatedActions($roleType);
	
}
?>