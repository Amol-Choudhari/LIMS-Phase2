<?php ?>

<?php echo $this->Form->create(null,array()); ?>
<?php if(!empty($check_user_role)){

	echo $this->element('user_dashboard_elements/common_dashboard_home_view');

}else{ echo "Sorry... You don't have any role assigned by admin."; } ?>

<div class="clear"></div>
<?php echo $this->Form->end(); ?>
