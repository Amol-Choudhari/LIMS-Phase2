<!-- below condition/code applied on 09-08-2022 by Amol-->
<?php if ($showNablLogo=='yes') { ?>
    <tr><td></td><td><h5><span style="font-size:10px;">NABL Accredited : </span></h5></td></tr>
<?php } ?>

<!-- all parameters value rows for regular report and if NABL then accredited parameter value row -->
<?php if(isset($table_str)){ echo $table_str; }?>

<!-- below condition/code applied on 09-08-2022 by Amol-->
<?php if ($showNablLogo=='yes') { ?>

    <!-- conditionally closing table here to separate table on next page for non acredited-->
    </table>
    <br pagebreak="true" />
    <table width="100%" border="1">

        <?php echo $this->element('/final_sample_test_report/report_table_headers_main'); ?>
        <tr><td></td><td><h5><span style="font-size:10px;">NABL Non Accredited : </span></h5></td></tr>

        <!-- if nabl non accredited parameters rows-->
        <?php if(isset($table_str2)){ echo $table_str2; }?>
<?php } ?>