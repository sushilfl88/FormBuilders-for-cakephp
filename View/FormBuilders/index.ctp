<?php echo $this->element('form_builder_script');?>
<?php echo $this->element('reload_datatable_script');?>
<?php echo $this->element('move_rows_up_down_script');?>
<?php echo $this->element('submit_script');?>
<!--FORM AREA-->
<div class="main adm-form-builder clearfix">
   <div class="floater div220">
      <?php echo $this->element('left_form');?>
   </div>
   <!--end div20-->
   <div class="floater div700">
      <div class="formbox profile-details">
         <h1 class="clearfix"><?php echo __('Form Builder'); ?>
            <span class="selected-user">
            <span class="load-name" id="loading-name">*<?php echo __('Please select a form from the left panel to view it\'s details'); ?>.</span>
            </span>
            
         </h1>
         <?php  echo $this->Html->link('Multiple Clients-Forms Association', '/form_builders/form_associations/associateFormClient/', array('class'=>'button  link-pop floater-rt marginRight5','id'=>'multipleFormClientAssociation')); ?>
         <br/>
         <div  class="alert" id="warningNoField" style="display:none;">
		     <a  class="close" href="javascript: void(0)">×</a>
		     <span><?php echo __("Please add atleast one field in order to view form in Workflow Forms/Briefs listing. ");?></span>
		     </div>
         <div class="formbox-tabs clearfix">
            <ul>
               <li><a href="#"><?php echo __('Form'); ?></a></li>
               <li><a href="#"><?php echo __('Fields'); ?></a></li>
               <li><a id="defaultTeam" href="#" style="display:none"><?php echo __('Default Team'); ?></a></li>
            </ul>
         </div>
         <!-- START OF DETAILS SECTION  -->
         <div class="tab-stage">
            <?php echo $this->element('form_metadata');?>
         </div>
         <!-- // END OF DETAILS SECTION  -->
         <!-- START OF ACTIONS SECTION  -->
         <div class="tab-stage">
            <?php echo $this->element('form_fields_data');?>
         </div>
         <!-- // END OF ACTIONS SECTION  -->
         <!-- START OF DEFAULT TEAM SECTION  -->
         <div class="tab-stage add-default-team-element">
            <?php echo $this->element('form_default_team');?>
         </div>
         <!-- // END OF DEFAULT TEAM SECTION   -->
      </div>
   </div>
   <!--end div80-->
</div>
<!--END FORM AREA-->

