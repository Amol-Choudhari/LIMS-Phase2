
<div class="row pt-5" id="note">
    <div class="col-md-9">
        <strong class="text-primary">Note: As this sample is for "Inter Laboratory Comparison" so must be forwarded to min. 5 RAL/CAL </strong>
    </div>
    <div class="col-md-3"> 
      <!-- add for show quantity & unit 10-11-2022-->
      <lable  class="alert alert-info form-control p-2" value='<?php echo $getcommodity['sample_total_qnt']; ?>'>Available Qty <span id="avai_qnt"><?php echo $getcommodity['sample_total_qnt']; ?></span> <?php echo $getcommodity['unit_weight']; ?></lable>
     </div>
</div>

  <div class="container">
    <div class="row pb-3">
      <div class="col-md-12">
        <div class="table-responsive">
          <strong>Selected RAL/CAL To Forward Sample List</strong>
          <table class="table table-bordered table-striped table-hover">
            <thead>
              <tr class="table-secondary">
                <th class="text-center">Sr. No.</th>
                <th class="text-center">RAL/CAL Name</th>
                <th class="text-center">Inward Officer</th>
                <th class="text-center">Quantity (<?php echo $getcommodity['unit_weight']; ?>)</th>
                <th class="text-center">Action</th>
              </tr>
            </thead>
            
            <!-- show saved record accroding to ral name and inwd office value 14/06/2022 -->
            <?php if (!empty($getSavedList)){ ?>

                <tbody id="tbodyid">
                    <?php 
                        $i=1;
                        foreach($getSavedList as $each) { ?>
                        <tr>
                          <td class='text-center'><?php echo $i; ?></td>
                          <td id='<?php echo $each['rid']; ?>' class='text-center'><?php echo $each['ro_office']; ?></td>
                          <td id='<?php echo $each['urid']; ?>' class='text-center'><?php echo $each['f_name'].' '.$each['l_name']; ?></td>
                          <td class='text-center'><input type='text' name='qty'class="text-center" value="<?php echo $each['qty']; ?>"></td>      
                          <td class="text-center"><button type='button' class='btn btn-danger remove'><i class='glyphicon glyphicon-remove'></i></td>
                        </tr>
                       
                    <?php $i++; } ?>
                </tbody>
            
            <?php }else { ?>

                <tbody id="tbodyid">

                </tbody>
              
            <?php } ?>
          </table>
        </div>
      </div>
    </div> 
     <!-- added for test name seprate view done by Shreeya on 07-11-2022-->
    <div class="row border-top border-dark">
   
      <div class="col-md-3 p-3">
          <label>Select Test Parameter<span class="required-star">*</span></label>
          <select class="form-control" id="parameterList" require>
            <?php foreach ($test_name as $test) { ?>
                  <option value="<?php echo $test['test_code'] ?>"><?php echo $test['test_name'] ?></option>
            <?php }  ?>
          </select>
      </div>
      <div class="col-md-9 p-3">
        <strong class="text-primary">Note: Select Test Parameters To process ILC For This Sample </strong>
      </div> 
    </div>
    <div class="row">
      <div class="col-md-12 table-responsive">
            <strong>Selected Test Parameters</strong>
            <table class="table table-bordered table-striped table-hover">
              <thead>
                <tr class="table-secondary">
                  <th class="text-center">Sr. No.</th>
                  <th class="text-center">Test Name</th>
                  <th class="text-center">Action</th>
                </tr>
              </thead>
               <!-- show saved selected record accroding to test name  09/11/2022 -->
              <?php if (!empty($SavedList)){ ?>

                <tbody id="TesTable">
                    <?php 
                        $j=1;
                        foreach($SavedList as $test) { ?>
                        <tr>
                          <td class='text-center'><?php echo $j; ?></td>
                          <td  id='<?php echo $test['tid']; ?>' class='text-center'><?php echo $test['test_name']; ?></td>
                          <td class="text-center"><button type='button' class='btn btn-danger remove'><i class='glyphicon glyphicon-remove'></i></td></tr>
                      
                    <?php $j++; } ?>
                </tbody>

                <?php }else { ?>

                <tbody id="TesTable">

                </tbody>

              <?php } ?>
            </table>
        </div>
    </div>
    <a href="#" class="btn btn-primary float-right" id="addselectbtn">save</a>
    <p id="flashnote"><b>Please save the selected RALs/CALs & Select Test Parameters then proceed to forward sample</b></p>
  </div>
  <!-- select min 5 ral to display save button 14/06/2022-->
  <?php if (!empty($getSavedList)){ ?>
    <input type="hidden" id="getSavedList" value="<?php echo $getSavedList[0]['f_name']; ?>">  
  <?php } ?>
  <?php echo $this->Html->Script("sampleForward/ilc_forward_script") ?>
  <?php echo $this->Html->Script("sampleForward/ilc_forward_test_parameter") ?>
  