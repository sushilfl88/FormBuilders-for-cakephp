<?php 
echo $this->Html->scriptBlock('
		$(document).ready(function() {	
		if($("#formList").val()!=""){
						formId = $("#formList").val();
		}
		clientId = $("#avlClientList").val();	
		 	FormValidation = OMCakeFormHandler.clone();
			FormValidation.init({form: "NewForm", submitButton:"addNewForm"});
			FormValidation.success = function (msg) {
				if(msg.success){
					$(".alert-success").show();
					if(msg.add){
				    	$("#success").html("Form Added Succesfully...");
				    	 $.colorbox.close();
				    	 getAssociatedForms(clientId);
				    }
				    else{
				    	$("#success").html("Form Updated Succesfully...");
				    	 $.colorbox.close();
				    }
				   		   
				}
			}	
		 });
		 ');
?>
<script>
$("#formCategoryNew").change(function(){
	var categoryId = $("#formCategoryNew").val();
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
            		$("#formStatusNew").show();
           			$("#formStatusNew").html();
           			optionsStatus = "";
			    	optionsStatus += "<option value=''>Select Status</option>";
			    	jQuery.each(obj.status, function(i, val) { 
			    		optionsStatus += "<option value=" + val.StateEngine.status_code + ">" + val.StatusCode.label + "</option>";
				   	});
			    	$("#formStatusNew").html(optionsStatus);
			   	}
		   	});
		});
</script>
<div class="modal modal-campaignType">
	<div class="modal-header">
	  <h3><?php echo __('Update Form');?></h3>
	</div>
	<?php echo $this->Form->create('Form',array('url' => $url,'id'=>'NewForm','class'=>'ajax form-horizontal')); ?>
	<?php echo $this->Form->input('id',array('type'=>'hidden','value'=> $id));?>  
	<fieldset>
		<div class="modal-body">
 	        <div class="control-group">
		      	<label class="control-label"><?php echo __('Name');?></label>
		 			<div class="controls">
		 				
		          		<?php echo $this->Form->input('label',array('id'=>'labelNew','label'=>false,'value'=> $label));?>
		          		
		        	</div>
		     </div>
     		<div class="control-group">
		      	<label class="control-label"><?php echo __('Form Category');?></label>
		 			<div class="controls">
		          		<?php echo $this->Form->input('category_id',array('id'=>'formCategoryNew','type'=>'select','label'=>false,'options'=>$listOfCategories,'value'=>$categoryData,'empty'=>'select category'));?>
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
          		<div class="controls item-radio rate-card-radio" id="status">
          			<?php echo $this->Form->input('status',array('id'=>'formStatusNew','type'=>'select','label'=>false,'options'=>$listOfStatus,'value'=>$currentStatus,'style'=>''));?>
          			</div>
      		</div>
      		<div class="control-group">
		      <label class="control-label" style="padding-top:0px;"><?php echo __('Form Visibility');?></label>
		      <div class="controls item-radio rate-card-radio">
		       <?php 	$options = array("0" => ' Private',"1" => ' Public' );
						echo $this->Form->radio('is_public',$options,array('legend'=>false,'id'=>'settings','value'=>$is_public)); ?>
		      </div>
		   </div>
           <div class="control-group">
      			<label class="control-label"></label>
      				<div class="controls"><button class="btn btn-primary" id="addNewForm"><?php echo __('Save Changes');?></button></div>
      		</div>  
    </fieldset>
   <?php echo $this->Form->end(); ?>	
</div> 