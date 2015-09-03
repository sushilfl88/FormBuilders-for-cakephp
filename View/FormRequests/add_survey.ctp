<script>
$(function(){
	 $(".modal-body").css("height","100px");
	 $("#new_survey").click(function(){
		 window.location.href = $('#surveyForm').val();
		});
});

function changeContentType(){
	clientHid = $('#selectClient option:selected').val();
	clientName = $('#selectClient option:selected').text();

			 	$.ajax({
				type: "GET",
			    url:"/form_builders/form_requests/view/Survey/"+clientHid,	                
			    success: function(data){
					var obj = jQuery.parseJSON(data);
						if(obj.success){

							var options = "" ;
	   						$(obj.Forms).each(function(i,val){
	   							options += '<option value="/projects/field_values/form_request/create/'+ val.FormAssociation.id +'">' + val.Form.label + '</option>';
							});
							$.fn.colorbox.resize();     
		   					$('#formOptions').show();
		   					$('#surveyForm').html(options);
		   					 } // success close
           			 
	 			} 
   			}); //ajax close

	}		
			
</script>
<div class="modal multi-spec-class-modal">
	<div class="modal-header">
		<h3>
		<?php echo __('Add Survey'); ?>
		</h3>
	</div>

	<div class="modal-body">
	<?php
	echo $this->Form->create("Survey", array( 'id' => 'Survey', 'inputDefaults' => array('div' => false, 'label' => false)));

	if(isset($hierarchyValueData) && count ($hierarchyValueData)>0 && $hierarchyValueData):?>
		<fieldset class="ajax form-horizontal">
			<div class="control-group">
				<label class="control-label"> <?php echo __('Select client'); ?> </label>
				<div class="controls">
				<?php echo $this->Form->input('client_hid', array('type' => 'select', 'id'=>'selectClient','options' => $hierarchyValueData,'empty' => true,'onchange' => "javascript:changeContentType(this);"));?>
				</div>
			</div>
			<div class="control-group" id="formOptions" style="display: none">
				<label class="control-label"> <?php echo __('Select Form'); ?>
				</label>
				<div class="controls">
				<?php echo $this->Form->input('survey_form', array('type' => 'select', 'id'=>'surveyForm','empty' => true));?>
				</div>
			</div>

			<?php endif;?>
		</fieldset>

	</div>

	<div class="modal-footer">
		<a href="javascript: void(0)" class="btn btn-primary" id="new_survey"><?php echo __('Start A New Survey'); ?>
		</a>
	</div>
	<?php echo $this->Form->end()?>
</div>
