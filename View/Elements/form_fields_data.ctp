<div class="stage user-client-access clearfix" >
   <div class="add-person clearfix">
      
      <?php echo $this->Html->link('Add Fields', '/fields/add/form/', array('class'=>'button  link-pop floater-rt marginRight5','id'=>'add_form')); ?>
      <?php echo $this->Html->link('Choose from Existing', '/projects/fields/getExistingListOfFields/form/', array('class'=>'button  link-pop floater-rt marginRight5','id'=>'list_exisiting_field')); ?>
      <?php echo $this->Html->link('Preview Form', '/projects/fields/form_request_preview/', array('class'=>'button  floater-rt marginRight5','id'=>'previewForm','target'=> '_blank')); ?>
   </div>
   <input type="hidden"  id="formId" name="data['CampaignType']['client_hid']"  />
   <div class=" clearfix">
      <table class="datatable" id="extended-data-form" >
         <thead>
            <tr>
               <th width="20px"><?php echo $this->Form->input('select_all_campaign_dataTable',array('type'=>'checkbox','label'=>false)); ?></th>
               <th width="150px">
                  <div class="col-name"><?php echo __("Field Label"); ?></div>
               </th>
               <th width="70px">
                  <div class="col-name"><?php echo __("Input Type"); ?></div>
               </th>
               <th width="150px">
                  <div class="col-name"><?php echo __("Options"); ?></div>
               </th>
               <th width="110px">
                  <div class="col-name"><?php echo __("Status"); ?></div>
               </th>
               <th width="90px">
                  <div class="col-name"><?php echo __("Weight"); ?></div>
               </th>
               <th width="90px">
                  <div class="col-name no_sort"><?php echo __("Field order"); ?></div>
               </th>
               <th width="200px" class="col-name no_sort"><?php echo __("Actions");?></th>
            </tr>
         </thead>
      </table>
   </div>
   <div class="form-btn">
      <span class="floater"><?php echo $this->Html->link('Delete', '#', array('class'=>'button','id'=>'delete_form')); ?></span>				
      <span class="floater"><?php echo $this->Html->link('Change Status To Active', '#', array('class'=>'button','id'=>'change_to_active_form')); ?></span>					
      <span class="floater"><?php echo $this->Html->link('Change Status To Inactive', '#', array('class'=>'button','id'=>'change_to_inactive_form')); ?></span>
   </div>
</div>

