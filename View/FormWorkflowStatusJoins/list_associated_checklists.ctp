<script>
$("a.disasassociate").click(function(){
	var urlDisAssociate = $(this).attr("value");
	
	
       $.ajax({
            type: "POST",
            url:urlDisAssociate,
            dataType: 'json',
            beforeSend:function(){
            
		    },
		   complete:function(){
		    
		    },
            success: function(obj){
	            		if(obj){
	    				    $.colorbox.close();
	    				    $("#success").html("Disassociated Succesfully...");
					    	location.reload(true);     			
		            		}
            		}
		   	});
		});
</script>
<div class="modal modal-campaignType">
  <div class="modal-header">
	<h3><?php echo __('Associated forms');?></h3>
	 <div class="popup-alert alert-success" style="display:none">
	     <a href="javascript: void(0)" class="close" data-dismiss="alert">&times;</a>
	     <span id="success-pop"></span>
	</div>
    <div class="popup-alert" style="display:none">
	     <a href="javascript: void(0)" class="close" data-dismiss="alert">&times;</a>
	     <span id="warning-pop"></span>
    </div>
	
  </div>
  <fieldset>
		<div class="modal-body">
		
		
			<div class="control-group clear fieldset_padding_bottom">
				<div class="controls">
					<span class="floater width60"><h6><?php echo __('Sr.no');?></h6>
					</span>
					<span class="floater fieldset_label width150"> <h6><?php echo __('Forms');?></h6>
					</span>
					<span class="floater width150"><h6><?php echo __('Status');?></h6> 
					</span>
					<span class="floater width60"><h6><?php echo __('Preview');?></h6> 
					</span>
					<span class="floater width60"><h6><?php echo __('Remove Checklist');?></h6> 
					</span>
				</div>	
				</div>
				<!--  for  associated fields -->
				<?php 
				$counter = 1;
				foreach($associatedData as $key=>$values){
				?>
				<div class="control-group clear fieldset_padding_bottom associatedData ">
					<div class="controls">
					<span class="floater width60" >
						<?php echo $counter++;?>
					</span>
					<span class="floater width150" ><?php echo $values['Form']['label'];?></span>
					<span class="floater width150"><?php echo $values['StatusCode']['label'];?></span>
					<span class="floater width60"><a  target= "_blank" href = "/projects/fields/form_request_preview/<?php echo $values['Form']['id'];?>"><?php echo __('Preview');?></a></span>
					<span  class="floater width60"><a   href="#" class="disasassociate" value="/form_builders/form_workflow_status_joins/disassociateByFormWorkflowStatusJoinId/<?php echo $values['FormWorkflowStatusJoin']['id']; ?>"><?php echo __('Remove');?></a></span>
					</div>
				</div>
				<?php 
						} ?>
				<!--  for  associated fields ends here -->
				
			
				
			
		</div>
		
		
  </fieldset>
  <div class="control-group clear fieldset_padding_bottom modal-footer">
     			<label class="control-label"></label>
				<div class="controls">
					
				</div>
      	</div>  
</div> 
<?php $this->Form->end();?>

