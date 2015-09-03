<?php
interface IFormRequestService {
	/**
	 * ViewRequestedForm
	 * @params Array $loggedInUserArray
	 * @params integer $formId
	 * @returns Array
	 * @ref REDDEV-5127
	 * @version 5.3
	 */
	public function ViewRequestedForm($loggedInUserArray, $formId);
	/**
	 * __processFieldData will convert given array into desired form of array required for rendering of form
	 * @params Array $fieldsData
	 * @return Array
	 * @ref REDDEV-5127
	 * @version 5.3 
	 * 
	 */	
	public function __processFieldData($fieldsData);
	/**
	 * save will save the form request data into the table
	 * @param Array $data
	 * @params Array $loggedInUserArray
	 * @params Array $filesData
	 * @return boolean
	 * @ref REDDEV-5127
	 * @version 5.3
	 */
	public function save($data, $loggedInUserArray,$filesData);
	/**
	 * saveFileData() will save filedata into fieldValue and file model 
	 * @param Array $fileData
	 * @param integer $formRequestId
	 * @param integer $LoggedInUserId
	 * @return boolean
	 * @ref REDDEV-5127
	 * @version 5.3 
	 */
	//public function  saveFileData($fileData, $formRequestId, $LoggedInUserId);
	/**
	 * saveRejectionNoteService will save the rejection note
	 * @param array $data
	 * @return boolean
	 * @ref REDDEV-5127
	 * @version 5.3 
	 */
	public function saveRejectionNoteService($data);
	
}
?>