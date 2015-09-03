<?php echo $this->Html->scriptBlock('
					function submitData(){
						var is_required = 0;
						var fieldId=$(\'#fieldId\').val();
						var fieldTypeId=$(\'#fieldTypeId\').val();
						var label=$(\'#field_label\').val();
						var description=$(\'#field-description\').val();
						if($(\'#field-required\').attr(\'checked\')=="checked"){
							var is_required= 1;
						}
						if($(\'#fieldLabelDisplay\').attr(\'checked\')=="checked"){
							var display_label= 1;
						}
						var status=$("input:radio[name=\'data\\[status\\]\']:checked").val();
						var optionstr=\'\';
						var optionsArray=[];
						var tempArray=[];
						var tempHolder = new Array();
						//obj=$("input:radio[name=\'radio_options\\[\\]\']");
						obj=$(".fields input:text[name=\'options_label\\[\\]\']");
						if(obj){
							var j=1;
					   		$(obj).each(function (i,val){
					   			 /*optionstr+=\'option[\'+j+\'][label]=\'+$(this).val();
					   			 optionstr+=\'option[\'+j+\'][base]=\'+$(this).prev().attr(\'checked\');*/
					   			 var temp=new Object();
					   			 temp.label=$(this).val();
					   			 if($(this).prev().attr(\'checked\')){
					   			 	temp.checked=\'checked\';
					   			 }
					   			 else{
					   			 		temp.checked=\'undefined\';
					   			 }
					   			if($(\'#radio_textbox_\'+j).attr(\'checked\')=="checked") 
					   				temp.others_options =$(\'#radio_textbox_\'+j).val();
					   			else if($(\'#radio_textarea_\'+j).attr(\'checked\')=="checked") 
					   				temp.others_options =$(\'#radio_textarea_\'+j).val();
				
					   			 optionsArray.push(temp); 
					   			
					   			 j=j+1;
								});
							}
							
							obj=$(".fields input:hidden[name=\'options_label\\[\\]\']");
							$(obj).each(function (i,val){
					   			 var temp=new Object();
					   			 temp.label=$(this).val();
					   			 optionsArray.push(temp);
					   			 j=j+1;
								});
							
							
								var htmlInput = new Object();
								 htmlInput.input = $("#html_input_edit").val();
								 if(htmlInput!="")
								 optionsArray.push(htmlInput);
								 

						  $.ajax({
				                type: "POST",
				                url:"/fields/edit/",
				                data:{\'fieldId\':fieldId,\'fieldTypeId\':fieldTypeId,\'field_label\':label,\'status\':status,\'is_required\':is_required,\'is_display_label\':display_label,\'description\':description,\'option\':optionsArray},
				                success: function(msg){
				                	var obj = jQuery.parseJSON(msg);
				                	if(obj.success){ $.colorbox.close();
				                		$(".alert").hide();
					               		$(".alert-success").show();
							        	$("#success").html("Fields updated succesfully");
							        	var selectedType=$("input:radio[name=type]:checked").val();
										var exp = $("#search").val();
										$(\'.selected-user\').html(label);
										if($("#formList").val()!=""){
        			 						formId = $("#formList").val();
         								}
         								//refresh dtble
										getAssociatedFieldsData(formId);

							        }
							        else{
							       		
							        	$(".alert").show();
								        $("#warning").html(obj.errors);
								        $(".alert-success").hide();
							        }	
					               }
						        });		
		}
			');
?>
		
