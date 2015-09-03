<?php
require_once APP. '/Context/RedtraxContext.php';
require_once APP . '/Service/Logging/ILogService.php';
require_once APP. 'Plugin/FormBuilders/Service/FormWorkflowStatusJoin/FormWorkflowStatusJoinService.php';
require_once APP. 'Plugin/FormBuilders/Service/Form/FormService.php';
require_once APP. 'Plugin/FormBuilders/Service/FormRequest/FormRequestService.php';
require_once APP. '/Plugin/Files/Context/FilesContext.php';
require_once APP. '/Plugin/Files/Service/File/IFileService.php';
require_once APP. '/Plugin/Files/Service/File/FileService.php';


class FormBuildersContext extends RedtraxContext {
	/*Models*/
	const FORM_WORKFLOW_STATUS_JOIN_MODEL = "formWorkflowStatusJoinModel";
	const WORKFLOW_MODEL = "workflowModel";
	const STATUSCODE_MODEL = "statusCodeModel";
	const STATE_ENGINE_MODEL = "stateEngineModel";
	const FORM_MODEL = "formModel";
	const FORM_CATEGORY_MODEL = "formCategoryModel";
	const RATE_CARD_PRICE_MODEL = "rateCardPriceModel";
	const FORM_REQUEST_MODEL = "formRequestModel";
	const FORM_FIELD_JOIN_MODEL = "formFieldJoinModel";
	const FIELD_MODEL = "fieldModel";
	const ACTION_MODEL = "actionModel";
	const CONFIGURATION_SETTING_MODEL = "configurationSettingModel";
	const USER_MODEL = "userModel";
	const FIELD_VALUE_MODEL = "fieldValueModel";
	const FILE_MODEL = "fileModel";
	const FORM_PROJECT_ITEM_JOIN_MODEL = "formProjectItemJoinModel";
	/*Services*/

	const FORM_WORKFLOW_STATUS_JOIN_SETTING_SERVICE = "formWorkflowStatusJoinService";
	const FORM_SERVICE = "formService";
	const FORM_REQUEST_SERVICE = "formRequestService";
	const FILE_SERVICE = "fileService";
	static $instance;
	
	public static function getInstance () {
		if(self::$instance == null) {
			self::$instance = new FormBuildersContext();
		}
		
		return self::$instance;
	}
	/**
	 * This method Calls registerDefaults of all the Contexts that this module
	 * is dependant on.
	 * For ex: The CostSpecsContext needs the deliverablesService from the
	 * DeliverablesContext. So, we're letting the DeliverablesContext worry 
	 * about initialising the service for us.
	 */
	public function registerDependentContextDefaults () {
		
		FilesContext::getInstance()->registerDefaults();
	}
	/* set default models, services and all */
	public function registerDefaults() {
		
		parent::registerDefaults();
		$this->registerDependentContextDefaults();
		
		//pr(FilesContext::getInstance());die;
		$logger = $this->get(self::LOG_SERVICE);
		$fileService = $this->get(FilesContext::FILE_SERVICE);
		
		
		$workflowModel = $this->register(self::WORKFLOW_MODEL, ClassRegistry::init('Workflow.Workflow'));
		$statusCodeModel = $this->register(self::STATUSCODE_MODEL, ClassRegistry::init('Workflow.StatusCode'));
		$stateEngineModel = $this->register(self::STATE_ENGINE_MODEL, ClassRegistry::init('Workflow.StateEngine'));
		$formRequsetModel = $this->register(self::FORM_REQUEST_MODEL, ClassRegistry::init('FormBuilders.FormRequest'));
		$formFieldJoinModel = $this->register(self::FORM_FIELD_JOIN_MODEL, ClassRegistry::init('FormBuilders.FormFieldJoin'));
		$actionModel = $this->register(self::ACTION_MODEL, ClassRegistry::init('AccessControls.Action'));
		$configurationSettingModel = $this->register(self::CONFIGURATION_SETTING_MODEL, ClassRegistry::init('AccessControls.ConfigurationSetting'));
		$fieldModel = $this->register(self::FIELD_MODEL, ClassRegistry::init('Projects.Field'));
		$formModel = $this->register(self::FORM_MODEL, ClassRegistry::init('FormBuilders.Form'));
		$formCategoryModel = $this->register(self::FORM_CATEGORY_MODEL, ClassRegistry::init('FormBuilders.FormCategory'));
		$rateCardPriceModel = $this->register(self::RATE_CARD_PRICE_MODEL, ClassRegistry::init('Deliverables.RateCardPrice'));
		$userModel = $this->register(self::USER_MODEL,ClassRegistry::init('AccessControls.User'));
		$fieldValueModel = $this->register(self::FIELD_VALUE_MODEL,ClassRegistry::init('Projects.FieldValue'));
		$fileModel = $this->register(self::FILE_MODEL, ClassRegistry::init('Files.File'));
		$formProjectItemJoinModel = $this->register(self::FORM_PROJECT_ITEM_JOIN_MODEL, ClassRegistry::init('FormBuilders.FormProjectItemJoin'));
		$this->register(self::FORM_WORKFLOW_STATUS_JOIN_MODEL, ClassRegistry::init('FormBuilders.FormWorkflowStatusJoin'));
		$model = $this->get(self::FORM_WORKFLOW_STATUS_JOIN_MODEL);
		$this->register(self::FORM_WORKFLOW_STATUS_JOIN_MODEL, new FormWorkflowStatusJoinService($model, $workflowModel,$statusCodeModel,$stateEngineModel,$formModel,$formCategoryModel,$rateCardPriceModel,  $logger));
		$this->register(self::FORM_MODEL, new FormService($formModel, $logger));
		$this->register(self::FORM_REQUEST_MODEL, new FormRequestService($formRequsetModel, $formFieldJoinModel, $fieldModel, $formModel, $actionModel, $configurationSettingModel, $userModel, $fieldValueModel, $stateEngineModel, $fileModel, $formProjectItemJoinModel, $logger, $fileService));

	}
	
	
	
}
?>