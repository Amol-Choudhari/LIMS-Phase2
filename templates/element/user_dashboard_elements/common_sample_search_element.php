<?php //echo $this->Form->create(null,array('class'=>'form-inline ml-3 search-bx')); ?>
<!-- below search box view code added on 11-05-2017 by Amol -->
<div class="input-group input-group-sm form-inline ml-3 search-bx">

	<?php echo $this->form->control('sample_code', array('type'=>'search', 'id'=>'srch_sample_id', 'class'=>'form-control form-control-navbar', 'label'=>false, 'placeholder'=>'Search Sample')); ?>
	
	<div class="input-group-append">
	
	<?php //echo $this->form->control('Submit', array('type'=>'submit', 'name'=>'search_applicant', 'class'=>'fas fa-search')); ?>
	
	<a title="Search Sample" id="search_sample_btn" href="#"><span class="glyphicon glyphicon-search"></span></a>
</div>

</div>
<div class="clearfix"></div>
<?php //echo $this->Form->end(); ?>

<?php echo $this->element('user_dashboard_elements/sampleSearchInPopupElement'); ?>

<?php echo $this->Html->script('dashboard/dashboard-search-sample-js'); ?>