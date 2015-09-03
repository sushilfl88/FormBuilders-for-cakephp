<?php
	require_once APP. '/Service/Logging/ILogService.php';
	require_once APP. 'Plugin/FormBuilders/Service/Form/IFormService.php';
	class FormService implements IFormService {
	private $formModel;
	private $logger;
	
	
	public function __construct(
			Form $formModel,
			ILogService $logger) {  $this->formModel = $formModel;
									$this->logger = $logger;
								}
	
	}
	?>
