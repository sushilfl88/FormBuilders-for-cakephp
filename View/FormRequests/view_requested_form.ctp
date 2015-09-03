<script src="/files/js/vendor/jquery.ui.widget.js"></script>
<script src="/files/js/jquery.iframe-transport.js"></script>
<script src="/files/js/jquery.fileupload.js"></script>
<link href="/css/ColVisAlt.css" rel="stylesheet">
<link href="/css/jquery.dialog2.css" rel="stylesheet">
<link href="/css/redtrax.css" rel="stylesheet">
<link href="/css/bootstrap-responsive.css" rel="stylesheet">
<?php  
 $formReqId ="";
	if(isset($formRequestId)&& $formRequestId!=""){
		$formReqId = $formRequestId;
	}
?>
<script type="text/javascript">
//ADDED START
window.onunload = refreshParent;
function refreshParent() {
    window.opener.location.reload();
    self.close();
}
//ADDED END
$('.formrequest-file').live('click', function () {
	
	
	var formRequsetId	= $("#formRequestId").val();
	 $.ajax({
          type: "POST",
          url:"/form_builders/form_requests/remove/",
          data:{'fileId':$(this).attr('id'),'formRequestId':formRequsetId,'path':$(this).attr('path')},
	 });  	
	$('#delete-'+$(this).attr('id')).animate({
			opacity: 'hide',
			height: 'hide',
			marginBottom: '0px'
	}, 'fast', function () {
		$('#delete-'+$(this).attr('id')).remove();
	});
	return false;
});

function deleteAttachmentAjax(id,uniqeId) { 
	
	if(confirm("Do you really wish to Delete attachment?")) {
		$.post("/projects/fieldValues/deleteAttachedFiles/"+id+"/0", function(data) {
			$("#file_attach_"+ uniqeId).remove();
		});
	}
}
$(document).ready(function() {
	$("#FormProjectItemJoinStateEngineId").val(window.opener.document.getElementById('stateEngineId').value);
	$("#FormProjectItemJoinProjectId").val(window.opener.document.getElementById('projectId').value);
	$("#FormProjectItemJoinProjectItemId").val(window.opener.document.getElementById('projectItemId').value);
	$(".nextStatuses").click(function (){
		$("#statusFormRequest").html("");
		$("#statusFormRequest").html("<input type='hidden' name='data[FormRequest][status]' value='"+$(this).attr('id')+"' >");
		 validateFormRequest();
	});
	
	
	
	$("#approveCampaignRequest").click(function (){
		$("#statusFormRequest").html("");
		$("#statusFormRequest").html("<input type='hidden' name='data[FormRequest][status]' value='brief_active'>");
		validateFormRequest();
		
		
	});
	$("#submitRequest").click(function (){
		$("#statusFormRequest").html("");
		$("#statusFormRequest").html("<input type='hidden' name='data[FormRequest][status]' value='brief_submitted'>");
		     	
		validateFormRequest();
		
		
	});
	
	$("#saveFormRequest").click(function (){
		var jformType = '<?php echo $formType; ?>';
		
		if(jformType == 'Brief'){
			$("#statusFormRequest").html("");
			$("#statusFormRequest").html("<input type='hidden' name='data[FormRequest][status]' value='brief_draft'>");
		}else if($("#formRequestId").text() != ""){
			$("#statusFormRequest").html("");
		}
		
		$("#saveFormRequestStatus").html("");
		$("#saveFormRequestStatus").html("<input type='hidden' name='data[FormRequest][savestatus]' value='1'>");
				
		 var complete_form_request=$("#formRequestName").val(); 
		if($('#formRequestStatusLabel').html()){
			if(jformType == 'Brief'){
			 if($.trim($('#formRequestStatusLabel').html())!='Draft'){
				if(!confirm("Saving edits will change the status of the brief to 'Draft' and the form will need to be re-submitted")){
					return false;
	    		}
	       	}
		}

			
				if(jformType != 'Brief'){
					 validateFormRequest();
					 
				 
			}
				
		 }      	
			 
		 if(complete_form_request != ''){
			 
	     		$("form#FormRequest").submit();
	    }
	    else{
	     		 $(".alert").show();
	     		 $("#warning").html("Please provide a form name to save it as a Draft");
            	 $(".alert-success").hide();
	    }
		
	});

	$('.checkbox.required').each(function() {
	    $('input:checkbox').addClass('required');
	});

	
	
});
function validateFormRequest(){
	var flag= new Array();

		var fieldCount=0;
	$.each($('.required'), function() {
     	var type = $(this).attr('type');
     	
     	var is_hidden = $(this).parents('.hidden'); 
     	var id_of_hidden = $(is_hidden).attr('id');
     	if(id_of_hidden==undefined){
     		
	     	switch (type) {
	            case 'radio':
	                var radio_name = $(this).attr('name');
	                var span_id = 'span-'+$(this).attr('id');
              		
                	var all_radios = $('input:radio[name^="'+radio_name+'"]');
                 	var count_for_elements =0;
                 	var count_of_unchecked_elements=0;
  					all_radios.each(function(){ 
  						count_for_elements++;
  						if(!($(this).is(':checked'))){
  							count_of_unchecked_elements++;		
  						}
  					});
	  				
	  				if(count_for_elements!=0 && count_of_unchecked_elements!=0 && count_for_elements==count_of_unchecked_elements)
	  				{
	  					$('#'+span_id).html("This field is mandatory.");
	                	flag[fieldCount]=1;
	  				}
	  				else{
	  					$('#'+span_id).html("");
	                	flag[fieldCount]=0;
	  				}
	  				
	                var all_radios = $('input:radio[name^="'+radio_name+'"]');
	  					all_radios.each(function(){ 
	  						if($(this).is(':checked')){
	  							var support = $(this).attr('support');
	                			var support_val = ":::::::";
	                			
	                			if(support!=undefined){
	                				support_val = $("#"+support).val();
	                			}
	            
	                			if(support_val==""){
	                				$('#'+span_id).html("This field is mandatory.");
                				flag[fieldCount]=1;
	                    		}
	                    		else{
		                    	$('#'+span_id).html("");
			                		flag[fieldCount]=0;
			                    }
	  						}
	  					});
	                break;
	            case 'checkbox':
	              	var cb_name = $(this).attr('name');
	              	var cb_id = $('input:checkbox[name^="'+cb_name+'"]:checked').val();
	                var div_id = $(this).parent().parent().parent().attr('id');
	                var span_id = 'span-'+div_id;
	                //alert(span_id);
	      			var count = 0;
	      			
	              	var checked = $('input:checkbox[name^="'+cb_name+'"]:checked');
	              	checked.each(function(){ 
							count++;
						});
                
	                if(count==0){
	                	$('#'+span_id).html("This field is mandatory.");
	                	flag[fieldCount]=1;
	                }
	                else{
	                	$('#'+span_id).html("");
	                	flag[fieldCount]=0;
	                }
	                
	                var other = cb_name.search('Other');
	                
	                var if_cb_is_checked = $(this).is(':checked'); 
	                var mother = $(this).attr('mother'); 
	                	var checked_cbs_in_mother = $('input:checkbox[name^="'+mother+'"]:checked');
	                	var count_of_checked = 0;
	                	checked_cbs_in_mother.each(function(){ 								count_of_checked++;
						});
					
						if(count_of_checked>0){
							$('#'+span_id).html("");	
						}
						
	                if(count_of_checked==0 && other > -1 && if_cb_is_checked){
	                	$('#'+span_id).html("");
                	var support = $(this).attr('support');
	                	var support_val = "";
	                	if(support!=undefined){
	                		support_val = $("#"+support).val();
               	}
	                	
	                	if(support_val==""){
	                		$('#'+span_id).html("This field is mandatory.");
	                		flag[fieldCount]=1;
	                		
	                    }
	                    else{
	                    	$('#'+span_id).html("");
	                    	flag[fieldCount]=0;
	                    }
	                }
	                
	                break;
	                
	            default:
	                var val = $(this).val();
	                var span_id = 'span-'+$(this).attr('id');
	                if(val=="")
	                {
	                	$('#'+span_id).html("This field is mandatory.");
	                	flag[fieldCount]=1;
	                	break;
	                }
	                else{
	                	$('#'+span_id).html("");
	                	flag[fieldCount]=0;
	                }
	                break;
	        }
	        
	        fieldCount++;
     	}
	});
		

     	if(flag.indexOf(1)== -1){
     		$("form#FormRequest").submit();
     	}
     	else{ 
     		$('.alert').show();
  			$('.alert-success').hide();
  			$('html, body').animate({scrollTop: '0px'},400);
  			$('#warning').html('Please select/enter data for the mandatory fields');
     		return false;
     	}
}

</script>

<div class="container form-request">
<?php if(isset($ajaxPop)){?>
<div class="modal email_popup_form">
<div class="modal-header"><h3><i class="fa-icon-envelope"></i> <?php echo __('Checklist');?></h3></div>
<div class="modal-body">
<?php }?>
					<?php 
					$frlabel ="";
					if(isset($formRequestData['FormRequest']['label']) && $formRequestData['FormRequest']['label']!="")
					{
						$frlabel=$formRequestData['FormRequest']['label'];
					}
					?>
					
					
	<span id="clientHid" style="display:none"><?php // echo $clientHid;?></span>

		<?php  if(isset($formRequestData) && ($formRequestData['FormRequest']['status'] == 'survey_not_valid' || $formRequestData['FormRequest']['status'] == 'checklist_rejected_requires_revision' )){?>
		<div class="popup-alert"><?php echo __('Rejection Note: '); 	echo $formRequestData['FormRequest']['rejection_notes']; ?></div>
		<?php  }?>
   		<div class="row-fluid forms-sub-head">
        
        <!-- If not new form show add project button @ ref REDDEV-4832 -->
					<?php  /*if(isset($formRequestData['FormRequest'])){ 
						$formLabelForProject = 'NULL';
						if(!empty($frlabel)){
							$formLabelForProject = str_replace(" ","+",$frlabel);
						}
						//@ref REDDEV-4889 && REDDEV-4832	
						if(!$isClient){?>
							<div class="add-item-btn">
							<?php if($viewAddButton && $isAddFeature){?>
								<a id="addProjectButton" href="/projects/quick_add/null/0/'XXX%20XXX%20XXX'/<?php echo $clientHid;?>/forms/<?php echo $formLabelForProject; ?>/<?php echo $formType; ?>/<?php echo $formRequestAssociation;?>" class="link-pop btn btn-primary">
									<?php echo __('Create project');?>
								</a>
							
							<?php 	} 
								echo $this->Html->link('Associate Form to Projects', "/projects/projects/get_associated_projects/form/$formRequestAssociation/$formType/$formLabelForProject", array('class'=>' btn btn-primary link-pop ','id'=>'multipleFormProjectsAssociation')); 
							?>	
							</div>
							<?php }?>
							
							
							
					<?php }*/ ?>
					<!-- Ends here -->
					<h2 class="span9"><span class="forms-icon" title="<?php echo $formType; ?> <?php echo __('Form');?>"></span><?php echo $formLabel;?><?php //echo $clientLabel.' - '.$formLabel;?> 
        			<h6 class="span6"><?php echo (isset($descriptionForm))? $descriptionForm:'';?></h6>
        			</h2>
        <?php 
        	$projectId = isset($postData['project_id'])? $postData['project_id']:NULL;
        	$projectItemId = isset($postData['project_item_id'])? $postData['project_item_id']:NULL;
        	if((isset($formReqId)) && (isset($formRequestData['StatusCode']['label']) && $formRequestData['StatusCode']['label']!='')):
        ?>	
        <div class="span3 brief-status">
        	<label><?php echo __('Status:');?></label> 
        	<span class="dropdown">
	        	<a href="#" <?php if((count($formStatus) > 0 )):?> class="dropdown-toggle" data-toggle="dropdown" <?php endif;?> id="formRequestStatus"><span id="formRequestStatusLabel"><?php echo $formRequestData['StatusCode']['label'];?></span><?php  if((count($formStatus) > 0 )):?><b class="caret"></b><?php endif;?></a>
	        		<ul class="dropdown-menu" id="statusMenu">
	       <?php 
        			foreach($formStatus as $formRequestStatusVal):
        			if($formRequestStatusVal['StateEngine']['status_code'] == 'survey_not_valid' || $formRequestStatusVal['StateEngine']['status_code'] == 'checklist_rejected_requires_revision' ){
        	?>	
        	        <li><a href="/form_builders/form_requests/add_rejection_note/<?php echo urlencode($formRequestStatusVal['StateEngine']['status_code']); ?>/NULL/<?php echo $formReqId ?>/<?php echo urlencode($formLabel); ?>/" 
        	        	  class="link-pop">
        	        	 <?php echo $formRequestStatusVal['StatusCode']['label'];?>
        	        	</a>
        	        </li> 
        	        
	       <?php }else{?>
	       <li><a href="javascript:void(0);" 
        	        			onClick="javascript:changeStatus(<?php echo $formReqId.',\''.$formRequestStatusVal['StateEngine']['status_code'].'\''.',\''.$formRequestStatusVal['StatusCode']['label'].'\''.',\''.$formRequestData['FormRequest']['status'].'\''.',\''.$formLabel.'\''?>);">
        	        			<?php echo $formRequestStatusVal['StatusCode']['label'];?>
        	        			</a></li> 
           <?php } endforeach;
        	?>
        		 </ul>
        	</span>
        </div>
        <?php 
        endif;
        ?>
        </div><!--end .briefs-sub-head-->
        <?php
        $status = ''; 
         if(isset($formRequestData['FormRequest']['status'])){
         	$status = $formRequestData['FormRequest']['status'];
         }
        if(count($fieldsData)>0){
       ?>
        <div class="follow-me save-changes nav-content-absolute" id="follow-me-buttons"> 
    		<div class="save-msg">
    			<?php echo __('Currently Editing ');?><?php echo $formType; ?>
    			 <span>
    			 	
					<a href="#" class="btn btn-primary" id="saveFormRequest">
						<?php echo __('Save');?>
					<?php  
					if($formType != 'Brief' && isset($formStatus)){ //FOR NOW it is implemented for checklist and survey forms
					foreach ($formStatus as $keystaus => $keyValue){
						if($keyValue['StateEngine']['status_code'] == 'survey_not_valid' || $keyValue['StateEngine']['status_code'] == 'checklist_rejected_requires_revision' ){  
						$href="/form_builders/form_requests/add_rejection_note/".urlencode($keyValue['StateEngine']['status_code'])."/".$formReqId."/NULL/".urlencode($formLabel)."/"; 
						$class = "link-pop btn";
						}else{
						$href = "#";
						$class = "nextStatuses";	
						}
						?>
						<a href="<?php echo $href ;?>" class="btn btn-primary  <?php echo $class;?>" id="<?php echo $keyValue['StateEngine']['status_code'];?>" >
						<?php  echo ucwords(str_replace("_"," ",$keyValue['StateEngine']['action_code']));?>
						</a>
						
						<?php }
					}
					$showButton = 'none';
					if($status == 'brief_submitted'):
						$showButton = '';
					endif;
					
					?>
					
					<a href="#" class="btn btn-primary" id="approveCampaignRequest" style = "display:<?php echo $showButton; ?>">
						<?php echo __('Approve');?>
					</a>
					
					<a href="/form_builders/form_requests/add_rejection_note/brief_rejected/<?php echo isset($formReqId)?$formReqId:NULL; ?>/<?php echo isset($formRequestAssociation)?$formRequestAssociation:NULL ;?>" class="btn btn-primary link-pop btn" id="rejectFormRequest" style = "display:<?php echo $showButton; ?>">
						<?php echo __('Reject');?>
					</a>
					</a>
					<?php ?>
					<?php 
					$showSubmitButton = 'none';
					if($status == 'brief_rejected' || $status == 'brief_draft'):
						$showSubmitButton = '';
					endif;?>
					<a href="#" class="btn btn-primary" id="submitRequest"  style = "display:<?php echo $showSubmitButton; ?>">
						<?php echo __('Submit');?>
					</a>  
					<a href="<?php echo ($formType == 'Brief')?'/campaign_requests':'/forms'?>" class="btn" id="cancelFormRequest">
						<?php echo __('Cancel');?>
					</a>
				</span>
			</div>
        </div>
        <?php } ?>
        	
<?php

echo $this->Html->scriptBlock('
	 	$(document).ready( function(){
	 		//$(".date").mask("9999/99/99");
			$(".number").mask("9?99999999");
			//for other checkbox show hide
			$(".Others").click(function (){
				valueForChkBx = $(this).attr("id");
        		valueForChkBx = valueForChkBx.split(\'-\');
        		 texareaID = "#textArea-"+valueForChkBx[1];
			if (this.checked) {
				$(texareaID).show();
        		
        	}else{
        		$(texareaID).hide();
        		$(texareaID).html("");
        	}
			});
			$.each($(\'.datepicker\'), function(index, value) { 
			$(this).datepicker({
									\'dateFormat\':\'yy-mm-dd\',
									\'focusFirstField\': false
									});
		});
		
    	});
	'); 
		
		
		 
	    ?>
	    	
		 <div class="brief-form">
			<div class="add-client">
			<?php 
				
				echo $this->Form->create('FormRequestData',array('url' => '/form_builders/form_requests/add/'.$formReqId, 'id'=>'FormRequest','class'=>'ajax form-horizontal','enctype'=>"multipart/form-data"));?>
			<?php echo $this->Form->input('FormRequest.client_hid',array('type'=>'hidden','id'=>'clientHid','value'=>$client_id,'label'=>false,'div'=>false));?>
			<?php echo $this->Form->input('Form.label',array('type'=>'hidden','id'=>'formLabel','value'=>$formLabel,'label'=>false,'div'=>false));?>
			
        	<?php 
        	if(isset($formRequestAssociation)){
        	echo $this->Form->input('FormRequest.formRequestAssociation',array('type'=>'hidden','id'=>'formRequestAssociationId','value'=>$formRequestAssociation,'label'=>false,'div'=>false));
        	}?>
        	<?php echo $this->Form->input('FormRequest.workflow_id',array('type'=>'hidden','id'=>'formWorkFlowId','value'=>$workFlowId,'label'=>false,'div'=>false));?>
        	<?php echo $this->Form->input('FormRequest.form_id',array('type'=>'hidden','id'=>'formId','value'=>$formId,'label'=>false,'div'=>false));?>
        	<!-- Hidden values for form data to be saved for associated project items @ref REDDEV-5127 	 -->
        	<?php //if(isset($postData) && !empty($postData)){?>
	        	<?php echo $this->Form->input('FormProjectItemJoin.state_engine_id',array('type'=>'hidden','label'=>false,'div'=>false));?>
				<?php echo $this->Form->input('FormProjectItemJoin.project_id',array('type'=>'hidden','label'=>false,'div'=>false));?>
				<?php echo $this->Form->input('FormProjectItemJoin.project_item_id',array('type'=>'hidden','label'=>false,'div'=>false));?>
        	<?php //}?>	
        	<!-- Ends Here -->
	<div id="statusFormRequest">
	<?php if(!isset($formRequestData)){
		if($formType == 'Brief'){?>
		<input type='hidden' name='data[FormRequest][status]'  value='brief_draft'>
	<?php }else{?>
		<input type="hidden" name="data[FormRequest][status]"  value="<?php echo strtolower($formType).'_inprogress'; ?>"/>
	<?php }}?>
    </div>
    
    <div id="saveFormRequestStatus">
    </div>
    <div id = "workflowId" style = "display:none"  ><?php echo $workFlowId; ?></div>
    <div id = "formType" style = "display:none" ><?php echo $formType; ?></div>
    <div id="formRequestId" style = "display:none" ><?php echo $formReqId; ?></div>
	<div class="popup-alert" style="display:none">
     <a href="#" class="close" data-dismiss="alert">&times;</a>
     <span id="popupwarning"></span>
     </div>
     <!-- Hardcoded Campaign Requset Name -->
		 <div class="single-item campaign-name">
		 	<label><?php echo __($formType.' Name');?></label> 
				 
					<?php 
					
					echo $this->Form->input('FormRequest.label', array('type'=>'text','div'=>false, 'label'=>false,'id'=>'formRequestName', 'value'=>$frlabel ,'class'=>'span9'),array('class'=>'span9 inputtext txt required', 'style'=>'width:200px;padding:5px;'));?>
				
		</div>
		<!-- Ends here -->
    	<?php  
	     if(!empty($fieldsData)){
		$belongsTo='';
		$elementsToDisplay='';
	
		?>
       			<?php 	$i=0;
       			
       					//foreach ($avlFieldList as $key=>$data) {
       					//ksort($data);// sort array order by weight
       					foreach($fieldsData as $k=>$element){ 
								?>
	    				<?php 
	    					$id=$element['Field']['id'];
	    			 	 		if(isset($element['FieldValue']['id'])){
				    			$predeclaredVal=$element['FieldValue']['value'];
				    		}
				    		else{
				    			$predeclaredVal="";
				    		}
				    		$description ="";
				    		if(!empty($element['Field']['description'])){
				    			$description = $element['Field']['description'];
				    		}
				    		$reqclass = ($element['Field']['is_required'])? 'required':NULL;
				    		$showHideLabel = isset($element['Field']['is_display_label'])?'block':'none';
				    		switch ( $element['FieldTypes']['label'] ){
				    			case "text" :  
				    				?>
				    				<div class="brief-details basic-form">
				    					<div class='single-item marginBriefsLabel'>
				    						<label class = "briefLabel" style="display:<?php echo $showHideLabel;?>"><?php echo $element['Field']['label'];?><span class="astrix"><?php echo ($element['Field']['is_required'])?'*':NULL;?></span></label>
				    						<?php if($description != ''){?><span class="briefDescription"><?php echo $description; ?></span><?}?>
				    						
				    						<?php echo $this->Form->input('val-'.$id.'-'.$element['Field']['label'] , array('type'=>'text','div'=>false, 'label'=>false,'id'=>'val-'.$id, 'value'=>$predeclaredVal,'class'=>"span9 $reqclass"),array('class'=>'inputtext txt required', 'style'=>'width:200px;padding:5px;'))?>
				    						<span id="span-<?php echo 'val-'.$id;?>" class="astrix"></span> 
				    					</div>
				    				</div>
				    				<div class="clear"></div>
				    				<?php 	
				    				break;
				    			case "number" :
				    				?> 
				    				<div class="brief-details basic-form">
				    					<div class='single-item marginBriefsLabel'>
				    					<label class = "briefLabel" style="display:<?php echo $showHideLabel;?>"><?php echo $element['Field']['label'];?><span class="astrix"><?php echo ($element['Field']['is_required'])?'*':NULL;?></span></label>
				    					<?php if($description != ''){?><span class="briefDescription"><?php echo $description; ?></span><?}?> 
					    				<?php 
													
													if($predeclaredVal == "" || $predeclaredVal == "NULL"){
														$valForDate = "";
													}else{
														$valForDate = date('Y-m-d',strtotime($predeclaredVal));
													} 
													echo $this->Form->input('val-'.$id.'-'.$element['Field']['label'] , array('type'=>'text','div'=>false, 'label'=>false,'id'=>'val-'.$id, 'value'=>$predeclaredVal,'class'=>"$reqclass",),array( 'style'=>'width:200px;padding:5px;'));?>
													
					    					</div><span id="span-<?php echo 'val-'.$id;?>" class="astrix"></span>
				    					</div>
				    					<div class="clear"></div>
				    				<?php 
				    				break;
				    			case "textarea" :
							    				?> 
							    				<div class="brief-details basic-form">
				    							<div class='single-item marginBriefsLabel'>
							    				<label class = "briefLabel" style="display:<?php echo $showHideLabel;?>"><?php echo $element['Field']['label'];?><span class="astrix"><?php echo ($element['Field']['is_required'])?'*':NULL;?></span></label>
							    				<?php if($description != ''){?><span class="briefDescription"><?php echo $description; ?></span><?}?>
							    				<?php echo $this->Form->input('val-'.$id.'-'.$element['Field']['label'] ,array('type'=>'textarea','div'=>false, 'label'=>false,'id'=>'val-'.$id, 'value'=>$predeclaredVal,'class'=>"span9 $reqclass"),array('class'=>'inputtext number required'));?>
							    				<span id="span-<?php echo 'val-'.$id;?>" class="astrix"></span>
							    				</div>
							    				</div>
							    				<div class="clear"></div>
							    				<?php 
							    				break;
								case "date" : 
									?>
									<div class="date-obj">
										<div class="date-select marginBriefDateField">
											<label class = "briefLabel" style="display:<?php echo $showHideLabel;?>"><?php echo $element['Field']['label'];?><span class="astrix"><?php echo ($element['Field']['is_required'])?'*':NULL;?></span></label>
											<?php if($description != ''){?><span class="briefDescription"><?php echo $description; ?></span><?}?>
												<?php 
														
														if($predeclaredVal == "" || $predeclaredVal == "NULL"){
															$valForDate = "";
														}else{
															$valForDate = date('Y-m-d',strtotime($predeclaredVal));
														} echo $this->Form->input('val-'.$id.'-'.$element['Field']['label'] , array('type'=>'text','div'=>false, 'label'=>false,'id'=>'val-'.$id,'class'=>'date datepicker date-metadata span2 '.$reqclass, 'value'=>$valForDate),array('class'=>'inputtext date required', 'style'=>'width:200px;padding:5px;'));?>
														<span id="span-<?php echo 'val-'.$id;?>" class="astrix"></span>
										</div>
									</div>
									<div class="clear"></div>
									<?php 	
									break;
								case "html_textarea":?>
										<div class="brief-details basic-form">
										<label class = "briefLabel" style="display:<?php echo $showHideLabel;?>"><?php echo $element['Field']['label'];?><span class="astrix"><?php echo ($element['Field']['is_required'])?'*':NULL;?></span></label>
										<?php if($description != ''){?><span class="briefDescription"><?php echo $description; ?></span><?}?>
										<div class='single-item'>
											<?php  echo stripslashes($element['Field']['options']); ?>
										</div>
										</div>
										<div class="clear"></div>
								<?php 	
									break;
								case "field_set":?>
										<fieldset>
										<legend style="display:<?php echo $showHideLabel;?>"><?php echo $element['Field']['label'];?><span class="astrix"><?php echo ($element['Field']['is_required'])?'*':NULL;?></span></legend>
										<?php if($description != ''){?><span class="briefDescription"><?php echo $description; ?></span><?}?>
										
										<?php if(array_key_exists($element['FormFieldJoin']['id'], $childArray)){ 
											echo $this->element('FormRequests/render_fieldsets_field_formrequest',array('childArray'=>$childArray[$element['FormFieldJoin']['id']],'formReqId'=>$formReqId));
										}?>
										
										</fieldset> 
										<div class="clear"></div>
								<?php 	
									break;
									case "file_upload":
										
										?>
									
										<div class="brief-details basic-form">
				    						<div class='single-item marginBriefsLabel'>
												<label class = "briefLabel" style="display:<?php echo $showHideLabel;?>"><?php echo $element['Field']['label'];?><span class="astrix"><?php echo ($element['Field']['is_required'])?'*':NULL;?></span></label>
												<?php if($description != ''){?><span class="briefDescription"><?php echo $description; ?></span><?}?> 
													<?php //$hid = $element['HierarchyValue']['id'];
													 if($formReqId == "") {
														$formReqId =0;
													   }
													?>
												<input type="file" name="files" class="fileupload" id="fileupload-<?php echo $i; ?>"  onclick="attachmetaDatafiles('<?php echo $formReqId;?>','<?php echo $id;?>',<?php echo $i;?>)" multiple>
												<input type="hidden" id='<?php echo 'val-'.$id;?>' value=""  name="<?php echo('data[FormRequestData][val-'.$id.'-'.$element["Field"]["label"].']');?>" class="<?php echo $reqclass;?>">
												<div id="upFile-<?php echo $i++; ?>" class= "upFile"></div>
												<span id='span-<?php echo 'val-'.$id;?>' class="astrix"></span>
											</div>
										<?php 
										 if(isset($fileArray[$k]) && isset($fileArray[$k])) {
										
										?>		<div class="cr_file_campaign_request">
													<?php echo $this->FormRequestsFile->getAssociatedFormFilesHtmlMetadata($fileArray[$k],'form');?>
												</div>			

									<?php 
										 }
										 ?>	
										 </div>
										 <div class="clear"></div>
									<?php 
									break;
									case "checkbox":
									$options=array();
									$otherOptions="";
									$chkValues=array();
									$optionOther="";
									$textAreaValue="";
									if(isset($predeclaredVal) && $predeclaredVal!=""){
									$optionOther=explode('::',$predeclaredVal);
									if(isset($optionOther[1]))
									$textAreaValue = $optionOther[1];
									$chkValues = explode('|',$optionOther[0]);
									}
									
									$optionsExplodes=explode('|',$element['Field']['options']);
									 $defaultVal = "" ;
									if(isset($optionDefault[1])){ 
										$defaultVal = $optionDefault[1]; 
									} 
									
									foreach( $optionsExplodes as $k=>$optionsExplode){
										if($optionsExplode != "[othersTextarea]"){
											$options[$optionsExplode] = $optionsExplode;
										}else if($optionsExplode == "[othersTextarea]"){
												if($textAreaValue!=""){
													$otherOptions.= '<div class="checkbox"><label><input type="checkbox" class="Others" name="Others" id="Other-'.$id.$element["HierarchyValue"]["id"].'" checked>Other:</label></div>' ;
													$otherOptions.= '<div class="other-txtarea"><textarea name="data[FormRequestData][val-'.$id.'-'.$element["Field"]["label"].'-'.$element["HierarchyValue"]["id"].'][Other]" id="textArea-'.$id.$element["HierarchyValue"]["id"].'">'.$textAreaValue.'</textarea></div>';
												}else{
													$otherOptions.= '<div class="checkbox"><label><input type="checkbox" class="Others" name="Others" id="Other-'.$id.$element["HierarchyValue"]["id"].'">Other:</label></div>' ;
													$otherOptions.= '<div class="other-txtarea"><textarea style="display:none" name="data[FormRequestData][val-'.$id.'-'.$element["Field"]["label"].'-'.$element["HierarchyValue"]["id"].'][Other]" id="textArea-'.$id.$element["HierarchyValue"]["id"].'">'.$textAreaValue.'</textarea></div>';
												}
										}
									}
									?>
									<div class='check-stack brief-service marginBriefsLabel'>
									<h3 style="display:<?php echo $showHideLabel;?>"><?php echo $element['Field']['label'];?><span class="astrix"><?php echo ($element['Field']['is_required'])?'*':NULL;?></span><br/><?php if($description != ''){?><span class="briefDescription"><?php echo $description; ?></span><?}?></h3>
									
											<div id='<?php echo 'val-'.$element['Field']['id']; ?>'>
											<?php 
											echo $this->Form->input('val-'.$id.'-'.$element['Field']['label'], array('multiple' => 'checkbox','value'=>$chkValues, 'options' => $options,'label'=>false,'class' => 'checkbox '. $reqclass,'id' => 'checkboxVal'));
											echo $otherOptions;
											
											?>
											<span id="span-<?php echo 'val-'.$element['Field']['id']; ?>" class="astrix"></span>
										</div></div><div class="clear"></div>
									<?php 
									break;
									case "select":
									$options=array();
				    				$optionDefault=explode('::',$element['Field']['options']);
									$optionsExplodes=explode('|',$optionDefault[0]);
									 $defaultVal = "" ;
									if(isset($optionDefault[1])){ 
										$defaultVal = $optionDefault[1]; 
									} 
									foreach( $optionsExplodes as $optionsExplode){
										if($optionsExplode==$predeclaredVal){
											$selected="selected";
										}
										else if($optionsExplode == $defaultVal && $predeclaredVal ==""){
											$selected="selected";
										}else{
											$selected="";
										}
									$options[] = array('value'=>$optionsExplode,'name'=>$optionsExplode,'selected'=>$selected); 
									
									}
									?>
									<div class=' single-item select2-controls marginBriefsLabel'>
									<label class = "briefLabel" style="display:<?php echo $showHideLabel;?>"><?php echo $element['Field']['label'];?><span class="astrix"><?php echo ($element['Field']['is_required'])?'*':NULL;?></span></label>
									<?php if($description != ''){?><span class="briefDescription"><?php echo $description; ?></span><?}?>
										
											<?php echo $this->Form->input('val-'.$id.'-'.$element['Field']['label'], array('type'=>'select','options'=>$options,'div'=>false,'label'=>false,'empty' => '--Select--','class'=> $reqclass, 'id'=>'val-'.$id),array('style'=>'width:212px;padding:3px;'));?>
											<br><span id="span-<?php echo 'val-'.$id;?>" class="astrix"></span>
										</div><div class="clear"></div>
										
									<?php 
									break;
									case "radio":
									$radioAsterik = ($element['Field']['is_required'])?'*':NULL;
									$options=array();
									$textAreaValue="";
				    				if(isset($predeclaredVal) && $predeclaredVal!=""){
									$optionText=explode('[',$predeclaredVal);
									if(isset($optionText[1])){
											$textAreaValue = $optionText[1];
										}
											$predeclaredVal = $optionText[0];
									}
				    				$optionDefault=explode('::',$element['Field']['options']);
									$optionsExplodes=explode('|',$optionDefault[0]);
									 $defaultVal = "" ;
									if(isset($optionDefault[1])){ 
										$defaultVal = $optionDefault[1]; 
									} 
									$htmlForRadio ="";
									$htmlForTextareaOrTextBox ="";
									$htmlForRadio .='<div class="check-stack brief-service marginBriefsLabel">';
									$htmlForRadio .='<label class = "briefLabel" style="display:'.$showHideLabel.'">'.$element["Field"]["label"].'<span class="astrix">'.$radioAsterik.'</span></label>';
									if($description != ''){
									$htmlForRadio .= '<span  class="briefDescription" >'. $description.'</span>';
									} 
									$htmlForRadio .= '<div class="clear"></div>';
									foreach( $optionsExplodes as $optionsExplode){
										$checked=false;
										$valueInput ="";
										$htmlForTextareaOrTextBox ="";
										if(strrpos($optionsExplode, "[othersTextarea]") > 0) { 
											$optionsExplode = str_replace("[othersTextarea]", "", $optionsExplode);
											$stripoptionsExplode = preg_replace('/( *)/', '', $optionsExplode);
											if($optionsExplode == $predeclaredVal)
											$valueInput = $textAreaValue;
											$htmlForTextareaOrTextBox ='<textarea class="span9" name="data[FormRequestData][val-'.$id.'-'.$element["Field"]["label"].'][others_options]['.$stripoptionsExplode.']">'.$valueInput.'</textarea>';
											
										}
										if(strrpos($optionsExplode, "[othersTextbox]") > 0) {
											$optionsExplode = str_replace("[othersTextbox]", "", $optionsExplode);
											$stripoptionsExplode = preg_replace('/( *)/', '', $optionsExplode);
											if($optionsExplode == $predeclaredVal)
											$valueInput = $textAreaValue; 
											$htmlForTextareaOrTextBox ='<input class="span9" type="text"  name="data[FormRequestData][val-'.$id.'-'.$element["Field"]["label"].'][others_options]['.$stripoptionsExplode.']" value="'.$valueInput.'">';
										}
										if($optionsExplode==$predeclaredVal){
											$checked = "checked = 'checked'";
										}
										else if($optionsExplode == $defaultVal && $predeclaredVal ==""){
											$checked = "checked = 'checked'";
											
										}
									$htmlForRadio .= '<div class="">';
									$htmlForRadio .= '<label class="radio">';
									$htmlForRadio .= '<input class="'.$reqclass.'" type = "radio" id=val-'.$id.' value="'.$optionsExplode.'"  '.$checked.'   name="data[FormRequestData][val-'.$id.'-'.$element["Field"]["label"].'][value]" >';
									$htmlForRadio .= $optionsExplode;
									$htmlForRadio .= '</label>';
									$htmlForRadio .= $htmlForTextareaOrTextBox;
									$htmlForRadio .= '</div>';
									
									}
									$htmlForRadio .= '</div>';
									?>
									
									
										
											<?php echo $htmlForRadio;?>
											<span id="span-<?php echo 'val-'.$id;?>" class="astrix"></span>
										<div class="clear"></div>
									<?php 
									break;
									case "hr" :  
				    				?>
				    				<hr>
				    				<div class="clear"></div>
				    				<?php 	
				    				break;
				    		}// end of switch
	    				}//end of foreach  
					//}//end of foreach 
	    		?>
	    		 <input type="hidden" name="type" value="form">

		
		<?php }else{?>
		<div class="formbox extended-data">
			Metadata fields does not exist. 
		</div>
		<?php }echo $this->Form->end();?>
		

</div><!--end brief-form--><?php echo $this->Form->end();?>
     <?php if(isset($ajaxPop)){?>
     </div><!-- model body div -->
     <div class="modal-footer"></div>
       </div>
       <?php }?>
       </div><!--end .container-->
<script>
<?php   
if(isset($avlFieldList) && count($avlFieldList)>0):?>	
$(function(){
		var message = $( ".follow-me" );
			var originalMessageTop = message.offset().top;
			var view = $( window );
			view.bind(
				"scroll resize",
				function(){
					var viewTop = view.scrollTop();
					if (
						(viewTop > originalMessageTop) &&
						!message.is( ".nav-content-fixed" )
						){
						message
							.removeClass( "nav-content-absolute" )
							.addClass( "nav-content-fixed" )
						;
					} else if (
						(viewTop <= originalMessageTop) &&
						message.is( ".nav-content-fixed" )
						){
						message
							.removeClass( "nav-content-fixed" )
							.addClass( "nav-content-absolute" )
						;
 
					}
				}
			);
			
			
	});
<?php endif;?>
	function fileError(){
		$('.alert').show();
		$('.alert-success').hide();
		$('html, body').animate({scrollTop: '0px'},400);
		$('#warning').html('Please Save Or Submit form Request Form Before Trying To Upload Any File');
	}
	function changeStatus(FormRequestId, formRequestNextCode, FormRequestLabel, formRequestCurrentCode, formLabel=NULL){
			
			workFlowId = $('#workflowId').text();
			category = $('#formType').text();
		
		$.ajax({
  				type: "GET",
  			    url:"/form_builders/form_requests/editStatus/statusUpdate/"+FormRequestId+'/'+formRequestNextCode+'/'+formRequestCurrentCode+'/'+workFlowId+'/'+category+'/'+formLabel+'/',	                
  			    success: function(data){
						var obj = jQuery.parseJSON(data);
							if(obj.success){
		   		    	 		$(".alert-success").show();
		            	 		$("#success").html(obj.success);
		            	 		if(obj.formRequestStatus){
			            	 		$('#statusMenu').html('');
			            	 		var flag=false;
			            	 		$(obj.formRequestStatus).each(function(i,val){
				            	 		$('#statusMenu').append('<li><a href="javascript:void(0);" onclick="javascript:changeStatus('+FormRequestId+',&apos;'+val.StateEngine.status_code+'&apos;,&apos;'+val.StatusCode.label+'&apos;,&apos;'+formRequestNextCode+'&apos;,&apos;'+formLabel+'&apos;);" >'+val.StatusCode.label+'</a></li>');
				            	 		flag=true;
				            	 	});
			            	 		if(!flag){
				            	 			$('#formRequestStatus').html(FormRequestLabel);	
			            	 				$('#formRequestStatus').removeClass('dropdown-toggle');
			            	 				$('#formRequestStatus').attr('data-toggle','');
			            	 				$('#follow-me-buttons').show();
											$('#saveAsDraftFormRequest').hide();
											$('#saveFormRequest').show();
			            	 	 	}
			            	 		else{
			            	 			$('#formRequestStatus').html(FormRequestLabel+'<b class="caret"></b>');
			            				$('#formRequestStatus').addClass('dropdown-toggle');
		            	 				$('#formRequestStatus').attr('data-toggle','dropdown');
		            	 				$('#saveAsDraftFormRequest').hide();
				            	 		
			            	 			
		            	 			}
			            	 	}
		            	 		if(obj.currentStatus){
			            	 		$("#statusFormRequest").html("<input type='hidden' name='data[FormRequest][status]' value='"+obj.currentStatus+"'>");
			            	 		location.reload();//TODO if we need to keep dropdown add functionality
			            	 		
				            	 	}
			    			}
			    			 else{
				            	 $(".alert").show();
				            	 $("#warning").html(obj.errors);
				            	 $(".alert-success").hide();
					         }	
	             } // success close
	   			}); //ajax close
	}

</script>
<script type="text/javascript">
var fileContainer = null;
var uniqueNoId = 0; // In order to delete the uploaded file which are stored in session but not yet saved unique Id should be generated.
function attachmetaDatafiles(campId,id,divId) { 
	if(campId == "0") {
		fileError();
		return;
	}
	var fileDivId = 'upFile-'+divId;
	var fileUploadId = 'fileupload-'+divId;
	var url = "/projects/fieldValues/addFilesForForms/form/"+fileDivId+"/"+campId+"/"+id+"/";
	var config = $.data($('#'+fileUploadId).get(0),'fileupload');
	var container = "upload_files_" +divId;
	if ($("#" + container)) {	
		$("#"+fileDivId).append('<ul id="'+ container + '"></ul>');
	}
	config.options.url = url;
	fileContainer = $("#" + container);
}
$(".fileupload").fileupload({ 
	autoUpload: false,
	dataType: 'json',
	add: function (e, data) { 
		var progressBar = null;
 		$.each(data.files, function (index, file) {
 			progressBar = $('<li><div class="upload-progress"><div class="html-image-upload-label">' + file.name + '</div><div class="progress progress-success"><div class="progress-img" style="width: 0%; display: inline-block;"></div></div><div class="progress-percentage">0%</div></div></li>');
 		 });
 		fileContainer.append(progressBar);
 		data.context = progressBar;		
		data.submit();
	},
    done: function (e, data) {
    	 var li = data.context;
         li.html('');
       
         $.each(data.files, function (index, file) {
             //console.log(data.files);
        	 if(data.result.file.id === true){
	             	li.attr("id", "file_attach_" + uniqueNoId);
	         	 	li.append("<a class='delete-file' title = '"+file.name+"' onclick='deleteAttachmentAjax(\""+file.name+"\",\""+uniqueNoId+"\")' href='javascript:void(0);'>delete</a><a href='#'>" + file.name+ "</a>");
        		 }else{
        			 $('#'+data.result.file.divId).append('<span class ="alert-info"> Please check file size it should not excceed 2 gb</span>');
            		 }
        	 uniqueNoId++;
        	 });
         

    },
    progress: function (e, data) {
        if (data.context) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            
            data.context.find('.progress')
                .attr('aria-valuenow', progress)
                .find('.progress-img').css(
                    'width',
                    progress + '%'
                );
            data.context.find('.progress-percentage').html(progress+"%");
        }
    },
   send: function (e, data) {
        // don't need it yet
    }		
    
});

$("select", $(".select2-controls")).select2({
	width:'220px'
});
</script>