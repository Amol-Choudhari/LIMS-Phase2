<div class="form-group row pt-3">
   <div class="col-md-2">
      <?php echo $this->Form->control('from_dt',array('type'=>'text','id'=>'from_dt','placeholder'=>'From Date','label'=>false,'class'=>'form-control','readonly'=>true)); ?>
   </div>
   <div class="col-md-2">
      <?php echo $this->Form->control('to_dt',array('type'=>'text','id'=>'to_dt','placeholder'=>'To Date','label'=>false,'class'=>'form-control','readonly'=>true)); ?>
   </div>
   <div class="col-md-2">
      <?php echo $this->Form->control('Submit',array('type'=>'submit','id'=>'search','placeholder'=>'Select Date','label'=>false,'class'=>'btn btn-primary form-control')); ?>
   </div>
   <div class="clear"></div>
</div>
<?php echo $this->Html->script("element/date_filter"); ?>
