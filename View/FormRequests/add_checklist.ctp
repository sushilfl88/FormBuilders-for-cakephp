<script>
$(function(){
	 $(".modal-body").css("height","100px");
	 $("#new_checklist").click(function(){
		 window.location.href = $('#checklistForm').val();
		});
});

function changeContentType(){
	clientHid = $('#selectClient option:selected').val();
	clientName = $('#selectClient option:selected').text();

			 	$.ajax({
				type: "GET",
			    url:"/form_builders/form_requests/view/Checklist/"+clientHid,/*2=>checklist category*/	             
			    success: function(data){
					var obj = jQuery.parseJSON(data);
						if(obj.success){

							var options = "" ;
	   						$(obj.Forms).each(function(i,val){
	   							options += '<option value="/projects/field_values/form_request/create/'+ val.FormAssociation.id +'">' + val.Form.label + '</option>';

		   					});
							$.fn.colorbox.resize();     
		   					$('#formOptions').show();
		   					$('#checklistForm').html(options);
		   					 } // success close
           			 
	 			} 
   			}); //ajax close

	}		
			
</script>
<div class="modal multi-spec-class-modal">
	<div class="modal-header">
		<h3>
		<?php echo __('Add Checklist'); ?>
		</h3>
	</div>

	<div class="modal-body">
	<?php
	echo $this->Form->create("Checklist", array( 'id' => 'Checklist', 'inputDefaults' => array('div' => false, 'label' => false)));

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
				<?php echo $this->Form->input('checklist_form', array('type' => 'select', 'id'=>'checklistForm','empty' => true));?>
				</div>
			</div>

			<?php endif;?>
		</fieldset>

	</div>

	<div class="modal-footer">
		<a href="javascript: void(0)" class="btn btn-primary" id="new_checklist"><?php echo __('Start A New Checklist'); ?>
		</a>
	</div>
	<?php echo $this->Form->end()?>
</div>
