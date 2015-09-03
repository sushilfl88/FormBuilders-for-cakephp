<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.0.5/angular.min.js"></script>
    <script>
	function FormWorkflowStatusJoinsController($scope,$http,limitToFilter) {
    	

    		/**
	    	 * Initialises the page. Then fetches the data
	    	 */
	    	$scope.initialisePage = function () {
	    		var url = "/form_builders/form_workflow_status_joins/getProjectItemWorkflowsStatusesChecklists";
	    		$scope.doGet(url,function(result){ 
					$scope.workflows = result.workflowList;
					$scope.statuses = result.statusList;
					$scope.forms = result.form;
					
				});
	    	};

        	$scope.doGet = function (url,onSuccess,onError) {
    		$scope.processing = true;
    		$http.get(url).success(function(result){
    			$scope.processing = false;
    			if(typeof(result) == "string" && result.match(/cake\-error|error/i)) {
    				$scope.errorMessage = result;
    			} else {
    				onSuccess(result);
    			}
    		}).error(function(data,status,headers,config){
    			$scope.processing = false;
    			$scope.errorMessage = data;
    		});
    	}
        	$scope.doPost = function (url,data,onSuccess,onError) {
        		$scope.processing = true;
        		$scope.errorMessage = "";
        		$http.defaults.headers.post['Content-Type'] = 'application/json';
        		$http.post(url,data,{'headers':{'Content-Type':'application/json'}}).success(function(result){
        			$scope.processing = false;
        			if(typeof(result) == "string" && result.match(/cake\-error|error/i)) {
        				$scope.errorMessage = result;
        			} else {
        				onSuccess(result);
        			}
        		}).error(function(data,status,headers,config){
        			$scope.processing = false;
        			$scope.errorMessage = data;
        			if(onError) {
        				onError (data);
        			}
        		});;
        	};
		$scope.updateStatus = function(selectedWorkflow){
					
					$("#statusDropDown").show();
		        	$scope.availableStatus = [];
					angular.forEach($scope.statuses, function(value){
		              if(value.workflowId == selectedWorkflow){
		                $scope.availableStatus.push(value);
		              }
		            });
					$.fn.colorbox.resize({});
          }
        $scope.associateChecklist = function (workflow,status,form){
            if(workflow && status && form){
            var rateCardIds = $("#ratecardPriceID").val();
        	var url = "/form_builders/form_workflow_status_joins/associateFormsToRateCards";
    		var data = {'workflowId':workflow,'statusCode':status,'formId':form,'rateCardIds':rateCardIds};
    		
    		$scope.doPost(url,data,function(result){
        		if(result){
				$.fn.colorbox.close();
				location.reload(true);
				$(".alert-success").show();
				$("#success").html("Associated succsessfully.");
        		}else{
        			$(".popup-alert").show();
					$("#popupwarning").html("Error occured while saving");
            		}	
    		});
            }
        }
        $scope.removeChecklist = function (workflow,status,form){
        	if(workflow && status && form){
            	
        	var rateCardIds = $("#ratecardPriceID").val();
        	var url = "/form_builders/form_workflow_status_joins/dissociateFormsToRateCards";
    		var data = {'workflowId':workflow,'statusCode':status,'formId':form,'rateCardIds':rateCardIds};
    		
    		$scope.doPost(url,data,function(result){
    			if(result){
    			$.fn.colorbox.close();
    			location.reload(true);
    			$(".alert-success").show();
				$("#success").html("Removed succsessfully.");
    			}else{
					$(".popup-alert").show();
					$("#popupwarning").html("Error occured while saving");
        			}
    		});
        }
        }
        
    }
    </script>
    <script>
	$(function(){
		$("#statusDropDown").hide();
		$.fn.colorbox.resize({});
		
	});
	</script>
	<?php //debug($ratecardPriceIds);?>
<div class="modal popup-content clearfix;">
<div class="modal-header"><h3><?php echo __('Associate/Remove Checklist');?></h3></div>
<div class="modal-body add-business-unit">
<div class="popup-alert" style="display:none">
     <a href="#" class="close" data-dismiss="alert">&times;</a>
     <span id="popupwarning"></span>
     </div>  
<section ng-app ng-controller="FormWorkflowStatusJoinsController" ng-init="initialisePage()" novalidate>


    	<div class="control-group  clearfix">
            	<label class="control-label floater"><?php echo __('Workflow :');?></label>
                <span class="controls floater"> 
	                <select data-ng-model="workflow" id = "workflowDropDown"  data-ng-change="updateStatus(workflow)" required>
		      			<option value=""><?php echo __('Select workflow :');?></option>
		       			<option ng-repeat="workflow in workflows" value="{{workflow.id}}">{{workflow.label}}</option>
	    			</select>
    			</span>
        </div>
        <br>	
        <div class="control-group  clearfix" id = "statusDropDown">
            	<label class="control-label floater"><?php echo __('Status :');?></label>
                <span class="controls floater"> 
	                <select data-ng-model="status"  required>
      					<option value=""><?php echo __('Select status :');?></option>
      					<option ng-repeat="status in availableStatus" value="{{status.status_code}}">{{status.status_label}}</option>
    				</select>
    			</span>
        </div>
        <br>
        <div class="control-group  clearfix">
            	<label class="control-label floater"><?php echo __('Checklists :');?></label>
                <span class="controls floater"> 
	                <select data-ng-model="form" ng-options="formId as formName for (formId,formName) in forms" required >
      					<option value=""><?php echo __('Select Checklists');?></option>
      				</select>
    			</span>
        </div>
        <input  type ="hidden" id = "ratecardPriceID" value="<?php echo $ratecardPriceIds;?>">
    <div class="modal-footer" id="add_client">
		<a href="#" class="btn btn-primary" id="associateChecklist" ng-click="associateChecklist(workflow,status,form)"><?php echo __('Associate Checklist');?></a>
		<a href="#" class="btn btn-primary" id="removeChecklist" ng-click="removeChecklist(workflow,status,form)"><?php echo __('Remove Checklist');?></a>
	</div>
</section>
	
</div>
</div>
</html>
