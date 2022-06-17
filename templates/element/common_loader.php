<!-- html -->

<?php echo $this->Html->css('common_loader');?>
<div class="text-center form_spinner">
	<div class="spinner-grow text-primary form_spinner_icon" role="status">
		<span class="sr-only">Loading...</span>
	</div>
	<div class="spinner-grow text-secondary form_spinner_icon" role="status">
		<span class="sr-only">Loading...</span>
	</div>
	<div class="spinner-grow text-success form_spinner_icon" role="status">
		<span class="sr-only">Loading...</span>
	</div>
	<div class="spinner-grow text-danger form_spinner_icon" role="status">
		<span class="sr-only">Loading...</span>
	</div>
	<br>
	<b>Loading Please Wait...</b>
</div>
<?php echo $this->Html->script('common_loader');?>