<?php ?>
    <div id="myModal" class="modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content col-md-10">
                <div class="modal-header"><h3 class="card-title-new" id="test_title"></h3><button type="button" class="close" id="close" data-dismiss="modal">&times;</button></div>
                    <div class="card-title-new" id="test_formulae"></div>
				        <?php echo $this->Form->create(null,array('name'=>'modal_test','id'=>'modal_test'));?>
                            <div class="modal-body">
					                <?php //pr($_SESSION);?>
                                    <input type="hidden" class="form-control" id="sample" name="chemist_code">
                                    <input type="hidden" class="form-control" id="test_v" name="test_code">
                                    <input type="hidden" name="tran_date" id="tran_date"  class="form-control" value="<?php echo date('Y-m-d');?>">
                                    <input type="hidden" name="user_code" id="user_code"  class="form-control" value="<?php echo $_SESSION["user_code"];?>">
                                    <input type="hidden" name="test_perfm_date" id="test_perfm_date"  class="form-control" value="<?php echo date('Y-m-d');?>">

                                    <!--Below DIV is for tests-->
                                    <div class="col-md-12"><div id="input_parameter_text"></div></div>

                                    <!--Below ROW is for the buttons / Reasons-->
                                    <div class="row">
                                        <div class="form-group" id="abc">
                                            <div class="form-group">
                                            <label class="control-label col-md-6" for="">Reason for Test Can't Perform</label>
                                            <div class="col-md-6">
                                                <textarea class="form-control"  rows="2" cols="200" name="remark" id="remark" required  placeholder="Enter remark" ></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer btpn">
                                <a href="#" id="calculate" class="btn btn-primary done">Calculate</a>
                                <button type="submit" id="save" name="save_test" class="btn btn-primary" disabled>Save</button>
                                <?php //echo $this->Form->submit('Save', array('name'=>'save_test', 'id'=>'save', 'label'=>false,'class'=>'btn btn-primary','disabled'=>true)); ?>
                                <label id="test_r" class="btn btn-primary" >Can't Perform Test</label>
                                <a href="#" type="button" class="btn btn-default" id="close1" data-dismiss="modal">Close</a>
                            </div>
			  <?php echo $this->Form->end(); ?>
            </div>
        </div>
    </div>
