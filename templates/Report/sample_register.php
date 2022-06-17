
<div class="col-md-6">
<?php // $this->Form->create(null, ['id'=>"rpt_comm_sample", 'autocomplete'=>"off", 'target'=>"_blank"]); ?>
<!-- <form id="rpt_comm_sample" method="post" action="" autocomplete="off" target="_blank"> -->
    <input type="hidden" class="form-control" name="label_name" id="label_name">
    <fieldset class="fsStyle">
        <!-- <legend class="legendStyle">Parameters</legend> -->        
        <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'Jr Chemist' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Sr Chemist' || $_SESSION['role'] == 'Cheif Chemist' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC') { ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="checkbox">								
                        <label><input type="checkbox" id="period" class="validate[minCheckbox[2]] checkbox" name="period" value="period" value="" disabled>Period</label>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <!--<label class="control-label col-md-4" for="sel1">From Date</label>-->
                        <div class="col-md-12">
                            <div class="input-group input-append date" id="datePicker">                            
                                <div class="input-group mb-2">                                    
                                    <input type="text" class="form-control" name="from_date" id="from_date" title="From Date" placeholder="Form (dd/mm/yyyy)" id="letr_date" /> 
                                    <div class="input-group-prepend">
                                        <div class="input-group-text rounded-right"><span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Hint for the search from date, Added by Pravin Bhakare on 07-06-2019-->
                        <label class="control-label" for="sel1">(Select Range of 'Sample Registration' Date)</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="checkbox">

                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <!--	<label class="control-label col-md-4" for="sel1">To date</label>-->
                        <div class="col-md-12">
                            <div class="input-group input-append date" id="datePicker1">
                                <div class="input-group mb-2">                                    
                                    <input type="text" class="form-control" name="to_date" id="to_date" title="To Date" placeholder="TO (dd/mm/yyyy)" id="letr_date" />
                                    <div class="input-group-prepend">
                                        <div class="input-group-text rounded-right"><span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><br>
        <?php }	?>        
        <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'RO/SO OIC') {  ?>

            <!-- show loading image until ajax respond with result, added on 18th JAN 2021 by Aniket Ganvir -->
            <div class="row" id="loading_con">
                <div class="col-md-12">
                </div>
                <div class="col-md-12">
                    <?php echo $this->Html->image('other/loader.gif', array('class' => 'loader_img')); ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="checkbox">
                        <label><input type="checkbox" id="lab1" class="validate[minCheckbox[2]] checkbox" name="labo" value="labo" disabled>Offices</label>
                    </div>
                </div>
                <div class="col-md-12">
                    <select class="form-control validate[required]" id="lab" name="lab" onchange="getlab();">
                        <option hidden="hidden" value=''>-----Select-----</option>
                        <?php foreach ($user_flag as $user_flag1) : 	?>
                            <?php /*if($_SESSION['user_flag']=="RAL"){  ?>
                            <option value="<?php echo $_SESSION['user_flag']; ?>" selected><?php echo $_SESSION['user_flag']; ?></option>
                            <?php } */ ?>
                            <option value="<?php echo $user_flag1[0]['user_flag']; ?>"><?php echo $user_flag1[0]['user_flag']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        <?php } ?>
        <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' ||  $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'DOL') { ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="checkbox">
                        <label id="ral_lab2"><input type="checkbox" id="ral_lab1" class="validate[minCheckbox[2]] checkbox" name="ral_lab1" value="labo" disabled></label>
                    </div>
                </div>
                <div class="col-md-12">
                    <select class="form-control validate[required]" id="ral_lab" name="ral_lab" onchange="getuser();">
                        <option hidden="hidden" value=''>-----Select-----</option>
                        <option value='all'>All</option>
                        <?php //foreach ($ral_list as $ral_list): 	
                        ?>
                        <!--<option value="<?php echo $ral_list['Offices_RO_SRO']['office_code'] . "~" . $ral_list['Offices_RO_SRO']['office_type'] . " ," . $ral_list['Offices_RO_SRO']['office_addr']; ?>">
                            <?php echo $ral_list['Offices_RO_SRO']['office_type'] . "," . $ral_list['Offices_RO_SRO']['office_addr']; ?></option>-->
                        <?php //endforeach; 
                        ?>

                        <?php foreach ($office as $office1) :	?>
                            <option value="<?php echo $office1['posted_ro_office'] . "~" . $office1['user_flag'] . " ," . $office1['ro_office']; ?>">
                                <?php echo $office1['user_flag'] . "," . $office1['ro_office']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        <?php } ?>
        <?php if ($_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'DOL') { ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="checkbox">
                        <label><input type="checkbox" id="sample" class="validate[minCheckbox[2]] checkbox" name="sampleo" value="sampleo" disabled>Sample Type</label>
                    </div>
                </div>
                <div class="col-md-12">
                    <select class="form-control validate[required]" id="sample_type" name="sample_type">
                        <option hidden="hidden" value=''>-----Select-----</option>
                        <?php foreach ($Sample_Type as $Sample_Type1) :	?>
                            <option value="<?php echo $Sample_Type1['sample_type_code']; ?>"><?php echo $Sample_Type1['sample_type_desc']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        <?php } ?>
        <?php if ($_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'DOL') { ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="checkbox">
                        <label><input type="checkbox" id="cat" class="validate[minCheckbox[2]] checkbox" name="cat" value="cotto" disabled>Category</label>
                    </div>
                </div>
                <div class="col-md-12">
                    <select class="form-control validate[required]" id="Category" name="Category">
                        <option hidden="hidden" value=''>-----Select-----</option>
                        <?php /* <?php foreach ($category_commodity as $Category1):	?>
                            <option value="<?php echo $Category1['category_commodity']['category_code']; ?>"><?php echo $Category1['category_commodity']['category_name']; ?></option>
                            <?php endforeach; ?> */ ?>
                    </select>
                </div>
            </div>
        <?php } ?>
        <?php if ($_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'DOL') { ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="checkbox">
                        <label><input type="checkbox" id="commo" class="validate[minCheckbox[2]] checkbox" name="commo" value="commo" disabled>Commodity</label>
                    </div>
                </div>
                <div class="col-md-12">
                    <select class="form-control validate[required]" id="Commodity" name="Commodity">
                        <option hidden="hidden" value=''>-----Select-----</option>
                        <?php foreach ($Commodity as $Commodity1) :	?>
                            <option value="<?php echo $Commodity1['commodity_code']; ?>"><?php echo $Commodity1['commodity_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        <?php } ?>
    </fieldset>

	<div class="row">
    <div class="col-md-6 col-md-offset-4 text-center">
        <span>
            <button class="btn btn-primary" type="submit" name="save" id="save">Generate Report</button>
        </span>
        <span>
            <button class="btn btn-primary" name="cancel" id="cancel">Cancel</button>
        </span>
        <span>
            <button class="btn btn-primary" name="close" id="close">Close</button>
        </span>
        <button type="reset" id="btn_reset" style="display: none;"></button>
    </div>
	</div>
    
<!-- </form> -->
<?php $this->Form->end(); ?>
</div>

<?php //pr($sample_inward1); exit;
if (isset($sample_inward) && $sample_inward != '') {
	echo $this->element('report/sample_inward_view');
} elseif (isset($sample_inward1)) {
	echo $this->element('report/sample_inward_view_one');
} elseif (isset($test_report)) {
	echo $this->element('report/test_report_view');
}
?>

<!-- below is JS of Pagination and export Div functions -->
<?php echo $this->Html->script('reportCountSample/pagerAndPrintDiv'); ?>
<!--  -->
<!-- below is CSS of count_sample -->
<?php echo $this->Html->css('reportCountSample/count_sample');  ?>
<?php echo $this->Html->css('reportCountSample/style_count_sample');  ?>
<!--  -->
<!-- below is JS of jQuery get_chemist_sample ajax function -->
<?php echo $this->Html->script('reportCountSample/getChemistSample'); ?>
<!--  -->
<!-- below is JS of jQuery hideparameters, ajax-get_lab_name && datepicker functions -->
<?php echo $this->Html->script('reportCountSample/documentOnReady'); ?>
<!--  -->
<!-- below is JS of jQuery getuser, getuserdelayed, getlab, ajax-search_value && save-cancel-close functions -->
<?php echo $this->Html->script('reportCountSample/remainingAjax'); ?>
<!--  -->
<!-- below is JS of Intialisation of Pager -->
<?php echo $this->Html->script('reportCountSample/pagerInit'); ?>
<!--  -->

<?php
echo $this->Html->Script('bootstrap-datepicker.min');
echo $this->Html->script('reportCountSample/report-functionality');
//print  $this->Session->flash("flash", array("element" => "flash-message_new")); 
?>

