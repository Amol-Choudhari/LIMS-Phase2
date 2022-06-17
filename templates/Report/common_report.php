<style>
    h4 {
        padding: 5px;
        font-family: times;
        font-size: 12pt;
    }

    h5 {
        padding: 5px;
        font-family: times;
        font-size: 11pt;
    }

    table {
        padding: 3px 5px;
        font-size: 9pt;
        font-family: times;
    }
</style>

<?php
if (!empty($records)) {
    $i = 1;
?>

    <table width="100%" border="1">
        <tr>
            <td width="12%" align="center">
                <img width="35" src="img/logos/emblem.png">
            </td>
            <td width="76%" align="center">
                <h4>Government of India <br> Ministry of Agriculture and Farmers Welfare<br>
                    Department of Agriculture Co-Operation & Farmers Welfare<br>
                    Directorate of Marketing & Inspection<br>
                </h4>
            </td>
            <td width="12%" align="center">
                <img src="img/logos/agmarklogo.png">
            </td>
        </tr>
    </table>

    <table>
        <tr>
            <td width="80%" align="center">
                <h5 style="text-align:center">Bifercation of Sample for the month of <?php echo $month_name;
                                                                                        echo ", ";
                                                                                        echo $year; ?> </h5>
            </td>
            <td width="20%" align="right">
                <h5><b>Date: <?php echo date("d/m/Y"); ?></b></h5>
            </td>
        </tr>
    </table>

    <table width="100%" border="1" align="center">

        <tr>
            <th>Sr No </th>
            <th> Name of Chemist </th>
            <th colspan="7">No. of sample analysed</th>
            <th>No. of working days</th>
            <th>Any other work attended</th>
            <th>Whether sample analysed as per norms</th>
        </tr>
        <tr>
            <th></th>
            <th></th>
            <th>Check</th>
            <th>Private Sample</th>
            <th>Sample from CAL</th>
            <th>Research</th>
            <th>Proficiency/ILC</th>
            <th>Internal Check</th>
            <th>Total</th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
        <?php
        foreach ($records as $record) {
            $recby_ch_date = $record['recby_ch_date'];
            $commencement_date = $record['commencement_date'];
            if ($recby_ch_date == '' || $commencement_date == '') {
                $working_days = '0';
            } else {
                $diff = abs(strtotime($commencement_date) - strtotime($recby_ch_date));
                $years = floor($diff / (365 * 60 * 60 * 24));
                $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
                $working_days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
            } ?>
            <tr>
                <td><?php echo $i; ?></td>
                <td><?php echo $record['name_chemist']; ?></td>
                <td><?php echo $record['check_count'] ?></td>
                <td><?php //echo $record['pvt_count'] ?></td>
                <td><?php echo $record['sample_frm_cal']; ?></td>
                <td><?php //echo $record['research_count']; ?></td>
                <td><?php //echo $record['ilc_count'] ?></td>
                <td><?php //echo $record['internal_check_count'] ?></td>
                <td><?php echo $record['check_count'] + $record['pvt_count'] + $record['sample_frm_cal'] + $record['research_count'] + $record['ilc_count']; ?></td>
                <td><?php echo $working_days ?></td>
                <td><?php echo $record['other']; ?></td>
                <td><?php echo $record['norms']; ?></td>
            </tr>
        <?php $i++;
        } ?>
    </table>
<?php
} else {
    echo "no data";
}
?>
<div style='margin-top:20px'>
    <h6 style="text-align:right; margin:0px"><b><?php echo $_SESSION["f_name"] . " " . $_SESSION["l_name"]; ?></b></h6>
    <h6 style="text-align:right; margin:0px"><b>(<?php echo $_SESSION["username"]; ?>)</b></h6>
    <h6 style="text-align:right"><b><?php echo $_SESSION["role"]; ?></b></h6>
</div>