<div class="stage user-client-access clearfix add-default-team">
   <div class="add-person clearfix">
      
      <?php echo $this->Html->link('Add Approver', '/campaigns/campaign_requests/share/', array('class'=>'button  link-pop floater-rt marginRight5','id'=>'brief_approver')); ?>
      <?php echo $this->Html->link('Add Editor', '/campaigns/campaign_requests/share/', array('class'=>'button  link-pop floater-rt marginRight5','id'=>'brief_editor')); ?>
      <?php echo $this->Html->link('Add Watcher', '/campaigns/campaign_requests/share/', array('class'=>'button link-pop floater-rt marginRight5','id'=>'brief_watcher','target'=> '_blank')); ?>
   </div>
   <input type="hidden"  id="formId" name="data['CampaignType']['client_hid']"  />
   <div class=" clearfix">
      <table class="datatable" id="formTeam" >
         <thead>
            <tr>
               <th width="20px"><?php echo $this->Form->input('select_all_campaign_dataTable',array('type'=>'checkbox','label'=>false)); ?></th>
               <th width="150px">
                  <div class="col-name"><?php echo __("Name"); ?></div>
               </th>
               <th width="70px">
                  <div class="col-name"><?php echo __("Roles"); ?></div>
               </th>
               <th width="70px">
                  <div class="col-name"><?php echo __("User Visibility"); ?></div>
               </th>
            </tr>
         </thead>
      </table>
   </div>
  <div class="form-btn">
      <span class="floater"><?php echo $this->Html->link('Delete', '#', array('class'=>'button','id'=>'deleteUser')); ?></span>
       <span class="floater"><?php echo $this->Html->link('Hide Users', '#', array('class'=>'button','id'=>'hideUser')); ?></span>
       <span class="floater"><?php echo $this->Html->link('Show Users', '#', array('class'=>'button','id'=>'showUser')); ?></span>				
   </div>
</div>

