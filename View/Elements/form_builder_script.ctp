<script type="text/javascript" >
	$(document).ready(function() {
		
		$(".load-name").css("background","none");
		$(".load-name").css("padding-left","0");
		 /*show hide user associated in default team*/
		$("#hideUser").click(function(){
			var allEditIds = [];
			var valueToPush = new Array();	
			var formId = $("#formId").val();
				$("#formTeam.datatable input:checked[id^=\"add\"]").each(function() {
					arrayVal = valueToPush[i];
					arr = $(this).attr("id").split("-");
					allEditIds.push({userId:arr[arr.length-1],role:$(this).val()});
				});
				//console.log(allEditIds);
				$.ajax({
	   				type: "POST",
	   				url: "/campaigns/campaign_requests/operateDefaultTeamFlagWise/",
	   				data : {userRole:allEditIds,formId:formId,operate:"hide"},
	   				success: function(data) {	
	   				getDefaultTeam(formId);
	   				}
	   				});	
		});
		/*ends here*/
		/*show hide user associated in default team*/
		$("#showUser").click(function(){
			var allEditIds = [];
			var valueToPush = new Array();	
			var formId = $("#formId").val();
				$("#formTeam.datatable input:checked[id^=\"add\"]").each(function() {
					arrayVal = valueToPush[i];
					arr = $(this).attr("id").split("-");
					allEditIds.push({userId:arr[arr.length-1],role:$(this).val()});
				});
				//console.log(allEditIds);
				$.ajax({
	   				type: "POST",
	   				url: "/campaigns/campaign_requests/operateDefaultTeamFlagWise/",
	   				data : {userRole:allEditIds,formId:formId,operate:"show"},
	   				success: function(data) {	
	   				getDefaultTeam(formId);
	   				}
	   				});	
		});
		/*ends here*/
		/*delete user associated in default team*/
		$("#deleteUser").click(function(){
			var allEditIds = [];
			var valueToPush = new Array();	
			var formId = $("#formId").val();
				$("#formTeam.datatable input:checked[id^=\"add\"]").each(function() {
					arrayVal = valueToPush[i];
					arr = $(this).attr("id").split("-");
					allEditIds.push({userId:arr[arr.length-1],role:$(this).val()});
				});
				//console.log(allEditIds);
				$.ajax({
	   				type: "POST",
	   				url: "/campaigns/campaign_requests/operateDefaultTeamFlagWise/",
	   				data : {userRole:allEditIds,formId:formId,operate:"delete"},
	   				success: function(data) {	
	   				getDefaultTeam(formId);
	   				}
	   				});	
		});
		/*ends here*/
		// For checkbox
    		$("#select_all_campaign_dataTable").click(function(){
    			if($("#select_all_campaign_dataTable").is(":checked"))
				{
				 	$("#extended-data-form.datatable :checkbox").each(function() {
				 		
			     		$(this).attr("checked",true);
			     		
					});
				}
				else
				{
					$("#extended-data-form.datatable :checkbox").each(function() {
			     		$(this).attr("checked",false);
			     	});
				}						
			});
		$(".formbox-tabs ul").tabs("div.tab-stage > div", {tabs: 'li'});
		  FormValidation = OMCakeFormHandler.clone();
			FormValidation.init({form: "Form", submitButton:"addForm"});
			FormValidation.success = function (msg) {
				if(msg.success){
					$(".alert-success").show();
					if(msg.add){
						$("#success").html("Form Added Succesfully...");
				    }
				    else{
				    	$("#success").html("Form Updated Succesfully...");
				    }
				    $.colorbox.close();
				   
				}
			}
			function getFormList(){ 
				  var cdata = {category:$('#form_type_filter').val(), status:$('#form_status_filter').val(),formString:$('#filter').val()};
					$.ajax({
				        type: "POST",
				        url: "/form/",
				        dataType: 'json',
				        data: cdata,
						beforeSend:function(){
							$("#loading-name").addClass("load-name");
							$(".load-name").text("<?php echo __('Retrieving Data'); ?>...");
					    },
					    success: function(msg){
					    			$(".load-name").text("*Please select a form from the left panel to view it's details.");
					    			$("#formList").find('option').remove();
				            		var htmlOptions = "";
				            		if(msg){
				            			$(msg).each(function(i,formInfo){
											htmlOptions += '<option   value="'+formInfo.Form.id+'">'+formInfo.Form.label+'</option>';
					            		});
					            		
					            	}
				            		$("#formList").html(htmlOptions);

							}
					});
				}
		  	$('#search_filters').click(function(){
		  		getFormList();
				
			});	
		  $('#reset_filters').click(function(){
			 
			  $('#filter').val("");
			  $('#form_type_filter').val("");
			  $('#form_status_filter').val("");
			  $("#category_filter").find("span").html("All Categories");
	          $("#status_filter").find("span").html("All Status");
			  getFormList();
			  
		  });
		
		
		  
		  showForms();
		  $("#formList").live("click", function(){
				var form_id_selected	= $(this).val();
				$(".tab-stage").show();
				get_details(form_id_selected);
				getAssociatedFieldsData(form_id_selected);
				getDefaultTeam(form_id_selected);
			});
		  function get_details(id){
				$("#role-details").html("");
				$.ajax({
			        type: "POST",
			        url: "/getFormData/" + id,
			        dataType: 'json',
					beforeSend:function(){
						// Retrieving information
						$("#edit-role-link").hide();
						$("#loading-name").addClass("load-name");
						$(".load-name").removeAttr("style").text("<?php echo __('Retrieving Data'); ?>...");
				    },
				    complete:function(){
						var role_name = $("#formList option:selected").text();
						$(".load-name").removeAttr("style").text(role_name);
						$("#loading-name").removeClass("load-name");
						$("#edit-role-link").show();
				    },
			        success: function(obj){
					    	$("#label").val(obj.form.Form.label);
					    	$("#descriptionForm").val(obj.form.Form.description);
					    	$("#formCategory option:selected").val(obj.form.Form.category_id);
					    	$("#distribution_list").val(obj.form.Form.distribution_list);
					    	//$("#formStatus option:selected").val(obj.form.Form.status);
					    	$("#FormId").val(obj.form.Form.id);
					    	if(obj.form.Form.is_public)
					    	$("#Settings"+obj.form.Form.is_public).attr("checked",true);
					    	
					    	options = "";
					    	optionsStatus = "";
					    	$("#formCategory").html();
					    	jQuery.each(obj.category, function(i, val) {
						    	options += "<option value=" + i + ">" + val + "</option>";
						    });
						    $("#formCategory").html(options);
						    $("#formCategory").val(obj.form.Form.category_id);
						    $("#formStatus").html();
						    	optionsStatus += "<option value=''>Select Status</option>";
						    jQuery.each(obj.currentStatus, function(i, val) {
							    	optionsStatus += "<option selected = 'selected' value=" + i + ">" + val + "</option>";
							    });	
						    jQuery.each(obj.status, function(i, val) {
						    	optionsStatus += "<option value=" + val.StateEngine.status_code + ">" + val.StatusCode.label + "</option>";
						    });
						    $("#formStatus").html(optionsStatus);
						    
						   
						}
				});
			}
			// To delete form data
			$("#delete_form").click(function (){	
				if($("#formList").val()!=""){
						formId = $("#formList").val();
					}
				$(".status-success").hide();	
				var allVals = [];
				$("#extended-data-form input:checked[id^=\"add\"]").each(function() {					
				    allVals.push($(this).val());
				});
					 var cdata = {from:"central", field_val_id:allVals,type:"form"};
	            $.ajax({
	                type: "POST",
	                url:"/form_builders/form_field_joins/delete/",
	                data: cdata,
	                beforeSend:function(){
	                	$(".alert-success").show();
				        $("#success").html("Deleting  Fields, please wait...");
				    },
				   complete:function(){
				        $(".status").hide();
				    },
	                success: function(msg){
	                 getAssociatedFieldsData(formId);
			      		statusMessages("Fields deleted Successfully.");
	                }
	            });		
			}); // delete
			
			// To change status from Inactive to active for form
			$("#change_to_active_form").click(function (){	
				$(".status-success").hide();			
				var allVals = [];
				$("#extended-data-form.datatable input:checked[id^=\"add\"]").each(function() {					
				    allVals.push($(this).val());
				     var row = $(this).parents("tr:first");
				     var getSelectData = $("#extended-data-form").dataTable().fnGetData(row[0]).slice(0); 
				         getSelectData[4]= "active";
				    $("#extended-data-form").dataTable().fnUpdate(getSelectData , row[0], 0, false, false);  
				    
				});
				
				if(allVals.length == 0) {
	         		statusMessages("Please select at least one field.");
	      		}else{
				
					var cdata = {arrayallval:allVals,column:4,updateTo:"active"};
		            $.ajax({
		                type: "POST",
		                url:"/form_builders/form_field_joins/edit",
		                data: cdata,
		                beforeSend:function(){
		                	$(".alert-success").show();
					        $("#success").html("Updating field status, please wait...");
					    },
					   complete:function(){
					        $(".status").hide();
					    },
		                success: function(msg){
		                
				      		statusMessages("Field status updated successfully.");
		                }
		            });	
	            }	
			}); // delete 
			
			// To change status from active to Inactive
			$("#change_to_inactive_form").click(function (){	
				$(".status-success").hide();			
				var allVals = [];
				$("#extended-data-form.datatable input:checked[id^=\"add\"]").each(function() {					
				    allVals.push($(this).val());
				      var row = $(this).parents("tr:first");
				     var getSelectData = $("#extended-data-form").dataTable().fnGetData(row[0]).slice(0); 
				         getSelectData[4]= "inactive";
				    $("#extended-data-form").dataTable().fnUpdate(getSelectData , row[0], 0, false, false);  
				});
				if(allVals.length == 0) {
	         		statusMessages("Please select at least one field.");
	      		}else{
					var cdata = {arrayallval:allVals,column:4,updateTo:"inactive"};
		            $.ajax({
		                type: "POST",
		                url:"/form_builders/form_field_joins/edit",
		                data: cdata,
		                beforeSend:function(){
		                	$(".alert-success").show();
					        $("#success").html("Updating  field status, please wait...");
					    },
					   complete:function(){
					        $(".status").hide();
					    },
		                success: function(msg){
		                 
				      		statusMessages("Field status updated successfully.");
		                }
		            });	
	            }	
			}); 
			//To change status on category change
			$("#formCategory").change(function(){
				var categoryId = $("#formCategory").val();
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
			            		$("#formStatus").show();
			           			$("#formStatus").html();
			           			optionsStatus = "";
						    	optionsStatus += "<option value=''>Select Status</option>";
						    	jQuery.each(obj.status, function(i, val) { 
						    		optionsStatus += "<option value=" + val.StateEngine.status_code + ">" + val.StatusCode.label + "</option>";
							   	});
						    	$("#formStatus").html(optionsStatus);
						   	}
					   	});
					});
				
			$(".advanced_options").click(function(){
        		if($(".show_all_filters").hasClass('hide')){
        			$(".advanced_options").html('Hide Advanced Options');
        			$(".show_all_filters").removeClass('hide');
        		}
        		else{
        			$(".advanced_options").html('Advanced Options');
        			$(".show_all_filters").addClass("hide");
        		}
        	});
	

	});


	function showForms(){
		$("#formList").remove();
		$("#static_formList").clone().appendTo(".name-big-list").show().attr("id", "formList");
	}

	/*function filter(query){
		  query	=	$.trim(query); //trim white space
		  query = query.replace(/ /gi, '|'); //add OR for regex query

		  $("#formList option").each(function() {
		    ($(this).text().search(new RegExp(query, "i")) < 0) ? $(this).remove() : $(this).show();
		  });
	}*/
	   //function for fetching fields associated to campaign types
	function getAssociatedFieldsData(formId){
	$("#brief-table").hide();
	$("#extended-data-campaign").show();
	$("#add_form").attr("href","/fields/add/form/"+formId);
	$("#previewForm").attr("href","/projects/fields/form_request_preview/"+formId);
	$("#list_exisiting_field").attr("href","/projects/fields/getExistingListOfFields/form/"+formId);
	$.ajax({
	   type: "GET",
	   url: "/projects/fields/form/"+formId+"/form/",
	   success: function(data) {		
	   			   		
	   		var obj = jQuery.parseJSON(data);
	   		// Add data into project item extended data tab
			$("#camapaignTypeId").val("");
			$("#extended-data-form").dataTable().fnClearTable();
				if(!jQuery.isEmptyObject(obj.ExtendedData)){ 
					$("#warningNoField").hide();
					
					/* Init DataTables */
					var crTable = $("#extended-data-form").dataTable();
					jQuery.each(obj.ExtendedData, function(i, val) {
						var b = crTable.dataTable().fnAddData(
						["<input type=checkbox id=add-"+val.FieldJoin.id+" value="+val.FieldJoin.id+" />", 
						val.Field.label, 
						val.FieldType.label, 
						val.Field.options, 
						val.FieldJoin.status,
						val.FieldJoin.weight
						,"<input class=up type=button  onclick= moveSelected(this) id= "+val.FieldJoin.id+" value=Up ><input class=down  type=button  onclick= moveSelected(this) id= "+val.FieldJoin.id+" value=Down >",
						
						"<a href=\'/projects/fields/editCampaignField/"+val.Field.id+"\'class=\'link-pop\'>Edit</a> "+val.FieldType.fieldset+""]);
						var oSettingscr = crTable.fnSettings();
						
						var nTr = oSettingscr.aoData[b[0]].nTr;
						$(nTr).attr("id",val.Field.id);	
						
					});
					
					
					
				}else{
					$("#warningNoField").show();
				} // Add data into project item extended data tab end
			
	   }
	});
}

	
function getDefaultTeam(formId){
		//alert($('#formCategory option:selected').text());
		//$("#brief-table").hide();
		//$("#extended-data-campaign").show();//716/196/brief_editor/
		$("#brief_approver").attr("href","/campaigns/campaign_requests/addDefaultTeam/"+formId+"/brief_approver");
		$("#brief_editor").attr("href","/campaigns/campaign_requests/addDefaultTeam/"+formId+"/brief_editor");
		$("#brief_watcher").attr("href","/campaigns/campaign_requests/addDefaultTeam/"+formId+"/brief_watcher");
		$("#formId").val(formId);
		$.ajax({
	   type: "GET",
	   url: "/campaigns/campaign_requests/getDefaultTeam/"+formId,
	   success: function(data) {		
		   $("#defaultTeam").show();
		   $(".add-default-team-element").show();
	   		var obj = jQuery.parseJSON(data);
	   		if(obj){
	   		// Add data into project item extended data tab
	   		$("#formTeam").dataTable().fnClearTable();

			var crTable = $("#formTeam").dataTable();
			
			var r='<select><option value="">All</option>';
			
			for(i in obj)
			{
				var temp = i.replace("_"," ");
				if(obj[i] instanceof Object)
				{
					
					jQuery.each(obj[i], function(j, val) { 
						var b = crTable.dataTable().fnAddData(
						["<input type=checkbox id=add-"+val.userId+" value="+i+" >", 
						val.name, 
						 temp,visibility(val.isHide)]
						 );
						var oSettingscr = crTable.fnSettings();
						
					});
				}
				
				r += '<option value="'+temp+'">'+temp+'</option>';
				
					
				
			
			}
			r+'</select>';

			var selectObj=$('#formTeam_filter');
			if(selectObj.find('select'))
			selectObj.find('select').remove();	
			
			selectObj.find('label').before(r);
   			$('select', selectObj).change( function () {
   				crTable.fnFilter( $(this).val(), 2 );
   			} );
			
	   	}else{
				$("#defaultTeam").hide();
				$(".add-default-team-element").hide();
				
				}
		}
	});
		
	}
	
	function visibility(val)
	{
		var retrun='Show';
		if(val==1)
		retrun='Hide';	

		return retrun;
	}
	
	 
</script>