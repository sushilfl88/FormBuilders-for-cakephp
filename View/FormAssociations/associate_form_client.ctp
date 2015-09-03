<script>
   $.fn.colorbox.resize({});
   var selectedforms = [];
   var selectedclients = [];
   $("#addAssociation").click(function (){
   		$(".status-success").hide();
   		var allFormsVals = [];
   		var allClientsVals =[];
   		$("#allFormList input:checked").each(function() {
   	     	allFormsVals.push($(this).val());
   	 	});
   	 	if(allFormsVals.length === 0){
	   		$(".popup-success").hide();
	   		$(".popup-alert").show();
	   		$("#popupwarning").html("Select atleast one form.");
	   		$.fn.colorbox.resize({});
	   		return false;
   	 	}
   	  
	   	$("#allClientsList input:checked").each(function() {
	   		allClientsVals.push($(this).val());
	   	});
   	if(allClientsVals.length === 0){
   		$(".popup-success").hide();
   		$(".popup-alert").show();
   		$("#popupwarning").html("Select atleast one client.");
   		$.fn.colorbox.resize({});
   		return false;
   	 }
   	var cdata = {forms:allFormsVals ,clients:allClientsVals,associate:'save'};
       $.ajax({
           type: "POST",
           url:"/form_builders/form_associations/associateFormClient/",
           data: cdata,
           beforeSend:function(){
       		$(".popup-success").hide();
   	        $(".popup-alert").show();
   			$("#popupwarning").html("Associating forms, please wait...");
   			$.fn.colorbox.resize({});
   	    },
           success: function(msg){
   			if(msg == 1){
           		statusMessages("Association added successfully.");
           		$.fn.colorbox.close();
   			}
   			$(".popup-alert").hide();
   		}
       });
      
   });
   $("#deleteAssociation").click(function (){
	   $(".status-success").hide();
	   var allFormsVals = [];
	   var allClientsVals =[];
	   $("#allFormList input:checked").each(function() {
	   	allFormsVals.push($(this).val());
	   });
	   if(allFormsVals.length === 0){
		   $(".popup-success").hide();
		   $(".popup-alert").show();
		   $("#popupwarning").html("Select atleast one Form.");
		   $.fn.colorbox.resize({});
		   return false;
	   }
   
	   $("#allClientsList input:checked").each(function() {
	   	allClientsVals.push($(this).val());
	   });
	   if(allClientsVals.length === 0){
		   $(".popup-success").hide();
		   $(".popup-alert").show();
		   $("#popupwarning").html("Select atleast one client.");
		   $.fn.colorbox.resize({});
		   return false;
	   }
	   var cdata = {forms:allFormsVals ,clients:allClientsVals,associate:'delete'};
	   if (confirm('Forms and clients association you wish to delete may be associated with forms.Do you want to procced?')) {
		   $.ajax({
			   type: "POST",
			   url:"/form_builders/form_associations/associateFormClient/",
			   data: cdata,
			   beforeSend:function(){
				   $(".popup-success").hide();
				   $(".popup-alert").show();
				   $("#popupwarning").html("Deleting associated forms, please wait...");
				   $.fn.colorbox.resize({});
			   },

			   success: function(msg){
			   if(msg == 1){
				   statusMessages("Association deleted successfully.");
				   $.fn.colorbox.close();
			   }
			   }
		   });
	   }else{
	  	 return false;
	   }
   
   });
   $("#search_forms").keyup(function(){
	   searchExp = $('#search_forms').val();
	   categoryId = $('#formCategorySelect').val();
	   $('#allFormList').html('');
	   var Data = {searchexp:searchExp,categoryId:categoryId,selectedforms:selectedforms.join(',')};
	   $.ajax({
		   type: 'POST',
		   url: '/form_builders/forms/searchForms/',
		   data: Data,					
		   success: function(data){
		   var obj = jQuery.parseJSON(data);
		   var optionsforms = '';
		   if(obj.selected){
			   $.each(obj.selected, function(i, val) {
			   if(i != 0)
			  	 optionsforms += '<label class = "checkbox"><input id = "'+ val +'" type = "checkbox" value  =  "' + i + '" name = "data[Form][id][]" onclick = "toggleCheckedform(this)" checked /> '+ val + '</label>';
			   });
		   }
		   if(obj.all){
			   $.each(obj.all, function(i, val) {
			   if(i != 0)
				   optionsforms += '<label class = "checkbox"><input id = "'+ val +'" type = "checkbox" value = "' + i + '" name = "data[Form][id][]" onclick = "toggleCheckedform(this)"  /> '+ val + '</label>';
			   });
		   }
		   $('#allFormList').html(optionsforms);
		   }
	   });	
   
   });
   $("#search_clients").keyup(function(){
	   searchExp = $('#search_clients').val();
	   $('#allClientsList').html('');
	   var Data = {searchexp:searchExp,selectedclients:selectedclients.join(',')};
	   $.ajax({
		   type: 'POST',
		   url: '/access_controls/hierarchy_values/searchClients/',
		   data: Data,					
		   success: function(data){
		   	   var obj = jQuery.parseJSON(data);
			   var optionsclients = '';
			   if(obj.selected){
				   $.each(obj.selected, function(i, val) {
					   optionsclients += '<label class = "checkbox"><input id = "'+ val +'" type = "checkbox" value = "' + i + '" name = "data[hierarchy_value][id][]" onclick = "toggleCheckedClients(this)" checked /> '+ val + '</label>';
				   });
			   }
			   if(obj.all){
				   $.each(obj.all, function(i, val) {
					   optionsclients += '<label class = "checkbox"><input id = "'+ val +'" type = "checkbox" value  =  "' + i + '" name = "data[hierarchy_value][id][]" onclick = "toggleCheckedClients(this)"  /> '+ val + '</label>';
				   });
			   }
			   $('#allClientsList').html(optionsclients);
		   }
	   });
   
   });
   $("#formCategorySelect").change(function(){ 
	   categoryId = $('#formCategorySelect').val();
	   $('#allFormList').html('');
	   $('#search_forms').val('');
	   var Data = {categoryId:categoryId};
		   $.ajax({
			   type: 'POST',
			   url: '/form_builders/form_associations/associateFormClient/',
			   data: Data,					
			   success: function(data){
			   $.fn.colorbox.resize({});
			   var obj = jQuery.parseJSON(data);
			   var optionsform = '';
			   $.each(obj, function(i, val) {
				   optionsform += '<label class="checkbox"><input id="'+ val +'" type="checkbox" value = "' + i + '" name="data[Form][id][]" onclick="toggleCheckedclass(this)"  /> '+ val + '</label>';
			   });
			   $('#allFormList').html(optionsform);
			   }
		   });
   });
   function toggleCheckedform(id) {
   	var val = $(id).attr('value');
   	if($(id).is(":checked")) {
   		selectedforms.push(val);
   	}
   	else{
   		for(var i = 0; i<selectedforms.length; i++) {
   	        if(selectedforms[i] == val) {
   	        	selectedforms.splice(i, 1);
   	            break;
   	        }
   	    }
   	}
   }
   function toggleCheckedClients(id) {
   	var val = $(id).attr('value');
   	if($(id).is(":checked")) {
   		selectedclients.push(val);
   	}
   	else{
   		for(var i = 0; i<selectedclients.length; i++) {
   	        if(selectedclients[i] == val) {
   	        	selectedclients.splice(i, 1);
   	            break;
   	        }
   	    }
   	}
   }
   $("#check_all_forms").click(function(){
   	if($("#check_all_forms").is(":checked")){
   		$("#allFormList :checkbox").each(function() {
   			var val=$(this).val();
   			$(this).attr("checked",true);
   			selectedforms.push(val);
   		}); 
   	}	
   	else{
   		$("#allFormList :checkbox").each(function() {
   			var val=$(this).val();
   			$(this).removeAttr("checked");
   			for(var i=0; i<selectedforms.length; i++) {
   		        if(selectedforms[i] == val) {
   		        	selectedforms.splice(i, 1);
   		        	break;
   		        }
   		    }
   		}); 
   	}
   });
   $("#check_all_clients").click(function(){
   	if($("#check_all_clients").is(":checked")){
   		$("#allClientsList :checkbox").each(function() {
   			var val=$(this).val();
   			$(this).attr("checked",true);
   			selectedclients.push(val);
   		}); 
   	}	
   	else{
   		$("#allClientsList :checkbox").each(function() {
   			var val=$(this).val();
   			$(this).removeAttr("checked");
   			for(var i=0; i<selectedclients.length; i++) {
   		        if(selectedclients[i] == val) {
   		        	selectedclients.splice(i, 1);
   		        	break;
   		        }
   		    }
   		}); 
   	}
   });
</script>
<div class = "modal multi-spec-class-modal">
   <div class = "modal-header">
      <h3><?php echo __('Multiple Form Client Association'); ?></h3>
   </div>
   <div class = "modal-body">
      <div class = "popup-alert" style = "display:none">
         <a href = "#" class = "close" data-dismiss = "alert">&times;</a>
         <span id = "popupwarning"></span>
      </div>
      <div style = "display:none" class = "popup-success">
         <a data-dismiss = "alert" class = "close" href = "javascript: void(0)">&times;</a>
         <span id = "successPop"></span>
      </div>
      <fieldset class = "form-horizontal">
         <div class = "searchbx" id = "service-filter-container"><label><?php echo __('Category');?></label>
            <?php 
               echo $this->Form->input('category',  array('type'  => 'select',  'div'  => false,  'label'  => false,  'options'  => $categories, 'empty'     =>   'All',   'id'   =>  'formCategorySelect', 'default'  => '0'));
               ?>
         </div>
         <br/>
         <div class = "avail-col avail-clients">
            <div class = ""><?php echo __('Available Forms');?></div>
            <div class = "searchbx">
               <?php 
                  echo "<label class = 'checkbox'>";
                  echo $this->Form->input('Check All',  array('type'  => 'checkbox', 'hiddenField'  => false, 'div'  => false, 'label'  => false, 'name'  => 'check_all', 'id'  => 'check_all_forms', 'class'  => 'margin-specs-project-items'));
                  echo "</label>";
                  ?>
               <?php 
                  echo $this->Form->input('Filter', array('type'  => 'text', 'hiddenField'  => false, 'div'  => false, 'label'  => false, 'name'  => 'search_forms', 'id'  => 'search_forms')); 
                  ?>
            </div>
            <div class = "multi-check-spec multi-check-classification" id = "allFormList">
               <?php 
                  foreach($listOfForms as $formKey  => $formVal){
                  
                  ?>
               <label class = "checkbox"><input type = "checkbox" id = <?php echo $formVal; ?> value = <?php echo $formKey;?> name = "data[SpecKeyJoin][deliverable_classification_id][]" onclick  = "toggleCheckedform(this)"><?php echo $formVal; ?></label> 
               <?php  }
                  ?>
            </div>
         </div>
         <div class = "avail-col avail-role">
            <div class = ""><?php echo __('Available Clients');?></div>
            <div class = "searchbx">
               <?php 
                  echo "<label class = 'checkbox'>";
                  echo $this->Form->input('Check All',  array('type'  => 'checkbox', 'hiddenField'  => false, 'div'  => false, 'label'  => false, 'name'  => 'check_all', 'id'  => 'check_all_clients', 'onclick'  => 'toggleCheckedClients(this);', 'class'  => 'margin-specs-project-items'));
                  echo "</label>";
                  ?>
               <?php 
                  echo $this->Form->input('Filter', array('type'  => 'text', 'hiddenField'  => false, 'div'  => false, 'label'  => false, 'name'  => 'search_clients', 'id'  => 'search_clients', 'onclick'  => 'toggleCheckedClients(this);')); 
                  ?>
            </div>
            <div class = "multi-check-spec multi-check-classification" id = "allClientsList">
               <?php 
                  foreach($listOfClients as $clientKey  => $clientVal){?>
               <label class = "checkbox"><input type = "checkbox" id = <?php   echo $clientVal; ?> value = <?php echo $clientKey;?> name = "data[hirearchy_value][id][]" onclick = "toggleCheckedClients(this)"><?php  echo $clientVal; ?></label>
               <?php }?>
            </div>
         </div>
      </fieldset>
   </div>
   <div class = "modal-footer"><button class = "btn btn-primary" id = "addAssociation"><?php echo __('Add Association');?></button>
      <button class = "btn btn-primary" id = "deleteAssociation"><?php echo __('Remove Association');?></button>
   </div>
</div>


