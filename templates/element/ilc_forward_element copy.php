
<div class="row">
    <div class="col-md-12 mt-3" id="note">
        <strong class="text-primary">Note: As this sample is for "Inter Laboratory Comparison" so must be forwarded to min. 5 RAL/CAL </strong>
    </div>
</div>

<div class="container">
    <div class="table-responsive" id="hidetable">
      <strong>Selected RAL/CAL To Forward Sample List</strong>
      <table class="table table-bordered table-striped table-hover">
        <thead>
          <tr class="table-secondary">
            <th class="text-center">Sr. No.</th>
            <th class="text-center">RAL/CAL Name</th>
            <th class="text-center">Inward Officer</th>
            <th class="text-center">Action</th>
          </tr>
        </thead>
        
        <!-- show saved record accroding to ral name and inwd office value 14/06/2022 -->
        <?php if (!empty($getSavedList)){ ?>

            <tbody id="tbodyid">
                <?php 
                    $i=1;
                    foreach($getSavedList as $each) { ?>
                    <tr><td class='text-center'><?php echo $i; ?></td><td id='<?php echo $each['rid']; ?>' class='text-center'><?php echo $each['ro_office']; ?></td><td id='<?php echo $each['urid']; ?>' class='text-center'><?php echo $each['f_name'].' '.$each['l_name']; ?></td><td class='text-center'><button type='button' class='btn btn-danger remove'><i class='glyphicon glyphicon-remove'></i></td></tr>
                        
                <?php $i++; } ?>
            </tbody>
        
        <?php }else { ?>

            <tbody id="tbodyid">

            </tbody>
           
        <?php } ?>
      </table>
        <a href="#" class="btn btn-primary float-right" id="addselectbtn">save</a>
        <p id="flashnote"><b>Please save the selected RALs then proceed to forward sample</b></p>
       
    </div>
    
    <div id="viewlab">
      <div class="row">
        <div class="customer_records">
          <input name="customer_name" type="text" value="name">
          <input name="customer_age" type="number" value="age">
          <input name="customer_email" type="email" value="email">

          <a class="extra-fields-customer" href="#">Add More Customer</a>
        </div>

        <div class="customer_records_dynamic"></div>

      </div>
   </div>
   
</div>
<!-- select min 5 ral to display save button 14/06/2022-->
<?php if (!empty($getSavedList)){ ?>
  <input type="hidden" id="getSavedList" value="<?php echo $getSavedList[0]['f_name']; ?>">  
<?php } ?>
<?php echo $this->Html->Script("sampleForward/ilc_forward_script") ?>