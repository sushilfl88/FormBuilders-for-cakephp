<script>
	function changeContentType(clientHid,clientName){
		$('#campaignclient').html(clientName+'<b class="caret"></b>');
			 	$.ajax({
				type: "GET",
			    url:"/form_builders/form_requests/view/client_hid/"+clientHid,	                
			    success: function(data){
					var obj = jQuery.parseJSON(data);
						if(obj.success){
							$('#FormMenu').html('');
	   						$(obj.Forms).each(function(i,val){
	   							$('#FormMenu').append('<li><a href="/projects/form_requests/create/'+val.FormRequest.id+'">'+val.FormRequest.label+'</a></li>');
		   					});
	   						$('#FormRequest').addClass('dropdown-toggle');
	   					 	$('#FormRequest').attr('data-toggle','dropdown');
	   					 	$('#FormRequest').html('Select Form<b class="caret"></b>');
	   					 	$('#brief-type').removeClass('none');
            			 } // success close
       			 
	 			} 
   			}); //ajax close

	}		
			
</script>


<iframe
	name="downloadExcel" id="downloadExcel" class="none"></iframe>
<table class="display-wrap" cellspacing="0" border="0">
	<tr>
		<td class="display-wraptd" valign="top">
			<div class="row-fluid briefs-sub-head">
				<h2 class="span7">
				<?php echo __('Forms');?>
				</h2>
				<?php //if(isset($formRequestData) && count ($formRequestData)>0 && $formRequestData)://need to change this condition?>
				<div class="add-item-btn marginRight5">
					<a href="/form_builders/form_requests/add_survey/" class="link-pop btn btn-primary"><i
						class="icon-plus icon-white"></i> <?php echo __('Submit Survey');?> </a>
				</div>
				<div class="add-item-btn marginRight5">
					<a href="/form_builders/form_requests/add_checklist/" class="link-pop btn btn-primary"><i
						class="icon-plus icon-white"></i> <?php echo __('Submit Checklist');?> </a>
				</div>
				<?php if($isPriviledge): ?>
				<div class="add-item-btn marginRight5">
					<a href="/form_builder" class="btn"><?php echo __('Form Builder');?> </a>
				</div>
				<?php endif;?>
			</div>
			<table class="display table">
				<thead>
					<tr>
						<th class="ratecrd-id"><span class="col-name"><?php echo __('ID');?>
						</span></th>
						 <th>
					        <span class="col-name"><?php echo __('Actions'); ?></span>
					        <span class="sort-carrot"></span>
					    </th> 
						<th class="ratecrd-office"><span class="col-name"><?php echo __('Client');?>
						</span></th>
						<th class="ratecrd-label"><span class="col-name"><?php echo __('Name');?>
						</span></th>
						<th class="ratecrd-label"><span class="col-name"><?php echo __('Form Type');?>
						</span></th>
						<th class="ratecrd-status"><span class="col-name"><?php echo __('Category');?>
						</span></th>
						<th class="ratecrd-status"><span class="col-name"><?php echo __('Status');?>
						</span></th>
						<th class="ratecrd-office"><span class="col-name"><?php echo __('Submitted By');?>
						</span></th>
						<th class="ratecrd-status"><span class="col-name"><?php echo __('Submitted Date');?>
						</span></th>
					</tr>
				</thead>
				<tbody>
				<?php
				//debug($formRequestData);
				foreach($formRequestData as $formRequestValue){
					?>
					<tr>

						<td class="ratecrd-id"><a
							href="/projects/field_values/form_request/view/<?php echo $this->App->getValue($formRequestValue, 'FormRequest.form_associations_id');?>/<?php echo $this->App->getValue($formRequestValue, 'FormRequest.id');?>"><?php echo $this->App->getValue($formRequestValue, 'FormRequest.id');?>
						</a></td>
						<td >
						  <div class="tools dropdown">
	    					<a href="#" class="dropdown-toggle tool-anchor" data-toggle="dropdown" title="Tools"><?php //echo __('Tools'); ?></a>
	    						<ul class="dropdown-menu">
						        	<li> <a  class="link-pop" href="/projects/send_emails/fr_view/47/<?php echo $this->App->getValue($formRequestValue, 'FormRequest.id');?>" target="_blank">Send Email</a> </li>
						        	<li> <a  class="link-pop" href="/projects/send_emails/fr_view/48/<?php echo $this->App->getValue($formRequestValue, 'FormRequest.id');?>" target="_blank" >Send Form Template</a></li>
						      </ul>
	    				</div>
						</td>
						<td class="ratecrd-office"><?php echo $this->App->getValue($formRequestValue, 'HierarchyValue.label');?>
						</td>

						<td class="ratecrd-label"><a
							href="/projects/field_values/form_request/view/<?php echo $this->App->getValue($formRequestValue, 'FormRequest.form_associations_id');?>/<?php echo $this->App->getValue($formRequestValue, 'FormRequest.id');?>"><?php echo $this->App->getValue($formRequestValue, 'FormRequest.label');?>
						</a></td>
						<td class="ratecrd-status"><?php echo $this->App->getValue($formRequestValue, 'Form.formname');?>
						</td>
						<td class="ratecrd-status"><?php echo $this->App->getValue($formRequestValue, 'Formcategory.categoryname');?>
						</td>
						<td class="ratecrd-status"><?php echo $this->App->getValue($formRequestValue, 'StatusCode.statuslabel');?>
						</td>
						<td class="ratecrd-office"><?php echo $this->App->getValue($formRequestValue, 'User.given_name').' '.$this->App->getValue($formRequestValue, 'User.surname');?>
						</td>
						<td class="ratecrd-status"><?php echo $formRequestValue[0]['submitted_date'];?>
						</td>
					</tr>
					<?php }?>
				</tbody>
			</table>
		</td>
	</tr>
</table>

<script>

(function($) {
	/*
	 * Function: fnGetColumnData
	 * Purpose:  Return an array of table values from a particular column.
	 * Returns:  array string: 1d data array 
	 * Inputs:   object:oSettings - dataTable settings object. This is always the last argument past to the function
	 *           int:iColumn - the id of the column to extract the data from
	 *           bool:bUnique - optional - if set to false duplicated values are not filtered out
	 *           bool:bFiltered - optional - if set to false all the table data is used (not only the filtered)
	 *           bool:bIgnoreEmpty - optional - if set to false empty values are not filtered from the result array
	 * Author:   Benedikt Forchhammer <b.forchhammer /AT\ mind2.de>
	 */
	$.fn.dataTableExt.oApi.fnGetColumnData = function ( oSettings, iColumn, bUnique, bFiltered, bIgnoreEmpty ) {
	    // check that we have a column id
	    if ( typeof iColumn == "undefined" ) return new Array();
	     
	    // by default we only wany unique data
	    if ( typeof bUnique == "undefined" ) bUnique = true;
	     
	    // by default we do want to only look at filtered data
	    if ( typeof bFiltered == "undefined" ) bFiltered = true;
	     
	    // by default we do not wany to include empty values
	    if ( typeof bIgnoreEmpty == "undefined" ) bIgnoreEmpty = true;
	     
	    // list of rows which we're going to loop through
	    var aiRows;
	     
	    // use only filtered rows
	    if (bFiltered == true) aiRows = oSettings.aiDisplay; 
	    // use all rows
	    else aiRows = oSettings.aiDisplayMaster; // all row numbers
	 
	    // set up data array    
	    var asResultData = new Array();
	     
	    for (var i=0,c=aiRows.length; i<c; i++) {
	        iRow = aiRows[i];
	        var aData = this.fnGetData(iRow);
	        var sValue = aData[iColumn];
	         
	        // ignore empty values?
	        if (bIgnoreEmpty == true && sValue.length == 0) continue;
	 
	        // ignore unique values?
	        else if (bUnique == true && jQuery.inArray(sValue, asResultData) > -1) continue;
	         
	        // else push the value onto the result data array
	        else asResultData.push(sValue);
	    }
	     
	    return asResultData;
	}}(jQuery));

jQuery.extend( jQuery.fn.dataTableExt.oSort, {
    "num-html-pre": function ( a ) {
        var x = a.replace( /<.*?>/g, "" );
        return parseFloat( x );
    },
 
    "num-html-asc": function ( a, b ) {
        return ((a < b) ? -1 : ((a > b) ? 1 : 0));
    },
 
    "num-html-desc": function ( a, b ) {
        return ((a < b) ? 1 : ((a > b) ? -1 : 0));
    }
} );

function fnCreateSelect(aData,name){
    var r='<select id="'+name+'">', i, iLen=aData.length;
    var o;
    for ( i=0 ; i<iLen ; i++ )
    {   
       o += '<option value="'+aData[i]+'">'+aData[i]+'</option>';
    }
    
	if(name!='client-filter'){
		r+='<option value="">All</option>'
     }
	else{
		if(i>1){
			r+='<option value="">All</option>'
		} 
	}
    r+=o;
    
    return r+'</select>';

    
}

$(function(){
	var oTable= $('.display').dataTable({
	"bAutoWidth": false,
	"aaSorting": [[ 1, "desc" ]],
	"oLanguage": {
	 	    "sSearch": "Filter:"
        },
     "aoColumns": [
    	              { "sType": "num-html" },
    	              null,
    	              null,
    	              null,
    	              null,
    	              null,
    	              null,
    	              null,
    	              null
    	         ],   
	"sDom": '<"adv-table-filters"<"export-excel"><"status-filter"><"type-filter"><"client-filter">><"head-controls"lf<"col-opt-btn"><"edit-items-table">>t<"foot-controls"ip>',
		"aoColumnDefs":[
		{"bSortable": false, "sWidth": "16px", "aTargets": [] },
		{"bVisible": false, "aTargets":[]}
		],
	"sPaginationType": "full_numbers",
	"iDisplayLength": 25
	});//END DATATABLE

	$('.status-filter').html("<label>Status:</label>" + fnCreateSelect(oTable.fnGetColumnData(6),'filter-status'));
	
	$('.status-filter').change( function () {
		oTable.fnFilter($('#filter-status').val(), 6 );
	});

	$('.type-filter').html("<label>Category:</label>" + fnCreateSelect(oTable.fnGetColumnData(5),'type-filter'));
	
	
	$('.type-filter').change( function () {
		oTable.fnFilter($('#type-filter').val(), 5 );
	});
	
	$('.client-filter').html("<label>Client:</label>" + fnCreateSelect(oTable.fnGetColumnData(2),'client-filter'));
	
	$("select", $(".type-filter,.status-filter,.client-filter")).select2({
		width:'160px'
	});

	
	
	var clientLen = $("#client-filter").find("option").length;

	var typeLen = $("#type-filter").find("option").length;
	var statusLen = $("#filter-status").find("option").length;


	if(typeLen > 1) {

		$(".type-filter").find("span").html("All");
		
	}

	if(statusLen > 1) {

		$(".status-filter").find("span").html("All");
		
	}
	
	if(clientLen > 1) {

		$(".client-filter").find("span").html("All");
	}
	
	$('.client-filter').change( function () {
		oTable.fnFilter($('#client-filter').val(), 2 );
	});
	
	$('.export-excel').html('<a  id="export-excel"  class="export-link" > <i class="icon-share-alt"></i> Export To Excel</a>');
	
	$('#export-excel').click(function(){
		var oSettings = oTable.fnSettings();
		var visible_cols= '';
		var filteredItemIds=[];
		for(var i=0,iLen=oSettings.aiDisplay.length;i<iLen;i++){
			filteredItemIds.push(parseInt($(oSettings.aoData[ oSettings.aiDisplay[i] ]._aData[0]).html()));
		}
		$('#downloadExcel').attr('src','/form_builders/form_requests/export/'+filteredItemIds);
		
	});
	
});	




</script>
