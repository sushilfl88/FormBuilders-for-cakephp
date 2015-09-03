<?php 
$i=100;
$belongsTo='';
ksort($childArray);
foreach($childArray as $k=>$element){ ?>
	    					
	    					<?php 
	    					$id=$element['Field']['id'];
	    			 	 		if((isset($element['FieldValue']['id']))){
				    			$predeclaredVal=$element['FieldValue']['value'];
				    		}
				    		else{
				    			$predeclaredVal="";
				    		}
				    		$description ="";
				    		if(!empty($element['Field']['description'])){
				    			$description = $element['Field']['description'];
				    		}
				    		$showHideLabelChild = (isset($element['Field']['is_display_label']) && $element['Field']['is_display_label'] == 1)?'block':'none';
				    		switch ( $element['FieldTypes']['label'] ){
				    			case "text" :  
				    				?>
				    				<div class="brief-details basic-form">
				    					<div class='single-item marginBriefDateField'>
				    						<label class = "briefLabel" style="display:<?php echo $showHideLabelChild;?>"><?php echo $element['Field']['label'];?></label>
				    						<?php if($description != ''){?><span class="briefDescription"><?php echo $description; ?></span><?}?>
				    						<?php echo $this->Form->input('val-'.$id.'-'.$element['Field']['label'] , array('type'=>'text','div'=>false, 'label'=>false,'id'=>'val-'.$id, 'value'=>$predeclaredVal,'class'=>"span9"),array('class'=>'inputtext txt required', 'style'=>'width:200px;padding:5px;'))?>
				    					</div>
				    				</div>
				    				<div class="clear"></div>
				    				<?php 	
				    				break;
				    			case "number" :
				    				?> 
				    				<div class="brief-details basic-form">
				    					<div class='single-item marginBriefDateField'>
				    					<label class = "briefLabel" style="display:<?php echo $showHideLabelChild;?>"><?php echo $element['Field']['label'];?></label>
				    						<?php if($description != ''){?><span class="briefDescription"><?php echo $description; ?></span><?}?>
					    				<?php 
													
													if($predeclaredVal == "" || $predeclaredVal == "NULL"){
														$valForDate = "";
													}else{
														$valForDate = date('Y-m-d',strtotime($predeclaredVal));
													} 
													echo $this->Form->input('val-'.$id.'-'.$element['Field']['label'] , array('type'=>'text','div'=>false, 'label'=>false,'id'=>'val-'.$id, 'value'=>$predeclaredVal),array('class'=>'date datepicker date-metadata', 'style'=>'width:200px;padding:5px;'));?>
					    					</div>
				    					</div>
				    					<div class="clear"></div>
				    				<?php 
				    				break;
				    			case "textarea" :
							    				?> 
							    				<div class="brief-details basic-form" >
				    							<div class='single-item marginBriefsLabel'>
							    				<label class = "briefLabel" style="display:<?php echo $showHideLabelChild;?>"><?php echo $element['Field']['label'];?></label>
				    							<?php if($description != ''){?><span class="briefDescription"><?php echo $description; ?></span><?}?>
							    				<?php echo $this->Form->input('val-'.$id.'-'.$element['Field']['label'] ,array('type'=>'textarea','div'=>false, 'label'=>false,'id'=>'val-'.$id, 'value'=>$predeclaredVal,'class'=>"span9"),array('class'=>'inputtext number required'));?>
							    				</div>
							    				</div>
							    				<div class="clear"></div>
							    				<?php 
							    				break;
								case "date" : 
									?>
									<div class="date-obj">
										<div class="date-select marginBriefDateField">
											<label class = "briefLabel" style="display:<?php echo $showHideLabelChild;?>"><?php echo $element['Field']['label'];?></label>
				    						<?php if($description != ''){?><span class="briefDescription"><?php echo $description; ?></span><?}?>
												<?php 
														
														if($predeclaredVal == "" || $predeclaredVal == "NULL"){
															$valForDate = "";
														}else{
															$valForDate = date('Y-m-d',strtotime($predeclaredVal));
														} echo $this->Form->input('val-'.$id.'-'.$element['Field']['label'] , array('type'=>'text','div'=>false, 'label'=>false,'id'=>'val-'.$id,'class'=>'date datepicker date-metadata span2', 'value'=>$valForDate),array('class'=>'inputtext date required', 'style'=>'width:200px;padding:5px;'));?>
										</div>
									</div>
									<div class="clear"></div>
									<?php 	
									break;
								case "html_textarea":?>
										<div class="brief-details basic-form">
										<label class = "briefLabel" style="display:<?php echo $showHideLabelChild;?>"><?php echo $element['Field']['label'];?></label>
				    					<?php if($description != ''){?><span class="briefDescription"><?php echo $description; ?></span><?}?>
										<div class='single-item marginBriefsLabel'>
											<?php  echo stripslashes($element['Field']['options']); ?>
										</div>
										</div>
										<div class="clear"></div>
								
								<?php 	
									break;
									case "file_upload":?>
										<div class="brief-details basic-form">
				    						<div class='single-item marginBriefsLabel'>
												<label class = "briefLabel" style="display:<?php echo $showHideLabelChild;?>"><?php echo $element['Field']['label'];?></label>
				    							<?php if($description != ''){?><span class="briefDescription"><?php echo $description; ?></span><?}?>
													<?php $hid = $element['HierarchyValue']['id'];
													  
													   if($formReqId == "") {
													   	
													   		$formReqId =0;
													   }
													
													?>
												<input type="file" name="files" class="fileupload" id="fileupload-<?php echo $i; ?>"  onclick="<?php echo "attachmetaDatafiles($formReqId,$id,$hid,$i)"; ?>" multiple>
												<input type="hidden" value=""  name="<?php echo('data[FormRequestData][val-'.$id.'-'.$element["Field"]["label"].']');?>">
												<div id="upFile-<?php echo $i++; ?>" class= "upFile"></div>
												<span id='span-<?php echo 'val-'.$id;?>' class="astrix"></span>
											</div>
										<?php 
										 if(isset($fileChildArray[$element['FormFieldJoin']['parent_fieldset_id']][$k]) && isset($fileChildArray[$element['FormFieldJoin']['parent_fieldset_id']][$k])) {
										
										?>		<div class="cr_file_campaign_request">
													<?php echo $this->FormRequestsFile->getAssociatedFormFilesHtmlMetadata($fileChildArray[$element['FormFieldJoin']['parent_fieldset_id']][$k],'form');?>
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
									//	if($predeclaredVal == " "){$predeclaredVal = $optionDefault[1]; $selected="selected"; }
									} 
									foreach( $optionsExplodes as $k=>$optionsExplode){
										if($optionsExplode != "[othersTextarea]"){
											$options[$optionsExplode] = $optionsExplode;
										}else if($optionsExplode == "[othersTextarea]"){
												if($textAreaValue!=""){
													$otherOptions.= '<div class="checkbox"><label><input type="checkbox" class="Others" name="Others" id="Other-'.$id.'" checked>Other:</label></div>' ;
													$otherOptions.= '<div class="other-txtarea"><textarea name="data[CampaignRequestData][val-'.$id.'-'.$element["Field"]["label"].'][Other]" id="textArea-'.$id.'">'.$textAreaValue.'</textarea></div>';
												}else{
													$otherOptions.= '<div class="checkbox"><label><input type="checkbox" class="Others" name="Others" id="Other-'.$id.$element["HierarchyValue"]["id"].'">Other:</label></div>' ;
													$otherOptions.= '<div class="other-txtarea"><textarea style="display:none" name="data[CampaignRequestData][val-'.$id.'-'.$element["Field"]["label"].'][Other]" id="textArea-'.'">'.$textAreaValue.'</textarea></div>';
												}
										}
									}
									?>
									
										<div class='check-stack brief-service marginBriefsLabel'>
										<h3 style="display:<?php echo $showHideLabelChild;?>"><?php echo $element['Field']['label'];?></br>
										<?php if($description != ''){?><span class="briefDescription"><?php echo $description; ?></span><?}?>
											<?php 
											echo $this->Form->input('val-'.$id.'-'.$element['Field']['label'], array('multiple' => 'checkbox','value'=>$chkValues, 'options' => $options,'label'=>false));
											echo $otherOptions;?>
										</div><div class="clear"></div>
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
									<label class = "briefLabel" style="display:<?php echo $showHideLabelChild;?>><?php echo $element['Field']['label'];?></label>
									<?php if($description != ''){?><span class="briefDescription"><?php echo $description; ?></span><?}?>
										<div class=' single-item select2-controls'>
											<?php echo $this->Form->input('val-'.$id.'-'.$element['Field']['label'], array('type'=>'select','options'=>$options,'div'=>false,'label'=>false,'empty' => '--Select--', 'id'=>'val-'.$id),array('class'=>'inputtext required' ,'style'=>'width:212px;padding:3px;'));?>
										</div>
										<div class="clear"></div>
									<?php 
									break;
									case "radio":
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
									$htmlForRadio .='<label class = "briefLabel" style="display:'.$showHideLabelChild.'">'.$element["Field"]["label"].'</label>';
									if($description != ''){
									$htmlForRadio .= '<span  class="briefDescription" >'. $description.'</span>';
									} 
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
											$htmlForTextareaOrTextBox ='<input type=text class="span9"  name="data[FormRequestData][val-'.$id.'-'.$element["Field"]["label"].'-'.$element["HierarchyValue"]["id"].'][others_options]['.$stripoptionsExplode.']" value="'.$valueInput.'">';
										}
										if($optionsExplode==$predeclaredVal){
											$checked = "checked = 'checked'";
										}
										else if($optionsExplode == $defaultVal && $predeclaredVal ==""){
											$checked = "checked = 'checked'";
											
										}
									$htmlForRadio .= '<div class="">';
									$htmlForRadio .= '<label class="radio">';
									$htmlForRadio .= '<input type=radio id=val-'.$id.' value="'.$optionsExplode.'"  '.$checked.'   name="data[FormRequest][val-'.$id.'-'.$element["Field"]["label"].'][value]" >';
									$htmlForRadio .= $optionsExplode;
									$htmlForRadio .= '</label>';
									$htmlForRadio .= $htmlForTextareaOrTextBox;
									$htmlForRadio .= '</div>';
									
									}
									$htmlForRadio .= '</div>';
									?>
									
										
											<?php echo $htmlForRadio;?>
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
	    		?>
	    		 		
	    		<?php
							
							}//end of foreach  