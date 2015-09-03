<script>
//jQuery to move table rows:
	function moveSelected(el) {
		 var hfid = ($(el).attr("id"));
		 var update = ($(el).attr("class"));
	      var row = $(el).parents('tr:first');
	      var tblID = $(el).parents("table:first").attr("id"); 
	     
	      	if($("#formList").val()!=""){
				formId = $("#formList").val();
			}
			
				var type = 'form'; 
				var URL = "/form_builders/form_field_joins/edit"; 
			
			
	      if ($(el).is('.up')) {
	    	  var operateOnRow = row.prev(); 
	            //row.insertBefore(row.prev());

	      }

	      else {
	    	  var operateOnRow = row.next();
	            //row.insertAfter(row.next());

	      }
	      if(operateOnRow.length!== 0){
	          
	      
	      var cdata = {hfid:hfid,modify:update,type:type};
	      $.ajax({
	          type: "POST",
	          url:URL,
	          data: cdata,
	          beforeSend:function(){
	          	$(".alert-success").show();
			        $("#success").html("Updating field\'s weight, please wait...");
			    },
			   complete:function(){
			        $(".status").hide();
			    },
	          success: function(msg){
	          	if(msg == 1){
		      		statusMessages("Field\'s weight updated successfully.");
		      		moveDataUp(row, operateOnRow,tblID);
					getCampaignRequestExtendedData(formId);
	      	 		
	          	}

	          }
	      });
	     	
	      }
	      
	     
	    	/* move the data in the internal datatable structure */
	    	function moveDataUp(row, prevRow,tblID){  
	    		var tblId = "#"+tblID; 
	    		var oTable = $(tblId).dataTable();
				var movedData = oTable.fnGetData(row[0]).slice(0);    // copy of row to move.
	    	    var prevData = oTable.fnGetData(prevRow[0]).slice(0); // copy of old data to be overwritten by above data.
	    	    var swap = movedData[5];
	    	    movedData[5] = prevData[5];
	    	    prevData[5] = swap;
	    	  
	    	    oTable.fnUpdate(prevData , row[0], 0, false, false); 
	    	    oTable.fnUpdate(movedData , prevRow[0], 0, true, true);
	    	}       
	}
</script>