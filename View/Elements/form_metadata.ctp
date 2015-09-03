<div class="stage user-profiles-form clearfix">
   <?php echo $this->Form->create('Form',array('url' =>'/form/add/','id'=>'Form','class'=>'ajax form-horizontal')); ?>
   <div class="control-group">
      <label class="control-label"><?php echo __('Name');?></label>
      <div class="controls">
         <?php echo $this->Form->input('label',array('id'=>'label','label'=>false,'value'=>''));?>
         <?php echo $this->Form->input('id',array('type'=>'hidden'));?>
      </div>
   </div>
   <div class="control-group">
      <label class="control-label"><?php echo __('Description');?></label>
      <div class="controls">
         <?php echo $this->Form->textarea('description',array('id'=>'descriptionForm','label'=>false,'value'=>''));?>
         
      </div>
   </div>
   
   <div class="control-group">
      <label class="control-label"><?php echo __('Form Category');?></label>
      <div class="controls">
         <?php echo $this->Form->input('category_id',array('id'=>'formCategory','type'=>'select','label'=>false,'options'=>'','value'=>''));?>
      </div>
   </div>
   <div class="control-group">
      <label class="control-label"><?php echo __('Email Distribution List');?></label>
      <div class="controls">
         <?php echo $this->Form->input('distribution_list',array('id'=>'distribution_list','label'=>false,'value'=>''));?>
      </div>
   </div>
   <div class="control-group">
      <label class="control-label"><?php echo __('Status');?></label>
      <div class="controls item-radio rate-card-radio" id="status">
        <?php echo $this->Form->input('status',array('id'=>'formStatus','type'=>'select','label'=>false,'options'=>'','value'=>''));?>
         <?php //	$options = array( 'active' => 'Active', 'inactive' => 'Inactive');
             //echo $this->Form->input('status',array('options'=>$options,'type'=>'radio','width'=>'15px','legend'=>false,'id'=>'status','default'=>'Active')); ?>
      </div>
   </div>
    <div class="control-group">
      <label class="control-label" style="padding-top:0px;"><?php echo __('Form Visibility');?></label>
      <div class="controls item-radio rate-card-radio">
       <?php 	$options = array("0" => ' Private',"1" => ' Public' );
				echo $this->Form->radio('is_public',$options,array('legend'=>false,'id'=>'settings')); ?>
      </div>
   </div>
   <div class="control-group">
      <label class="control-label"></label>
      <div class="controls"><button class="btn btn-primary" id="addForm"><?php echo __('Save Changes');?></button></div>
   </div>
   <?php echo $this->Form->end(); ?>
   <div class="form-btn">
   </div>
</div>

