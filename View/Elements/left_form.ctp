<div class="formbox users-box">
   <h1><?php echo __('Forms'); ?></h1>
   <div class="search clearfix">
      <label class="floater"><?php echo __('Search'); ?></label>
      <span class="floater">
      <?php echo $this->Form->input('filter', array(
         'type'	=> 'text',
         'div'		=> false,
         'label'	=> false
         )
         );
         ?>
      </span>
   </div>
    <div class="option-toggle"><a href="#" class="show advanced_options"> Advanced Options </a></div>
    
    <div class="filters hide show_all_filters">
    <ul>
  <li id="category_filter" >
      <?php echo $this->Form->input('form_type_filter', array(
         'type'	=> 'select',
         'options'	=> $listOfCategories, 
         'label'	=> false, 
         'div'		=> false,
         'empty'   =>  'All Categories'),
         array('class'=>'inputtext')
         );
         ?>
      </li>
 	
 	<li id="status_filter">
      
      <?php echo $this->Form->input('form_status_filter', array(
         'type'	=> 'select',
         'options'	=> $listOfStatus, 
         'label'	=> false, 
         'div'		=> false,
         'empty'   =>  'All Status'), 
         array('class'=>'inputtext')
         );
         ?>
   </li>
   </ul>
   </div>
    <div class="filters form-btn user_profile_button" >
		<?php echo $this->Html->link(__('Find'),'#',array('id'=>'search_filters', 'class'=>'btn pull-left'));?>
		<?php echo $this->Html->link(__('Reset Filter'),'#',array('id'=>'reset_filters', 'class'=>'btn pull-right'));?>
	</div>
   <div class="all-users">
      <div class="name-big-list">
         <?php echo  $this->Form->input('static_formList',array(  'type'	=> 'select',
            'options'	=> $listOfForm, 
            'size'	=> 25 , 
            'multiple'=> true, 
            'label'	=> false, 
            'div'		=> false,
            'style'	=> 'display:none'));?> 
      </div>
     
     <!--  <div class="add-btns clearfix">
         <span class="add-user-icon">
         </span>
      </div>  -->
   </div>
   <div class="user-stats">
      <ul>
         <li><?php echo __('Total Forms'); ?>: <?php echo count($listOfForm);?></li>
      </ul>
   </div>
   <div class="clearfix" >
      <span>
      <?php echo $this->Html->link('Create New Form', '/form/add/', array('class'=>'button link-pop floater-rt','id'=>'add_campaign_type')); ?>
      </span>
   </div>
</div>
<script type="text/javascript">
$("select", $("#category_filter")).select2({
	width: "197px"
});
/** Setting Default Value to All in Select2 Auto Complete for category dropdown*/
$("#category_filter").find("span").html("All Categories");

$("select", $("#status_filter")).select2({
	width: "197px"
});
/** Setting Default Value to All in Select2 Auto Complete for client dropdown*/
$("#status_filter").find("span").html("All Status");
</script>

