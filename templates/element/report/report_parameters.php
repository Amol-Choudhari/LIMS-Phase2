<?php echo $this->Form->create(null, ['id'=>"rpt_comm_sample", 'autocomplete'=>"off", 'target'=>"_blank"]); ?>
<!-- <form id="rpt_comm_sample" method="post" action="" autocomplete="off" target="_blank"> -->
    <input type="hidden" class="form-control" name="label_name" id="label_name">
    <fieldset class="fsStyle">
        <!-- <legend class="legendStyle">Parameters</legend> -->
        <?php if ($_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'Admin') { ?>
            <div class="row" id="paralast">
                <div class="col-md-12">
                    <div class="checkbox">
                        <label><input type="checkbox" id="last" class="validate[minCheckbox[2]] checkbox" value="" disabled>During Last</label>
                    </div>
                </div>
                <div class="col-md-12">
                    <label class="control-label" for="sel1">days</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="days" name="days">
                    </div>
                </div>
            </div>
        <?php } ?>
        <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'Jr Chemist' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Sr Chemist' || $_SESSION['role'] == 'Cheif Chemist' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC') { ?>
            <div class="row" id="paraperiod">
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
            <div class="row" id="parato_date">
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
        <?php if ($_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL') {  ?>
            <div class="row" id="paramonth">
                <div class="col-md-12">
                    <div class="checkbox">
                        <label><input type="checkbox" id="month1" class="validate[minCheckbox[2]] checkbox" name="montho" value="montho" disabled>Month</label>
                    </div>
                </div>
                <div class="col-md-12">
                    <select class="form-control validate[required]" id="month" name="month">
                        <option value=''>----Select----</option>
                        <option value='1'>Janaury</option>
                        <option value='2'>February</option>
                        <option value='3'>March</option>
                        <option value='4'>April</option>
                        <option value='5'>May</option>
                        <option value='6'>June</option>
                        <option value='7'>July</option>
                        <option value='8'>August</option>
                        <option value='9'>September</option>
                        <option value='10'>October</option>
                        <option value='11'>November</option>
                        <option value='12'>December</option>
                    </select>
                </div>
            </div>
        <?php } ?>
        <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'RO/SO OIC') {  ?>

            <!-- show loading image until ajax respond with result, added on 18th JAN 2021 by Aniket Ganvir -->
            <div class="row" id="loading_con">
                <div class="col-md-12">
                </div>
                <div class="col-md-12">
                    <?php echo $this->Html->image('other/loader.gif', array('class' => 'loader_img')); ?>
                </div>
            </div>

            <div class="row" id="paralab1">
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
            <div class="row" id="pararal_lab">
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
            <div class="row" id="parasample_type">
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
            <div class="row" id="paraCategory">
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
            <div class="row" id="paraCommodity">
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
        <?php if ($_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'Admin') { ?>
            <div class="row" id="paratest">
                <div class="col-md-12">
                    <div class="checkbox">
                        <label><input type="checkbox" id="test1" class="validate[minCheckbox[2]] checkbox" name="testo" value="testo" disabled>Test</label>
                    </div>
                </div>
                <div class="col-md-12">
                    <select class="form-control validate[required]" id="test" name="test">
                        <option hidden="hidden" value=''>-----Select-----</option>
                        <?php foreach ($Test as $Test1) :	?>
                            <option value="<?php echo $Test1['Test']['test_code']; ?>"><?php echo $Test1['Test']['test_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        <?php } ?>
        <?php if ($_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'DOL') { ?>
            <div class="row" id="parauser_a">
                <div class="col-md-12">
                    <div class="checkbox">
                        <label><input type="checkbox" id="user1" class="validate[minCheckbox[2]] checkbox" name="user1" value="usero" disabled>User</label>
                    </div>
                </div>
                <div class="col-md-12">
                    <select class="form-control validate[required]" id="user_a" name="user" required>
                        <option hidden="hidden" value=''>-----Select-----</option>
                        <?php foreach ($user as $user1) : 	?>
                            <option value="<?php echo $user1['Dmi_user']['id']; ?>"><?php echo $user1['Dmi_user']['f_name']; ?> <?php echo $user1['Dmi_user']['l_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        <?php } ?>
        <?php if ($_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Jr Chemist' || $_SESSION['role'] == 'Sr Chemist' || $_SESSION['role'] == 'Cheif Chemist' ||  $_SESSION['role'] == 'DOL') { ?>
            <div class="row" id="parachemist_code">
                <div class="col-md-12">
                    <div class="checkbox">
                        <label><input type="checkbox" id="code2" class="validate[minCheckbox[2]] checkbox" name="code2" value="codeoo" disabled>Sample Code Available at Chemist</label>
                    </div>
                </div>

                <?php //pr($user_str); 
                ?>
                <div class="col-md-12">
                    <select class="form-control" id="chemist_code" name="chemist_code" required>
                        <option value="">-----Select----- </option>
                    </select>
                </div>
            </div>
        <?php } ?>
        <?php if ($_SESSION['role'] == 'DOL' ||  $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'RO Officer') { ?>
            <div class="row" id="parasample_code">
                <div class="col-md-12">
                    <div class="checkbox">
                        <label><input type="checkbox" id="code1" class="validate[minCheckbox[2]] checkbox" name="code1" value="codeo" disabled>Sample Code</label>
                    </div>
                </div>
                <div class="col-md-12">
                    <select class="form-control" id="Sample_code" name="sample_code" required>
                        <option value="">-----Select----- </option>
                    </select>
                </div>
            </div>
        <?php } ?>
    </fieldset>

    
<!-- </form> -->
<?php echo $this->Form->end(); ?>

<div class="row parameters">
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