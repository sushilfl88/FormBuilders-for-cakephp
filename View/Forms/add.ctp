<?php 
echo $this->Html->scriptBlock('
		$(document).ready(function() {	
		
		clientId = $("#avlClientList").val();	
		 	FormValidation = OMCakeFormHandler.clone();
			FormValidation.init({form: "NewForm", submitButton:"addNewForm"});
			FormValidation.success = function (msg) {
				if(msg.success){
					$(".alert-success").show();
					if(msg.add == "clientProfile"){
				    	$("#success").html("Form Added Succesfully...");
				    	 getAssociatedForms(clientId);
				    }
				    else if(msg.add == "global"){
				    	$("#success").html("Form Added Succesfully...");
				    	location.reload(true);
				    }
				    $.colorbox.close();
				    
				    			   
				}
			}	
			
			
			
		 });
		  
		   
		 ');
?>
<script>
$("#category_id").change(function(){
	var categoryId = $("#category_id").val();
	var cdata = { categoryId:categoryId };
       $.ajax({
            type: "POST",
            url:"/form_builders/forms/getFormStatus/",
            dataType: 'json',
            data: cdata,
            beforeSend:function(){
            
		    },
		   complete:function(){
		    
		    },
            success: function(obj){
            		$("#formStatusNew #status").show();
           			$("#formStatusNew #status").html();
           			optionsStatus = "";
			    	optionsStatus += "<option value=''>Select Status</option>";
			    	jQuery.each(obj.status, function(i, val) { 
			    		optionsStatus += "<option value=" + val.StateEngine.status_code + ">" + val.StatusCode.label + "</option>";
				   	});
			    	$("#formStatusNew #status").html(optionsStatus);
			   	}
		   	});
		});
</script>
<div class="modal modal-campaignType">
	<div class="modal-header">
	  <h3><?php if($edit){ echo __('Update '); }else { echo __('Create New '); } echo __('Form');?></h3>
	</div>
	<?php echo $this->Form->create('Form',array('url' =>$url,'id'=>'NewForm','class'=>'ajax form-horizontal')); ?>  
	<fieldset>
		<div class="modal-body">
 	        <div class="control-group">
		      	<label class="control-label"><?php echo __('Name');?></label>
		 			<div class="controls">
		 				<?php 
		 					$label='';
		 					$dl='';
		 					$status='';
		 					$description = '';
		 					if(isset($campaignTypeData['Form']['label'])){
		 						$label=$campaignTypeData['Form']['label'];	
		 					}
		 					if(isset($campaignTypeData['Form']['distribution_list'])){
		 						$dl=$campaignTypeData['Form']['distribution_list'];	
		 					}
		 					if(isset($campaignTypeData['Form']['status'])){
		 						$status=$campaignTypeData['Form']['status'];	
		 					}	
		 					if(isset($campaignTypeData['Form']['description'])){
		 						$description=$campaignTypeData['Form']['description'];	
		 					}		 				
		 				?>
		          		<?php echo $this->Form->input('label',array('id'=>'label','label'=>false,'value'=>$label));?>
		          		
		        	</div>
		     </div>
		    <div class="control-group">
		      	<label class="control-label"><?php echo __('Description');?></label>
		 			<div class="controls">
		          		<?php echo $this->Form->input('description',array('id'=>'descriptionNewForm','label'=>false,'value'=>$description));?>
		        	</div>
		     </div>
     		<div class="control-group">
		      	<label class="control-label"><?php echo __('Form Category');?></label>
		 			<div class="controls">
		          		<?php echo $this->Form->input('category_id',array('id'=>'category_id','type'=>'select','label'=>false,'options'=>$listOfCategories,'value'=>'','empty'=>'select category'));?>
		        	</div>
		     </div>
 	        <div class="control-group">
		      	<label class="control-label"><?php echo __('Email Distribution List');?></label>
		 			<div class="controls">
		          		<?php echo $this->Form->input('distribution_list',array('id'=>'distribution_list','label'=>false,'value'=>$dl));?>
		        	</div>
		     </div>
     
    		<div class="control-group"  id="statusDiv" >   
          		<label class="control-label"><?php echo __('Status');?></label>
          		<div class="controls item-radio rate-card-radio" id="formStatusNew">
          			<?php echo $this->Form->input('status',array('id'=>'status','type'=>'select','label'=>false,'options'=>'','value'=>'','style'=>'display:none'));?>
          			<?php /*	$options = array( 'active' => 'Active', 'inactive' => 'Inactive');
          			echo $this->Form->input('status',array('options'=>$options,'type'=>'radio','width'=>'15px','legend'=>false,'id'=>'status','default'=>$status)); */?>
          		</div>
      		</div>
      		<div class="control-group">
		      <label class="control-label" style="padding-top:0px;"><?php echo __('Form Visibility');?></label>
		      <div class="controls item-radio rate-card-radio">
		       <?php 	$options = array("0" => ' Private',"1" => ' Public' );
						echo $this->Form->radio('is_public',$options,array('legend'=>false,'id'=>'settings','value'=>1)); ?>
		      </div>
		   </div>
           <div class="control-group">
      			<label class="control-label"></label>
      				<div class="controls"><button class="btn btn-primary" id="addNewForm"><?php echo __('Save Changes');?></button></div>
      		</div>  
    </fieldset>
   <?php echo $this->Form->end(); ?>	
</div> 