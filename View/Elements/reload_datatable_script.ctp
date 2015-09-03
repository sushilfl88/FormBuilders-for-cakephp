<script>
	function reloadDataTable(msg,ctId){	
		obj=jQuery.parseJSON(msg);
		objCtId = jQuery.parseJSON(ctId);
		getAssociatedFieldsData(objCtId);
		if(obj.success){
	    	$(".status-success").hide();	 
			statusMessages("Fields added Successfully.");
			$.fn.colorbox.close();
		}
		else{
			if(obj.errors){
				jQuery.each($('.modal-body input'), function(i, val) {
					$(this).removeClass('errors_border'); 
				});
				jQuery.each($('.modal-body .frm-elements span'), function(i, val) {
					$(this).removeClass('errors'); 
					$(this).html('');
				});
				jQuery.each(obj.errors, function(i, val) {
						$('#'+i+' input').addClass('errors_border');
						$('#'+i+' .frm-elements').append('<span class="errors">'+val[0]+'</span>');
				    });
							
			}
		}	
		
	}

</script>