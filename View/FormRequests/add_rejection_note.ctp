<script>
$(function(){
	 $(".modal-body").css("height","100px");
	 $("#save_note").click(function(){
	 
		 $("form#rejectionNote").submit();
		 $.ajax({
	          type: "POST",
	          url:"/form_builders/form_requests/save_rejection_note/",
	          data:{'rejection_note':$('#rejectionNote').val(),
		          	'fr_id':$('#fr_id').val(),
		          	'status':$('#statusForm').val(),
		          	'formLabel':$('#formLabel').val(),
		          	'currentStatus':$("#currentStatusForm").val(),
		          	'distributionList':$("#distributionList").text()
		          	},success: function(data){ 
		          		var obj = jQuery.parseJSON(data);
		       			if(obj.msg){
		               		
		               		$.fn.colorbox.close();
			               	$(".popup-alert").hide();
			               	statusMessages("Note added successfully.");
			               	window.location.href = obj.redirect;
		       			}
		       			
		       			
		       		}
		 });
		 
		});
});

	
			
</script>
<div class="modal multi-spec-class-modal">
	<div class="modal-header"><h3><?php echo __('Add Rejection Note'); ?></h3>
	</div>
	
	<div class="modal-body">
	<?php 
	echo $this->Form->create("rejectionNote", array('url' => '/form_builders/form_requests/save_rejection_note/' ,'id' => 'RejectionNote', 'inputDefaults' => array('div' => false, 'label' => false))); ?>
	
	
	<fieldset class="ajax form-horizontal">
				<div class="control-group">
							<label class="control-label"> <?php echo __('Notes'); ?> </label>
							<div class="controls">
							<?php echo $this->Form->input('', array('type' => 'textarea', 'id'=>'rejectionNote','empty' => true ,'name' =>'data[FormRequest][form_requests]'));?>
							<?php echo $this->Form->input('',array('type'=>'hidden','id'=>'fr_id','value'=>$formRequestId,'label'=>false,'div'=>false));?>
							<?php echo $this->Form->input('',array('type'=>'hidden','id'=>'statusForm','value'=>$status,'label'=>false,'div'=>false));?>
							<?php echo $this->Form->input('',array('type'=>'hidden','id'=>'formAssociation','value'=>$formRequestAssociation,'label'=>false,'div'=>false));?>
							<?php echo $this->Form->input('',array('type'=>'hidden','id'=>'formLabel','value'=>$formLabel,'label'=>false,'div'=>false));?>
							</div>
				</div>
	</fieldset>    	 
		
	</div>
	
	<div class="modal-footer">
		<a href="javascript: void(0)" class="btn btn-primary" id="save_note"><?php echo __('Save'); ?></a>
	</div>
<?php echo $this->Form->end()?>
</div>
