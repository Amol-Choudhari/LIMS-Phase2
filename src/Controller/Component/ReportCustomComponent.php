<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use DateTime;

class ReportCustomComponent extends Component
{

    /********************************************************************************************************************
     * Role RO OFFICER
     ********************************************************************************************************************/
    public static function getRoRejectSample($from_date, $to_date, $posted_ro_office, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ro_reject_sample WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT si.received_date,si.reject_date,CONCAT(du.f_name,' ',du.l_name) AS fullname,CONCAT(r.user_flag,',',mll.ro_office) AS labname,si.org_sample_code,mc.commodity_name,mst.sample_type_desc
                FROM sample_inward as si 
                JOIN (SELECT org_sample_code,max(id) AS id FROM workflow WHERE dst_loc_id='$ral_lab_no' GROUP BY(org_sample_code)) wf ON ((si.org_sample_code=wf.org_sample_code))
                JOIN workflow wff ON ((wf.id=wff.id))						
                Inner Join dmi_ro_offices AS mll ON mll.id=si.loc_id 
                Inner Join m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code 
                Inner Join dmi_users AS du ON du.id=si.user_code 
                Inner Join dmi_user_roles AS r On r.user_email_id=du.email
                Inner Join m_commodity AS mc ON mc.commodity_code=si.commodity_code And si.display='Y' AND si.acc_rej_flg='R'
                WHERE date(si.received_date) BETWEEN '$from_date' AND '$to_date'");
        $records = $query->fetchAll('assoc');
        
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $received_date = $record['received_date'];
                $reject_date = $record['reject_date'];
                $fullname = $record['fullname'];
                $labname = $record['labname'];
                $org_sample_code = $record['org_sample_code'];
                $commodity_name = $record['commodity_name'];
                $sample_type_desc = $record['sample_type_desc'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ro_reject_sample (sr_no,user_id, received_date, reject_date, fullname, labname, org_sample_code, commodity_name, sample_type_desc, report_date) 
                VALUES (
                '$i','$user_id','$received_date', '$reject_date', '$fullname', '$labname', '$org_sample_code', '$commodity_name', '$sample_type_desc', '$date')");

                $update = $con->execute("UPDATE temp_reportico_ro_reject_sample SET counts = (SELECT COUNT(user_id) FROM temp_reportico_ro_reject_sample WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getRoSampleRegister($from_date, $to_date, $posted_ro_office, $lab, $commodity, $category, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ro_sample_register WHERE user_id = '$user_id'");

        $str = "SELECT si.letr_date,mct.category_name,CONCAT(r.user_flag,',',mll.ro_office) AS labname, si.letr_ref_no, si.org_sample_code,si.stage_sample_code,si.sample_total_qnt AS sample_qnt,mu.unit_weight,mp.par_condition_desc,si.sample_total_qnt,si.received_date, mc.commodity_name,si.commodity_code, si.category_code,st.sample_type_desc,si.loc_id, si.dispatch_date,si.dispatch_date, si.grading_date,si.remark,r.user_flag,sample_total_qnt
                FROM sample_inward AS si
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN m_commodity_category AS mct ON mct.category_code=si.category_code
                INNER JOIN m_par_condition AS mp ON mp.par_condition_code=si.par_condition_code
                INNER JOIN m_unit_weight AS mu ON mu.unit_id=si.parcel_size
                INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN dmi_users AS du ON du.id=si.user_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=du.email";

        $str .= " WHERE si.display='Y' AND si.acc_rej_flg='A' AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date'";

        if ($_SESSION['user_flag'] == "RO" || $_SESSION['user_flag'] == "SO") {
            $str .= " AND si.loc_id='" . $_SESSION['posted_ro_office'] . "'";
        }
        if ($_SESSION['user_flag'] == "RO" || $_SESSION['user_flag'] == "SO") {
            $str .= " AND r.user_flag='" . $_SESSION['user_flag'] . "'";
        }

        $str .= "AND si.loc_id='$ral_lab_no'  AND r.user_flag='$ral_lab_name'  AND si.commodity_code='$commodity' AND si.category_code='$category' GROUP BY si.stage_sample_code,si.letr_date,sample_qnt,si.received_date,si.dispatch_date,si.grading_date,mll.ro_office,r.user_flag, mc.commodity_name,mct.category_name,si.commodity_code, si.category_code,st.sample_type_desc,si.loc_id,si.letr_ref_no,si.sample_total_qnt,mu.unit_weight,mp.par_condition_desc,si.dispatch_date, si.org_sample_code,si.remark
            ORDER BY received_date ASC";

        $query = $con->execute($str);
        $records = $query->fetchAll('assoc');
    
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $letr_date = $record['letr_date'];
                $category_name = $record['category_name'];
                $labname = $record['labname'];
                $letr_ref_no = $record['letr_ref_no'];
                $org_sample_code = $record['org_sample_code'];
                $stage_sample_code = $record['stage_sample_code'];
                $sample_qnt = $record['sample_qnt'];
                $unit_weight = $record['unit_weight'];
                $par_condition_desc = $record['par_condition_desc'];
                $sample_total_qnt = $record['sample_total_qnt'];
                $received_date = $record['received_date'];
                $commodity_name = $record['commodity_name'];
                $commodity_code = $record['commodity_code'];
                $category_code = $record['category_code'];
                $sample_type_desc = $record['sample_type_desc'];
                $loc_id = $record['loc_id'];
                $dispatch_date = $record['dispatch_date'];
                $grading_date = $record['grading_date'];
                $remark = $record['remark'];
                $user_flag = $record['user_flag'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ro_sample_register (sr_no, user_id, letr_date, category_name, labname, letr_ref_no, org_sample_code, stage_sample_code, sample_qnt, unit_weight, par_condition_desc, sample_total_qnt, received_date, commodity_name, commodity_code, category_code, sample_type_desc, loc_id, dispatch_date, grading_date, remark, user_flag, report_date) 
                VALUES (
                '$i','$user_id','$letr_date', '$category_name', '$labname', '$letr_ref_no', '$org_sample_code', '$stage_sample_code', '$sample_qnt', '$unit_weight', '$par_condition_desc', '$sample_total_qnt', '$received_date', '$commodity_name', '$commodity_code', '$category_code', '$sample_type_desc', '$loc_id', '$dispatch_date', '$grading_date', '$remark', '$user_flag', '$date')");

                $update = $con->execute("UPDATE temp_reportico_ro_sample_register SET counts = (SELECT COUNT(user_id) FROM temp_reportico_ro_sample_register WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getRoSampleRegistration($from_date, $to_date, $posted_ro_office, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ro_sample_registration WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT mc.commodity_name,si.grade,CONCAT(sd.pckr_nm,', ',pckr_addr) AS name_address_packer,sd.lot_no,si.received_date AS pack_date,sd.pack_size,sd.tbl,CONCAT(sd.shop_name,', ',sd.shop_address) AS shop_name_address,si.parcel_size,sd.smpl_drwl_dt,CONCAT(u.f_name,' ',u.l_name) AS name_officer,si.org_sample_code,si.dispatch_date,CONCAT(r.user_flag,', ',mll.ro_office) AS lab_or_office_name
                FROM sample_inward AS si
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN dmi_users AS u ON u.id=si.user_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN sample_inward_details AS sd ON sd.org_sample_code=si.stage_sample_code AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date' AND si.loc_id='$ral_lab_no' AND r.user_flag='$ral_lab_name'
                GROUP BY mll.ro_office,r.user_flag,si.grading_date,si.remark,si.grade,u.f_name,u.l_name,mc.commodity_name,si.grade,sd.pckr_nm,pckr_addr,si.received_date,sd.lot_no,sd.pack_size,sd.tbl,sd.shop_name,sd.shop_address,si.parcel_size,sd.smpl_drwl_dt, si.org_sample_code,si.dispatch_date,si.ral_lab_code,si.ral_anltc_rslt_rcpt_dt,si.anltc_rslt_chlng_flg,si.misgrd_param_value,si.misgrd_report_issue_dt, si.misgrd_reason,si.chlng_smpl_disptch_cal_dt,si.cal_anltc_rslt_rcpt_dt
                ORDER BY sd.smpl_drwl_dt DESC");
        $records = $query->fetchAll('assoc');
        
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $commodity_name = $record['commodity_name'];
                $grade = $record['grade'];
                $name_address_packer = $record['name_address_packer'];
                $lot_no = $record['lot_no'];
                $pack_date = $record['pack_date'];
                $pack_size = $record['pack_size'];
                $tbl = $record['tbl'];
                $shop_name_address = $record['shop_name_address'];
                $parcel_size = $record['parcel_size'];
                $smpl_drwl_dt = $record['smpl_drwl_dt'];
                $name_officer = $record['name_officer'];
                $org_sample_code = $record['org_sample_code'];
                $dispatch_date = $record['dispatch_date'];
                $lab_or_office_name = $record['lab_or_office_name'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ro_sample_registration (sr_no, user_id, commodity_name, grade, name_address_packer, lot_no, pack_date, pack_size, tbl, shop_name_address, parcel_size, smpl_drwl_dt, name_officer, org_sample_code, dispatch_date, lab_or_office_name, report_date) 
                VALUES (
                '$i','$user_id','$commodity_name', '$grade', '$name_address_packer', '$lot_no', '$pack_date', '$pack_size', '$tbl', '$shop_name_address', '$parcel_size', '$smpl_drwl_dt', '$name_officer', '$org_sample_code', '$dispatch_date', '$lab_or_office_name', '$date')");

                $update = $con->execute("UPDATE temp_reportico_ro_sample_registration SET counts = (SELECT COUNT(user_id) FROM temp_reportico_ro_sample_registration WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    /********************************************************************************************************************
     * Role Inward OFFICER
     ********************************************************************************************************************/
    public static function getIoSampleRegister($from_date, $to_date, $posted_ro_office, $lab, $sample, $commodity, $category, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_io_sample_register WHERE user_id = '$user_id'");
        $str = "SELECT si.letr_date,mct.category_name,CONCAT(r.user_flag,',',mll.ro_office) AS labname, si.letr_ref_no, si.org_sample_code,si.stage_sample_code,si.sample_total_qnt AS sample_qnt,mu.unit_weight,mp.par_condition_desc,si.sample_total_qnt,si.received_date, mc.commodity_name,si.commodity_code, si.category_code,st.sample_type_desc,si.loc_id, si.dispatch_date,si.dispatch_date, si.grading_date,si.remark,r.user_flag,sample_total_qnt
                FROM sample_inward AS si
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN m_commodity_category AS mct ON mct.category_code=si.category_code
                INNER JOIN m_par_condition AS mp ON mp.par_condition_code=si.par_condition_code
                INNER JOIN m_unit_weight AS mu ON mu.unit_id=si.parcel_size
                INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN dmi_users AS du ON du.id=si.user_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=du.email";

        $str .= " WHERE si.display='Y' AND si.acc_rej_flg='A' AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date'";

        if ($_SESSION['user_flag'] == "RO" || $_SESSION['user_flag'] == "SO") {
            $str .= " AND si.loc_id='" . $_SESSION['posted_ro_office'] . "'";
        }
        if ($_SESSION['user_flag'] == "RO" || $_SESSION['user_flag'] == "SO") {
            $str .= " AND r.user_flag='" . $_SESSION['user_flag'] . "'";
        }

        $str .= "AND si.loc_id='$ral_lab_no'  AND r.user_flag='$ral_lab_name' AND si.sample_type_code = '$sample' AND si.commodity_code='$commodity' AND si.category_code='$category' GROUP BY si.stage_sample_code,si.letr_date,sample_qnt,si.received_date,si.dispatch_date,si.grading_date,mll.ro_office,r.user_flag, mc.commodity_name,mct.category_name,si.commodity_code, si.category_code,st.sample_type_desc,si.loc_id,si.letr_ref_no,si.sample_total_qnt,mu.unit_weight,mp.par_condition_desc,si.dispatch_date, si.org_sample_code,si.remark
                ORDER BY received_date ASC";

        $query = $con->execute($str);
        $records = $query->fetchAll('assoc');
        
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $letr_date = $record['letr_date'];
                $category_name = $record['category_name'];
                $labname = $record['labname'];
                $letr_ref_no = $record['letr_ref_no'];
                $org_sample_code = $record['org_sample_code'];
                $stage_sample_code = $record['stage_sample_code'];
                $sample_qnt = $record['sample_qnt'];
                $unit_weight = $record['unit_weight'];
                $par_condition_desc = $record['par_condition_desc'];
                $sample_total_qnt = $record['sample_total_qnt'];
                $received_date = $record['received_date'];
                $commodity_name = $record['commodity_name'];
                $commodity_code = $record['commodity_code'];
                $category_code = $record['category_code'];
                $sample_type_desc = $record['sample_type_desc'];
                $loc_id = $record['loc_id'];
                $dispatch_date = $record['dispatch_date'];
                $grading_date = $record['grading_date'];
                $remark = $record['remark'];
                $user_flag = $record['user_flag'];
                $date = date("d/m/Y");

                $insert = $con->execute("INSERT INTO temp_reportico_io_sample_register (sr_no, user_id,letr_date, category_name, labname, letr_ref_no, org_sample_code, stage_sample_code, sample_qnt, unit_weight, par_condition_desc, sample_total_qnt, received_date, commodity_name, commodity_code, category_code, sample_type_desc, loc_id, dispatch_date, grading_date, remark, user_flag, report_date) 
                VALUES (
                '$i','$user_id','$letr_date', '$category_name', '$labname', '$letr_ref_no', '$org_sample_code', '$stage_sample_code', '$sample_qnt', '$unit_weight', '$par_condition_desc', '$sample_total_qnt', '$received_date', '$commodity_name', '$commodity_code', '$category_code', '$sample_type_desc', '$loc_id', '$dispatch_date', '$grading_date', '$remark', '$user_flag', '$date')");

                $update = $con->execute("UPDATE temp_reportico_io_sample_register SET counts = (SELECT COUNT(user_id) FROM temp_reportico_io_sample_register WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }

            return 1;
        } else {
            return 0;
        }
    }

    public static function getIoSampleReceivedRoSoRalCal($from_date, $to_date, $commodity, $posted_ro_office, $lab, $role, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_io_sample_received_rosoralcal WHERE user_id = '$user_id'");

        $sql = "SELECT si.received_date,si.org_sample_code,mct.category_name,mc.commodity_name,mst.sample_type_desc,du.role,mll.ro_office,r.user_flag,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name
		        FROM sample_inward as si 
				INNER JOIN m_commodity_category AS mct ON mct.category_code=si.category_code
				INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id 
				INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code 
				INNER JOIN dmi_users AS du On du.id=si.user_code
				INNER JOIN dmi_user_roles AS r On r.user_email_id=du.email
				INNER JOIN workflow AS w On w.org_sample_code=si.org_sample_code
				INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code And si.display='Y'";

        if ($from_date != '' && $to_date != '') {
            $sql .= " WHERE date(si.received_date) BETWEEN '$from_date' and '$to_date'";
        }
        if ($commodity != '') {
            $sql .= " AND si.commodity_code='$commodity'";
        }
        if ($lab == "RO" || $lab == "SO") {
            $sql .= " AND si.loc_id='" . $posted_ro_office . "'";
        } else if ($lab == "RAL" && $role == "Inward Officer") {
            $sql .= " AND w.dst_loc_id='" . $posted_ro_office . "'";
        } else if ($lab == "CAL" && $role == "Inward Officer") {
            $sql .= " AND w.dst_loc_id='" . $posted_ro_office . "'";
        }
        $sql .= " Group By du.role,si.org_sample_code,mct.category_name,mc.commodity_name,mst.sample_type_desc,si.received_date,mll.ro_office,r.user_flag,du.f_name,du.l_name";

        $sql .= " ORDER BY si.received_date asc";

        $query = $con->execute($sql);
        $records = $query->fetchAll('assoc');
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $received_date = $record['received_date'];
                $org_sample_code = $record['org_sample_code'];
                $category_name = $record['category_name'];
                $commodity_name = $record['commodity_name'];
                $sample_type_desc = $record['sample_type_desc'];
                $role = $record['role'];
                $ro_office = $record['ro_office'];
                $user_flag = $record['user_flag'];
                $lab_name = $record['lab_name'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_io_sample_received_rosoralcal (sr_no, user_id,received_date, org_sample_code, category_name, commodity_name, sample_type_desc, role, ro_office, user_flag, lab_name, report_date) 
                VALUES (
                '$i','$user_id','$received_date', '$org_sample_code', '$category_name', '$commodity_name', '$sample_type_desc', '$role', '$ro_office', '$user_flag', '$lab_name','$date')");

                $update = $con->execute("UPDATE temp_reportico_io_sample_received_rosoralcal SET counts = (SELECT COUNT(user_id) FROM temp_reportico_io_sample_received_rosoralcal WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getIoSampleAcceptedByChemistForTesting($from_date, $to_date, $posted_ro_office, $lab, $user, $ral_lab, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_io_sample_accepted_chemist_testing WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT mll.ro_office,CONCAT(du.f_name,' ',du.l_name,' (',du.role,')') AS chemist_name,si.received_date,msa.org_sample_code,mc.commodity_name,mst.sample_type_desc,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name
                FROM m_sample_allocate AS msa
                INNER JOIN sample_inward AS si ON si.org_sample_code=msa.org_sample_code
                INNER JOIN m_commodity AS mc ON si.commodity_code=mc.commodity_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code
                INNER JOIN dmi_users AS du ON du.id=msa.alloc_to_user_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=du.email 
                WHERE  msa.display='Y' AND msa.acptnce_flag='Y' AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date' AND msa.lab_code='$ral_lab_no' AND r.user_flag='$ral_lab_name' AND msa.alloc_to_user_code='$user'");
        
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $ro_office = $record['ro_office'];
                $chemist_name = $record['chemist_name'];
                $received_date = $record['received_date'];
                $org_sample_code = $record['org_sample_code'];
                $commodity_name = $record['commodity_name'];
                $sample_type_desc = $record['sample_type_desc'];
                $lab_name = $record['lab_name'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_io_sample_accepted_chemist_testing (sr_no, user_id,ro_office, chemist_name, received_date, org_sample_code, commodity_name, sample_type_desc, lab_name, report_date) 
                VALUES (
                '$i','$user_id','$ro_office', '$chemist_name', '$received_date', '$org_sample_code', '$commodity_name', '$sample_type_desc', '$lab_name','$date')");

                $update = $con->execute("UPDATE temp_reportico_io_sample_accepted_chemist_testing SET counts = (SELECT COUNT(user_id) FROM temp_reportico_io_sample_accepted_chemist_testing WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getIoRejectSample($from_date, $to_date, $posted_ro_office, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_io_reject_sample WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT si.received_date,si.reject_date,CONCAT(du.f_name,' ',du.l_name) AS fullname,CONCAT(r.user_flag,',',mll.ro_office) AS labname,si.org_sample_code,mc.commodity_name,mst.sample_type_desc
                FROM sample_inward as si 
                JOIN (SELECT org_sample_code,max(id) AS id FROM workflow WHERE dst_loc_id='$ral_lab_no' GROUP BY(org_sample_code)) wf ON ((si.org_sample_code=wf.org_sample_code))
                JOIN workflow wff ON ((wf.id=wff.id))						
                Inner Join dmi_ro_offices AS mll ON mll.id=si.loc_id 
                Inner Join m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code 
                Inner Join dmi_users AS du ON du.id=si.user_code 
                Inner Join dmi_user_roles AS r On r.user_email_id=du.email
                Inner Join m_commodity AS mc ON mc.commodity_code=si.commodity_code And si.display='Y' AND si.acc_rej_flg='R'
                WHERE date(si.received_date) BETWEEN '$from_date' AND '$to_date'");
    
        $records = $query->fetchAll('assoc');
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $received_date = $record['received_date'];
                $reject_date = $record['reject_date'];
                $fullname = $record['fullname'];
                $labname = $record['labname'];
                $org_sample_code = $record['org_sample_code'];
                $commodity_name = $record['commodity_name'];
                $sample_type_desc = $record['sample_type_desc'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_io_reject_sample (sr_no,user_id,received_date, reject_date, fullname, labname, org_sample_code, commodity_name, sample_type_desc, report_date) 
                VALUES (
                '$i','$user_id','$received_date', '$reject_date', '$fullname', '$labname', '$org_sample_code', '$commodity_name', '$sample_type_desc', '$date')");

                $update = $con->execute("UPDATE temp_reportico_io_reject_sample SET counts = (SELECT COUNT(user_id) FROM temp_reportico_io_reject_sample WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getIoSamplePendingForDispatch($from_date, $to_date, $posted_ro_office, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_io_sample_pending WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT si.org_sample_code,mc.commodity_name,si.acc_rej_flg AS status,si.received_date,mst.sample_type_desc,mll.ro_office,r.user_flag
                FROM sample_inward AS si
                INNER JOIN code_decode AS cd ON cd.org_sample_code!=si.org_sample_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN m_commodity AS mc ON si.commodity_code=mc.commodity_code
                INNER JOIN dmi_users AS du ON du.id=si.user_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=du.email
                INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code AND si.display='Y' AND si.acc_rej_flg='P' AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date' AND si.loc_id='$ral_lab_no'
                GROUP BY mll.ro_office,si.acc_rej_flg,r.user_flag,si.org_sample_code,mc.commodity_name,si.received_date,mst.sample_type_desc
                ORDER BY si.received_date ASC");
        $records = $query->fetchAll('assoc');
        
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $org_sample_code = $record['org_sample_code'];
                $commodity_name = $record['commodity_name'];
                $status = $record['status'];
                $received_date = $record['received_date'];
                $sample_type_desc = $record['sample_type_desc'];
                $ro_office = $record['ro_office'];
                $user_flag = $record['user_flag'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_io_sample_pending (sr_no, user_id,org_sample_code, commodity_name, status, received_date, sample_type_desc, ro_office, user_flag, report_date) 
                VALUES (
                '$i','$user_id','$org_sample_code', '$commodity_name', '$status', '$received_date', '$sample_type_desc', '$ro_office', '$user_flag', '$date')");

                $update = $con->execute("UPDATE temp_reportico_io_sample_pending SET counts = (SELECT COUNT(user_id) FROM temp_reportico_io_sample_pending WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getIoSampleAnalyzedCount($from_date, $to_date, $posted_ro_office, $sample_type, $lab, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_io_sample_analyzed WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT mll.ro_office,mst.sample_type_desc,mc.commodity_name, COUNT(mc.commodity_name) AS count_samples, COUNT(CASE WHEN si.status_flag = 'FG' THEN 1 END) AS finalized
                FROM sample_inward AS si
                INNER JOIN m_commodity_category AS mcc ON si.category_code=mcc.category_code
                INNER JOIN m_commodity AS mc ON si.commodity_code=mc.commodity_code
                INNER JOIN m_sample_type AS mst ON si.sample_type_code=mst.sample_type_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN dmi_users AS u ON u.id=si.user_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND si.display='Y' AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date' AND mst.sample_type_code='$sample_type' AND si.loc_id='$ral_lab_no' AND r.user_flag='$ral_lab_name'
                GROUP BY mll.ro_office,mst.sample_type_desc,mc.commodity_name
                ORDER BY mc.commodity_name ASC");
        $records = $query->fetchAll('assoc');
        
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $ro_office = $record['ro_office'];
                $sample_type_desc = $record['sample_type_desc'];
                $commodity_name = $record['commodity_name'];
                $count_samples = $record['count_samples'];
                $finalized = $record['finalized'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_io_sample_analyzed (sr_no,user_id,ro_office, sample_type_desc, commodity_name, count_samples, finalized, report_date) 
                VALUES (
                '$i','$user_id','$ro_office', '$sample_type_desc', '$commodity_name', '$count_samples', '$finalized','$date')");

                $update = $con->execute("UPDATE temp_reportico_io_sample_analyzed SET counts = (SELECT COUNT(user_id) FROM temp_reportico_io_sample_analyzed WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getIoCodingDecodingSection($from_date, $to_date, $posted_ro_office, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_io_coding_decoding WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT mll.ro_office,mc.commodity_name,si.stage_sample_code,w.tran_date,si.received_date,si.sample_total_qnt,muw.unit_weight,mst.sample_type_desc,si.remark
                FROM sample_inward AS si
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN m_unit_weight AS muw ON muw.unit_id=si.parcel_size
                INNER JOIN dmi_users AS u ON u.id=si.user_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN workflow AS w ON w.org_sample_code=si.org_sample_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code AND w.stage_smpl_flag In ('OF','HF','FG') AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date' AND w.dst_loc_id='$ral_lab_no'
                GROUP BY mll.ro_office,mc.commodity_name,w.tran_date,si.stage_sample_code,si.received_date,si.sample_total_qnt,muw.unit_weight,mst.sample_type_desc,si.remark
                ORDER BY mc.commodity_name ASC");
        $records = $query->fetchAll('assoc');
        
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $ro_office = $record['ro_office'];
                $commodity_name = $record['commodity_name'];
                $stage_sample_code = $record['stage_sample_code'];
                $tran_date = $record['tran_date'];
                $sample_total_qnt = $record['sample_total_qnt'];
                $unit_weight = $record['unit_weight'];
                $sample_qnt = $sample_total_qnt . ' ' . $unit_weight;
                $sample_type_desc = $record['sample_type_desc'];
                $remark = $record['remark'];
                $received_date = $record['received_date'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_io_coding_decoding (sr_no, user_id,ro_office, commodity_name, stage_sample_code, received_date, tran_date, sample_qnt, sample_type_desc, remark, report_date) 
                VALUES (
                '$i','$user_id','$ro_office', '$commodity_name', '$stage_sample_code','$received_date', '$tran_date', '$sample_qnt','$sample_type_desc','$remark','$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getIoSampleAllotedToChemistForTesting($from_date, $to_date, $posted_ro_office, $lab, $user, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_io_sample_alloted_chemist_testing WHERE user_id = '$user_id'");

        $str = "SELECT si.org_sample_code,mc.commodity_name,si.received_date,mst.sample_type_desc,mll.ro_office,r.user_flag,CONCAT(du.f_name,' ',du.l_name,' (',du.role,')') AS chemist_name,msa.alloc_date
                FROM sample_inward AS si
                INNER JOIN code_decode AS cd ON cd.org_sample_code=si.org_sample_code
                INNER JOIN m_sample_allocate AS msa ON msa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN m_commodity AS mc ON si.commodity_code=mc.commodity_code
                INNER JOIN dmi_users AS du ON du.id=cd.alloc_to_user_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=du.email
                INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code AND cd.display='Y' AND cd.chemist_code!='-' AND DATE(msa.alloc_date) BETWEEN '$from_date' AND '$to_date' AND cd.alloc_to_user_code='$user'";

        if ($_SESSION['user_flag'] == "RAL") {
            $str .= " AND cd.lab_code='" . $_SESSION['posted_ro_office'] . "'";
        } else {
            $str .= " AND cd.lab_code='$ral_lab_no'";
        }

        $str .= " ORDER BY si.received_date ASC";

        $query = $con->execute($str);

        
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $org_sample_code = $record['org_sample_code'];
                $commodity_name = $record['commodity_name'];
                $chemist_name = $record['chemist_name'];
                $received_date = $record['received_date'];
                $org_sample_code = $record['org_sample_code'];
                $ro_office = $record['ro_office'];
                $sample_type_desc = $record['sample_type_desc'];
                $user_flag = $record['user_flag'];
                $alloc_date = $record['alloc_date'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_io_sample_alloted_chemist_testing (sr_no, user_id, ro_office, chemist_name, received_date, org_sample_code, commodity_name, sample_type_desc, user_flag, alloc_date, report_date) 
                VALUES (
                '$i','$user_id','$ro_office', '$chemist_name', '$received_date', '$org_sample_code', '$commodity_name', '$sample_type_desc', '$user_flag','$alloc_date','$date')");

                $update = $con->execute("UPDATE temp_reportico_io_sample_alloted_chemist_testing SET counts = (SELECT COUNT(user_id) FROM temp_reportico_io_sample_alloted_chemist_testing WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getIoSampleAllotedToChemistForRetesting($from_date, $to_date, $posted_ro_office, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_io_sample_alloted_chemist_retesting WHERE user_id = '$user_id'");

        $str = "SELECT mll.ro_office,msa.org_sample_code,mc.commodity_name,si.received_date,mst.sample_type_desc,CONCAT(du.f_name,' ',du.l_name) AS chemist_name,msa.alloc_date
                FROM m_sample_allocate AS msa
                INNER JOIN sample_inward AS si ON si.org_sample_code=msa.org_sample_code
                INNER JOIN m_commodity AS mc ON si.commodity_code=mc.commodity_code
                INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN dmi_users AS du ON du.id=si.user_code AND msa.display='Y' AND msa.test_n_r='R' AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date'";

        if ($_SESSION['user_flag'] == "RAL") {
            $str .= " AND msa.lab_code='" . $_SESSION['posted_ro_office'] . "'";
        } else {
            $str .= " AND msa.lab_code='$ral_lab_no'";
        }

        $str .= " ORDER BY si.received_date ASC";

        $query = $con->execute($str);

        
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $org_sample_code = $record['org_sample_code'];
                $commodity_name = $record['commodity_name'];
                $chemist_name = $record['chemist_name'];
                $received_date = $record['received_date'];
                $org_sample_code = $record['org_sample_code'];
                $ro_office = $record['ro_office'];
                $sample_type_desc = $record['sample_type_desc'];
                $alloc_date = $record['alloc_date'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_io_sample_alloted_chemist_retesting (sr_no, user_id,ro_office, chemist_name, received_date, org_sample_code, commodity_name, sample_type_desc, alloc_date, report_date) 
                VALUES (
               '$i', '$user_id','$ro_office', '$chemist_name', '$received_date', '$org_sample_code', '$commodity_name', '$sample_type_desc','$alloc_date','$date')");

                $update = $con->execute("UPDATE temp_reportico_io_sample_alloted_chemist_retesting SET counts = (SELECT COUNT(user_id) FROM temp_reportico_io_sample_alloted_chemist_retesting WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getIoTestedSample($from_date, $to_date, $posted_ro_office, $user_code, $commodity, $lab, $sample_type, $ral_lab, $role, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_io_tested_sample WHERE user_id = '$user_id'");

        if ($role == 'Jr Chemist' || $role == 'Sr Chemist' || $role == 'Cheif Chemist') {
            $str = "SELECT du.role,mll.ro_office,CONCAT(du.f_name,' ',du.l_name) as chemist_name,msa.recby_ch_date,mst.sample_type_desc,mc.commodity_name,cd.sample_code as org_sample_code,
            msa.expect_complt,msa.commencement_date ,CASE WHEN msa.commencement_date>msa.expect_complt THEN msa.commencement_date   ::date-msa.expect_complt ::date ELSE 0
            END as delay,expect_complt::date - recby_ch_date::date  as complete,si.received_date,
            r.user_flag,du.f_name,du.l_name,grade,
            (SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
            FROM dmi_users AS u
            INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
            INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
            WHERE u.status = 'active' AND o.id = '$ral_lab_no'
            GROUP BY ral_lab) AS lab_name
            FROM sample_inward as si 
            Inner Join m_sample_allocate as msa ON msa.org_sample_code=si.org_sample_code
            Inner Join m_sample_type as mst ON mst.sample_type_code=si.sample_type_code 
            Inner Join dmi_ro_offices as mll ON mll.id=si.loc_id 
            Inner Join code_decode as cd on cd.org_sample_code=si.org_sample_code
            Inner Join dmi_users as du ON du.id=cd.alloc_to_user_code
            Inner Join dmi_user_roles as r On r.user_email_id=du.email		
            Inner Join m_commodity as mc ON mc.commodity_code=si.commodity_code 
                WHERE cd.display='Y' and cd.alloc_to_user_code='" . $_SESSION['user_code'] . "' AND cd.lab_code='$posted_ro_office' AND date(si.received_date) BETWEEN '$from_date' and '$to_date'
                GROUP BY du.role,mll.ro_office,du.f_name,du.l_name,cd.sample_code,msa.commencement_date,msa.recby_ch_date,msa.expect_complt,cd.chemist_code,mc.commodity_name,mst.sample_type_desc,si.received_date,r.user_flag,du.f_name,du.l_name,si.grade order by si.received_date";
        } else {
            $str = "SELECT mll.ro_office,CONCAT(du.f_name,' ',du.l_name,' (',du.role,')') as chemist_name,du.role,msa.recby_ch_date,mst.sample_type_desc,mc.commodity_name,cd.sample_code as org_sample_code,
            msa.expect_complt,msa.commencement_date, CASE WHEN msa.commencement_date>msa.expect_complt THEN msa.commencement_date   ::date-msa.expect_complt ::date ELSE 0
            END as delay,expect_complt::date - recby_ch_date::date  as complete,si.received_date,
            r.user_flag,du.f_name,du.l_name,grade,
            (SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
            FROM dmi_users AS u
            INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
            INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
            WHERE u.status = 'active' AND o.id = '$ral_lab_no'
            GROUP BY ral_lab) AS lab_name
            FROM sample_inward as si 
            Inner Join m_sample_allocate as msa ON msa.org_sample_code=si.org_sample_code
            Inner Join m_sample_type as mst ON mst.sample_type_code=si.sample_type_code 
            Inner Join dmi_users as du ON du.id=msa.alloc_to_user_code
            Inner Join dmi_user_roles as r On r.user_email_id=du.email
            Inner Join dmi_ro_offices as mll ON mll.id=si.loc_id 
            Inner Join code_decode as cd on cd.org_sample_code=si.org_sample_code 
            Inner Join m_commodity as mc ON mc.commodity_code=si.commodity_code 
            WHERE cd.display='Y' AND date(si.received_date) BETWEEN '$from_date' and '$to_date' AND si.sample_type_code='$sample_type' AND si.commodity_code='$commodity' AND r.user_flag='$ral_lab_name' AND msa.lab_code='$ral_lab_no'
            GROUP BY du.role,mll.ro_office,du.f_name,du.l_name,cd.sample_code,msa.commencement_date,msa.recby_ch_date,msa.expect_complt,cd.chemist_code,mc.commodity_name,mst.sample_type_desc,si.received_date,r.user_flag,du.f_name,du.l_name,si.grade order by si.received_date";
        }

        $query = $con->execute($str);
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $ro_office = $record['ro_office'];
                $chemist_name = $record['chemist_name'];
                $recby_ch_date = $record['recby_ch_date'];
                $sample_type_desc = $record['sample_type_desc'];
                $commodity_name = $record['commodity_name'];
                $org_sample_code = $record['org_sample_code'];
                $expect_complt = $record['expect_complt'];
                $commencement_date = $record['commencement_date'];
                $grade = $record['grade'];
                $role = $record['role'];
                $lab_name = $record['lab_name'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_io_tested_sample (sr_no, user_id,ro_office, role, chemist_name, recby_ch_date, org_sample_code, commodity_name, sample_type_desc, expect_complt, commencement_date, grade, lab_name, report_date) 
                VALUES (
                '$i','$user_id','$ro_office', '$role','$chemist_name', '$recby_ch_date', '$org_sample_code', '$commodity_name', '$sample_type_desc','$expect_complt','$commencement_date','$grade','$lab_name','$date')");

                $update = $con->execute("UPDATE temp_reportico_io_tested_sample SET counts = (SELECT COUNT(user_id) FROM temp_reportico_io_tested_sample WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getIoTestSubmitByChemist($from_date, $to_date, $chemist_code, $sample_code, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_io_test_submit_by_chemist WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT recby_ch_date,sa.chemist_code,sa.sample_code,c.commodity_name,CONCAT(du.f_name,' ',du.l_name,' (',du.role,') ') AS chemist_name,t.test_name,atd.result,'$chemist_code'AS chemist_code,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name
                FROM m_sample_allocate sa
                INNER JOIN actual_test_data AS atd ON sa.chemist_code=atd.chemist_code
                INNER JOIN code_decode AS cd ON cd.org_sample_code=sa.org_sample_code
                INNER JOIN m_test AS t ON atd.test_code=t.test_code
                INNER JOIN m_commodity AS c ON c.commodity_code=sa.commodity_code
                INNER JOIN dmi_users AS du ON du.id=sa.alloc_to_user_code AND DATE(sa.alloc_date) BETWEEN '$from_date' AND '$to_date' AND sa.chemist_code='$chemist_code'
                WHERE atd.chemist_code='$chemist_code' AND sa.sample_code='$sample_code' AND cd.status_flag in('C','G') AND sa.chemist_code='$chemist_code'
                GROUP BY du.role,sa.chemist_code,sa.recby_ch_date,sa.sample_code,c.commodity_name,du.f_name,du.l_name,t.test_name,atd.result");
        
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $recby_ch_date = $record['recby_ch_date'];
                $commodity_name = $record['commodity_name'];
                $sample_code = $record['sample_code'];
                $chemist_name = $record['chemist_name'];
                $test_name = $record['test_name'];
                $result = $record['result'];
                $chemist_code = $record['chemist_code'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_io_test_submit_by_chemist (sr_no, user_id,lab_name, recby_ch_date, commodity_name, sample_code, chemist_name, test_name, result, chemist_code, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$recby_ch_date', '$commodity_name', '$sample_code', '$chemist_name','$test_name','$result','$chemist_code','$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getIoReTestedSample($from_date, $to_date, $ral_lab_name, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_io_retested_sample WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT msa.sample_code AS org_sample_code,mc.commodity_name,mst.sample_type_desc,si.received_date,ml.ro_office,CONCAT(du.f_name,' ',du.l_name) AS full_name,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name
                FROM sample_inward si
                INNER JOIN m_sample_allocate AS msa ON msa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_ro_offices AS ml ON ml.id=si.loc_id
                INNER JOIN dmi_users AS du ON du.id=si.user_code
                INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code 
                where DATE(si.received_date) BETWEEN '$from_date' AND '$to_date' aND si.display='Y' AND msa.display='Y' AND msa.test_n_r='R' 
                GROUP BY msa.sample_code,mc.commodity_name,mst.sample_type_desc,si.received_date,ml.ro_office,du.f_name,du.l_name");
        
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $org_sample_code = $record['org_sample_code'];
                $commodity_name = $record['commodity_name'];
                $sample_type_desc = $record['sample_type_desc'];
                $received_date = $record['received_date'];
                $ro_office = $record['ro_office'];
                $full_name = $record['full_name'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_io_retested_sample (sr_no, user_id,lab_name, org_sample_code, commodity_name, sample_type_desc, received_date, ro_office, full_name, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$org_sample_code', '$commodity_name', '$sample_type_desc', '$received_date','$ro_office','$full_name','$date')");

                $update = $con->execute("UPDATE temp_reportico_io_retested_sample SET counts = (SELECT COUNT(user_id) FROM temp_reportico_io_retested_sample WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getIoReTestedSampleByChemist($from_date, $to_date, $ral_lab_name, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_io_retested_sample_submit WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT  msa.sample_code AS org_sample_code,mc.commodity_name,mst.sample_type_desc,si.received_date,ml.lab_name AS lab,CONCAT(du.f_name,' ',du.l_name) AS full_name,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name

                FROM sample_inward AS si
                INNER JOIN m_lab AS ml ON ml.lab_code=si.loc_id
                INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code
                INNER JOIN dmi_users AS du ON du.id=si.user_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=du.email
                INNER JOIN m_sample_allocate AS msa ON msa.org_sample_code=si.org_sample_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code 
                WHERE si.display='Y' AND msa.display='Y' AND msa.test_n_r='R' AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date'
                GROUP BY msa.sample_code,mc.commodity_name,mst.sample_type_desc,si.received_date,ml.lab_name,du.f_name,du.l_name");
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $org_sample_code = $record['org_sample_code'];
                $commodity_name = $record['commodity_name'];
                $sample_type_desc = $record['sample_type_desc'];
                $received_date = $record['received_date'];
                $full_name = $record['full_name'];
                $lab = $record['lab'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_io_retested_sample_submit (sr_no, user_id,lab_name, org_sample_code, commodity_name, sample_type_desc, received_date, full_name, lab, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$org_sample_code', '$commodity_name', '$sample_type_desc', '$received_date','$full_name','$lab','$date')");

                $update = $con->execute("UPDATE temp_reportico_io_retested_sample_submit SET counts = (SELECT COUNT(user_id) FROM temp_reportico_io_retested_sample_submit WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getIoSampleAnalyzedByChemist($from_date, $to_date, $posted_ro_office, $user, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_io_sample_analyzed_chemist WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT DISTINCT(sa.sample_code),ml.ro_office,si.stage_sample_code,ml.id AS lab,CONCAT(r.user_flag,', ',ml.ro_office) AS sample_received_from,mc.commodity_name,sc.sam_condition_desc,ct.container_desc,pc.par_condition_desc,
                si.received_date,si.letr_ref_no, CONCAT(u.f_name,' ', u.l_name) AS name_chemist,si.sample_total_qnt,si.lab_code,si.grading_date,si.remark,sa.alloc_date,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name
                FROM sample_inward AS si
                INNER JOIN m_sample_condition AS sc ON sc.sam_condition_code=si.sam_condition_code
                INNER JOIN m_par_condition AS pc ON pc.par_condition_code=si.par_condition_code
                INNER JOIN m_container_type AS ct ON ct.container_code=si.container_code
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                INNER JOIN dmi_ro_offices AS ml ON ml.id=sa.lab_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                WHERE sa.lab_code='$posted_ro_office' AND DATE(sa.alloc_date) BETWEEN '$from_date' AND '$to_date' AND sa.alloc_to_user_code='$user'
                ORDER BY si.received_date ASC");
        // print_r($query);exit;
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $sample_code = $record['sample_code'];
                $ro_office = $record['ro_office'];
                $stage_sample_code = $record['stage_sample_code'];
                $sample_received_from = $record['sample_received_from'];
                $commodity_name = $record['commodity_name'];
                $sam_condition_desc = $record['sam_condition_desc'];
                $container_desc = $record['container_desc'];
                $par_condition_desc = $record['par_condition_desc'];
                $received_date = $record['received_date'];
                $letr_ref_no = $record['letr_ref_no'];
                $name_chemist = $record['name_chemist'];
                $sample_total_qnt = $record['sample_total_qnt'];
                $lab_code = $record['lab_code'];
                $grading_date = $record['grading_date'];
                $remark = $record['remark'];
                $alloc_date = $record['alloc_date'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_io_sample_analyzed_chemist (sr_no, user_id,lab_name, sample_code, ro_office, stage_sample_code, sample_received_from, commodity_name, sam_condition_desc, container_desc,par_condition_desc,received_date, letr_ref_no, name_chemist, sample_total_qnt, lab_code, grading_date, remark, alloc_date, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$sample_code', '$ro_office', '$stage_sample_code', '$sample_received_from','$commodity_name','$sam_condition_desc','$container_desc','$par_condition_desc','$received_date','$letr_ref_no','$name_chemist','$sample_total_qnt','$lab_code','$grading_date','$remark','$alloc_date','$date')");

                $update = $con->execute("UPDATE temp_reportico_io_sample_analyzed_chemist SET counts = (SELECT COUNT(user_id) FROM temp_reportico_io_sample_analyzed_chemist WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }
    public static function getIoCategorywiseReceivedSample($from_date, $to_date, $posted_ro_office, $sample_type, $Category, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_io_categorywise_received_sample WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT c.category_name, COUNT(*),st.sample_type_desc,ml.ro_office,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name
                FROM sample_inward AS si
                INNER JOIN m_commodity_category AS c ON c.category_code=si.category_code
                INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                INNER JOIN code_decode AS cd ON si.org_sample_code=cd.org_sample_code
                INNER JOIN dmi_ro_offices AS ml ON ml.id=cd.lab_code AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date' AND cd.lab_code='$ral_lab_no' AND si.category_code='$Category' AND si.sample_type_code='$sample_type'
                GROUP BY category_name,ml.ro_office,st.sample_type_desc
                ORDER BY c.category_name ASC");
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $count = $record['count'];
                $ro_office = $record['ro_office'];
                $sample_type_desc = $record['sample_type_desc'];
                $category_name = $record['category_name'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_io_categorywise_received_sample (sr_no, user_id,lab_name, count, ro_office, sample_type_desc, category_name, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$count', '$ro_office', '$sample_type_desc','$category_name','$date')");

                $update = $con->execute("UPDATE temp_reportico_io_categorywise_received_sample SET counts = (SELECT COUNT(user_id) FROM temp_reportico_io_categorywise_received_sample WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getIoTimeTakenAnalysisSample($from_date, $to_date, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_io_timetaken_analysis WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT si.stage_sample_code,mc.commodity_name,si.received_date,si.dispatch_date,si.dispatch_date:: DATE -si.received_date:: DATE AS time_taken, 
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users as u 
                INNER JOIN dmi_ro_offices as o On u.posted_ro_office=o.id
                Inner Join dmi_user_roles as r on r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name

                FROM sample_inward AS si
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN dmi_users AS du ON du.id=si.user_code
                INNER JOIN user_role AS ur ON ur.role_name=du.role AND ur.role_name IN('RO Officer','SO Officer') AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date'
                GROUP BY si.stage_sample_code,mc.commodity_name,si.received_date,si.dispatch_date
                ORDER BY si.received_date ASC");

        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $stage_sample_code = $record['stage_sample_code'];
                $commodity_name = $record['commodity_name'];
                $received_date = $record['received_date'];
                $dispatch_date = $record['dispatch_date'];
                $time_taken = $record['time_taken'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_io_timetaken_analysis (sr_no, user_id,lab_name, stage_sample_code, commodity_name, received_date, dispatch_date, time_taken, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$stage_sample_code', '$commodity_name', '$received_date','$dispatch_date','$time_taken','$date')");

                $update = $con->execute("UPDATE temp_reportico_io_timetaken_analysis SET counts = (SELECT COUNT(user_id) FROM temp_reportico_io_timetaken_analysis WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getIoCommodityWisePrivateAnalysis($lab, $posted_ro_office, $commodity, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_io_commoditywise_private_analysis WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT si.fin_year,mc.commodity_name, COUNT(si.sample_type_code) AS sample_count,ml.ro_office, '$lab' AS lab_name
                FROM code_decode AS cd
                INNER JOIN sample_inward AS si ON cd.org_sample_code=si.org_sample_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN dmi_ro_offices AS ml ON si.loc_id=ml.id
                INNER JOIN dmi_users AS u ON u.id=si.user_code AND si.display='Y' AND si.sample_type_code='2' AND si.commodity_code='$commodity' AND cd.lab_code='$ral_lab_no'
                GROUP BY si.fin_year,mc.commodity_name,ml.ro_office
                ORDER BY commodity_name ASC");
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab = $record['lab_name'];
                $ro_office = $record['ro_office'];
                $lab_name = $lab . ', ' . $ro_office;
                $fin_year = $record['fin_year'];
                $commodity_name = $record['commodity_name'];
                $sample_count = $record['sample_count'];
                $ro_office = $record['ro_office'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_io_commoditywise_private_analysis (sr_no, user_id,lab_name, fin_year, commodity_name, sample_count, ro_office, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$fin_year', '$commodity_name', '$sample_count','$ro_office','$date')");

                $update = $con->execute("UPDATE temp_reportico_io_commoditywise_private_analysis SET counts = (SELECT COUNT(user_id) FROM temp_reportico_io_commoditywise_private_analysis WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getIoConsolidateSatementBroughtFwdCarriedFwdSample($month, $user, $posted_ro_office, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_io_consoli_sample WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT CONCAT(u.f_name,' ',u.l_name) AS chemist_name,ur.role_name,r.user_flag,mlc.ro_office,'$month' AS month,
                (
                SELECT count(distinct(sample_code)) as bf_count 
                FROM code_decode
                WHERE lab_code='$ral_lab_no' and status_flag NOT IN('G','F' ) and alloc_to_user_code='$user'
                ) as bf_count,
                (
                SELECT count(distinct(sample_code)) as received_count 
                FROM m_sample_allocate 
                WHERE lab_code='$ral_lab_no' and Extract(month from alloc_date)::INTEGER = '$month' and alloc_to_user_code='$user'
                ) AS received_count,
                (
                SELECT count(distinct(cd.sample_code)) as analyzed_count_one from code_decode as cd
                Inner Join m_sample_allocate as sa ON sa.sample_code=cd.sample_code 			
                where test_n_r!='R' and cd.lab_code='$ral_lab_no' and Extract(month from sa.alloc_date)::INTEGER = '$month' and cd.alloc_to_user_code='$user'
                ) AS analyzed_count_one,                            
                (
                SELECT count(distinct(cd.sample_code)) as analyzed_count_two from code_decode as cd
                Inner Join m_sample_allocate as sa ON sa.sample_code=cd.sample_code 			
                where test_n_r='R' and cd.lab_code='$ral_lab_no' and Extract(month from alloc_date)::INTEGER = '$month' and cd.alloc_to_user_code='$user'
                ) AS analyzed_count_two,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name
                
                FROM dmi_users as u
                Inner Join dmi_ro_offices as mlc ON mlc.id=u.posted_ro_office 							
                Inner Join user_role as ur ON ur.role_name=u.role 
                inner join dmi_user_roles as r on u.email=r.user_email_id
                WHERE mlc.id='$ral_lab_no' and u.id='$user' And u.role In ('Jr Chemist','Sr Chemist','Cheif Chemist') and u.status='active'");
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $bf_count = $record['bf_count'];
                $received_count = $record['received_count'];
                $total = $bf_count + $received_count;
                $analyzed_count_one = $record['analyzed_count_one'];
                $analyzed_count_two = $record['analyzed_count_two'];
                $carried_for = $total - $analyzed_count_one;
                $chemist_name = $record['chemist_name'];
                $role_name = $record['role_name'];
                $month = date("F", mktime(0, 0, 0, $record['month'], 10));
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_io_consoli_sample (sr_no, user_id,lab_name, chemist_name, bf_count, received_count, total, analyzed_count_one, analyzed_count_two, carried_for, role_name, month, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$chemist_name', '$bf_count', '$received_count', '$total','$analyzed_count_one','$analyzed_count_two','$carried_for','$role_name','$month','$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function  getIoNoCheckPrivateResearchSample($from_date, $to_date, $posted_ro_office, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $begin = new DateTime($from_date);
        $end = new DateTime($to_date);
        $k = 0;
        while ($begin <= $end) {
            $yr_data[$k]['month'] = $begin->format('m');
            $yr_data[$k]['year'] = $begin->format('Y');
            $month1[$k] = $begin->format('M') . ',' . $begin->format('Y');

            $k++;
            $begin->modify('first day of next month');
        } //

        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_io_chk_pvt_research WHERE user_id = '$user_id'");

        foreach ($yr_data as $da) {
            $month = $da['month'];
            $year = $da['year'];

            $query = $con->execute("SELECT dmi_ro_offices.id AS lab_code,
                    (
                    SELECT COUNT(DISTINCT(cd.org_sample_code)) AS check_analyzed_count
                    FROM code_decode cd
                    INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                    INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    WHERE cd.display='Y' AND st.sample_type_desc='Check' AND cd.status_flag In('C','G') AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND cd.lab_code='$ral_lab_no' AND DATE(sa.alloc_date) BETWEEN '$from_date' AND '$to_date'
                    ) AS check_analyzed_count,
                    (
                    SELECT COUNT(DISTINCT(cd.org_sample_code)) AS res_analyzed_count
                    FROM code_decode cd
                    INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                    INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    WHERE cd.display='Y' AND st.sample_type_desc='Research' AND cd.status_flag In('C','G') AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND cd.lab_code='$ral_lab_no' AND DATE(sa.alloc_date) BETWEEN '$from_date' AND '$to_date'
                    ) AS res_analyzed_count,
                    (
                    SELECT COUNT(DISTINCT(cd.org_sample_code)) AS chk_analyzed_count
                    FROM code_decode cd
                    INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                    INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    WHERE cd.display='Y' AND st.sample_type_desc='Challenged' AND cd.status_flag In('C','G') AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND cd.lab_code='$ral_lab_no' AND DATE(sa.alloc_date) BETWEEN '$from_date' AND '$to_date'
                    ) AS chk_analyzed_count,
                    (
                    SELECT COUNT(DISTINCT(cd.org_sample_code)) AS othr_analyzed_count
                    FROM code_decode cd
                    INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                    INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    WHERE cd.display='Y' AND st.sample_type_desc NOT IN('Check','Research','Challenged') AND cd.status_flag In('C','G') AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND cd.lab_code='$ral_lab_no' AND DATE(sa.alloc_date) BETWEEN '$from_date' AND '$to_date'
                    ) AS othr_analyzed_count,
                    (
                    SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                    FROM dmi_users AS u
                    INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                    INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                    WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                    GROUP BY ral_lab
                    ) AS lab_name
                    
                    FROM dmi_users
                    INNER JOIN dmi_ro_offices ON dmi_users.posted_ro_office=dmi_ro_offices.id
                    INNER JOIN dmi_user_roles ON dmi_user_roles.user_email_id=dmi_users.email AND dmi_user_roles.user_flag In('RAL','CAL') AND dmi_users.posted_ro_office='$ral_lab_no'
                    WHERE dmi_users.status = 'active'
                    GROUP BY dmi_user_roles.user_flag,dmi_ro_offices.id,dmi_ro_offices.ro_office
                    ORDER BY dmi_ro_offices.ro_office ASC");

            $records = $query->fetchAll('assoc');
            // print_r($records);

            $query->closeCursor();
            if (!empty($records)) {
                foreach ($records as $record) {
                    $lab_name = $record['lab_name'];
                    $check_analyzed_count = $record['check_analyzed_count'];
                    $res_analyzed_count = $record['res_analyzed_count'];
                    $chk_analyzed_count = $record['chk_analyzed_count'];
                    $othr_analyzed_count = $record['othr_analyzed_count'];
                    $date = date("d/m/Y");
                    $i = $i + 1;

                    $insert = $con->execute("INSERT INTO temp_reportico_io_chk_pvt_research (sr_no, user_id, lab_name, check_analyzed_count, res_analyzed_count, chk_analyzed_count, othr_analyzed_count,  report_date) 
                    VALUES (
                    '$i','$user_id','$lab_name', '$check_analyzed_count', '$res_analyzed_count', '$chk_analyzed_count', '$othr_analyzed_count','$date')");
                }
                return 1;
            } else {
                return 0;
            }
        }
    }

    public static function getIoSampleAlloteAnalyzePending($from_date, $to_date, $posted_ro_office, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_io_sample_allot_analyz_pend WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT dmi_ro_offices.id AS lab_code,
                (
                SELECT COUNT(*) AS allotment_count
                FROM code_decode AS cd
                INNER JOIN m_sample_allocate AS msa ON cd.org_sample_code=msa.org_sample_code
                WHERE cd.status_flag in ('N','C','G') AND DATE(msa.alloc_date) BETWEEN '$from_date' AND '$to_date' AND cd.lab_code='$ral_lab_no'
                ) AS allotment_count,
                (
                SELECT COUNT(*) AS analyzed_count
                FROM code_decode AS cd
                INNER JOIN m_sample_allocate AS msa ON cd.org_sample_code=msa.org_sample_code
                WHERE cd.status_flag in ('C','G') AND DATE(msa.alloc_date) BETWEEN '$from_date' AND '$to_date' AND cd.lab_code='$ral_lab_no'
                ) AS analyzed_count, 
                (
                SELECT COUNT(*) AS pending_count
                FROM code_decode AS cd
                INNER JOIN m_sample_allocate AS msa ON cd.org_sample_code=msa.org_sample_code
                WHERE cd.status_flag='N' AND DATE(msa.alloc_date) BETWEEN '$from_date' AND '$to_date' AND cd.lab_code='$ral_lab_no'
                ) AS pending_count,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name
                
                FROM dmi_users
                INNER JOIN dmi_user_roles AS r ON dmi_users.email=r.user_email_id
                INNER JOIN dmi_ro_offices ON dmi_users.posted_ro_office=dmi_ro_offices.id AND r.user_flag In ('RAL','CAL') AND dmi_users.posted_ro_office='$ral_lab_no'
                WHERE dmi_users.status = 'active'
                GROUP BY r.user_flag,dmi_ro_offices.id,dmi_ro_offices.ro_office
                ORDER BY dmi_ro_offices.ro_office ASC");
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $allotment_count = $record['allotment_count'];
                $analyzed_count = $record['analyzed_count'];
                $pending_count = $record['pending_count'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_io_sample_allot_analyz_pend (sr_no, user_id, lab_name, allotment_count, analyzed_count, pending_count, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$allotment_count', '$analyzed_count', '$pending_count', '$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getIoPerformanceRalCal($from_date, $to_date, $posted_ro_office, $lab, $sample_type, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $month = date("m", strtotime($from_date));

        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_io_performance_ral_cal WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT drf.id AS lab_code, 
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS progress_sample
                FROM code_decode cd
                INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                WHERE cd.display='Y' AND si.sample_type_code='$sample_type' AND cd.lab_code='$ral_lab_no' AND cd.status_flag In('C','G') AND date(sa.alloc_date) BETWEEN '$from_date' AND '$to_date'
                ) AS progress_sample,
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS tot_sample_month
                FROM code_decode cd
                INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                WHERE cd.display='Y' AND si.sample_type_code='$sample_type' AND cd.lab_code='$ral_lab_no' AND cd.status_flag In('C','G') AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = $month
                ) AS tot_sample_month,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name
                
                FROM dmi_users u
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                INNER JOIN dmi_ro_offices drf ON u.posted_ro_office=drf.id
                WHERE u.status = 'active' AND r.user_flag='$ral_lab_name' AND drf.id='$ral_lab_no'
                GROUP BY r.user_flag,drf.id,drf.ro_office
                ORDER BY drf.ro_office ASC
                ");
       
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $progress_sample = $record['progress_sample'];
                $tot_sample_month = $record['tot_sample_month'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_io_performance_ral_cal (sr_no, user_id, lab_name, progress_sample, tot_sample_month, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$progress_sample', '$tot_sample_month', '$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function  getIoChemistWiseSampleAnalysis($month, $posted_ro_office, $user, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_io_chemist_wise_samp_analysis WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT CONCAT(u.f_name,' ',u.l_name,'(',u.role,')') AS chemist_name, u.id, 
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS check_analyzed_count
                FROM code_decode cd
                INNER JOIN sample_inward AS si ON si.stage_sample_code=cd.sample_code
                INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.sample_code=si.stage_sample_code
                WHERE cd.display='Y' AND st.sample_type_desc='Check' AND cd.status_flag In('C','G') AND cd.alloc_to_user_code='$user' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month'
                ) AS check_analyzed_count, 
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS res_analyzed_count
                FROM code_decode cd
                INNER JOIN sample_inward AS si ON si.stage_sample_code=cd.sample_code
                INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.sample_code=si.stage_sample_code
                WHERE cd.display='Y' AND st.sample_type_desc='Research' AND cd.status_flag In('C','G') AND cd.alloc_to_user_code='$user' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month'
                ) AS res_analyzed_count, 
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS res_challenged_count
                FROM code_decode cd
                INNER JOIN sample_inward AS si ON si.stage_sample_code=cd.sample_code
                INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.sample_code=si.stage_sample_code
                WHERE cd.display='Y' AND st.sample_type_desc='Challenged' AND cd.status_flag In('C','G') AND cd.alloc_to_user_code='$user' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month'
                ) AS res_challenged_count, 
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS othr_analyzed_count
                FROM code_decode cd
                INNER JOIN sample_inward AS si ON si.stage_sample_code=cd.sample_code
                INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.sample_code=si.stage_sample_code
                WHERE cd.display='Y' AND st.sample_type_desc NOT IN('Check','Research','Challenged','Apex(Check)') AND cd.status_flag In('C','G') AND cd.alloc_to_user_code='$user' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month'
                ) AS othr_analyzed_count,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name
                
                FROM dmi_users u
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                INNER JOIN dmi_ro_offices drf ON u.posted_ro_office=drf.id
                WHERE u.posted_ro_office= '$ral_lab_no' AND u.role IN('Jr Chemist','Sr Chemist','Cheif Chemist') AND u.STATUS = 'active' AND u.id = '$user'
                ");
       
        $records = $query->fetchAll('assoc');
        

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $chemist_name = $record['chemist_name'];
                $check_analyzed_count = $record['check_analyzed_count'];
                $res_analyzed_count = $record['res_analyzed_count'];
                $res_challenged_count = $record['res_challenged_count'];
                $othr_analyzed_count = $record['othr_analyzed_count'];
                $date = date("d/m/Y");
                $month = date("F", mktime(0, 0, 0, $month, 10));
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_io_chemist_wise_samp_analysis (sr_no,user_id, lab_name, chemist_name, check_analyzed_count, res_analyzed_count, res_challenged_count, othr_analyzed_count, month, report_date) 
                VALUES (
               '$i','$user_id', '$lab_name', '$chemist_name', '$check_analyzed_count','$res_analyzed_count','$res_challenged_count','$othr_analyzed_count', '$month','$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getIoCommodityConsolidatedReport($month, $posted_ro_office, $sample_type, $commodity, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_io_commodity_consolidated WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT commodity_name,
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS bf_count
                FROM code_decode AS cd
                INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                WHERE cd.lab_code='$ral_lab_no' AND cd.status_flag='G' AND EXTRACT(MONTH
                FROM si.received_date):: INTEGER = '$month' AND si.sample_type_code='$sample_type' AND si.commodity_code='$commodity'
                ) AS bf_count,
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS analyzed_count
                FROM code_decode AS cd
                INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                WHERE cd.lab_code='$ral_lab_no' AND cd.status_flag='G' AND EXTRACT(MONTH
                FROM si.received_date):: INTEGER = '$month' AND si.sample_type_code='$sample_type' AND si.commodity_code='$commodity'
                ) AS analyzed_count,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users as u 
                INNER JOIN dmi_ro_offices as o On u.posted_ro_office=o.id
                Inner Join dmi_user_roles as r on r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name

                FROM m_commodity
                WHERE commodity_code='$commodity'
                ");
        $records = $query->fetchAll('assoc');
        

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $commodity_name = $record['commodity_name'];
                $bf_count = $record['bf_count'];
                $analyzed_count = $record['analyzed_count'];
                $carried_for = $bf_count - $analyzed_count;
                $date = date("d/m/Y");
                $month = date("F", mktime(0, 0, 0, $month, 10));
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_io_commodity_consolidated (sr_no, user_id, lab_name, commodity_name, bf_count, analyzed_count, carried_for, month, report_date) 
                VALUES (
               '$i','$user_id', '$lab_name', '$commodity_name', '$bf_count','$analyzed_count', '$carried_for', '$month','$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getIoCommodityCheckChallengedSample($from_date, $to_date, $commodity, $ral_lab_no, $ral_lab_name, $posted_ro_office)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_io_commodity_check_challenged WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT commodity_name,
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS bf_count
                FROM code_decode AS cd
                INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                WHERE cd.lab_code='$ral_lab_no' AND cd.status_flag NOT IN('G') AND si.sample_type_code In ('1','4') AND DATE(si.received_date) < '$from_date' AND si.commodity_code='$commodity'
                ) AS bf_count, 
                (
                SELECT COUNT(DISTINCT(sa.sample_code)) AS received_count
                FROM m_sample_allocate AS sa
                INNER JOIN sample_inward AS si ON si.org_sample_code=sa.org_sample_code
                WHERE sa.lab_code='$ral_lab_no' AND si.sample_type_code In ('1','4') AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date' AND si.commodity_code='$commodity'
                ) AS received_count,
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS pass_count
                FROM code_decode AS cd
                INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                WHERE cd.lab_code='$ral_lab_no' AND si.remark='Pass' AND si.sample_type_code In ('1','4') AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date' AND si.commodity_code='$commodity'
                ) AS pass_count, 
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS fail_count
                FROM code_decode AS cd
                INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                WHERE cd.lab_code='$ral_lab_no' AND si.remark='Fail' AND si.sample_type_code In ('1','4') AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date' AND si.commodity_code=$commodity
                ) AS fail_count,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users as u 
                INNER JOIN dmi_ro_offices as o On u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles as r on r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab) AS lab_name
                from m_commodity where commodity_code='$commodity'
                ");
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $commodity_name = $record['commodity_name'];
                $bf_count = $record['bf_count'];
                $received_count = $record['received_count'];
                $total = $bf_count + $received_count;
                $pass_count = $record['pass_count'];
                $fail_count = $record['fail_count'];
                $total_analysis = $pass_count + $fail_count;
                $cf_total = $total - $total_analysis;
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_io_commodity_check_challenged (sr_no, user_id, lab_name, commodity_name, bf_count, received_count, total, pass_count, fail_count, total_analysis, cf_total, report_date) 
                VALUES (
               '$i','$user_id', '$lab_name', '$commodity_name', '$bf_count','$received_count', '$total', '$pass_count', '$fail_count', '$total_analysis', '$cf_total','$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getIoBroughtForwardAnalysedCarrSample($month, $ral_lab, $posted_ro_office, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_io_brg_fwd_ana_carr_fwd_sam WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT CONCAT(u.f_name,' ',u.l_name,' (',ur.role_name,') ') AS chemist_name,'$month' AS month,
                (
                SELECT COUNT(DISTINCT(sample_code)) AS bf_count
                FROM code_decode
                WHERE lab_code='$ral_lab_no' AND status_flag NOT IN('G','F')
                ) AS bf_count,
                (
                SELECT COUNT(DISTINCT(sample_code)) AS received_count
                FROM m_sample_allocate
                WHERE lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM alloc_date):: INTEGER = '$month'
                ) AS received_count,
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS analyzed_count_in_month
                FROM code_decode AS cd
                INNER JOIN m_sample_allocate AS sa ON sa.sample_code=cd.sample_code
                WHERE test_n_r!='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month'
                ) AS analyzed_count_in_month,                            
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS analyzed_count_in_month_repeat
                FROM code_decode AS cd
                INNER JOIN m_sample_allocate AS sa ON sa.sample_code=cd.sample_code
                WHERE test_n_r='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM alloc_date):: INTEGER = '$month'
                ) AS analyzed_count_in_month_repeat,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name
                
                from dmi_users as u
                INNER JOIN dmi_ro_offices AS mlc ON mlc.id=u.posted_ro_office
                INNER JOIN user_role AS ur ON ur.role_name=u.role
                INNER JOIN dmi_user_roles AS r ON u.email=r.user_email_id
                WHERE mlc.id='$ral_lab_no' AND u.role In ('Jr Chemist','Sr Chemist','Cheif Chemist') AND u.status = 'active'
                ");
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $chemist_name = $record['chemist_name'];
                $bf_count = $record['bf_count'];
                $received_count = $record['received_count'];
                $total = $bf_count + $received_count;
                $analyzed_count_in_month = $record['analyzed_count_in_month'];
                $analyzed_count_in_month_repeat = $record['analyzed_count_in_month_repeat'];
                $carried_for = $total - $analyzed_count_in_month;
                $monthNo = $record['month'];
                $i = $i + 1;
                $month = date("F", mktime(0, 0, 0, $monthNo, 10));
                $date = date("d/m/Y");

                $insert = $con->execute("INSERT INTO temp_reportico_io_brg_fwd_ana_carr_fwd_sam (sr_no, user_id, lab_name, chemist_name, bf_count, received_count, total, analyzed_count_in_month, analyzed_count_in_month_repeat, carried_for, month, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$chemist_name', '$bf_count','$received_count', '$total', '$analyzed_count_in_month', '$analyzed_count_in_month_repeat', '$carried_for','$month','$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }
    /********************************************************************************************************************
     * Role SO OFFICER
     ********************************************************************************************************************/
    public static function getSoSampleRegister($from_date, $to_date, $posted_ro_office, $lab, $commodity, $category, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_so_sample_register WHERE user_id = '$user_id'");

        $str = "SELECT si.letr_date,mct.category_name,CONCAT(r.user_flag,',',mll.ro_office) AS labname, si.letr_ref_no, si.org_sample_code,si.stage_sample_code,si.sample_total_qnt AS sample_qnt,mu.unit_weight,mp.par_condition_desc,si.sample_total_qnt,si.received_date, mc.commodity_name,si.commodity_code, si.category_code,st.sample_type_desc,si.loc_id, si.dispatch_date,si.dispatch_date, si.grading_date,si.remark,r.user_flag,sample_total_qnt
                FROM sample_inward AS si
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN m_commodity_category AS mct ON mct.category_code=si.category_code
                INNER JOIN m_par_condition AS mp ON mp.par_condition_code=si.par_condition_code
                INNER JOIN m_unit_weight AS mu ON mu.unit_id=si.parcel_size
                INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN dmi_users AS du ON du.id=si.user_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=du.email";

        $str .= " WHERE si.display='Y' AND si.acc_rej_flg='A' AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date'";

        if ($_SESSION['user_flag'] == "RO" || $_SESSION['user_flag'] == "SO") {
            $str .= " AND si.loc_id='" . $_SESSION['posted_ro_office'] . "'";
        }
        if ($_SESSION['user_flag'] == "RO" || $_SESSION['user_flag'] == "SO") {
            $str .= " AND r.user_flag='" . $_SESSION['user_flag'] . "'";
        }

        $str .= "AND si.loc_id='$ral_lab_no'  AND r.user_flag='$ral_lab_name' AND si.commodity_code='$commodity' AND si.category_code='$category' GROUP BY si.stage_sample_code,si.letr_date,sample_qnt,si.received_date,si.dispatch_date,si.grading_date,mll.ro_office,r.user_flag, mc.commodity_name,mct.category_name,si.commodity_code, si.category_code,st.sample_type_desc,si.loc_id,si.letr_ref_no,si.sample_total_qnt,mu.unit_weight,mp.par_condition_desc,si.dispatch_date, si.org_sample_code,si.remark
                ORDER BY received_date ASC";

        $query = $con->execute($str);

        $records = $query->fetchAll('assoc');
        // print_r($records);exit;
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $letr_date = $record['letr_date'];
                $category_name = $record['category_name'];
                $labname = $record['labname'];
                $letr_ref_no = $record['letr_ref_no'];
                $org_sample_code = $record['org_sample_code'];
                $stage_sample_code = $record['stage_sample_code'];
                $sample_qnt = $record['sample_qnt'];
                $unit_weight = $record['unit_weight'];
                $par_condition_desc = $record['par_condition_desc'];
                $sample_total_qnt = $record['sample_total_qnt'];
                $received_date = $record['received_date'];
                $commodity_name = $record['commodity_name'];
                $commodity_code = $record['commodity_code'];
                $category_code = $record['category_code'];
                $sample_type_desc = $record['sample_type_desc'];
                $loc_id = $record['loc_id'];
                $dispatch_date = $record['dispatch_date'];
                $grading_date = $record['grading_date'];
                $remark = $record['remark'];
                $user_flag = $record['user_flag'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_so_sample_register (sr_no, user_id, letr_date, category_name, labname, letr_ref_no, org_sample_code, stage_sample_code, sample_qnt, unit_weight, par_condition_desc, sample_total_qnt, received_date, commodity_name, commodity_code, category_code, sample_type_desc, loc_id, dispatch_date, grading_date, remark, user_flag, report_date) 
                VALUES (
                '$i','$user_id','$letr_date', '$category_name', '$labname', '$letr_ref_no', '$org_sample_code', '$stage_sample_code', '$sample_qnt', '$unit_weight', '$par_condition_desc', '$sample_total_qnt', '$received_date', '$commodity_name', '$commodity_code', '$category_code', '$sample_type_desc', '$loc_id', '$dispatch_date', '$grading_date', '$remark', '$user_flag', '$date')");

                $update = $con->execute("UPDATE temp_reportico_so_sample_register SET counts = (SELECT COUNT(sample_type_desc) FROM temp_reportico_so_sample_register WHERE sample_type_desc = '$sample_type_desc') WHERE sample_type_desc = '$sample_type_desc'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getSoSampleRegistration($from_date, $to_date, $posted_ro_office, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_so_sample_registration WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT mc.commodity_name,si.grade,CONCAT(sd.pckr_nm,', ',pckr_addr) AS name_address_packer,sd.lot_no,si.received_date AS pack_date,sd.pack_size,sd.tbl,CONCAT(sd.shop_name,', ',sd.shop_address) AS shop_name_address,si.parcel_size,sd.smpl_drwl_dt,CONCAT(u.f_name,' ',u.l_name) AS name_officer,si.org_sample_code,si.dispatch_date,CONCAT(r.user_flag,', ',mll.ro_office) AS lab_or_office_name
                FROM sample_inward AS si
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN dmi_users AS u ON u.id=si.user_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN sample_inward_details AS sd ON sd.org_sample_code=si.stage_sample_code AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date' AND si.loc_id='$ral_lab_no' AND r.user_flag='$ral_lab_name'
                GROUP BY mll.ro_office,r.user_flag,si.grading_date,si.remark,si.grade,u.f_name,u.l_name,mc.commodity_name,si.grade,sd.pckr_nm,pckr_addr,si.received_date,sd.lot_no,sd.pack_size,sd.tbl,sd.shop_name,sd.shop_address,si.parcel_size,sd.smpl_drwl_dt, si.org_sample_code,si.dispatch_date,si.ral_lab_code,si.ral_anltc_rslt_rcpt_dt,si.anltc_rslt_chlng_flg,si.misgrd_param_value,si.misgrd_report_issue_dt, si.misgrd_reason,si.chlng_smpl_disptch_cal_dt,si.cal_anltc_rslt_rcpt_dt
                ORDER BY sd.smpl_drwl_dt DESC");
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $commodity_name = $record['commodity_name'];
                $grade = $record['grade'];
                $name_address_packer = $record['name_address_packer'];
                $lot_no = $record['lot_no'];
                $pack_date = $record['pack_date'];
                $pack_size = $record['pack_size'];
                $tbl = $record['tbl'];
                $shop_name_address = $record['shop_name_address'];
                $parcel_size = $record['parcel_size'];
                $smpl_drwl_dt = $record['smpl_drwl_dt'];
                $name_officer = $record['name_officer'];
                $org_sample_code = $record['org_sample_code'];
                $dispatch_date = $record['dispatch_date'];
                $lab_or_office_name = $record['lab_or_office_name'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_so_sample_registration (sr_no, user_id, commodity_name, grade, name_address_packer, lot_no, pack_date, pack_size, tbl, shop_name_address, parcel_size, smpl_drwl_dt, name_officer, org_sample_code, dispatch_date, lab_or_office_name, report_date) 
                VALUES (
                '$i','$user_id','$commodity_name', '$grade', '$name_address_packer', '$lot_no', '$pack_date', '$pack_size', '$tbl', '$shop_name_address', '$parcel_size', '$smpl_drwl_dt', '$name_officer', '$org_sample_code', '$dispatch_date', '$lab_or_office_name', '$date')");

                $update = $con->execute("UPDATE temp_reportico_so_sample_registration SET counts = (SELECT COUNT(user_id) FROM temp_reportico_so_sample_registration WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    /********************************************************************************************************************
     * Role RAL/CAL OIC
     ********************************************************************************************************************/

    public static function getRalCalOicSampleAnalyzedCount($from_date, $to_date, $posted_ro_office, $sample_type, $lab, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ral_sample_analyzed WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT mll.ro_office,mst.sample_type_desc,mc.commodity_name, COUNT(mc.commodity_name) AS count_samples, COUNT(CASE WHEN si.status_flag = 'FG' THEN 1 END) AS finalized
                FROM sample_inward AS si
                INNER JOIN m_commodity_category AS mcc ON si.category_code=mcc.category_code
                INNER JOIN m_commodity AS mc ON si.commodity_code=mc.commodity_code
                INNER JOIN m_sample_type AS mst ON si.sample_type_code=mst.sample_type_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN dmi_users AS u ON u.id=si.user_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND si.display='Y' AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date' AND mst.sample_type_code='$sample_type' AND si.loc_id='$ral_lab_no' AND r.user_flag='$ral_lab_name'
                GROUP BY mll.ro_office,mst.sample_type_desc,mc.commodity_name
                ORDER BY mc.commodity_name ASC");
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $ro_office = $record['ro_office'];
                $sample_type_desc = $record['sample_type_desc'];
                $commodity_name = $record['commodity_name'];
                $count_samples = $record['count_samples'];
                $finalized = $record['finalized'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ral_sample_analyzed (sr_no, user_id, ro_office, sample_type_desc, commodity_name, count_samples, finalized, report_date) 
                VALUES (
                '$i','$user_id','$ro_office', '$sample_type_desc', '$commodity_name', '$count_samples', '$finalized','$date')");

                $update = $con->execute("UPDATE temp_reportico_ral_sample_analyzed SET counts = (SELECT COUNT(user_id) FROM temp_reportico_ral_sample_analyzed WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getRalCalOicRejectSample($from_date, $to_date, $posted_ro_office, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ral_cal_oic_reject_sample WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT si.received_date,si.reject_date,CONCAT(du.f_name,' ',du.l_name) AS fullname,CONCAT(r.user_flag,',',mll.ro_office) AS labname,si.org_sample_code,mc.commodity_name,mst.sample_type_desc
                FROM sample_inward as si 
                JOIN (SELECT org_sample_code,max(id) AS id FROM workflow WHERE dst_loc_id='$ral_lab_no' GROUP BY(org_sample_code)) wf ON ((si.org_sample_code=wf.org_sample_code))
                JOIN workflow wff ON ((wf.id=wff.id))						
                Inner Join dmi_ro_offices AS mll ON mll.id=si.loc_id 
                Inner Join m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code 
                Inner Join dmi_users AS du ON du.id=si.user_code 
                Inner Join dmi_user_roles AS r On r.user_email_id=du.email
                Inner Join m_commodity AS mc ON mc.commodity_code=si.commodity_code And si.display='Y' AND si.acc_rej_flg='R'
                WHERE date(si.received_date) BETWEEN '$from_date' AND '$to_date'");
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $received_date = $record['received_date'];
                $reject_date = $record['reject_date'];
                $fullname = $record['fullname'];
                $labname = $record['labname'];
                $org_sample_code = $record['org_sample_code'];
                $commodity_name = $record['commodity_name'];
                $sample_type_desc = $record['sample_type_desc'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ral_cal_oic_reject_sample (sr_no, user_id,received_date, reject_date, fullname, labname, org_sample_code, commodity_name, sample_type_desc, report_date) 
                VALUES (
               '$i','$user_id', '$received_date', '$reject_date', '$fullname', '$labname', '$org_sample_code', '$commodity_name', '$sample_type_desc', '$date')");

                $update = $con->execute("UPDATE temp_reportico_ral_cal_oic_reject_sample SET counts = (SELECT COUNT(user_id) FROM temp_reportico_ral_cal_oic_reject_sample WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getRalCalOicSampleReceivedRoSoRalCal($from_date, $to_date, $commodity, $posted_ro_office, $lab, $role, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ral_cal_oic_sample_received_rosoralcal WHERE user_id = '$user_id'");

        $sql = "SELECT si.received_date,si.org_sample_code,mct.category_name,mc.commodity_name,mst.sample_type_desc,du.role,mll.ro_office,r.user_flag,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name
		        FROM sample_inward as si 
				INNER JOIN m_commodity_category AS mct ON mct.category_code=si.category_code
				INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id 
				INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code 
				INNER JOIN dmi_users AS du On du.id=si.user_code
				INNER JOIN dmi_user_roles AS r On r.user_email_id=du.email
				INNER JOIN workflow AS w On w.org_sample_code=si.org_sample_code
				INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code And si.display='Y'";

        if ($from_date != '' && $to_date != '') {
            $sql .= " WHERE date(si.received_date) BETWEEN '$from_date' and '$to_date'";
        }
        if ($commodity != '') {
            $sql .= " AND si.commodity_code='$commodity'";
        }
        if ($lab == "RO" || $lab == "SO") {
            $sql .= " AND si.loc_id='" . $posted_ro_office . "'";
        } else if ($lab == "RAL" && $role == "Inward Officer") {
            $sql .= " AND w.dst_loc_id='" . $posted_ro_office . "'";
        } else if ($lab == "CAL" && $role == "Inward Officer") {
            $sql .= " AND w.dst_loc_id='" . $posted_ro_office . "'";
        }
        $sql .= " Group By du.role,si.org_sample_code,mct.category_name,mc.commodity_name,mst.sample_type_desc,si.received_date,mll.ro_office,r.user_flag,du.f_name,du.l_name";
        $sql .= " ORDER BY si.received_date asc";

        $query = $con->execute($sql);
        $records = $query->fetchAll('assoc');
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $received_date = $record['received_date'];
                $org_sample_code = $record['org_sample_code'];
                $category_name = $record['category_name'];
                $commodity_name = $record['commodity_name'];
                $sample_type_desc = $record['sample_type_desc'];
                $role = $record['role'];
                $ro_office = $record['ro_office'];
                $user_flag = $record['user_flag'];
                $lab_name = $record['lab_name'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ral_cal_oic_sample_received_rosoralcal (sr_no, user_id,received_date, org_sample_code, category_name, commodity_name, sample_type_desc, role, ro_office, user_flag, lab_name, report_date) 
                VALUES (
                '$i','$user_id','$received_date', '$org_sample_code', '$category_name', '$commodity_name', '$sample_type_desc', '$role', '$ro_office', '$user_flag', '$lab_name','$date')");

                $update = $con->execute("UPDATE temp_reportico_ral_cal_oic_sample_received_rosoralcal SET counts = (SELECT COUNT(user_id) FROM temp_reportico_ral_cal_oic_sample_received_rosoralcal WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }
    public static function getRalCalOicSampleRegister($from_date, $to_date, $posted_ro_office, $lab, $sample, $commodity, $category, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ral_cal_oic_sample_register WHERE user_id = '$user_id'");

        $str = "SELECT si.letr_date,mct.category_name,CONCAT(r.user_flag,',',mll.ro_office) AS labname, si.letr_ref_no, si.org_sample_code,si.stage_sample_code,si.sample_total_qnt AS sample_qnt,mu.unit_weight,mp.par_condition_desc,si.sample_total_qnt,si.received_date, mc.commodity_name,si.commodity_code, si.category_code,st.sample_type_desc,si.loc_id, si.dispatch_date,si.dispatch_date, si.grading_date,si.remark,r.user_flag,sample_total_qnt
                FROM sample_inward AS si
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN m_commodity_category AS mct ON mct.category_code=si.category_code
                INNER JOIN m_par_condition AS mp ON mp.par_condition_code=si.par_condition_code
                INNER JOIN m_unit_weight AS mu ON mu.unit_id=si.parcel_size
                INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN dmi_users AS du ON du.id=si.user_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=du.email";

        $str .= " WHERE si.display='Y' AND si.acc_rej_flg='A' AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date'";

        if ($_SESSION['user_flag'] == "RO" || $_SESSION['user_flag'] == "SO") {
            $str .= " AND si.loc_id='" . $_SESSION['posted_ro_office'] . "'";
        }
        if ($_SESSION['user_flag'] == "RO" || $_SESSION['user_flag'] == "SO") {
            $str .= " AND r.user_flag='" . $_SESSION['user_flag'] . "'";
        }

        $str .= "AND si.loc_id='$ral_lab_no'  AND r.user_flag='$ral_lab_name' AND si.sample_type_code = '$sample' AND si.commodity_code='$commodity' AND si.category_code='$category' GROUP BY si.stage_sample_code,si.letr_date,sample_qnt,si.received_date,si.dispatch_date,si.grading_date,mll.ro_office,r.user_flag, mc.commodity_name,mct.category_name,si.commodity_code, si.category_code,st.sample_type_desc,si.loc_id,si.letr_ref_no,si.sample_total_qnt,mu.unit_weight,mp.par_condition_desc,si.dispatch_date, si.org_sample_code,si.remark
                ORDER BY received_date ASC";

        $query = $con->execute($str);

        $records = $query->fetchAll('assoc');
        // print_r($records);exit;
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $letr_date = $record['letr_date'];
                $category_name = $record['category_name'];
                $labname = $record['labname'];
                $letr_ref_no = $record['letr_ref_no'];
                $org_sample_code = $record['org_sample_code'];
                $stage_sample_code = $record['stage_sample_code'];
                $sample_qnt = $record['sample_qnt'];
                $unit_weight = $record['unit_weight'];
                $par_condition_desc = $record['par_condition_desc'];
                $sample_total_qnt = $record['sample_total_qnt'];
                $received_date = $record['received_date'];
                $commodity_name = $record['commodity_name'];
                $commodity_code = $record['commodity_code'];
                $category_code = $record['category_code'];
                $sample_type_desc = $record['sample_type_desc'];
                $loc_id = $record['loc_id'];
                $dispatch_date = $record['dispatch_date'];
                $grading_date = $record['grading_date'];
                $remark = $record['remark'];
                $user_flag = $record['user_flag'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ral_cal_oic_sample_register (sr_no, user_id, letr_date, category_name, labname, letr_ref_no, org_sample_code, stage_sample_code, sample_qnt, unit_weight, par_condition_desc, sample_total_qnt, received_date, commodity_name, commodity_code, category_code, sample_type_desc, loc_id, dispatch_date, grading_date, remark, user_flag, report_date) 
                VALUES (
                '$i','$user_id','$letr_date', '$category_name', '$labname', '$letr_ref_no', '$org_sample_code', '$stage_sample_code', '$sample_qnt', '$unit_weight', '$par_condition_desc', '$sample_total_qnt', '$received_date', '$commodity_name', '$commodity_code', '$category_code', '$sample_type_desc', '$loc_id', '$dispatch_date', '$grading_date', '$remark', '$user_flag', '$date')");

                $update = $con->execute("UPDATE temp_reportico_ral_cal_oic_sample_register SET counts = (SELECT COUNT(user_id) FROM temp_reportico_ral_cal_oic_sample_register WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }


    public static function getRalCalOicSampleRegistration($from_date, $to_date, $posted_ro_office, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ral_cal_oic_sample_registration WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT mc.commodity_name,si.grade,CONCAT(sd.pckr_nm,', ',pckr_addr) AS name_address_packer,sd.lot_no,si.received_date AS pack_date,sd.pack_size,sd.tbl,CONCAT(sd.shop_name,', ',sd.shop_address) AS shop_name_address,si.parcel_size,sd.smpl_drwl_dt,CONCAT(u.f_name,' ',u.l_name) AS name_officer,si.org_sample_code,si.dispatch_date,CONCAT(r.user_flag,', ',mll.ro_office) AS lab_or_office_name
                FROM sample_inward AS si
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN dmi_users AS u ON u.id=si.user_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN sample_inward_details AS sd ON sd.org_sample_code=si.stage_sample_code AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date' AND si.loc_id='$ral_lab_no' AND r.user_flag='$ral_lab_name'
                GROUP BY mll.ro_office,r.user_flag,si.grading_date,si.remark,si.grade,u.f_name,u.l_name,mc.commodity_name,si.grade,sd.pckr_nm,pckr_addr,si.received_date,sd.lot_no,sd.pack_size,sd.tbl,sd.shop_name,sd.shop_address,si.parcel_size,sd.smpl_drwl_dt, si.org_sample_code,si.dispatch_date,si.ral_lab_code,si.ral_anltc_rslt_rcpt_dt,si.anltc_rslt_chlng_flg,si.misgrd_param_value,si.misgrd_report_issue_dt, si.misgrd_reason,si.chlng_smpl_disptch_cal_dt,si.cal_anltc_rslt_rcpt_dt
                ORDER BY sd.smpl_drwl_dt DESC");
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $commodity_name = $record['commodity_name'];
                $grade = $record['grade'];
                $name_address_packer = $record['name_address_packer'];
                $lot_no = $record['lot_no'];
                $pack_date = $record['pack_date'];
                $pack_size = $record['pack_size'];
                $tbl = $record['tbl'];
                $shop_name_address = $record['shop_name_address'];
                $parcel_size = $record['parcel_size'];
                $smpl_drwl_dt = $record['smpl_drwl_dt'];
                $name_officer = $record['name_officer'];
                $org_sample_code = $record['org_sample_code'];
                $dispatch_date = $record['dispatch_date'];
                $lab_or_office_name = $record['lab_or_office_name'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ral_cal_oic_sample_registration (sr_no, user_id, commodity_name, grade, name_address_packer, lot_no, pack_date, pack_size, tbl, shop_name_address, parcel_size, smpl_drwl_dt, name_officer, org_sample_code, dispatch_date, lab_or_office_name, report_date) 
                VALUES (
                '$i','$user_id','$commodity_name', '$grade', '$name_address_packer', '$lot_no', '$pack_date', '$pack_size', '$tbl', '$shop_name_address', '$parcel_size', '$smpl_drwl_dt', '$name_officer', '$org_sample_code', '$dispatch_date', '$lab_or_office_name', '$date')");

                $update = $con->execute("UPDATE temp_reportico_ral_cal_oic_sample_registration SET counts = (SELECT COUNT(user_id) FROM temp_reportico_ral_cal_oic_sample_registration WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getRalCalOicSampleAcceptedByChemistForTesting($from_date, $to_date, $posted_ro_office, $lab, $user, $ral_lab, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ral_cal_oic_sample_accepted_chemist_testing WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT mll.ro_office,CONCAT(du.f_name,' ',du.l_name,' (',du.role,')') AS chemist_name,si.received_date,msa.org_sample_code,mc.commodity_name,mst.sample_type_desc,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name
                FROM m_sample_allocate AS msa
                INNER JOIN sample_inward AS si ON si.org_sample_code=msa.org_sample_code
                INNER JOIN m_commodity AS mc ON si.commodity_code=mc.commodity_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code
                INNER JOIN dmi_users AS du ON du.id=msa.alloc_to_user_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=du.email 
                WHERE  msa.display='Y' AND msa.acptnce_flag='Y' AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date' AND msa.lab_code='$ral_lab_no' AND r.user_flag='$ral_lab_name' AND msa.alloc_to_user_code='$user'");
        // print_r($query);exit;
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $ro_office = $record['ro_office'];
                $chemist_name = $record['chemist_name'];
                $received_date = $record['received_date'];
                $org_sample_code = $record['org_sample_code'];
                $commodity_name = $record['commodity_name'];
                $sample_type_desc = $record['sample_type_desc'];
                $lab_name = $record['lab_name'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ral_cal_oic_sample_accepted_chemist_testing (sr_no, user_id, ro_office, chemist_name, received_date, org_sample_code, commodity_name, sample_type_desc, lab_name, report_date) 
                VALUES (
                '$i','$user_id','$ro_office', '$chemist_name', '$received_date', '$org_sample_code', '$commodity_name', '$sample_type_desc', '$lab_name','$date')");

                $update = $con->execute("UPDATE temp_reportico_ral_cal_oic_sample_accepted_chemist_testing SET counts = (SELECT COUNT(user_id) FROM temp_reportico_ral_cal_oic_sample_accepted_chemist_testing WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getRalCalOicCodingDecodingSection($from_date, $to_date, $posted_ro_office, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ral_cal_oic_coding_decoding WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT mll.ro_office,mc.commodity_name,si.stage_sample_code,w.tran_date,si.received_date,si.sample_total_qnt,muw.unit_weight,mst.sample_type_desc,si.remark
                FROM sample_inward AS si
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN m_unit_weight AS muw ON muw.unit_id=si.parcel_size
                INNER JOIN dmi_users AS u ON u.id=si.user_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN workflow AS w ON w.org_sample_code=si.org_sample_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code AND w.stage_smpl_flag In ('OF','HF','FG') AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date' AND w.dst_loc_id='$ral_lab_no'
                GROUP BY mll.ro_office,mc.commodity_name,w.tran_date,si.stage_sample_code,si.received_date,si.sample_total_qnt,muw.unit_weight,mst.sample_type_desc,si.remark
                ORDER BY mc.commodity_name ASC");
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $ro_office = $record['ro_office'];
                $commodity_name = $record['commodity_name'];
                $stage_sample_code = $record['stage_sample_code'];
                $tran_date = $record['tran_date'];
                $sample_total_qnt = $record['sample_total_qnt'];
                $unit_weight = $record['unit_weight'];
                $sample_qnt = $sample_total_qnt . ' ' . $unit_weight;
                $sample_type_desc = $record['sample_type_desc'];
                $remark = $record['remark'];
                $received_date = $record['received_date'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ral_cal_oic_coding_decoding (sr_no, user_id, ro_office, commodity_name, stage_sample_code, received_date, tran_date, sample_qnt, sample_type_desc, remark, report_date) 
                VALUES (
                '$i','$user_id','$ro_office', '$commodity_name', '$stage_sample_code','$received_date', '$tran_date', '$sample_qnt','$sample_type_desc','$remark','$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getRalCalOicSampleAllotedToChemistForTesting($from_date, $to_date, $posted_ro_office, $lab, $user, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ral_cal_oic_sample_alloted_chemist_testing WHERE user_id = '$user_id'");

        $str = "SELECT si.org_sample_code,mc.commodity_name,si.received_date,mst.sample_type_desc,mll.ro_office,r.user_flag,CONCAT(du.f_name,' ',du.l_name,' (',du.role,')') AS chemist_name,msa.alloc_date
                FROM sample_inward AS si
                INNER JOIN code_decode AS cd ON cd.org_sample_code=si.org_sample_code
                INNER JOIN m_sample_allocate AS msa ON msa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN m_commodity AS mc ON si.commodity_code=mc.commodity_code
                INNER JOIN dmi_users AS du ON du.id=cd.alloc_to_user_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=du.email
                INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code AND cd.display='Y' AND cd.chemist_code!='-' AND DATE(msa.alloc_date) BETWEEN '$from_date' AND '$to_date' AND cd.alloc_to_user_code='$user'";

        if ($_SESSION['user_flag'] == "RAL") {
            $str .= " AND cd.lab_code='" . $_SESSION['posted_ro_office'] . "'";
        } else {
            $str .= " AND cd.lab_code='$ral_lab_no'";
        }

        $str .= " ORDER BY si.received_date ASC";

        $query = $con->execute($str);

        // print_r($query);exit;
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $org_sample_code = $record['org_sample_code'];
                $commodity_name = $record['commodity_name'];
                $chemist_name = $record['chemist_name'];
                $received_date = $record['received_date'];
                $org_sample_code = $record['org_sample_code'];
                $ro_office = $record['ro_office'];
                $sample_type_desc = $record['sample_type_desc'];
                $user_flag = $record['user_flag'];
                $alloc_date = $record['alloc_date'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ral_cal_oic_sample_alloted_chemist_testing (sr_no, user_id, ro_office, chemist_name, received_date, org_sample_code, commodity_name, sample_type_desc, user_flag, alloc_date, report_date) 
                VALUES (
                '$i','$user_id','$ro_office', '$chemist_name', '$received_date', '$org_sample_code', '$commodity_name', '$sample_type_desc', '$user_flag','$alloc_date','$date')");

                $update = $con->execute("UPDATE temp_reportico_ral_cal_oic_sample_alloted_chemist_testing SET counts = (SELECT COUNT(user_id) FROM temp_reportico_ral_cal_oic_sample_alloted_chemist_testing WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getRalCalOicSampleAllotedToChemistForRetesting($from_date, $to_date, $posted_ro_office, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ral_cal_oic_sample_alloted_chemist_retesting WHERE user_id = '$user_id'");

        $str = "SELECT mll.ro_office,msa.org_sample_code,mc.commodity_name,si.received_date,mst.sample_type_desc,CONCAT(du.f_name,' ',du.l_name) AS chemist_name,msa.alloc_date
                FROM m_sample_allocate AS msa
                INNER JOIN sample_inward AS si ON si.org_sample_code=msa.org_sample_code
                INNER JOIN m_commodity AS mc ON si.commodity_code=mc.commodity_code
                INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN dmi_users AS du ON du.id=si.user_code AND msa.display='Y' AND msa.test_n_r='R' AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date'";

        if ($_SESSION['user_flag'] == "RAL") {
            $str .= " AND msa.lab_code='" . $_SESSION['posted_ro_office'] . "'";
        } else {
            $str .= " AND msa.lab_code='$ral_lab_no'";
        }

        $str .= " ORDER BY si.received_date ASC";

        $query = $con->execute($str);
        //  print_r($query);exit;
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $org_sample_code = $record['org_sample_code'];
                $commodity_name = $record['commodity_name'];
                $chemist_name = $record['chemist_name'];
                $received_date = $record['received_date'];
                $org_sample_code = $record['org_sample_code'];
                $ro_office = $record['ro_office'];
                $sample_type_desc = $record['sample_type_desc'];
                $alloc_date = $record['alloc_date'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ral_cal_oic_sample_alloted_chemist_retesting (sr_no, user_id, ro_office, chemist_name, received_date, org_sample_code, commodity_name, sample_type_desc, alloc_date, report_date) 
                VALUES (
                '$i','$user_id','$ro_office', '$chemist_name', '$received_date', '$org_sample_code', '$commodity_name', '$sample_type_desc','$alloc_date','$date')");

                $update = $con->execute("UPDATE temp_reportico_ral_cal_oic_sample_alloted_chemist_retesting SET counts = (SELECT COUNT(user_id) FROM temp_reportico_ral_cal_oic_sample_alloted_chemist_retesting WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getRalCalOicTestedSample($from_date, $to_date, $posted_ro_office, $user_code, $commodity, $lab, $sample_type, $ral_lab, $role, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ral_cal_oic_tested_sample WHERE user_id = '$user_id'");

        if ($role == 'Jr Chemist' || $role == 'Sr Chemist' || $role == 'Cheif Chemist') {
            $str = "SELECT du.role,mll.ro_office,CONCAT(du.f_name,' ',du.l_name) as chemist_name,msa.recby_ch_date,mst.sample_type_desc,mc.commodity_name,cd.sample_code as org_sample_code,
                msa.expect_complt,msa.commencement_date ,CASE WHEN msa.commencement_date>msa.expect_complt THEN msa.commencement_date   ::date-msa.expect_complt ::date ELSE 0
                END as delay,expect_complt::date - recby_ch_date::date  as complete,si.received_date,
                r.user_flag,du.f_name,du.l_name,grade,
                (SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab) AS lab_name
                FROM sample_inward as si 
                Inner Join m_sample_allocate as msa ON msa.org_sample_code=si.org_sample_code
                Inner Join m_sample_type as mst ON mst.sample_type_code=si.sample_type_code 
                Inner Join dmi_ro_offices as mll ON mll.id=si.loc_id 
                Inner Join code_decode as cd on cd.org_sample_code=si.org_sample_code
                Inner Join dmi_users as du ON du.id=cd.alloc_to_user_code
                Inner Join dmi_user_roles as r On r.user_email_id=du.email		
                Inner Join m_commodity as mc ON mc.commodity_code=si.commodity_code 
                WHERE cd.display='Y' and cd.alloc_to_user_code='" . $_SESSION['user_code'] . "' AND cd.lab_code='$posted_ro_office' AND date(si.received_date) BETWEEN '$from_date' and '$to_date'
                GROUP BY du.role,mll.ro_office,du.f_name,du.l_name,cd.sample_code,msa.commencement_date,msa.recby_ch_date,msa.expect_complt,cd.chemist_code,mc.commodity_name,mst.sample_type_desc,si.received_date,r.user_flag,du.f_name,du.l_name,si.grade order by si.received_date";
        } else {
                $str = "SELECT mll.ro_office,CONCAT(du.f_name,' ',du.l_name,' (',du.role,')') as chemist_name,du.role,msa.recby_ch_date,mst.sample_type_desc,mc.commodity_name,cd.sample_code as org_sample_code,
                msa.expect_complt,msa.commencement_date, CASE WHEN msa.commencement_date>msa.expect_complt THEN msa.commencement_date   ::date-msa.expect_complt ::date ELSE 0
                END as delay,expect_complt::date - recby_ch_date::date  as complete,si.received_date,
                r.user_flag,du.f_name,du.l_name,grade,
                (SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab) AS lab_name
                FROM sample_inward as si 
                Inner Join m_sample_allocate as msa ON msa.org_sample_code=si.org_sample_code
                Inner Join m_sample_type as mst ON mst.sample_type_code=si.sample_type_code 
                Inner Join dmi_users as du ON du.id=msa.alloc_to_user_code
                Inner Join dmi_user_roles as r On r.user_email_id=du.email
                Inner Join dmi_ro_offices as mll ON mll.id=si.loc_id 
                Inner Join code_decode as cd on cd.org_sample_code=si.org_sample_code 
                Inner Join m_commodity as mc ON mc.commodity_code=si.commodity_code 
                WHERE cd.display='Y' AND date(si.received_date) BETWEEN '$from_date' and '$to_date' AND si.sample_type_code='$sample_type' AND si.commodity_code='$commodity' AND r.user_flag='$ral_lab_name' AND msa.lab_code='$ral_lab_no'
                GROUP BY du.role,mll.ro_office,du.f_name,du.l_name,cd.sample_code,msa.commencement_date,msa.recby_ch_date,msa.expect_complt,cd.chemist_code,mc.commodity_name,mst.sample_type_desc,si.received_date,r.user_flag,du.f_name,du.l_name,si.grade order by si.received_date";
        }

        $query = $con->execute($str);
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $ro_office = $record['ro_office'];
                $chemist_name = $record['chemist_name'];
                $recby_ch_date = $record['recby_ch_date'];
                $sample_type_desc = $record['sample_type_desc'];
                $commodity_name = $record['commodity_name'];
                $org_sample_code = $record['org_sample_code'];
                $expect_complt = $record['expect_complt'];
                $commencement_date = $record['commencement_date'];
                $grade = $record['grade'];
                $lab_name = $record['lab_name'];
                $role = $record['role'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ral_cal_oic_tested_sample (sr_no, user_id, ro_office, role, chemist_name, recby_ch_date, org_sample_code, commodity_name, sample_type_desc, expect_complt, commencement_date, grade, lab_name, report_date) 
                VALUES (
                '$i','$user_id','$ro_office','$role', '$chemist_name', '$recby_ch_date', '$org_sample_code', '$commodity_name', '$sample_type_desc','$expect_complt','$commencement_date','$grade','$lab_name','$date')");

                $update = $con->execute("UPDATE temp_reportico_ral_cal_oic_tested_sample SET counts = (SELECT COUNT(user_id) FROM temp_reportico_ral_cal_oic_tested_sample WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getRalCalOicTestSubmitByChemist($from_date, $to_date, $chemist_code, $sample_code, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ral_cal_oic_test_submit_by_chemist WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT recby_ch_date,sa.chemist_code,sa.sample_code,c.commodity_name,CONCAT(du.f_name,' ',du.l_name,' (',du.role,') ') AS chemist_name,t.test_name,atd.result,'$chemist_code'AS chemist_code,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name
                FROM m_sample_allocate sa
                INNER JOIN actual_test_data AS atd ON sa.chemist_code=atd.chemist_code
                INNER JOIN code_decode AS cd ON cd.org_sample_code=sa.org_sample_code
                INNER JOIN m_test AS t ON atd.test_code=t.test_code
                INNER JOIN m_commodity AS c ON c.commodity_code=sa.commodity_code
                INNER JOIN dmi_users AS du ON du.id=sa.alloc_to_user_code AND DATE(sa.alloc_date) BETWEEN '$from_date' AND '$to_date' AND sa.chemist_code='$chemist_code'
                WHERE atd.chemist_code='$chemist_code' AND sa.sample_code='$sample_code' AND cd.status_flag in('C','G') AND sa.chemist_code='$chemist_code'
                GROUP BY du.role,sa.chemist_code,sa.recby_ch_date,sa.sample_code,c.commodity_name,du.f_name,du.l_name,t.test_name,atd.result");
        // print_r($query);exit;
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $recby_ch_date = $record['recby_ch_date'];
                $commodity_name = $record['commodity_name'];
                $sample_code = $record['sample_code'];
                $chemist_name = $record['chemist_name'];
                $test_name = $record['test_name'];
                $result = $record['result'];
                $chemist_code = $record['chemist_code'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ral_cal_oic_test_submit_by_chemist (sr_no, user_id, lab_name, recby_ch_date, commodity_name, sample_code, chemist_name, test_name, result, chemist_code, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$recby_ch_date', '$commodity_name', '$sample_code', '$chemist_name','$test_name','$result','$chemist_code','$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getRalCalOicReTestedSample($from_date, $to_date, $ral_lab_name, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ral_cal_oic_retested_sample WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT  msa.sample_code AS org_sample_code,mc.commodity_name,mst.sample_type_desc,si.received_date,ml.ro_office,CONCAT(du.f_name,' ',du.l_name) AS full_name,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name
                FROM sample_inward si
                INNER JOIN m_sample_allocate AS msa ON msa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_ro_offices AS ml ON ml.id=si.loc_id
                INNER JOIN dmi_users AS du ON du.id=si.user_code
                INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code 
                where DATE(si.received_date) BETWEEN '$from_date' AND '$to_date' aND si.display='Y' AND msa.display='Y' AND msa.test_n_r='R' 
                GROUP BY msa.sample_code,mc.commodity_name,mst.sample_type_desc,si.received_date,ml.ro_office,du.f_name,du.l_name");
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $org_sample_code = $record['org_sample_code'];
                $commodity_name = $record['commodity_name'];
                $sample_type_desc = $record['sample_type_desc'];
                $received_date = $record['received_date'];
                $ro_office = $record['ro_office'];
                $full_name = $record['full_name'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ral_cal_oic_retested_sample (sr_no, user_id, lab_name, org_sample_code, commodity_name, sample_type_desc, received_date, ro_office, full_name, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$org_sample_code', '$commodity_name', '$sample_type_desc', '$received_date','$ro_office','$full_name','$date')");

                $update = $con->execute("UPDATE temp_reportico_ral_cal_oic_retested_sample SET counts = (SELECT COUNT(user_id) FROM temp_reportico_ral_cal_oic_retested_sample WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getRalCalOicReTestedSampleByChemist($from_date, $to_date, $ral_lab_name, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ral_cal_oic_retested_sample_submit WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT  msa.sample_code AS org_sample_code,mc.commodity_name,mst.sample_type_desc,si.received_date,ml.lab_name AS lab,CONCAT(du.f_name,' ',du.l_name) AS full_name,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name
                FROM sample_inward AS si
                INNER JOIN m_lab AS ml ON ml.lab_code=si.loc_id
                INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code
                INNER JOIN dmi_users AS du ON du.id=si.user_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=du.email
                INNER JOIN m_sample_allocate AS msa ON msa.org_sample_code=si.org_sample_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code 
                WHERE si.display='Y' AND msa.display='Y' AND msa.test_n_r='R' AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date'
                GROUP BY msa.sample_code,mc.commodity_name,mst.sample_type_desc,si.received_date,ml.lab_name,du.f_name,du.l_name");
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $org_sample_code = $record['org_sample_code'];
                $commodity_name = $record['commodity_name'];
                $sample_type_desc = $record['sample_type_desc'];
                $received_date = $record['received_date'];
                $full_name = $record['full_name'];
                $lab = $record['lab'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ral_cal_oic_retested_sample_submit (sr_no, user_id, lab_name, org_sample_code, commodity_name, sample_type_desc, received_date, full_name, lab, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$org_sample_code', '$commodity_name', '$sample_type_desc', '$received_date','$full_name','$lab','$date')");

                $update = $con->execute("UPDATE temp_reportico_ral_cal_oic_retested_sample_submit SET counts = (SELECT COUNT(user_id) FROM temp_reportico_ral_cal_oic_retested_sample_submit WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getRalCalOicSampleAnalyzedByChemist($from_date, $to_date, $posted_ro_office, $user, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ral_cal_oic_sample_analyzed_chemist WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT DISTINCT(sa.sample_code),ml.ro_office,si.stage_sample_code,ml.id AS lab,CONCAT(r.user_flag,', ',ml.ro_office) AS sample_received_from,mc.commodity_name,sc.sam_condition_desc,ct.container_desc,pc.par_condition_desc,
                si.received_date,si.letr_ref_no, CONCAT(u.f_name,' ', u.l_name) AS name_chemist,si.sample_total_qnt,si.lab_code,si.grading_date,si.remark,sa.alloc_date,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name
                FROM sample_inward AS si
                INNER JOIN m_sample_condition AS sc ON sc.sam_condition_code=si.sam_condition_code
                INNER JOIN m_par_condition AS pc ON pc.par_condition_code=si.par_condition_code
                INNER JOIN m_container_type AS ct ON ct.container_code=si.container_code
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                INNER JOIN dmi_ro_offices AS ml ON ml.id=sa.lab_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                WHERE sa.lab_code='$posted_ro_office' AND DATE(sa.alloc_date) BETWEEN '$from_date' AND '$to_date' AND sa.alloc_to_user_code='$user'
                ORDER BY si.received_date ASC");
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $sample_code = $record['sample_code'];
                $ro_office = $record['ro_office'];
                $stage_sample_code = $record['stage_sample_code'];
                $sample_received_from = $record['sample_received_from'];
                $commodity_name = $record['commodity_name'];
                $sam_condition_desc = $record['sam_condition_desc'];
                $container_desc = $record['container_desc'];
                $par_condition_desc = $record['par_condition_desc'];
                $received_date = $record['received_date'];
                $letr_ref_no = $record['letr_ref_no'];
                $name_chemist = $record['name_chemist'];
                $sample_total_qnt = $record['sample_total_qnt'];
                $lab_code = $record['lab_code'];
                $grading_date = $record['grading_date'];
                $remark = $record['remark'];
                $alloc_date = $record['alloc_date'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ral_cal_oic_sample_analyzed_chemist (sr_no, user_id, lab_name, sample_code, ro_office, stage_sample_code, sample_received_from, commodity_name, sam_condition_desc, container_desc,par_condition_desc,received_date, letr_ref_no, name_chemist, sample_total_qnt, lab_code, grading_date, remark, alloc_date, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$sample_code', '$ro_office', '$stage_sample_code', '$sample_received_from','$commodity_name','$sam_condition_desc','$container_desc','$par_condition_desc','$received_date','$letr_ref_no','$name_chemist','$sample_total_qnt','$lab_code','$grading_date','$remark','$alloc_date','$date')");

                $update = $con->execute("UPDATE temp_reportico_ral_cal_oic_sample_analyzed_chemist SET counts = (SELECT COUNT(user_id) FROM temp_reportico_ral_cal_oic_sample_analyzed_chemist WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function  getRalCalOicNoCheckPrivateResearchSample($from_date, $to_date, $posted_ro_office, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $begin = new DateTime($from_date);
        $end = new DateTime($to_date);
        $k = 0;
        while ($begin <= $end) {
            $yr_data[$k]['month'] = $begin->format('m');
            $yr_data[$k]['year'] = $begin->format('Y');
            $month1[$k] = $begin->format('M') . ',' . $begin->format('Y');

            $k++;
            $begin->modify('first day of next month');
        } //

        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ral_cal_oic_chk_pvt_research WHERE user_id = '$user_id'");

        foreach ($yr_data as $da) {
            $month = $da['month'];
            $year = $da['year'];

            $query = $con->execute("SELECT dmi_ro_offices.id AS lab_code,
                    (
                    SELECT COUNT(DISTINCT(cd.org_sample_code)) AS check_analyzed_count
                    FROM code_decode cd
                    INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                    INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    WHERE cd.display='Y' AND st.sample_type_desc='Check' AND cd.status_flag In('C','G') AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND cd.lab_code='$ral_lab_no' AND DATE(sa.alloc_date) BETWEEN '$from_date' AND '$to_date'
                    ) AS check_analyzed_count,
                    (
                    SELECT COUNT(DISTINCT(cd.org_sample_code)) AS res_analyzed_count
                    FROM code_decode cd
                    INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                    INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    WHERE cd.display='Y' AND st.sample_type_desc='Research' AND cd.status_flag In('C','G') AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND cd.lab_code='$ral_lab_no' AND DATE(sa.alloc_date) BETWEEN '$from_date' AND '$to_date'
                    ) AS res_analyzed_count,
                    (
                    SELECT COUNT(DISTINCT(cd.org_sample_code)) AS chk_analyzed_count
                    FROM code_decode cd
                    INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                    INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    WHERE cd.display='Y' AND st.sample_type_desc='Challenged' AND cd.status_flag In('C','G') AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND cd.lab_code='$ral_lab_no' AND DATE(sa.alloc_date) BETWEEN '$from_date' AND '$to_date'
                    ) AS chk_analyzed_count,
                    (
                    SELECT COUNT(DISTINCT(cd.org_sample_code)) AS othr_analyzed_count
                    FROM code_decode cd
                    INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                    INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    WHERE cd.display='Y' AND st.sample_type_desc NOT IN('Check','Research','Challenged') AND cd.status_flag In('C','G') AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND cd.lab_code='$ral_lab_no' AND DATE(sa.alloc_date) BETWEEN '$from_date' AND '$to_date'
                    ) AS othr_analyzed_count,
                    (
                    SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                    FROM dmi_users AS u
                    INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                    INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                    WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                    GROUP BY ral_lab
                    ) AS lab_name
                    
                    FROM dmi_users
                    INNER JOIN dmi_ro_offices ON dmi_users.posted_ro_office=dmi_ro_offices.id
                    INNER JOIN dmi_user_roles ON dmi_user_roles.user_email_id=dmi_users.email AND dmi_user_roles.user_flag In('RAL','CAL') AND dmi_users.posted_ro_office='$ral_lab_no'
                    WHERE dmi_users.status = 'active'
                    GROUP BY dmi_user_roles.user_flag,dmi_ro_offices.id,dmi_ro_offices.ro_office
                    ORDER BY dmi_ro_offices.ro_office ASC");

            $records = $query->fetchAll('assoc');
            // print_r($records);

            $query->closeCursor();
            if (!empty($records)) {
                foreach ($records as $record) {
                    $lab_name = $record['lab_name'];
                    $check_analyzed_count = $record['check_analyzed_count'];
                    $res_analyzed_count = $record['res_analyzed_count'];
                    $chk_analyzed_count = $record['chk_analyzed_count'];
                    $othr_analyzed_count = $record['othr_analyzed_count'];
                    $date = date("d/m/Y");
                    $i = $i + 1;

                    $insert = $con->execute("INSERT INTO temp_reportico_ral_cal_oic_chk_pvt_research (sr_no, user_id, lab_name, check_analyzed_count, res_analyzed_count, chk_analyzed_count, othr_analyzed_count,  report_date) 
            VALUES (
                '$i','$user_id','$lab_name', '$check_analyzed_count', '$res_analyzed_count', '$chk_analyzed_count', '$othr_analyzed_count','$date')");
                }
                return 1;
            } else {
                return 0;
            }
        }
    }

    public static function getRalCalOicChemistWiseSampleAnalysis($month, $posted_ro_office, $user, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ral_cal_oic_chemist_wise_samp_analysis WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT CONCAT(u.f_name,' ',u.l_name,'(',u.role,')') AS chemist_name, u.id, 
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS check_analyzed_count
                FROM code_decode cd
                INNER JOIN sample_inward AS si ON si.stage_sample_code=cd.sample_code
                INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.sample_code=si.stage_sample_code
                WHERE cd.display='Y' AND st.sample_type_desc='Check' AND cd.status_flag In('C','G') AND cd.alloc_to_user_code='$user' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month'
                ) AS check_analyzed_count, 
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS res_analyzed_count
                FROM code_decode cd
                INNER JOIN sample_inward AS si ON si.stage_sample_code=cd.sample_code
                INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.sample_code=si.stage_sample_code
                WHERE cd.display='Y' AND st.sample_type_desc='Research' AND cd.status_flag In('C','G') AND cd.alloc_to_user_code='$user' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month'
                ) AS res_analyzed_count, 
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS res_challenged_count
                FROM code_decode cd
                INNER JOIN sample_inward AS si ON si.stage_sample_code=cd.sample_code
                INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.sample_code=si.stage_sample_code
                WHERE cd.display='Y' AND st.sample_type_desc='Challenged' AND cd.status_flag In('C','G') AND cd.alloc_to_user_code='$user' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month'
                ) AS res_challenged_count, 
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS othr_analyzed_count
                FROM code_decode cd
                INNER JOIN sample_inward AS si ON si.stage_sample_code=cd.sample_code
                INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.sample_code=si.stage_sample_code
                WHERE cd.display='Y' AND st.sample_type_desc NOT IN('Check','Research','Challenged','Apex(Check)') AND cd.status_flag In('C','G') AND cd.alloc_to_user_code='$user' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month'
                ) AS othr_analyzed_count,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name
                
                FROM dmi_users u
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                INNER JOIN dmi_ro_offices drf ON u.posted_ro_office=drf.id
                WHERE u.posted_ro_office= '$ral_lab_no' AND u.role IN('Jr Chemist','Sr Chemist','Cheif Chemist') AND u.STATUS = 'active' AND u.id = '$user'
                ");
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $chemist_name = $record['chemist_name'];
                $check_analyzed_count = $record['check_analyzed_count'];
                $res_analyzed_count = $record['res_analyzed_count'];
                $res_challenged_count = $record['res_challenged_count'];
                $othr_analyzed_count = $record['othr_analyzed_count'];
                $date = date("d/m/Y");
                $month = date("F", mktime(0, 0, 0, $month, 10));
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ral_cal_oic_chemist_wise_samp_analysis (sr_no, user_id, lab_name, chemist_name, check_analyzed_count, res_analyzed_count, res_challenged_count, othr_analyzed_count, month, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$chemist_name', '$check_analyzed_count','$res_analyzed_count','$res_challenged_count','$othr_analyzed_count', '$month','$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getRalCalOicConsolidateSatementBroughtFwdCarriedFwdSample($month, $user, $posted_ro_office, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ral_cal_oic_consoli_sample WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT CONCAT(u.f_name,' ',u.l_name,' (',ur.role_name,') ') AS chemist_name,ur.role_name AS role_name,'$month' AS month,
                (
                SELECT count(distinct(sample_code)) as bf_count 
                FROM code_decode 
                WHERE lab_code='$ral_lab_no' and status_flag NOT IN('G','F' ) and alloc_to_user_code='$user'
                ) as bf_count,
                (
                SELECT count(distinct(sample_code)) as received_count 
                FROM m_sample_allocate 
                WHERE lab_code='$ral_lab_no' and Extract(month from alloc_date)::INTEGER = '$month' and alloc_to_user_code='$user'
                ) AS received_count,
                (
                SELECT count(distinct(cd.sample_code)) as analyzed_count_one 
                FROM code_decode as cd
                INNER Join m_sample_allocate as sa ON sa.sample_code=cd.sample_code 			
                WHERE test_n_r!='R' and cd.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' and cd.alloc_to_user_code='$user'
                ) AS analyzed_count_one,                            
                (
                SELECT count(distinct(cd.sample_code)) as analyzed_count_two FROM code_decode as cd
                Inner Join m_sample_allocate as sa ON sa.sample_code=cd.sample_code 			
                where test_n_r='R' and cd.lab_code='$ral_lab_no' and Extract(month from alloc_date)::INTEGER = '$month' and cd.alloc_to_user_code='$user') AS analyzed_count_two,
                (SELECT CONCAT(r.user_flag,', ',o.ro_office
                ) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab) AS lab_name
                
                from dmi_users as u
                Inner Join dmi_ro_offices as mlc ON mlc.id=u.posted_ro_office 							
                Inner Join user_role as ur ON ur.role_name=u.role 
                inner join dmi_user_roles as r on u.email=r.user_email_id
                WHERE mlc.id='$ral_lab_no' and u.id='$user' And u.role In ('Jr Chemist','Sr Chemist','Cheif Chemist') and u.status='active'");
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $bf_count = $record['bf_count'];
                $received_count = $record['received_count'];
                $total = $bf_count + $received_count;
                $analyzed_count_one = $record['analyzed_count_one'];
                $analyzed_count_two = $record['analyzed_count_two'];
                $carried_for = $total - $analyzed_count_one;
                $chemist_name = $record['chemist_name'];
                $role_name = $record['role_name'];
                $month = date("F", mktime(0, 0, 0, $record['month'], 10));
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ral_cal_oic_consoli_sample (sr_no, user_id, lab_name, chemist_name, bf_count, received_count, total, analyzed_count_one, analyzed_count_two, carried_for, role_name, month, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$chemist_name', '$bf_count', '$received_count', '$total','$analyzed_count_one','$analyzed_count_two','$carried_for','$role_name','$month','$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getRalCalOicSampleAlloteAnalyzePending($from_date, $to_date, $posted_ro_office, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ral_cal_oic_sample_allot_analyz_pend WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT dmi_ro_offices.id AS lab_code,
                (
                SELECT COUNT(*) AS allotment_count
                FROM code_decode AS cd
                INNER JOIN m_sample_allocate AS msa ON cd.org_sample_code=msa.org_sample_code
                WHERE cd.status_flag in ('N','C','G') AND DATE(msa.alloc_date) BETWEEN '$from_date' AND '$to_date' AND cd.lab_code='$ral_lab_no'
                ) AS allotment_count,
                (
                SELECT COUNT(*) AS analyzed_count
                FROM code_decode AS cd
                INNER JOIN m_sample_allocate AS msa ON cd.org_sample_code=msa.org_sample_code
                WHERE cd.status_flag in ('C','G') AND DATE(msa.alloc_date) BETWEEN '$from_date' AND '$to_date' AND cd.lab_code='$ral_lab_no'
                ) AS analyzed_count,
                (
                SELECT COUNT(*) AS pending_count
                FROM code_decode AS cd
                INNER JOIN m_sample_allocate AS msa ON cd.org_sample_code=msa.org_sample_code
                WHERE cd.status_flag='N' AND DATE(msa.alloc_date) BETWEEN '$from_date' AND '$to_date' AND cd.lab_code='$ral_lab_no'
                ) AS pending_count,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name
                
                FROM dmi_users
                INNER JOIN dmi_user_roles AS r ON dmi_users.email=r.user_email_id
                INNER JOIN dmi_ro_offices ON dmi_users.posted_ro_office=dmi_ro_offices.id AND r.user_flag In ('RAL','CAL') AND dmi_users.posted_ro_office='$ral_lab_no'
                WHERE dmi_users.status = 'active'
                GROUP BY r.user_flag,dmi_ro_offices.id,dmi_ro_offices.ro_office
                ORDER BY dmi_ro_offices.ro_office ASC");
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $allotment_count = $record['allotment_count'];
                $analyzed_count = $record['analyzed_count'];
                $pending_count = $record['pending_count'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ral_cal_oic_sample_allot_analyz_pend (sr_no, user_id, lab_name, allotment_count, analyzed_count, pending_count, report_date) 
                VALUES (
               '$i','$user_id', '$lab_name', '$allotment_count', '$analyzed_count', '$pending_count', '$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getRalCalOicPerformanceRalCal($from_date, $to_date, $posted_ro_office, $lab, $sample_type, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $month = date("m", strtotime($from_date));

        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ral_cal_oic_performance_ral_cal WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT drf.id AS lab_code,
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS progress_sample
                FROM code_decode cd
                INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                WHERE cd.display='Y' AND si.sample_type_code='$sample_type' AND cd.lab_code='$ral_lab_no' AND cd.status_flag In('C','G') AND date(sa.alloc_date) BETWEEN '$from_date' AND '$to_date'
                ) AS progress_sample,
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS tot_sample_month
                FROM code_decode cd
                INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                WHERE cd.display='Y' AND si.sample_type_code='$sample_type' AND cd.lab_code='$ral_lab_no' AND cd.status_flag In('C','G') AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = $month
                ) AS tot_sample_month,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name
                
                FROM dmi_users u
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                INNER JOIN dmi_ro_offices drf ON u.posted_ro_office=drf.id
                WHERE u.status = 'active' AND r.user_flag='$ral_lab_name' AND drf.id='$ral_lab_no'
                GROUP BY r.user_flag,drf.id,drf.ro_office
                ORDER BY drf.ro_office ASC
                ");
       
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $progress_sample = $record['progress_sample'];
                $tot_sample_month = $record['tot_sample_month'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ral_cal_oic_performance_ral_cal (sr_no, user_id, lab_name, progress_sample, tot_sample_month, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$progress_sample', '$tot_sample_month', '$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getRalCalOicBroughtForwardAnalysedCarrSample($month, $ral_lab, $posted_ro_office, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ral_cal_oic_brg_fwd_ana_carr_fwd_sam WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT CONCAT(u.f_name,' ',u.l_name,' (',ur.role_name,') ') AS chemist_name,'$month' AS month,
                (
                SELECT COUNT(DISTINCT(sample_code)) AS bf_count
                FROM code_decode
                WHERE lab_code='$ral_lab_no' AND status_flag NOT IN('G','F')
                ) AS bf_count,
                (
                SELECT COUNT(DISTINCT(sample_code)) AS received_count
                FROM m_sample_allocate
                WHERE lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM alloc_date):: INTEGER = '$month'
                ) AS received_count,
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS analyzed_count_in_month
                FROM code_decode AS cd
                INNER JOIN m_sample_allocate AS sa ON sa.sample_code=cd.sample_code
                WHERE test_n_r!='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month'
                ) AS analyzed_count_in_month,                            
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS analyzed_count_in_month_repeat
                FROM code_decode AS cd
                INNER JOIN m_sample_allocate AS sa ON sa.sample_code=cd.sample_code
                WHERE test_n_r='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM alloc_date):: INTEGER = '$month'
                ) AS analyzed_count_in_month_repeat,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name
                
                from dmi_users as u
                INNER JOIN dmi_ro_offices AS mlc ON mlc.id=u.posted_ro_office
                INNER JOIN user_role AS ur ON ur.role_name=u.role
                INNER JOIN dmi_user_roles AS r ON u.email=r.user_email_id
                WHERE mlc.id='$ral_lab_no' AND u.role In ('Jr Chemist','Sr Chemist','Cheif Chemist') AND u.status = 'active'
                ");
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $chemist_name = $record['chemist_name'];
                $bf_count = $record['bf_count'];
                $received_count = $record['received_count'];
                $total = $bf_count + $received_count;
                $analyzed_count_in_month = $record['analyzed_count_in_month'];
                $analyzed_count_in_month_repeat = $record['analyzed_count_in_month_repeat'];
                $carried_for = $total - $analyzed_count_in_month;
                $monthNo = $record['month'];
                $month = date("F", mktime(0, 0, 0, $monthNo, 10));
                // echo $month;exit;
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ral_cal_oic_brg_fwd_ana_carr_fwd_sam (sr_no, user_id, lab_name, chemist_name, bf_count, received_count, total, analyzed_count_in_month, analyzed_count_in_month_repeat, carried_for, month, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$chemist_name', '$bf_count','$received_count', '$total', '$analyzed_count_in_month', '$analyzed_count_in_month_repeat', '$carried_for','$month','$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getRalCalOicCategorywiseReceivedSample($from_date, $to_date, $posted_ro_office, $sample_type, $Category, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ral_cal_oic_categorywise_received_sample WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT c.category_name, COUNT(*),st.sample_type_desc,ml.ro_office,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name
                FROM sample_inward AS si
                INNER JOIN m_commodity_category AS c ON c.category_code=si.category_code
                INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                INNER JOIN code_decode AS cd ON si.org_sample_code=cd.org_sample_code
                INNER JOIN dmi_ro_offices AS ml ON ml.id=cd.lab_code AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date' AND cd.lab_code='$ral_lab_no' AND si.category_code='$Category' AND si.sample_type_code='$sample_type'
                GROUP BY category_name,ml.ro_office,st.sample_type_desc
                ORDER BY c.category_name ASC");
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $count = $record['count'];
                $ro_office = $record['ro_office'];
                $sample_type_desc = $record['sample_type_desc'];
                $category_name = $record['category_name'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ral_cal_oic_categorywise_received_sample (sr_no, user_id, lab_name, count, ro_office, sample_type_desc, category_name, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$count', '$ro_office', '$sample_type_desc','$category_name','$date')");

                $update = $con->execute("UPDATE temp_reportico_ral_cal_oic_categorywise_received_sample SET counts = (SELECT COUNT(user_id) FROM temp_reportico_ral_cal_oic_categorywise_received_sample WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getRalCalOicCommodityConsolidatedReport($month, $posted_ro_office, $sample_type, $commodity, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ral_cal_oic_commodity_consolidated WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT commodity_name, 
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS bf_count
                FROM code_decode AS cd
                INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                WHERE cd.lab_code='$ral_lab_no' AND cd.status_flag='G' AND EXTRACT(MONTH
                FROM si.received_date):: INTEGER = '$month' AND si.sample_type_code='$sample_type' AND si.commodity_code='$commodity'
                ) AS bf_count,
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS analyzed_count
                FROM code_decode AS cd
                INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                WHERE cd.lab_code='$ral_lab_no' AND cd.status_flag='G' AND EXTRACT(MONTH
                FROM si.received_date):: INTEGER = '$month' AND si.sample_type_code='$sample_type' AND si.commodity_code='$commodity'
                ) AS analyzed_count,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users as u 
                INNER JOIN dmi_ro_offices as o On u.posted_ro_office=o.id
                Inner Join dmi_user_roles as r on r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name

                FROM m_commodity
                WHERE commodity_code='$commodity'
                ");
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $commodity_name = $record['commodity_name'];
                $bf_count = $record['bf_count'];
                $analyzed_count = $record['analyzed_count'];
                $carried_for = $bf_count - $analyzed_count;
                $date = date("d/m/Y");
                $month = date("F", mktime(0, 0, 0, $month, 10));
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ral_cal_oic_commodity_consolidated (sr_no, user_id, lab_name, commodity_name, bf_count, analyzed_count, carried_for, month, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$commodity_name', '$bf_count','$analyzed_count', '$carried_for', '$month','$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getRalCalOicTimeTakenAnalysisSample($from_date, $to_date, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ral_cal_oic_timetaken_analysis WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT si.stage_sample_code,mc.commodity_name,si.received_date,si.dispatch_date,si.dispatch_date:: DATE -si.received_date:: DATE AS time_taken,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name

                FROM sample_inward AS si
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN dmi_users AS du ON du.id=si.user_code
                INNER JOIN user_role AS ur ON ur.role_name=du.role AND ur.role_name IN('RO Officer','SO Officer') AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date'
                GROUP BY si.stage_sample_code,mc.commodity_name,si.received_date,si.dispatch_date
                ORDER BY si.received_date ASC");
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $stage_sample_code = $record['stage_sample_code'];
                $commodity_name = $record['commodity_name'];
                $received_date = $record['received_date'];
                $dispatch_date = $record['dispatch_date'];
                $time_taken = $record['time_taken'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ral_cal_oic_timetaken_analysis (sr_no, user_id, lab_name, stage_sample_code, commodity_name, received_date, dispatch_date, time_taken, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$stage_sample_code', '$commodity_name', '$received_date','$dispatch_date','$time_taken','$date')");

                $update = $con->execute("UPDATE temp_reportico_ral_cal_oic_timetaken_analysis SET counts = (SELECT COUNT(user_id) FROM temp_reportico_ral_cal_oic_timetaken_analysis WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getRalCalOicCommodityCheckChallengedSample($from_date, $to_date, $commodity, $ral_lab_no, $ral_lab_name, $posted_ro_office)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ral_cal_oic_commodity_check_challenged WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT commodity_name, 
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS bf_count
                FROM code_decode AS cd
                INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                WHERE cd.lab_code='$ral_lab_no' AND cd.status_flag NOT IN('G') AND si.sample_type_code In ('1','4') AND DATE(si.received_date) < '$from_date' AND si.commodity_code='$commodity'
                ) AS bf_count,
                (
                SELECT COUNT(DISTINCT(sa.sample_code)) AS received_count
                FROM m_sample_allocate AS sa
                INNER JOIN sample_inward AS si ON si.org_sample_code=sa.org_sample_code
                WHERE sa.lab_code='$ral_lab_no' AND si.sample_type_code In ('1','4') AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date' AND si.commodity_code='$commodity'
                ) AS received_count,
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS pass_count
                FROM code_decode AS cd
                INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                WHERE cd.lab_code='$ral_lab_no' AND si.remark='Pass' AND si.sample_type_code In ('1','4') AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date' AND si.commodity_code='$commodity'
                ) AS pass_count,
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS fail_count
                FROM code_decode AS cd
                INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                WHERE cd.lab_code='$ral_lab_no' AND si.remark='Fail' AND si.sample_type_code In ('1','4') AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date' AND si.commodity_code=$commodity
                ) AS fail_count,
                (
                Select CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                from dmi_users as u 
                Inner Join dmi_ro_offices as o On u.posted_ro_office=o.id
                Inner Join dmi_user_roles as r on r.user_email_id=u.email
                and r.user_flag='$ral_lab_name'
                where u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name

                FROM m_commodity where commodity_code='$commodity'
        ");
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $commodity_name = $record['commodity_name'];
                $bf_count = $record['bf_count'];
                $received_count = $record['received_count'];
                $total = $bf_count + $received_count;
                $pass_count = $record['pass_count'];
                $fail_count = $record['fail_count'];
                $total_analysis = $pass_count + $fail_count;
                $cf_total = $total - $total_analysis;
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ral_cal_oic_commodity_check_challenged (sr_no, user_id, lab_name, commodity_name, bf_count, received_count, total, pass_count, fail_count, total_analysis, cf_total, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$commodity_name', '$bf_count','$received_count', '$total', '$pass_count', '$fail_count', '$total_analysis', '$cf_total','$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getRalCalOicCommodityWisePrivateAnalysis($lab, $posted_ro_office, $commodity, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ral_cal_oic_commoditywise_private_analysis WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT si.fin_year,mc.commodity_name, COUNT(si.sample_type_code) AS sample_count,ml.ro_office, '$lab' AS lab_name
                FROM code_decode AS cd
                INNER JOIN sample_inward AS si ON cd.org_sample_code=si.org_sample_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN dmi_ro_offices AS ml ON si.loc_id=ml.id
                INNER JOIN dmi_users AS u ON u.id=si.user_code AND si.display='Y' AND si.sample_type_code='2' AND si.commodity_code='$commodity' AND cd.lab_code='$ral_lab_no'
                GROUP BY si.fin_year,mc.commodity_name,ml.ro_office
                ORDER BY commodity_name ASC");
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab = $record['lab_name'];
                $ro_office = $record['ro_office'];
                $lab_name = $lab . ', ' . $ro_office;
                $fin_year = $record['fin_year'];
                $commodity_name = $record['commodity_name'];
                $sample_count = $record['sample_count'];
                $ro_office = $record['ro_office'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ral_cal_oic_commoditywise_private_analysis (sr_no, user_id, lab_name, fin_year, commodity_name, sample_count, ro_office, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$fin_year', '$commodity_name', '$sample_count','$ro_office','$date')");

                $update = $con->execute("UPDATE temp_reportico_ral_cal_oic_commoditywise_private_analysis SET counts = (SELECT COUNT(user_id) FROM temp_reportico_ral_cal_oic_commoditywise_private_analysis WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getRalCalOicAllStaticsCounts($from_date, $to_date, $commodity, $office_type)
    {
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');
        $from_date = str_replace('/', '-', $from_date);
        $to_date = str_replace('/', '-', $to_date);

        $dmiRoOffice = TableRegistry::getTableLocator()->get('DmiRoOffices');
        $workflow = TableRegistry::getTableLocator()->get('workflow');

        /********************************** Office Type  = RAL *********************************************************** */
        if ($office_type == 'RAL') {
            $delete = $con->execute("DELETE FROM temp_reportico_ral_cal_oic_all_office_statistic WHERE user_id = '$user_id'");

            //get all RAL/CAL office id
            $ral_loc_id = $dmiRoOffice->find('all', array('fields' => array('id', 'ro_office', 'office_type'), 'conditions' => array('office_type' => 'RAL', 'OR' => array('delete_status IS NULL', 'delete_status' => 'no')), 'order' => 'ro_office'))->toArray();

            $i = 0;
            $ofsc_name = array();
            $inward = array();
            $forward = array();
            $forward_to_test = array();
            $finalized = array();
            $internal = array();
            $external = array();

            $ral_ids = array();

            //getting all RAL offices tables ids to compare below
            $a = 0;
            foreach ($ral_loc_id as $each_id) {
                $ral_ids[$a] = $each_id['id'];
                $a = $a + 1;
            }

            // $q is used for index for inserting value
            $q = 0;

            //getting counts for RAL/CAL offices
            foreach ($ral_loc_id as $each_ofsc) {
                $ofsc_name[$i] = $each_ofsc['ro_office'];

                $strInward = "SELECT w.org_sample_code FROM workflow w
            INNER JOIN sample_inward si ON si.org_sample_code = w.org_sample_code";
                if (!empty($commodity)) {
                    $strInward .= " WHERE w.dst_loc_id = '" . $each_ofsc['id'] . "' AND w.stage_smpl_flag = 'AS' AND w.tran_date BETWEEN '$from_date' AND '$to_date' AND si.commodity_code = '$commodity' ";
                } else {
                    $strInward .= " WHERE w.dst_loc_id = '" . $each_ofsc['id'] . "' AND w.stage_smpl_flag = 'AS' AND w.tran_date BETWEEN '$from_date' AND '$to_date'";
                }
                $strInward .= "GROUP BY w.org_sample_code";
                $query = $con->execute($strInward);
                $recordsInward = $query->fetchAll('assoc');
                $query->closeCursor();

                $strForward = "SELECT w.org_sample_code FROM workflow w
                                INNER JOIN sample_inward si ON si.org_sample_code = w.org_sample_code";
                if (!empty($commodity)) {
                    $strForward .= " WHERE w.dst_loc_id = '" . $each_ofsc['id'] . "' AND w.stage_smpl_flag IN('OF,HF') AND w.tran_date BETWEEN '$from_date' AND '$to_date' AND si.commodity_code = '$commodity' ";
                } else {
                    $strForward .= " WHERE w.dst_loc_id = '" . $each_ofsc['id'] . "' AND w.stage_smpl_flag IN('OF,HF') AND w.tran_date BETWEEN '$from_date' AND '$to_date'";
                }
                $strForward .= "GROUP BY w.org_sample_code";
                $query = $con->execute($strForward);
                $recordsForward = $query->fetchAll('assoc');
                $query->closeCursor();

                $strForwardToTest = "SELECT w.org_sample_code FROM workflow w
                                    INNER JOIN sample_inward si ON si.org_sample_code = w.org_sample_code";
                if (!empty($commodity)) {
                    $strForwardToTest .= " WHERE w.dst_loc_id = '" . $each_ofsc['id'] . "' AND w.stage_smpl_flag = 'TA' AND w.tran_date BETWEEN '$from_date' AND '$to_date' AND si.commodity_code = '$commodity' ";
                } else {
                    $strForwardToTest .= " WHERE w.dst_loc_id = '" . $each_ofsc['id'] . "' AND w.stage_smpl_flag= 'TA' AND w.tran_date BETWEEN '$from_date' AND '$to_date'";
                }
                $strForwardToTest .= "GROUP BY w.org_sample_code";
                $query = $con->execute($strForwardToTest);
                $recordsForwardToTest = $query->fetchAll('assoc');
                $query->closeCursor();

                $strFinalized = "SELECT w.org_sample_code FROM workflow w
                                INNER JOIN sample_inward si ON si.org_sample_code = w.org_sample_code";
                if (!empty($commodity)) {
                    $strFinalized .= " WHERE w.dst_loc_id = '" . $each_ofsc['id'] . "' AND w.stage_smpl_flag = 'FG' AND w.tran_date BETWEEN '$from_date' AND '$to_date' AND si.commodity_code = '$commodity' ";
                } else {
                    $strFinalized .= " WHERE w.dst_loc_id = '" . $each_ofsc['id'] . "' AND w.stage_smpl_flag= 'FG' AND w.tran_date BETWEEN '$from_date' AND '$to_date'";
                }
                $strFinalized .= "GROUP BY w.org_sample_code";
                $query = $con->execute($strFinalized);
                $recordsFinalized = $query->fetchAll('assoc');
                $query->closeCursor();

                //this below code used to differentiate the finalized sample 
                //(Inwarded internally OR Inwaarded by RO/SO office)
                $int = 0;
                $ext = 0;

                if (!empty($recordsFinalized)) {
                    foreach ($recordsFinalized as $each_f) {
                        $query = $con->execute("SELECT w.src_loc_id FROM workflow w WHERE w.org_sample_code = '" . $each_f['org_sample_code'] . "' AND w.stage_smpl_flag IN ('OF,HF,SI')  ");
                        $get_details = $query->fetchAll('assoc');
                        $query->closeCursor();
                        if (!empty($get_details)) {

                            $sr_loc_id = $get_details['src_loc_id'];

                            if (in_array($sr_loc_id, $ral_ids)) {

                                $int = $int + 1;
                            } else {

                                $ext = $ext + 1;
                            }
                        }
                    }
                }

                $internal[$i] = $int;
                $external[$i] = $ext;

                $i = $i + 1;

                $inward = COUNT($recordsInward);
                $forward = COUNT($recordsForward);
                $forward_to_test = COUNT($recordsForwardToTest);

                $queryCommodity = $con->execute("SELECT commodity_name FROM m_commodity WHERE commodity_code = '$commodity'");
                $recCommodities = $queryCommodity->fetchAll('assoc');
                foreach ($recCommodities as $recCommodity) {
                    $commodity_name = $recCommodity['commodity_name'];
                }

                $insert = $con->execute("INSERT INTO temp_reportico_ral_cal_oic_all_office_statistic (user_id, ofsc_name, inward, forward, forward_to_test, internal, external, commodity_name) 
                        VALUES (
                        '$user_id','$ofsc_name[$q]', '$inward', '$forward', '$forward_to_test', '$internal[$q]','$external[$q]','$commodity_name')");

                $q = $q + 1;
            }
        }

        /********************************** Office Type = RO *********************************************************** */
        if ($office_type == 'RO') {

            $delete = $con->execute("DELETE FROM temp_reportico_ral_cal_oic_ro_all_office_statistic WHERE user_id = '$user_id'");

            //get all RO/SO office id
            $query = $con->execute("SELECT id, ro_office,office_type FROM dmi_ro_offices where office_type IN ('RO','SO') AND (delete_status IS NULL OR delete_status = 'no')");
            $ro_loc_id = $query->fetchAll('assoc');
            $query->closeCursor();

            $i = 0;
            $ro_ofsc_name = array();
            $ro_inward = array();
            $ro_forward = array();
            $ro_finalized = array();

            //$k is used for index in inserting value
            $k =  0;
            //getting counts for RO/SO offices
            foreach ($ro_loc_id as $each_ofsc) {

                $ro_ofsc_name[$i] = $each_ofsc['ro_office'];

                $strRoInward = "SELECT si.org_sample_code FROM sample_inward si
                INNER JOIN workflow w ON w.org_sample_code = si.org_sample_code AND si.status_flag = 'S'";
                if (!empty($commodity)) {
                    $strRoInward .= " WHERE w.dst_loc_id = '" . $each_ofsc['id'] . "'  AND si.modified BETWEEN '$from_date' AND '$to_date' AND si.commodity_code = '$commodity' ";
                } else {
                    $strRoInward .= " WHERE w.dst_loc_id = '" . $each_ofsc['id'] . "'  AND si.modified BETWEEN '$from_date' AND '$to_date' ";
                }
                $strRoInward .= "GROUP BY si.org_sample_code";
                $query = $con->execute($strRoInward);

                $recordsRoInward = $query->fetchAll('assoc');
                $query->closeCursor();

                $strRoFoward = "SELECT w.org_sample_code FROM workflow w INNER JOIN sample_inward si ON si.org_sample_code = w.org_sample_code";

                if (!empty($commodity)) {
                    $strRoFoward .= " WHERE w.src_loc_id = '" . $each_ofsc['id'] . "' AND w.stage_smpl_flag IN ('OF,HF') AND w.tran_date BETWEEN '$from_date' AND '$to_date' AND si.commodity_code = '$commodity'";
                } else {
                    $strRoFoward .= " WHERE w.src_loc_id = '" . $each_ofsc['id'] . "' AND w.stage_smpl_flag IN ('OF,HF') AND w.tran_date BETWEEN '$from_date' AND '$to_date'";
                }
                $strRoFoward .= "GROUP BY w.org_sample_code";
                $query = $con->execute($strRoFoward);

                $recordsRoForward = $query->fetchAll('assoc');
                $query->closeCursor();

                $strAllSmplForwarded = "SELECT w.org_sample_code FROM workflow w INNER JOIN sample_inward si ON si.org_sample_code = w.org_sample_code";

                if (!empty($commodity)) { //'w.stage_smpl_flag'=>array(/*'OF','HF',*/'SI')); Removed by Shweta Apale on 15-11-2021 Condition in old Report 
                    $strAllSmplForwarded .= " WHERE w.src_loc_id = '" . $each_ofsc['id'] . "' AND w.stage_smpl_flag = 'SI' AND si.commodity_code = '$commodity'";
                } else {
                    $strAllSmplForwarded .= " WHERE w.src_loc_id = '" . $each_ofsc['id'] . "' AND w.stage_smpl_flag = 'SI'";
                }
                $strAllSmplForwarded .= "GROUP BY w.org_sample_code";
                $query = $con->execute($strAllSmplForwarded);

                $recordsALlSmplForwarded = $query->fetchAll('assoc');
                $query->closeCursor();

                $j = 0;
                $smpl_arry = array();
                foreach ($recordsALlSmplForwarded as $each) {

                    if (!in_array($each['org_sample_code'], $smpl_arry)) {
                        $smpl_arry[$j] = $each['org_sample_code'];
                        $j = $j + 1;
                    }
                }
                $smpl_arry = implode(',', $smpl_arry);
                $strRoFinalized = "SELECT w.org_sample_code FROM workflow w WHERE w.org_sample_code = '$smpl_arry' AND w.stage_smpl_flag = 'FG' AND w.tran_date BETWEEN '$from_date' AND '$to_date' GROUP BY w.org_sample_code";
                $query = $con->execute($strRoFinalized);

                $recordsRoFinalized = $query->fetchAll('assoc');
                $query->closeCursor();

                $i = $i + 1;

                $ro_inward = COUNT($recordsRoInward);
                $ro_forward = COUNT($recordsRoForward);
                $ro_finalized = COUNT($recordsRoFinalized);

                $queryCommodity = $con->execute("SELECT commodity_name FROM m_commodity WHERE commodity_code = '$commodity'");
                $recCommodities = $queryCommodity->fetchAll('assoc');
                foreach ($recCommodities as $recCommodity) {
                    $commodity_name = $recCommodity['commodity_name'];
                }
                $insert = $con->execute("INSERT INTO temp_reportico_ral_cal_oic_ro_all_office_statistic (user_id, ofsc_name, pending, forward, result, commodity_name) 
                VALUES (
                    '$user_id','$ro_ofsc_name[$k]', '$ro_inward', '$ro_forward', '$ro_finalized','$commodity_name')");

                $k = $k + 1;
            }
        }
    }

    /********************************************************************************************************************
     * Role RO/SO OIC
     ********************************************************************************************************************/
    public static function getRoSoOicRejectSample($from_date, $to_date, $posted_ro_office, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ro_so_oic_reject_sample WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT si.received_date,si.reject_date,CONCAT(du.f_name,' ',du.l_name) AS fullname,CONCAT(r.user_flag,',',mll.ro_office) AS labname,si.org_sample_code,mc.commodity_name,mst.sample_type_desc
                FROM sample_inward as si 
                JOIN (SELECT org_sample_code,max(id) AS id FROM workflow WHERE dst_loc_id='$ral_lab_no' GROUP BY(org_sample_code)) wf ON ((si.org_sample_code=wf.org_sample_code))
                JOIN workflow wff ON ((wf.id=wff.id))						
                Inner Join dmi_ro_offices AS mll ON mll.id=si.loc_id 
                Inner Join m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code 
                Inner Join dmi_users AS du ON du.id=si.user_code 
                Inner Join dmi_user_roles AS r On r.user_email_id=du.email
                Inner Join m_commodity AS mc ON mc.commodity_code=si.commodity_code And si.display='Y' AND si.acc_rej_flg='R'
                WHERE date(si.received_date) BETWEEN '$from_date' AND '$to_date'");
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $received_date = $record['received_date'];
                $reject_date = $record['reject_date'];
                $fullname = $record['fullname'];
                $labname = $record['labname'];
                $org_sample_code = $record['org_sample_code'];
                $commodity_name = $record['commodity_name'];
                $sample_type_desc = $record['sample_type_desc'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ro_so_oic_reject_sample (sr_no, user_id, received_date, reject_date, fullname, labname, org_sample_code, commodity_name, sample_type_desc, report_date) 
                VALUES (
                '$i','$user_id','$received_date', '$reject_date', '$fullname', '$labname', '$org_sample_code', '$commodity_name', '$sample_type_desc', '$date')");

                $update = $con->execute("UPDATE temp_reportico_ro_so_oic_reject_sample SET counts = (SELECT COUNT(user_id) FROM temp_reportico_ro_so_oic_reject_sample WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getRoSoOicSampleRegister($from_date, $to_date, $posted_ro_office, $lab, $commodity, $category, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ro_so_oic_sample_register WHERE user_id = '$user_id'");

        $str = "SELECT si.letr_date,mct.category_name,CONCAT(r.user_flag,',',mll.ro_office) AS labname, si.letr_ref_no, si.org_sample_code,si.stage_sample_code,si.sample_total_qnt AS sample_qnt,mu.unit_weight,mp.par_condition_desc,si.sample_total_qnt,si.received_date, mc.commodity_name,si.commodity_code, si.category_code,st.sample_type_desc,si.loc_id, si.dispatch_date,si.dispatch_date, si.grading_date,si.remark,r.user_flag,sample_total_qnt
                FROM sample_inward AS si
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN m_commodity_category AS mct ON mct.category_code=si.category_code
                INNER JOIN m_par_condition AS mp ON mp.par_condition_code=si.par_condition_code
                INNER JOIN m_unit_weight AS mu ON mu.unit_id=si.parcel_size
                INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN dmi_users AS du ON du.id=si.user_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=du.email";

        $str .= " WHERE si.display='Y' AND si.acc_rej_flg='A' AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date'";

        if ($_SESSION['user_flag'] == "RO" || $_SESSION['user_flag'] == "SO") {
            $str .= " AND si.loc_id='" . $_SESSION['posted_ro_office'] . "'";
        }
        if ($_SESSION['user_flag'] == "RO" || $_SESSION['user_flag'] == "SO") {
            $str .= " AND r.user_flag='" . $_SESSION['user_flag'] . "'";
        }

        $str .= "AND si.loc_id='$ral_lab_no'  AND r.user_flag='$ral_lab_name'  AND si.commodity_code='$commodity' AND si.category_code='$category' GROUP BY si.stage_sample_code,si.letr_date,sample_qnt,si.received_date,si.dispatch_date,si.grading_date,mll.ro_office,r.user_flag, mc.commodity_name,mct.category_name,si.commodity_code, si.category_code,st.sample_type_desc,si.loc_id,si.letr_ref_no,si.sample_total_qnt,mu.unit_weight,mp.par_condition_desc,si.dispatch_date, si.org_sample_code,si.remark
                ORDER BY received_date ASC";

        $query = $con->execute($str);
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $letr_date = $record['letr_date'];
                $category_name = $record['category_name'];
                $labname = $record['labname'];
                $letr_ref_no = $record['letr_ref_no'];
                $org_sample_code = $record['org_sample_code'];
                $stage_sample_code = $record['stage_sample_code'];
                $sample_qnt = $record['sample_qnt'];
                $unit_weight = $record['unit_weight'];
                $par_condition_desc = $record['par_condition_desc'];
                $sample_total_qnt = $record['sample_total_qnt'];
                $received_date = $record['received_date'];
                $commodity_name = $record['commodity_name'];
                $commodity_code = $record['commodity_code'];
                $category_code = $record['category_code'];
                $sample_type_desc = $record['sample_type_desc'];
                $loc_id = $record['loc_id'];
                $dispatch_date = $record['dispatch_date'];
                $grading_date = $record['grading_date'];
                $remark = $record['remark'];
                $user_flag = $record['user_flag'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ro_so_oic_sample_register (sr_no, user_id,letr_date, category_name, labname, letr_ref_no, org_sample_code, stage_sample_code, sample_qnt, unit_weight, par_condition_desc, sample_total_qnt, received_date, commodity_name, commodity_code, category_code, sample_type_desc, loc_id, dispatch_date, grading_date, remark, user_flag, report_date) 
                VALUES (
                '$i','$user_id','$letr_date', '$category_name', '$labname', '$letr_ref_no', '$org_sample_code', '$stage_sample_code', '$sample_qnt', '$unit_weight', '$par_condition_desc', '$sample_total_qnt', '$received_date', '$commodity_name', '$commodity_code', '$category_code', '$sample_type_desc', '$loc_id', '$dispatch_date', '$grading_date', '$remark', '$user_flag', '$date')");

                $update = $con->execute("UPDATE temp_reportico_ro_so_oic_sample_register SET counts = (SELECT COUNT(user_id) FROM temp_reportico_ro_so_oic_sample_register WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getRoSoOicSampleAnalyzedCount($from_date, $to_date, $posted_ro_office, $lab, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ro_so_oic_sample_analyzed WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT mll.ro_office,mst.sample_type_desc,mc.commodity_name, COUNT(mc.commodity_name) AS count_samples, COUNT(CASE WHEN si.status_flag = 'FG' THEN 1 END) AS finalized
                FROM sample_inward AS si
                INNER JOIN m_commodity_category AS mcc ON si.category_code=mcc.category_code
                INNER JOIN m_commodity AS mc ON si.commodity_code=mc.commodity_code
                INNER JOIN m_sample_type AS mst ON si.sample_type_code=mst.sample_type_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN dmi_users AS u ON u.id=si.user_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND si.display='Y' AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date'  AND si.loc_id='$ral_lab_no' AND r.user_flag='$ral_lab_name'
                GROUP BY mll.ro_office,mst.sample_type_desc,mc.commodity_name
                ORDER BY mc.commodity_name ASC");
        $records = $query->fetchAll('assoc');
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $ro_office = $record['ro_office'];
                $sample_type_desc = $record['sample_type_desc'];
                $commodity_name = $record['commodity_name'];
                $count_samples = $record['count_samples'];
                $finalized = $record['finalized'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ro_so_oic_sample_analyzed (sr_no, user_id,ro_office, sample_type_desc, commodity_name, count_samples, finalized, report_date) 
                VALUES (
                '$i','$user_id','$ro_office', '$sample_type_desc', '$commodity_name', '$count_samples', '$finalized','$date')");

                $update = $con->execute("UPDATE temp_reportico_ro_so_oic_sample_analyzed SET counts = (SELECT COUNT(user_id) FROM temp_reportico_ro_so_oic_sample_analyzed WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getRoSoOicSampleRegistration($from_date, $to_date, $posted_ro_office, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ro_so_oic_sample_registration WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT mc.commodity_name,si.grade,CONCAT(sd.pckr_nm,', ',pckr_addr) AS name_address_packer,sd.lot_no,si.received_date AS pack_date,sd.pack_size,sd.tbl,CONCAT(sd.shop_name,', ',sd.shop_address) AS shop_name_address,si.parcel_size,sd.smpl_drwl_dt,CONCAT(u.f_name,' ',u.l_name) AS name_officer,si.org_sample_code,si.dispatch_date,CONCAT(r.user_flag,', ',mll.ro_office) AS lab_or_office_name
                FROM sample_inward AS si
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN dmi_users AS u ON u.id=si.user_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN sample_inward_details AS sd ON sd.org_sample_code=si.stage_sample_code AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date' AND si.loc_id='$ral_lab_no' AND r.user_flag='$ral_lab_name'
                GROUP BY mll.ro_office,r.user_flag,si.grading_date,si.remark,si.grade,u.f_name,u.l_name,mc.commodity_name,si.grade,sd.pckr_nm,pckr_addr,si.received_date,sd.lot_no,sd.pack_size,sd.tbl,sd.shop_name,sd.shop_address,si.parcel_size,sd.smpl_drwl_dt, si.org_sample_code,si.dispatch_date,si.ral_lab_code,si.ral_anltc_rslt_rcpt_dt,si.anltc_rslt_chlng_flg,si.misgrd_param_value,si.misgrd_report_issue_dt, si.misgrd_reason,si.chlng_smpl_disptch_cal_dt,si.cal_anltc_rslt_rcpt_dt
                ORDER BY sd.smpl_drwl_dt DESC");
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $commodity_name = $record['commodity_name'];
                $grade = $record['grade'];
                $name_address_packer = $record['name_address_packer'];
                $lot_no = $record['lot_no'];
                $pack_date = $record['pack_date'];
                $pack_size = $record['pack_size'];
                $tbl = $record['tbl'];
                $shop_name_address = $record['shop_name_address'];
                $parcel_size = $record['parcel_size'];
                $smpl_drwl_dt = $record['smpl_drwl_dt'];
                $name_officer = $record['name_officer'];
                $org_sample_code = $record['org_sample_code'];
                $dispatch_date = $record['dispatch_date'];
                $lab_or_office_name = $record['lab_or_office_name'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ro_so_oic_sample_registration (sr_no, user_id, commodity_name, grade, name_address_packer, lot_no, pack_date, pack_size, tbl, shop_name_address, parcel_size, smpl_drwl_dt, name_officer, org_sample_code, dispatch_date, lab_or_office_name, report_date) 
                VALUES (
                '$i','$user_id','$commodity_name', '$grade', '$name_address_packer', '$lot_no', '$pack_date', '$pack_size', '$tbl', '$shop_name_address', '$parcel_size', '$smpl_drwl_dt', '$name_officer', '$org_sample_code', '$dispatch_date', '$lab_or_office_name', '$date')");

                $update = $con->execute("UPDATE temp_reportico_ro_so_oic_sample_registration SET counts = (SELECT COUNT(user_id) FROM temp_reportico_ro_so_oic_sample_registration WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    /********************************************************************************************************************
     * Role Jr Chemist
     ********************************************************************************************************************/

    public static function getJrReTestedSample($from_date, $to_date)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_jr_retested_sample WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT msa.sample_code AS org_sample_code,mc.commodity_name,mst.sample_type_desc,si.received_date,ml.ro_office,CONCAT(du.f_name,' ',du.l_name) AS full_name
                FROM sample_inward si
                INNER JOIN m_sample_allocate AS msa ON msa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_ro_offices AS ml ON ml.id=si.loc_id
                INNER JOIN dmi_users AS du ON du.id=si.user_code
                INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code 
                where DATE(si.received_date) BETWEEN '$from_date' AND '$to_date' aND si.display='Y' AND msa.display='Y' AND msa.test_n_r='R' 
                GROUP BY msa.sample_code,mc.commodity_name,mst.sample_type_desc,si.received_date,ml.ro_office,du.f_name,du.l_name");
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $org_sample_code = $record['org_sample_code'];
                $commodity_name = $record['commodity_name'];
                $sample_type_desc = $record['sample_type_desc'];
                $received_date = $record['received_date'];
                $ro_office = $record['ro_office'];
                $full_name = $record['full_name'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_jr_retested_sample (sr_no , user_id, org_sample_code, commodity_name, sample_type_desc, received_date, ro_office, full_name, report_date) 
                VALUES (
                '$i','$user_id','$org_sample_code', '$commodity_name', '$sample_type_desc', '$received_date','$ro_office','$full_name','$date')");

                $update = $con->execute("UPDATE temp_reportico_jr_retested_sample SET counts = (SELECT COUNT(user_id) FROM temp_reportico_jr_retested_sample WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getJrReTestedSampleByChemist($from_date, $to_date)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_jr_retested_sample_submit WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT  msa.sample_code AS org_sample_code,mc.commodity_name,mst.sample_type_desc,si.received_date,ml.lab_name AS lab,CONCAT(du.f_name,' ',du.l_name) AS full_name
                FROM sample_inward AS si
                INNER JOIN m_lab AS ml ON ml.lab_code=si.loc_id
                INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code
                INNER JOIN dmi_users AS du ON du.id=si.user_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=du.email
                INNER JOIN m_sample_allocate AS msa ON msa.org_sample_code=si.org_sample_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code 
                WHERE si.display='Y' AND msa.display='Y' AND msa.test_n_r='R' AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date'
                GROUP BY msa.sample_code,mc.commodity_name,mst.sample_type_desc,si.received_date,ml.lab_name,du.f_name,du.l_name");
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $org_sample_code = $record['org_sample_code'];
                $commodity_name = $record['commodity_name'];
                $sample_type_desc = $record['sample_type_desc'];
                $received_date = $record['received_date'];
                $full_name = $record['full_name'];
                $lab = $record['lab'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_jr_retested_sample_submit (sr_no, user_id, org_sample_code, commodity_name, sample_type_desc, received_date, full_name, lab, report_date) 
                VALUES (
                '$i','$user_id', '$org_sample_code', '$commodity_name', '$sample_type_desc', '$received_date','$full_name','$lab','$date')");

                $update = $con->execute("UPDATE temp_reportico_jr_retested_sample_submit SET counts = (SELECT COUNT(user_id) FROM temp_reportico_jr_retested_sample_submit WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getJrTestSubmitByChemist($from_date, $to_date, $chemist_code)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_jr_test_submit_by_chemist WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT recby_ch_date,sa.chemist_code,sa.sample_code,c.commodity_name,CONCAT(du.f_name,' ',du.l_name,' (',du.role,') ') AS chemist_name,t.test_name,atd.result,'$chemist_code'AS chemist_code
                FROM m_sample_allocate sa
                INNER JOIN actual_test_data AS atd ON sa.chemist_code=atd.chemist_code
                INNER JOIN code_decode AS cd ON cd.org_sample_code=sa.org_sample_code
                INNER JOIN m_test AS t ON atd.test_code=t.test_code
                INNER JOIN m_commodity AS c ON c.commodity_code=sa.commodity_code
                INNER JOIN dmi_users AS du ON du.id=sa.alloc_to_user_code AND DATE(sa.alloc_date) BETWEEN '$from_date' AND '$to_date' AND sa.chemist_code='$chemist_code'
                WHERE atd.chemist_code='$chemist_code' AND cd.status_flag in('C','G') AND sa.chemist_code='$chemist_code'
                GROUP BY du.role,sa.chemist_code,sa.recby_ch_date,sa.sample_code,c.commodity_name,du.f_name,du.l_name,t.test_name,atd.result");
        // print_r($query);exit;
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $recby_ch_date = $record['recby_ch_date'];
                $commodity_name = $record['commodity_name'];
                $sample_code = $record['sample_code'];
                $chemist_name = $record['chemist_name'];
                $test_name = $record['test_name'];
                $result = $record['result'];
                $chemist_code = $record['chemist_code'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_jr_test_submit_by_chemist (sr_no, user_id, recby_ch_date, commodity_name, sample_code, chemist_name, test_name, result, chemist_code, report_date) 
                VALUES (
                '$i','$user_id','$recby_ch_date', '$commodity_name', '$sample_code', '$chemist_name','$test_name','$result','$chemist_code','$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }


    /********************************************************************************************************************
     * Role Sr Chemist
     ********************************************************************************************************************/

    public static function getSrReTestedSample($from_date, $to_date)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_sr_retested_sample WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT  msa.sample_code AS org_sample_code,mc.commodity_name,mst.sample_type_desc,si.received_date,ml.ro_office,CONCAT(du.f_name,' ',du.l_name) AS full_name
                FROM sample_inward si
                INNER JOIN m_sample_allocate AS msa ON msa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_ro_offices AS ml ON ml.id=si.loc_id
                INNER JOIN dmi_users AS du ON du.id=si.user_code
                INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code 
                where DATE(si.received_date) BETWEEN '$from_date' AND '$to_date' aND si.display='Y' AND msa.display='Y' AND msa.test_n_r='R' 
                GROUP BY msa.sample_code,mc.commodity_name,mst.sample_type_desc,si.received_date,ml.ro_office,du.f_name,du.l_name");
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $org_sample_code = $record['org_sample_code'];
                $commodity_name = $record['commodity_name'];
                $sample_type_desc = $record['sample_type_desc'];
                $received_date = $record['received_date'];
                $ro_office = $record['ro_office'];
                $full_name = $record['full_name'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_sr_retested_sample (sr_no, user_id, org_sample_code, commodity_name, sample_type_desc, received_date, ro_office, full_name, report_date) 
                VALUES (
                '$i','$user_id','$org_sample_code', '$commodity_name', '$sample_type_desc', '$received_date','$ro_office','$full_name','$date')");

                $update = $con->execute("UPDATE temp_reportico_sr_retested_sample SET counts = (SELECT COUNT(user_id) FROM temp_reportico_sr_retested_sample WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getSrReTestedSampleByChemist($from_date, $to_date)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_sr_retested_sample_submit WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT  msa.sample_code AS org_sample_code,mc.commodity_name,mst.sample_type_desc,si.received_date,ml.lab_name AS lab,CONCAT(du.f_name,' ',du.l_name) AS full_name
                FROM sample_inward AS si
                INNER JOIN m_lab AS ml ON ml.lab_code=si.loc_id
                INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code
                INNER JOIN dmi_users AS du ON du.id=si.user_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=du.email
                INNER JOIN m_sample_allocate AS msa ON msa.org_sample_code=si.org_sample_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code 
                WHERE si.display='Y' AND msa.display='Y' AND msa.test_n_r='R' AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date'
                GROUP BY msa.sample_code,mc.commodity_name,mst.sample_type_desc,si.received_date,ml.lab_name,du.f_name,du.l_name");
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $org_sample_code = $record['org_sample_code'];
                $commodity_name = $record['commodity_name'];
                $sample_type_desc = $record['sample_type_desc'];
                $received_date = $record['received_date'];
                $full_name = $record['full_name'];
                $lab = $record['lab'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_sr_retested_sample_submit (sr_no, user_id, org_sample_code, commodity_name, sample_type_desc, received_date, full_name, lab, report_date) 
                VALUES (
                '$i','$user_id','$org_sample_code', '$commodity_name', '$sample_type_desc', '$received_date','$full_name','$lab','$date')");

                $update = $con->execute("UPDATE temp_reportico_sr_retested_sample_submit SET counts = (SELECT COUNT(user_id) FROM temp_reportico_sr_retested_sample_submit WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getSrTestSubmitByChemist($from_date, $to_date, $chemist_code)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_sr_test_submit_by_chemist WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT recby_ch_date,sa.chemist_code,sa.sample_code,c.commodity_name,CONCAT(du.f_name,' ',du.l_name,' (',du.role,') ') AS chemist_name,t.test_name,atd.result,'$chemist_code'AS chemist_code
                FROM m_sample_allocate sa
                INNER JOIN actual_test_data AS atd ON sa.chemist_code=atd.chemist_code
                INNER JOIN code_decode AS cd ON cd.org_sample_code=sa.org_sample_code
                INNER JOIN m_test AS t ON atd.test_code=t.test_code
                INNER JOIN m_commodity AS c ON c.commodity_code=sa.commodity_code
                INNER JOIN dmi_users AS du ON du.id=sa.alloc_to_user_code AND DATE(sa.alloc_date) BETWEEN '$from_date' AND '$to_date' AND sa.chemist_code='$chemist_code'
                WHERE atd.chemist_code='$chemist_code' AND cd.status_flag in('C','G') AND sa.chemist_code='$chemist_code'
                GROUP BY du.role,sa.chemist_code,sa.recby_ch_date,sa.sample_code,c.commodity_name,du.f_name,du.l_name,t.test_name,atd.result");
        // print_r($query);exit;
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $recby_ch_date = $record['recby_ch_date'];
                $commodity_name = $record['commodity_name'];
                $sample_code = $record['sample_code'];
                $chemist_name = $record['chemist_name'];
                $test_name = $record['test_name'];
                $result = $record['result'];
                $chemist_code = $record['chemist_code'];
                $date = date("d/m/Y");

                $insert = $con->execute("INSERT INTO temp_reportico_sr_test_submit_by_chemist (sr_no, user_id, recby_ch_date, commodity_name, sample_code, chemist_name, test_name, result, chemist_code, report_date) 
                VALUES (
                '$i','$user_id','$recby_ch_date', '$commodity_name', '$sample_code', '$chemist_name','$test_name','$result','$chemist_code','$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }

    /********************************************************************************************************************
     * Role Lab Incharge
     ********************************************************************************************************************/
    public static function getLabIncahrgeCodingDecodingSection($from_date, $to_date, $posted_ro_office, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_lab_incharge_coding_decoding WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT mll.ro_office,mc.commodity_name,si.stage_sample_code,w.tran_date,si.received_date,si.sample_total_qnt,muw.unit_weight,mst.sample_type_desc,si.remark
                FROM sample_inward AS si
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN m_unit_weight AS muw ON muw.unit_id=si.parcel_size
                INNER JOIN dmi_users AS u ON u.id=si.user_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN workflow AS w ON w.org_sample_code=si.org_sample_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code AND w.stage_smpl_flag In ('OF','HF','FG') AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date' AND w.dst_loc_id='$ral_lab_no'
                GROUP BY mll.ro_office,mc.commodity_name,w.tran_date,si.stage_sample_code,si.received_date,si.sample_total_qnt,muw.unit_weight,mst.sample_type_desc,si.remark
                ORDER BY mc.commodity_name ASC");
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $ro_office = $record['ro_office'];
                $commodity_name = $record['commodity_name'];
                $stage_sample_code = $record['stage_sample_code'];
                $tran_date = $record['tran_date'];
                $sample_total_qnt = $record['sample_total_qnt'];
                $unit_weight = $record['unit_weight'];
                $sample_qnt = $sample_total_qnt . ' ' . $unit_weight;
                $sample_type_desc = $record['sample_type_desc'];
                $remark = $record['remark'];
                $received_date = $record['received_date'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_lab_incharge_coding_decoding (sr_no, user_id, ro_office, commodity_name, stage_sample_code, received_date, tran_date, sample_qnt, sample_type_desc, remark, report_date) 
                VALUES (
                '$i','$user_id','$ro_office', '$commodity_name', '$stage_sample_code','$received_date', '$tran_date', '$sample_qnt','$sample_type_desc','$remark','$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getLabInchargeSampleAllotedToChemistForRetesting($from_date, $to_date, $posted_ro_office, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_lab_incharge_sample_alloted_chemist_retesting WHERE user_id = '$user_id'");

        $str = "SELECT mll.ro_office,msa.org_sample_code,mc.commodity_name,si.received_date,mst.sample_type_desc,CONCAT(du.f_name,' ',du.l_name) AS chemist_name,msa.alloc_date
                FROM m_sample_allocate AS msa
                INNER JOIN sample_inward AS si ON si.org_sample_code=msa.org_sample_code
                INNER JOIN m_commodity AS mc ON si.commodity_code=mc.commodity_code
                INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN dmi_users AS du ON du.id=si.user_code AND msa.display='Y' AND msa.test_n_r='R' AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date'";

        if ($_SESSION['user_flag'] == "RAL") {
            $str .= " AND msa.lab_code='" . $_SESSION['posted_ro_office'] . "'";
        } else {
            $str .= " AND msa.lab_code='$ral_lab_no'";
        }

        $str .= " ORDER BY si.received_date ASC";

        $query = $con->execute($str);
        //  print_r($query);exit;
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $org_sample_code = $record['org_sample_code'];
                $commodity_name = $record['commodity_name'];
                $chemist_name = $record['chemist_name'];
                $received_date = $record['received_date'];
                $org_sample_code = $record['org_sample_code'];
                $ro_office = $record['ro_office'];
                $sample_type_desc = $record['sample_type_desc'];
                $alloc_date = $record['alloc_date'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_lab_incharge_sample_alloted_chemist_retesting (sr_no, user_id, ro_office, chemist_name, received_date, org_sample_code, commodity_name, sample_type_desc, alloc_date, report_date) 
                VALUES (
                '$i','$user_id','$ro_office', '$chemist_name', '$received_date', '$org_sample_code', '$commodity_name', '$sample_type_desc','$alloc_date','$date')");

                $update = $con->execute("UPDATE temp_reportico_lab_incharge_sample_alloted_chemist_retesting SET counts = (SELECT COUNT(user_id) FROM temp_reportico_lab_incharge_sample_alloted_chemist_retesting WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getLabInchargeSampleAllotedToChemistForTesting($from_date, $to_date, $posted_ro_office, $lab, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_lab_incharge_sample_alloted_chemist_testing WHERE user_id = '$user_id'");

        $str = "SELECT si.org_sample_code,mc.commodity_name,si.received_date,mst.sample_type_desc,mll.ro_office,r.user_flag,CONCAT(du.f_name,' ',du.l_name,' (',du.role,')') AS chemist_name,msa.alloc_date
                FROM sample_inward AS si
                INNER JOIN code_decode AS cd ON cd.org_sample_code=si.org_sample_code
                INNER JOIN m_sample_allocate AS msa ON msa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN m_commodity AS mc ON si.commodity_code=mc.commodity_code
                INNER JOIN dmi_users AS du ON du.id=cd.alloc_to_user_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=du.email
                INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code AND cd.display='Y' AND cd.chemist_code!='-' AND DATE(msa.alloc_date) BETWEEN '$from_date' AND '$to_date' ";

        if ($_SESSION['user_flag'] == "RAL") {
            $str .= " AND cd.lab_code='" . $_SESSION['posted_ro_office'] . "'";
        } else {
            $str .= " AND cd.lab_code='$ral_lab_no'";
        }

        $str .= " ORDER BY si.received_date ASC";

        $query = $con->execute($str);

        // print_r($query);exit;
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $org_sample_code = $record['org_sample_code'];
                $commodity_name = $record['commodity_name'];
                $chemist_name = $record['chemist_name'];
                $received_date = $record['received_date'];
                $org_sample_code = $record['org_sample_code'];
                $ro_office = $record['ro_office'];
                $sample_type_desc = $record['sample_type_desc'];
                $user_flag = $record['user_flag'];
                $alloc_date = $record['alloc_date'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_lab_incharge_sample_alloted_chemist_testing (sr_no, user_id, ro_office, chemist_name, received_date, org_sample_code, commodity_name, sample_type_desc, user_flag, alloc_date, report_date) 
                VALUES (
                '$i','$user_id','$ro_office', '$chemist_name', '$received_date', '$org_sample_code', '$commodity_name', '$sample_type_desc', '$user_flag','$alloc_date','$date')");

                $update = $con->execute("UPDATE temp_reportico_lab_incharge_sample_alloted_chemist_testing SET counts = (SELECT COUNT(user_id) FROM temp_reportico_lab_incharge_sample_alloted_chemist_testing WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    /********************************************************************************************************************
     * Role DOL
     ********************************************************************************************************************/
    public static function getDolSampleRegistration($from_date, $to_date, $posted_ro_office, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_dol_sample_registration WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT mc.commodity_name,si.grade,CONCAT(sd.pckr_nm,', ',pckr_addr) AS name_address_packer,sd.lot_no,si.received_date AS pack_date,sd.pack_size,sd.tbl,CONCAT(sd.shop_name,', ',sd.shop_address) AS shop_name_address,si.parcel_size,sd.smpl_drwl_dt,CONCAT(u.f_name,' ',u.l_name) AS name_officer,si.org_sample_code,si.dispatch_date,CONCAT(r.user_flag,', ',mll.ro_office) AS lab_or_office_name
                FROM sample_inward AS si
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN dmi_users AS u ON u.id=si.user_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN sample_inward_details AS sd ON sd.org_sample_code=si.stage_sample_code AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date' AND si.loc_id='$ral_lab_no' AND r.user_flag='$ral_lab_no'
                GROUP BY mll.ro_office,r.user_flag,si.grading_date,si.remark,si.grade,u.f_name,u.l_name,mc.commodity_name,si.grade,sd.pckr_nm,pckr_addr,si.received_date,sd.lot_no,sd.pack_size,sd.tbl,sd.shop_name,sd.shop_address,si.parcel_size,sd.smpl_drwl_dt, si.org_sample_code,si.dispatch_date,si.ral_lab_code,si.ral_anltc_rslt_rcpt_dt,si.anltc_rslt_chlng_flg,si.misgrd_param_value,si.misgrd_report_issue_dt, si.misgrd_reason,si.chlng_smpl_disptch_cal_dt,si.cal_anltc_rslt_rcpt_dt
                ORDER BY sd.smpl_drwl_dt DESC");
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $commodity_name = $record['commodity_name'];
                $grade = $record['grade'];
                $name_address_packer = $record['name_address_packer'];
                $lot_no = $record['lot_no'];
                $pack_date = $record['pack_date'];
                $pack_size = $record['pack_size'];
                $tbl = $record['tbl'];
                $shop_name_address = $record['shop_name_address'];
                $parcel_size = $record['parcel_size'];
                $smpl_drwl_dt = $record['smpl_drwl_dt'];
                $name_officer = $record['name_officer'];
                $org_sample_code = $record['org_sample_code'];
                $dispatch_date = $record['dispatch_date'];
                $lab_or_office_name = $record['lab_or_office_name'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_dol_sample_registration (sr_no, user_id, commodity_name, grade, name_address_packer, lot_no, pack_date, pack_size, tbl, shop_name_address, parcel_size, smpl_drwl_dt, name_officer, org_sample_code, dispatch_date, lab_or_office_name, report_date) 
                VALUES (
                '$i','$user_id','$commodity_name', '$grade', '$name_address_packer', '$lot_no', '$pack_date', '$pack_size', '$tbl', '$shop_name_address', '$parcel_size', '$smpl_drwl_dt', '$name_officer', '$org_sample_code', '$dispatch_date', '$lab_or_office_name', '$date')");

                $update = $con->execute("UPDATE temp_reportico_dol_sample_registration SET counts = (SELECT COUNT(user_id) FROM temp_reportico_dol_sample_registration WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getDolSampleRegister($from_date, $to_date, $posted_ro_office, $lab, $commodity, $sample, $category, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_dol_sample_register WHERE user_id = '$user_id'");

        $str = "SELECT si.letr_date,mct.category_name,CONCAT(r.user_flag,',',mll.ro_office) AS labname, si.letr_ref_no, si.org_sample_code,si.stage_sample_code,si.sample_total_qnt AS sample_qnt,mu.unit_weight,mp.par_condition_desc,si.sample_total_qnt,si.received_date, mc.commodity_name,si.commodity_code, si.category_code,st.sample_type_desc,si.loc_id, si.dispatch_date,si.dispatch_date, si.grading_date,si.remark,r.user_flag,sample_total_qnt
                FROM sample_inward AS si
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN m_commodity_category AS mct ON mct.category_code=si.category_code
                INNER JOIN m_par_condition AS mp ON mp.par_condition_code=si.par_condition_code
                INNER JOIN m_unit_weight AS mu ON mu.unit_id=si.parcel_size
                INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN dmi_users AS du ON du.id=si.user_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=du.email";

        $str .= " WHERE si.display='Y' AND si.acc_rej_flg='A' AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date'";

        if ($_SESSION['user_flag'] == "RO" || $_SESSION['user_flag'] == "SO") {
            $str .= " AND si.loc_id='" . $_SESSION['posted_ro_office'] . "'";
        }
        if ($_SESSION['user_flag'] == "RO" || $_SESSION['user_flag'] == "SO") {
            $str .= " AND r.user_flag='" . $_SESSION['user_flag'] . "'";
        }

        $str .= "AND si.loc_id='$ral_lab_no'  AND r.user_flag='$ral_lab_name' AND si.sample_type_code = '$sample' AND si.commodity_code='$commodity' AND si.category_code='$category' GROUP BY si.stage_sample_code,si.letr_date,sample_qnt,si.received_date,si.dispatch_date,si.grading_date,mll.ro_office,r.user_flag, mc.commodity_name,mct.category_name,si.commodity_code, si.category_code,st.sample_type_desc,si.loc_id,si.letr_ref_no,si.sample_total_qnt,mu.unit_weight,mp.par_condition_desc,si.dispatch_date, si.org_sample_code,si.remark
                ORDER BY received_date ASC";

        $query = $con->execute($str);
        // print_r($query);exit;
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $letr_date = $record['letr_date'];
                $category_name = $record['category_name'];
                $labname = $record['labname'];
                $letr_ref_no = $record['letr_ref_no'];
                $org_sample_code = $record['org_sample_code'];
                $stage_sample_code = $record['stage_sample_code'];
                $sample_qnt = $record['sample_qnt'];
                $unit_weight = $record['unit_weight'];
                $par_condition_desc = $record['par_condition_desc'];
                $sample_total_qnt = $record['sample_total_qnt'];
                $received_date = $record['received_date'];
                $commodity_name = $record['commodity_name'];
                $commodity_code = $record['commodity_code'];
                $category_code = $record['category_code'];
                $sample_type_desc = $record['sample_type_desc'];
                $loc_id = $record['loc_id'];
                $dispatch_date = $record['dispatch_date'];
                $grading_date = $record['grading_date'];
                $remark = $record['remark'];
                $user_flag = $record['user_flag'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_dol_sample_register (sr_no, user_id, letr_date, category_name, labname, letr_ref_no, org_sample_code, stage_sample_code, sample_qnt, unit_weight, par_condition_desc, sample_total_qnt, received_date, commodity_name, commodity_code, category_code, sample_type_desc, loc_id, dispatch_date, grading_date, remark, user_flag, report_date) 
                VALUES (
                '$i','$user_id','$letr_date', '$category_name', '$labname', '$letr_ref_no', '$org_sample_code', '$stage_sample_code', '$sample_qnt', '$unit_weight', '$par_condition_desc', '$sample_total_qnt', '$received_date', '$commodity_name', '$commodity_code', '$category_code', '$sample_type_desc', '$loc_id', '$dispatch_date', '$grading_date', '$remark', '$user_flag', '$date')");

                $update = $con->execute("UPDATE temp_reportico_dol_sample_register SET counts = (SELECT COUNT(user_id) FROM temp_reportico_dol_sample_register WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getDolSampleReceivedRoSoRalCal($from_date, $to_date, $commodity, $posted_ro_office, $lab, $role, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_dol_sample_received_rosoralcal WHERE user_id = '$user_id'");

        $sql = "SELECT si.received_date,si.org_sample_code,mct.category_name,mc.commodity_name,mst.sample_type_desc,du.role,mll.ro_office,r.user_flag,CONCAT('$ral_lab_name',', ',mll.ro_office) AS lab_name
		        FROM sample_inward as si 
				INNER JOIN m_commodity_category AS mct ON mct.category_code=si.category_code
				INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id 
				INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code 
				INNER JOIN dmi_users AS du On du.id=si.user_code
				INNER JOIN dmi_user_roles AS r On r.user_email_id=du.email
				INNER JOIN workflow AS w On w.org_sample_code=si.org_sample_code
				INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code And si.display='Y'";

        if ($from_date != '' && $to_date != '') {
            $sql .= " WHERE date(si.received_date) BETWEEN '$from_date' and '$to_date'";
        }
        if ($commodity != '') {
            $sql .= " AND si.commodity_code='$commodity'";
        }
        if ($lab == "RO" || $lab == "SO") {
            $sql .= " AND si.loc_id='" . $posted_ro_office . "'";
        } else if ($lab == "RAL" && $role == "Inward Officer") {
            $sql .= " AND w.dst_loc_id='" . $posted_ro_office . "'";
        } else if ($lab == "CAL" && $role == "Inward Officer") {
            $sql .= " AND w.dst_loc_id='" . $posted_ro_office . "'";
        }
        $sql .= " Group By du.role,si.org_sample_code,mct.category_name,mc.commodity_name,mst.sample_type_desc,si.received_date,mll.ro_office,r.user_flag,du.f_name,du.l_name";
        $sql .= " ORDER BY si.received_date asc";

        $query = $con->execute($sql);
        $records = $query->fetchAll('assoc');
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $received_date = $record['received_date'];
                $org_sample_code = $record['org_sample_code'];
                $category_name = $record['category_name'];
                $commodity_name = $record['commodity_name'];
                $sample_type_desc = $record['sample_type_desc'];
                $role = $record['role'];
                $ro_office = $record['ro_office'];
                $user_flag = $record['user_flag'];
                $lab_name = $record['lab_name'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_dol_sample_received_rosoralcal (sr_no, user_id, received_date, org_sample_code, category_name, commodity_name, sample_type_desc, role, ro_office, user_flag, lab_name, report_date) 
                VALUES (
                '$i','$user_id','$received_date', '$org_sample_code', '$category_name', '$commodity_name', '$sample_type_desc', '$role', '$ro_office', '$user_flag', '$lab_name','$date')");

                $update = $con->execute("UPDATE temp_reportico_dol_sample_received_rosoralcal SET counts = (SELECT COUNT(user_id) FROM temp_reportico_dol_sample_received_rosoralcal WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getDolSampleAnalyzedCount($from_date, $to_date, $posted_ro_office, $lab, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_dol_sample_analyzed WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT mll.ro_office,mst.sample_type_desc,mc.commodity_name, COUNT(mc.commodity_name) AS count_samples, COUNT(CASE WHEN si.status_flag = 'FG' THEN 1 END) AS finalized
                FROM sample_inward AS si
                INNER JOIN m_commodity_category AS mcc ON si.category_code=mcc.category_code
                INNER JOIN m_commodity AS mc ON si.commodity_code=mc.commodity_code
                INNER JOIN m_sample_type AS mst ON si.sample_type_code=mst.sample_type_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN dmi_users AS u ON u.id=si.user_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND si.display='Y' AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date'  AND si.loc_id='$ral_lab_no' AND r.user_flag='$ral_lab_name'
                GROUP BY mll.ro_office,mst.sample_type_desc,mc.commodity_name
                ORDER BY mc.commodity_name ASC");
        $records = $query->fetchAll('assoc');
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $ro_office = $record['ro_office'];
                $sample_type_desc = $record['sample_type_desc'];
                $commodity_name = $record['commodity_name'];
                $count_samples = $record['count_samples'];
                $finalized = $record['finalized'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_dol_sample_analyzed (sr_no, user_id, ro_office, sample_type_desc, commodity_name, count_samples, finalized, report_date) 
                VALUES (
                '$i','$user_id','$ro_office', '$sample_type_desc', '$commodity_name', '$count_samples', '$finalized','$date')");

                $update = $con->execute("UPDATE temp_reportico_dol_sample_analyzed SET counts = (SELECT COUNT(user_id) FROM temp_reportico_dol_sample_analyzed WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getDolSampleAcceptedByChemistForTesting($from_date, $to_date, $posted_ro_office, $lab, $user, $ral_lab, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_dol_sample_accepted_chemist_testing WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT CONCAT(du.f_name,' ',du.l_name,' (',du.role,')') AS chemist_name, mll.ro_office,si.received_date,msa.org_sample_code,mc.commodity_name,mst.sample_type_desc,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name
                FROM m_sample_allocate AS msa
                INNER JOIN sample_inward AS si ON si.org_sample_code=msa.org_sample_code
                INNER JOIN m_commodity AS mc ON si.commodity_code=mc.commodity_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code
                INNER JOIN dmi_users AS du ON du.id=msa.alloc_to_user_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=du.email 
                WHERE  msa.display='Y' AND msa.acptnce_flag='Y' AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date' AND msa.lab_code='$ral_lab_no' AND r.user_flag='$ral_lab_name' AND msa.alloc_to_user_code='$user'");
        // print_r($query);exit;
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $ro_office = $record['ro_office'];
                $chemist_name = $record['chemist_name'];
                $received_date = $record['received_date'];
                $org_sample_code = $record['org_sample_code'];
                $commodity_name = $record['commodity_name'];
                $sample_type_desc = $record['sample_type_desc'];
                $lab_name = $record['lab_name'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_dol_sample_accepted_chemist_testing (sr_no, user_id, ro_office, chemist_name, received_date, org_sample_code, commodity_name, sample_type_desc, lab_name, report_date) 
                VALUES (
                '$i','$user_id','$ro_office', '$chemist_name', '$received_date', '$org_sample_code', '$commodity_name', '$sample_type_desc', '$lab_name','$date')");

                $update = $con->execute("UPDATE temp_reportico_dol_sample_accepted_chemist_testing SET counts = (SELECT COUNT(user_id) FROM temp_reportico_dol_sample_accepted_chemist_testing WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getDolRejectSample($from_date, $to_date, $posted_ro_office, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_dol_reject_sample WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT si.received_date,si.reject_date,CONCAT(du.f_name,' ',du.l_name) AS fullname,CONCAT(r.user_flag,',',mll.ro_office) AS labname,si.org_sample_code,mc.commodity_name,mst.sample_type_desc
                FROM sample_inward as si 
                JOIN (SELECT org_sample_code,max(id) AS id FROM workflow WHERE dst_loc_id='$ral_lab_no' GROUP BY(org_sample_code)) wf ON ((si.org_sample_code=wf.org_sample_code))
                JOIN workflow wff ON ((wf.id=wff.id))						
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id 
                INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code 
                INNER JOIN dmi_users AS du ON du.id=si.user_code 
                INNER JOIN dmi_user_roles AS r On r.user_email_id=du.email
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code And si.display='Y' AND si.acc_rej_flg='R'
                WHERE date(si.received_date) BETWEEN '$from_date' AND '$to_date'");
        // print_r($query);exit;
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $received_date = $record['received_date'];
                $reject_date = $record['reject_date'];
                $fullname = $record['fullname'];
                $labname = $record['labname'];
                $org_sample_code = $record['org_sample_code'];
                $commodity_name = $record['commodity_name'];
                $sample_type_desc = $record['sample_type_desc'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_dol_reject_sample (sr_no, user_id, received_date, reject_date, fullname, labname, org_sample_code, commodity_name, sample_type_desc, report_date) 
                VALUES (
                '$i','$user_id','$received_date', '$reject_date', '$fullname', '$labname', '$org_sample_code', '$commodity_name', '$sample_type_desc', '$date')");

                $update = $con->execute("UPDATE temp_reportico_dol_reject_sample SET counts = (SELECT COUNT(user_id) FROM temp_reportico_dol_reject_sample WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getDolSamplePendingForDispatch($from_date, $to_date, $posted_ro_office, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_dol_sample_pending WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT si.org_sample_code,mc.commodity_name,si.acc_rej_flg AS status,si.received_date,mst.sample_type_desc,mll.ro_office,r.user_flag
                FROM sample_inward AS si
                INNER JOIN code_decode AS cd ON cd.org_sample_code!=si.org_sample_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN m_commodity AS mc ON si.commodity_code=mc.commodity_code
                INNER JOIN dmi_users AS du ON du.id=si.user_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=du.email
                INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code AND si.display='Y' AND si.acc_rej_flg='P' AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date' AND si.loc_id='$ral_lab_no'
                GROUP BY mll.ro_office,si.acc_rej_flg,r.user_flag,si.org_sample_code,mc.commodity_name,si.received_date,mst.sample_type_desc
                ORDER BY si.received_date ASC");
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $org_sample_code = $record['org_sample_code'];
                $commodity_name = $record['commodity_name'];
                $status = $record['status'];
                $received_date = $record['received_date'];
                $sample_type_desc = $record['sample_type_desc'];
                $ro_office = $record['ro_office'];
                $user_flag = $record['user_flag'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_dol_sample_pending (sr_no, user_id, org_sample_code, commodity_name, status, received_date, sample_type_desc, ro_office, user_flag, report_date) 
                VALUES (
                '$i','$user_id','$org_sample_code', '$commodity_name', '$status', '$received_date', '$sample_type_desc', '$ro_office', '$user_flag', '$date')");

                $update = $con->execute("UPDATE temp_reportico_dol_sample_pending SET counts = (SELECT COUNT(user_id) FROM temp_reportico_dol_sample_pending WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getDolCodingDecodingSection($from_date, $to_date, $posted_ro_office, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_dol_coding_decoding WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT mll.ro_office,mc.commodity_name,si.stage_sample_code,w.tran_date,si.received_date,si.sample_total_qnt,muw.unit_weight,mst.sample_type_desc,si.remark
                FROM sample_inward AS si
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN m_unit_weight AS muw ON muw.unit_id=si.parcel_size
                INNER JOIN dmi_users AS u ON u.id=si.user_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN workflow AS w ON w.org_sample_code=si.org_sample_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code AND w.stage_smpl_flag In ('OF','HF','FG') AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date' AND w.dst_loc_id='$ral_lab_no'
                GROUP BY mll.ro_office,mc.commodity_name,w.tran_date,si.stage_sample_code,si.received_date,si.sample_total_qnt,muw.unit_weight,mst.sample_type_desc,si.remark
                ORDER BY mc.commodity_name ASC");
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $ro_office = $record['ro_office'];
                $commodity_name = $record['commodity_name'];
                $stage_sample_code = $record['stage_sample_code'];
                $tran_date = $record['tran_date'];
                $sample_total_qnt = $record['sample_total_qnt'];
                $unit_weight = $record['unit_weight'];
                $sample_qnt = $sample_total_qnt . ' ' . $unit_weight;
                $sample_type_desc = $record['sample_type_desc'];
                $remark = $record['remark'];
                $received_date = $record['received_date'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_dol_coding_decoding (sr_no, user_id, ro_office, commodity_name, stage_sample_code, received_date, tran_date, sample_qnt, sample_type_desc, remark, report_date) 
                VALUES (
                '$user_id','$ro_office', '$commodity_name', '$stage_sample_code','$received_date', '$tran_date', '$sample_qnt','$sample_type_desc','$remark','$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getDolSampleAllotedToChemistForTesting($from_date, $to_date, $posted_ro_office, $lab, $user, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_dol_sample_alloted_chemist_testing WHERE user_id = '$user_id'");

        $str = "SELECT si.org_sample_code,mc.commodity_name,si.received_date,mst.sample_type_desc,mll.ro_office,r.user_flag,CONCAT(du.f_name,' ',du.l_name,' (',du.role,')') AS chemist_name,msa.alloc_date
                FROM sample_inward AS si
                INNER JOIN code_decode AS cd ON cd.org_sample_code=si.org_sample_code
                INNER JOIN m_sample_allocate AS msa ON msa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN m_commodity AS mc ON si.commodity_code=mc.commodity_code
                INNER JOIN dmi_users AS du ON du.id=cd.alloc_to_user_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=du.email
                INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code AND cd.display='Y' AND cd.chemist_code!='-' AND DATE(msa.alloc_date) BETWEEN '$from_date' AND '$to_date' AND cd.alloc_to_user_code='$user'";

        if ($_SESSION['user_flag'] == "RAL") {
            $str .= " AND cd.lab_code='" . $_SESSION['posted_ro_office'] . "'";
        } else {
            $str .= " AND cd.lab_code='$ral_lab_no'";
        }

        $str .= " ORDER BY si.received_date ASC";

        $query = $con->execute($str);


        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $org_sample_code = $record['org_sample_code'];
                $commodity_name = $record['commodity_name'];
                $chemist_name = $record['chemist_name'];
                $received_date = $record['received_date'];
                $org_sample_code = $record['org_sample_code'];
                $ro_office = $record['ro_office'];
                $sample_type_desc = $record['sample_type_desc'];
                $user_flag = $record['user_flag'];
                $alloc_date = $record['alloc_date'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_dol_sample_alloted_chemist_testing (sr_no, user_id, ro_office, chemist_name, received_date, org_sample_code, commodity_name, sample_type_desc, user_flag, alloc_date, report_date) 
                VALUES (
                '$i','$user_id','$ro_office', '$chemist_name', '$received_date', '$org_sample_code', '$commodity_name', '$sample_type_desc', '$user_flag','$alloc_date','$date')");

                $update = $con->execute("UPDATE temp_reportico_dol_sample_alloted_chemist_testing SET counts = (SELECT COUNT(user_id) FROM temp_reportico_dol_sample_alloted_chemist_testing WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getDolSampleAllotedToChemistForRetesting($from_date, $to_date, $posted_ro_office, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_dol_sample_alloted_chemist_retesting WHERE user_id = '$user_id'");

        $str = "SELECT mll.ro_office,msa.org_sample_code,mc.commodity_name,si.received_date,mst.sample_type_desc,CONCAT(du.f_name,' ',du.l_name) AS chemist_name,msa.alloc_date
                FROM m_sample_allocate AS msa
                INNER JOIN sample_inward AS si ON si.org_sample_code=msa.org_sample_code
                INNER JOIN m_commodity AS mc ON si.commodity_code=mc.commodity_code
                INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN dmi_users AS du ON du.id=si.user_code AND msa.display='Y' AND msa.test_n_r='R' AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date'";

        if ($_SESSION['user_flag'] == "RAL") {
            $str .= " AND msa.lab_code='" . $_SESSION['posted_ro_office'] . "'";
        } else {
            $str .= " AND msa.lab_code='$ral_lab_no'";
        }

        $str .= " ORDER BY si.received_date ASC";

        $query = $con->execute($str);
        //  print_r($query);exit;
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $org_sample_code = $record['org_sample_code'];
                $commodity_name = $record['commodity_name'];
                $chemist_name = $record['chemist_name'];
                $received_date = $record['received_date'];
                $org_sample_code = $record['org_sample_code'];
                $ro_office = $record['ro_office'];
                $sample_type_desc = $record['sample_type_desc'];
                $alloc_date = $record['alloc_date'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_dol_sample_alloted_chemist_retesting (sr_no, user_id, ro_office, chemist_name, received_date, org_sample_code, commodity_name, sample_type_desc, alloc_date, report_date) 
                VALUES (
                '$i','$user_id','$ro_office', '$chemist_name', '$received_date', '$org_sample_code', '$commodity_name', '$sample_type_desc','$alloc_date','$date')");

                $update = $con->execute("UPDATE temp_reportico_dol_sample_alloted_chemist_retesting SET counts = (SELECT COUNT(user_id) FROM temp_reportico_dol_sample_alloted_chemist_retesting WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getDolReTestedSample($from_date, $to_date, $ral_lab_name, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_dol_retested_sample WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT msa.sample_code AS org_sample_code,mc.commodity_name,mst.sample_type_desc,si.received_date,ml.ro_office,CONCAT(du.f_name,' ',du.l_name) AS full_name,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name
                FROM sample_inward si
                INNER JOIN m_sample_allocate AS msa ON msa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_ro_offices AS ml ON ml.id=si.loc_id
                INNER JOIN dmi_users AS du ON du.id=si.user_code
                INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code 
                where DATE(si.received_date) BETWEEN '$from_date' AND '$to_date' aND si.display='Y' AND msa.display='Y' AND msa.test_n_r='R' 
                GROUP BY msa.sample_code,mc.commodity_name,mst.sample_type_desc,si.received_date,ml.ro_office,du.f_name,du.l_name");
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $org_sample_code = $record['org_sample_code'];
                $commodity_name = $record['commodity_name'];
                $sample_type_desc = $record['sample_type_desc'];
                $received_date = $record['received_date'];
                $ro_office = $record['ro_office'];
                $full_name = $record['full_name'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_dol_retested_sample (sr_no, user_id, lab_name, org_sample_code, commodity_name, sample_type_desc, received_date, ro_office, full_name, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$org_sample_code', '$commodity_name', '$sample_type_desc', '$received_date','$ro_office','$full_name','$date')");

                $update = $con->execute("UPDATE temp_reportico_dol_retested_sample SET counts = (SELECT COUNT(user_id) FROM temp_reportico_dol_retested_sample WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getDolReTestedSampleByChemist($from_date, $to_date, $ral_lab_name, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_dol_retested_sample_submit WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT  msa.sample_code AS org_sample_code,mc.commodity_name,mst.sample_type_desc,si.received_date,ml.lab_name AS lab,CONCAT(du.f_name,' ',du.l_name) AS full_name,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name
                FROM sample_inward AS si
                INNER JOIN m_lab AS ml ON ml.lab_code=si.loc_id
                INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code
                INNER JOIN dmi_users AS du ON du.id=si.user_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=du.email
                INNER JOIN m_sample_allocate AS msa ON msa.org_sample_code=si.org_sample_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code 
                WHERE si.display='Y' AND msa.display='Y' AND msa.test_n_r='R' AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date'
                GROUP BY msa.sample_code,mc.commodity_name,mst.sample_type_desc,si.received_date,ml.lab_name,du.f_name,du.l_name");
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $org_sample_code = $record['org_sample_code'];
                $commodity_name = $record['commodity_name'];
                $sample_type_desc = $record['sample_type_desc'];
                $received_date = $record['received_date'];
                $full_name = $record['full_name'];
                $lab = $record['lab'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_dol_retested_sample_submit (sr_no, user_id, lab_name, org_sample_code, commodity_name, sample_type_desc, received_date, full_name, lab, report_date) 
                VALUES (
               '$i', '$user_id','$lab_name', '$org_sample_code', '$commodity_name', '$sample_type_desc', '$received_date','$full_name','$lab','$date')");

                $update = $con->execute("UPDATE temp_reportico_dol_retested_sample_submit SET counts = (SELECT COUNT(user_id) FROM temp_reportico_io_retested_sample_submit WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getDolTestedSample($from_date, $to_date, $posted_ro_office, $user_code, $commodity, $lab, $sample_type, $ral_lab, $role, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_dol_tested_sample WHERE user_id = '$user_id'");

        if ($role == 'Jr Chemist' || $role == 'Sr Chemist' || $role == 'Cheif Chemist') {
            $str = "SELECT du.role,mll.ro_office,CONCAT(du.f_name,' ',du.l_name) as chemist_name,msa.recby_ch_date,mst.sample_type_desc,mc.commodity_name,cd.sample_code as org_sample_code,
                    msa.expect_complt,msa.commencement_date ,CASE WHEN msa.commencement_date>msa.expect_complt THEN msa.commencement_date   ::date-msa.expect_complt ::date ELSE 0
                    END as delay,expect_complt::date - recby_ch_date::date  as complete,si.received_date,
                    r.user_flag,du.f_name,du.l_name,grade,
                    (SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                    FROM dmi_users AS u
                    INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                    INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                    WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                    GROUP BY ral_lab) AS lab_name
                    FROM sample_inward as si 
                    Inner Join m_sample_allocate as msa ON msa.org_sample_code=si.org_sample_code
                    Inner Join m_sample_type as mst ON mst.sample_type_code=si.sample_type_code 
                    Inner Join dmi_ro_offices as mll ON mll.id=si.loc_id 
                    Inner Join code_decode as cd on cd.org_sample_code=si.org_sample_code
                    Inner Join dmi_users as du ON du.id=cd.alloc_to_user_code
                    Inner Join dmi_user_roles as r On r.user_email_id=du.email		
                    Inner Join m_commodity as mc ON mc.commodity_code=si.commodity_code 
                    WHERE cd.display='Y' and cd.alloc_to_user_code='" . $_SESSION['user_code'] . "' AND cd.lab_code='$posted_ro_office' AND date(si.received_date) BETWEEN '$from_date' and '$to_date'
                    GROUP BY du.role,mll.ro_office,du.f_name,du.l_name,cd.sample_code,msa.commencement_date,msa.recby_ch_date,msa.expect_complt,cd.chemist_code,mc.commodity_name,mst.sample_type_desc,si.received_date,r.user_flag,du.f_name,du.l_name,si.grade order by si.received_date";
        } else {
            $str = "SELECT mll.ro_office,CONCAT(du.f_name,' ',du.l_name,' (',du.role,')') as chemist_name, du.role, msa.recby_ch_date,mst.sample_type_desc,mc.commodity_name,cd.sample_code as org_sample_code,
                    msa.expect_complt,msa.commencement_date, CASE WHEN msa.commencement_date>msa.expect_complt THEN msa.commencement_date   ::date-msa.expect_complt ::date ELSE 0
                    END as delay,expect_complt::date - recby_ch_date::date  as complete,si.received_date,
                    r.user_flag,du.f_name,du.l_name,grade,
                    (SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                    FROM dmi_users AS u
                    INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                    INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                    WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                    GROUP BY ral_lab) AS lab_name
                    FROM sample_inward as si 
                    Inner Join m_sample_allocate as msa ON msa.org_sample_code=si.org_sample_code
                    Inner Join m_sample_type as mst ON mst.sample_type_code=si.sample_type_code 
                    Inner Join dmi_users as du ON du.id=msa.alloc_to_user_code
                    Inner Join dmi_user_roles as r On r.user_email_id=du.email
                    Inner Join dmi_ro_offices as mll ON mll.id=si.loc_id 
                    Inner Join code_decode as cd on cd.org_sample_code=si.org_sample_code 
                    Inner Join m_commodity as mc ON mc.commodity_code=si.commodity_code 
                    WHERE cd.display='Y' AND date(si.received_date) BETWEEN '$from_date' and '$to_date' AND si.sample_type_code='$sample_type' AND si.commodity_code='$commodity' AND r.user_flag='$ral_lab_name' AND msa.lab_code='$ral_lab_no'
                    GROUP BY du.role,mll.ro_office,du.f_name,du.l_name,cd.sample_code,msa.commencement_date,msa.recby_ch_date,msa.expect_complt,cd.chemist_code,mc.commodity_name,mst.sample_type_desc,si.received_date,r.user_flag,du.f_name,du.l_name,si.grade order by si.received_date";
                        }
        $query = $con->execute($str);
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $ro_office = $record['ro_office'];
                $chemist_name = $record['chemist_name'];
                $recby_ch_date = $record['recby_ch_date'];
                $sample_type_desc = $record['sample_type_desc'];
                $commodity_name = $record['commodity_name'];
                $org_sample_code = $record['org_sample_code'];
                $expect_complt = $record['expect_complt'];
                $commencement_date = $record['commencement_date'];
                $grade = $record['grade'];
                $lab_name = $record['lab_name'];
                $role = $record['role'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_dol_tested_sample (sr_no, user_id, role, ro_office, chemist_name, recby_ch_date, org_sample_code, commodity_name, sample_type_desc, expect_complt, commencement_date, grade, lab_name, report_date) 
                VALUES (
                '$i','$user_id','$role','$ro_office', '$chemist_name', '$recby_ch_date', '$org_sample_code', '$commodity_name', '$sample_type_desc','$expect_complt','$commencement_date','$grade','$lab_name','$date')");

                $update = $con->execute("UPDATE temp_reportico_dol_tested_sample SET counts = (SELECT COUNT(user_id) FROM temp_reportico_dol_tested_sample WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getDolTestSubmitByChemist($from_date, $to_date, $chemist_code, $sample_code, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_dol_test_submit_by_chemist WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT recby_ch_date,sa.chemist_code,sa.sample_code,c.commodity_name,CONCAT(du.f_name,' ',du.l_name,' (',du.role,') ') AS chemist_name,t.test_name,atd.result,'$chemist_code'AS chemist_code,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name
                FROM m_sample_allocate sa
                INNER JOIN actual_test_data AS atd ON sa.chemist_code=atd.chemist_code
                INNER JOIN code_decode AS cd ON cd.org_sample_code=sa.org_sample_code
                INNER JOIN m_test AS t ON atd.test_code=t.test_code
                INNER JOIN m_commodity AS c ON c.commodity_code=sa.commodity_code
                INNER JOIN dmi_users AS du ON du.id=sa.alloc_to_user_code AND DATE(sa.alloc_date) BETWEEN '$from_date' AND '$to_date' AND sa.chemist_code='$chemist_code'
                WHERE atd.chemist_code='$chemist_code' AND sa.sample_code='$sample_code' AND cd.status_flag in('C','G') AND sa.chemist_code='$chemist_code'
                GROUP BY du.role,sa.chemist_code,sa.recby_ch_date,sa.sample_code,c.commodity_name,du.f_name,du.l_name,t.test_name,atd.result");
        // print_r($query);exit;
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $recby_ch_date = $record['recby_ch_date'];
                $commodity_name = $record['commodity_name'];
                $sample_code = $record['sample_code'];
                $chemist_name = $record['chemist_name'];
                $test_name = $record['test_name'];
                $result = $record['result'];
                $chemist_code = $record['chemist_code'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_dol_test_submit_by_chemist(sr_no, user_id, lab_name, recby_ch_date, commodity_name, sample_code, chemist_name, test_name, result, chemist_code, report_date) 
            VALUES (
                '$i','$user_id','$lab_name', '$recby_ch_date', '$commodity_name', '$sample_code', '$chemist_name','$test_name','$result','$chemist_code','$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getDolChemistWiseSampleAnalysis($month, $posted_ro_office, $user, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_dol_chemist_wise_samp_analysis WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT CONCAT(u.f_name,' ',u.l_name,'(',u.role,')') AS chemist_name, r.user_flag,drf.ro_office,u.id, 
        (
        SELECT COUNT(DISTINCT(cd.sample_code)) AS check_analyzed_count
        FROM code_decode cd
        INNER JOIN sample_inward AS si ON si.stage_sample_code=cd.sample_code
        INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
        INNER JOIN m_sample_allocate AS sa ON sa.sample_code=si.stage_sample_code
        WHERE cd.display='Y' AND st.sample_type_desc='Check' AND cd.status_flag In('C','G') AND cd.alloc_to_user_code='$user' AND EXTRACT(MONTH
        FROM sa.alloc_date):: INTEGER = '$month'
        ) AS check_analyzed_count,  
        (
        SELECT COUNT(DISTINCT(cd.sample_code)) AS res_analyzed_count
        FROM code_decode cd
        INNER JOIN sample_inward AS si ON si.stage_sample_code=cd.sample_code
        INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
        INNER JOIN m_sample_allocate AS sa ON sa.sample_code=si.stage_sample_code
        WHERE cd.display='Y' AND st.sample_type_desc='Research' AND cd.status_flag In('C','G') AND cd.alloc_to_user_code='$user' AND EXTRACT(MONTH
        FROM sa.alloc_date):: INTEGER = '$month'
        ) AS res_analyzed_count,  
        (
        SELECT COUNT(DISTINCT(cd.sample_code)) AS res_challenged_count
        FROM code_decode cd
        INNER JOIN sample_inward AS si ON si.stage_sample_code=cd.sample_code
        INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
        INNER JOIN m_sample_allocate AS sa ON sa.sample_code=si.stage_sample_code
        WHERE cd.display='Y' AND st.sample_type_desc='Challenged' AND cd.status_flag In('C','G') AND cd.alloc_to_user_code='$user' AND EXTRACT(MONTH
        FROM sa.alloc_date):: INTEGER = '$month'
        ) AS res_challenged_count,  
        (
        SELECT COUNT(DISTINCT(cd.sample_code)) AS othr_analyzed_count
        FROM code_decode cd
        INNER JOIN sample_inward AS si ON si.stage_sample_code=cd.sample_code
        INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
        INNER JOIN m_sample_allocate AS sa ON sa.sample_code=si.stage_sample_code
        WHERE cd.display='Y' AND st.sample_type_desc NOT IN('Check','Research','Challenged','Apex(Check)') AND cd.status_flag In('C','G') AND cd.alloc_to_user_code='$user' AND EXTRACT(MONTH
        FROM sa.alloc_date):: INTEGER = '$month'
        ) AS othr_analyzed_count,
        (
        SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
        FROM dmi_users AS u
        INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
        INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
        WHERE u.status = 'active' AND o.id = '$ral_lab_no'
        GROUP BY ral_lab
        ) AS lab_name        
        
        FROM dmi_users u
        INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
        INNER JOIN dmi_ro_offices drf ON u.posted_ro_office=drf.id
        WHERE u.posted_ro_office= '$ral_lab_no' AND u.role IN('Jr Chemist','Sr Chemist','Cheif Chemist') AND u.STATUS = 'active' AND u.id = '$user'
        ");
       
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $chemist_name = $record['chemist_name'];
                $check_analyzed_count = $record['check_analyzed_count'];
                $res_analyzed_count = $record['res_analyzed_count'];
                $res_challenged_count = $record['res_challenged_count'];
                $othr_analyzed_count = $record['othr_analyzed_count'];
                $date = date("d/m/Y");
                $month = date("F", mktime(0, 0, 0, $month, 10));
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_dol_chemist_wise_samp_analysis (sr_no, user_id, lab_name, chemist_name, check_analyzed_count, res_analyzed_count, res_challenged_count, othr_analyzed_count, month, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$chemist_name', '$check_analyzed_count','$res_analyzed_count','$res_challenged_count','$othr_analyzed_count', '$month','$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getDolConsolidateSatementBroughtFwdCarriedFwdSample($month, $user, $posted_ro_office, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_dol_consoli_sample WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT CONCAT(u.f_name,' ',u.l_name) AS chemist_name,ur.role_name,'$month' AS month,
                (
                SELECT count(DISTINCT(sample_code)) AS bf_count 
                FROM code_decode 
                WHERE lab_code='$ral_lab_no' AND status_flag NOT IN('G','F' ) AND alloc_to_user_code='$user'
                ) AS bf_count,
                (
                SELECT count(DISTINCT(sample_code)) AS received_count 
                FROM m_sample_allocate 
                WHERE lab_code='$ral_lab_no' AND Extract(month FROM alloc_date)::INTEGER = '$month' AND alloc_to_user_code='$user'
                ) AS received_count,  
                (
                SELECT count(DISTINCT(cd.sample_code)) AS analyzed_count_one FROM code_decode AS cd
                INNER JOIN m_sample_allocate AS sa ON sa.sample_code=cd.sample_code 			
                WHERE test_n_r!='R' AND cd.lab_code='$ral_lab_no' AND Extract(month FROM sa.alloc_date)::INTEGER = '$month' AND cd.alloc_to_user_code='$user'
                ) AS analyzed_count_one,                                
                (
                SELECT count(DISTINCT(cd.sample_code)) AS analyzed_count_two FROM code_decode AS cd
                INNER JOIN m_sample_allocate AS sa ON sa.sample_code=cd.sample_code 			
                WHERE test_n_r='R' AND cd.lab_code='$ral_lab_no' AND Extract(month FROM alloc_date)::INTEGER = '$month' AND cd.alloc_to_user_code='$user'
                ) AS analyzed_count_two,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name
            
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS mlc ON mlc.id=u.posted_ro_office 							
                INNER JOIN user_role AS ur ON ur.role_name=u.role 
                INNER JOIN dmi_user_roles AS r on u.email=r.user_email_id
                WHERE mlc.id='$ral_lab_no' AND u.id='$user' And u.role IN ('Jr Chemist','Sr Chemist','Cheif Chemist') and u.status='active'");
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $bf_count = $record['bf_count'];
                $received_count = $record['received_count'];
                $total = $bf_count + $received_count;
                $analyzed_count_one = $record['analyzed_count_one'];
                $analyzed_count_two = $record['analyzed_count_two'];
                $carried_for = $total - $analyzed_count_one;
                $chemist_name = $record['chemist_name'];
                $role_name = $record['role_name'];
                $month = date("F", mktime(0, 0, 0, $record['month'], 10));
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_dol_consoli_sample (sr_no, user_id, lab_name, chemist_name, bf_count, received_count, total, analyzed_count_one, analyzed_count_two, carried_for, role_name, month, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$chemist_name', '$bf_count', '$received_count', '$total','$analyzed_count_one','$analyzed_count_two','$carried_for','$role_name','$month','$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static  function getDolNoCheckPrivateResearchSample($from_date, $to_date, $posted_ro_office, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $begin = new DateTime($from_date);
        $end = new DateTime($to_date);
        $k = 0;
        while ($begin <= $end) {
            $yr_data[$k]['month'] = $begin->format('m');
            $yr_data[$k]['year'] = $begin->format('Y');
            $month1[$k] = $begin->format('M') . ',' . $begin->format('Y');

            $k++;
            $begin->modify('first day of next month');
        } //

        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_dol_chk_pvt_research WHERE user_id = '$user_id'");

        foreach ($yr_data as $da) {
            $month = $da['month'];
            $year = $da['year'];

            $query = $con->execute("SELECT dmi_ro_offices.id AS lab_code,dmi_user_roles.user_flag ,dmi_ro_offices.ro_office, 
                    (
                    SELECT COUNT(DISTINCT(cd.org_sample_code)) AS check_analyzed_count
                    FROM code_decode cd
                    INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                    INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    WHERE cd.display='Y' AND st.sample_type_desc='Check' AND cd.status_flag In('C','G') AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND cd.lab_code='$ral_lab_no' AND DATE(sa.alloc_date) BETWEEN '$from_date' AND '$to_date'
                    ) AS check_analyzed_count,
                    (
                    SELECT COUNT(DISTINCT(cd.org_sample_code)) AS res_analyzed_count
                    FROM code_decode cd
                    INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                    INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    WHERE cd.display='Y' AND st.sample_type_desc='Research' AND cd.status_flag In('C','G') AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND cd.lab_code='$ral_lab_no' AND DATE(sa.alloc_date) BETWEEN '$from_date' AND '$to_date'
                    ) AS res_analyzed_count,
                    (
                    SELECT COUNT(DISTINCT(cd.org_sample_code)) AS chk_analyzed_count
                    FROM code_decode cd
                    INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                    INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    WHERE cd.display='Y' AND st.sample_type_desc='Challenged' AND cd.status_flag In('C','G') AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND cd.lab_code='$ral_lab_no' AND DATE(sa.alloc_date) BETWEEN '$from_date' AND '$to_date'
                    ) AS chk_analyzed_count,
                    (
                    SELECT COUNT(DISTINCT(cd.org_sample_code)) AS othr_analyzed_count
                    FROM code_decode cd
                    INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                    INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    WHERE cd.display='Y' AND st.sample_type_desc NOT IN('Check','Research','Challenged') AND cd.status_flag In('C','G') AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND cd.lab_code='$ral_lab_no' AND DATE(sa.alloc_date) BETWEEN '$from_date' AND '$to_date'
                    ) AS othr_analyzed_count,
                    (
                    SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                    FROM dmi_users AS u
                    INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                    INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                    WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                    GROUP BY ral_lab
                    ) AS lab_name
                    
                    FROM dmi_users
                    INNER JOIN dmi_ro_offices ON dmi_users.posted_ro_office=dmi_ro_offices.id
                    INNER JOIN dmi_user_roles ON dmi_user_roles.user_email_id=dmi_users.email AND dmi_user_roles.user_flag In('RAL','CAL') AND dmi_users.posted_ro_office='$ral_lab_no'
                    WHERE dmi_users.status = 'active'
                    GROUP BY dmi_user_roles.user_flag,dmi_ro_offices.id,dmi_ro_offices.ro_office
                    ORDER BY dmi_ro_offices.ro_office ASC");

            $records = $query->fetchAll('assoc');
            // print_r($records);

            $query->closeCursor();
            if (!empty($records)) {
                foreach ($records as $record) {
                    $lab_name = $record['lab_name'];
                    $check_analyzed_count = $record['check_analyzed_count'];
                    $res_analyzed_count = $record['res_analyzed_count'];
                    $chk_analyzed_count = $record['chk_analyzed_count'];
                    $othr_analyzed_count = $record['othr_analyzed_count'];
                    $date = date("d/m/Y");
                    $i = $i + 1;

                    $insert = $con->execute("INSERT INTO temp_reportico_dol_chk_pvt_research (sr_no, user_id, lab_name, check_analyzed_count, res_analyzed_count, chk_analyzed_count, othr_analyzed_count,  report_date) 
                    VALUES (
                    '$i','$user_id','$lab_name', '$check_analyzed_count', '$res_analyzed_count', '$chk_analyzed_count', '$othr_analyzed_count','$date')");
                }
                return 1;
            } else {
                return 0;
            }
        }
    }

    public static function getDolPerformanceRalCal($from_date, $to_date, $posted_ro_office, $lab, $sample_type, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $month = date("m", strtotime($from_date));

        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_dol_performance_ral_cal WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT drf.id AS lab_code, CONCAT(r.user_flag,', ',drf.ro_office) AS lab_name,
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS progress_sample
                FROM code_decode cd
                INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                WHERE cd.display='Y' AND si.sample_type_code='$sample_type' AND cd.lab_code='$ral_lab_no' AND cd.status_flag In('C','G') AND date(sa.alloc_date) BETWEEN '$from_date' AND '$to_date'
                ) AS progress_sample,
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS tot_sample_month
                FROM code_decode cd
                INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                WHERE cd.display='Y' AND si.sample_type_code='$sample_type' AND cd.lab_code='$ral_lab_no' AND cd.status_flag In('C','G') AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = $month
                ) AS tot_sample_month
                FROM dmi_users u
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                INNER JOIN dmi_ro_offices drf ON u.posted_ro_office=drf.id
                WHERE u.status = 'active' AND r.user_flag='$ral_lab_name' AND drf.id='$ral_lab_no'
                GROUP BY r.user_flag,drf.id,drf.ro_office
                ORDER BY drf.ro_office ASC
                ");
       
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $progress_sample = $record['progress_sample'];
                $tot_sample_month = $record['tot_sample_month'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_dol_performance_ral_cal (sr_no, user_id, lab_name, progress_sample, tot_sample_month, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$progress_sample', '$tot_sample_month', '$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getDolSampleAnalyzedByChemist($from_date, $to_date, $posted_ro_office, $user, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_dol_sample_analyzed_chemist WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT DISTINCT(sa.sample_code),CONCAT('$ral_lab_name',', ',ml.ro_office) AS lab_name,ml.ro_office,si.stage_sample_code,ml.id AS lab,CONCAT(r.user_flag,', ',ml.ro_office) AS sample_received_from,mc.commodity_name,sc.sam_condition_desc,ct.container_desc,pc.par_condition_desc,
                si.received_date,si.letr_ref_no, CONCAT(u.f_name,' ', u.l_name) AS name_chemist,si.sample_total_qnt,si.lab_code,si.grading_date,si.remark,sa.alloc_date
                FROM sample_inward AS si
                INNER JOIN m_sample_condition AS sc ON sc.sam_condition_code=si.sam_condition_code
                INNER JOIN m_par_condition AS pc ON pc.par_condition_code=si.par_condition_code
                INNER JOIN m_container_type AS ct ON ct.container_code=si.container_code
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                INNER JOIN dmi_ro_offices AS ml ON ml.id=sa.lab_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                WHERE sa.lab_code='$posted_ro_office' AND DATE(sa.alloc_date) BETWEEN '$from_date' AND '$to_date' AND sa.alloc_to_user_code='$user'
                ORDER BY si.received_date ASC");
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $sample_code = $record['sample_code'];
                $ro_office = $record['ro_office'];
                $stage_sample_code = $record['stage_sample_code'];
                $sample_received_from = $record['sample_received_from'];
                $commodity_name = $record['commodity_name'];
                $sam_condition_desc = $record['sam_condition_desc'];
                $container_desc = $record['container_desc'];
                $par_condition_desc = $record['par_condition_desc'];
                $received_date = $record['received_date'];
                $letr_ref_no = $record['letr_ref_no'];
                $name_chemist = $record['name_chemist'];
                $sample_total_qnt = $record['sample_total_qnt'];
                $lab_code = $record['lab_code'];
                $grading_date = $record['grading_date'];
                $remark = $record['remark'];
                $alloc_date = $record['alloc_date'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_dol_sample_analyzed_chemist (sr_no, user_id, lab_name, sample_code, ro_office, stage_sample_code, sample_received_from, commodity_name, sam_condition_desc, container_desc,par_condition_desc,received_date, letr_ref_no, name_chemist, sample_total_qnt, lab_code, grading_date, remark, alloc_date, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$sample_code', '$ro_office', '$stage_sample_code', '$sample_received_from','$commodity_name','$sam_condition_desc','$container_desc','$par_condition_desc','$received_date','$letr_ref_no','$name_chemist','$sample_total_qnt','$lab_code','$grading_date','$remark','$alloc_date','$date')");

                $update = $con->execute("UPDATE temp_reportico_dol_sample_analyzed_chemist SET counts = (SELECT COUNT(user_id) FROM temp_reportico_dol_sample_analyzed_chemist WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getDolSampleAlloteAnalyzePending($from_date, $to_date, $posted_ro_office, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_dol_sample_allot_analyz_pend WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT dmi_ro_offices.id AS lab_code, CONCAT(r.user_flag,', ',dmi_ro_offices.ro_office) AS lab_name,
                (
                SELECT COUNT(*) AS allotment_count
                FROM code_decode AS cd
                INNER JOIN m_sample_allocate AS msa ON cd.org_sample_code=msa.org_sample_code
                WHERE cd.status_flag in ('N','C','G') AND DATE(msa.alloc_date) BETWEEN '$from_date' AND '$to_date' AND cd.lab_code='$ral_lab_no'
                ) AS allotment_count,
                (
                SELECT COUNT(*) AS analyzed_count
                FROM code_decode AS cd
                INNER JOIN m_sample_allocate AS msa ON cd.org_sample_code=msa.org_sample_code
                WHERE cd.status_flag in ('C','G') AND DATE(msa.alloc_date) BETWEEN '$from_date' AND '$to_date' AND cd.lab_code='$ral_lab_no'
                ) AS analyzed_count,
                (
                SELECT COUNT(*) AS pending_count
                FROM code_decode AS cd
                INNER JOIN m_sample_allocate AS msa ON cd.org_sample_code=msa.org_sample_code
                WHERE cd.status_flag='N' AND DATE(msa.alloc_date) BETWEEN '$from_date' AND '$to_date' AND cd.lab_code='$ral_lab_no'
                ) AS pending_count
                FROM dmi_users
                INNER JOIN dmi_user_roles AS r ON dmi_users.email=r.user_email_id
                INNER JOIN dmi_ro_offices ON dmi_users.posted_ro_office=dmi_ro_offices.id AND r.user_flag In ('RAL','CAL') AND dmi_users.posted_ro_office='$ral_lab_no'
                WHERE dmi_users.status = 'active'
                GROUP BY r.user_flag,dmi_ro_offices.id,dmi_ro_offices.ro_office
                ORDER BY dmi_ro_offices.ro_office ASC");
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $allotment_count = $record['allotment_count'];
                $analyzed_count = $record['analyzed_count'];
                $pending_count = $record['pending_count'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_dol_sample_allot_analyz_pend (sr_no, user_id, lab_name, allotment_count, analyzed_count, pending_count, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$allotment_count', '$analyzed_count', '$pending_count', '$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getDolDetailsSampleAnalyzedByChemist($month, $year, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_dol_details_sample_analyzed WHERE user_id = '$user_id'");

        $sql = "SELECT  sa.alloc_to_user_code, sa.commodity_code FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                INNER JOIN dmi_ro_offices AS ml ON ml.id=sa.lab_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND u.role IN ('Jr Chemist','Sr Chemist','Cheif Chemist')
                GROUP BY sa.alloc_to_user_code, sa.commodity_code";

        $sql = $con->execute($sql);
        $recordNames = $sql->fetchAll('assoc');
        $sql->closeCursor();

        $record_insert = 0;
        foreach ($recordNames as $recordName) {
            $user_code = $recordName['alloc_to_user_code'];
            $commodity_code = $recordName['commodity_code'];

            $query = $con->execute("SELECT mc.commodity_name,  sa.alloc_date, sa.recby_ch_date, sa.commencement_date, CONCAT(u.f_name,' ',u.l_name,' (',u.role,') ') AS name_chemist, si.org_sample_code AS project_sample,
                'NA' AS other, 'NA' AS other_work, 'Yes' AS norm, mst.sample_type_desc,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 1
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no'  AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                ) AS check_count,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 5
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                ) AS check_apex_count,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 4
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                ) AS challenged_count,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 7
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                ) AS ilc_count,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 2
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                ) AS research_count,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code AND sa.test_n_r = 'R' 
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                ) AS retesting_count,
                (
                SELECT COUNT(ct.test_code)
                FROM commodity_test ct
                INNER JOIN m_sample_allocate AS msa ON msa.commodity_code = ct.commodity_code AND msa.lab_code = '$ral_lab_no'
                INNER JOIN sample_inward si ON msa.org_sample_code = si.org_sample_code AND msa.commodity_code = si.commodity_code
                WHERE ct.commodity_code = $commodity_code AND msa.alloc_to_user_code = '$user_code' AND EXTRACT(MONTH
                FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM msa.alloc_date):: INTEGER = '$year'
                ) AS no_of_param,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name
                
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                INNER JOIN dmi_ro_offices AS ml ON ml.id=sa.lab_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month'  AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                GROUP BY mc.commodity_name,  sa.alloc_date, sa.recby_ch_date, sa.commencement_date, project_sample,  name_chemist, mst.sample_type_desc, si.received_date
                ORDER BY si.received_date ASC");
            $records = $query->fetchAll('assoc');
            $query->closeCursor();
            if (!empty($records)) {
                foreach ($records as $record) {
                    $commodity_name = $record['commodity_name'];
                    $project_sample = $record['project_sample'];
                    $check_count = $record['check_count'];
                    $check_apex_count = $record['check_apex_count'];
                    $challenged_count = $record['challenged_count'];
                    $ilc_count = $record['ilc_count'];
                    $research_count = $record['research_count'];
                    $retesting_count = $record['retesting_count'];
                    $recby_ch_date = $record['recby_ch_date'];
                    $commencement_date = $record['commencement_date'];
                    $name_chemist = $record['name_chemist'];
                    $sample_type_desc = $record['sample_type_desc'];
                    $no_of_param = $record['no_of_param'];
                    if ($recby_ch_date == '' && $commencement_date == '') {
                        $working_days = '0';
                    } else {
                        $diff = abs(strtotime($commencement_date) - strtotime($recby_ch_date));
                        $years = floor($diff / (365 * 60 * 60 * 24));
                        $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
                        $working_days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
                    }
                    $other = $record['other'];
                    $other_work = $record['other_work'];
                    $norm = $record['norm'];
                    $lab_name = $record['lab_name'];
                    $date = date("d/m/Y");
                    $i = $i + 1;
                    $total_no = $check_count + $check_apex_count + $challenged_count + $ilc_count + $research_count + $retesting_count;

                    $insert = $con->execute("INSERT INTO temp_reportico_dol_details_sample_analyzed (sr_no, user_id, lab_name, name_chemist, sample_type_desc, commodity_name, project_sample, check_count, check_apex_count, challenged_count, ilc_count, research_count, retesting_count, working_days, no_of_param, other, other_work, norm, total_no, report_date) 
                    VALUES (
                '$i','$user_id','$lab_name','$name_chemist', '$sample_type_desc', '$commodity_name', '$project_sample', '$check_count', '$check_apex_count', '$challenged_count', '$ilc_count','$research_count','$retesting_count', '$working_days', '$no_of_param', '$other','$other_work','$norm', '$total_no', '$date')");

                    $update = $con->execute("UPDATE temp_reportico_dol_details_sample_analyzed SET counts = (SELECT COUNT(user_id) FROM temp_reportico_dol_details_sample_analyzed WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
                }

                $record_insert = 1;
            }
        }

        if ($record_insert == 1) {
            return 1;
        } else {
            return 0;
        }
    }
  
    //added for 26-08-2022 by shreeya
    //Details Of Samples Analysed Carry Forward For SampleType
    public static function getDolDetailsOfSamplesAnalysedCarryForwardForSampleType($month, $year, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_dol_details_of_samples_analysed_carry_forward_for_sample WHERE user_id = '$user_id'");

        $sql = "SELECT  sa.alloc_to_user_code, sa.commodity_code,si.sample_type_code FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                INNER JOIN dmi_ro_offices AS ml ON ml.id=sa.lab_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND u.role IN ('Jr Chemist','Sr Chemist','Cheif Chemist')
                GROUP BY sa.alloc_to_user_code, sa.commodity_code,si.sample_type_code";
       
        $sql = $con->execute($sql);
        $recordNames = $sql->fetchAll('assoc');
        $sql->closeCursor();

        $record_insert = 0;
        foreach ($recordNames as $recordName) {
            $user_code = $recordName['alloc_to_user_code'];
            $commodity_code = $recordName['commodity_code'];

            $query = $con->execute("SELECT mc.commodity_name,  sa.alloc_date, sa.recby_ch_date, sa.commencement_date, CONCAT(u.f_name,' ',u.l_name,' (',u.role,') ') AS name_chemist,'NA' AS other, 'NA' AS other_work, 'Yes' AS norm, mst.sample_type_desc,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 1
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no'  AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                ) AS check_count,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 5
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                ) AS check_apex_count,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 4
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                ) AS challenged_count,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 9
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                ) AS ilc_count,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 2
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                ) AS research_count,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code AND sa.test_n_r = 'R' 
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                ) AS retesting_count,
                (
                SELECT COUNT(ct.test_code)
                FROM commodity_test ct
                INNER JOIN m_sample_allocate AS msa ON msa.commodity_code = ct.commodity_code AND msa.lab_code = '$ral_lab_no'
                INNER JOIN sample_inward si ON msa.org_sample_code = si.org_sample_code AND msa.commodity_code = si.commodity_code
                WHERE ct.commodity_code = $commodity_code AND msa.alloc_to_user_code = '$user_code' AND EXTRACT(MONTH
                FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM msa.alloc_date):: INTEGER = '$year'
                ) AS no_of_param,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name

                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                INNER JOIN dmi_ro_offices AS ml ON ml.id=sa.lab_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND sa.alloc_to_user_code = '$user_code'
                GROUP BY mc.commodity_name,  sa.alloc_date, sa.recby_ch_date, sa.commencement_date, name_chemist, mst.sample_type_desc, si.received_date
                ORDER BY si.received_date ASC");
            $records = $query->fetchAll('assoc');

            $query->closeCursor();
            if (!empty($records)) {
                foreach ($records as $record) {
                    $commodity_name = $record['commodity_name'];
                    $project_sample = 'NA';
                    $check_count = $record['check_count'];
                    $check_apex_count = $record['check_apex_count'];
                    $challenged_count = $record['challenged_count'];
                    $ilc_count = $record['ilc_count'];
                    $research_count = $record['research_count'];
                    $retesting_count = $record['retesting_count'];
                    $recby_ch_date = $record['recby_ch_date'];
                    $commencement_date = $record['commencement_date'];
                    $name_chemist = $record['name_chemist'];
                    $sample_type_desc = $record['sample_type_desc'];
                    $lab_name = $record['lab_name'];
                    $no_of_param = $record['no_of_param'];

                    if ($recby_ch_date == '' || $commencement_date == '') {
                        $working_days = '0';
                    } else {
                        $diff = abs(strtotime($commencement_date) - strtotime($recby_ch_date));
                        $years = floor($diff / (365 * 60 * 60 * 24));
                        $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
                        $working_days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
                    }
                    $other = $record['other'];
                    $other_work = $record['other_work'];
                    $norm = $record['norm'];
                    $date = date("d/m/Y");
                    $i = $i + 1;
                    $total_no = $check_count + $check_apex_count + $challenged_count + $ilc_count + $research_count + $retesting_count;

                    $insert = $con->execute("INSERT INTO temp_dol_details_of_samples_analysed_carry_forward_for_sample 
                    (sr_no,user_id,months,lab_name,name_chemist,sample_type_desc,commodity_name,project_sample,check_count,check_apex_count,challenged_count,ilc_count,research_count,retesting_count,working_days,
                    no_of_param,other,other_work,norm,report_date,total_no) 
                    VALUES (
                    '$i','$user_id','$month','$lab_name','$name_chemist', '$sample_type_desc', '$commodity_name', '$project_sample', '$check_count', '$check_apex_count', '$challenged_count', '$ilc_count','$research_count','$retesting_count', '$working_days',
                    '$no_of_param', '$other','$other_work','$norm', '$date','$total_no')");

                    $update = $con->execute("UPDATE temp_dol_details_of_samples_analysed_carry_forward_for_sample SET counts = (SELECT COUNT(user_id) FROM temp_dol_details_of_samples_analysed_carry_forward_for_sample WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
                }
                                                                                                                                                        
                $record_insert = 1;
            }
        }

        if ($record_insert == 1) {
            return 1;
        } else {
            return 0;
        }
    }


    //added for consolidated report on 22-08-2022 by shreeya
    public static function getDolConsolidatedReporteAnalyzedByChemist($month, $year, $ral_lab_no, $ral_lab_name)
    {
     
        $i = 0;
        $user_id = $_SESSION['user_code'];
       
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_dol_consolidated_reporte_analyzed_by_chemists WHERE user_id = '$user_id'");

        $sql = "SELECT  sa.alloc_to_user_code, sa.commodity_code, mst.sample_type_code,u.email FROM sample_inward AS si
        INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code
        INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
        INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
        INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
        INNER JOIN dmi_ro_offices AS ml ON ml.id=sa.lab_code
        INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
        WHERE si.entry_type = 'sub_sample' AND sa.lab_code ='$ral_lab_no' AND EXTRACT(MONTH
        FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
        FROM sa.alloc_date):: INTEGER = '$year' AND u.role IN ('Jr Chemist','Sr Chemist','Cheif Chemist')
        GROUP BY sa.alloc_to_user_code, sa.commodity_code, mst.sample_type_code,u.email";
                
        $sql2 = $con->execute($sql2);
        $recordNames = $sql2->fetchAll('assoc');
        $sql2->closeCursor();

        $record_insert = 0;
        foreach ($recordNames as $recordName) {
            $user_code = $recordName['alloc_to_user_code'];
            $commodity_code = $recordName['commodity_code'];

            $query = $con->execute("SELECT mc.commodity_name,  sa.alloc_date, sa.recby_ch_date, sa.commencement_date, CONCAT(u.f_name,' ',u.l_name,' (',u.role,') ') AS name_chemist,'NA' AS other, 'NA' AS other_work, 'Yes' AS norm, mst.sample_type_desc,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 1
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no'  AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                ) AS check_count,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 5
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                ) AS check_apex_count,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 4
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                ) AS challenged_count,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 9
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE si.entry_type = 'sub_sample' AND sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                ) AS ilc_count,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 2
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                ) AS research_count,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code AND sa.test_n_r = 'R' 
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                ) AS retesting_count,
                (
                SELECT COUNT(ct.test_code)
                FROM commodity_test ct
                INNER JOIN m_sample_allocate AS msa ON msa.commodity_code = ct.commodity_code AND msa.lab_code = '$ral_lab_no'
                INNER JOIN sample_inward si ON msa.org_sample_code = si.org_sample_code AND msa.commodity_code = si.commodity_code
                WHERE ct.commodity_code = $commodity_code AND msa.alloc_to_user_code = '$user_code' AND EXTRACT(MONTH
                FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM msa.alloc_date):: INTEGER = '$year'
                ) AS no_of_param,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                INNER JOIN dmi_ro_offices AS ml ON ml.id=sa.lab_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND sa.alloc_to_user_code = '$user_code'
                GROUP BY mc.commodity_name,  sa.alloc_date, sa.recby_ch_date, sa.commencement_date, name_chemist, mst.sample_type_desc, si.received_date
                ORDER BY si.received_date ASC");

            $records = $query->fetchAll('assoc');
            $query->closeCursor();
            if (!empty($records)) {
                foreach ($records as $record) {
                    $name_chemist            = $record['name_chemist'];
                    $check_apex_count        = $record['check_apex_count'];
                    $challenged_count        = $record['challenged_count'];
                    $retesting_count         = $record['retesting_count'];
                    $ilc_count               = $record['ilc_count'];
                    $other_private_sample    = 'NA';
                    $research_count          = $record['research_count'];
                    $project_sample          = 'NA';
                    $smpl_analysed_instrn    = 'NA';
                    $check_count             = $record['check_count'];
                    $report_date             = date("d/m/Y");
                    $total_no                = 'NA';
                    $lab_name                = $record['lab_name'];
                    $sample_type_desc        = $record['sample_type_desc'];
                    $i                       = $i + 1;
                    $total_no                = $check_count + $check_apex_count + $challenged_count + $ilc_count + $research_count + $retesting_count;

                    $insert = $con->execute("INSERT INTO temp_dol_consolidated_reporte_analyzed_by_chemists (sr_no, user_id, lab_name, name_chemist, sample_type_desc, project_sample, check_count, check_apex_count, challenged_count, ilc_count, research_count, retesting_count,other_private_sample,smpl_analysed_instrn, total_no, report_date) 
                    VALUES (
                    '$i','$user_id','$lab_name','$name_chemist', '$sample_type_desc', '$project_sample', '$check_count', '$check_apex_count', '$challenged_count', '$ilc_count','$research_count','$retesting_count', '$other_private_sample','$smpl_analysed_instrn', '$total_no', '$report_date')");

                    $update = $con->execute("UPDATE temp_dol_consolidated_reporte_analyzed_by_chemists SET counts = (SELECT COUNT(user_id) FROM temp_dol_consolidated_reporte_analyzed_by_chemists WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
                }

                $record_insert = 1;
            }
        }

        if ($record_insert == 1) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function getDolMonthlyCarryBroughtForward($month, $year, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_dol_monthly_carry_brought_fwd WHERE user_id = '$user_id'");

        $sql = "SELECT mcc.category_code
                FROM dmi_users AS u
                INNER JOIN sample_inward si ON u.id = si.user_code
                INNER JOIN m_sample_allocate msa ON si.org_sample_code = msa.org_sample_code
                INNER JOIN m_commodity_category mcc ON si.category_code = mcc.category_code
                INNER JOIN dmi_ro_offices AS mlc ON mlc.id=u.posted_ro_office
                INNER JOIN user_role AS ur ON ur.role_name=u.role
                INNER JOIN dmi_user_roles AS r ON u.email=r.user_email_id
                WHERE mlc.id='$ral_lab_no' AND EXTRACT(MONTH
                FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM msa.alloc_date):: INTEGER = '$year' AND u.status = 'active'  
                GROUP BY mcc.category_code";
        $sql = $con->execute($sql);
        $recordNames = $sql->fetchAll('assoc');
        $sql->closeCursor();

        $record_insert = 0;
        foreach ($recordNames as $recordName) {
            $category_code = $recordName['category_code'];
            $query = $con->execute("SELECT mcc.category_name, mcc.category_code,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS bf_count
                    FROM code_decode cd
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '1'
                    INNER JOIN m_sample_allocate msa ON si.org_sample_code = msa.org_sample_code
                    INNER JOIN m_commodity_category mcc ON si.category_code = mcc.category_code
                    WHERE cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year' AND cd.status_flag NOT IN('G','F') AND mcc.category_code = '$category_code'
                    ) AS check_bf_count,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS check_apex_bf_count
                    FROM code_decode cd
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '5'
                    INNER JOIN m_sample_allocate msa ON si.org_sample_code = msa.org_sample_code
                    INNER JOIN m_commodity_category mcc ON si.category_code = mcc.category_code
                    WHERE cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year' AND cd.status_flag NOT IN('G','F') AND mcc.category_code = '$category_code'
                    ) AS check_apex_bf_count,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS challenged_bf_count
                    FROM code_decode cd
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '4'
                    INNER JOIN m_sample_allocate msa ON si.org_sample_code = msa.org_sample_code
                    INNER JOIN m_commodity_category mcc ON si.category_code = mcc.category_code
                    WHERE cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year' AND cd.status_flag NOT IN('G','F') AND mcc.category_code = '$category_code'
                    ) AS challenged_bf_count,
                    (
                    SELECT COUNT(DISTINCT(msa.sample_code)) AS received_count
                    FROM m_sample_allocate msa
                    INNER JOIN sample_inward si ON msa.org_sample_code = si.org_sample_code AND si.sample_type_code = '1' AND msa.category_code = si.category_code
                    WHERE msa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year'  AND mcc.category_code = '$category_code'
                    ) AS check_received_count,
                    (
                    SELECT COUNT(DISTINCT(msa.sample_code)) AS check_apex_received_count
                    FROM m_sample_allocate msa
                    INNER JOIN sample_inward si ON msa.org_sample_code = si.org_sample_code AND si.sample_type_code = '4' AND msa.category_code = si.category_code
                    WHERE msa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year'  AND mcc.category_code = '$category_code'
                    ) AS check_apex_received_count,
                    (
                    SELECT COUNT(DISTINCT(msa.sample_code)) AS challenged_received_count
                    FROM m_sample_allocate msa
                    INNER JOIN sample_inward si ON msa.org_sample_code = si.org_sample_code AND si.sample_type_code = '5' AND msa.category_code = si.category_code
                    WHERE msa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year'  AND mcc.category_code = '$category_code'
                    ) AS challenged_received_count, 
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS analyzed_count_in_month
                    FROM code_decode AS cd
                    INNER JOIN m_sample_allocate AS msa ON msa.sample_code=cd.sample_code
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '1' AND msa.category_code = si.category_code
                    WHERE test_n_r!='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year'  AND mcc.category_code = '$category_code') AS check_analyzed_count_in_month,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS check_apex_analyzed_count_in_month
                    FROM code_decode AS cd
                    INNER JOIN m_sample_allocate AS msa ON msa.sample_code=cd.sample_code
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '4' AND msa.category_code = si.category_code
                    WHERE test_n_r!='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year'  AND mcc.category_code = '$category_code'
                    ) AS check_apex_analyzed_count_in_month,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS challenged_analyzed_count_in_month
                    FROM code_decode AS cd
                    INNER JOIN m_sample_allocate AS msa ON msa.sample_code=cd.sample_code
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '5' AND msa.category_code = si.category_code
                    WHERE test_n_r!='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year'  AND mcc.category_code = '$category_code'
                    ) AS challenged_analyzed_count_in_month,
                    (
                    SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                    FROM dmi_users AS u
                    INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                    INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                    WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                    GROUP BY ral_lab
                    ) AS lab_name
                    
                    FROM dmi_users AS u
                    INNER JOIN sample_inward si ON u.id = si.user_code
                    INNER JOIN m_sample_allocate msa ON msa.org_sample_code = si.org_sample_code
                    INNER JOIN m_commodity_category mcc ON si.category_code = mcc.category_code
                    INNER JOIN dmi_ro_offices AS mlc ON mlc.id=u.posted_ro_office
                    INNER JOIN user_role AS ur ON ur.role_name=u.role
                    INNER JOIN dmi_user_roles AS r ON u.email=r.user_email_id
                    WHERE mlc.id='$ral_lab_no' AND u.status = 'active' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year'    AND mcc.category_code = '$category_code'
                    GROUP BY mcc.category_name, mcc.category_code");
            $records = $query->fetchAll('assoc');
            $query->closeCursor();
            if (!empty($records)) {
                foreach ($records as $record) {
                    $category_name = $record['category_name'];
                    $check_bf_count = $record['check_bf_count'];
                    $check_apex_bf_count = $record['check_apex_bf_count'];
                    $challenged_bf_count = $record['challenged_bf_count'];
                    $check_received_count = $record['check_received_count'];
                    $check_apex_received_count = $record['check_apex_received_count'];
                    $challenged_received_count = $record['challenged_received_count'];
                    $check_analyzed_count_in_month = $record['check_analyzed_count_in_month'];
                    $check_apex_analyzed_count_in_month = $record['check_apex_analyzed_count_in_month'];
                    $challenged_analyzed_count_in_month = $record['challenged_analyzed_count_in_month'];
                    $total_check = $check_bf_count + $check_received_count;
                    $total_check_apex = $check_apex_bf_count + $check_apex_received_count;
                    $total_challenged  = $challenged_bf_count + $challenged_received_count;
                    $carry_check = $total_check - $check_analyzed_count_in_month;
                    $carry_check_apex = $total_check_apex - $check_apex_analyzed_count_in_month;
                    $carry_challenged = $total_challenged - $challenged_analyzed_count_in_month;
                    $lab_name = $record['lab_name'];
                    $date = date("d/m/Y");
                    $i = $i + 1;

                    $insert = $con->execute("INSERT INTO temp_reportico_dol_monthly_carry_brought_fwd (sr_no, user_id, lab_name, category_name, check_bf_count, check_apex_bf_count, challenged_bf_count, check_received_count, check_apex_received_count, challenged_received_count, check_analyzed_count_in_month, check_apex_analyzed_count_in_month, challenged_analyzed_count_in_month, total_check, total_check_apex, total_challenged, carry_check, carry_check_apex, carry_challenged, report_date) 
                    VALUES (
                    '$i','$user_id','$lab_name','$category_name', '$check_bf_count', '$check_apex_bf_count', '$challenged_bf_count', '$check_received_count', '$check_apex_received_count', '$challenged_received_count', '$check_analyzed_count_in_month','$check_apex_analyzed_count_in_month', '$challenged_analyzed_count_in_month', '$total_check','$total_check_apex','$total_challenged', '$carry_check','$carry_check_apex','$carry_challenged', '$date')");

                    $update = $con->execute("UPDATE temp_reportico_dol_monthly_carry_brought_fwd SET counts = (SELECT COUNT(user_id) FROM temp_reportico_dol_monthly_carry_brought_fwd WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
                }
                $record_insert = 1;
            }
        }

        if ($record_insert == 1) {
            return 1;
        } else {
            return 0;
        }
    }
    //update on report on 01-09-2022 by shreeya
    public static function getDolChemAnnMprDivisionWise($month, $year, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_dol_info_mpr_division WHERE user_id = '$user_id'");

        $sql = "SELECT sa.alloc_to_user_code, sa.commodity_code, mcc.category_code, mst.sample_type_code
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN m_commodity_category mcc ON si.category_code = mcc.category_code
                WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'
                GROUP BY sa.alloc_to_user_code, sa.commodity_code,mcc.category_code, mst.sample_type_code";

        $sql = $con->execute($sql);
        $recordNames = $sql->fetchAll('assoc');
        $sql->closeCursor();

        $record_insert = 0;
        foreach ($recordNames as $recordName) {
            $user_code = $recordName['alloc_to_user_code'];
            $commodity_code = $recordName['commodity_code'];
            $sample_type = $recordName['sample_type_code'];
            $category_code = $recordName['category_code'];
            $query = $con->execute("SELECT mc.commodity_name,CONCAT(u.f_name,' ',u.l_name,' (',u.role,') ') AS  name_chemist,si.remark,mst.sample_type_desc,mcc.category_name,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS bf_count
                    FROM code_decode AS cd
                    INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                    WHERE cd.lab_code='$ral_lab_no' AND cd.status_flag='G' AND EXTRACT(MONTH
                    FROM si.received_date):: INTEGER = '$month' AND si.user_code = '$user_code'
                    ) AS bf_count,
                    (
                    SELECT COUNT(*) AS allotment_count
                    FROM code_decode AS cd
                    INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                    INNER JOIN m_sample_allocate AS msa ON cd.org_sample_code=msa.org_sample_code
                    WHERE cd.status_flag in ('N','C','G') AND cd.lab_code='$ral_lab_no'
                    AND EXTRACT(MONTH
                    FROM si.received_date):: INTEGER = '$month'
                    ) AS allotment_count,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS bf_count
                    FROM code_decode cd
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '1'
                    INNER JOIN m_sample_allocate msa ON si.org_sample_code = msa.org_sample_code
                    WHERE cd.lab_code='$ral_lab_no' AND cd.status_flag NOT IN('G','F') AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year' AND si.user_code = '$user_code'
                    ) AS check_bf_count,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS check_apex_bf_count
                    FROM code_decode cd
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '5'
                    INNER JOIN m_sample_allocate msa ON si.org_sample_code = msa.org_sample_code
                    WHERE cd.lab_code='$ral_lab_no' AND cd.status_flag NOT IN('G','F') AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year'  AND si.user_code = '$user_code'
                    ) AS check_apex_bf_count,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS challenged_bf_count
                    FROM code_decode cd
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '4'
                    INNER JOIN m_sample_allocate msa ON si.org_sample_code = msa.org_sample_code
                    WHERE cd.lab_code='$ral_lab_no' AND cd.status_flag NOT IN('G','F')AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year'   AND si.user_code = '$user_code'
                    ) AS challenged_bf_count,
                    (
                    SELECT COUNT(DISTINCT(msa.sample_code)) AS received_count
                    FROM m_sample_allocate msa
                    INNER JOIN sample_inward si ON msa.org_sample_code = si.org_sample_code AND si.sample_type_code = '1'
                    WHERE msa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year' AND msa.alloc_to_user_code = '$user_code'
                    ) AS check_received_count,
                    (
                    SELECT COUNT(DISTINCT(msa.sample_code)) AS check_apex_received_count
                    FROM m_sample_allocate msa
                    INNER JOIN sample_inward si ON msa.org_sample_code = si.org_sample_code AND si.sample_type_code = '4'
                    WHERE msa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year' AND msa.alloc_to_user_code = '$user_code'
                    ) AS check_apex_received_count,
                    (
                    SELECT COUNT(DISTINCT(msa.sample_code)) AS challenged_received_count
                    FROM m_sample_allocate msa
                    INNER JOIN sample_inward si ON msa.org_sample_code = si.org_sample_code AND si.sample_type_code = '5'
                    WHERE msa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year' AND msa.alloc_to_user_code = '$user_code'
                    ) AS challenged_received_count,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS analyzed_count_in_month
                    FROM code_decode AS cd
                    INNER JOIN m_sample_allocate AS msa ON msa.sample_code=cd.sample_code
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '1' AND msa.commodity_code = si.commodity_code
                    WHERE test_n_r!='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year' AND msa.commodity_code = '$commodity_code'AND msa.alloc_to_user_code = '$user_code'
                    ) AS check_analyzed_count_in_month,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS check_apex_analyzed_count_in_month
                    FROM code_decode AS cd
                    INNER JOIN m_sample_allocate AS msa ON msa.sample_code=cd.sample_code
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '4' AND msa.commodity_code = si.commodity_code
                    WHERE test_n_r!='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year' AND msa.commodity_code = '$commodity_code'AND msa.alloc_to_user_code = '$user_code'
                    ) AS check_apex_analyzed_count_in_month,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS challenged_analyzed_count_in_month
                    FROM code_decode AS cd
                    INNER JOIN m_sample_allocate AS msa ON msa.sample_code=cd.sample_code
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '5' AND msa.commodity_code = si.commodity_code
                    WHERE test_n_r!='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year' AND msa.commodity_code = '$commodity_code' AND msa.alloc_to_user_code = '$user_code'
                    ) AS challenged_analyzed_count_in_month,
                    (
                    SELECT COUNT(ct.test_code)
                    FROM commodity_test ct
                    INNER JOIN m_sample_allocate AS msa ON msa.commodity_code = ct.commodity_code AND msa.lab_code = '$ral_lab_no'
                    INNER JOIN sample_inward si ON msa.org_sample_code = si.org_sample_code AND msa.commodity_code = si.commodity_code AND si.sample_type_code IN (1,4)
                    WHERE ct.commodity_code = $commodity_code AND msa.alloc_to_user_code = '$user_code' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year'
                    ) AS no_of_param,
                    (
                    SELECT COUNT(atd.alloc_to_user_code) FROM actual_test_data atd
                    INNER JOIN m_sample_allocate msa ON msa.alloc_to_user_code = atd.alloc_to_user_code AND msa.chemist_code = atd.chemist_code
                    INNER JOIN sample_inward si ON msa.org_sample_code = si.org_sample_code AND si.sample_type_code IN (1,4)
                    WHERE atd.lab_code = '$ral_lab_no' AND msa.commodity_code = '$commodity_code' AND msa.alloc_to_user_code = '$user_code' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year' 
                    ) AS no_of_para_analys,
                    (
                    SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                    FROM dmi_users AS u
                    INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                    INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                    WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                    GROUP BY ral_lab
                    ) AS lab_name

                    FROM sample_inward AS si
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code
                    INNER JOIN m_commodity_category mcc ON si.category_code = mcc.category_code 
                    INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                    INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                    INNER JOIN dmi_ro_offices AS ml ON ml.id=sa.lab_code
                    INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                    WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND sa.alloc_to_user_code = '$user_code'
                    GROUP BY sa.alloc_to_user_code,mc.commodity_name,mcc.category_name,u.f_name,u.l_name,u.role,si.remark ,mst.sample_type_desc
                    ORDER BY mc.commodity_name ASC ");
            $records = $query->fetchAll('assoc');
            $query->closeCursor();
            if (!empty($records)) {
                foreach ($records as $record) {
                    $commodity_name = $record['commodity_name'];
                    $bf_count = $record['bf_count'];
                    $user_code = $record['allotment_count'];
                    $check_bf_count = $record['check_bf_count'];
                    $check_apex_bf_count = $record['check_apex_bf_count'];
                    $challenged_bf_count = $record['challenged_bf_count'];
                    $check_received_count = $record['check_received_count'];
                    $check_apex_received_count = $record['check_apex_received_count'];
                    $challenged_received_count = $record['challenged_received_count'];
                    $check_analyzed_count_in_month = $record['check_analyzed_count_in_month'];
                    $check_apex_analyzed_count_in_month = $record['check_apex_analyzed_count_in_month'];
                    $challenged_analyzed_count_in_month = $record['challenged_analyzed_count_in_month'];
                    $total_check = $check_bf_count + $check_received_count;
                    $total_check_apex = $check_apex_bf_count + $check_apex_received_count;
                    $total_challenged  = $challenged_bf_count + $challenged_received_count;
                    $carry_check = $total_check - $check_analyzed_count_in_month;
                    $carry_check_apex = $total_check_apex - $check_apex_analyzed_count_in_month;
                    $carry_challenged = $total_challenged - $challenged_analyzed_count_in_month;
                    $name_chemist = $record['name_chemist'];
                    $no_of_param = $record['no_of_param'];
                    $no_of_para_analys = $record['no_of_para_analys'];
                    $remark = $record['remark'];
                    $lab_name = $record['lab_name'];
                    $date = date("d/m/Y");
                    $i = $i + 1;

                    $insert = $con->execute("INSERT INTO temp_reportico_dol_info_mpr_division (sr_no, user_id, lab_name,category_name, commodity_name,sample_type_desc,bf_count,allotment_count, check_bf_count, check_apex_bf_count, challenged_bf_count, check_received_count, check_apex_received_count, challenged_received_count, check_analyzed_count_in_month, check_apex_analyzed_count_in_month, challenged_analyzed_count_in_month, total_check, total_check_apex, total_challenged, carry_check, carry_check_apex, carry_challenged, name_chemist, no_of_param, no_of_para_analys, remark, report_date) 
                    VALUES (
                    '$i','$user_id','$lab_name','$category_name','$commodity_name','$sample_type_desc','$bf_count','$user_code', '$check_bf_count', '$check_apex_bf_count', '$challenged_bf_count', '$check_received_count', '$check_apex_received_count', '$challenged_received_count', '$check_analyzed_count_in_month','$check_apex_analyzed_count_in_month', '$challenged_analyzed_count_in_month', '$total_check','$total_check_apex','$total_challenged', '$carry_check','$carry_check_apex','$carry_challenged','$name_chemist','$no_of_param', '$no_of_para_analys','$remark', '$date')");

                    $update = $con->execute("UPDATE temp_reportico_dol_info_mpr_division SET counts = (SELECT COUNT(user_id) FROM temp_reportico_dol_info_mpr_division WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
                }

                $record_insert = 1;
            }
        }

        if ($record_insert == 1) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function getDolDetailsSampleAnalyzedByRal($month, $year, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_dol_details_sample_analyzed_ral WHERE user_id = '$user_id'");

        $sql = "SELECT sa.commodity_code
                FROM sample_inward AS si
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month'AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' ";

        $sql = $con->execute($sql);
        $recordNames = $sql->fetchAll('assoc');
        $sql->closeCursor();

        $record_insert = 0;
        foreach ($recordNames as $recordName) {
            $commodity_code = $recordName['commodity_code'];

            $query = $con->execute("SELECT mc.commodity_name,  si.org_sample_code, mst.sample_type_desc,
                        (
                        SELECT COUNT(cd.sample_code)
                        FROM code_decode cd
                        INNER JOIN m_sample_allocate AS msa ON msa.sample_code=cd.sample_code
                        INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '1' AND msa.commodity_code = si.commodity_code
                        WHERE test_n_r!='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                        FROM msa.alloc_date):: INTEGER = '$month'AND EXTRACT(YEAR
                        FROM msa.alloc_date):: INTEGER = '$year' AND msa.commodity_code = '$commodity_code'
                        ) AS commodity_counts,
                        (
                        SELECT COUNT(cd.sample_code)
                        FROM code_decode cd
                        INNER JOIN m_sample_allocate AS msa ON msa.sample_code=cd.sample_code
                        INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '7' AND msa.commodity_code = si.commodity_code
                        WHERE cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                        FROM msa.alloc_date):: INTEGER = '$month'AND EXTRACT(YEAR
                        FROM msa.alloc_date):: INTEGER = '$year'
                        ) AS inter_lab_compare,
                        (
                        SELECT COUNT(cd.sample_code)
                        FROM code_decode cd
                        INNER JOIN m_sample_allocate AS msa ON msa.sample_code=cd.sample_code
                        INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '0' AND msa.commodity_code = si.commodity_code
                        WHERE cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                        FROM msa.alloc_date):: INTEGER = '$month'AND EXTRACT(YEAR
                        FROM msa.alloc_date):: INTEGER = '$year'
                        ) AS pvt_sample,
                        (
                        SELECT COUNT(cd.sample_code)
                        FROM code_decode cd
                        INNER JOIN m_sample_allocate AS msa ON msa.sample_code=cd.sample_code
                        INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '0' AND msa.commodity_code = si.commodity_code
                        WHERE cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                        FROM msa.alloc_date):: INTEGER = '$month'AND EXTRACT(YEAR
                        FROM msa.alloc_date):: INTEGER = '$year'
                        ) AS inter_check,
                        (
                        SELECT COUNT(cd.sample_code)
                        FROM code_decode cd
                        INNER JOIN m_sample_allocate AS msa ON msa.sample_code=cd.sample_code
                        INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '0' AND msa.commodity_code = si.commodity_code
                        WHERE cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                        FROM msa.alloc_date):: INTEGER = '$month'AND EXTRACT(YEAR
                        FROM msa.alloc_date):: INTEGER = '$year'
                        ) AS proj_sample,
                        (
                        SELECT COUNT(cd.sample_code)
                        FROM code_decode cd
                        INNER JOIN m_sample_allocate AS msa ON msa.sample_code=cd.sample_code
                        INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '0' AND msa.commodity_code = si.commodity_code
                        WHERE cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                        FROM msa.alloc_date):: INTEGER = '$month'AND EXTRACT(YEAR
                        FROM msa.alloc_date):: INTEGER = '$year'
                        ) AS repeat_sample,
                        (
                        SELECT COUNT(cd.sample_code)
                        FROM code_decode cd
                        INNER JOIN m_sample_allocate AS msa ON msa.sample_code=cd.sample_code
                        INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '0' AND msa.commodity_code = si.commodity_code
                        WHERE cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                        FROM msa.alloc_date):: INTEGER = '$month'AND EXTRACT(YEAR
                        FROM msa.alloc_date):: INTEGER = '$year'
                        ) AS pt_samp,        
                        (
                        SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                        FROM dmi_users AS u
                        INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                        INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                        WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                        GROUP BY ral_lab
                        ) AS lab_name

                        FROM sample_inward AS si
                        INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code
                        INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                        INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                        INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                        INNER JOIN dmi_ro_offices AS ml ON ml.id=sa.lab_code
                        INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                        
                        WHERE sa.lab_code='$ral_lab_no'  AND EXTRACT(MONTH
                        FROM sa.alloc_date):: INTEGER = '$month'AND EXTRACT(YEAR
                        FROM sa.alloc_date):: INTEGER = '$year'
                        GROUP BY mc.commodity_name, mst.sample_type_desc, si.org_sample_code, si.received_date
                        ORDER BY si.received_date ASC");

            $records = $query->fetchAll('assoc');
            $query->closeCursor();
            if (!empty($records)) {
                foreach ($records as $record) {
                    $commodity_name = $record['commodity_name'];
                    $org_sample_code = $record['org_sample_code'];
                    $commodity_counts = $record['commodity_counts'];
                    $inter_lab_compare = $record['inter_lab_compare'];
                    // $pvt_sample = $record['pvt_sample'];
                    // $inter_check = $record['inter_check'];
                    // $proj_sample = $record['proj_sample'];
                    // $repeat_sample = $record['repeat_sample'];
                    // $pt_samp = $record['pt_samp'];

                    $pvt_sample = 'NA';
                    $inter_check = 'NA';
                    $proj_sample = 'NA';
                    $repeat_sample = 'NA';
                    $pt_samp = 'NA';

                    $sample_type_desc = $record['sample_type_desc'];
                    $lab_name = $record['lab_name'];
                    $date = date("d/m/Y");
                    $i = $i + 1;

                    $insert = $con->execute("INSERT INTO temp_reportico_dol_details_sample_analyzed_ral (sr_no, user_id, lab_name,org_sample_code, sample_type_desc, commodity_name, commodity_counts, inter_lab_compare, pvt_sample, inter_check, proj_sample, repeat_sample, pt_samp, report_date) 
                    VALUES (
                    '$i','$user_id','$lab_name','$org_sample_code', '$sample_type_desc', '$commodity_name', '$commodity_counts', '$inter_lab_compare', '$pvt_sample', '$inter_check', '$proj_sample','$repeat_sample','$pt_samp', '$date')");

                    $update = $con->execute("UPDATE temp_reportico_dol_details_sample_analyzed_ral SET counts = (SELECT COUNT(user_id) FROM temp_reportico_dol_details_sample_analyzed_ral WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
                }

                $record_insert = 1;
            }
        }

        if ($record_insert == 1) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function getDolBifercationRal($month, $year, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_dol_bifercation_ral WHERE user_id = '$user_id'");

        $sql = "SELECT sa.alloc_to_user_code
                FROM sample_inward AS si
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND u.role IN ('Jr Chemist','Sr Chemist','Cheif Chemist')";

        $sql = $con->execute($sql);
        $recordNames = $sql->fetchAll('assoc');
        $sql->closeCursor();

        $record_insert = 0;
        foreach ($recordNames as $recordName) {
            $user_code = $recordName['alloc_to_user_code'];

            $query = $con->execute("SELECT sa.alloc_to_user_code,sa.recby_ch_date, sa.commencement_date,'NA' AS other,'Yes' AS norms, '$month' AS months,CONCAT(u.f_name,' ',u.l_name,' (',u.role,') ') AS name_chemist,
                    (
                    SELECT COUNT(si.sample_type_code)
                    FROM sample_inward AS si
                    INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 1
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                    WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND sa.alloc_to_user_code = '$user_code'
                    ) AS check_count,
                    (
                    SELECT COUNT(si.sample_type_code)
                    FROM sample_inward AS si
                    INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 0
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                    WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND sa.alloc_to_user_code = '$user_code'
                    ) AS pvt_count,
                    (
                    SELECT COUNT(si.sample_type_code)
                    FROM sample_inward AS si
                    INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 2
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                    WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND sa.alloc_to_user_code = '$user_code'
                    ) AS research_count,
                    (
                    SELECT COUNT(si.sample_type_code)
                    FROM sample_inward AS si
                    INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 7
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                    WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND sa.alloc_to_user_code = '$user_code'
                    ) AS ilc_count,
                    (
                    SELECT COUNT(si.sample_type_code)
                    FROM sample_inward AS si
                    INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 0
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                    WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND sa.alloc_to_user_code = '$user_code'
                    ) AS internal_check_count,
                    (
                    SELECT count(w.org_sample_code) 
                    FROM workflow w 
                    WHERE w.dst_loc_id = $ral_lab_no AND EXTRACT(MONTH
                    FROM w.tran_date):: INTEGER = '$month'AND EXTRACT(YEAR
                    FROM w.tran_date):: INTEGER = '$year' AND w.user_code = '$user_code'
                    ) AS sample_frm_cal,
                    (
                    SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                    FROM dmi_users AS u
                    INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                    INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                    WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                    GROUP BY ral_lab
                    ) AS lab_name

                    FROM sample_inward AS si
                    INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                    INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                    INNER JOIN dmi_ro_offices AS ml ON ml.id=sa.lab_code
                    INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                    WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND sa.alloc_to_user_code = '$user_code'
                    GROUP BY u.f_name,u.l_name, u.role, sa.alloc_to_user_code,sa.recby_ch_date, sa.commencement_date");

            $records = $query->fetchAll('assoc');
            $query->closeCursor();
            if (!empty($records)) {
                foreach ($records as $record) {
                    $other = $record['other'];
                    $norms = $record['norms'];
                    $name_chemist = $record['name_chemist'];
                    $check_count = $record['check_count'];
                    $pvt_count = $record['pvt_count'];
                    $research_count = $record['research_count'];
                    $ilc_count = $record['ilc_count'];
                    $internal_check_count = $record['internal_check_count'];
                    $sample_frm_cal = $record['sample_frm_cal'];
                    $total = $check_count + $pvt_count + $research_count + $ilc_count + $internal_check_count + $sample_frm_cal;
                    $lab_name = $record['lab_name'];
                    $date = date("d/m/Y");
                    $i = $i + 1;
                    $recby_ch_date = $record['recby_ch_date'];
                    $commencement_date = $record['commencement_date'];
                    if ($recby_ch_date == '' || $commencement_date == '') {
                        $working_days = '0';
                    } else {
                        $diff = abs(strtotime($commencement_date) - strtotime($recby_ch_date));
                        $years = floor($diff / (365 * 60 * 60 * 24));
                        $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
                        $working_days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
                    }

                    $insert = $con->execute("INSERT INTO temp_reportico_dol_bifercation_ral (sr_no, user_id, lab_name,other, norms, name_chemist, check_count, pvt_count, research_count, ilc_count, internal_check_count, sample_frm_cal, total, working_days, report_date) 
                    VALUES (
                    '$i','$user_id','$lab_name','$other', '$norms', '$name_chemist', '$check_count', '$pvt_count', '$research_count', '$ilc_count', '$internal_check_count','$sample_frm_cal','$total','$working_days','$date')");

                    $update = $con->execute("UPDATE temp_reportico_dol_bifercation_ral SET counts = (SELECT COUNT(user_id) FROM temp_reportico_dol_bifercation_ral WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
                }

                $record_insert = 1;
            }
        }

        if ($record_insert == 1) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function getDolMonthChekPendRal($month, $year, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_dol_month_chk_pend_ral WHERE user_id = '$user_id'");

        $sql = "SELECT sa.alloc_to_user_code, sa.commodity_code
                FROM sample_inward AS si
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND u.role IN ('Jr Chemist','Sr Chemist','Cheif Chemist')";

        $sql = $con->execute($sql);
        $recordNames = $sql->fetchAll('assoc');
        $sql->closeCursor();

        $record_insert = 0;
        foreach ($recordNames as $recordName) {
            $user_code = $recordName['alloc_to_user_code'];
            $commodity_code = $recordName['commodity_code'];

            $query = $con->execute("SELECT sa.alloc_to_user_code, sa.commodity_code,si.remark, CONCAT(u.f_name,' ',u.l_name,' (',u.role,') ') AS name_chemist,  mc.commodity_name,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS bf_count
                    FROM code_decode AS cd
                    INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                    INNER JOIN m_sample_allocate sa ON si.org_sample_code = sa.org_sample_code
                    WHERE cd.lab_code='$ral_lab_no' AND cd.status_flag NOT IN('G','F') AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND si.sample_type_code='1' AND cd.alloc_to_user_code='$user_code'
                    ) AS bf_count,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS allotment_count
                    FROM code_decode AS cd
                    INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                    INNER JOIN m_sample_allocate sa ON si.org_sample_code = sa.org_sample_code
                    WHERE cd.status_flag in ('N','C','G') AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND cd.lab_code='$ral_lab_no' AND si.sample_type_code='1' AND cd.alloc_to_user_code='$user_code'
                    ) AS allotment_count,
                    (
                    SELECT COUNT(sa.org_sample_code)
                    FROM m_sample_allocate sa
                    INNER JOIN sample_inward si ON si.org_sample_code = sa.org_sample_code
                    WHERE EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND sa.lab_code='$ral_lab_no' AND si.sample_type_code='1' AND sa.alloc_to_user_code='$user_code' AND sa.commodity_code = '$commodity_code'
                    ) AS check_analyze_commodity,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS pending_count
                    FROM code_decode AS cd
                    INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                    WHERE cd.status_flag='N' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND cd.alloc_to_user_code='$user_code' AND cd.lab_code='$ral_lab_no'
                    ) AS pending_count,
                    (
                    SELECT COUNT(atd.alloc_to_user_code) FROM actual_test_data atd
                    INNER JOIN m_sample_allocate msa ON msa.alloc_to_user_code = atd.alloc_to_user_code AND msa.chemist_code = atd.chemist_code
                    INNER JOIN sample_inward si ON msa.org_sample_code = si.org_sample_code
                    INNER JOIN commodity_test ct ON atd.test_code = ct.test_code
                    WHERE atd.lab_code = '$ral_lab_no' AND ct.commodity_code = '$commodity_code' AND msa.alloc_to_user_code = '$user_code' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year' 
                    ) AS no_of_para_analys,
                    (
                    SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                    FROM dmi_users AS u
                    INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                    INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                    WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                    GROUP BY ral_lab
                    ) AS lab_name
                    
                    FROM sample_inward AS si
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                    inner join m_commodity AS mc ON sa.commodity_code = mc.commodity_code
                    WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND sa.alloc_to_user_code = '$user_code'
                    GROUP BY sa.alloc_to_user_code, sa.commodity_code,si.remark,u.f_name,u.l_name,u.role, mc.commodity_name, sa.alloc_date");

            $records = $query->fetchAll('assoc');
            $query->closeCursor();
            if (!empty($records)) {
                foreach ($records as $record) {
                    $remark = $record['remark'];
                    $name_chemist = $record['name_chemist'];
                    $no_of_parameter = $record['no_of_para_analys'];
                    $bf_count = $record['bf_count'];
                    $allotment_count = $record['allotment_count'];
                    $check_analyze_commodity = $record['check_analyze_commodity'];
                    $pending_count = $record['pending_count'];
                    $reason = "NA";;
                    $total = $bf_count + $allotment_count + $check_analyze_commodity;
                    $lab_name = $record['lab_name'];
                    $commodity_name = $record['commodity_name'];
                    $date = date("d/m/Y");
                    $i = $i + 1;


                    $insert = $con->execute("INSERT INTO temp_reportico_dol_month_chk_pend_ral (sr_no, user_id, lab_name,remark, name_chemist, no_of_parameter, bf_count, allotment_count, check_analyze_commodity, pending_count, reason, commodity_name, total, report_date) 
                    VALUES (
                    '$i','$user_id','$lab_name','$remark', '$name_chemist', '$no_of_parameter', '$bf_count', '$allotment_count', '$check_analyze_commodity', '$pending_count','$reason','$commodity_name','$total','$date')");

                    $update = $con->execute("UPDATE temp_reportico_dol_month_chk_pend_ral SET counts = (SELECT COUNT(user_id) FROM temp_reportico_dol_month_chk_pend_ral WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
                }

                $record_insert = 1;
            }
        }

        if ($record_insert == 1) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function getDolCommodityWiseSampleRalAnnxeure($month, $years, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        if ($month == 02) {
            $month_one = 12;
            $month_two = $month - 1;
        } else if ($month == 01) {
            $month_one = 11;
            $month_two = 12;
        } else {
            $month_one = $month - 2;
            $month_two = $month - 1;
        }
        if (strlen($month_one) == 1) {
            $month_one = '0' . $month_one;
        }
        if (strlen($month_two) == 1) {
            $month_two = '0' . $month_two;
        }
        if ($month_one == 11 || $month_one == 12 || $month_two == 12) {
            $year_new = $years - 1;
        }
        if ($month_one != 11 || $month_one != 12 || $month_two != 12) {
            $year = $years;
        }

        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_dol_commo_wise_sample_ral_annexure WHERE user_id = '$user_id'");

        $main_sql = "SELECT sa.commodity_code
                FROM sample_inward AS si
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month'  AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' GROUP BY sa.commodity_code";

        $main_sql = $con->execute($main_sql);
        $recordNames = $main_sql->fetchAll('assoc');
        $main_sql->closeCursor();

        $record_insert = 0;
        foreach ($recordNames as $recordName) {
            $commodity_code = $recordName['commodity_code'];

            $sql = "SELECT sa.commodity_code, mc.commodity_name, mgd.grade_desc, ";

            if ($month_one == 11 ||  $month_two == 12) {
                $sql .= " CASE when sa.lab_code='$ral_lab_no' AND (EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_one' OR EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_two') AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year_new' AND w.stage_smpl_flag= 'FG' AND sa.commodity_code = '$commodity_code' AND mgd.grade_code NOT IN(348,370) then mgd.grade_desc
                    end  as conformed_std,
            
                    CASE when sa.lab_code='$ral_lab_no' AND (EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_one' OR EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_two') AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year_new' AND w.stage_smpl_flag= 'FG' AND sa.commodity_code = '$commodity_code' AND mgd.grade_code IN(348,370) then mgd.grade_desc
                    end as misgrd, 

                    (SELECT COUNT(DISTINCT(cd.sample_code)) AS bf_count
                    FROM code_decode AS cd
                    INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                    INNER JOIN m_sample_allocate sa ON si.org_sample_code = sa.org_sample_code
                    WHERE cd.lab_code='$ral_lab_no' AND cd.status_flag NOT IN('G','F') AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_one' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year_new' AND sa.commodity_code='$commodity_code') AS bf_count, 
                    
                    (SELECT COUNT(DISTINCT(sa.sample_code)) AS received_count
                    FROM m_sample_allocate sa
                    WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_two' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year_new' AND sa.commodity_code='$commodity_code') AS received_count,
                    
                    (SELECT COUNT(DISTINCT(cd.sample_code)) AS analyzed_count_one
                    FROM code_decode AS cd
                    INNER JOIN m_sample_allocate AS sa ON sa.sample_code=cd.sample_code
                    WHERE test_n_r!='R' AND cd.lab_code='$ral_lab_no' AND (EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_one' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_two') AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year_new' AND sa.commodity_code='$commodity_code') AS sample_analyze, ";
            } else if ($month_one == 12) {
                $sql .= " CASE when sa.lab_code='$ral_lab_no' AND (EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_one' OR EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_two') AND (EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year_new' OR EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year') AND w.stage_smpl_flag= 'FG' AND sa.commodity_code = '$commodity_code' AND mgd.grade_code NOT IN(348,370) then mgd.grade_desc
                    end  as conformed_std,
            
                    CASE when sa.lab_code='$ral_lab_no' AND (EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_one' OR EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_two') AND (EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year_new' OR EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year') AND w.stage_smpl_flag= 'FG' AND sa.commodity_code = '$commodity_code' AND mgd.grade_code IN(348,370) then mgd.grade_desc
                    end as misgrd,

                    (SELECT COUNT(DISTINCT(cd.sample_code)) AS bf_count
                    FROM code_decode AS cd
                    INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                    INNER JOIN m_sample_allocate sa ON si.org_sample_code = sa.org_sample_code
                    WHERE cd.lab_code='$ral_lab_no' AND cd.status_flag NOT IN('G','F') AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_one' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year_new' AND sa.commodity_code='$commodity_code') AS bf_count, 
                    
                    (SELECT COUNT(DISTINCT(sa.sample_code)) AS received_count
                    FROM m_sample_allocate sa
                    WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_two' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND sa.commodity_code='$commodity_code') AS received_count,
                    
                    (SELECT COUNT(DISTINCT(cd.sample_code)) AS analyzed_count_one
                    FROM code_decode AS cd
                    INNER JOIN m_sample_allocate AS sa ON sa.sample_code=cd.sample_code
                    WHERE test_n_r!='R' AND cd.lab_code='$ral_lab_no' AND (EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_one' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_two') AND (EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year_new' OR EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year') AND sa.commodity_code='$commodity_code') AS sample_analyze, ";
            } else {
                $sql .= " CASE when sa.lab_code='$ral_lab_no' AND (EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month_one' OR EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month_two') AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND w.stage_smpl_flag= 'FG' AND sa.commodity_code = '$commodity_code' AND mgd.grade_code NOT IN(348,370) then mgd.grade_desc
                end  as conformed_std,
        
                CASE when sa.lab_code='$ral_lab_no' AND (EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month_one' OR EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month_two') AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND w.stage_smpl_flag= 'FG' AND sa.commodity_code = '$commodity_code' AND mgd.grade_code IN(348,370) then mgd.grade_desc
                end as misgrd,

                (SELECT COUNT(DISTINCT(cd.sample_code)) AS bf_count
                FROM code_decode AS cd
                INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                INNER JOIN m_sample_allocate sa ON si.org_sample_code = sa.org_sample_code
                WHERE cd.lab_code='$ral_lab_no' AND cd.status_flag NOT IN('G','F') AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month_one' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND sa.commodity_code='$commodity_code') AS bf_count, 
                
                (SELECT COUNT(DISTINCT(sa.sample_code)) AS received_count
                FROM m_sample_allocate sa
                WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month_two' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND sa.commodity_code='$commodity_code') AS received_count,
                
                (SELECT COUNT(DISTINCT(cd.sample_code)) AS analyzed_count_one
                FROM code_decode AS cd
                INNER JOIN m_sample_allocate AS sa ON sa.sample_code=cd.sample_code
                WHERE test_n_r!='R' AND cd.lab_code='$ral_lab_no' AND (EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month_one' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month_two') AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND sa.commodity_code='$commodity_code') AS sample_analyze, ";
            }

            $sql .= " ( SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab) AS lab_name
                
                FROM sample_inward AS si
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                INNER JOIN workflow w ON si.org_sample_code = w.org_sample_code
                INNER JOIN m_grade_desc mgd ON mgd.grade_code = si.grade
                INNER JOIN m_commodity AS mc ON sa.commodity_code = mc.commodity_code
                WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND sa.commodity_code = '$commodity_code'
                GROUP BY sa.commodity_code, mc.commodity_name, mgd.grade_desc,sa.lab_code, sa.alloc_date,w.stage_smpl_flag,mgd.grade_code";

            $query = $con->execute($sql);
            $records = $query->fetchAll('assoc');
            $query->closeCursor();
            if (!empty($records)) {
                foreach ($records as $record) {
                    $bf_count = $record['bf_count'];
                    $received_count = $record['received_count'];
                    $sample_analyze = $record['sample_analyze'];
                    $total = $bf_count + $received_count;
                    $conformed_std = $record['conformed_std'];
                    $misgrade = $record['misgrd'];
                    $cf_month = $total - $sample_analyze;
                    $lab_name = $record['lab_name'];
                    $commodity_name = $record['commodity_name'];
                    $date = date("d/m/Y");
                    $i = $i + 1;
                    if($conformed_std == ''){
                        $conformed_std = 'NULL';
                    }
                    if($misgrade == '')
                    {
                        $misgrade = "NULL";
                    }

                    $insert = $con->execute("INSERT INTO temp_reportico_dol_commo_wise_sample_ral_annexure (sr_no, user_id, lab_name, commodity_name, bf_count, received_count, sample_analyze, total, conformed_std, misgrade, cf_month, report_date) 
                    VALUES (
                    '$i','$user_id','$lab_name', '$commodity_name','$bf_count', '$received_count', '$sample_analyze', '$total','$conformed_std','$misgrade','$cf_month','$date')");

                    $update = $con->execute("UPDATE temp_reportico_dol_commo_wise_sample_ral_annexure SET counts = (SELECT COUNT(user_id) FROM temp_reportico_dol_commo_wise_sample_ral_annexure WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
                }

                $record_insert = 1;
            }
        }

        if ($record_insert == 1) {
            return 1;
        } else {
            return 0;
        }
    }


    public static function getDolStatementChkBfCfSample($month, $year, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_dol_smt_chk_bf_cf_sample WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT u.role, si.remark,
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS bf_count
                FROM code_decode AS cd
                INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                INNER JOIN m_sample_allocate sa ON si.org_sample_code = sa.org_sample_code
                WHERE cd.lab_code='$ral_lab_no' AND cd.status_flag NOT IN('G','F') AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND si.sample_type_code='1'
                ) AS bf_count,
                (
                SELECT COUNT(DISTINCT(sa.sample_code)) AS received_count
                FROM m_sample_allocate sa
                INNER JOIN sample_inward si ON sa.org_sample_code = si.org_sample_code AND si.sample_type_code = '1'
                WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'
                ) AS received_count,
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS analyzed_count_one
                FROM code_decode AS cd
                INNER JOIN m_sample_allocate AS sa ON sa.sample_code=cd.sample_code
                WHERE test_n_r !='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'
                ) AS analyzed_count_original,
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS analyzed_count_two
                FROM code_decode AS cd
                INNER JOIN m_sample_allocate AS sa ON sa.sample_code=cd.sample_code
                WHERE test_n_r='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'
                ) AS analyzed_count_duplicate,
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS analyzed_count_two
                FROM code_decode AS cd
                INNER JOIN m_sample_allocate AS sa ON sa.sample_code=cd.sample_code
                WHERE test_n_r='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'
                ) AS analyzed_count_repeat,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name
                
                FROM sample_inward AS si
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                INNER JOIN m_commodity AS mc ON sa.commodity_code = mc.commodity_code
                WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND u.role IN ('Jr Chemist','Sr Chemist','Cheif Chemist')
                GROUP BY si.remark,u.role");
       
        $records = $query->fetchAll('assoc');
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $bf_count = $record['bf_count'];
                $received_count = $record['received_count'];
                $role = $record['role'];
                $total = $bf_count + $received_count;
                $analyzed_count_original = $record['analyzed_count_original'];
                $analyzed_count_duplicate = $record['analyzed_count_duplicate'];
                $analyzed_count_repeat = $record['analyzed_count_repeat'];
                $carray_forward = $total - $analyzed_count_original;
                $sancationed_strength = 'NA';
                $staff_strength = 'NA';
                $lab_name = $record['lab_name'];
                $remark = $record['remark'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_dol_smt_chk_bf_cf_sample (sr_no, user_id, lab_name, role, bf_count, received_count, total, analyzed_count_original, analyzed_count_duplicate, analyzed_count_repeat, carry_forward, sancationed_strength, staff_strength, remark, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$role','$bf_count', '$received_count', '$total','$analyzed_count_original','$analyzed_count_duplicate','$analyzed_count_repeat','$carray_forward','$sancationed_strength','$staff_strength','$remark','$date')");

                $update = $con->execute("UPDATE temp_reportico_dol_smt_chk_bf_cf_sample SET counts = (SELECT COUNT(user_id) FROM temp_reportico_dol_smt_chk_bf_cf_sample WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getDolTimeTakenReport($month, $year, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_dol_time_taken_report WHERE user_id = '$user_id'");

        $sql = "SELECT sa.commodity_code
                FROM sample_inward AS si
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month'  AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' GROUP BY sa.commodity_code";

        $sql = $con->execute($sql);
        $recordNames = $sql->fetchAll('assoc');
        $sql->closeCursor();

        $record_insert = 0;
        foreach ($recordNames as $recordName) {
            $commodity_code = $recordName['commodity_code'];
            $query = $con->execute("SELECT mc.commodity_name,si.received_date,si.dispatch_date,si.remark,si.dispatch_date:: DATE -si.received_date:: DATE AS time_taken,'NA' AS reason,
                    (
                    SELECT COUNT(si.stage_sample_code)
                    FROM sample_inward AS si
                    WHERE EXTRACT(MONTH
                    FROM si.received_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM si.received_date):: INTEGER = '$year' AND si.commodity_code = $commodity_code
                    ) AS sample_count,
                    (
                    SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                    FROM dmi_users AS u
                    INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                    INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                    WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                    GROUP BY ral_lab
                    ) AS lab_name
                    FROM sample_inward AS si
                    INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                    INNER JOIN dmi_users AS du ON du.id=si.user_code
                    INNER JOIN user_role AS ur ON ur.role_name=du.role AND EXTRACT(MONTH
                    FROM si.received_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM si.received_date):: INTEGER = '$year' AND si.commodity_code = $commodity_code
                    GROUP BY si.stage_sample_code,mc.commodity_name,si.received_date,si.dispatch_date,si.commodity_code,si.remark");
           
            $records = $query->fetchAll('assoc');
            $query->closeCursor();
            if (!empty($records)) {
                foreach ($records as $record) {
                    $commodity_name = $record['commodity_name'];
                    $received_date = $record['received_date'];
                    $dispatch_date = $record['dispatch_date'];
                    $remark = $record['remark'];
                    $time_taken = $record['time_taken'];
                    $lab_name = $record['lab_name'];
                    $reason = $record['reason'];
                    $sample_count = $record['sample_count'];
                    $date = date("d/m/Y");
                    $i = $i + 1;

                    $insert = $con->execute("INSERT INTO temp_reportico_dol_time_taken_report (sr_no, user_id, lab_name, commodity_name, received_date, dispatch_date, remark, time_taken, reason, sample_count, report_date) 
                    VALUES (
                    '$i','$user_id','$lab_name', '$commodity_name','$received_date', '$dispatch_date', '$remark', '$time_taken','$reason','$sample_count','$date')");

                    $update = $con->execute("UPDATE temp_reportico_dol_time_taken_report SET counts = (SELECT COUNT(user_id) FROM temp_reportico_dol_time_taken_report WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
                }

                $record_insert = 1;
            }
        }

        if ($record_insert == 1) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function getDolSmplAllotCodingSection($month, $year, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_dol_smpl_coding_section WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT mc.commodity_name,si.stage_sample_code,CONCAT(si.sample_total_qnt,' ',muw.unit_weight) AS quantity,mst.sample_type_desc, 'ALL'AS parameter, 'NA' AS code_number
                FROM sample_inward AS si
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN m_sample_allocate msa ON msa.org_sample_code = si.org_sample_code
                INNER JOIN m_unit_weight AS muw ON muw.unit_id=si.parcel_size
                INNER JOIN dmi_users AS u ON u.id=si.user_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN workflow AS w ON w.org_sample_code=si.org_sample_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code AND w.stage_smpl_flag In ('OF','HF','FG') AND EXTRACT(MONTH
                FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM msa.alloc_date):: INTEGER = '$year' AND w.dst_loc_id='$ral_lab_no'
                GROUP BY mc.commodity_name,si.stage_sample_code,si.sample_total_qnt,muw.unit_weight,mst.sample_type_desc,si.remark
                ORDER BY mc.commodity_name ASC");
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $commodity_name = $record['commodity_name'];
                $stage_sample_code = $record['stage_sample_code'];
                $quantity = $record['quantity'];
                $sample_type_desc = $record['sample_type_desc'];
                $parameter = $record['parameter'];
                $code_number = $record['code_number'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_dol_smpl_coding_section(sr_no, user_id, commodity_name, stage_sample_code, quantity, sample_type_desc, parameter, code_number, report_date) 
                VALUES (
                '$i','$user_id','$commodity_name', '$stage_sample_code','$quantity', '$sample_type_desc','$parameter','$code_number','$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getDolSmplAnalyticalSectionChemistAnalysis($month, $year, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_dol_smpl_analytical_section_chemist_analysis WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT mc.commodity_name,'ALL'AS parameter, 'NA' AS code_number, si.remark
                FROM sample_inward AS si
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN m_sample_allocate msa ON msa.org_sample_code = si.org_sample_code
                INNER JOIN dmi_users AS u ON u.id=si.user_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN workflow AS w ON w.org_sample_code=si.org_sample_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                WHERE EXTRACT(MONTH
                FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM msa.alloc_date):: INTEGER = '$year' AND  w.dst_loc_id='$ral_lab_no' AND u.role IN ('Jr Chemist','Sr Chemist')
                GROUP BY mc.commodity_name,si.remark, u.f_name, u.l_name, u.role
                ORDER BY mc.commodity_name ASC");
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $commodity_name = $record['commodity_name'];
                $parameter = $record['parameter'];
                $code_number = $record['code_number'];
                $remark = $record['remark'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_dol_smpl_analytical_section_chemist_analysis(sr_no, user_id, commodity_name, parameter, code_number, remark, report_date) 
                VALUES (
                '$i','$user_id','$commodity_name', '$parameter','$code_number','$remark','$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getDolParticularSampleAnanlyzeReceiveRal($month, $year, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_dol_particular_analyze_receive WHERE user_id = '$user_id'");

        $sql = "SELECT sa.commodity_code,si.sample_type_code
                FROM sample_inward AS si
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month'  AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' GROUP BY sa.commodity_code,si.sample_type_code";

        $sql = $con->execute($sql);
        $recordNames = $sql->fetchAll('assoc');
        $sql->closeCursor();

        $record_insert = 0;
        foreach ($recordNames as $recordName) {
            $commodity_code = $recordName['commodity_code'];
            $sample_type_code = $recordName['sample_type_code'];
            $query = $con->execute("SELECT mc.commodity_name,si.remark, mst.sample_type_desc,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS bf_count
                    FROM code_decode AS cd
                    INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                    INNER JOIN m_sample_allocate sa ON si.org_sample_code = sa.org_sample_code
                    WHERE cd.lab_code='$ral_lab_no' AND cd.status_flag NOT IN('G','F') AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND sa.commodity_code='$commodity_code' AND si.sample_type_code = '$sample_type_code'
                    ) AS bf_count,    
                    (
                    SELECT COUNT(DISTINCT(sa.sample_code)) AS received_count
                    FROM m_sample_allocate sa
                    WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND sa.commodity_code='$commodity_code' AND si.sample_type_code = '$sample_type_code'
                    ) AS received_count,   
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS analyzed_count_one
                    FROM code_decode AS cd
                    INNER JOIN m_sample_allocate AS sa ON sa.sample_code=cd.sample_code
                    WHERE test_n_r !='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND sa.commodity_code='$commodity_code' AND si.sample_type_code = '$sample_type_code'
                    ) AS analyzed_count_original, 
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS analyzed_count_two
                    FROM code_decode AS cd
                    INNER JOIN m_sample_allocate AS sa ON sa.sample_code=cd.sample_code
                    WHERE test_n_r='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND sa.commodity_code='$commodity_code' AND si.sample_type_code = '$sample_type_code'
                    ) AS analyzed_count_duplicate,
                    (
                    SELECT COUNT(DISTINCT(sample_code)) AS received_count
                    FROM m_sample_allocate sa
                    WHERE lab_code='$ral_lab_no' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year'AND sa.commodity_code='$commodity_code' AND si.sample_type_code = '$sample_type_code'
                    ) AS received_count_year,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS analyzed_count_one
                    FROM code_decode AS cd
                    INNER JOIN m_sample_allocate AS sa ON sa.sample_code=cd.sample_code
                    WHERE test_n_r !='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND sa.commodity_code='$commodity_code' AND si.sample_type_code = '$sample_type_code'
                    ) AS analyzed_count_year,
                    (
                    SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                    FROM dmi_users AS u
                    INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                    INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                    WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                    GROUP BY ral_lab
                    ) AS lab_name
                    
                    FROM sample_inward AS si
                    INNER JOIN m_sample_type mst ON si.sample_type_code = mst.sample_type_code
                    INNER JOIN m_commodity mc ON si.commodity_code = mc.commodity_code
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                    WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND sa.commodity_code='$commodity_code'
                    GROUP BY sa.commodity_code,si.remark, si.sample_type_code, mc.commodity_name, mst.sample_type_desc");
           
            $records = $query->fetchAll('assoc');
            $query->closeCursor();
            if (!empty($records)) {
                foreach ($records as $record) {
                    $commodity_name = $record['commodity_name'];
                    $bf_count = $record['bf_count'];
                    $received_count = $record['received_count'];
                    $total = $bf_count + $received_count;
                    $remark = $record['remark'];
                    $analyzed_count_original = $record['analyzed_count_original'];
                    $analyzed_count_duplicate = $record['analyzed_count_duplicate'];
                    $analyzed_count_original = $record['analyzed_count_original'];
                    $received_count_year = $record['received_count_year'];
                    $analyzed_count_year = $record['analyzed_count_year'];
                    $carry_forward = $total - $analyzed_count_original;
                    $lab_name = $record['lab_name'];
                    $sample_type_desc = $record['sample_type_desc'];
                    $date = date("d/m/Y");
                    $i = $i + 1;

                    $insert = $con->execute("INSERT INTO temp_reportico_dol_particular_analyze_receive (sr_no, user_id, lab_name, commodity_name, sample_type_desc, bf_count, received_count, remark, total, analyzed_count_original, analyzed_count_duplicate, received_count_year, analyzed_count_year, carry_forward, report_date) 
                    VALUES (
                    '$i','$user_id','$lab_name', '$commodity_name','$sample_type_desc','$bf_count', '$received_count', '$remark', '$total','$analyzed_count_original','$analyzed_count_duplicate','$received_count_year','$analyzed_count_year','$carry_forward','$date')");

                    $update = $con->execute("UPDATE temp_reportico_dol_particular_analyze_receive SET counts = (SELECT COUNT(user_id) FROM temp_reportico_dol_particular_analyze_receive WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
                }

                $record_insert = 1;
            }
        }

        if ($record_insert == 1) {
            return 1;
        } else {
            return 0;
        }
    }


    /********************************************************************************************************************
     * Role Head Office
     ********************************************************************************************************************/

    public static function getHoRejectSample($from_date, $to_date, $posted_ro_office, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ho_reject_sample WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT si.received_date,si.reject_date,CONCAT(du.f_name,' ',du.l_name) AS fullname,CONCAT(r.user_flag,',',mll.ro_office) AS labname,si.org_sample_code,mc.commodity_name,mst.sample_type_desc
                FROM sample_inward as si 
                JOIN (SELECT org_sample_code,max(id) AS id FROM workflow WHERE dst_loc_id='$ral_lab_no' GROUP BY(org_sample_code)) wf ON ((si.org_sample_code=wf.org_sample_code))
                JOIN workflow wff ON ((wf.id=wff.id))						
                Inner Join dmi_ro_offices AS mll ON mll.id=si.loc_id 
                Inner Join m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code 
                Inner Join dmi_users AS du ON du.id=si.user_code 
                Inner Join dmi_user_roles AS r On r.user_email_id=du.email
                Inner Join m_commodity AS mc ON mc.commodity_code=si.commodity_code And si.display='Y' AND si.acc_rej_flg='R'
                WHERE date(si.received_date) BETWEEN '$from_date' AND '$to_date'");
        $records = $query->fetchAll('assoc');
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $received_date = $record['received_date'];
                $reject_date = $record['reject_date'];
                $fullname = $record['fullname'];
                $labname = $record['labname'];
                $org_sample_code = $record['org_sample_code'];
                $commodity_name = $record['commodity_name'];
                $sample_type_desc = $record['sample_type_desc'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ho_reject_sample (sr_no, user_id, received_date, reject_date, fullname, labname, org_sample_code, commodity_name, sample_type_desc, report_date) 
                VALUES (
                '$i','$user_id','$received_date', '$reject_date', '$fullname', '$labname', '$org_sample_code', '$commodity_name', '$sample_type_desc', '$date')");

                $update = $con->execute("UPDATE temp_reportico_ho_reject_sample SET counts = (SELECT COUNT(user_id) FROM temp_reportico_ho_reject_sample WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getHoSampleReceivedRoSoRalCal($from_date, $to_date, $commodity, $posted_ro_office, $lab, $role, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ho_sample_received_rosoralcal WHERE user_id = '$user_id'");

        $sql = "SELECT si.received_date,si.org_sample_code,mct.category_name,mc.commodity_name,mst.sample_type_desc,du.role,mll.ro_office,r.user_flag,CONCAT('$ral_lab_name',', ',mll.ro_office) AS lab_name
		        FROM sample_inward as si 
				INNER JOIN m_commodity_category AS mct ON mct.category_code=si.category_code
				INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id 
				INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code 
				INNER JOIN dmi_users AS du On du.id=si.user_code
				INNER JOIN dmi_user_roles AS r On r.user_email_id=du.email
				INNER JOIN workflow AS w On w.org_sample_code=si.org_sample_code
				INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code And si.display='Y'";

        if ($from_date != '' && $to_date != '') {
            $sql .= " WHERE date(si.received_date) BETWEEN '$from_date' and '$to_date'";
        }
        if ($commodity != '') {
            $sql .= " AND si.commodity_code='$commodity'";
        }
        if ($lab == "RO" || $lab == "SO") {
            $sql .= " AND si.loc_id='" . $posted_ro_office . "'";
        } else if ($lab == "RAL" && $role == "Inward Officer") {
            $sql .= " AND w.dst_loc_id='" . $posted_ro_office . "'";
        } else if ($lab == "CAL" && $role == "Inward Officer") {
            $sql .= " AND w.dst_loc_id='" . $posted_ro_office . "'";
        }
        $sql .= " Group By du.role,si.org_sample_code,mct.category_name,mc.commodity_name,mst.sample_type_desc,si.received_date,mll.ro_office,r.user_flag,du.f_name,du.l_name";

        $sql .= " ORDER BY si.received_date asc";

        $query = $con->execute($sql);
        $records = $query->fetchAll('assoc');
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $received_date = $record['received_date'];
                $org_sample_code = $record['org_sample_code'];
                $category_name = $record['category_name'];
                $commodity_name = $record['commodity_name'];
                $sample_type_desc = $record['sample_type_desc'];
                $role = $record['role'];
                $ro_office = $record['ro_office'];
                $user_flag = $record['user_flag'];
                $lab_name = $record['lab_name'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ho_sample_received_rosoralcal (sr_no, user_id, received_date, org_sample_code, category_name, commodity_name, sample_type_desc, role, ro_office, user_flag, lab_name, report_date) 
                VALUES (
                '$i','$user_id','$received_date', '$org_sample_code', '$category_name', '$commodity_name', '$sample_type_desc', '$role', '$ro_office', '$user_flag', '$lab_name','$date')");

                $update = $con->execute("UPDATE temp_reportico_ho_sample_received_rosoralcal SET counts = (SELECT COUNT(user_id) FROM temp_reportico_ho_sample_received_rosoralcal WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getHoSampleRegister($from_date, $to_date, $posted_ro_office, $lab, $commodity, $sample, $category, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ho_sample_register WHERE user_id = '$user_id'");

        $str = "SELECT si.letr_date,mct.category_name,CONCAT(r.user_flag,',',mll.ro_office) AS labname, si.letr_ref_no, si.org_sample_code,si.stage_sample_code,si.sample_total_qnt AS sample_qnt,mu.unit_weight,mp.par_condition_desc,si.sample_total_qnt,si.received_date, mc.commodity_name,si.commodity_code, si.category_code,st.sample_type_desc,si.loc_id, si.dispatch_date,si.dispatch_date, si.grading_date,si.remark,r.user_flag,sample_total_qnt
                FROM sample_inward AS si
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN m_commodity_category AS mct ON mct.category_code=si.category_code
                INNER JOIN m_par_condition AS mp ON mp.par_condition_code=si.par_condition_code
                INNER JOIN m_unit_weight AS mu ON mu.unit_id=si.parcel_size
                INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN dmi_users AS du ON du.id=si.user_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=du.email";

        $str .= " WHERE si.display='Y' AND si.acc_rej_flg='A' AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date'";

        if ($_SESSION['user_flag'] == "RO" || $_SESSION['user_flag'] == "SO") {
            $str .= " AND si.loc_id='" . $_SESSION['posted_ro_office'] . "'";
        }
        if ($_SESSION['user_flag'] == "RO" || $_SESSION['user_flag'] == "SO") {
            $str .= " AND r.user_flag='" . $_SESSION['user_flag'] . "'";
        }

        $str .= "AND si.loc_id='$ral_lab_no'  AND r.user_flag='$ral_lab_name' AND si.sample_type_code = '$sample' AND si.commodity_code='$commodity' AND si.category_code='$category' GROUP BY si.stage_sample_code,si.letr_date,sample_qnt,si.received_date,si.dispatch_date,si.grading_date,mll.ro_office,r.user_flag, mc.commodity_name,mct.category_name,si.commodity_code, si.category_code,st.sample_type_desc,si.loc_id,si.letr_ref_no,si.sample_total_qnt,mu.unit_weight,mp.par_condition_desc,si.dispatch_date, si.org_sample_code,si.remark
        ORDER BY received_date ASC";

        $query = $con->execute($str);
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $letr_date = $record['letr_date'];
                $category_name = $record['category_name'];
                $labname = $record['labname'];
                $letr_ref_no = $record['letr_ref_no'];
                $org_sample_code = $record['org_sample_code'];
                $stage_sample_code = $record['stage_sample_code'];
                $sample_qnt = $record['sample_qnt'];
                $unit_weight = $record['unit_weight'];
                $par_condition_desc = $record['par_condition_desc'];
                $sample_total_qnt = $record['sample_total_qnt'];
                $received_date = $record['received_date'];
                $commodity_name = $record['commodity_name'];
                $commodity_code = $record['commodity_code'];
                $category_code = $record['category_code'];
                $sample_type_desc = $record['sample_type_desc'];
                $loc_id = $record['loc_id'];
                $dispatch_date = $record['dispatch_date'];
                $grading_date = $record['grading_date'];
                $remark = $record['remark'];
                $user_flag = $record['user_flag'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ho_sample_register (sr_no, user_id, letr_date, category_name, labname, letr_ref_no, org_sample_code, stage_sample_code, sample_qnt, unit_weight, par_condition_desc, sample_total_qnt, received_date, commodity_name, commodity_code, category_code, sample_type_desc, loc_id, dispatch_date, grading_date, remark, user_flag, report_date) 
                VALUES (
                '$i','$user_id','$letr_date', '$category_name', '$labname', '$letr_ref_no', '$org_sample_code', '$stage_sample_code', '$sample_qnt', '$unit_weight', '$par_condition_desc', '$sample_total_qnt', '$received_date', '$commodity_name', '$commodity_code', '$category_code', '$sample_type_desc', '$loc_id', '$dispatch_date', '$grading_date', '$remark', '$user_flag', '$date')");

                $update = $con->execute("UPDATE temp_reportico_ho_sample_register SET counts = (SELECT COUNT(user_id) FROM temp_reportico_ho_sample_register WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getHoSampleAnalyzedCount($from_date, $to_date, $posted_ro_office, $lab, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ho_sample_analyzed WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT mll.ro_office,mst.sample_type_desc,mc.commodity_name, COUNT(mc.commodity_name) AS count_samples, COUNT(CASE WHEN si.status_flag = 'FG' THEN 1 END) AS finalized
                FROM sample_inward AS si
                INNER JOIN m_commodity_category AS mcc ON si.category_code=mcc.category_code
                INNER JOIN m_commodity AS mc ON si.commodity_code=mc.commodity_code
                INNER JOIN m_sample_type AS mst ON si.sample_type_code=mst.sample_type_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN dmi_users AS u ON u.id=si.user_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND si.display='Y' AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date'  AND si.loc_id='$ral_lab_no' AND r.user_flag='$ral_lab_name'
                GROUP BY mll.ro_office,mst.sample_type_desc,mc.commodity_name
                ORDER BY mc.commodity_name ASC");
        $records = $query->fetchAll('assoc');
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $ro_office = $record['ro_office'];
                $sample_type_desc = $record['sample_type_desc'];
                $commodity_name = $record['commodity_name'];
                $count_samples = $record['count_samples'];
                $finalized = $record['finalized'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ho_sample_analyzed (sr_no,user_id, ro_office, sample_type_desc, commodity_name, count_samples, finalized, report_date) 
                VALUES (
                '$i','$user_id','$ro_office', '$sample_type_desc', '$commodity_name', '$count_samples', '$finalized','$date')");

                $update = $con->execute("UPDATE temp_reportico_ho_sample_analyzed SET counts = (SELECT COUNT(user_id) FROM temp_reportico_ho_sample_analyzed WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getHoSampleAcceptedByChemistForTesting($from_date, $to_date, $posted_ro_office, $lab, $user, $ral_lab, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ho_sample_accepted_chemist_testing WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT CONCAT('$ral_lab_name',', ',mll.ro_office) AS lab_name,CONCAT(du.f_name,' ',du.l_name,' (',du.role,')') AS chemist_name, mll.ro_office, si.received_date,msa.org_sample_code,mc.commodity_name,mst.sample_type_desc
                FROM m_sample_allocate AS msa
                INNER JOIN sample_inward AS si ON si.org_sample_code=msa.org_sample_code
                INNER JOIN m_commodity AS mc ON si.commodity_code=mc.commodity_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code
                INNER JOIN dmi_users AS du ON du.id=msa.alloc_to_user_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=du.email 
                WHERE  msa.display='Y' AND msa.acptnce_flag='Y' AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date' AND msa.lab_code='$ral_lab_no' AND r.user_flag='$ral_lab_name' AND msa.alloc_to_user_code='$user'");
        // print_r($query);exit;
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $ro_office = $record['ro_office'];
                $chemist_name = $record['chemist_name'];
                $received_date = $record['received_date'];
                $org_sample_code = $record['org_sample_code'];
                $commodity_name = $record['commodity_name'];
                $sample_type_desc = $record['sample_type_desc'];
                $lab_name = $record['lab_name'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ho_sample_accepted_chemist_testing (sr_no, user_id, ro_office, chemist_name, received_date, org_sample_code, commodity_name, sample_type_desc, lab_name, report_date) 
                VALUES (
                '$i','$user_id','$ro_office', '$chemist_name', '$received_date', '$org_sample_code', '$commodity_name', '$sample_type_desc', '$lab_name','$date')");

                $update = $con->execute("UPDATE temp_reportico_ho_sample_accepted_chemist_testing SET counts = (SELECT COUNT(user_id) FROM temp_reportico_ho_sample_accepted_chemist_testing WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getHoSamplePendingForDispatch($from_date, $to_date, $posted_ro_office, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ho_sample_pending WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT si.org_sample_code,mc.commodity_name,si.acc_rej_flg AS status,si.received_date,mst.sample_type_desc,mll.ro_office,r.user_flag
                FROM sample_inward AS si
                INNER JOIN code_decode AS cd ON cd.org_sample_code!=si.org_sample_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN m_commodity AS mc ON si.commodity_code=mc.commodity_code
                INNER JOIN dmi_users AS du ON du.id=si.user_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=du.email
                INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code AND si.display='Y' AND si.acc_rej_flg='P' AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date' AND si.loc_id='$ral_lab_no'
                GROUP BY mll.ro_office,si.acc_rej_flg,r.user_flag,si.org_sample_code,mc.commodity_name,si.received_date,mst.sample_type_desc
                ORDER BY si.received_date ASC");
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $org_sample_code = $record['org_sample_code'];
                $commodity_name = $record['commodity_name'];
                $status = $record['status'];
                $received_date = $record['received_date'];
                $sample_type_desc = $record['sample_type_desc'];
                $ro_office = $record['ro_office'];
                $user_flag = $record['user_flag'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ho_sample_pending (sr_no, user_id, org_sample_code, commodity_name, status, received_date, sample_type_desc, ro_office, user_flag, report_date) 
                VALUES (
                '$i','$user_id','$org_sample_code', '$commodity_name', '$status', '$received_date', '$sample_type_desc', '$ro_office', '$user_flag', '$date')");

                $update = $con->execute("UPDATE temp_reportico_ho_sample_pending SET counts = (SELECT COUNT(user_id) FROM temp_reportico_ho_sample_pending WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getHoCodingDecodingSection($from_date, $to_date, $posted_ro_office, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ho_coding_decoding WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT mll.ro_office,mc.commodity_name,si.stage_sample_code,w.tran_date,si.received_date,si.sample_total_qnt,muw.unit_weight,mst.sample_type_desc,si.remark
                FROM sample_inward AS si
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN m_unit_weight AS muw ON muw.unit_id=si.parcel_size
                INNER JOIN dmi_users AS u ON u.id=si.user_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN workflow AS w ON w.org_sample_code=si.org_sample_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code AND w.stage_smpl_flag In ('OF','HF','FG') AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date' AND w.dst_loc_id='$ral_lab_no'
                GROUP BY mll.ro_office,mc.commodity_name,w.tran_date,si.stage_sample_code,si.received_date,si.sample_total_qnt,muw.unit_weight,mst.sample_type_desc,si.remark
                ORDER BY mc.commodity_name ASC");
        // print_r($query);exit;
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $ro_office = $record['ro_office'];
                $commodity_name = $record['commodity_name'];
                $stage_sample_code = $record['stage_sample_code'];
                $tran_date = $record['tran_date'];
                $sample_total_qnt = $record['sample_total_qnt'];
                $unit_weight = $record['unit_weight'];
                $sample_qnt = $sample_total_qnt . ' ' . $unit_weight;
                $sample_type_desc = $record['sample_type_desc'];
                $remark = $record['remark'];
                $received_date = $record['received_date'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ho_coding_decoding (sr_no, user_id, ro_office, commodity_name, stage_sample_code, received_date, tran_date, sample_qnt, sample_type_desc, remark, report_date) 
                VALUES (
                '$i','$user_id','$ro_office', '$commodity_name', '$stage_sample_code','$received_date', '$tran_date', '$sample_qnt','$sample_type_desc','$remark','$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getHoSampleAllotedToChemistForRetesting($from_date, $to_date, $posted_ro_office, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ho_sample_alloted_chemist_retesting WHERE user_id = '$user_id'");

        $str = "SELECT mll.ro_office,msa.org_sample_code,mc.commodity_name,si.received_date,mst.sample_type_desc,CONCAT(du.f_name,' ',du.l_name) AS chemist_name,msa.alloc_date
                FROM m_sample_allocate AS msa
                INNER JOIN sample_inward AS si ON si.org_sample_code=msa.org_sample_code
                INNER JOIN m_commodity AS mc ON si.commodity_code=mc.commodity_code
                INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN dmi_users AS du ON du.id=si.user_code AND msa.display='Y' AND msa.test_n_r='R' AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date'";

        if ($_SESSION['user_flag'] == "RAL") {
            $str .= " AND msa.lab_code='" . $_SESSION['posted_ro_office'] . "'";
        } else {
            $str .= " AND msa.lab_code='$ral_lab_no'";
        }

        $str .= " ORDER BY si.received_date ASC";

        $query = $con->execute($str);
        //  print_r($query);exit;
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $org_sample_code = $record['org_sample_code'];
                $commodity_name = $record['commodity_name'];
                $chemist_name = $record['chemist_name'];
                $received_date = $record['received_date'];
                $org_sample_code = $record['org_sample_code'];
                $ro_office = $record['ro_office'];
                $sample_type_desc = $record['sample_type_desc'];
                $alloc_date = $record['alloc_date'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ho_sample_alloted_chemist_retesting (sr_no, user_id, ro_office, chemist_name, received_date, org_sample_code, commodity_name, sample_type_desc, alloc_date, report_date) 
                VALUES (
                '$i','$user_id','$ro_office', '$chemist_name', '$received_date', '$org_sample_code', '$commodity_name', '$sample_type_desc','$alloc_date','$date')");

                $update = $con->execute("UPDATE temp_reportico_ho_sample_alloted_chemist_retesting SET counts = (SELECT COUNT(user_id) FROM temp_reportico_ho_sample_alloted_chemist_retesting WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getHoSampleAllotedToChemistForTesting($from_date, $to_date, $posted_ro_office, $lab, $user, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ho_sample_alloted_chemist_testing WHERE user_id = '$user_id'");

        $str = "SELECT si.org_sample_code,mc.commodity_name,si.received_date,mst.sample_type_desc,mll.ro_office,r.user_flag,CONCAT(du.f_name,' ',du.l_name,' (',du.role,')') AS chemist_name,msa.alloc_date
                FROM sample_inward AS si
                INNER JOIN code_decode AS cd ON cd.org_sample_code=si.org_sample_code
                INNER JOIN m_sample_allocate AS msa ON msa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN m_commodity AS mc ON si.commodity_code=mc.commodity_code
                INNER JOIN dmi_users AS du ON du.id=cd.alloc_to_user_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=du.email
                INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code AND cd.display='Y' AND cd.chemist_code!='-' AND DATE(msa.alloc_date) BETWEEN '$from_date' AND '$to_date' AND cd.alloc_to_user_code='$user'";

        if ($_SESSION['user_flag'] == "RAL") {
            $str .= " AND cd.lab_code='" . $_SESSION['posted_ro_office'] . "'";
        } else {
            $str .= " AND cd.lab_code='$ral_lab_no'";
        }

        $str .= " ORDER BY si.received_date ASC";
        $query = $con->execute($str);


        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $org_sample_code = $record['org_sample_code'];
                $commodity_name = $record['commodity_name'];
                $chemist_name = $record['chemist_name'];
                $received_date = $record['received_date'];
                $org_sample_code = $record['org_sample_code'];
                $ro_office = $record['ro_office'];
                $sample_type_desc = $record['sample_type_desc'];
                $user_flag = $record['user_flag'];
                $alloc_date = $record['alloc_date'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ho_sample_alloted_chemist_testing (sr_no, user_id, ro_office, chemist_name, received_date, org_sample_code, commodity_name, sample_type_desc, user_flag, alloc_date, report_date) 
                VALUES (
                '$i','$user_id','$ro_office', '$chemist_name', '$received_date', '$org_sample_code', '$commodity_name', '$sample_type_desc', '$user_flag','$alloc_date','$date')");

                $update = $con->execute("UPDATE temp_reportico_ho_sample_alloted_chemist_testing SET counts = (SELECT COUNT(user_id) FROM temp_reportico_ho_sample_alloted_chemist_testing WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getHoTestedSample($from_date, $to_date, $posted_ro_office, $user_code, $commodity, $lab, $sample_type, $ral_lab, $role, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ho_tested_sample WHERE user_id = '$user_id'");

        if ($role == 'Jr Chemist' || $role == 'Sr Chemist' || $role == 'Cheif Chemist') {
            $str = "SELECT du.role,CONCAT(du.f_name,' ',du.l_name) as chemist_name, mll.ro_office, msa.recby_ch_date,mst.sample_type_desc,mc.commodity_name,cd.sample_code as org_sample_code,
            msa.expect_complt,msa.commencement_date ,CASE WHEN msa.commencement_date>msa.expect_complt THEN msa.commencement_date   ::date-msa.expect_complt ::date ELSE 0
            END as delay,expect_complt::date - recby_ch_date::date  as complete,si.received_date,
            r.user_flag,du.f_name,du.l_name,grade,
            (SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
            FROM dmi_users AS u
            INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
            INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
            WHERE u.status = 'active' AND o.id = '$ral_lab_no'
            GROUP BY ral_lab) AS lab_name
            FROM sample_inward as si 
            Inner Join m_sample_allocate as msa ON msa.org_sample_code=si.org_sample_code
            Inner Join m_sample_type as mst ON mst.sample_type_code=si.sample_type_code 
            Inner Join dmi_ro_offices as mll ON mll.id=si.loc_id 
            Inner Join code_decode as cd on cd.org_sample_code=si.org_sample_code
            Inner Join dmi_users as du ON du.id=cd.alloc_to_user_code
            Inner Join dmi_user_roles as r On r.user_email_id=du.email		
            Inner Join m_commodity as mc ON mc.commodity_code=si.commodity_code 
            WHERE cd.display='Y' and cd.alloc_to_user_code='" . $_SESSION['user_code'] . "' AND cd.lab_code='$posted_ro_office' AND date(si.received_date) BETWEEN '$from_date' and '$to_date'
            GROUP BY du.role,mll.ro_office,du.f_name,du.l_name,cd.sample_code,msa.commencement_date,msa.recby_ch_date,msa.expect_complt,cd.chemist_code,mc.commodity_name,mst.sample_type_desc,si.received_date,r.user_flag,du.f_name,du.l_name,si.grade order by si.received_date";
        } else {
            $str = "SELECT CONCAT(du.f_name,' ',du.l_name,' (',du.role,')') as chemist_name, mll.ro_office, du.role, msa.recby_ch_date,mst.sample_type_desc,mc.commodity_name,cd.sample_code as org_sample_code,
            msa.expect_complt,msa.commencement_date, CASE WHEN msa.commencement_date>msa.expect_complt THEN msa.commencement_date   ::date-msa.expect_complt ::date ELSE 0
            END as delay,expect_complt::date - recby_ch_date::date  as complete,si.received_date,
            r.user_flag,du.f_name,du.l_name,grade,
            (SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
            FROM dmi_users AS u
            INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
            INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
            WHERE u.status = 'active' AND o.id = '$ral_lab_no'
            GROUP BY ral_lab) AS lab_name
            FROM sample_inward as si 
            Inner Join m_sample_allocate as msa ON msa.org_sample_code=si.org_sample_code
            Inner Join m_sample_type as mst ON mst.sample_type_code=si.sample_type_code 
            Inner Join dmi_users as du ON du.id=msa.alloc_to_user_code
            Inner Join dmi_user_roles as r On r.user_email_id=du.email
            Inner Join dmi_ro_offices as mll ON mll.id=si.loc_id 
            Inner Join code_decode as cd on cd.org_sample_code=si.org_sample_code 
            Inner Join m_commodity as mc ON mc.commodity_code=si.commodity_code 
            WHERE cd.display='Y' AND date(si.received_date) BETWEEN '$from_date' and '$to_date' AND si.sample_type_code='$sample_type' AND si.commodity_code='$commodity' AND r.user_flag='$ral_lab_name' AND msa.lab_code='$ral_lab_no'
            GROUP BY du.role,mll.ro_office,du.f_name,du.l_name,cd.sample_code,msa.commencement_date,msa.recby_ch_date,msa.expect_complt,cd.chemist_code,mc.commodity_name,mst.sample_type_desc,si.received_date,r.user_flag,du.f_name,du.l_name,si.grade order by si.received_date";
        }
        $query = $con->execute($str);
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $ro_office = $record['ro_office'];
                $chemist_name = $record['chemist_name'];
                $recby_ch_date = $record['recby_ch_date'];
                $sample_type_desc = $record['sample_type_desc'];
                $commodity_name = $record['commodity_name'];
                $org_sample_code = $record['org_sample_code'];
                $expect_complt = $record['expect_complt'];
                $commencement_date = $record['commencement_date'];
                $grade = $record['grade'];
                $lab_name = $record['lab_name'];
                $role = $record['role'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ho_tested_sample (sr_no, user_id, role, ro_office, chemist_name, recby_ch_date, org_sample_code, commodity_name, sample_type_desc, expect_complt, commencement_date, grade, lab_name, report_date) 
                VALUES (
                '$i','$user_id','$role','$ro_office', '$chemist_name', '$recby_ch_date', '$org_sample_code', '$commodity_name', '$sample_type_desc','$expect_complt','$commencement_date','$grade','$lab_name','$date')");

                $update = $con->execute("UPDATE temp_reportico_ho_tested_sample SET counts = (SELECT COUNT(user_id) FROM temp_reportico_ho_tested_sample WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getHoTestSubmitByChemist($from_date, $to_date, $chemist_code, $sample_code, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ho_test_submit_by_chemist WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT recby_ch_date,sa.chemist_code,sa.sample_code,c.commodity_name,CONCAT(du.f_name,' ',du.l_name,' (',du.role,') ') AS chemist_name,t.test_name,atd.result,'$chemist_code'AS chemist_code,
                (SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab) AS lab_name
                FROM m_sample_allocate sa
                INNER JOIN actual_test_data AS atd ON sa.chemist_code=atd.chemist_code
                INNER JOIN code_decode AS cd ON cd.org_sample_code=sa.org_sample_code
                INNER JOIN m_test AS t ON atd.test_code=t.test_code
                INNER JOIN m_commodity AS c ON c.commodity_code=sa.commodity_code
                INNER JOIN dmi_users AS du ON du.id=sa.alloc_to_user_code AND DATE(sa.alloc_date) BETWEEN '$from_date' AND '$to_date' AND sa.chemist_code='$chemist_code'
                WHERE atd.chemist_code='$chemist_code' AND sa.sample_code='$sample_code' AND cd.status_flag in('C','G') AND sa.chemist_code='$chemist_code'
                
                GROUP BY du.role,sa.chemist_code,sa.recby_ch_date,sa.sample_code,c.commodity_name,du.f_name,du.l_name,t.test_name,atd.result");
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $recby_ch_date = $record['recby_ch_date'];
                $commodity_name = $record['commodity_name'];
                $sample_code = $record['sample_code'];
                $chemist_name = $record['chemist_name'];
                $test_name = $record['test_name'];
                $result = $record['result'];
                $chemist_code = $record['chemist_code'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ho_test_submit_by_chemist(sr_no, user_id, lab_name, recby_ch_date, commodity_name, sample_code, chemist_name, test_name, result, chemist_code, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$recby_ch_date', '$commodity_name', '$sample_code', '$chemist_name','$test_name','$result','$chemist_code','$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getHoCommodityConsolidatedReport($month, $posted_ro_office, $sample_type, $commodity, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ho_commodity_consolidated WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT commodity_name, 
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS bf_count
                FROM code_decode AS cd
                INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                WHERE cd.lab_code='$ral_lab_no' AND cd.status_flag='G' AND EXTRACT(MONTH
                FROM si.received_date):: INTEGER = '$month' AND si.sample_type_code='$sample_type' AND si.commodity_code='$commodity'
                ) AS bf_count,
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS analyzed_count
                FROM code_decode AS cd
                INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                WHERE cd.lab_code='$ral_lab_no' AND cd.status_flag='G' AND EXTRACT(MONTH
                FROM si.received_date):: INTEGER = '$month' AND si.sample_type_code='$sample_type' AND si.commodity_code='$commodity'
                ) AS analyzed_count,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users as u 
                INNER JOIN dmi_ro_offices as o On u.posted_ro_office=o.id
                Inner Join dmi_user_roles as r on r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name
                FROM m_commodity
                WHERE commodity_code='$commodity'
                ");
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $commodity_name = $record['commodity_name'];
                $bf_count = $record['bf_count'];
                $analyzed_count = $record['analyzed_count'];
                $carried_for = $bf_count - $analyzed_count;
                $date = date("d/m/Y");
                $month = date("F", mktime(0, 0, 0, $month, 10));
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ho_commodity_consolidated (sr_no, user_id, lab_name, commodity_name, bf_count, analyzed_count, carried_for, month, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$commodity_name', '$bf_count','$analyzed_count', '$carried_for', '$month','$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getHoCommodityWisePrivateAnalysis($lab, $posted_ro_office, $commodity, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ho_commoditywise_private_analysis WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT si.fin_year,mc.commodity_name, COUNT(si.sample_type_code) AS sample_count,ml.ro_office, '$lab' AS lab_name
                FROM code_decode AS cd
                INNER JOIN sample_inward AS si ON cd.org_sample_code=si.org_sample_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN dmi_ro_offices AS ml ON si.loc_id=ml.id
                INNER JOIN dmi_users AS u ON u.id=si.user_code AND si.display='Y' AND si.sample_type_code='2' AND si.commodity_code='$commodity' AND cd.lab_code='$ral_lab_no'
                GROUP BY si.fin_year,mc.commodity_name,ml.ro_office
                ORDER BY commodity_name ASC");
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab = $record['lab_name'];
                $ro_office = $record['ro_office'];
                $lab_name = $lab . ', ' . $ro_office;
                $fin_year = $record['fin_year'];
                $commodity_name = $record['commodity_name'];
                $sample_count = $record['sample_count'];
                $ro_office = $record['ro_office'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ho_commoditywise_private_analysis (sr_no, user_id, lab_name, fin_year, commodity_name, sample_count, ro_office, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$fin_year', '$commodity_name', '$sample_count','$ro_office','$date')");

                $update = $con->execute("UPDATE temp_reportico_ho_commoditywise_private_analysis SET counts = (SELECT COUNT(user_id) FROM temp_reportico_ho_commoditywise_private_analysis WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getHoAllStaticsCounts($from_date, $to_date, $commodity, $office_type)
    {
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');
        $from_date = str_replace('/', '-', $from_date);
        $to_date = str_replace('/', '-', $to_date);

        $dmiRoOffice = TableRegistry::getTableLocator()->get('DmiRoOffices');
        $workflow = TableRegistry::getTableLocator()->get('workflow');

        if ($office_type == 'RAL') {
            $delete = $con->execute("DELETE FROM temp_reportico_ho_all_office_statistic WHERE user_id = '$user_id'");

            //get all RAL/CAL office id
            $ral_loc_id = $dmiRoOffice->find('all', array('fields' => array('id', 'ro_office', 'office_type'), 'conditions' => array('office_type' => 'RAL', 'OR' => array('delete_status IS NULL', 'delete_status' => 'no')), 'order' => 'ro_office'))->toArray();

            $i = 0;
            $ofsc_name = array();
            $inward = array();
            $forward = array();
            $forward_to_test = array();
            $finalized = array();
            $internal = array();
            $external = array();

            $ral_ids = array();

            //getting all RAL offices tables ids to compare below
            $a = 0;
            foreach ($ral_loc_id as $each_id) {
                $ral_ids[$a] = $each_id['id'];
                $a = $a + 1;
            }

            // $q is used for index for inserting value
            $q = 0;

            //getting counts for RAL/CAL offices
            foreach ($ral_loc_id as $each_ofsc) {
                $ofsc_name[$i] = $each_ofsc['ro_office'];

                $strInward = "SELECT w.org_sample_code FROM workflow w
                            INNER JOIN sample_inward si ON si.org_sample_code = w.org_sample_code";
                if (!empty($commodity)) {
                    $strInward .= " WHERE w.dst_loc_id = '" . $each_ofsc['id'] . "' AND w.stage_smpl_flag = 'AS' AND w.tran_date BETWEEN '$from_date' AND '$to_date' AND si.commodity_code = '$commodity' ";
                } else {
                    $strInward .= " WHERE w.dst_loc_id = '" . $each_ofsc['id'] . "' AND w.stage_smpl_flag = 'AS' AND w.tran_date BETWEEN '$from_date' AND '$to_date'";
                }
                $strInward .= "GROUP BY w.org_sample_code";
                $query = $con->execute($strInward);
                // print_r($query);exit;
                $recordsInward = $query->fetchAll('assoc');
                // print_r($records);exit;
                $query->closeCursor();

                $strForward = "SELECT w.org_sample_code FROM workflow w
                            INNER JOIN sample_inward si ON si.org_sample_code = w.org_sample_code";
                if (!empty($commodity)) {
                    $strForward .= " WHERE w.dst_loc_id = '" . $each_ofsc['id'] . "' AND w.stage_smpl_flag IN('OF,HF') AND w.tran_date BETWEEN '$from_date' AND '$to_date' AND si.commodity_code = '$commodity' ";
                } else {
                    $strForward .= " WHERE w.dst_loc_id = '" . $each_ofsc['id'] . "' AND w.stage_smpl_flag IN('OF,HF') AND w.tran_date BETWEEN '$from_date' AND '$to_date'";
                }
                $strForward .= "GROUP BY w.org_sample_code";
                $query = $con->execute($strForward);
                $recordsForward = $query->fetchAll('assoc');
                $query->closeCursor();

                $strForwardToTest = "SELECT w.org_sample_code FROM workflow w
                                    INNER JOIN sample_inward si ON si.org_sample_code = w.org_sample_code";
                if (!empty($commodity)) {
                    $strForwardToTest .= " WHERE w.dst_loc_id = '" . $each_ofsc['id'] . "' AND w.stage_smpl_flag = 'TA' AND w.tran_date BETWEEN '$from_date' AND '$to_date' AND si.commodity_code = '$commodity' ";
                } else {
                    $strForwardToTest .= " WHERE w.dst_loc_id = '" . $each_ofsc['id'] . "' AND w.stage_smpl_flag= 'TA' AND w.tran_date BETWEEN '$from_date' AND '$to_date'";
                }
                $strForwardToTest .= "GROUP BY w.org_sample_code";
                $query = $con->execute($strForwardToTest);
                $recordsForwardToTest = $query->fetchAll('assoc');
                $query->closeCursor();

                $strFinalized = "SELECT w.org_sample_code FROM workflow w
                                INNER JOIN sample_inward si ON si.org_sample_code = w.org_sample_code";
                if (!empty($commodity)) {
                    $strFinalized .= " WHERE w.dst_loc_id = '" . $each_ofsc['id'] . "' AND w.stage_smpl_flag = 'FG' AND w.tran_date BETWEEN '$from_date' AND '$to_date' AND si.commodity_code = '$commodity' ";
                } else {
                    $strFinalized .= " WHERE w.dst_loc_id = '" . $each_ofsc['id'] . "' AND w.stage_smpl_flag= 'FG' AND w.tran_date BETWEEN '$from_date' AND '$to_date'";
                }
                $strFinalized .= "GROUP BY w.org_sample_code";
                $query = $con->execute($strFinalized);
                $recordsFinalized = $query->fetchAll('assoc');
                $query->closeCursor();

                //this below code used to differentiate the finalized sample 
                //(Inwarded internally OR Inwaarded by RO/SO office)
                $int = 0;
                $ext = 0;

                if (!empty($recordsFinalized)) {
                    foreach ($recordsFinalized as $each_f) {
                        $query = $con->execute("SELECT w.src_loc_id FROM workflow w WHERE w.org_sample_code = '" . $each_f['org_sample_code'] . "' AND w.stage_smpl_flag IN ('OF,HF,SI')  ");
                        $get_details = $query->fetchAll('assoc');
                        $query->closeCursor();
                        if (!empty($get_details)) {

                            $sr_loc_id = $get_details['src_loc_id'];

                            if (in_array($sr_loc_id, $ral_ids)) {

                                $int = $int + 1;
                            } else {

                                $ext = $ext + 1;
                            }
                        }
                    }
                }

                $internal[$i] = $int;
                $external[$i] = $ext;

                $i = $i + 1;

                $inward = COUNT($recordsInward);
                $forward = COUNT($recordsForward);
                $forward_to_test = COUNT($recordsForwardToTest);

                $queryCommodity = $con->execute("SELECT commodity_name FROM m_commodity WHERE commodity_code = '$commodity'");
                $recCommodities = $queryCommodity->fetchAll('assoc');
                foreach ($recCommodities as $recCommodity) {
                    $commodity_name = $recCommodity['commodity_name'];
                }

                $insert = $con->execute("INSERT INTO temp_reportico_ho_all_office_statistic (user_id, ofsc_name, inward, forward, forward_to_test, internal, external, commodity_name) 
                VALUES (
                    '$user_id','$ofsc_name[$q]', '$inward', '$forward', '$forward_to_test', '$internal[$q]','$external[$q]','$commodity_name')");

                $q = $q + 1;
            }
        }

        if ($office_type == 'RO') {
            $delete = $con->execute("DELETE FROM temp_reportico_ho_ro_all_office_statistic WHERE user_id = '$user_id'");

            //get all RO/SO office id
            $query = $con->execute("SELECT id, ro_office,office_type FROM dmi_ro_offices where office_type IN ('RO','SO') AND (delete_status IS NULL OR delete_status = 'no')");
            $ro_loc_id = $query->fetchAll('assoc');
            $query->closeCursor();
            // print_r($ro_loc_id);exit;

            $i = 0;
            $ro_ofsc_name = array();
            $ro_inward = array();
            $ro_forward = array();
            $ro_finalized = array();

            //$k is used fro index in inserting value in temp table
            $k =  0;
            //getting counts for RO/SO offices
            foreach ($ro_loc_id as $each_ofsc) {

                $ro_ofsc_name[$i] = $each_ofsc['ro_office'];

                $strRoInward = "SELECT si.org_sample_code FROM sample_inward si
                             INNER JOIN workflow w ON w.org_sample_code = si.org_sample_code AND si.status_flag = 'S'";
                if (!empty($commodity)) {
                    $strRoInward .= " WHERE w.dst_loc_id = '" . $each_ofsc['id'] . "'  AND si.modified BETWEEN '$from_date' AND '$to_date' AND si.commodity_code = '$commodity' ";
                } else {
                    $strRoInward .= " WHERE w.dst_loc_id = '" . $each_ofsc['id'] . "'  AND si.modified BETWEEN '$from_date' AND '$to_date' ";
                }
                $strRoInward .= "GROUP BY si.org_sample_code";
                $query = $con->execute($strRoInward);
                // print_r($query);exit;

                $recordsRoInward = $query->fetchAll('assoc');
                $query->closeCursor();

                $strRoFoward = "SELECT w.org_sample_code FROM workflow w INNER JOIN sample_inward si ON si.org_sample_code = w.org_sample_code";

                if (!empty($commodity)) {
                    $strRoFoward .= " WHERE w.src_loc_id = '" . $each_ofsc['id'] . "' AND w.stage_smpl_flag IN ('OF,HF') AND w.tran_date BETWEEN '$from_date' AND '$to_date' AND si.commodity_code = '$commodity'";
                } else {
                    $strRoFoward .= " WHERE w.src_loc_id = '" . $each_ofsc['id'] . "' AND w.stage_smpl_flag IN ('OF,HF') AND w.tran_date BETWEEN '$from_date' AND '$to_date'";
                }
                $strRoFoward .= "GROUP BY w.org_sample_code";
                $query = $con->execute($strRoFoward);
                // print_r($query);exit;

                $recordsRoForward = $query->fetchAll('assoc');
                $query->closeCursor();

                $strAllSmplForwarded = "SELECT w.org_sample_code FROM workflow w INNER JOIN sample_inward si ON si.org_sample_code = w.org_sample_code";

                if (!empty($commodity)) { //'w.stage_smpl_flag'=>array(/*'OF','HF',*/'SI')); Removed by Shweta Apale on 15-11-2021 Condition in old Report 
                    $strAllSmplForwarded .= " WHERE w.src_loc_id = '" . $each_ofsc['id'] . "' AND w.stage_smpl_flag = 'SI' AND si.commodity_code = '$commodity'";
                } else {
                    $strAllSmplForwarded .= " WHERE w.src_loc_id = '" . $each_ofsc['id'] . "' AND w.stage_smpl_flag = 'SI'";
                }
                $strAllSmplForwarded .= "GROUP BY w.org_sample_code";
                $query = $con->execute($strAllSmplForwarded);
                // print_r($query);exit;

                $recordsALlSmplForwarded = $query->fetchAll('assoc');
                $query->closeCursor();

                $j = 0;
                $smpl_arry = array();
                foreach ($recordsALlSmplForwarded as $each) {

                    if (!in_array($each['org_sample_code'], $smpl_arry)) {
                        $smpl_arry[$j] = $each['org_sample_code'];
                        $j = $j + 1;
                    }
                }
                $smpl_arry = implode(',', $smpl_arry);
                $strRoFinalized = "SELECT w.org_sample_code FROM workflow w WHERE w.org_sample_code = '$smpl_arry' AND w.stage_smpl_flag = 'FG' AND w.tran_date BETWEEN '$from_date' AND '$to_date' GROUP BY w.org_sample_code";
                $query = $con->execute($strRoFinalized);

                $recordsRoFinalized = $query->fetchAll('assoc');
                $query->closeCursor();

                $i = $i + 1;

                $ro_inward = COUNT($recordsRoInward);
                $ro_forward = COUNT($recordsRoForward);
                $ro_finalized = COUNT($recordsRoFinalized);

                $queryCommodity = $con->execute("SELECT commodity_name FROM m_commodity WHERE commodity_code = '$commodity'");
                $recCommodities = $queryCommodity->fetchAll('assoc');
                foreach ($recCommodities as $recCommodity) {
                    $commodity_name = $recCommodity['commodity_name'];
                }
                $insert = $con->execute("INSERT INTO temp_reportico_ho_ro_all_office_statistic (user_id, ofsc_name, pending, forward, result, commodity_name) 
                VALUES (
                '$user_id','$ro_ofsc_name[$k]', '$ro_inward', '$ro_forward', '$ro_finalized','$commodity_name')");

                $k = $k + 1;
            }
        }
    }

    public static function getHoDetailsSampleAnalyzedByChemist($month, $year, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ho_details_sample_analyzed WHERE user_id = '$user_id'");

        $sql = "SELECT  sa.alloc_to_user_code, sa.commodity_code FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                INNER JOIN dmi_ro_offices AS ml ON ml.id=sa.lab_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND u.role IN ('Jr Chemist','Sr Chemist','Cheif Chemist')
                GROUP BY sa.alloc_to_user_code, sa.commodity_code";

        $sql = $con->execute($sql);
        $recordNames = $sql->fetchAll('assoc');
        $sql->closeCursor();

        $record_insert = 0;
        foreach ($recordNames as $recordName) {
            $user_code = $recordName['alloc_to_user_code'];
            $commodity_code = $recordName['commodity_code'];

            $query = $con->execute("SELECT mc.commodity_name,  sa.alloc_date, sa.recby_ch_date, sa.commencement_date, CONCAT(u.f_name,' ',u.l_name,' (',u.role,') ') AS name_chemist,'NA' AS other, 'NA' AS other_work, 'Yes' AS norm, mst.sample_type_desc,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 1
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no'  AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                ) AS check_count,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 5
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                ) AS check_apex_count,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 4
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                ) AS challenged_count,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 9
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                ) AS ilc_count,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 2
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                ) AS research_count,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code AND sa.test_n_r = 'R' 
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                ) AS retesting_count,
                (
                SELECT COUNT(ct.test_code)
                FROM commodity_test ct
                INNER JOIN m_sample_allocate AS msa ON msa.commodity_code = ct.commodity_code AND msa.lab_code = '$ral_lab_no'
                INNER JOIN sample_inward si ON msa.org_sample_code = si.org_sample_code AND msa.commodity_code = si.commodity_code
                WHERE ct.commodity_code = $commodity_code AND msa.alloc_to_user_code = '$user_code' AND EXTRACT(MONTH
                FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM msa.alloc_date):: INTEGER = '$year'
                ) AS no_of_param,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                INNER JOIN dmi_ro_offices AS ml ON ml.id=sa.lab_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND sa.alloc_to_user_code = '$user_code'
                GROUP BY mc.commodity_name,  sa.alloc_date, sa.recby_ch_date, sa.commencement_date, name_chemist, mst.sample_type_desc, si.received_date
                ORDER BY si.received_date ASC");
            $records = $query->fetchAll('assoc');
            $query->closeCursor();
            if (!empty($records)) {
                foreach ($records as $record) {
                    $commodity_name = $record['commodity_name'];
                    $project_sample = 'NA';
                    $check_count = $record['check_count'];
                    $check_apex_count = $record['check_apex_count'];
                    $challenged_count = $record['challenged_count'];
                    $ilc_count = $record['ilc_count'];
                    $research_count = $record['research_count'];
                    $retesting_count = $record['retesting_count'];
                    $recby_ch_date = $record['recby_ch_date'];
                    $commencement_date = $record['commencement_date'];
                    $name_chemist = $record['name_chemist'];
                    $sample_type_desc = $record['sample_type_desc'];
                    $lab_name = $record['lab_name'];
                    $no_of_param = $record['no_of_param'];

                    if ($recby_ch_date == '' || $commencement_date == '') {
                        $working_days = '0';
                    } else {
                        $diff = abs(strtotime($commencement_date) - strtotime($recby_ch_date));
                        $years = floor($diff / (365 * 60 * 60 * 24));
                        $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
                        $working_days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
                    }
                    $other = $record['other'];
                    $other_work = $record['other_work'];
                    $norm = $record['norm'];
                    $date = date("d/m/Y");
                    $i = $i + 1;
                    $total_no = $check_count + $check_apex_count + $challenged_count + $ilc_count + $research_count + $retesting_count;

                    $insert = $con->execute("INSERT INTO temp_reportico_ho_details_sample_analyzed (sr_no, user_id, lab_name, name_chemist, sample_type_desc, commodity_name, project_sample, check_count, check_apex_count, challenged_count, ilc_count, research_count, retesting_count, working_days, no_of_param, other, other_work, norm, total_no, report_date) 
                    VALUES (
                    '$i','$user_id','$lab_name','$name_chemist', '$sample_type_desc', '$commodity_name', '$project_sample', '$check_count', '$check_apex_count', '$challenged_count', '$ilc_count','$research_count','$retesting_count', '$working_days','$no_of_param', '$other','$other_work','$norm', '$total_no', '$date')");

                    $update = $con->execute("UPDATE temp_reportico_ho_details_sample_analyzed SET counts = (SELECT COUNT(user_id) FROM temp_reportico_ho_details_sample_analyzed WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
                }

                $record_insert = 1;
            }
        }

        if ($record_insert == 1) {
            return 1;
        } else {
            return 0;
        }
    }
	
    //added for 26-08-2022 by shreeya
    //Details Of Samples Analysed Carry Forward For SampleType
    public static function getHoDetailsOfSamplesAnalysedCarryForwardForSampleType($month, $year, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_details_of_samples_analysed_carry_forward_for_sample_type WHERE user_id = '$user_id'");

        $sql = "SELECT  sa.alloc_to_user_code, sa.commodity_code,si.sample_type_code FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                INNER JOIN dmi_ro_offices AS ml ON ml.id=sa.lab_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND u.role IN ('Jr Chemist','Sr Chemist','Cheif Chemist')
                GROUP BY sa.alloc_to_user_code, sa.commodity_code,si.sample_type_code";
       
        $sql = $con->execute($sql);
        $recordNames = $sql->fetchAll('assoc');
        $sql->closeCursor();

        $record_insert = 0;
        foreach ($recordNames as $recordName) {
            $user_code = $recordName['alloc_to_user_code'];
            $commodity_code = $recordName['commodity_code'];

            $query = $con->execute("SELECT mc.commodity_name,  sa.alloc_date, sa.recby_ch_date, sa.commencement_date, CONCAT(u.f_name,' ',u.l_name,' (',u.role,') ') AS name_chemist,'NA' AS other, 'NA' AS other_work, 'Yes' AS norm, mst.sample_type_desc,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 1
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no'  AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                ) AS check_count,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 5
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                ) AS check_apex_count,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 4
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                ) AS challenged_count,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 7
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                ) AS ilc_count,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 2
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                ) AS research_count,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code AND sa.test_n_r = 'R' 
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                ) AS retesting_count,
                (
                SELECT COUNT(ct.test_code)
                FROM commodity_test ct
                INNER JOIN m_sample_allocate AS msa ON msa.commodity_code = ct.commodity_code AND msa.lab_code = '$ral_lab_no'
                INNER JOIN sample_inward si ON msa.org_sample_code = si.org_sample_code AND msa.commodity_code = si.commodity_code
                WHERE ct.commodity_code = $commodity_code AND msa.alloc_to_user_code = '$user_code' AND EXTRACT(MONTH
                FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM msa.alloc_date):: INTEGER = '$year'
                ) AS no_of_param,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name

                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                INNER JOIN dmi_ro_offices AS ml ON ml.id=sa.lab_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND sa.alloc_to_user_code = '$user_code'
                GROUP BY mc.commodity_name,  sa.alloc_date, sa.recby_ch_date, sa.commencement_date, name_chemist, mst.sample_type_desc, si.received_date
                ORDER BY si.received_date ASC");
            $records = $query->fetchAll('assoc');

            $query->closeCursor();
            if (!empty($records)) {
                foreach ($records as $record) {
                    $commodity_name = $record['commodity_name'];
                    $project_sample = 'NA';
                    $check_count = $record['check_count'];
                    $check_apex_count = $record['check_apex_count'];
                    $challenged_count = $record['challenged_count'];
                    $ilc_count = $record['ilc_count'];
                    $research_count = $record['research_count'];
                    $retesting_count = $record['retesting_count'];
                    $recby_ch_date = $record['recby_ch_date'];
                    $commencement_date = $record['commencement_date'];
                    $name_chemist = $record['name_chemist'];
                    $sample_type_desc = $record['sample_type_desc'];
                    $lab_name = $record['lab_name'];
                    $no_of_param = $record['no_of_param'];

                    if ($recby_ch_date == '' || $commencement_date == '') {
                        $working_days = '0';
                    } else {
                        $diff = abs(strtotime($commencement_date) - strtotime($recby_ch_date));
                        $years = floor($diff / (365 * 60 * 60 * 24));
                        $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
                        $working_days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
                    }
                    $other = $record['other'];
                    $other_work = $record['other_work'];
                    $norm = $record['norm'];
                    $date = date("d/m/Y");
                    $i = $i + 1;
                    $total_no = $check_count + $check_apex_count + $challenged_count + $ilc_count + $research_count + $retesting_count;

                    $insert = $con->execute("INSERT INTO temp_details_of_samples_analysed_carry_forward_for_sample_type 
                    (sr_no,user_id,months,lab_name,name_chemist,sample_type_desc,commodity_name,project_sample,check_count,check_apex_count,challenged_count,ilc_count,research_count,retesting_count,working_days,
                    no_of_param,other,other_work,norm,report_date,total_no) 
                    VALUES (
                    '$i','$user_id','$month','$lab_name','$name_chemist', '$sample_type_desc', '$commodity_name', '$project_sample', '$check_count', '$check_apex_count', '$challenged_count', '$ilc_count','$research_count','$retesting_count', '$working_days',
                    '$no_of_param', '$other','$other_work','$norm', '$date','$total_no')");

                    $update = $con->execute("UPDATE temp_details_of_samples_analysed_carry_forward_for_sample_type SET counts = (SELECT COUNT(user_id) FROM temp_details_of_samples_analysed_carry_forward_for_sample_type WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
                }
                                                                                                                                                        
                $record_insert = 1;
            }
        }

        if ($record_insert == 1) {
            return 1;
        } else {
            return 0;
        }
    }
	
    public static function getHoMonthlyCarryBroughtForward($month, $year, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ho_monthly_carry_brought_fwd WHERE user_id = '$user_id'");

        $sql = "SELECT mcc.category_code
                FROM dmi_users AS u
                INNER JOIN sample_inward si ON u.id = si.user_code
                INNER JOIN m_sample_allocate msa ON si.org_sample_code = msa.org_sample_code
                INNER JOIN m_commodity_category mcc ON si.category_code = mcc.category_code
                INNER JOIN dmi_ro_offices AS mlc ON mlc.id=u.posted_ro_office
                INNER JOIN user_role AS ur ON ur.role_name=u.role
                INNER JOIN dmi_user_roles AS r ON u.email=r.user_email_id
                WHERE mlc.id='$ral_lab_no' AND EXTRACT(MONTH
                FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM msa.alloc_date):: INTEGER = '$year' AND u.status = 'active'  
                GROUP BY mcc.category_code";

        $sql = $con->execute($sql);
        $recordNames = $sql->fetchAll('assoc');
        $sql->closeCursor();

        $record_insert = 0;
        foreach ($recordNames as $recordName) {
            $category_code = $recordName['category_code'];

            $query = $con->execute("SELECT mcc.category_name, mcc.category_code,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS bf_count
                    FROM code_decode cd
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '1'
                    INNER JOIN m_sample_allocate msa ON si.org_sample_code = msa.org_sample_code
                    INNER JOIN m_commodity_category mcc ON si.category_code = mcc.category_code
                    WHERE cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year' AND cd.status_flag NOT IN('G','F') AND mcc.category_code = '$category_code'
                    ) AS check_bf_count,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS check_apex_bf_count
                    FROM code_decode cd
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '5'
                    INNER JOIN m_sample_allocate msa ON si.org_sample_code = msa.org_sample_code
                    INNER JOIN m_commodity_category mcc ON si.category_code = mcc.category_code
                    WHERE cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year' AND cd.status_flag NOT IN('G','F') AND mcc.category_code = '$category_code'
                    ) AS check_apex_bf_count,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS challenged_bf_count
                    FROM code_decode cd
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '4'
                    INNER JOIN m_sample_allocate msa ON si.org_sample_code = msa.org_sample_code
                    INNER JOIN m_commodity_category mcc ON si.category_code = mcc.category_code
                    WHERE cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year' AND cd.status_flag NOT IN('G','F') AND mcc.category_code = '$category_code'
                    ) AS challenged_bf_count,
                    (
                    SELECT COUNT(DISTINCT(msa.sample_code)) AS received_count
                    FROM m_sample_allocate msa
                    INNER JOIN sample_inward si ON msa.org_sample_code = si.org_sample_code AND si.sample_type_code = '1' AND msa.category_code = si.category_code
                    WHERE msa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year'  AND mcc.category_code = '$category_code'
                    ) AS check_received_count,
                    (
                    SELECT COUNT(DISTINCT(msa.sample_code)) AS check_apex_received_count
                    FROM m_sample_allocate msa
                    INNER JOIN sample_inward si ON msa.org_sample_code = si.org_sample_code AND si.sample_type_code = '4' AND msa.category_code = si.category_code
                    WHERE msa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year'  AND mcc.category_code = '$category_code'
                    ) AS check_apex_received_count,
                    (
                    SELECT COUNT(DISTINCT(msa.sample_code)) AS challenged_received_count
                    FROM m_sample_allocate msa
                    INNER JOIN sample_inward si ON msa.org_sample_code = si.org_sample_code AND si.sample_type_code = '5' AND msa.category_code = si.category_code
                    WHERE msa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year'  AND mcc.category_code = '$category_code'
                    ) AS challenged_received_count, 
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS analyzed_count_in_month
                    FROM code_decode AS cd
                    INNER JOIN m_sample_allocate AS msa ON msa.sample_code=cd.sample_code
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '1' AND msa.category_code = si.category_code
                    WHERE test_n_r!='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year'  AND mcc.category_code = '$category_code'
                    ) AS check_analyzed_count_in_month,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS check_apex_analyzed_count_in_month
                    FROM code_decode AS cd
                    INNER JOIN m_sample_allocate AS msa ON msa.sample_code=cd.sample_code
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '4' AND msa.category_code = si.category_code
                    WHERE test_n_r!='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month'  AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year'  AND mcc.category_code = '$category_code'
                    ) AS check_apex_analyzed_count_in_month,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS challenged_analyzed_count_in_month
                    FROM code_decode AS cd
                    INNER JOIN m_sample_allocate AS msa ON msa.sample_code=cd.sample_code
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '5' AND msa.category_code = si.category_code
                    WHERE test_n_r!='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year'  AND mcc.category_code = '$category_code'
                    ) AS challenged_analyzed_count_in_month,
                    ( 
                    SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                    FROM dmi_users AS u
                    INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                    INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                    WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                    GROUP BY ral_lab
                    ) AS lab_name
                        
                    FROM dmi_users AS u
                    INNER JOIN sample_inward si ON u.id = si.user_code
                    INNER JOIN m_sample_allocate msa ON si.org_sample_code = msa.org_sample_code
                    INNER JOIN m_commodity_category mcc ON si.category_code = mcc.category_code
                    INNER JOIN dmi_ro_offices AS mlc ON mlc.id=u.posted_ro_office
                    INNER JOIN user_role AS ur ON ur.role_name=u.role
                    INNER JOIN dmi_user_roles AS r ON u.email=r.user_email_id
                    WHERE mlc.id='$ral_lab_no' AND u.status = 'active'AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year'  AND mcc.category_code = '$category_code'
                    GROUP BY mcc.category_name, mcc.category_code ");
            $records = $query->fetchAll('assoc');
            $query->closeCursor();
            if (!empty($records)) {
                foreach ($records as $record) {
                    $category_name = $record['category_name'];
                    $check_bf_count = $record['check_bf_count'];
                    $check_apex_bf_count = $record['check_apex_bf_count'];
                    $challenged_bf_count = $record['challenged_bf_count'];
                    $check_received_count = $record['check_received_count'];
                    $check_apex_received_count = $record['check_apex_received_count'];
                    $challenged_received_count = $record['challenged_received_count'];
                    $check_analyzed_count_in_month = $record['check_analyzed_count_in_month'];
                    $check_apex_analyzed_count_in_month = $record['check_apex_analyzed_count_in_month'];
                    $challenged_analyzed_count_in_month = $record['challenged_analyzed_count_in_month'];
                    $total_check = $check_bf_count + $check_received_count;
                    $total_check_apex = $check_apex_bf_count + $check_apex_received_count;
                    $total_challenged  = $challenged_bf_count + $challenged_received_count;
                    $carry_check = $total_check - $check_analyzed_count_in_month;
                    $carry_check_apex = $total_check_apex - $check_apex_analyzed_count_in_month;
                    $carry_challenged = $total_challenged - $challenged_analyzed_count_in_month;
                    $lab_name = $record['lab_name'];
                    $date = date("d/m/Y");
                    $i = $i + 1;

                    $insert = $con->execute("INSERT INTO temp_reportico_ho_monthly_carry_brought_fwd (sr_no, user_id, lab_name, category_name, check_bf_count, check_apex_bf_count, challenged_bf_count, check_received_count, check_apex_received_count, challenged_received_count, check_analyzed_count_in_month, check_apex_analyzed_count_in_month, challenged_analyzed_count_in_month, total_check, total_check_apex, total_challenged, carry_check, carry_check_apex, carry_challenged, report_date) 
                    VALUES (
                    '$i','$user_id','$lab_name','$category_name', '$check_bf_count', '$check_apex_bf_count', '$challenged_bf_count', '$check_received_count', '$check_apex_received_count', '$challenged_received_count', '$check_analyzed_count_in_month','$check_apex_analyzed_count_in_month', '$challenged_analyzed_count_in_month', '$total_check','$total_check_apex','$total_challenged', '$carry_check','$carry_check_apex','$carry_challenged', '$date')");

                    $update = $con->execute("UPDATE temp_reportico_ho_monthly_carry_brought_fwd SET counts = (SELECT COUNT(user_id) FROM temp_reportico_ho_monthly_carry_brought_fwd WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
                }

                $record_insert = 1;
            }
        }
        if ($record_insert == 1) {
            return 1;
        } else {
            return 0;
        }
    }

    // update report on 01-09-2022 by shreeya
    public static function getHoChemAnnMprDivisionWise($month, $year, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ho_info_mpr_division WHERE user_id = '$user_id'");

        $sql = "SELECT sa.alloc_to_user_code, sa.commodity_code, mcc.category_code, mst.sample_type_code
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN m_commodity_category mcc ON si.category_code = mcc.category_code
                WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'
                GROUP BY sa.alloc_to_user_code, sa.commodity_code,mcc.category_code, mst.sample_type_code";
    
        $sql = $con->execute($sql);
        $recordNames = $sql->fetchAll('assoc');
        $sql->closeCursor();

        $record_insert = 0;
        foreach ($recordNames as $recordName) {
            $user_code = $recordName['alloc_to_user_code'];
            $commodity_code = $recordName['commodity_code'];
            $sample_type = $recordName['sample_type_code'];
            $category_code = $recordName['category_code'];
          
            $query = $con->execute("SELECT mc.commodity_name,CONCAT(u.f_name,' ',u.l_name,' (',u.role,') ') AS  name_chemist,si.remark,mst.sample_type_desc,mcc.category_name,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS bf_count
                    FROM code_decode AS cd
                    INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                    WHERE cd.lab_code='$ral_lab_no' AND cd.status_flag='G' AND EXTRACT(MONTH
                    FROM si.received_date):: INTEGER = '$month' AND si.user_code = '$user_code'
                    ) AS bf_count,
                    (
                    SELECT COUNT(*) AS allotment_count
                    FROM code_decode AS cd
                    INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                    INNER JOIN m_sample_allocate AS msa ON cd.org_sample_code=msa.org_sample_code
                    WHERE cd.status_flag in ('N','C','G') AND cd.lab_code='$ral_lab_no'
                    AND EXTRACT(MONTH
                    FROM si.received_date):: INTEGER = '$month'
                    ) AS allotment_count,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS bf_count
                    FROM code_decode cd
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '1'
                    INNER JOIN m_sample_allocate msa ON si.org_sample_code = msa.org_sample_code
                    WHERE cd.lab_code='$ral_lab_no' AND cd.status_flag NOT IN('G','F') AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year' AND si.user_code = '$user_code'
                    ) AS check_bf_count,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS check_apex_bf_count
                    FROM code_decode cd
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '5'
                    INNER JOIN m_sample_allocate msa ON si.org_sample_code = msa.org_sample_code
                    WHERE cd.lab_code='$ral_lab_no' AND cd.status_flag NOT IN('G','F') AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year'  AND si.user_code = '$user_code'
                    ) AS check_apex_bf_count,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS challenged_bf_count
                    FROM code_decode cd
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '4'
                    INNER JOIN m_sample_allocate msa ON si.org_sample_code = msa.org_sample_code
                    WHERE cd.lab_code='$ral_lab_no' AND cd.status_flag NOT IN('G','F')AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year'   AND si.user_code = '$user_code'
                    ) AS challenged_bf_count,
                    (
                    SELECT COUNT(DISTINCT(msa.sample_code)) AS received_count
                    FROM m_sample_allocate msa
                    INNER JOIN sample_inward si ON msa.org_sample_code = si.org_sample_code AND si.sample_type_code = '1'
                    WHERE msa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year' AND msa.alloc_to_user_code = '$user_code'
                    ) AS check_received_count, 
                    (
                    SELECT COUNT(DISTINCT(msa.sample_code)) AS check_apex_received_count
                    FROM m_sample_allocate msa
                    INNER JOIN sample_inward si ON msa.org_sample_code = si.org_sample_code AND si.sample_type_code = '4'
                    WHERE msa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year' AND msa.alloc_to_user_code = '$user_code'
                    ) AS check_apex_received_count,
                    (
                    SELECT COUNT(DISTINCT(msa.sample_code)) AS challenged_received_count
                    FROM m_sample_allocate msa
                    INNER JOIN sample_inward si ON msa.org_sample_code = si.org_sample_code AND si.sample_type_code = '5'
                    WHERE msa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year' AND msa.alloc_to_user_code = '$user_code'
                    ) AS challenged_received_count,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS analyzed_count_in_month
                    FROM code_decode AS cd
                    INNER JOIN m_sample_allocate AS msa ON msa.sample_code=cd.sample_code
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '1' AND msa.commodity_code = si.commodity_code
                    WHERE test_n_r!='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year' AND msa.commodity_code = '$commodity_code'AND msa.alloc_to_user_code = '$user_code'
                    ) AS check_analyzed_count_in_month, 
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS check_apex_analyzed_count_in_month
                    FROM code_decode AS cd
                    INNER JOIN m_sample_allocate AS msa ON msa.sample_code=cd.sample_code
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '4' AND msa.commodity_code = si.commodity_code
                    WHERE test_n_r!='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year' AND msa.commodity_code = '$commodity_code'AND msa.alloc_to_user_code = '$user_code'
                    ) AS check_apex_analyzed_count_in_month,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS challenged_analyzed_count_in_month
                    FROM code_decode AS cd
                    INNER JOIN m_sample_allocate AS msa ON msa.sample_code=cd.sample_code
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '5' AND msa.commodity_code = si.commodity_code
                    WHERE test_n_r!='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year' AND msa.commodity_code = '$commodity_code' AND msa.alloc_to_user_code = '$user_code'
                    ) AS challenged_analyzed_count_in_month,
                    (
                    SELECT COUNT(ct.test_code)
                    FROM commodity_test ct
                    INNER JOIN m_sample_allocate AS msa ON msa.commodity_code = ct.commodity_code AND msa.lab_code = '$ral_lab_no'
                    INNER JOIN sample_inward si ON msa.org_sample_code = si.org_sample_code AND msa.commodity_code = si.commodity_code AND si.sample_type_code IN (1,4)
                    WHERE ct.commodity_code = $commodity_code AND msa.alloc_to_user_code = '$user_code' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year'
                    ) AS no_of_param,
                    (
                    SELECT COUNT(atd.alloc_to_user_code) FROM actual_test_data atd
                    INNER JOIN m_sample_allocate msa ON msa.alloc_to_user_code = atd.alloc_to_user_code AND msa.chemist_code = atd.chemist_code
                    INNER JOIN sample_inward si ON msa.org_sample_code = si.org_sample_code AND si.sample_type_code IN (1,4)
                    WHERE atd.lab_code = '$ral_lab_no' AND msa.commodity_code = '$commodity_code' AND msa.alloc_to_user_code = '$user_code' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year' 
                    ) AS no_of_para_analys,
                    ( 
                    SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                    FROM dmi_users AS u
                    INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                    INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                    WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                    GROUP BY ral_lab
                    ) AS lab_name

                    FROM sample_inward AS si
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code
                    INNER JOIN m_commodity_category mcc ON si.category_code = mcc.category_code 
                    INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                    INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                    INNER JOIN dmi_ro_offices AS ml ON ml.id=sa.lab_code
                    INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                    WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND sa.alloc_to_user_code = '$user_code'
                    GROUP BY sa.alloc_to_user_code,mc.commodity_name,mcc.category_name,u.f_name,u.l_name,u.role,si.remark ,mst.sample_type_desc
                    ORDER BY mc.commodity_name ASC ");

            $records = $query->fetchAll('assoc');
           
            $query->closeCursor();
            if (!empty($records)) {
                foreach ($records as $record) {
                    $category_name = $record['category_name'];
                    $commodity_name = $record['commodity_name'];
                    $bf_count = $record['bf_count'];
                    $user_code = $record['allotment_count'];
                    $no_of_para_analys = $record['no_of_para_analys'];
                    $no_of_param = $record['no_of_param'];
                    $remark = $record['remark'];
                    $check_bf_count = $record['check_bf_count'];
                    $check_apex_bf_count = $record['check_apex_bf_count'];
                    $challenged_bf_count = $record['challenged_bf_count'];
                    $check_received_count = $record['check_received_count'];
                    $check_apex_received_count = $record['check_apex_received_count'];
                    $challenged_received_count = $record['challenged_received_count'];
                    $check_analyzed_count_in_month = $record['check_analyzed_count_in_month'];
                    $check_apex_analyzed_count_in_month = $record['check_apex_analyzed_count_in_month'];
                    $challenged_analyzed_count_in_month = $record['challenged_analyzed_count_in_month'];
                    $total_check = $check_bf_count + $check_received_count;
                    $total_check_apex = $check_apex_bf_count + $check_apex_received_count;
                    $total_challenged  = $challenged_bf_count + $challenged_received_count;
                    $carry_check = $total_check - $check_analyzed_count_in_month;
                    $carry_check_apex = $total_check_apex - $check_apex_analyzed_count_in_month;
                    $carry_challenged = $total_challenged - $challenged_analyzed_count_in_month;
                    $name_chemist = $record['name_chemist'];
                    $no_of_param = $record['no_of_param'];
                    $no_of_para_analys = $record['no_of_para_analys'];
                    $remark = $record['remark'];
					$sample_type_desc = $record['sample_type_desc'];
                    $lab_name = $record['lab_name'];
                    $date = date("d/m/Y");
                    $i = $i + 1;

                    $insert = $con->execute("INSERT INTO temp_reportico_ho_info_mpr_division (sr_no, user_id, lab_name,category_name, commodity_name,sample_type_desc,bf_count,allotment_count,check_bf_count, check_apex_bf_count, challenged_bf_count, check_received_count, check_apex_received_count, challenged_received_count, check_analyzed_count_in_month, check_apex_analyzed_count_in_month, challenged_analyzed_count_in_month, total_check, total_check_apex, total_challenged, carry_check, carry_check_apex, carry_challenged, name_chemist, remark, no_of_param, no_of_para_analys, report_date) 
                    VALUES (
                    '$i','$user_id','$lab_name','$category_name','$commodity_name','$sample_type_desc','$bf_count','$user_code','$check_bf_count', '$check_apex_bf_count', '$challenged_bf_count', '$check_received_count', '$check_apex_received_count', '$challenged_received_count', '$check_analyzed_count_in_month','$check_apex_analyzed_count_in_month', '$challenged_analyzed_count_in_month', '$total_check','$total_check_apex','$total_challenged', '$carry_check','$carry_check_apex','$carry_challenged','$name_chemist','$remark', '$no_of_param', '$no_of_para_analys', '$date')"); 

                    $update = $con->execute("UPDATE temp_reportico_ho_info_mpr_division SET counts = (SELECT COUNT(user_id) FROM temp_reportico_ho_info_mpr_division WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
                }

                $record_insert = 1;
            }
        }

        if ($record_insert == 1) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function getHoDetailsSampleAnalyzedByRal($month, $year, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ho_details_sample_analyzed_ral WHERE user_id = '$user_id'");

        // $sql = "SELECT sa.commodity_code,si.sample_type_code
        //         FROM sample_inward AS si
        //         INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
        //         WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
        //         FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
        //         FROM sa.alloc_date):: INTEGER = '$year' ";
        
        $sql = "SELECT  sa.alloc_to_user_code, sa.commodity_code,si.sample_type_code FROM sample_inward AS si
        INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code
        INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
        INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
        INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
        INNER JOIN dmi_ro_offices AS ml ON ml.id=sa.lab_code
        INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
        WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
        FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
        FROM sa.alloc_date):: INTEGER = '$year'  AND u.role IN ('Jr Chemist','Sr Chemist','Cheif Chemist')
        GROUP BY sa.alloc_to_user_code, sa.commodity_code,si.sample_type_code ";
       
        $sql = $con->execute($sql);
        $recordNames = $sql->fetchAll('assoc');
        $sql->closeCursor();
       
       
        $record_insert = 0;
        foreach ($recordNames as $recordName) {
            $commodity_code = $recordName['commodity_code'];
            $sample_type = $recordName['sample_type_code'];
          
            $query = $con->execute("SELECT mc.commodity_name,  si.org_sample_code, mst.sample_type_desc,
                    (
                    SELECT COUNT(cd.sample_code)
                    FROM code_decode cd
                    INNER JOIN m_sample_allocate AS msa ON msa.sample_code=cd.sample_code
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '1' AND msa.commodity_code = si.commodity_code
                    WHERE test_n_r!='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month'AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year' AND msa.commodity_code = '$commodity_code'
                    ) AS commodity_counts,
                    (
                    SELECT COUNT(cd.sample_code)
                    FROM code_decode cd
                    INNER JOIN m_sample_allocate AS msa ON msa.sample_code=cd.sample_code
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '9' AND msa.commodity_code = si.commodity_code
                    WHERE cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month'AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year'
                    ) AS inter_lab_compare,
                    (
                    SELECT COUNT(cd.sample_code)
                    FROM code_decode cd
                    INNER JOIN m_sample_allocate AS msa ON msa.sample_code=cd.sample_code
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '0' AND msa.commodity_code = si.commodity_code
                    WHERE cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month'AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year'
                    ) AS pvt_sample,
                    (
                    SELECT COUNT(cd.sample_code)
                    FROM code_decode cd
                    INNER JOIN m_sample_allocate AS msa ON msa.sample_code=cd.sample_code
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '0' AND msa.commodity_code = si.commodity_code
                    WHERE cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month'AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year'
                    ) AS inter_check,
                    (
                    SELECT COUNT(cd.sample_code)
                    FROM code_decode cd
                    INNER JOIN m_sample_allocate AS msa ON msa.sample_code=cd.sample_code
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '0' AND msa.commodity_code = si.commodity_code
                    WHERE cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month'AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year'
                    ) AS proj_sample,
                    (
                    SELECT COUNT(cd.sample_code)
                    FROM code_decode cd
                    INNER JOIN m_sample_allocate AS msa ON msa.sample_code=cd.sample_code
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '0' AND msa.commodity_code = si.commodity_code
                    WHERE cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month'AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year'
                    ) AS repeat_sample,
                    (
                    SELECT COUNT(cd.sample_code)
                    FROM code_decode cd
                    INNER JOIN m_sample_allocate AS msa ON msa.sample_code=cd.sample_code
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '0' AND msa.commodity_code = si.commodity_code
                    WHERE cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month'AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year'
                    ) AS pt_samp,  
                    (
                    SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                    FROM dmi_users AS u
                    INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                    INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                    WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                    GROUP BY ral_lab
                    ) AS lab_name

                    FROM sample_inward AS si
                    INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                    INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                    INNER JOIN dmi_ro_offices AS ml ON ml.id=sa.lab_code
                    INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                    WHERE sa.lab_code='$ral_lab_no'  AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month'AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year'
                    GROUP BY mc.commodity_name, mst.sample_type_desc, si.org_sample_code, si.received_date
                    ORDER BY si.received_date ASC");

            $records = $query->fetchAll('assoc');
            $query->closeCursor();
            if (!empty($records)) {
                foreach ($records as $record) {
                    $commodity_name = $record['commodity_name'];
                    $org_sample_code = $record['org_sample_code'];
                    $commodity_counts = $record['commodity_counts'];
                    $inter_lab_compare = $record['inter_lab_compare'];
                    // $pvt_sample = $record['pvt_sample'];
                    // $inter_check = $record['inter_check'];
                    // $proj_sample = $record['proj_sample'];
                    // $repeat_sample = $record['repeat_sample'];
                    // $pt_samp = $record['pt_samp'];

                    $pvt_sample = 'NA';
                    $inter_check = 'NA';
                    $proj_sample = 'NA';
                    $repeat_sample = 'NA';
                    $pt_samp = 'NA';

                    $sample_type_desc = $record['sample_type_desc'];
                    $lab_name = $record['lab_name'];
                    $date = date("d/m/Y");
                    $i = $i + 1;

                    $insert = $con->execute("INSERT INTO temp_reportico_ho_details_sample_analyzed_ral (sr_no, user_id, lab_name,org_sample_code, sample_type_desc, commodity_name, commodity_counts, inter_lab_compare, pvt_sample, inter_check, proj_sample, repeat_sample, pt_samp, report_date) 
                    VALUES (
                    '$i','$user_id','$lab_name','$org_sample_code', '$sample_type_desc', '$commodity_name', '$commodity_counts', '$inter_lab_compare', '$pvt_sample', '$inter_check', '$proj_sample','$repeat_sample','$pt_samp', '$date')");

                    $update = $con->execute("UPDATE temp_reportico_ho_details_sample_analyzed_ral SET counts = (SELECT COUNT(user_id) FROM temp_reportico_ho_details_sample_analyzed_ral WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
                }

                $record_insert = 1;
            }
        }

        if ($record_insert == 1) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function getHoBifercationRal($month, $year, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ho_bifercation_ral WHERE user_id = '$user_id'");

        $sql = "SELECT sa.alloc_to_user_code
                FROM sample_inward AS si
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND u.role IN ('Jr Chemist','Sr Chemist','Cheif Chemist')";

        $sql = $con->execute($sql);
        $recordNames = $sql->fetchAll('assoc');
        $sql->closeCursor();

        $record_insert = 0;
        foreach ($recordNames as $recordName) {
            $user_code = $recordName['alloc_to_user_code'];

            $query = $con->execute("SELECT sa.alloc_to_user_code,sa.recby_ch_date, sa.commencement_date,'NA' AS other,'Yes' AS norms, CONCAT(u.f_name,' ',u.l_name,' (',u.role,') ') AS name_chemist,
                        (
                        SELECT COUNT(si.sample_type_code)
                        FROM sample_inward AS si
                        INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 1
                        INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                        INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                        WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                        FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                        FROM sa.alloc_date):: INTEGER = '$year' AND sa.alloc_to_user_code = '$user_code'
                        ) AS check_count,
                        (
                        SELECT COUNT(si.sample_type_code)
                        FROM sample_inward AS si
                        INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 0
                        INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                        INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                        WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                        FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                        FROM sa.alloc_date):: INTEGER = '$year' AND sa.alloc_to_user_code = '$user_code'
                        ) AS pvt_count,
                        (
                        SELECT COUNT(si.sample_type_code)
                        FROM sample_inward AS si
                        INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 2
                        INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                        INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                        WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                        FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                        FROM sa.alloc_date):: INTEGER = '$year' AND sa.alloc_to_user_code = '$user_code'
                        ) AS research_count,
                        (
                        SELECT COUNT(si.sample_type_code)
                        FROM sample_inward AS si
                        INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 7
                        INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                        INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                        WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                        FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                        FROM sa.alloc_date):: INTEGER = '$year' AND sa.alloc_to_user_code = '$user_code'
                        ) AS ilc_count,
                        (
                        SELECT COUNT(si.sample_type_code)
                        FROM sample_inward AS si
                        INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 0
                        INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                        INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                        WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                        FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                        FROM sa.alloc_date):: INTEGER = '$year' AND sa.alloc_to_user_code = '$user_code'
                        ) AS internal_check_count,
                        (
                        SELECT count(w.org_sample_code) 
                        FROM workflow w 
                        WHERE w.dst_loc_id = $ral_lab_no AND EXTRACT(MONTH
                        FROM w.tran_date):: INTEGER = '$month' AND EXTRACT(YEAR
                        FROM w.tran_date):: INTEGER = '$year' AND w.user_code = '$user_code'
                        ) AS sample_frm_cal,
                        (
                        SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                        FROM dmi_users AS u
                        INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                        INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                        WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                        GROUP BY ral_lab
                        ) AS lab_name

                        FROM sample_inward AS si
                        INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code
                        INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                        INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                        INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                        INNER JOIN dmi_ro_offices AS ml ON ml.id=sa.lab_code
                        INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                        WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                        FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                        FROM sa.alloc_date):: INTEGER = '$year' AND sa.alloc_to_user_code = '$user_code'
                        GROUP BY u.f_name,u.l_name, u.role, sa.alloc_to_user_code,sa.recby_ch_date, sa.commencement_date");

            $records = $query->fetchAll('assoc');
            $query->closeCursor();
            if (!empty($records)) {
                foreach ($records as $record) {
                    $other = $record['other'];
                    $norms = $record['norms'];
                    $name_chemist = $record['name_chemist'];
                    $check_count = $record['check_count'];
                    $pvt_count = $record['pvt_count'];
                    $research_count = $record['research_count'];
                    $ilc_count = $record['ilc_count'];
                    $internal_check_count = $record['internal_check_count'];
                    $sample_frm_cal = $record['sample_frm_cal'];
                    $total = $check_count + $pvt_count + $research_count + $ilc_count + $internal_check_count + $sample_frm_cal;
                    $lab_name = $record['lab_name'];
                    $date = date("d/m/Y");
                    $i = $i + 1;
                    $recby_ch_date = $record['recby_ch_date'];
                    $commencement_date = $record['commencement_date'];
                    if ($recby_ch_date == '' || $commencement_date == '') {
                        $working_days = '0';
                    } else {
                        $diff = abs(strtotime($commencement_date) - strtotime($recby_ch_date));
                        $years = floor($diff / (365 * 60 * 60 * 24));
                        $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
                        $working_days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
                    }

                    $insert = $con->execute("INSERT INTO temp_reportico_ho_bifercation_ral (sr_no, user_id, lab_name,other, norms, name_chemist, check_count, pvt_count, research_count, ilc_count, internal_check_count, sample_frm_cal, total, working_days, report_date) 
                    VALUES (
                    '$i','$user_id','$lab_name','$other', '$norms', '$name_chemist', '$check_count', '$pvt_count', '$research_count', '$ilc_count', '$internal_check_count','$sample_frm_cal','$total','$working_days','$date')");

                    $update = $con->execute("UPDATE temp_reportico_ho_bifercation_ral SET counts = (SELECT COUNT(user_id) FROM temp_reportico_ho_bifercation_ral WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
                }

                $record_insert = 1;
            }
        }

        if ($record_insert == 1) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function getHoMonthChekPendRal($month, $year, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ho_month_chk_pend_ral WHERE user_id = '$user_id'");

        $sql = "SELECT sa.alloc_to_user_code, sa.commodity_code
                FROM sample_inward AS si
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month'AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND u.role IN ('Jr Chemist','Sr Chemist','Cheif Chemist')";

        $sql = $con->execute($sql);
        $recordNames = $sql->fetchAll('assoc');
        $sql->closeCursor();

        $record_insert = 0;
        foreach ($recordNames as $recordName) {
            $user_code = $recordName['alloc_to_user_code'];
            $commodity_code = $recordName['commodity_code'];

            $query = $con->execute("SELECT sa.alloc_to_user_code, sa.commodity_code,si.remark, CONCAT(u.f_name,' ',u.l_name,' (',u.role,') ') AS name_chemist, mc.commodity_name,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS bf_count
                    FROM code_decode AS cd
                    INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                    INNER JOIN m_sample_allocate sa ON si.org_sample_code = sa.org_sample_code
                    WHERE cd.lab_code='$ral_lab_no' AND cd.status_flag NOT IN('G','F') AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month'AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND si.sample_type_code='1' AND cd.alloc_to_user_code='$user_code'
                    ) AS bf_count,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS allotment_count
                    FROM code_decode AS cd
                    INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                    INNER JOIN m_sample_allocate sa ON si.org_sample_code = sa.org_sample_code
                    WHERE cd.status_flag in ('N','C','G') AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month'AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND cd.lab_code='$ral_lab_no' AND si.sample_type_code='1' AND cd.alloc_to_user_code='$user_code'
                    ) AS allotment_count,
                    (
                    SELECT COUNT(sa.org_sample_code)
                    FROM m_sample_allocate sa
                    INNER JOIN sample_inward si ON si.org_sample_code = sa.org_sample_code
                    WHERE EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month'AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND sa.lab_code='$ral_lab_no' AND si.sample_type_code='1' AND sa.alloc_to_user_code='$user_code' AND sa.commodity_code = '$commodity_code'
                    ) AS check_analyze_commodity,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS pending_count
                    FROM code_decode AS cd
                    INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                    WHERE cd.status_flag='N' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month'AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND cd.alloc_to_user_code='$user_code' AND cd.lab_code='$ral_lab_no'
                    ) AS pending_count,
                    (
                    SELECT COUNT(atd.alloc_to_user_code) FROM actual_test_data atd
                    INNER JOIN m_sample_allocate msa ON msa.alloc_to_user_code = atd.alloc_to_user_code AND msa.chemist_code = atd.chemist_code
                    INNER JOIN sample_inward si ON msa.org_sample_code = si.org_sample_code
                    INNER JOIN commodity_test ct ON atd.test_code = ct.test_code
                    WHERE atd.lab_code = '$ral_lab_no' AND ct.commodity_code = '$commodity_code' AND msa.alloc_to_user_code = '$user_code' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year' 
                    ) AS no_of_para_analys,
                    (
                    SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                    FROM dmi_users AS u
                    INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                    INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                    WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                    GROUP BY ral_lab
                    ) AS lab_name
                    
                    FROM sample_inward AS si
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                    inner join m_commodity AS mc ON sa.commodity_code = mc.commodity_code
                    WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month'  AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year'AND sa.alloc_to_user_code = '$user_code'
                    GROUP BY sa.alloc_to_user_code, sa.commodity_code,si.remark,u.f_name,u.l_name,u.role, mc.commodity_name, sa.alloc_date");

            $records = $query->fetchAll('assoc');
            $query->closeCursor();
            if (!empty($records)) {
                foreach ($records as $record) {
                    $remark = $record['remark'];
                    $name_chemist = $record['name_chemist'];
                    $no_of_parameter = $record['no_of_para_analys'];
                    $bf_count = $record['bf_count'];
                    $allotment_count = $record['allotment_count'];
                    $check_analyze_commodity = $record['check_analyze_commodity'];
                    $pending_count = $record['pending_count'];
                    $reason = "NA";;
                    $total = $bf_count + $allotment_count + $check_analyze_commodity;
                    $lab_name = $record['lab_name'];
                    $commodity_name = $record['commodity_name'];
                    $date = date("d/m/Y");
                    $i = $i + 1;

                    $insert = $con->execute("INSERT INTO temp_reportico_ho_month_chk_pend_ral (sr_no, user_id, lab_name,remark, name_chemist, no_of_parameter, bf_count, allotment_count, check_analyze_commodity, pending_count, reason, commodity_name, total, report_date) 
                    VALUES (
                    '$i','$user_id','$lab_name','$remark', '$name_chemist', '$no_of_parameter', '$bf_count', '$allotment_count', '$check_analyze_commodity', '$pending_count','$reason','$commodity_name','$total','$date')");

                    $update = $con->execute("UPDATE temp_reportico_ho_month_chk_pend_ral SET counts = (SELECT COUNT(user_id) FROM temp_reportico_ho_month_chk_pend_ral WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
                }

                $record_insert = 1;
            }
        }

        if ($record_insert == 1) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function getHoCommodityWiseSampleRalAnnxeure($month, $years, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        if ($month == 02) {
            $month_one = 12;
            $month_two = $month - 1;
        } else if ($month == 01) {
            $month_one = 11;
            $month_two = 12;
        } else {
            $month_one = $month - 2;
            $month_two = $month - 1;
        }
        if (strlen($month_one) == 1) {
            $month_one = '0' . $month_one;
        }
        if (strlen($month_two) == 1) {
            $month_two = '0' . $month_two;
        }
        if ($month_one == 11 || $month_one == 12 || $month_two == 12) {
            $year_new = $years - 1;
        }
        if ($month_one != 11 || $month_one != 12 || $month_two != 12) {
            $year = $years;
        }

        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ho_commo_wise_sample_ral_annexure WHERE user_id = '$user_id'");

        $main_sql = "SELECT sa.commodity_code
                FROM sample_inward AS si
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month'  AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' GROUP BY sa.commodity_code";

        $main_sql = $con->execute($main_sql);
        $recordNames = $main_sql->fetchAll('assoc');
        $main_sql->closeCursor();

        $record_insert = 0;
        foreach ($recordNames as $recordName) {
            $commodity_code = $recordName['commodity_code'];

            $sql = "SELECT sa.commodity_code, mc.commodity_name, mgd.grade_desc, ";

            if ($month_one == 11 ||  $month_two == 12) {
                $sql .= " CASE when sa.lab_code='$ral_lab_no' AND (EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_one' OR EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_two') AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year_new' AND w.stage_smpl_flag= 'FG' AND sa.commodity_code = '$commodity_code' AND mgd.grade_code NOT IN(348,370) then mgd.grade_desc
                    end  as conformed_std,
            
                    CASE when sa.lab_code='$ral_lab_no' AND (EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_one' OR EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_two') AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year_new' AND w.stage_smpl_flag= 'FG' AND sa.commodity_code = '$commodity_code' AND mgd.grade_code IN(348,370) then mgd.grade_desc
                    end as misgrd, 

                    (SELECT COUNT(DISTINCT(cd.sample_code)) AS bf_count
                    FROM code_decode AS cd
                    INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                    INNER JOIN m_sample_allocate sa ON si.org_sample_code = sa.org_sample_code
                    WHERE cd.lab_code='$ral_lab_no' AND cd.status_flag NOT IN('G','F') AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_one' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year_new' AND sa.commodity_code='$commodity_code') AS bf_count, 
                    
                    (SELECT COUNT(DISTINCT(sa.sample_code)) AS received_count
                    FROM m_sample_allocate sa
                    WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_two' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year_new' AND sa.commodity_code='$commodity_code') AS received_count,
                    
                    (SELECT COUNT(DISTINCT(cd.sample_code)) AS analyzed_count_one
                    FROM code_decode AS cd
                    INNER JOIN m_sample_allocate AS sa ON sa.sample_code=cd.sample_code
                    WHERE test_n_r!='R' AND cd.lab_code='$ral_lab_no' AND (EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_one' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_two') AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year_new' AND sa.commodity_code='$commodity_code') AS sample_analyze, ";
            } else if ($month_one == 12) {
                $sql .= " CASE when sa.lab_code='$ral_lab_no' AND (EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_one' OR EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_two') AND (EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year_new' OR EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year') AND w.stage_smpl_flag= 'FG' AND sa.commodity_code = '$commodity_code' AND mgd.grade_code NOT IN(348,370) then mgd.grade_desc
                    end  as conformed_std,
            
                    CASE when sa.lab_code='$ral_lab_no' AND (EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_one' OR EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_two') AND (EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year_new' OR EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year') AND w.stage_smpl_flag= 'FG' AND sa.commodity_code = '$commodity_code' AND mgd.grade_code IN(348,370) then mgd.grade_desc
                    end as misgrd,

                    (SELECT COUNT(DISTINCT(cd.sample_code)) AS bf_count
                    FROM code_decode AS cd
                    INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                    INNER JOIN m_sample_allocate sa ON si.org_sample_code = sa.org_sample_code
                    WHERE cd.lab_code='$ral_lab_no' AND cd.status_flag NOT IN('G','F') AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_one' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year_new' AND sa.commodity_code='$commodity_code') AS bf_count, 
                    
                    (SELECT COUNT(DISTINCT(sa.sample_code)) AS received_count
                    FROM m_sample_allocate sa
                    WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_two' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND sa.commodity_code='$commodity_code') AS received_count,
                    
                    (SELECT COUNT(DISTINCT(cd.sample_code)) AS analyzed_count_one
                    FROM code_decode AS cd
                    INNER JOIN m_sample_allocate AS sa ON sa.sample_code=cd.sample_code
                    WHERE test_n_r!='R' AND cd.lab_code='$ral_lab_no' AND (EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_one' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_two') AND (EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year_new' OR EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year') AND sa.commodity_code='$commodity_code') AS sample_analyze, ";
            } else {
                $sql .= " CASE when sa.lab_code='$ral_lab_no' AND (EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month_one' OR EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month_two') AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND w.stage_smpl_flag= 'FG' AND sa.commodity_code = '$commodity_code' AND mgd.grade_code NOT IN(348,370) then mgd.grade_desc
                end  as conformed_std,
        
                CASE when sa.lab_code='$ral_lab_no' AND (EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month_one' OR EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month_two') AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND w.stage_smpl_flag= 'FG' AND sa.commodity_code = '$commodity_code' AND mgd.grade_code IN(348,370) then mgd.grade_desc
                end as misgrd,

                (SELECT COUNT(DISTINCT(cd.sample_code)) AS bf_count
                FROM code_decode AS cd
                INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                INNER JOIN m_sample_allocate sa ON si.org_sample_code = sa.org_sample_code
                WHERE cd.lab_code='$ral_lab_no' AND cd.status_flag NOT IN('G','F') AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month_one' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND sa.commodity_code='$commodity_code') AS bf_count, 
                
                (SELECT COUNT(DISTINCT(sa.sample_code)) AS received_count
                FROM m_sample_allocate sa
                WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month_two' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND sa.commodity_code='$commodity_code') AS received_count,
                
                (SELECT COUNT(DISTINCT(cd.sample_code)) AS analyzed_count_one
                FROM code_decode AS cd
                INNER JOIN m_sample_allocate AS sa ON sa.sample_code=cd.sample_code
                WHERE test_n_r!='R' AND cd.lab_code='$ral_lab_no' AND (EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month_one' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month_two') AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND sa.commodity_code='$commodity_code') AS sample_analyze, ";
            }

            $sql .= " ( SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab) AS lab_name
                
                FROM sample_inward AS si
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                INNER JOIN workflow w ON si.org_sample_code = w.org_sample_code
                INNER JOIN m_grade_desc mgd ON mgd.grade_code = si.grade
                INNER JOIN m_commodity AS mc ON sa.commodity_code = mc.commodity_code
                WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND sa.commodity_code = '$commodity_code'
                GROUP BY sa.commodity_code, mc.commodity_name, mgd.grade_desc,sa.lab_code, sa.alloc_date,w.stage_smpl_flag,mgd.grade_code";

            $query = $con->execute($sql);
            $records = $query->fetchAll('assoc');
            $query->closeCursor();
            if (!empty($records)) {
                foreach ($records as $record) {
                    $bf_count = $record['bf_count'];
                    $received_count = $record['received_count'];
                    $sample_analyze = $record['sample_analyze'];
                    $total = $bf_count + $received_count;
                    $conformed_std = $record['conformed_std'];
                    $misgrade = $record['misgrd'];
                    $cf_month = $total - $sample_analyze;
                    $lab_name = $record['lab_name'];
                    $commodity_name = $record['commodity_name'];
                    $date = date("d/m/Y");
                    $i = $i + 1;
                    if($conformed_std == ''){
                        $conformed_std = 'NULL';
                    }
                    if($misgrade == '')
                    {
                        $misgrade = "NULL";
                    }
                    
                    $insert = $con->execute("INSERT INTO temp_reportico_ho_commo_wise_sample_ral_annexure (sr_no, user_id, lab_name, commodity_name, bf_count, received_count, sample_analyze, total, conformed_std, misgrade, cf_month, report_date) 
                    VALUES (
                    '$i','$user_id','$lab_name', '$commodity_name','$bf_count', '$received_count', '$sample_analyze', '$total','$conformed_std','$misgrade','$cf_month','$date')");

                    $update = $con->execute("UPDATE temp_reportico_ho_commo_wise_sample_ral_annexure SET counts = (SELECT COUNT(user_id) FROM temp_reportico_ho_commo_wise_sample_ral_annexure WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
                }

                $record_insert = 1;
            }
        }

        if ($record_insert == 1) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function getHoStatementChkBfCfSample($month, $year, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ho_smt_chk_bf_cf_sample WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT u.role, si.remark,
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS bf_count
                FROM code_decode AS cd
                INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                INNER JOIN m_sample_allocate sa ON si.org_sample_code = sa.org_sample_code
                WHERE cd.lab_code='$ral_lab_no' AND cd.status_flag NOT IN('G','F') AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND si.sample_type_code='1'
                ) AS bf_count,
                (
                SELECT COUNT(DISTINCT(sa.sample_code)) AS received_count
                FROM m_sample_allocate sa
                INNER JOIN sample_inward si ON sa.org_sample_code = si.org_sample_code AND si.sample_type_code = '1'
                WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'
                ) AS received_count,
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS analyzed_count_one
                FROM code_decode AS cd
                INNER JOIN m_sample_allocate AS sa ON sa.sample_code=cd.sample_code
                WHERE test_n_r !='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'
                ) AS analyzed_count_original,
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS analyzed_count_two
                FROM code_decode AS cd
                INNER JOIN m_sample_allocate AS sa ON sa.sample_code=cd.sample_code
                WHERE test_n_r='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'
                ) AS analyzed_count_duplicate,
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS analyzed_count_two
                FROM code_decode AS cd
                INNER JOIN m_sample_allocate AS sa ON sa.sample_code=cd.sample_code
                WHERE test_n_r='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'
                ) AS analyzed_count_repeat,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name
                
                FROM sample_inward AS si
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                INNER JOIN m_commodity AS mc ON sa.commodity_code = mc.commodity_code
                WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND u.role IN ('Jr Chemist','Sr Chemist','Cheif Chemist')
                GROUP BY si.remark,u.role");
       
        $records = $query->fetchAll('assoc');
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $bf_count = $record['bf_count'];
                $received_count = $record['received_count'];
                $role = $record['role'];
                $total = $bf_count + $received_count;
                $analyzed_count_original = $record['analyzed_count_original'];
                $analyzed_count_duplicate = $record['analyzed_count_duplicate'];
                $analyzed_count_repeat = $record['analyzed_count_repeat'];
                $carray_forward = $total - $analyzed_count_original;
                $sancationed_strength = 'NA';
                $staff_strength = 'NA';
                $lab_name = $record['lab_name'];
                $remark = $record['remark'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ho_smt_chk_bf_cf_sample (sr_no, user_id, lab_name, role, bf_count, received_count, total, analyzed_count_original, analyzed_count_duplicate, analyzed_count_repeat, carry_forward, sancationed_strength, staff_strength, remark, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$role','$bf_count', '$received_count', '$total','$analyzed_count_original','$analyzed_count_duplicate','$analyzed_count_repeat','$carray_forward','$sancationed_strength','$staff_strength','$remark','$date')");

                $update = $con->execute("UPDATE temp_reportico_ho_smt_chk_bf_cf_sample SET counts = (SELECT COUNT(user_id) FROM temp_reportico_ho_smt_chk_bf_cf_sample WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getHoTimeTakenReport($month, $year, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ho_time_taken_report WHERE user_id = '$user_id'");

        $sql = "SELECT sa.commodity_code
                FROM sample_inward AS si
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month'  AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' GROUP BY sa.commodity_code";

        $sql = $con->execute($sql);
        $recordNames = $sql->fetchAll('assoc');
        $sql->closeCursor();

        $record_insert = 0;
        foreach ($recordNames as $recordName) {
            $commodity_code = $recordName['commodity_code'];
            $query = $con->execute("SELECT mc.commodity_name,si.received_date,si.dispatch_date,si.remark,si.dispatch_date:: DATE -si.received_date:: DATE AS time_taken,'NA' AS reason,
                    (
                    SELECT COUNT(si.stage_sample_code)
                    FROM sample_inward AS si
                    WHERE EXTRACT(MONTH
                    FROM si.received_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM si.received_date):: INTEGER = '$year' AND si.commodity_code = $commodity_code
                    ) AS sample_count,
                    (
                    SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                    FROM dmi_users AS u
                    INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                    INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                    WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                    GROUP BY ral_lab
                    ) AS lab_name

                    FROM sample_inward AS si
                    INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                    INNER JOIN dmi_users AS du ON du.id=si.user_code
                    INNER JOIN user_role AS ur ON ur.role_name=du.role AND EXTRACT(MONTH
                    FROM si.received_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM si.received_date):: INTEGER = '$year' AND si.commodity_code = $commodity_code
                    GROUP BY si.stage_sample_code,mc.commodity_name,si.received_date,si.dispatch_date,si.commodity_code,si.remark");
           
            $records = $query->fetchAll('assoc');
            $query->closeCursor();
            if (!empty($records)) {
                foreach ($records as $record) {
                    $commodity_name = $record['commodity_name'];
                    $received_date = $record['received_date'];
                    $dispatch_date = $record['dispatch_date'];
                    $remark = $record['remark'];
                    $time_taken = $record['time_taken'];
                    $lab_name = $record['lab_name'];
                    $reason = $record['reason'];
                    $sample_count = $record['sample_count'];
                    $date = date("d/m/Y");
                    $i = $i + 1;

                    $insert = $con->execute("INSERT INTO temp_reportico_ho_time_taken_report (sr_no, user_id, lab_name, commodity_name, received_date, dispatch_date, remark, time_taken, reason, sample_count, report_date) 
                    VALUES (
                    '$i','$user_id','$lab_name', '$commodity_name','$received_date', '$dispatch_date', '$remark', '$time_taken','$reason','$sample_count','$date')");

                    $update = $con->execute("UPDATE temp_reportico_ho_time_taken_report SET counts = (SELECT COUNT(user_id) FROM temp_reportico_ho_time_taken_report WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
                }

                $record_insert = 1;
            }
        }

        if ($record_insert == 1) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function getHoSmplAllotCodingSection($month, $year, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ho_smpl_coding_section WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT mc.commodity_name,si.stage_sample_code,CONCAT(si.sample_total_qnt,' ',muw.unit_weight) AS quantity,mst.sample_type_desc, 'ALL'AS parameter, 'NA' AS code_number
                FROM sample_inward AS si
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN m_sample_allocate msa ON msa.org_sample_code = si.org_sample_code
                INNER JOIN m_unit_weight AS muw ON muw.unit_id=si.parcel_size
                INNER JOIN dmi_users AS u ON u.id=si.user_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN workflow AS w ON w.org_sample_code=si.org_sample_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code AND w.stage_smpl_flag In ('OF','HF','FG') AND EXTRACT(MONTH
                FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM msa.alloc_date):: INTEGER = '$year' AND w.dst_loc_id='$ral_lab_no'
                GROUP BY mc.commodity_name,si.stage_sample_code,si.sample_total_qnt,muw.unit_weight,mst.sample_type_desc,si.remark
                ORDER BY mc.commodity_name ASC");

        $records = $query->fetchAll('assoc');
        // print_r($records);exit;
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $commodity_name = $record['commodity_name'];
                $stage_sample_code = $record['stage_sample_code'];
                $quantity = $record['quantity'];
                $sample_type_desc = $record['sample_type_desc'];
                $parameter = $record['parameter'];
                $code_number = $record['code_number'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ho_smpl_coding_section(sr_no, user_id, commodity_name, stage_sample_code, quantity, sample_type_desc, parameter, code_number, report_date) 
                VALUES (
                '$i','$user_id','$commodity_name', '$stage_sample_code','$quantity', '$sample_type_desc','$parameter','$code_number','$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getHoSmplAnalyticalSectionChemistAnalysis($month, $year, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ho_smpl_analytical_section_chemist_analysis WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT mc.commodity_name,'ALL'AS parameter, 'NA' AS code_number, si.remark
                FROM sample_inward AS si
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN m_sample_allocate msa ON msa.org_sample_code = si.org_sample_code
                INNER JOIN dmi_users AS u ON u.id=si.user_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN workflow AS w ON w.org_sample_code=si.org_sample_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                WHERE EXTRACT(MONTH
                FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM msa.alloc_date):: INTEGER = '$year' AND  w.dst_loc_id='$ral_lab_no' AND u.role IN ('Jr Chemist','Sr Chemist')
                GROUP BY mc.commodity_name,si.remark, u.f_name, u.l_name, u.role
                ORDER BY mc.commodity_name ASC");
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $commodity_name = $record['commodity_name'];
                $parameter = $record['parameter'];
                $code_number = $record['code_number'];
                $remark = $record['remark'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_ho_smpl_analytical_section_chemist_analysis(sr_no, user_id, commodity_name, parameter, code_number, remark, report_date) 
                VALUES (
                '$i','$user_id','$commodity_name', '$parameter','$code_number','$remark','$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getHoParticularSampleAnanlyzeReceiveRal($month, $year, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_ho_particular_analyze_receive WHERE user_id = '$user_id'");

        $sql = "SELECT sa.commodity_code,si.sample_type_code
                FROM sample_inward AS si
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month'  AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' GROUP BY sa.commodity_code,si.sample_type_code";

        $sql = $con->execute($sql);
        $recordNames = $sql->fetchAll('assoc');
        $sql->closeCursor();

        $record_insert = 0;
        foreach ($recordNames as $recordName) {
            $commodity_code = $recordName['commodity_code'];
            $sample_type_code = $recordName['sample_type_code'];
            $query = $con->execute("SELECT mc.commodity_name,si.remark, mst.sample_type_desc,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS bf_count
                    FROM code_decode AS cd
                    INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                    INNER JOIN m_sample_allocate sa ON si.org_sample_code = sa.org_sample_code
                    WHERE cd.lab_code='$ral_lab_no' AND cd.status_flag NOT IN('G','F') AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND sa.commodity_code='$commodity_code' AND si.sample_type_code = '$sample_type_code'
                    ) AS bf_count,    
                    (
                    SELECT COUNT(DISTINCT(sa.sample_code)) AS received_count
                    FROM m_sample_allocate sa
                    WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND sa.commodity_code='$commodity_code' AND si.sample_type_code = '$sample_type_code'
                    ) AS received_count,   
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS analyzed_count_one
                    FROM code_decode AS cd
                    INNER JOIN m_sample_allocate AS sa ON sa.sample_code=cd.sample_code
                    WHERE test_n_r !='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND sa.commodity_code='$commodity_code' AND si.sample_type_code = '$sample_type_code'
                    ) AS analyzed_count_original, 
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS analyzed_count_two
                    FROM code_decode AS cd
                    INNER JOIN m_sample_allocate AS sa ON sa.sample_code=cd.sample_code
                    WHERE test_n_r='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND sa.commodity_code='$commodity_code' AND si.sample_type_code = '$sample_type_code'
                    ) AS analyzed_count_duplicate,
                    (
                    SELECT COUNT(DISTINCT(sample_code)) AS received_count
                    FROM m_sample_allocate sa
                    WHERE lab_code='$ral_lab_no' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year'AND sa.commodity_code='$commodity_code' AND si.sample_type_code = '$sample_type_code'
                    ) AS received_count_year,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS analyzed_count_one
                    FROM code_decode AS cd
                    INNER JOIN m_sample_allocate AS sa ON sa.sample_code=cd.sample_code
                    WHERE test_n_r !='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND sa.commodity_code='$commodity_code' AND si.sample_type_code = '$sample_type_code'
                    ) AS analyzed_count_year,
                    (
                    SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                    FROM dmi_users AS u
                    INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                    INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                    WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                    GROUP BY ral_lab
                    ) AS lab_name
                    
                    FROM sample_inward AS si
                    INNER JOIN m_sample_type mst ON si.sample_type_code = mst.sample_type_code
                    INNER JOIN m_commodity mc ON si.commodity_code = mc.commodity_code
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                    WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND sa.commodity_code='$commodity_code'
                    GROUP BY sa.commodity_code,si.remark, si.sample_type_code, mc.commodity_name, mst.sample_type_desc");
           
            $records = $query->fetchAll('assoc');
            $query->closeCursor();
            if (!empty($records)) {
                foreach ($records as $record) {
                    $commodity_name = $record['commodity_name'];
                    $bf_count = $record['bf_count'];
                    $received_count = $record['received_count'];
                    $total = $bf_count + $received_count;
                    $remark = $record['remark'];
                    $analyzed_count_original = $record['analyzed_count_original'];
                    $analyzed_count_duplicate = $record['analyzed_count_duplicate'];
                    $analyzed_count_original = $record['analyzed_count_original'];
                    $received_count_year = $record['received_count_year'];
                    $analyzed_count_year = $record['analyzed_count_year'];
                    $carry_forward = $total - $analyzed_count_original;
                    $lab_name = $record['lab_name'];
                    $sample_type_desc = $record['sample_type_desc'];
                    $date = date("d/m/Y");
                    $i = $i + 1;

                    $insert = $con->execute("INSERT INTO temp_reportico_ho_particular_analyze_receive (sr_no, user_id, lab_name, commodity_name, sample_type_desc, bf_count, received_count, remark, total, analyzed_count_original, analyzed_count_duplicate, received_count_year, analyzed_count_year, carry_forward, report_date) 
                    VALUES (
                    '$i','$user_id','$lab_name', '$commodity_name','$sample_type_desc','$bf_count', '$received_count', '$remark', '$total','$analyzed_count_original','$analyzed_count_duplicate','$received_count_year','$analyzed_count_year','$carry_forward','$date')");

                    $update = $con->execute("UPDATE temp_reportico_ho_particular_analyze_receive SET counts = (SELECT COUNT(user_id) FROM temp_reportico_ho_particular_analyze_receive WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
                }

                $record_insert = 1;
            }
        }

        if ($record_insert == 1) {
            return 1;
        } else {
            return 0;
        }
    }


    /********************************************************************************************************************
     * Role Admin
     ********************************************************************************************************************/
    public static function getAdminRejectSample($from_date, $to_date, $posted_ro_office, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_admin_reject_sample WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT si.received_date,si.reject_date,CONCAT(du.f_name,' ',du.l_name) AS fullname,CONCAT(r.user_flag,',',mll.ro_office) AS labname,si.org_sample_code,mc.commodity_name,mst.sample_type_desc
                FROM sample_inward as si 
                JOIN (SELECT org_sample_code,max(id) AS id FROM workflow WHERE dst_loc_id='$ral_lab_no' GROUP BY(org_sample_code)) wf ON ((si.org_sample_code=wf.org_sample_code))
                JOIN workflow wff ON ((wf.id=wff.id))						
                Inner Join dmi_ro_offices AS mll ON mll.id=si.loc_id 
                Inner Join m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code 
                Inner Join dmi_users AS du ON du.id=si.user_code 
                Inner Join dmi_user_roles AS r On r.user_email_id=du.email
                Inner Join m_commodity AS mc ON mc.commodity_code=si.commodity_code And si.display='Y' AND si.acc_rej_flg='R'
                WHERE date(si.received_date) BETWEEN '$from_date' AND '$to_date'");
        // print_r($query);exit;
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $received_date = $record['received_date'];
                $reject_date = $record['reject_date'];
                $fullname = $record['fullname'];
                $labname = $record['labname'];
                $org_sample_code = $record['org_sample_code'];
                $commodity_name = $record['commodity_name'];
                $sample_type_desc = $record['sample_type_desc'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_admin_reject_sample (sr_no, user_id, received_date, reject_date, fullname, labname, org_sample_code, commodity_name, sample_type_desc, report_date) 
                VALUES (
                '$i','$user_id','$received_date', '$reject_date', '$fullname', '$labname', '$org_sample_code', '$commodity_name', '$sample_type_desc', '$date')");

                $update = $con->execute("UPDATE temp_reportico_admin_reject_sample SET counts = (SELECT COUNT(user_id) FROM temp_reportico_admin_reject_sample WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getAdminSampleRegister($from_date, $to_date, $posted_ro_office, $lab, $commodity, $sample, $category, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_admin_sample_register WHERE user_id = '$user_id'");

        $str = "SELECT si.letr_date,mct.category_name,CONCAT(r.user_flag,',',mll.ro_office) AS labname, si.letr_ref_no, si.org_sample_code,si.stage_sample_code,si.sample_total_qnt AS sample_qnt,mu.unit_weight,mp.par_condition_desc,si.sample_total_qnt,si.received_date, mc.commodity_name,si.commodity_code, si.category_code,st.sample_type_desc,si.loc_id, si.dispatch_date,si.dispatch_date, si.grading_date,si.remark,r.user_flag,sample_total_qnt
                FROM sample_inward AS si
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN m_commodity_category AS mct ON mct.category_code=si.category_code
                INNER JOIN m_par_condition AS mp ON mp.par_condition_code=si.par_condition_code
                INNER JOIN m_unit_weight AS mu ON mu.unit_id=si.parcel_size
                INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN dmi_users AS du ON du.id=si.user_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=du.email";

        $str .= " WHERE si.display='Y' AND si.acc_rej_flg='A' AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date'";

        if ($_SESSION['user_flag'] == "RO" || $_SESSION['user_flag'] == "SO") {
            $str .= " AND si.loc_id='" . $_SESSION['posted_ro_office'] . "'";
        }
        if ($_SESSION['user_flag'] == "RO" || $_SESSION['user_flag'] == "SO") {
            $str .= " AND r.user_flag='" . $_SESSION['user_flag'] . "'";
        }

        $str .= "AND si.loc_id='$ral_lab_no'  AND r.user_flag='$ral_lab_name' AND si.sample_type_code = '$sample' AND si.commodity_code='$commodity' AND si.category_code='$category' GROUP BY si.stage_sample_code,si.letr_date,sample_qnt,si.received_date,si.dispatch_date,si.grading_date,mll.ro_office,r.user_flag, mc.commodity_name,mct.category_name,si.commodity_code, si.category_code,st.sample_type_desc,si.loc_id,si.letr_ref_no,si.sample_total_qnt,mu.unit_weight,mp.par_condition_desc,si.dispatch_date, si.org_sample_code,si.remark
        ORDER BY received_date ASC";

        $query = $con->execute($str);
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $letr_date = $record['letr_date'];
                $category_name = $record['category_name'];
                $labname = $record['labname'];
                $letr_ref_no = $record['letr_ref_no'];
                $org_sample_code = $record['org_sample_code'];
                $stage_sample_code = $record['stage_sample_code'];
                $sample_qnt = $record['sample_qnt'];
                $unit_weight = $record['unit_weight'];
                $par_condition_desc = $record['par_condition_desc'];
                $sample_total_qnt = $record['sample_total_qnt'];
                $received_date = $record['received_date'];
                $commodity_name = $record['commodity_name'];
                $commodity_code = $record['commodity_code'];
                $category_code = $record['category_code'];
                $sample_type_desc = $record['sample_type_desc'];
                $loc_id = $record['loc_id'];
                $dispatch_date = $record['dispatch_date'];
                $grading_date = $record['grading_date'];
                $remark = $record['remark'];
                $user_flag = $record['user_flag'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_admin_sample_register (sr_no, user_id, letr_date, category_name, labname, letr_ref_no, org_sample_code, stage_sample_code, sample_qnt, unit_weight, par_condition_desc, sample_total_qnt, received_date, commodity_name, commodity_code, category_code, sample_type_desc, loc_id, dispatch_date, grading_date, remark, user_flag, report_date) 
                VALUES (
                '$i','$user_id','$letr_date', '$category_name', '$labname', '$letr_ref_no', '$org_sample_code', '$stage_sample_code', '$sample_qnt', '$unit_weight', '$par_condition_desc', '$sample_total_qnt', '$received_date', '$commodity_name', '$commodity_code', '$category_code', '$sample_type_desc', '$loc_id', '$dispatch_date', '$grading_date', '$remark', '$user_flag', '$date')");

                $update = $con->execute("UPDATE temp_reportico_admin_sample_register SET counts = (SELECT COUNT(user_id) FROM temp_reportico_admin_sample_register WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getAdminSampleReceivedRoSoRalCal($from_date, $to_date, $commodity, $posted_ro_office, $lab, $role, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_admin_sample_received_rosoralcal WHERE user_id = '$user_id'");

        $sql = "SELECT si.received_date,si.org_sample_code,mct.category_name,mc.commodity_name,mst.sample_type_desc,du.role,mll.ro_office,r.user_flag,CONCAT('$ral_lab_name',', ',mll.ro_office) AS lab_name
		        FROM sample_inward as si 
				INNER JOIN m_commodity_category AS mct ON mct.category_code=si.category_code
				INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id 
				INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code 
				INNER JOIN dmi_users AS du On du.id=si.user_code
				INNER JOIN dmi_user_roles AS r On r.user_email_id=du.email
				INNER JOIN workflow AS w On w.org_sample_code=si.org_sample_code
				INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code And si.display='Y'";

        if ($from_date != '' && $to_date != '') {
            $sql .= " WHERE date(si.received_date) BETWEEN '$from_date' and '$to_date'";
        }
        if ($commodity != '') {
            $sql .= " AND si.commodity_code='$commodity'";
        }
        if ($lab == "RO" || $lab == "SO") {
            $sql .= " AND si.loc_id='" . $posted_ro_office . "'";
        } else if ($lab == "RAL" && $role == "Inward Officer") {
            $sql .= " AND w.dst_loc_id='" . $posted_ro_office . "'";
        } else if ($lab == "CAL" && $role == "Inward Officer") {
            $sql .= " AND w.dst_loc_id='" . $posted_ro_office . "'";
        }
        $sql .= " Group By du.role,si.org_sample_code,mct.category_name,mc.commodity_name,mst.sample_type_desc,si.received_date,mll.ro_office,r.user_flag,du.f_name,du.l_name";

        $sql .= " ORDER BY si.received_date asc";

        $query = $con->execute($sql);
        $records = $query->fetchAll('assoc');
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $received_date = $record['received_date'];
                $org_sample_code = $record['org_sample_code'];
                $category_name = $record['category_name'];
                $commodity_name = $record['commodity_name'];
                $sample_type_desc = $record['sample_type_desc'];
                $role = $record['role'];
                $ro_office = $record['ro_office'];
                $user_flag = $record['user_flag'];
                $lab_name = $record['lab_name'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_admin_sample_received_rosoralcal (sr_no, user_id, received_date, org_sample_code, category_name, commodity_name, sample_type_desc, role, ro_office, user_flag, lab_name, report_date) 
                VALUES (
                '$i','$user_id','$received_date', '$org_sample_code', '$category_name', '$commodity_name', '$sample_type_desc', '$role', '$ro_office', '$user_flag', '$lab_name','$date')");

                $update = $con->execute("UPDATE temp_reportico_admin_sample_received_rosoralcal SET counts = (SELECT COUNT(user_id) FROM temp_reportico_admin_sample_received_rosoralcal WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getAdminSampleAcceptedByChemistForTesting($from_date, $to_date, $posted_ro_office, $lab, $user, $ral_lab, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_admin_sample_accepted_chemist_testing WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT CONCAT('$ral_lab_name',', ',mll.ro_office) AS lab_name,CONCAT(du.f_name,' ',du.l_name,' (',du.role,')') AS chemist_name, mll.ro_office, si.received_date,msa.org_sample_code,mc.commodity_name,mst.sample_type_desc
                FROM m_sample_allocate AS msa
                INNER JOIN sample_inward AS si ON si.org_sample_code=msa.org_sample_code
                INNER JOIN m_commodity AS mc ON si.commodity_code=mc.commodity_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code
                INNER JOIN dmi_users AS du ON du.id=msa.alloc_to_user_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=du.email 
                WHERE  msa.display='Y' AND msa.acptnce_flag='Y' AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date' AND msa.lab_code='$ral_lab_no' AND r.user_flag='$ral_lab_name' AND msa.alloc_to_user_code='$user'");
        // print_r($query);exit;
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $ro_office = $record['ro_office'];
                $chemist_name = $record['chemist_name'];
                $received_date = $record['received_date'];
                $org_sample_code = $record['org_sample_code'];
                $commodity_name = $record['commodity_name'];
                $sample_type_desc = $record['sample_type_desc'];
                $lab_name = $record['lab_name'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_admin_sample_accepted_chemist_testing (sr_no, user_id, ro_office, chemist_name, received_date, org_sample_code, commodity_name, sample_type_desc, lab_name, report_date) 
                VALUES (
                '$i','$user_id','$ro_office', '$chemist_name', '$received_date', '$org_sample_code', '$commodity_name', '$sample_type_desc', '$lab_name','$date')");

                $update = $con->execute("UPDATE temp_reportico_admin_sample_accepted_chemist_testing SET counts = (SELECT COUNT(user_id) FROM temp_reportico_admin_sample_accepted_chemist_testing WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getAdminSamplePendingForDispatch($from_date, $to_date, $posted_ro_office, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_admin_sample_pending WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT si.org_sample_code,mc.commodity_name,si.acc_rej_flg AS status,si.received_date,mst.sample_type_desc,mll.ro_office,r.user_flag
                FROM sample_inward AS si
                INNER JOIN code_decode AS cd ON cd.org_sample_code!=si.org_sample_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN m_commodity AS mc ON si.commodity_code=mc.commodity_code
                INNER JOIN dmi_users AS du ON du.id=si.user_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=du.email
                INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code AND si.display='Y' AND si.acc_rej_flg='P' AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date' AND si.loc_id='$ral_lab_no'
                GROUP BY mll.ro_office,si.acc_rej_flg,r.user_flag,si.org_sample_code,mc.commodity_name,si.received_date,mst.sample_type_desc
                ORDER BY si.received_date ASC");
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $org_sample_code = $record['org_sample_code'];
                $commodity_name = $record['commodity_name'];
                $status = $record['status'];
                $received_date = $record['received_date'];
                $sample_type_desc = $record['sample_type_desc'];
                $ro_office = $record['ro_office'];
                $user_flag = $record['user_flag'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_admin_sample_pending (sr_no, user_id, org_sample_code, commodity_name, status, received_date, sample_type_desc, ro_office, user_flag, report_date) 
                VALUES (
                '$i','$user_id','$org_sample_code', '$commodity_name', '$status', '$received_date', '$sample_type_desc', '$ro_office', '$user_flag', '$date')");

                $update = $con->execute("UPDATE temp_reportico_admin_sample_pending SET counts = (SELECT COUNT(user_id) FROM temp_reportico_admin_sample_pending WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getAdminSampleRegistration($from_date, $to_date, $posted_ro_office, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_admin_sample_registration WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT mc.commodity_name,si.grade,CONCAT(sd.pckr_nm,', ',pckr_addr) AS name_address_packer,sd.lot_no,si.received_date AS pack_date,sd.pack_size,sd.tbl,CONCAT(sd.shop_name,', ',sd.shop_address) AS shop_name_address,si.parcel_size,sd.smpl_drwl_dt,CONCAT(u.f_name,' ',u.l_name) AS name_officer,si.org_sample_code,si.dispatch_date,CONCAT(r.user_flag,', ',mll.ro_office) AS lab_or_office_name
                FROM sample_inward AS si
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN dmi_users AS u ON u.id=si.user_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN sample_inward_details AS sd ON sd.org_sample_code=si.stage_sample_code AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date' AND si.loc_id='$ral_lab_no' AND r.user_flag='$ral_lab_no'
                GROUP BY mll.ro_office,r.user_flag,si.grading_date,si.remark,si.grade,u.f_name,u.l_name,mc.commodity_name,si.grade,sd.pckr_nm,pckr_addr,si.received_date,sd.lot_no,sd.pack_size,sd.tbl,sd.shop_name,sd.shop_address,si.parcel_size,sd.smpl_drwl_dt, si.org_sample_code,si.dispatch_date,si.ral_lab_code,si.ral_anltc_rslt_rcpt_dt,si.anltc_rslt_chlng_flg,si.misgrd_param_value,si.misgrd_report_issue_dt, si.misgrd_reason,si.chlng_smpl_disptch_cal_dt,si.cal_anltc_rslt_rcpt_dt
                ORDER BY sd.smpl_drwl_dt DESC");
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $commodity_name = $record['commodity_name'];
                $grade = $record['grade'];
                $name_address_packer = $record['name_address_packer'];
                $lot_no = $record['lot_no'];
                $pack_date = $record['pack_date'];
                $pack_size = $record['pack_size'];
                $tbl = $record['tbl'];
                $shop_name_address = $record['shop_name_address'];
                $parcel_size = $record['parcel_size'];
                $smpl_drwl_dt = $record['smpl_drwl_dt'];
                $name_officer = $record['name_officer'];
                $org_sample_code = $record['org_sample_code'];
                $dispatch_date = $record['dispatch_date'];
                $lab_or_office_name = $record['lab_or_office_name'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_admin_sample_registration (sr_no, user_id, commodity_name, grade, name_address_packer, lot_no, pack_date, pack_size, tbl, shop_name_address, parcel_size, smpl_drwl_dt, name_officer, org_sample_code, dispatch_date, lab_or_office_name, report_date) 
                VALUES (
                '$i','$user_id','$commodity_name', '$grade', '$name_address_packer', '$lot_no', '$pack_date', '$pack_size', '$tbl', '$shop_name_address', '$parcel_size', '$smpl_drwl_dt', '$name_officer', '$org_sample_code', '$dispatch_date', '$lab_or_office_name', '$date')");

                $update = $con->execute("UPDATE temp_reportico_admin_sample_registration SET counts = (SELECT COUNT(user_id) FROM temp_reportico_admin_sample_registration WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getAdminCodingDecodingSection($from_date, $to_date, $posted_ro_office, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_admin_coding_decoding WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT mll.ro_office,mc.commodity_name,si.stage_sample_code,w.tran_date,si.received_date,si.sample_total_qnt,muw.unit_weight,mst.sample_type_desc,si.remark
                FROM sample_inward AS si
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN m_unit_weight AS muw ON muw.unit_id=si.parcel_size
                INNER JOIN dmi_users AS u ON u.id=si.user_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN workflow AS w ON w.org_sample_code=si.org_sample_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code AND w.stage_smpl_flag In ('OF','HF','FG') AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date' AND w.dst_loc_id='$ral_lab_no'
                GROUP BY mll.ro_office,mc.commodity_name,w.tran_date,si.stage_sample_code,si.received_date,si.sample_total_qnt,muw.unit_weight,mst.sample_type_desc,si.remark
                ORDER BY mc.commodity_name ASC");
        // print_r($query);exit;
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $ro_office = $record['ro_office'];
                $commodity_name = $record['commodity_name'];
                $stage_sample_code = $record['stage_sample_code'];
                $tran_date = $record['tran_date'];
                $sample_total_qnt = $record['sample_total_qnt'];
                $unit_weight = $record['unit_weight'];
                $sample_qnt = $sample_total_qnt . ' ' . $unit_weight;
                $sample_type_desc = $record['sample_type_desc'];
                $remark = $record['remark'];
                $received_date = $record['received_date'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_admin_coding_decoding (sr_no, user_id, ro_office, commodity_name, stage_sample_code, received_date, tran_date, sample_qnt, sample_type_desc, remark, report_date) 
                VALUES (
                '$i','$user_id','$ro_office', '$commodity_name', '$stage_sample_code','$received_date', '$tran_date', '$sample_qnt','$sample_type_desc','$remark','$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getAdminSampleAllotedToChemistForTesting($from_date, $to_date, $posted_ro_office, $lab, $user, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_admin_sample_alloted_chemist_testing WHERE user_id = '$user_id'");

        $str = "SELECT si.org_sample_code,mc.commodity_name,si.received_date,mst.sample_type_desc,mll.ro_office,r.user_flag,CONCAT(du.f_name,' ',du.l_name,' (',du.role,')') AS chemist_name,msa.alloc_date
                FROM sample_inward AS si
                INNER JOIN code_decode AS cd ON cd.org_sample_code=si.org_sample_code
                INNER JOIN m_sample_allocate AS msa ON msa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN m_commodity AS mc ON si.commodity_code=mc.commodity_code
                INNER JOIN dmi_users AS du ON du.id=cd.alloc_to_user_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=du.email
                INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code AND cd.display='Y' AND cd.chemist_code!='-' AND DATE(msa.alloc_date) BETWEEN '$from_date' AND '$to_date' AND cd.alloc_to_user_code='$user'";

        if ($_SESSION['user_flag'] == "RAL") {
            $str .= " AND cd.lab_code='" . $_SESSION['posted_ro_office'] . "'";
        } else {
            $str .= " AND cd.lab_code='$ral_lab_no'";
        }

        $str .= " ORDER BY si.received_date ASC";
        $query = $con->execute($str);


        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $org_sample_code = $record['org_sample_code'];
                $commodity_name = $record['commodity_name'];
                $chemist_name = $record['chemist_name'];
                $received_date = $record['received_date'];
                $org_sample_code = $record['org_sample_code'];
                $ro_office = $record['ro_office'];
                $sample_type_desc = $record['sample_type_desc'];
                $user_flag = $record['user_flag'];
                $alloc_date = $record['alloc_date'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_admin_sample_alloted_chemist_testing (sr_no, user_id, ro_office, chemist_name, received_date, org_sample_code, commodity_name, sample_type_desc, user_flag, alloc_date, report_date) 
                VALUES (
                '$i','$user_id','$ro_office', '$chemist_name', '$received_date', '$org_sample_code', '$commodity_name', '$sample_type_desc', '$user_flag','$alloc_date','$date')");

                $update = $con->execute("UPDATE temp_reportico_admin_sample_alloted_chemist_testing SET counts = (SELECT COUNT(user_id) FROM temp_reportico_admin_sample_alloted_chemist_testing WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getAdminSampleAllotedToChemistForRetesting($from_date, $to_date, $posted_ro_office, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_admin_sample_alloted_chemist_retesting WHERE user_id = '$user_id'");

        $str = "SELECT mll.ro_office,msa.org_sample_code,mc.commodity_name,si.received_date,mst.sample_type_desc,CONCAT(du.f_name,' ',du.l_name) AS chemist_name,msa.alloc_date
                FROM m_sample_allocate AS msa
                INNER JOIN sample_inward AS si ON si.org_sample_code=msa.org_sample_code
                INNER JOIN m_commodity AS mc ON si.commodity_code=mc.commodity_code
                INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN dmi_users AS du ON du.id=si.user_code AND msa.display='Y' AND msa.test_n_r='R' AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date'";

        if ($_SESSION['user_flag'] == "RAL") {
            $str .= " AND msa.lab_code='" . $_SESSION['posted_ro_office'] . "'";
        } else {
            $str .= " AND msa.lab_code='$ral_lab_no'";
        }

        $str .= " ORDER BY si.received_date ASC";

        $query = $con->execute($str);
        //  print_r($query);exit;
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $org_sample_code = $record['org_sample_code'];
                $commodity_name = $record['commodity_name'];
                $chemist_name = $record['chemist_name'];
                $received_date = $record['received_date'];
                $org_sample_code = $record['org_sample_code'];
                $ro_office = $record['ro_office'];
                $sample_type_desc = $record['sample_type_desc'];
                $alloc_date = $record['alloc_date'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_admin_sample_alloted_chemist_retesting (sr_no, user_id, ro_office, chemist_name, received_date, org_sample_code, commodity_name, sample_type_desc, alloc_date, report_date) 
                VALUES (
                '$i','$user_id','$ro_office', '$chemist_name', '$received_date', '$org_sample_code', '$commodity_name', '$sample_type_desc','$alloc_date','$date')");

                $update = $con->execute("UPDATE temp_reportico_admin_sample_alloted_chemist_retesting SET counts = (SELECT COUNT(user_id) FROM temp_reportico_admin_sample_alloted_chemist_retesting WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getAdminTestedSample($from_date, $to_date, $posted_ro_office, $user_code, $commodity, $lab, $sample_type, $ral_lab, $role, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_admin_tested_sample WHERE user_id = '$user_id'");

        if ($role == 'Jr Chemist' || $role == 'Sr Chemist' || $role == 'Cheif Chemist') {
            $str = "SELECT du.role,CONCAT(du.f_name,' ',du.l_name) as chemist_name, mll.ro_office, msa.recby_ch_date,mst.sample_type_desc,mc.commodity_name,cd.sample_code as org_sample_code,
            msa.expect_complt,msa.commencement_date ,CASE WHEN msa.commencement_date>msa.expect_complt THEN msa.commencement_date   ::date-msa.expect_complt ::date ELSE 0
            END as delay,expect_complt::date - recby_ch_date::date  as complete,si.received_date,
            r.user_flag,du.f_name,du.l_name,grade,
            (SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
            FROM dmi_users AS u
            INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
            INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
            WHERE u.status = 'active' AND o.id = '$ral_lab_no'
            GROUP BY ral_lab) AS lab_name
            FROM sample_inward as si 
            Inner Join m_sample_allocate as msa ON msa.org_sample_code=si.org_sample_code
            Inner Join m_sample_type as mst ON mst.sample_type_code=si.sample_type_code 
            Inner Join dmi_ro_offices as mll ON mll.id=si.loc_id 
            Inner Join code_decode as cd on cd.org_sample_code=si.org_sample_code
            Inner Join dmi_users as du ON du.id=cd.alloc_to_user_code
            Inner Join dmi_user_roles as r On r.user_email_id=du.email		
            Inner Join m_commodity as mc ON mc.commodity_code=si.commodity_code 
            WHERE cd.display='Y' and cd.alloc_to_user_code='" . $_SESSION['user_code'] . "' AND cd.lab_code='$posted_ro_office' AND date(si.received_date) BETWEEN '$from_date' and '$to_date'
            GROUP BY du.role,mll.ro_office,du.f_name,du.l_name,cd.sample_code,msa.commencement_date,msa.recby_ch_date,msa.expect_complt,cd.chemist_code,mc.commodity_name,mst.sample_type_desc,si.received_date,r.user_flag,du.f_name,du.l_name,si.grade order by si.received_date";
        } else {
            $str = "SELECT CONCAT(du.f_name,' ',du.l_name,' (',du.role,')') as chemist_name, mll.ro_office, du.role, msa.recby_ch_date,mst.sample_type_desc,mc.commodity_name,cd.sample_code as org_sample_code,
            msa.expect_complt,msa.commencement_date, CASE WHEN msa.commencement_date>msa.expect_complt THEN msa.commencement_date   ::date-msa.expect_complt ::date ELSE 0
            END as delay,expect_complt::date - recby_ch_date::date  as complete,si.received_date,
            r.user_flag,du.f_name,du.l_name,grade,
            (SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
            FROM dmi_users AS u
            INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
            INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
            WHERE u.status = 'active' AND o.id = '$ral_lab_no'
            GROUP BY ral_lab) AS lab_name
            FROM sample_inward as si 
            Inner Join m_sample_allocate as msa ON msa.org_sample_code=si.org_sample_code
            Inner Join m_sample_type as mst ON mst.sample_type_code=si.sample_type_code 
            Inner Join dmi_users as du ON du.id=msa.alloc_to_user_code
            Inner Join dmi_user_roles as r On r.user_email_id=du.email
            Inner Join dmi_ro_offices as mll ON mll.id=si.loc_id 
            Inner Join code_decode as cd on cd.org_sample_code=si.org_sample_code 
            Inner Join m_commodity as mc ON mc.commodity_code=si.commodity_code 
            WHERE cd.display='Y' AND date(si.received_date) BETWEEN '$from_date' and '$to_date' AND si.sample_type_code='$sample_type' AND si.commodity_code='$commodity' AND r.user_flag='$ral_lab_name' AND msa.lab_code='$ral_lab_no'
            GROUP BY du.role,mll.ro_office,du.f_name,du.l_name,cd.sample_code,msa.commencement_date,msa.recby_ch_date,msa.expect_complt,cd.chemist_code,mc.commodity_name,mst.sample_type_desc,si.received_date,r.user_flag,du.f_name,du.l_name,si.grade order by si.received_date";
        }
        $query = $con->execute($str);
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $ro_office = $record['ro_office'];
                $chemist_name = $record['chemist_name'];
                $recby_ch_date = $record['recby_ch_date'];
                $sample_type_desc = $record['sample_type_desc'];
                $commodity_name = $record['commodity_name'];
                $org_sample_code = $record['org_sample_code'];
                $expect_complt = $record['expect_complt'];
                $commencement_date = $record['commencement_date'];
                $grade = $record['grade'];
                $lab_name = $record['lab_name'];
                $role = $record['role'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_admin_tested_sample (sr_no, user_id, role, ro_office, chemist_name, recby_ch_date, org_sample_code, commodity_name, sample_type_desc, expect_complt, commencement_date, grade, lab_name, report_date) 
                VALUES (
                '$i','$user_id','$role','$ro_office', '$chemist_name', '$recby_ch_date', '$org_sample_code', '$commodity_name', '$sample_type_desc','$expect_complt','$commencement_date','$grade','$lab_name','$date')");

                $update = $con->execute("UPDATE temp_reportico_admin_tested_sample SET counts = (SELECT COUNT(user_id) FROM temp_reportico_admin_tested_sample WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getAdminTestSubmitByChemist($from_date, $to_date, $chemist_code, $sample_code, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_admin_test_submit_by_chemist WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT recby_ch_date,sa.chemist_code,sa.sample_code,c.commodity_name,CONCAT(du.f_name,' ',du.l_name,' (',du.role,') ') AS chemist_name,t.test_name,atd.result,'$chemist_code'AS chemist_code,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name
                FROM m_sample_allocate sa
                INNER JOIN actual_test_data AS atd ON sa.chemist_code=atd.chemist_code
                INNER JOIN code_decode AS cd ON cd.org_sample_code=sa.org_sample_code
                INNER JOIN m_test AS t ON atd.test_code=t.test_code
                INNER JOIN m_commodity AS c ON c.commodity_code=sa.commodity_code
                INNER JOIN dmi_users AS du ON du.id=sa.alloc_to_user_code AND DATE(sa.alloc_date) BETWEEN '$from_date' AND '$to_date' AND sa.chemist_code='$chemist_code'
                WHERE atd.chemist_code='$chemist_code' AND sa.sample_code='$sample_code' AND cd.status_flag in('C','G') AND sa.chemist_code='$chemist_code'
                GROUP BY du.role,sa.chemist_code,sa.recby_ch_date,sa.sample_code,c.commodity_name,du.f_name,du.l_name,t.test_name,atd.result");
        // print_r($query);exit;
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $recby_ch_date = $record['recby_ch_date'];
                $commodity_name = $record['commodity_name'];
                $sample_code = $record['sample_code'];
                $chemist_name = $record['chemist_name'];
                $test_name = $record['test_name'];
                $result = $record['result'];
                $chemist_code = $record['chemist_code'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_admin_test_submit_by_chemist(sr_no, user_id, lab_name, recby_ch_date, commodity_name, sample_code, chemist_name, test_name, result, chemist_code, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$recby_ch_date', '$commodity_name', '$sample_code', '$chemist_name','$test_name','$result','$chemist_code','$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getAdminCommodityWisePrivateAnalysis($lab, $posted_ro_office, $commodity, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_admin_commoditywise_private_analysis WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT si.fin_year,mc.commodity_name, COUNT(si.sample_type_code) AS sample_count,ml.ro_office, '$lab' AS lab_name
                FROM code_decode AS cd
                INNER JOIN sample_inward AS si ON cd.org_sample_code=si.org_sample_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN dmi_ro_offices AS ml ON si.loc_id=ml.id
                INNER JOIN dmi_users AS u ON u.id=si.user_code AND si.display='Y' AND si.sample_type_code='2' AND si.commodity_code='$commodity' AND cd.lab_code='$ral_lab_no'
                GROUP BY si.fin_year,mc.commodity_name,ml.ro_office
                ORDER BY commodity_name ASC");
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab = $record['lab_name'];
                $ro_office = $record['ro_office'];
                $lab_name = $lab . ', ' . $ro_office;
                $fin_year = $record['fin_year'];
                $commodity_name = $record['commodity_name'];
                $sample_count = $record['sample_count'];
                $ro_office = $record['ro_office'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_admin_commoditywise_private_analysis (sr_no, user_id, lab_name, fin_year, commodity_name, sample_count, ro_office, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$fin_year', '$commodity_name', '$sample_count','$ro_office','$date')");

                $update = $con->execute("UPDATE temp_reportico_admin_commoditywise_private_analysis SET counts = (SELECT COUNT(user_id) FROM temp_reportico_admin_commoditywise_private_analysis WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getAdminCommodityConsolidatedReport($month, $posted_ro_office, $sample_type, $commodity, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_admin_commodity_consolidated WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT commodity_name, 
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS bf_count
                FROM code_decode AS cd
                INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                WHERE cd.lab_code='$ral_lab_no' AND cd.status_flag='G' AND EXTRACT(MONTH
                FROM si.received_date):: INTEGER = '$month' AND si.sample_type_code='$sample_type' AND si.commodity_code='$commodity'
                ) AS bf_count,
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS analyzed_count
                FROM code_decode AS cd
                INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                WHERE cd.lab_code='$ral_lab_no' AND cd.status_flag='G' AND EXTRACT(MONTH
                FROM si.received_date):: INTEGER = '$month' AND si.sample_type_code='$sample_type' AND si.commodity_code='$commodity'
                ) AS analyzed_count,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users as u 
                INNER JOIN dmi_ro_offices as o On u.posted_ro_office=o.id
                Inner Join dmi_user_roles as r on r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name
                FROM m_commodity
                WHERE commodity_code='$commodity'
                ");
        // print_r($query);exit;
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $commodity_name = $record['commodity_name'];
                $bf_count = $record['bf_count'];
                $analyzed_count = $record['analyzed_count'];
                $carried_for = $bf_count - $analyzed_count;
                $date = date("d/m/Y");
                $month = date("F", mktime(0, 0, 0, $month, 10));
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_admin_commodity_consolidated (sr_no, user_id, lab_name, commodity_name, bf_count, analyzed_count, carried_for, month, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$commodity_name', '$bf_count','$analyzed_count', '$carried_for', '$month','$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getAdminBroughtForwardAnalysedCarrSample($month, $ral_lab, $posted_ro_office, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_admin_brg_fwd_ana_carr_fwd_sam WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT CONCAT(u.f_name,' ',u.l_name,' (',ur.role_name,') ') AS chemist_name,'$month' AS month,
                (
                SELECT COUNT(DISTINCT(sample_code)) AS bf_count
                FROM code_decode
                WHERE lab_code='$ral_lab_no' AND status_flag NOT IN('G','F')
                ) AS bf_count,
                (
                SELECT COUNT(DISTINCT(sample_code)) AS received_count
                FROM m_sample_allocate
                WHERE lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM alloc_date):: INTEGER = '$month'
                ) AS received_count,
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS analyzed_count_in_month
                FROM code_decode AS cd
                INNER JOIN m_sample_allocate AS sa ON sa.sample_code=cd.sample_code
                WHERE test_n_r!='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month'
                ) AS analyzed_count_in_month,                            
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS analyzed_count_in_month_repeat
                FROM code_decode AS cd
                INNER JOIN m_sample_allocate AS sa ON sa.sample_code=cd.sample_code
                WHERE test_n_r='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM alloc_date):: INTEGER = '$month'
                ) AS analyzed_count_in_month_repeat,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name
                
                from dmi_users as u
                INNER JOIN dmi_ro_offices AS mlc ON mlc.id=u.posted_ro_office
                INNER JOIN user_role AS ur ON ur.role_name=u.role
                INNER JOIN dmi_user_roles AS r ON u.email=r.user_email_id
                WHERE mlc.id='$ral_lab_no' AND u.role In ('Jr Chemist','Sr Chemist','Cheif Chemist') AND u.status = 'active'
                ");
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $chemist_name = $record['chemist_name'];
                $bf_count = $record['bf_count'];
                $received_count = $record['received_count'];
                $total = $bf_count + $received_count;
                $analyzed_count_in_month = $record['analyzed_count_in_month'];
                $analyzed_count_in_month_repeat = $record['analyzed_count_in_month_repeat'];
                $carried_for = $total - $analyzed_count_in_month;
                $monthNo = $record['month'];
                $month = date("F", mktime(0, 0, 0, $monthNo, 10));
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_admin_brg_fwd_ana_carr_fwd_sam (sr_no, user_id, lab_name, chemist_name, bf_count, received_count, total, analyzed_count_in_month, analyzed_count_in_month_repeat, carried_for, month, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$chemist_name', '$bf_count','$received_count', '$total', '$analyzed_count_in_month', '$analyzed_count_in_month_repeat', '$carried_for','$month','$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getAdminCategorywiseReceivedSample($from_date, $to_date, $posted_ro_office, $sample_type, $Category, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_admin_categorywise_received_sample WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT c.category_name, COUNT(*),st.sample_type_desc,ml.ro_office,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name
                FROM sample_inward AS si
                INNER JOIN m_commodity_category AS c ON c.category_code=si.category_code
                INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                INNER JOIN code_decode AS cd ON si.org_sample_code=cd.org_sample_code
                INNER JOIN dmi_ro_offices AS ml ON ml.id=cd.lab_code AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date' AND cd.lab_code='$ral_lab_no' AND si.category_code='$Category' AND si.sample_type_code='$sample_type'
                GROUP BY category_name,ml.ro_office,st.sample_type_desc
                ORDER BY c.category_name ASC");
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $count = $record['count'];
                $ro_office = $record['ro_office'];
                $sample_type_desc = $record['sample_type_desc'];
                $category_name = $record['category_name'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_admin_categorywise_received_sample (sr_no, user_id, lab_name, count, ro_office, sample_type_desc, category_name, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$count', '$ro_office', '$sample_type_desc','$category_name','$date')");

                $update = $con->execute("UPDATE temp_reportico_admin_categorywise_received_sample SET counts = (SELECT COUNT(user_id) FROM temp_reportico_admin_categorywise_received_sample WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getAdminCommodityCheckChallengedSample($from_date, $to_date, $commodity, $ral_lab_no, $ral_lab_name, $posted_ro_office)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_admin_commodity_check_challenged WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT commodity_name, 
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS bf_count
                FROM code_decode AS cd
                INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                WHERE cd.lab_code='$ral_lab_no' AND cd.status_flag NOT IN('G') AND si.sample_type_code In ('1','4') AND DATE(si.received_date) < '$from_date' AND si.commodity_code='$commodity'
                ) AS bf_count,
                (
                SELECT COUNT(DISTINCT(sa.sample_code)) AS received_count
                FROM m_sample_allocate AS sa
                INNER JOIN sample_inward AS si ON si.org_sample_code=sa.org_sample_code
                WHERE sa.lab_code='$ral_lab_no' AND si.sample_type_code In ('1','4') AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date' AND si.commodity_code='$commodity'
                ) AS received_count,
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS pass_count
                FROM code_decode AS cd
                INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                WHERE cd.lab_code='$ral_lab_no' AND si.remark='Pass' AND si.sample_type_code In ('1','4') AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date' AND si.commodity_code='$commodity'
                ) AS pass_count,
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS fail_count
                FROM code_decode AS cd
                INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                WHERE cd.lab_code='$ral_lab_no' AND si.remark='Fail' AND si.sample_type_code In ('1','4') AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date' AND si.commodity_code= '$commodity'
                ) AS fail_count,
                (
                Select CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                from dmi_users as u 
                Inner Join dmi_ro_offices as o On u.posted_ro_office=o.id
                Inner Join dmi_user_roles as r on r.user_email_id=u.email
                and r.user_flag='$ral_lab_name'
                where u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name

                FROM m_commodity where commodity_code='$commodity'
                ");
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $commodity_name = $record['commodity_name'];
                $bf_count = $record['bf_count'];
                $received_count = $record['received_count'];
                $total = $bf_count + $received_count;
                $pass_count = $record['pass_count'];
                $fail_count = $record['fail_count'];
                $total_analysis = $pass_count + $fail_count;
                $cf_total = $total - $total_analysis;
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_admin_commodity_check_challenged (sr_no, user_id, lab_name, commodity_name, bf_count, received_count, total, pass_count, fail_count, total_analysis, cf_total, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$commodity_name', '$bf_count','$received_count', '$total', '$pass_count', '$fail_count', '$total_analysis', '$cf_total','$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getAdminTimeTakenAnalysisSample($from_date, $to_date, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_admin_timetaken_analysis WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT si.stage_sample_code,mc.commodity_name,si.received_date,si.dispatch_date,si.dispatch_date:: DATE -si.received_date:: DATE AS time_taken,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name

                FROM sample_inward AS si
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN dmi_users AS du ON du.id=si.user_code
                INNER JOIN user_role AS ur ON ur.role_name=du.role AND ur.role_name IN('RO Officer','SO Officer') AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date'
                GROUP BY si.stage_sample_code,mc.commodity_name,si.received_date,si.dispatch_date
                ORDER BY si.received_date ASC");
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $stage_sample_code = $record['stage_sample_code'];
                $commodity_name = $record['commodity_name'];
                $received_date = $record['received_date'];
                $dispatch_date = $record['dispatch_date'];
                $time_taken = $record['time_taken'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_admin_timetaken_analysis (sr_no, user_id, lab_name, stage_sample_code, commodity_name, received_date, dispatch_date, time_taken, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$stage_sample_code', '$commodity_name', '$received_date','$dispatch_date','$time_taken','$date')");

                $update = $con->execute("UPDATE temp_reportico_admin_timetaken_analysis SET counts = (SELECT COUNT(user_id) FROM temp_reportico_admin_timetaken_analysis WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getAdminChemistWiseSampleAnalysis($month, $posted_ro_office, $user, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_admin_chemist_wise_samp_analysis WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT CONCAT(u.f_name,' ',u.l_name,'(',u.role,')') AS chemist_name, r.user_flag,drf.ro_office,u.id, 
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS check_analyzed_count
                FROM code_decode cd
                INNER JOIN sample_inward AS si ON si.stage_sample_code=cd.sample_code
                INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.sample_code=si.stage_sample_code
                WHERE cd.display='Y' AND st.sample_type_desc='Check' AND cd.status_flag In('C','G') AND cd.alloc_to_user_code='$user' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month'
                ) AS check_analyzed_count, 
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS res_analyzed_count
                FROM code_decode cd
                INNER JOIN sample_inward AS si ON si.stage_sample_code=cd.sample_code
                INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.sample_code=si.stage_sample_code
                WHERE cd.display='Y' AND st.sample_type_desc='Research' AND cd.status_flag In('C','G') AND cd.alloc_to_user_code='$user' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month'
                ) AS res_analyzed_count, 
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS res_challenged_count
                FROM code_decode cd
                INNER JOIN sample_inward AS si ON si.stage_sample_code=cd.sample_code
                INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.sample_code=si.stage_sample_code
                WHERE cd.display='Y' AND st.sample_type_desc='Challenged' AND cd.status_flag In('C','G') AND cd.alloc_to_user_code='$user' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month'
                ) AS res_challenged_count, 
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS othr_analyzed_count
                FROM code_decode cd
                INNER JOIN sample_inward AS si ON si.stage_sample_code=cd.sample_code
                INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.sample_code=si.stage_sample_code
                WHERE cd.display='Y' AND st.sample_type_desc NOT IN('Check','Research','Challenged','Apex(Check)') AND cd.status_flag In('C','G') AND cd.alloc_to_user_code='$user' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month'
                ) AS othr_analyzed_count,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name        
                
                FROM dmi_users u
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                INNER JOIN dmi_ro_offices drf ON u.posted_ro_office=drf.id
                WHERE u.posted_ro_office= '$ral_lab_no' AND u.role IN('Jr Chemist','Sr Chemist','Cheif Chemist') AND u.STATUS = 'active' AND u.id = '$user'
                ");
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $chemist_name = $record['chemist_name'];
                $check_analyzed_count = $record['check_analyzed_count'];
                $res_analyzed_count = $record['res_analyzed_count'];
                $res_challenged_count = $record['res_challenged_count'];
                $othr_analyzed_count = $record['othr_analyzed_count'];
                $date = date("d/m/Y");
                $month = date("F", mktime(0, 0, 0, $month, 10));
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_admin_chemist_wise_samp_analysis (sr_no, user_id, lab_name, chemist_name, check_analyzed_count, res_analyzed_count, res_challenged_count, othr_analyzed_count, month, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$chemist_name', '$check_analyzed_count','$res_analyzed_count','$res_challenged_count','$othr_analyzed_count', '$month','$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getAdminConsolidateSatementBroughtFwdCarriedFwdSample($month, $user, $posted_ro_office, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_admin_consoli_sample WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT CONCAT(u.f_name,' ',u.l_name) AS chemist_name,ur.role_name,'$month' AS month,
                (
                SELECT count(DISTINCT(sample_code)) AS bf_count 
                FROM code_decode 
                WHERE lab_code='$ral_lab_no' AND status_flag NOT IN('G','F' ) AND alloc_to_user_code='$user'
                ) AS bf_count,
                (
                SELECT count(DISTINCT(sample_code)) AS received_count 
                FROM m_sample_allocate 
                WHERE lab_code='$ral_lab_no' AND Extract(month FROM alloc_date)::INTEGER = '$month' AND alloc_to_user_code='$user'
                ) AS received_count,
                (
                SELECT count(DISTINCT(cd.sample_code)) AS analyzed_count_one 
                FROM code_decode AS cd
                INNER JOIN m_sample_allocate AS sa ON sa.sample_code=cd.sample_code 			
                WHERE test_n_r!='R' AND cd.lab_code='$ral_lab_no' AND Extract(month FROM sa.alloc_date)::INTEGER = '$month' AND cd.alloc_to_user_code='$user'
                ) AS analyzed_count_one,                            
                (
                SELECT count(DISTINCT(cd.sample_code)) AS analyzed_count_two FROM code_decode AS cd
                INNER JOIN m_sample_allocate AS sa ON sa.sample_code=cd.sample_code 			
                WHERE test_n_r='R' AND cd.lab_code='$ral_lab_no' AND Extract(month FROM alloc_date)::INTEGER = '$month' AND cd.alloc_to_user_code='$user'
                ) AS analyzed_count_two,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name
            
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS mlc ON mlc.id=u.posted_ro_office 							
                INNER JOIN user_role AS ur ON ur.role_name=u.role 
                INNER JOIN dmi_user_roles AS r on u.email=r.user_email_id
                WHERE mlc.id='$ral_lab_no' AND u.id='$user' And u.role IN ('Jr Chemist','Sr Chemist','Cheif Chemist') and u.status='active'");
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $bf_count = $record['bf_count'];
                $received_count = $record['received_count'];
                $total = $bf_count + $received_count;
                $analyzed_count_one = $record['analyzed_count_one'];
                $analyzed_count_two = $record['analyzed_count_two'];
                $carried_for = $total - $analyzed_count_one;
                $chemist_name = $record['chemist_name'];
                $role_name = $record['role_name'];
                $month = date("F", mktime(0, 0, 0, $record['month'], 10));
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_admin_consoli_sample (sr_no, user_id, lab_name, chemist_name, bf_count, received_count, total, analyzed_count_one, analyzed_count_two, carried_for, role_name, month, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$chemist_name', '$bf_count', '$received_count', '$total','$analyzed_count_one','$analyzed_count_two','$carried_for','$role_name','$month','$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static  function getAdminNoCheckPrivateResearchSample($from_date, $to_date, $posted_ro_office, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $begin = new DateTime($from_date);
        $end = new DateTime($to_date);
        $k = 0;
        while ($begin <= $end) {
            $yr_data[$k]['month'] = $begin->format('m');
            $yr_data[$k]['year'] = $begin->format('Y');
            $month1[$k] = $begin->format('M') . ',' . $begin->format('Y');

            $k++;
            $begin->modify('first day of next month');
        } //

        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_admin_chk_pvt_research WHERE user_id = '$user_id'");

        foreach ($yr_data as $da) {
            $month = $da['month'];
            $year = $da['year'];

            $query = $con->execute("SELECT dmi_ro_offices.id AS lab_code,dmi_user_roles.user_flag ,dmi_ro_offices.ro_office, 
                    (
                    SELECT COUNT(DISTINCT(cd.org_sample_code)) AS check_analyzed_count
                    FROM code_decode cd
                    INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                    INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    WHERE cd.display='Y' AND st.sample_type_desc='Check' AND cd.status_flag In('C','G') AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND cd.lab_code='$ral_lab_no' AND DATE(sa.alloc_date) BETWEEN '$from_date' AND '$to_date'
                    ) AS check_analyzed_count,
                    (
                    SELECT COUNT(DISTINCT(cd.org_sample_code)) AS res_analyzed_count
                    FROM code_decode cd
                    INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                    INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    WHERE cd.display='Y' AND st.sample_type_desc='Research' AND cd.status_flag In('C','G') AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND cd.lab_code='$ral_lab_no' AND DATE(sa.alloc_date) BETWEEN '$from_date' AND '$to_date'
                    ) AS res_analyzed_count,
                    (
                    SELECT COUNT(DISTINCT(cd.org_sample_code)) AS chk_analyzed_count
                    FROM code_decode cd
                    INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                    INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    WHERE cd.display='Y' AND st.sample_type_desc='Challenged' AND cd.status_flag In('C','G') AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND cd.lab_code='$ral_lab_no' AND DATE(sa.alloc_date) BETWEEN '$from_date' AND '$to_date'
                    ) AS chk_analyzed_count,
                    (
                    SELECT COUNT(DISTINCT(cd.org_sample_code)) AS othr_analyzed_count
                    FROM code_decode cd
                    INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                    INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    WHERE cd.display='Y' AND st.sample_type_desc NOT IN('Check','Research','Challenged') AND cd.status_flag In('C','G') AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND cd.lab_code='$ral_lab_no' AND DATE(sa.alloc_date) BETWEEN '$from_date' AND '$to_date'
                    ) AS othr_analyzed_count,
                    (
                    SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                    FROM dmi_users AS u
                    INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                    INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                    WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                    GROUP BY ral_lab
                    ) AS lab_name
                    
                    FROM dmi_users
                    INNER JOIN dmi_ro_offices ON dmi_users.posted_ro_office=dmi_ro_offices.id
                    INNER JOIN dmi_user_roles ON dmi_user_roles.user_email_id=dmi_users.email AND dmi_user_roles.user_flag In('RAL','CAL') AND dmi_users.posted_ro_office='$ral_lab_no'
                    WHERE dmi_users.status = 'active'
                    GROUP BY dmi_user_roles.user_flag,dmi_ro_offices.id,dmi_ro_offices.ro_office
                    ORDER BY dmi_ro_offices.ro_office ASC");

            $records = $query->fetchAll('assoc');
            // print_r($records);

            $query->closeCursor();
            if (!empty($records)) {
                foreach ($records as $record) {
                    $lab_name = $record['lab_name'];
                    $check_analyzed_count = $record['check_analyzed_count'];
                    $res_analyzed_count = $record['res_analyzed_count'];
                    $chk_analyzed_count = $record['chk_analyzed_count'];
                    $othr_analyzed_count = $record['othr_analyzed_count'];
                    $date = date("d/m/Y");
                    $i = $i + 1;

                    $insert = $con->execute("INSERT INTO temp_reportico_admin_chk_pvt_research (sr_no, user_id, lab_name, check_analyzed_count, res_analyzed_count, chk_analyzed_count, othr_analyzed_count,  report_date) 
                    VALUES (
                    '$i','$user_id','$lab_name', '$check_analyzed_count', '$res_analyzed_count', '$chk_analyzed_count', '$othr_analyzed_count','$date')");
                }
                return 1;
            } else {
                return 0;
            }
        }
    }

    public static function getAdminPerformanceRalCal($from_date, $to_date, $posted_ro_office, $lab, $sample_type, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $month = date("m", strtotime($from_date));

        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_admin_performance_ral_cal WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT drf.id AS lab_code, CONCAT(r.user_flag,', ',drf.ro_office) AS lab_name,
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS progress_sample
                FROM code_decode cd
                INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                WHERE cd.display='Y' AND si.sample_type_code='$sample_type' AND cd.lab_code='$ral_lab_no' AND cd.status_flag In('C','G') AND date(sa.alloc_date) BETWEEN '$from_date' AND '$to_date'
                ) AS progress_sample,
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS tot_sample_month
                FROM code_decode cd
                INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                INNER JOIN m_sample_type AS st ON st.sample_type_code=si.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                WHERE cd.display='Y' AND si.sample_type_code='$sample_type' AND cd.lab_code='$ral_lab_no' AND cd.status_flag In('C','G') AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = $month
                ) AS tot_sample_month
                
                FROM dmi_users u
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                INNER JOIN dmi_ro_offices drf ON u.posted_ro_office=drf.id
                WHERE u.status = 'active' AND r.user_flag='$ral_lab_name' AND drf.id='$ral_lab_no'
                GROUP BY r.user_flag,drf.id,drf.ro_office
                ORDER BY drf.ro_office ASC
                ");
       
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $progress_sample = $record['progress_sample'];
                $tot_sample_month = $record['tot_sample_month'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_admin_performance_ral_cal (sr_no, user_id, lab_name, progress_sample, tot_sample_month, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$progress_sample', '$tot_sample_month', '$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getAdminSampleAnalyzedByChemist($from_date, $to_date, $posted_ro_office, $user, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_admin_sample_analyzed_chemist WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT DISTINCT(sa.sample_code),CONCAT('$ral_lab_name',', ',ml.ro_office) AS lab_name,ml.ro_office,si.stage_sample_code,ml.id AS lab,CONCAT(r.user_flag,', ',ml.ro_office) AS sample_received_from,mc.commodity_name,sc.sam_condition_desc,ct.container_desc,pc.par_condition_desc,
                si.received_date,si.letr_ref_no, CONCAT(u.f_name,' ', u.l_name) AS name_chemist,si.sample_total_qnt,si.lab_code,si.grading_date,si.remark,sa.alloc_date
                FROM sample_inward AS si
                INNER JOIN m_sample_condition AS sc ON sc.sam_condition_code=si.sam_condition_code
                INNER JOIN m_par_condition AS pc ON pc.par_condition_code=si.par_condition_code
                INNER JOIN m_container_type AS ct ON ct.container_code=si.container_code
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                INNER JOIN dmi_ro_offices AS ml ON ml.id=sa.lab_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                WHERE sa.lab_code='$posted_ro_office' AND DATE(sa.alloc_date) BETWEEN '$from_date' AND '$to_date' AND sa.alloc_to_user_code='$user'
                ORDER BY si.received_date ASC");
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $sample_code = $record['sample_code'];
                $ro_office = $record['ro_office'];
                $stage_sample_code = $record['stage_sample_code'];
                $sample_received_from = $record['sample_received_from'];
                $commodity_name = $record['commodity_name'];
                $sam_condition_desc = $record['sam_condition_desc'];
                $container_desc = $record['container_desc'];
                $par_condition_desc = $record['par_condition_desc'];
                $received_date = $record['received_date'];
                $letr_ref_no = $record['letr_ref_no'];
                $name_chemist = $record['name_chemist'];
                $sample_total_qnt = $record['sample_total_qnt'];
                $lab_code = $record['lab_code'];
                $grading_date = $record['grading_date'];
                $remark = $record['remark'];
                $alloc_date = $record['alloc_date'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_admin_sample_analyzed_chemist (sr_no, user_id, lab_name, sample_code, ro_office, stage_sample_code, sample_received_from, commodity_name, sam_condition_desc, container_desc,par_condition_desc,received_date, letr_ref_no, name_chemist, sample_total_qnt, lab_code, grading_date, remark, alloc_date, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$sample_code', '$ro_office', '$stage_sample_code', '$sample_received_from','$commodity_name','$sam_condition_desc','$container_desc','$par_condition_desc','$received_date','$letr_ref_no','$name_chemist','$sample_total_qnt','$lab_code','$grading_date','$remark','$alloc_date','$date')");

                $update = $con->execute("UPDATE temp_reportico_admin_sample_analyzed_chemist SET counts = (SELECT COUNT(user_id) FROM temp_reportico_admin_sample_analyzed_chemist WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getAdminSampleAlloteAnalyzePending($from_date, $to_date, $posted_ro_office, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_admin_sample_allot_analyz_pend WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT dmi_ro_offices.id AS lab_code, CONCAT(r.user_flag,', ',dmi_ro_offices.ro_office) AS lab_name,
                (
                SELECT COUNT(*) AS allotment_count
                FROM code_decode AS cd
                INNER JOIN m_sample_allocate AS msa ON cd.org_sample_code=msa.org_sample_code
                WHERE cd.status_flag in ('N','C','G') AND DATE(msa.alloc_date) BETWEEN '$from_date' AND '$to_date' AND cd.lab_code='$ral_lab_no'
                ) AS allotment_count,   
                (
                SELECT COUNT(*) AS analyzed_count
                FROM code_decode AS cd
                INNER JOIN m_sample_allocate AS msa ON cd.org_sample_code=msa.org_sample_code
                WHERE cd.status_flag in ('C','G') AND DATE(msa.alloc_date) BETWEEN '$from_date' AND '$to_date' AND cd.lab_code='$ral_lab_no'
                ) AS analyzed_count,
                (
                SELECT COUNT(*) AS pending_count
                FROM code_decode AS cd
                INNER JOIN m_sample_allocate AS msa ON cd.org_sample_code=msa.org_sample_code
                WHERE cd.status_flag='N' AND DATE(msa.alloc_date) BETWEEN '$from_date' AND '$to_date' AND cd.lab_code='$ral_lab_no'
                ) AS pending_count
                
                FROM dmi_users
                INNER JOIN dmi_user_roles AS r ON dmi_users.email=r.user_email_id
                INNER JOIN dmi_ro_offices ON dmi_users.posted_ro_office=dmi_ro_offices.id AND r.user_flag In ('RAL','CAL') AND dmi_users.posted_ro_office='$ral_lab_no'
                WHERE dmi_users.status = 'active'
                GROUP BY r.user_flag,dmi_ro_offices.id,dmi_ro_offices.ro_office
                ORDER BY dmi_ro_offices.ro_office ASC");
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $allotment_count = $record['allotment_count'];
                $analyzed_count = $record['analyzed_count'];
                $pending_count = $record['pending_count'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_admin_sample_allot_analyz_pend (sr_no, user_id, lab_name, allotment_count, analyzed_count, pending_count, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$allotment_count', '$analyzed_count', '$pending_count', '$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getAdminReTestedSample($from_date, $to_date, $ral_lab_name, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_admin_retested_sample WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT msa.sample_code AS org_sample_code,mc.commodity_name,mst.sample_type_desc,si.received_date,ml.ro_office,CONCAT(du.f_name,' ',du.l_name) AS full_name,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name
                FROM sample_inward si
                INNER JOIN m_sample_allocate AS msa ON msa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_ro_offices AS ml ON ml.id=si.loc_id
                INNER JOIN dmi_users AS du ON du.id=si.user_code
                INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code 
                WHERE DATE(si.received_date) BETWEEN '$from_date' AND '$to_date' aND si.display='Y' AND msa.display='Y' AND msa.test_n_r='R' 
                GROUP BY msa.sample_code,mc.commodity_name,mst.sample_type_desc,si.received_date,ml.ro_office,du.f_name,du.l_name");
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $org_sample_code = $record['org_sample_code'];
                $commodity_name = $record['commodity_name'];
                $sample_type_desc = $record['sample_type_desc'];
                $received_date = $record['received_date'];
                $ro_office = $record['ro_office'];
                $full_name = $record['full_name'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_admin_retested_sample (sr_no, user_id, lab_name, org_sample_code, commodity_name, sample_type_desc, received_date, ro_office, full_name, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$org_sample_code', '$commodity_name', '$sample_type_desc', '$received_date','$ro_office','$full_name','$date')");

                $update = $con->execute("UPDATE temp_reportico_admin_retested_sample SET counts = (SELECT COUNT(user_id) FROM temp_reportico_admin_retested_sample WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getAdminReTestedSampleByChemist($from_date, $to_date, $ral_lab_name, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_admin_retested_sample_submit WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT  msa.sample_code AS org_sample_code,mc.commodity_name,mst.sample_type_desc,si.received_date,ml.lab_name AS lab,CONCAT(du.f_name,' ',du.l_name) AS full_name,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name
                FROM sample_inward AS si
                INNER JOIN m_lab AS ml ON ml.lab_code=si.loc_id
                INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code
                INNER JOIN dmi_users AS du ON du.id=si.user_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=du.email
                INNER JOIN m_sample_allocate AS msa ON msa.org_sample_code=si.org_sample_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code 
                WHERE si.display='Y' AND msa.display='Y' AND msa.test_n_r='R' AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date'
                GROUP BY msa.sample_code,mc.commodity_name,mst.sample_type_desc,si.received_date,ml.lab_name,du.f_name,du.l_name");
        $records = $query->fetchAll('assoc');

        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $lab_name = $record['lab_name'];
                $org_sample_code = $record['org_sample_code'];
                $commodity_name = $record['commodity_name'];
                $sample_type_desc = $record['sample_type_desc'];
                $received_date = $record['received_date'];
                $full_name = $record['full_name'];
                $lab = $record['lab'];
                $date = date("d/m/Y");
                $i = $i + 1;
                $insert = $con->execute("INSERT INTO temp_reportico_admin_retested_sample_submit (sr_no,user_id, lab_name, org_sample_code, commodity_name, sample_type_desc, received_date, full_name, lab, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$org_sample_code', '$commodity_name', '$sample_type_desc', '$received_date','$full_name','$lab','$date')");

                $update = $con->execute("UPDATE temp_reportico_admin_retested_sample_submit SET counts = (SELECT COUNT(user_id) FROM temp_reportico_admin_retested_sample_submit WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getAdminDetailsSampleAnalyzedByChemist($month, $year, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_admin_details_sample_analyzed WHERE user_id = '$user_id'");

        $sql = "SELECT  sa.alloc_to_user_code, sa.commodity_code FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                INNER JOIN dmi_ro_offices AS ml ON ml.id=sa.lab_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND u.role IN ('Jr Chemist','Sr Chemist','Cheif Chemist')
                GROUP BY sa.alloc_to_user_code,sa.commodity_code";

        $sql = $con->execute($sql);
        $recordNames = $sql->fetchAll('assoc');
        $sql->closeCursor();

        $record_insert = 0;
        foreach ($recordNames as $recordName) {
            $user_code = $recordName['alloc_to_user_code'];
            $commodity_code = $recordName['commodity_code'];

            $query = $con->execute("SELECT mc.commodity_name,  sa.alloc_date, sa.recby_ch_date, sa.commencement_date, CONCAT(u.f_name,' ',u.l_name,' (',u.role,') ') AS name_chemist, si.org_sample_code AS project_sample,
                'NA' AS other, 'NA' AS other_work, 'Yes' AS norm, mst.sample_type_desc,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 1
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no'  AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND sa.alloc_to_user_code = '$user_code'
                ) AS check_count,  
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 5
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND sa.alloc_to_user_code = '$user_code'
                ) AS check_apex_count,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 4
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND sa.alloc_to_user_code = '$user_code'
                ) AS challenged_count,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 9
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND sa.alloc_to_user_code = '$user_code'
                ) AS ilc_count, 
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 2
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND sa.alloc_to_user_code = '$user_code'
                ) AS research_count,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code AND sa.test_n_r = 'R' 
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND sa.alloc_to_user_code = '$user_code'
                ) AS retesting_count,
                ( 
                SELECT COUNT(ct.test_code)
                FROM commodity_test ct
                INNER JOIN m_sample_allocate AS msa ON msa.commodity_code = ct.commodity_code AND msa.lab_code = '$ral_lab_no'
                INNER JOIN sample_inward si ON msa.org_sample_code = si.org_sample_code AND msa.commodity_code = si.commodity_code
                WHERE ct.commodity_code = $commodity_code AND msa.alloc_to_user_code = '$user_code' AND EXTRACT(MONTH
                FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM msa.alloc_date):: INTEGER = '$year'
                ) AS no_of_param,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name
                
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                INNER JOIN dmi_ro_offices AS ml ON ml.id=sa.lab_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND sa.alloc_to_user_code = '$user_code'
                GROUP BY mc.commodity_name,  sa.alloc_date, sa.recby_ch_date, sa.commencement_date, project_sample,  name_chemist, mst.sample_type_desc, si.received_date
                ORDER BY si.received_date ASC");
            $records = $query->fetchAll('assoc');
            $query->closeCursor();
            if (!empty($records)) {
                foreach ($records as $record) {
                    $commodity_name = $record['commodity_name'];
                    $project_sample = $record['project_sample'];
                    $check_count = $record['check_count'];
                    $check_apex_count = $record['check_apex_count'];
                    $challenged_count = $record['challenged_count'];
                    $ilc_count = $record['ilc_count'];
                    $research_count = $record['research_count'];
                    $retesting_count = $record['retesting_count'];
                    $recby_ch_date = $record['recby_ch_date'];
                    $commencement_date = $record['commencement_date'];
                    $name_chemist = $record['name_chemist'];
                    $sample_type_desc = $record['sample_type_desc'];
                    $lab_name = $record['lab_name'];
                    $no_of_param = $record['no_of_param'];
                    if ($recby_ch_date == '' || $commencement_date == '') {
                        $working_days = '0';
                    } else {
                        $diff = abs(strtotime($commencement_date) - strtotime($recby_ch_date));
                        $years = floor($diff / (365 * 60 * 60 * 24));
                        $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
                        $working_days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
                    }
                    $other = $record['other'];
                    $other_work = $record['other_work'];
                    $norm = $record['norm'];
                    $date = date("d/m/Y");
                    $i = $i + 1;
                    $total_no = $check_count + $check_apex_count + $challenged_count + $ilc_count + $research_count + $retesting_count;

                    $insert = $con->execute("INSERT INTO temp_reportico_admin_details_sample_analyzed (sr_no, user_id, lab_name, name_chemist, sample_type_desc, commodity_name, project_sample, check_count, check_apex_count, challenged_count, ilc_count, research_count, retesting_count, no_of_param, working_days, other, other_work, norm, total_no, report_date) 
                    VALUES (
                    '$i','$user_id','$lab_name','$name_chemist', '$sample_type_desc', '$commodity_name', '$project_sample', '$check_count', '$check_apex_count', '$challenged_count', '$ilc_count','$research_count','$retesting_count', '$no_of_param','$working_days', '$other','$other_work','$norm', '$total_no', '$date')");

                    $update = $con->execute("UPDATE temp_reportico_admin_details_sample_analyzed SET counts = (SELECT COUNT(user_id) FROM temp_reportico_admin_details_sample_analyzed WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
                }

                $record_insert = 1;
            }
        }

        if ($record_insert == 1) {
            return 1;
        } else {
            return 0;
        }
    }
    //added for Details Of Samples Analysed SampleType 23-08-2022 by shreeya
    public static function getAdminDetailsOfSamplesAnalysedCarryForwardForSampleType($month, $year, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_admin_details_of_samples_analysed_carry_forward_for_sample WHERE user_id = '$user_id'");

        $sql = "SELECT  sa.alloc_to_user_code, sa.commodity_code,si.sample_type_code FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                INNER JOIN dmi_ro_offices AS ml ON ml.id=sa.lab_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND u.role IN ('Jr Chemist','Sr Chemist','Cheif Chemist')
                GROUP BY sa.alloc_to_user_code, sa.commodity_code,si.sample_type_code";
       
        $sql = $con->execute($sql);
        $recordNames = $sql->fetchAll('assoc');
        $sql->closeCursor();

        $record_insert = 0;
        foreach ($recordNames as $recordName) {
            $user_code = $recordName['alloc_to_user_code'];
            $commodity_code = $recordName['commodity_code'];

            $query = $con->execute("SELECT mc.commodity_name,  sa.alloc_date, sa.recby_ch_date, sa.commencement_date, CONCAT(u.f_name,' ',u.l_name,' (',u.role,') ') AS name_chemist,'NA' AS other, 'NA' AS other_work, 'Yes' AS norm, mst.sample_type_desc,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 1
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no'  AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                ) AS check_count,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 5
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                ) AS check_apex_count,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 4
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                ) AS challenged_count,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 9
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                ) AS ilc_count,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 2
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                ) AS research_count,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code AND sa.test_n_r = 'R' 
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                ) AS retesting_count,
                (
                SELECT COUNT(ct.test_code)
                FROM commodity_test ct
                INNER JOIN m_sample_allocate AS msa ON msa.commodity_code = ct.commodity_code AND msa.lab_code = '$ral_lab_no'
                INNER JOIN sample_inward si ON msa.org_sample_code = si.org_sample_code AND msa.commodity_code = si.commodity_code
                WHERE ct.commodity_code = $commodity_code AND msa.alloc_to_user_code = '$user_code' AND EXTRACT(MONTH
                FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM msa.alloc_date):: INTEGER = '$year'
                ) AS no_of_param,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name

                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                INNER JOIN dmi_ro_offices AS ml ON ml.id=sa.lab_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND sa.alloc_to_user_code = '$user_code'
                GROUP BY mc.commodity_name,  sa.alloc_date, sa.recby_ch_date, sa.commencement_date, name_chemist, mst.sample_type_desc, si.received_date
                ORDER BY si.received_date ASC");
            $records = $query->fetchAll('assoc');

            $query->closeCursor();
            if (!empty($records)) {
                foreach ($records as $record) {
                    $commodity_name = $record['commodity_name'];
                    $project_sample = 'NA';
                    $check_count = $record['check_count'];
                    $check_apex_count = $record['check_apex_count'];
                    $challenged_count = $record['challenged_count'];
                    $ilc_count = $record['ilc_count'];
                    $research_count = $record['research_count'];
                    $retesting_count = $record['retesting_count'];
                    $recby_ch_date = $record['recby_ch_date'];
                    $commencement_date = $record['commencement_date'];
                    $name_chemist = $record['name_chemist'];
                    $sample_type_desc = $record['sample_type_desc'];
                    $lab_name = $record['lab_name'];
                    $no_of_param = $record['no_of_param'];

                    if ($recby_ch_date == '' || $commencement_date == '') {
                        $working_days = '0';
                    } else {
                        $diff = abs(strtotime($commencement_date) - strtotime($recby_ch_date));
                        $years = floor($diff / (365 * 60 * 60 * 24));
                        $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
                        $working_days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
                    }
                    $other = $record['other'];
                    $other_work = $record['other_work'];
                    $norm = $record['norm'];
                    $date = date("d/m/Y");
                    $i = $i + 1;
                    $total_no = $check_count + $check_apex_count + $challenged_count + $ilc_count + $research_count + $retesting_count;

                    $insert = $con->execute("INSERT INTO temp_admin_details_of_samples_analysed_carry_forward_for_sample
                    (sr_no,user_id,months,lab_name,name_chemist,sample_type_desc,commodity_name,project_sample,check_count,check_apex_count,challenged_count,ilc_count,research_count,retesting_count,working_days,
                    no_of_param,other,other_work,norm,report_date,total_no) 
                    VALUES (
                    '$i','$user_id','$month','$lab_name','$name_chemist', '$sample_type_desc', '$commodity_name', '$project_sample', '$check_count', '$check_apex_count', '$challenged_count', '$ilc_count','$research_count','$retesting_count', '$working_days',
                    '$no_of_param', '$other','$other_work','$norm', '$date','$total_no')");

                    $update = $con->execute("UPDATE temp_admin_details_of_samples_analysed_carry_forward_for_sample SET counts = (SELECT COUNT(user_id) FROM temp_admin_details_of_samples_analysed_carry_forward_for_sample WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
                }
                                                                                                                                                        
                $record_insert = 1;
            }
        }

        if ($record_insert == 1) {
            return 1;
        } else {
            return 0;
        }
    }
    //added for consolidated report chemist on 23-08-2022 by shreeya
    public static function getAdminConsolidatedReporteAnalyzedByChemist($month, $year, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_admin_consolidated_reporte_analyzed_by_chemists WHERE user_id = '$user_id'");

        $sql = "SELECT  sa.alloc_to_user_code, sa.commodity_code, mst.sample_type_code,u.email FROM sample_inward AS si
        INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code
        INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
        INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
        INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
        INNER JOIN dmi_ro_offices AS ml ON ml.id=sa.lab_code
        INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
        WHERE si.entry_type = 'sub_sample' AND sa.lab_code ='$ral_lab_no' AND EXTRACT(MONTH
        FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
        FROM sa.alloc_date):: INTEGER = '$year' AND u.role IN ('Jr Chemist','Sr Chemist','Cheif Chemist')
        GROUP BY sa.alloc_to_user_code, sa.commodity_code, mst.sample_type_code,u.email";

        $sql1 = $con->execute($sql1);
        $recordNames = $sql1->fetchAll('assoc');
        $sql1->closeCursor();

        $record_insert = 0;
        foreach ($recordNames as $recordName) {
            $user_code = $recordName['alloc_to_user_code'];
            $commodity_code = $recordName['commodity_code'];

            $query = $con->execute("SELECT mc.commodity_name,  sa.alloc_date, sa.recby_ch_date, sa.commencement_date, CONCAT(u.f_name,' ',u.l_name,' (',u.role,') ') AS name_chemist,'NA' AS other, 'NA' AS other_work, 'Yes' AS norm, mst.sample_type_desc,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 1
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no'  AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                ) AS check_count,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 5
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                ) AS check_apex_count,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 4
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                ) AS challenged_count,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 9
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE si.entry_type = 'sub_sample' AND sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                ) AS ilc_count,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 2
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                ) AS research_count,
                (
                SELECT COUNT(si.sample_type_code) 
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code AND sa.test_n_r = 'R' 
                INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                ) AS retesting_count,
                (
                SELECT COUNT(ct.test_code)
                FROM commodity_test ct
                INNER JOIN m_sample_allocate AS msa ON msa.commodity_code = ct.commodity_code AND msa.lab_code = '$ral_lab_no'
                INNER JOIN sample_inward si ON msa.org_sample_code = si.org_sample_code AND msa.commodity_code = si.commodity_code
                WHERE ct.commodity_code = $commodity_code AND msa.alloc_to_user_code = '$user_code' AND EXTRACT(MONTH
                FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM msa.alloc_date):: INTEGER = '$year'
                ) AS no_of_param,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                INNER JOIN dmi_ro_offices AS ml ON ml.id=sa.lab_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND sa.alloc_to_user_code = '$user_code'
                GROUP BY mc.commodity_name,  sa.alloc_date, sa.recby_ch_date, sa.commencement_date, name_chemist, mst.sample_type_desc, si.received_date
                ORDER BY si.received_date ASC");

            $records = $query->fetchAll('assoc');
            $query->closeCursor();
            if (!empty($records)) {
                foreach ($records as $record) {
                    $name_chemist            = $record['name_chemist'];
                    $check_apex_count        = $record['check_apex_count'];
                    $challenged_count        = $record['challenged_count'];
                    $retesting_count         = $record['retesting_count'];
                    $ilc_count               = $record['ilc_count'];
                    $other_private_sample    = 'NA';
                    $research_count          = $record['research_count'];
                    $project_sample          = 'NA';
                    $smpl_analysed_instrn    = 'NA';
                    $check_count             = $record['check_count'];
                    $report_date             = date("d/m/Y");
                    $total_no                = 'NA';
                    $lab_name                = $record['lab_name'];
                    $sample_type_desc        = $record['sample_type_desc'];
                    $i                       = $i + 1;
                    $total_no                = $check_count + $check_apex_count + $challenged_count + $ilc_count + $research_count + $retesting_count;


                    $insert = $con->execute("INSERT INTO temp_admin_consolidated_reporte_analyzed_by_chemists (sr_no, user_id, lab_name, name_chemist, sample_type_desc, project_sample, check_count, check_apex_count, challenged_count, ilc_count, research_count, retesting_count,other_private_sample,smpl_analysed_instrn,  total_no, report_date) 
                    VALUES (
                    '$i','$user_id','$lab_name','$name_chemist', '$sample_type_desc',  '$project_sample', '$check_count', '$check_apex_count', '$challenged_count', '$ilc_count','$research_count','$retesting_count', '$other_private_sample','$smpl_analysed_instrn', '$total_no', '$report_date')");

                    $update = $con->execute("UPDATE temp_admin_consolidated_reporte_analyzed_by_chemists SET counts = (SELECT COUNT(user_id) FROM temp_admin_consolidated_reporte_analyzed_by_chemists WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
                }

                $record_insert = 1;
            }
        }

        if ($record_insert == 1) {
            return 1;
        } else {
            return 0;
        }
    }
    public static function getAdminMonthlyCarryBroughtForward($month, $year, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_admin_monthly_carry_brought_fwd WHERE user_id = '$user_id'");

        $sql = "SELECT mcc.category_code
                FROM dmi_users AS u
                INNER JOIN sample_inward si ON u.id = si.user_code
                INNER JOIN m_sample_allocate msa ON si.org_sample_code = msa.org_sample_code
                INNER JOIN m_commodity_category mcc ON si.category_code = mcc.category_code
                INNER JOIN dmi_ro_offices AS mlc ON mlc.id=u.posted_ro_office
                INNER JOIN user_role AS ur ON ur.role_name=u.role
                INNER JOIN dmi_user_roles AS r ON u.email=r.user_email_id
                WHERE mlc.id='$ral_lab_no' AND EXTRACT(MONTH
                FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM msa.alloc_date):: INTEGER = '$year' AND u.status = 'active'  
                GROUP BY mcc.category_code";

        $sql = $con->execute($sql);
        $recordNames = $sql->fetchAll('assoc');
        $sql->closeCursor();

        $record_insert = 0;
        foreach ($recordNames as $recordName) {
            $category_code = $recordName['category_code'];

            $query = $con->execute("SELECT mcc.category_name, mcc.category_code,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS bf_count
                    FROM code_decode cd
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '1'
                    INNER JOIN m_sample_allocate msa ON si.org_sample_code = msa.org_sample_code
                    INNER JOIN m_commodity_category mcc ON si.category_code = mcc.category_code
                    WHERE cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year' AND cd.status_flag NOT IN('G','F') AND mcc.category_code = '$category_code'
                    ) AS check_bf_count,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS check_apex_bf_count
                    FROM code_decode cd
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '5'
                    INNER JOIN m_sample_allocate msa ON si.org_sample_code = msa.org_sample_code
                    INNER JOIN m_commodity_category mcc ON si.category_code = mcc.category_code
                    WHERE cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year' AND cd.status_flag NOT IN('G','F') AND mcc.category_code = '$category_code'
                    ) AS check_apex_bf_count,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS challenged_bf_count
                    FROM code_decode cd
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '4'
                    INNER JOIN m_sample_allocate msa ON si.org_sample_code = msa.org_sample_code
                    INNER JOIN m_commodity_category mcc ON si.category_code = mcc.category_code
                    WHERE cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year' AND cd.status_flag NOT IN('G','F') AND mcc.category_code = '$category_code'
                    ) AS challenged_bf_count,
                    (
                    SELECT COUNT(DISTINCT(msa.sample_code)) AS received_count
                    FROM m_sample_allocate msa
                    INNER JOIN sample_inward si ON msa.org_sample_code = si.org_sample_code AND si.sample_type_code = '1' AND msa.category_code = si.category_code
                    WHERE msa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year'  AND mcc.category_code = '$category_code'
                    ) AS check_received_count,
                    (
                    SELECT COUNT(DISTINCT(msa.sample_code)) AS check_apex_received_count
                    FROM m_sample_allocate msa
                    INNER JOIN sample_inward si ON msa.org_sample_code = si.org_sample_code AND si.sample_type_code = '4' AND msa.category_code = si.category_code
                    WHERE msa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year'  AND mcc.category_code = '$category_code'
                    ) AS check_apex_received_count,
                    (
                    SELECT COUNT(DISTINCT(msa.sample_code)) AS challenged_received_count
                    FROM m_sample_allocate msa
                    INNER JOIN sample_inward si ON msa.org_sample_code = si.org_sample_code AND si.sample_type_code = '5' AND msa.category_code = si.category_code
                    WHERE msa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year'  AND mcc.category_code = '$category_code'
                    ) AS challenged_received_count, 
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS analyzed_count_in_month
                    FROM code_decode AS cd
                    INNER JOIN m_sample_allocate AS msa ON msa.sample_code=cd.sample_code
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '1' AND msa.category_code = si.category_code
                    WHERE test_n_r!='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year'  AND mcc.category_code = '$category_code'
                    ) AS check_analyzed_count_in_month,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS check_apex_analyzed_count_in_month
                    FROM code_decode AS cd
                    INNER JOIN m_sample_allocate AS msa ON msa.sample_code=cd.sample_code
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '4' AND msa.category_code = si.category_code
                    WHERE test_n_r!='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year'  AND mcc.category_code = '$category_code'
                    ) AS check_apex_analyzed_count_in_month,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS challenged_analyzed_count_in_month
                    FROM code_decode AS cd
                    INNER JOIN m_sample_allocate AS msa ON msa.sample_code=cd.sample_code
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '5' AND msa.category_code = si.category_code
                    WHERE test_n_r!='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year'  AND mcc.category_code = '$category_code'
                    ) AS challenged_analyzed_count_in_month,
                    (
                    SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                    FROM dmi_users AS u
                    INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                    INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                    WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                    GROUP BY ral_lab
                    ) AS lab_name
                    
                    FROM dmi_users AS u
                    INNER JOIN sample_inward si ON u.id = si.user_code
                    INNER JOIN m_sample_allocate msa ON msa.org_sample_code = si.org_sample_code
                    INNER JOIN m_commodity_category mcc ON si.category_code = mcc.category_code
                    INNER JOIN dmi_ro_offices AS mlc ON mlc.id=u.posted_ro_office
                    INNER JOIN user_role AS ur ON ur.role_name=u.role
                    INNER JOIN dmi_user_roles AS r ON u.email=r.user_email_id
                    WHERE mlc.id='$ral_lab_no' AND u.status = 'active' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year'    AND mcc.category_code = '$category_code'
                    GROUP BY mcc.category_name, mcc.category_code");

            $records = $query->fetchAll('assoc');
            $query->closeCursor();
            if (!empty($records)) {
                foreach ($records as $record) {
                    $category_name = $record['category_name'];
                    $check_bf_count = $record['check_bf_count'];
                    $check_apex_bf_count = $record['check_apex_bf_count'];
                    $challenged_bf_count = $record['challenged_bf_count'];
                    $check_received_count = $record['check_received_count'];
                    $check_apex_received_count = $record['check_apex_received_count'];
                    $challenged_received_count = $record['challenged_received_count'];
                    $check_analyzed_count_in_month = $record['check_analyzed_count_in_month'];
                    $check_apex_analyzed_count_in_month = $record['check_apex_analyzed_count_in_month'];
                    $challenged_analyzed_count_in_month = $record['challenged_analyzed_count_in_month'];
                    $total_check = $check_bf_count + $check_received_count;
                    $total_check_apex = $check_apex_bf_count + $check_apex_received_count;
                    $total_challenged  = $challenged_bf_count + $challenged_received_count;
                    $carry_check = $total_check - $check_analyzed_count_in_month;
                    $carry_check_apex = $total_check_apex - $check_apex_analyzed_count_in_month;
                    $carry_challenged = $total_challenged - $challenged_analyzed_count_in_month;
                    $lab_name = $record['lab_name'];
                    $date = date("d/m/Y");
                    $i = $i + 1;

                    $insert = $con->execute("INSERT INTO temp_reportico_admin_monthly_carry_brought_fwd (sr_no, user_id, lab_name, category_name, check_bf_count, check_apex_bf_count, challenged_bf_count, check_received_count, check_apex_received_count, challenged_received_count, check_analyzed_count_in_month, check_apex_analyzed_count_in_month, challenged_analyzed_count_in_month, total_check, total_check_apex, total_challenged, carry_check, carry_check_apex, carry_challenged, report_date) 
                    VALUES (
                    '$i','$user_id','$lab_name','$category_name', '$check_bf_count', '$check_apex_bf_count', '$challenged_bf_count', '$check_received_count', '$check_apex_received_count', '$challenged_received_count', '$check_analyzed_count_in_month','$check_apex_analyzed_count_in_month', '$challenged_analyzed_count_in_month', '$total_check','$total_check_apex','$total_challenged', '$carry_check','$carry_check_apex','$carry_challenged', '$date')");

                    $update = $con->execute("UPDATE temp_reportico_admin_monthly_carry_brought_fwd SET counts = (SELECT COUNT(user_id) FROM temp_reportico_admin_monthly_carry_brought_fwd WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
                }

                $record_insert = 1;
            }
        }

        if ($record_insert == 1) {
            return 1;
        } else {
            return 0;
        }
    }
    // InfoAnnMprDivisionWise
    // update on report 01-09-2022 by shreeya
    public static function getAdminChemAnnMprDivisionWise($month, $year, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_admin_info_mpr_division WHERE user_id = '$user_id'");

        // $sql = "SELECT sa.alloc_to_user_code, sa.commodity_code
        //         FROM sample_inward AS si
        //         INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
        //         WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
        //         FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
        //         FROM sa.alloc_date):: INTEGER = '$year'";

        $sql = "SELECT sa.alloc_to_user_code, sa.commodity_code, mcc.category_code, mst.sample_type_code
                FROM sample_inward AS si
                INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN m_commodity_category mcc ON si.category_code = mcc.category_code
                WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'
                GROUP BY sa.alloc_to_user_code, sa.commodity_code,mcc.category_code, mst.sample_type_code";

        $sql = $con->execute($sql);
        $recordNames = $sql->fetchAll('assoc');
        $sql->closeCursor();

        $record_insert = 0;
        foreach ($recordNames as $recordName) {
            $user_code = $recordName['alloc_to_user_code'];
            $commodity_code = $recordName['commodity_code'];
            $sample_type = $recordName['sample_type_code'];
            $category_code = $recordName['category_code'];
          
            $query = $con->execute("SELECT mc.commodity_name,CONCAT(u.f_name,' ',u.l_name,' (',u.role,') ') AS  name_chemist,si.remark,mst.sample_type_desc,mcc.category_name,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS bf_count
                    FROM code_decode AS cd
                    INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                    WHERE cd.lab_code='$ral_lab_no' AND cd.status_flag='G' AND EXTRACT(MONTH
                    FROM si.received_date):: INTEGER = '$month' AND si.user_code = '$user_code'
                    ) AS bf_count,
                    (
                    SELECT COUNT(*) AS allotment_count
                    FROM code_decode AS cd
                    INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                    INNER JOIN m_sample_allocate AS msa ON cd.org_sample_code=msa.org_sample_code
                    WHERE cd.status_flag in ('N','C','G') AND cd.lab_code='$ral_lab_no'
                    AND EXTRACT(MONTH
                    FROM si.received_date):: INTEGER = '$month'
                    ) AS allotment_count,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS bf_count
                    FROM code_decode cd
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '1'
                    INNER JOIN m_sample_allocate msa ON si.org_sample_code = msa.org_sample_code
                    WHERE cd.lab_code='$ral_lab_no' AND cd.status_flag NOT IN('G','F') AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year' AND si.user_code = '$user_code'
                    ) AS check_bf_count,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS check_apex_bf_count
                    FROM code_decode cd
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '5'
                    INNER JOIN m_sample_allocate msa ON si.org_sample_code = msa.org_sample_code
                    WHERE cd.lab_code='$ral_lab_no' AND cd.status_flag NOT IN('G','F') AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year'  AND si.user_code = '$user_code'
                    ) AS check_apex_bf_count,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS challenged_bf_count
                    FROM code_decode cd
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '4'
                    INNER JOIN m_sample_allocate msa ON si.org_sample_code = msa.org_sample_code
                    WHERE cd.lab_code='$ral_lab_no' AND cd.status_flag NOT IN('G','F')AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year'   AND si.user_code = '$user_code'
                    ) AS challenged_bf_count,
                    (
                    SELECT COUNT(DISTINCT(msa.sample_code)) AS received_count
                    FROM m_sample_allocate msa
                    INNER JOIN sample_inward si ON msa.org_sample_code = si.org_sample_code AND si.sample_type_code = '1'
                    WHERE msa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year' AND msa.alloc_to_user_code = '$user_code'
                    ) AS check_received_count, 
                    (
                    SELECT COUNT(DISTINCT(msa.sample_code)) AS check_apex_received_count
                    FROM m_sample_allocate msa
                    INNER JOIN sample_inward si ON msa.org_sample_code = si.org_sample_code AND si.sample_type_code = '4'
                    WHERE msa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year' AND msa.alloc_to_user_code = '$user_code'
                    ) AS check_apex_received_count,
                    (
                    SELECT COUNT(DISTINCT(msa.sample_code)) AS challenged_received_count
                    FROM m_sample_allocate msa
                    INNER JOIN sample_inward si ON msa.org_sample_code = si.org_sample_code AND si.sample_type_code = '5'
                    WHERE msa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year' AND msa.alloc_to_user_code = '$user_code'
                    ) AS challenged_received_count,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS analyzed_count_in_month
                    FROM code_decode AS cd
                    INNER JOIN m_sample_allocate AS msa ON msa.sample_code=cd.sample_code
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '1' AND msa.commodity_code = si.commodity_code
                    WHERE test_n_r!='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year' AND msa.commodity_code = '$commodity_code'AND msa.alloc_to_user_code = '$user_code'
                    ) AS check_analyzed_count_in_month, 
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS check_apex_analyzed_count_in_month
                    FROM code_decode AS cd
                    INNER JOIN m_sample_allocate AS msa ON msa.sample_code=cd.sample_code
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '4' AND msa.commodity_code = si.commodity_code
                    WHERE test_n_r!='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year' AND msa.commodity_code = '$commodity_code'AND msa.alloc_to_user_code = '$user_code'
                    ) AS check_apex_analyzed_count_in_month,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS challenged_analyzed_count_in_month
                    FROM code_decode AS cd
                    INNER JOIN m_sample_allocate AS msa ON msa.sample_code=cd.sample_code
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '5' AND msa.commodity_code = si.commodity_code
                    WHERE test_n_r!='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year' AND msa.commodity_code = '$commodity_code' AND msa.alloc_to_user_code = '$user_code'
                    ) AS challenged_analyzed_count_in_month,
                    (
                    SELECT COUNT(ct.test_code)
                    FROM commodity_test ct
                    INNER JOIN m_sample_allocate AS msa ON msa.commodity_code = ct.commodity_code AND msa.lab_code = '$ral_lab_no'
                    INNER JOIN sample_inward si ON msa.org_sample_code = si.org_sample_code AND msa.commodity_code = si.commodity_code AND si.sample_type_code IN (1,4)
                    WHERE ct.commodity_code = $commodity_code AND msa.alloc_to_user_code = '$user_code' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year'
                    ) AS no_of_param,
                    (
                    SELECT COUNT(atd.alloc_to_user_code) FROM actual_test_data atd
                    INNER JOIN m_sample_allocate msa ON msa.alloc_to_user_code = atd.alloc_to_user_code AND msa.chemist_code = atd.chemist_code
                    INNER JOIN sample_inward si ON msa.org_sample_code = si.org_sample_code AND si.sample_type_code IN (1,4)
					 
                    WHERE atd.lab_code = '$ral_lab_no' AND msa.commodity_code = '$commodity_code' AND msa.alloc_to_user_code = '$user_code' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year' 
                    ) AS no_of_para_analys,
                    ( 
                    SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                    FROM dmi_users AS u
                    INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                    INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                    WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                    GROUP BY ral_lab
                    ) AS lab_name

                    FROM sample_inward AS si
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code
                    INNER JOIN m_commodity_category mcc ON si.category_code = mcc.category_code 
                    INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                    INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                    INNER JOIN dmi_ro_offices AS ml ON ml.id=sa.lab_code
                    INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                    WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND sa.alloc_to_user_code = '$user_code'
                    GROUP BY sa.alloc_to_user_code,mc.commodity_name,mcc.category_name,u.f_name,u.l_name,u.role,si.remark ,mst.sample_type_desc
                    ORDER BY mc.commodity_name ASC ");

            $records = $query->fetchAll('assoc');

            $query->closeCursor();
            if (!empty($records)) {
                foreach ($records as $record) {
                    $category_name = $record['category_name'];
                    $commodity_name = $record['commodity_name'];
                    $bf_count = $record['bf_count'];
                    $user_code = $record['allotment_count'];
                    $no_of_para_analys = $record['no_of_para_analys'];
                    $no_of_param = $record['no_of_param'];
                    $remark = $record['remark'];
                    $check_bf_count   = $record['check_bf_count'];
                    $check_apex_bf_count = $record['check_apex_bf_count'];
                    $challenged_bf_count = $record['challenged_bf_count'];
                    $check_received_count  = $record['check_received_count'];
                    $check_apex_received_count = $record['check_apex_received_count'];
                    $challenged_received_count = $record['challenged_received_count'];
                    $check_analyzed_count_in_month = $record['check_analyzed_count_in_month'];
                    $check_apex_analyzed_count_in_month = $record['check_apex_analyzed_count_in_month'];
                    $challenged_analyzed_count_in_month = $record['challenged_analyzed_count_in_month'];
                    $total_check = $check_bf_count + $check_received_count;
                    $total_check_apex = $check_apex_bf_count + $check_apex_received_count;
                    $total_challenged  = $challenged_bf_count + $challenged_received_count;
                    $carry_check = $total_check - $check_analyzed_count_in_month;
                    $carry_check_apex = $total_check_apex - $check_apex_analyzed_count_in_month;
                    $carry_challenged = $total_challenged - $challenged_analyzed_count_in_month;
                    $name_chemist = $record['name_chemist'];
                    $sample_type_desc = $record['sample_type_desc'];
                    $lab_name = $record['lab_name'];
																					  
																							  
                    $date = date("d/m/Y");
                    $i = $i + 1;


                    $insert = $con->execute("INSERT INTO temp_reportico_admin_info_mpr_division (sr_no, user_id, lab_name,category_name, commodity_name, sample_type_desc,bf_count,allotment_count,check_bf_count, check_apex_bf_count, challenged_bf_count, check_received_count, check_apex_received_count, challenged_received_count, check_analyzed_count_in_month, check_apex_analyzed_count_in_month, challenged_analyzed_count_in_month, total_check, total_check_apex, total_challenged, carry_check, carry_check_apex, carry_challenged, name_chemist, remark, no_of_param, no_of_para_analys, report_date) 
                    VALUES (
                    '$i','$user_id','$lab_name','$category_name','$commodity_name','$sample_type_desc','$bf_count','$user_code','$check_bf_count', '$check_apex_bf_count', '$challenged_bf_count', '$check_received_count', '$check_apex_received_count', '$challenged_received_count', '$check_analyzed_count_in_month','$check_apex_analyzed_count_in_month', '$challenged_analyzed_count_in_month', '$total_check','$total_check_apex','$total_challenged', '$carry_check','$carry_check_apex','$carry_challenged','$name_chemist','$remark', '$no_of_param', '$no_of_para_analys', '$date')");

                    $update = $con->execute("UPDATE temp_reportico_admin_info_mpr_division SET counts = (SELECT COUNT(user_id) FROM temp_reportico_admin_info_mpr_division WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
                }

                $record_insert = 1;
            }
        }

        if ($record_insert == 1) {
            return 1;
        } else {
            return 0;
        }
    }
    public static function getAdminDetailsSampleAnalyzedByRal($month, $year, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_admin_details_sample_analyzed_ral WHERE user_id = '$user_id'");

        $sql = "SELECT sa.commodity_code
                FROM sample_inward AS si
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month'AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' ";

        $sql = $con->execute($sql);
        $recordNames = $sql->fetchAll('assoc');
        $sql->closeCursor();

        $record_insert = 0;
        foreach ($recordNames as $recordName) {
            $commodity_code = $recordName['commodity_code'];

            $query = $con->execute("SELECT mc.commodity_name,  si.org_sample_code, mst.sample_type_desc,
                    (
                    SELECT COUNT(cd.sample_code)
                    FROM code_decode cd
                    INNER JOIN m_sample_allocate AS msa ON msa.sample_code=cd.sample_code
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '1' AND msa.commodity_code = si.commodity_code
                    WHERE test_n_r!='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month'AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year' AND msa.commodity_code = '$commodity_code'
                    ) AS commodity_counts,
                    (
                    SELECT COUNT(cd.sample_code)
                    FROM code_decode cd
                    INNER JOIN m_sample_allocate AS msa ON msa.sample_code=cd.sample_code
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '7' AND msa.commodity_code = si.commodity_code
                    WHERE cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month'AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year'
                    ) AS inter_lab_compare,
                    (
                    SELECT COUNT(cd.sample_code)
                    FROM code_decode cd
                    INNER JOIN m_sample_allocate AS msa ON msa.sample_code=cd.sample_code
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '0' AND msa.commodity_code = si.commodity_code
                    WHERE cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month'AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year'
                    ) AS pvt_sample,
                    (
                    SELECT COUNT(cd.sample_code)
                    FROM code_decode cd
                    INNER JOIN m_sample_allocate AS msa ON msa.sample_code=cd.sample_code
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '0' AND msa.commodity_code = si.commodity_code
                    WHERE cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month'AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year'
                    ) AS inter_check,
                    (
                    SELECT COUNT(cd.sample_code)
                    FROM code_decode cd
                    INNER JOIN m_sample_allocate AS msa ON msa.sample_code=cd.sample_code
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '0' AND msa.commodity_code = si.commodity_code
                    WHERE cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month'AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year'
                    ) AS proj_sample,
                    (
                    SELECT COUNT(cd.sample_code)
                    FROM code_decode cd
                    INNER JOIN m_sample_allocate AS msa ON msa.sample_code=cd.sample_code
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '0' AND msa.commodity_code = si.commodity_code
                    WHERE cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month'AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year'
                    ) AS repeat_sample,
                    (
                    SELECT COUNT(cd.sample_code)
                    FROM code_decode cd
                    INNER JOIN m_sample_allocate AS msa ON msa.sample_code=cd.sample_code
                    INNER JOIN sample_inward si ON cd.org_sample_code = si.org_sample_code AND si.sample_type_code = '0' AND msa.commodity_code = si.commodity_code
                    WHERE cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month'AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year'
                    ) AS pt_samp,     
                    (
                    SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                    FROM dmi_users AS u
                    INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                    INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                    WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                    GROUP BY ral_lab
                    ) AS lab_name

                    FROM sample_inward AS si
                    INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                    INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                    INNER JOIN dmi_ro_offices AS ml ON ml.id=sa.lab_code
                    INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                    WHERE sa.lab_code='$ral_lab_no'  AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month'AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year'
                    GROUP BY mc.commodity_name, mst.sample_type_desc, si.org_sample_code, si.received_date
                    ORDER BY si.received_date ASC");

            $records = $query->fetchAll('assoc');
            $query->closeCursor();
            if (!empty($records)) {
                foreach ($records as $record) {
                    $commodity_name = $record['commodity_name'];
                    $org_sample_code = $record['org_sample_code'];
                    $commodity_counts = $record['commodity_counts'];
                    $inter_lab_compare = $record['inter_lab_compare'];
                     // $pvt_sample = $record['pvt_sample'];
                    // $inter_check = $record['inter_check'];
                    // $proj_sample = $record['proj_sample'];
                    // $repeat_sample = $record['repeat_sample'];
                    // $pt_samp = $record['pt_samp'];

                    $pvt_sample = 'NA';
                    $inter_check = 'NA';
                    $proj_sample = 'NA';
                    $repeat_sample = 'NA';
                    $pt_samp = 'NA';

                    $sample_type_desc = $record['sample_type_desc'];
                    $lab_name = $record['lab_name'];
                    $date = date("d/m/Y");
                    $i = $i + 1;

                    $insert = $con->execute("INSERT INTO temp_reportico_admin_details_sample_analyzed_ral (sr_no, user_id, lab_name,org_sample_code, sample_type_desc, commodity_name, commodity_counts, inter_lab_compare, pvt_sample, inter_check, proj_sample, repeat_sample, pt_samp, report_date) 
                    VALUES (
                    '$i','$user_id','$lab_name','$org_sample_code', '$sample_type_desc', '$commodity_name', '$commodity_counts', '$inter_lab_compare', '$pvt_sample', '$inter_check', '$proj_sample','$repeat_sample','$pt_samp', '$date')");

                    $update = $con->execute("UPDATE temp_reportico_admin_details_sample_analyzed_ral SET counts = (SELECT COUNT(user_id) FROM temp_reportico_admin_details_sample_analyzed_ral WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
                }

                $record_insert = 1;
            }
        }

        if ($record_insert == 1) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function getAdminBifercationRal($month, $year, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_admin_bifercation_ral WHERE user_id = '$user_id'");

        $sql = "SELECT sa.alloc_to_user_code
                FROM sample_inward AS si
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND u.role IN ('Jr Chemist','Sr Chemist','Cheif Chemist')";

        $sql = $con->execute($sql);
        $recordNames = $sql->fetchAll('assoc');
        $sql->closeCursor();

        $record_insert = 0;
        foreach ($recordNames as $recordName) {
            $user_code = $recordName['alloc_to_user_code'];

            $query = $con->execute("SELECT sa.alloc_to_user_code,sa.recby_ch_date, sa.commencement_date,'NA' AS other,'Yes' AS norms, CONCAT(u.f_name,' ',u.l_name,' (',u.role,') ') AS name_chemist,
                    (
                    SELECT COUNT(si.sample_type_code)
                    FROM sample_inward AS si
                    INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 1
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                    WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND sa.alloc_to_user_code = '$user_code'
                    ) AS check_count,
                    (
                    SELECT COUNT(si.sample_type_code)
                    FROM sample_inward AS si
                    INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 0
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                    WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month'  AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND sa.alloc_to_user_code = '$user_code'
                    ) AS pvt_count,
                    (
                    SELECT COUNT(si.sample_type_code)
                    FROM sample_inward AS si
                    INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 2
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                    WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month'  AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND sa.alloc_to_user_code = '$user_code'
                    ) AS research_count,
                    (
                    SELECT COUNT(si.sample_type_code)
                    FROM sample_inward AS si
                    INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 7
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                    WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month'  AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND sa.alloc_to_user_code = '$user_code'
                    ) AS ilc_count,
                    (
                    SELECT COUNT(si.sample_type_code)
                    FROM sample_inward AS si
                    INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 0
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                    WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND sa.alloc_to_user_code = '$user_code'
                    ) AS internal_check_count,
                    (
                    SELECT count(w.org_sample_code) 
                    FROM workflow w 
                    WHERE w.dst_loc_id = $ral_lab_no AND EXTRACT(MONTH
                    FROM w.tran_date):: INTEGER = '$month'  AND EXTRACT(YEAR
                    FROM w.tran_date):: INTEGER = '$year' AND w.user_code = '$user_code'
                    ) AS sample_frm_cal,
                    (
                    SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                    FROM dmi_users AS u
                    INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                    INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                    WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                    GROUP BY ral_lab
                    ) AS lab_name

                    FROM sample_inward AS si
                    INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                    INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                    INNER JOIN dmi_ro_offices AS ml ON ml.id=sa.lab_code
                    INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                    WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND sa.alloc_to_user_code = '$user_code'
                    GROUP BY u.f_name,u.l_name, u.role, sa.alloc_to_user_code,sa.recby_ch_date, sa.commencement_date");

            $records = $query->fetchAll('assoc');
            $query->closeCursor();
            if (!empty($records)) {
                foreach ($records as $record) {
                    $other = $record['other'];
                    $norms = $record['norms'];
                    $name_chemist = $record['name_chemist'];
                    $check_count = $record['check_count'];
                    $pvt_count = $record['pvt_count'];
                    $research_count = $record['research_count'];
                    $ilc_count = $record['ilc_count'];
                    $internal_check_count = $record['internal_check_count'];
                    $sample_frm_cal = $record['sample_frm_cal'];
                    $total = $check_count + $pvt_count + $research_count + $ilc_count + $internal_check_count + $sample_frm_cal;
                    $lab_name = $record['lab_name'];
                    $date = date("d/m/Y");
                    $i = $i + 1;
                    $recby_ch_date = $record['recby_ch_date'];
                    $commencement_date = $record['commencement_date'];
                    if ($recby_ch_date == '' || $commencement_date == '') {
                        $working_days = '0';
                    } else {
                        $diff = abs(strtotime($commencement_date) - strtotime($recby_ch_date));
                        $years = floor($diff / (365 * 60 * 60 * 24));
                        $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
                        $working_days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
                    }

                    $insert = $con->execute("INSERT INTO temp_reportico_admin_bifercation_ral (sr_no, user_id, lab_name,other, norms, name_chemist, check_count, pvt_count, research_count, ilc_count, internal_check_count, sample_frm_cal, total, working_days, report_date) 
                    VALUES (
                    '$i','$user_id','$lab_name','$other', '$norms', '$name_chemist', '$check_count', '$pvt_count', '$research_count', '$ilc_count', '$internal_check_count','$sample_frm_cal','$total','$working_days','$date')");

                    $update = $con->execute("UPDATE temp_reportico_admin_bifercation_ral SET counts = (SELECT COUNT(user_id) FROM temp_reportico_admin_bifercation_ral WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
                }

                $record_insert = 1;
            }
        }

        if ($record_insert == 1) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function getAdminMonthChekPendRal($month, $year, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_admin_month_chk_pend_ral WHERE user_id = '$user_id'");

        $sql = "SELECT sa.alloc_to_user_code, sa.commodity_code
                FROM sample_inward AS si
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND u.role IN ('Jr Chemist','Sr Chemist','Cheif Chemist')";

        $sql = $con->execute($sql);
        $recordNames = $sql->fetchAll('assoc');
        $sql->closeCursor();

        $record_insert = 0;
        foreach ($recordNames as $recordName) {
            $user_code = $recordName['alloc_to_user_code'];
            $commodity_code = $recordName['commodity_code'];

            $query = $con->execute("SELECT sa.alloc_to_user_code, sa.commodity_code,si.remark, CONCAT(u.f_name,' ',u.l_name,' (',u.role,') ') AS name_chemist, mc.commodity_name,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS bf_count
                    FROM code_decode AS cd
                    INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                    INNER JOIN m_sample_allocate sa ON si.org_sample_code = sa.org_sample_code
                    WHERE cd.lab_code='$ral_lab_no' AND cd.status_flag NOT IN('G','F') AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND si.sample_type_code='1' AND cd.alloc_to_user_code='$user_code'
                    ) AS bf_count,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS allotment_count
                    FROM code_decode AS cd
                    INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                    INNER JOIN m_sample_allocate sa ON si.org_sample_code = sa.org_sample_code
                    WHERE cd.status_flag in ('N','C','G') AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND cd.lab_code='$ral_lab_no' AND si.sample_type_code='1' AND cd.alloc_to_user_code='$user_code'
                    ) AS allotment_count,
                    (
                    SELECT COUNT(sa.org_sample_code)
                    FROM m_sample_allocate sa
                    INNER JOIN sample_inward si ON si.org_sample_code = sa.org_sample_code
                    WHERE EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND sa.lab_code='$ral_lab_no' AND si.sample_type_code='1' AND sa.alloc_to_user_code='$user_code' AND sa.commodity_code = '$commodity_code'
                    ) AS check_analyze_commodity,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS pending_count
                    FROM code_decode AS cd
                    INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                    WHERE cd.status_flag='N' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND cd.alloc_to_user_code='$user_code' AND cd.lab_code='$ral_lab_no'
                    ) AS pending_count,
                    (
                    SELECT COUNT(atd.alloc_to_user_code) FROM actual_test_data atd
                    INNER JOIN m_sample_allocate msa ON msa.alloc_to_user_code = atd.alloc_to_user_code AND msa.chemist_code = atd.chemist_code
                    INNER JOIN sample_inward si ON msa.org_sample_code = si.org_sample_code AND si.sample_type_code IN (1,4)
                    INNER JOIN commodity_test ct ON atd.test_code = ct.test_code
                    WHERE atd.lab_code = '$ral_lab_no' AND ct.commodity_code = '$commodity_code' AND msa.alloc_to_user_code = '$user_code' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year' 
                    ) AS no_of_para_analys,
                    (
                    SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                    FROM dmi_users AS u
                    INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                    INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                    WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                    GROUP BY ral_lab
                    ) AS lab_name
                    
                    FROM sample_inward AS si
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                    inner join m_commodity AS mc ON sa.commodity_code = mc.commodity_code
                    WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND sa.alloc_to_user_code = '$user_code'
                    GROUP BY sa.alloc_to_user_code, sa.commodity_code,si.remark,u.f_name,u.l_name,u.role, mc.commodity_name, sa.alloc_date");

            $records = $query->fetchAll('assoc');
            $query->closeCursor();
            if (!empty($records)) {
                foreach ($records as $record) {
                    $remark = $record['remark'];
                    $name_chemist = $record['name_chemist'];
                    $no_of_parameter = $record['no_of_para_analys'];
                    $bf_count = $record['bf_count'];
                    $allotment_count = $record['allotment_count'];
                    $check_analyze_commodity = $record['check_analyze_commodity'];
                    $pending_count = $record['pending_count'];
                    $reason = "NA";;
                    $total = $bf_count + $allotment_count + $check_analyze_commodity;
                    $lab_name = $record['lab_name'];
                    $commodity_name = $record['commodity_name'];
                    $date = date("d/m/Y");
                    $i = $i + 1;


                    $insert = $con->execute("INSERT INTO temp_reportico_admin_month_chk_pend_ral (sr_no, user_id, lab_name,remark, name_chemist, no_of_parameter, bf_count, allotment_count, check_analyze_commodity, pending_count, reason, commodity_name, total, report_date) 
                    VALUES (
                    '$i','$user_id','$lab_name','$remark', '$name_chemist', '$no_of_parameter', '$bf_count', '$allotment_count', '$check_analyze_commodity', '$pending_count','$reason','$commodity_name','$total','$date')");

                    $update = $con->execute("UPDATE temp_reportico_admin_month_chk_pend_ral SET counts = (SELECT COUNT(user_id) FROM temp_reportico_admin_month_chk_pend_ral WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
                }

                $record_insert = 1;
            }
        }

        if ($record_insert == 1) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function getAdminCommodityWiseSampleRalAnnxeure($month, $years, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        if ($month == 02) {
            $month_one = 12;
            $month_two = $month - 1;
        } else if ($month == 01) {
            $month_one = 11;
            $month_two = 12;
        } else {
            $month_one = $month - 2;
            $month_two = $month - 1;
        }
        if (strlen($month_one) == 1) {
            $month_one = '0' . $month_one;
        }
        if (strlen($month_two) == 1) {
            $month_two = '0' . $month_two;
        }
        if ($month_one == 11 || $month_one == 12 || $month_two == 12) {
            $year_new = $years - 1;
        }
        if ($month_one != 11 || $month_one != 12 || $month_two != 12) {
            $year = $years;
        }

        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_admin_commo_wise_sample_ral_annexure WHERE user_id = '$user_id'");

        $main_sql = "SELECT sa.commodity_code
                FROM sample_inward AS si
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month'  AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' GROUP BY sa.commodity_code";

        $main_sql = $con->execute($main_sql);
        $recordNames = $main_sql->fetchAll('assoc');
        $main_sql->closeCursor();

        $record_insert = 0;
        foreach ($recordNames as $recordName) {
            $commodity_code = $recordName['commodity_code'];

            $sql = "SELECT sa.commodity_code, mc.commodity_name, mgd.grade_desc, ";

            if ($month_one == 11 ||  $month_two == 12) {
                $sql .= " CASE when sa.lab_code='$ral_lab_no' AND (EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_one' OR EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_two') AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year_new' AND w.stage_smpl_flag= 'FG' AND sa.commodity_code = '$commodity_code' AND mgd.grade_code NOT IN(348,370) then mgd.grade_desc
                    end  as conformed_std,
            
                    CASE when sa.lab_code='$ral_lab_no' AND (EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_one' OR EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_two') AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year_new' AND w.stage_smpl_flag= 'FG' AND sa.commodity_code = '$commodity_code' AND mgd.grade_code IN(348,370) then mgd.grade_desc
                    end as misgrd, 

                    (SELECT COUNT(DISTINCT(cd.sample_code)) AS bf_count
                    FROM code_decode AS cd
                    INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                    INNER JOIN m_sample_allocate sa ON si.org_sample_code = sa.org_sample_code
                    WHERE cd.lab_code='$ral_lab_no' AND cd.status_flag NOT IN('G','F') AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_one' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year_new' AND sa.commodity_code='$commodity_code') AS bf_count, 
                    
                    (SELECT COUNT(DISTINCT(sa.sample_code)) AS received_count
                    FROM m_sample_allocate sa
                    WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_two' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year_new' AND sa.commodity_code='$commodity_code') AS received_count,
                    
                    (SELECT COUNT(DISTINCT(cd.sample_code)) AS analyzed_count_one
                    FROM code_decode AS cd
                    INNER JOIN m_sample_allocate AS sa ON sa.sample_code=cd.sample_code
                    WHERE test_n_r!='R' AND cd.lab_code='$ral_lab_no' AND (EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_one' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_two') AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year_new' AND sa.commodity_code='$commodity_code') AS sample_analyze, ";
            } else if ($month_one == 12) {
                $sql .= " CASE when sa.lab_code='$ral_lab_no' AND (EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_one' OR EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_two') AND (EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year_new' OR EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year') AND w.stage_smpl_flag= 'FG' AND sa.commodity_code = '$commodity_code' AND mgd.grade_code NOT IN(348,370) then mgd.grade_desc
                    end  as conformed_std,
            
                    CASE when sa.lab_code='$ral_lab_no' AND (EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_one' OR EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_two') AND (EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year_new' OR EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year') AND w.stage_smpl_flag= 'FG' AND sa.commodity_code = '$commodity_code' AND mgd.grade_code IN(348,370) then mgd.grade_desc
                    end as misgrd,

                    (SELECT COUNT(DISTINCT(cd.sample_code)) AS bf_count
                    FROM code_decode AS cd
                    INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                    INNER JOIN m_sample_allocate sa ON si.org_sample_code = sa.org_sample_code
                    WHERE cd.lab_code='$ral_lab_no' AND cd.status_flag NOT IN('G','F') AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_one' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year_new' AND sa.commodity_code='$commodity_code') AS bf_count, 
                    
                    (SELECT COUNT(DISTINCT(sa.sample_code)) AS received_count
                    FROM m_sample_allocate sa
                    WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_two' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND sa.commodity_code='$commodity_code') AS received_count,
                    
                    (SELECT COUNT(DISTINCT(cd.sample_code)) AS analyzed_count_one
                    FROM code_decode AS cd
                    INNER JOIN m_sample_allocate AS sa ON sa.sample_code=cd.sample_code
                    WHERE test_n_r!='R' AND cd.lab_code='$ral_lab_no' AND (EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_one' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month_two') AND (EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year_new' OR EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year') AND sa.commodity_code='$commodity_code') AS sample_analyze, ";
            } else {
                $sql .= " CASE when sa.lab_code='$ral_lab_no' AND (EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month_one' OR EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month_two') AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND w.stage_smpl_flag= 'FG' AND sa.commodity_code = '$commodity_code' AND mgd.grade_code NOT IN(348,370) then mgd.grade_desc
                end  as conformed_std,
        
                CASE when sa.lab_code='$ral_lab_no' AND (EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month_one' OR EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month_two') AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND w.stage_smpl_flag= 'FG' AND sa.commodity_code = '$commodity_code' AND mgd.grade_code IN(348,370) then mgd.grade_desc
                end as misgrd,

                (SELECT COUNT(DISTINCT(cd.sample_code)) AS bf_count
                FROM code_decode AS cd
                INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                INNER JOIN m_sample_allocate sa ON si.org_sample_code = sa.org_sample_code
                WHERE cd.lab_code='$ral_lab_no' AND cd.status_flag NOT IN('G','F') AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month_one' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND sa.commodity_code='$commodity_code') AS bf_count, 
                
                (SELECT COUNT(DISTINCT(sa.sample_code)) AS received_count
                FROM m_sample_allocate sa
                WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month_two' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND sa.commodity_code='$commodity_code') AS received_count,
                
                (SELECT COUNT(DISTINCT(cd.sample_code)) AS analyzed_count_one
                FROM code_decode AS cd
                INNER JOIN m_sample_allocate AS sa ON sa.sample_code=cd.sample_code
                WHERE test_n_r!='R' AND cd.lab_code='$ral_lab_no' AND (EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month_one' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month_two') AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND sa.commodity_code='$commodity_code') AS sample_analyze, ";
            }

            $sql .= " ( SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab) AS lab_name
                
                FROM sample_inward AS si
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                INNER JOIN workflow w ON si.org_sample_code = w.org_sample_code
                INNER JOIN m_grade_desc mgd ON mgd.grade_code = si.grade
                INNER JOIN m_commodity AS mc ON sa.commodity_code = mc.commodity_code
                WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND sa.commodity_code = '$commodity_code'
                GROUP BY sa.commodity_code, mc.commodity_name, mgd.grade_desc,sa.lab_code, sa.alloc_date,w.stage_smpl_flag,mgd.grade_code";

            $query = $con->execute($sql);
            $records = $query->fetchAll('assoc');
            $query->closeCursor();
            if (!empty($records)) {
                foreach ($records as $record) {
                    $bf_count = $record['bf_count'];
                    $received_count = $record['received_count'];
                    $sample_analyze = $record['sample_analyze'];
                    $total = $bf_count + $received_count;
                    $conformed_std = $record['conformed_std'];
                    $misgrade = $record['misgrd'];
                    $cf_month = $total - $sample_analyze;
                    $lab_name = $record['lab_name'];
                    $commodity_name = $record['commodity_name'];
                    $date = date("d/m/Y");
                    $i = $i + 1;
                    if($conformed_std == ''){
                        $conformed_std = 'NULL';
                    }
                    if($misgrade == '')
                    {
                        $misgrade = "NULL";
                    }

                    $insert = $con->execute("INSERT INTO temp_reportico_admin_commo_wise_sample_ral_annexure (sr_no, user_id, lab_name, commodity_name, bf_count, received_count, sample_analyze, total, conformed_std, misgrade, cf_month, report_date) 
                    VALUES (
                    '$i','$user_id','$lab_name', '$commodity_name','$bf_count', '$received_count', '$sample_analyze', '$total','$conformed_std','$misgrade','$cf_month','$date')");


                    $update = $con->execute("UPDATE temp_reportico_admin_commo_wise_sample_ral_annexure SET counts = (SELECT COUNT(user_id) FROM temp_reportico_admin_commo_wise_sample_ral_annexure WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
                }

                $record_insert = 1;
            }
        }

        if ($record_insert == 1) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function getAdminStatementChkBfCfSample($month, $year, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_admin_smt_chk_bf_cf_sample WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT u.role, si.remark,
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS bf_count
                FROM code_decode AS cd
                INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                INNER JOIN m_sample_allocate sa ON si.org_sample_code = sa.org_sample_code
                WHERE cd.lab_code='$ral_lab_no' AND cd.status_flag NOT IN('G','F') AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND si.sample_type_code='1'
                ) AS bf_count,
                (
                SELECT COUNT(DISTINCT(sa.sample_code)) AS received_count
                FROM m_sample_allocate sa
                INNER JOIN sample_inward si ON sa.org_sample_code = si.org_sample_code AND si.sample_type_code = '1'
                WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'
                ) AS received_count,
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS analyzed_count_one
                FROM code_decode AS cd
                INNER JOIN m_sample_allocate AS sa ON sa.sample_code=cd.sample_code
                WHERE test_n_r !='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'
                ) AS analyzed_count_original,
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS analyzed_count_two
                FROM code_decode AS cd
                INNER JOIN m_sample_allocate AS sa ON sa.sample_code=cd.sample_code
                WHERE test_n_r='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'
                ) AS analyzed_count_duplicate,
                (
                SELECT COUNT(DISTINCT(cd.sample_code)) AS analyzed_count_two
                FROM code_decode AS cd
                INNER JOIN m_sample_allocate AS sa ON sa.sample_code=cd.sample_code
                WHERE test_n_r='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year'
                ) AS analyzed_count_repeat,
                (
                SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                FROM dmi_users AS u
                INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                GROUP BY ral_lab
                ) AS lab_name
                
                FROM sample_inward AS si
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                INNER JOIN m_commodity AS mc ON sa.commodity_code = mc.commodity_code
                WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' AND u.role IN ('Jr Chemist','Sr Chemist','Cheif Chemist')
                GROUP BY si.remark,u.role");
       
        $records = $query->fetchAll('assoc');
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $bf_count = $record['bf_count'];
                $received_count = $record['received_count'];
                $role = $record['role'];
                $total = $bf_count + $received_count;
                $analyzed_count_original = $record['analyzed_count_original'];
                $analyzed_count_duplicate = $record['analyzed_count_duplicate'];
                $analyzed_count_repeat = $record['analyzed_count_repeat'];
                $carray_forward = $total - $analyzed_count_original;
                $sancationed_strength = 'NA';
                $staff_strength = 'NA';
                $lab_name = $record['lab_name'];
                $remark = $record['remark'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_admin_smt_chk_bf_cf_sample (sr_no, user_id, lab_name, role, bf_count, received_count, total, analyzed_count_original, analyzed_count_duplicate, analyzed_count_repeat, carry_forward, sancationed_strength, staff_strength, remark, report_date) 
                VALUES (
                '$i','$user_id','$lab_name', '$role','$bf_count', '$received_count', '$total','$analyzed_count_original','$analyzed_count_duplicate','$analyzed_count_repeat','$carray_forward','$sancationed_strength','$staff_strength','$remark','$date')");

                $update = $con->execute("UPDATE temp_reportico_admin_smt_chk_bf_cf_sample SET counts = (SELECT COUNT(user_id) FROM temp_reportico_admin_smt_chk_bf_cf_sample WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getAdminTimeTakenReport($month, $year, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_admin_time_taken_report WHERE user_id = '$user_id'");

        $sql = "SELECT sa.commodity_code
                FROM sample_inward AS si
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month'  AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' GROUP BY sa.commodity_code";

        $sql = $con->execute($sql);
        $recordNames = $sql->fetchAll('assoc');
        $sql->closeCursor();

        $record_insert = 0;
        foreach ($recordNames as $recordName) {
            $commodity_code = $recordName['commodity_code'];
            $query = $con->execute("SELECT mc.commodity_name,si.received_date,si.dispatch_date,si.remark,si.dispatch_date:: DATE -si.received_date:: DATE AS time_taken,'NA' AS reason,
                    (
                    SELECT COUNT(si.stage_sample_code)
                    FROM sample_inward AS si
                    WHERE EXTRACT(MONTH
                    FROM si.received_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM si.received_date):: INTEGER = '$year' AND si.commodity_code = $commodity_code
                    ) AS sample_count,
                    (
                    SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                    FROM dmi_users AS u
                    INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                    INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                    WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                    GROUP BY ral_lab
                    ) AS lab_name
                    FROM sample_inward AS si
                    INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                    INNER JOIN dmi_users AS du ON du.id=si.user_code
                    INNER JOIN user_role AS ur ON ur.role_name=du.role AND EXTRACT(MONTH
                    FROM si.received_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM si.received_date):: INTEGER = '$year' AND si.commodity_code = $commodity_code
                    GROUP BY si.stage_sample_code,mc.commodity_name,si.received_date,si.dispatch_date,si.commodity_code,si.remark");
           
            $records = $query->fetchAll('assoc');
            $query->closeCursor();
            if (!empty($records)) {
                foreach ($records as $record) {
                    $commodity_name = $record['commodity_name'];
                    $received_date = $record['received_date'];
                    $dispatch_date = $record['dispatch_date'];
                    $remark = $record['remark'];
                    $time_taken = $record['time_taken'];
                    $lab_name = $record['lab_name'];
                    $reason = $record['reason'];
                    $sample_count = $record['sample_count'];
                    $date = date("d/m/Y");
                    $i = $i + 1;

                    $insert = $con->execute("INSERT INTO temp_reportico_admin_time_taken_report (sr_no, user_id, lab_name, commodity_name, received_date, dispatch_date, remark, time_taken, reason, sample_count, report_date) 
                    VALUES (
                    '$i','$user_id','$lab_name', '$commodity_name','$received_date', '$dispatch_date', '$remark', '$time_taken','$reason','$sample_count','$date')");

                    $update = $con->execute("UPDATE temp_reportico_admin_time_taken_report SET counts = (SELECT COUNT(user_id) FROM temp_reportico_admin_time_taken_report WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
                }

                $record_insert = 1;
            }
        }

        if ($record_insert == 1) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function getAdminSmplAllotCodingSection($month, $year, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_admin_smpl_coding_section WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT mc.commodity_name,si.stage_sample_code,CONCAT(si.sample_total_qnt,' ',muw.unit_weight) AS quantity,mst.sample_type_desc, 'ALL'AS parameter, 'NA' AS code_number
                FROM sample_inward AS si
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN m_sample_allocate msa ON msa.org_sample_code = si.org_sample_code
                INNER JOIN m_unit_weight AS muw ON muw.unit_id=si.parcel_size
                INNER JOIN dmi_users AS u ON u.id=si.user_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN workflow AS w ON w.org_sample_code=si.org_sample_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                INNER JOIN m_sample_type AS mst ON mst.sample_type_code=si.sample_type_code AND w.stage_smpl_flag In ('OF','HF','FG') AND EXTRACT(MONTH
                FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM msa.alloc_date):: INTEGER = '$year' AND w.dst_loc_id='$ral_lab_no'
                GROUP BY mc.commodity_name,si.stage_sample_code,si.sample_total_qnt,muw.unit_weight,mst.sample_type_desc,si.remark
                ORDER BY mc.commodity_name ASC");
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $commodity_name = $record['commodity_name'];
                $stage_sample_code = $record['stage_sample_code'];
                $quantity = $record['quantity'];
                $sample_type_desc = $record['sample_type_desc'];
                $parameter = $record['parameter'];
                $code_number = $record['code_number'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_admin_smpl_coding_section(sr_no, user_id, commodity_name, stage_sample_code, quantity, sample_type_desc, parameter, code_number, report_date) 
                VALUES (
                '$i','$user_id','$commodity_name', '$stage_sample_code','$quantity', '$sample_type_desc','$parameter','$code_number','$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getAdminSmplAnalyticalSectionChemistAnalysis($month, $year, $ral_lab_no)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_admin_smpl_analytical_section_chemist_analysis WHERE user_id = '$user_id'");

        $query = $con->execute("SELECT mc.commodity_name,'ALL'AS parameter, 'NA' AS code_number, si.remark
                FROM sample_inward AS si
                INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                INNER JOIN m_sample_allocate msa ON msa.org_sample_code = si.org_sample_code
                INNER JOIN dmi_users AS u ON u.id=si.user_code
                INNER JOIN dmi_ro_offices AS mll ON mll.id=si.loc_id
                INNER JOIN workflow AS w ON w.org_sample_code=si.org_sample_code
                INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                WHERE EXTRACT(MONTH
                FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                FROM msa.alloc_date):: INTEGER = '$year' AND  w.dst_loc_id='$ral_lab_no' AND u.role IN ('Jr Chemist','Sr Chemist')
                GROUP BY mc.commodity_name,si.remark, u.f_name, u.l_name, u.role
                ORDER BY mc.commodity_name ASC");
        $records = $query->fetchAll('assoc');
        // print_r($records);exit;
        $query->closeCursor();
        if (!empty($records)) {
            foreach ($records as $record) {
                $commodity_name = $record['commodity_name'];
                $parameter = $record['parameter'];
                $code_number = $record['code_number'];
                $remark = $record['remark'];
                $date = date("d/m/Y");
                $i = $i + 1;

                $insert = $con->execute("INSERT INTO temp_reportico_admin_smpl_analytical_section_chemist_analysis(sr_no, user_id, commodity_name, parameter, code_number, remark, report_date) 
                VALUES (
                '$i','$user_id','$commodity_name', '$parameter','$code_number','$remark','$date')");
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function getAdminParticularSampleAnanlyzeReceiveRal($month, $year, $ral_lab_no, $ral_lab_name)
    {
        $i = 0;
        $user_id = $_SESSION['user_code'];
        $con = ConnectionManager::get('default');

        $delete = $con->execute("DELETE FROM temp_reportico_admin_particular_analyze_receive WHERE user_id = '$user_id'");

        $sql = "SELECT sa.commodity_code,si.sample_type_code
                FROM sample_inward AS si
                INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                FROM sa.alloc_date):: INTEGER = '$month'  AND EXTRACT(YEAR
                FROM sa.alloc_date):: INTEGER = '$year' GROUP BY sa.commodity_code,si.sample_type_code";

        $sql = $con->execute($sql);
        $recordNames = $sql->fetchAll('assoc');
        $sql->closeCursor();

        $record_insert = 0;
        foreach ($recordNames as $recordName) {
            $commodity_code = $recordName['commodity_code'];
            $sample_type_code = $recordName['sample_type_code'];
            $query = $con->execute("SELECT mc.commodity_name,si.remark, mst.sample_type_desc,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS bf_count
                    FROM code_decode AS cd
                    INNER JOIN sample_inward AS si ON si.org_sample_code=cd.org_sample_code
                    INNER JOIN m_sample_allocate sa ON si.org_sample_code = sa.org_sample_code
                    WHERE cd.lab_code='$ral_lab_no' AND cd.status_flag NOT IN('G','F') AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND sa.commodity_code='$commodity_code' AND si.sample_type_code = '$sample_type_code'
                    ) AS bf_count,    
                    (
                    SELECT COUNT(DISTINCT(sa.sample_code)) AS received_count
                    FROM m_sample_allocate sa
                    WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND sa.commodity_code='$commodity_code' AND si.sample_type_code = '$sample_type_code'
                    ) AS received_count,   
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS analyzed_count_one
                    FROM code_decode AS cd
                    INNER JOIN m_sample_allocate AS sa ON sa.sample_code=cd.sample_code
                    WHERE test_n_r !='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND sa.commodity_code='$commodity_code' AND si.sample_type_code = '$sample_type_code'
                    ) AS analyzed_count_original, 
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS analyzed_count_two
                    FROM code_decode AS cd
                    INNER JOIN m_sample_allocate AS sa ON sa.sample_code=cd.sample_code
                    WHERE test_n_r='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND sa.commodity_code='$commodity_code' AND si.sample_type_code = '$sample_type_code'
                    ) AS analyzed_count_duplicate,
                    (
                    SELECT COUNT(DISTINCT(sample_code)) AS received_count
                    FROM m_sample_allocate sa
                    WHERE lab_code='$ral_lab_no' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year'AND sa.commodity_code='$commodity_code' AND si.sample_type_code = '$sample_type_code'
                    ) AS received_count_year,
                    (
                    SELECT COUNT(DISTINCT(cd.sample_code)) AS analyzed_count_one
                    FROM code_decode AS cd
                    INNER JOIN m_sample_allocate AS sa ON sa.sample_code=cd.sample_code
                    WHERE test_n_r !='R' AND cd.lab_code='$ral_lab_no' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND sa.commodity_code='$commodity_code' AND si.sample_type_code = '$sample_type_code'
                    ) AS analyzed_count_year,
                    (
                    SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                    FROM dmi_users AS u
                    INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                    INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                    WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                    GROUP BY ral_lab
                    ) AS lab_name
                    
                    FROM sample_inward AS si
                    INNER JOIN m_sample_type mst ON si.sample_type_code = mst.sample_type_code
                    INNER JOIN m_commodity mc ON si.commodity_code = mc.commodity_code
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                    WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND sa.commodity_code='$commodity_code'
                    GROUP BY sa.commodity_code,si.remark, si.sample_type_code, mc.commodity_name, mst.sample_type_desc");
           
            $records = $query->fetchAll('assoc');
            $query->closeCursor();
            if (!empty($records)) {
                foreach ($records as $record) {
                    $commodity_name = $record['commodity_name'];
                    $bf_count = $record['bf_count'];
                    $received_count = $record['received_count'];
                    $total = $bf_count + $received_count;
                    $remark = $record['remark'];
                    $analyzed_count_original = $record['analyzed_count_original'];
                    $analyzed_count_duplicate = $record['analyzed_count_duplicate'];
                    $analyzed_count_original = $record['analyzed_count_original'];
                    $received_count_year = $record['received_count_year'];
                    $analyzed_count_year = $record['analyzed_count_year'];
                    $carry_forward = $total - $analyzed_count_original;
                    $lab_name = $record['lab_name'];
                    $sample_type_desc = $record['sample_type_desc'];
                    $date = date("d/m/Y");
                    $i = $i + 1;

                    $insert = $con->execute("INSERT INTO temp_reportico_admin_particular_analyze_receive (sr_no, user_id, lab_name, commodity_name, sample_type_desc, bf_count, received_count, remark, total, analyzed_count_original, analyzed_count_duplicate, received_count_year, analyzed_count_year, carry_forward, report_date) 
                    VALUES (
                    '$i','$user_id','$lab_name', '$commodity_name','$sample_type_desc','$bf_count', '$received_count', '$remark', '$total','$analyzed_count_original','$analyzed_count_duplicate','$received_count_year','$analyzed_count_year','$carry_forward','$date')");

                    $update = $con->execute("UPDATE temp_reportico_admin_particular_analyze_receive SET counts = (SELECT COUNT(user_id) FROM temp_reportico_admin_particular_analyze_receive WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
                }

                $record_insert = 1;
            }
        }

        if ($record_insert == 1) {
            return 1;
        } else {
            return 0;
        }
    }


        //added for consolidate report by chemist on 23-08-2022 by shreeya
        public static function getHoConsolidatedReporteAnalyzedByChemist($month, $year, $ral_lab_no, $ral_lab_name)
        {
    
            $i = 0;
            $user_id = $_SESSION['user_code'];
            $con = ConnectionManager::get('default');
          
            $delete = $con->execute("DELETE FROM temp_consolidated_reporte_analyzed_by_chemists WHERE user_id = '$user_id'");
    
            $sql = "SELECT  sa.alloc_to_user_code, sa.commodity_code, mst.sample_type_code,u.email FROM sample_inward AS si
                    INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                    INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                    INNER JOIN dmi_ro_offices AS ml ON ml.id=sa.lab_code
                    INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                    WHERE si.entry_type = 'sub_sample' AND sa.lab_code ='$ral_lab_no' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND u.role IN ('Jr Chemist','Sr Chemist','Cheif Chemist')
                    GROUP BY sa.alloc_to_user_code, sa.commodity_code, mst.sample_type_code,u.email";
        
            $sql = $con->execute($sql);
            $recordNames = $sql->fetchAll('assoc');
            $sql->closeCursor();
    
            $record_insert = 0;
            foreach ($recordNames as $recordName) {
                $user_code = $recordName['alloc_to_user_code'];
                $commodity_code = $recordName['commodity_code'];
    
                $query = $con->execute("SELECT mc.commodity_name,  sa.alloc_date, sa.recby_ch_date, sa.commencement_date, CONCAT(u.f_name,' ',u.l_name,' (',u.role,') ') AS name_chemist,'NA' AS other, 'NA' AS other_work, 'Yes' AS norm, mst.sample_type_desc,
                    (
                    SELECT COUNT(si.sample_type_code) 
                    FROM sample_inward AS si
                    INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 1
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                    WHERE sa.lab_code='$ral_lab_no'  AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                    ) AS check_count,
                    (
                    SELECT COUNT(si.sample_type_code) 
                    FROM sample_inward AS si
                    INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 5
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                    WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                    ) AS check_apex_count,
                    (
                    SELECT COUNT(si.sample_type_code) 
                    FROM sample_inward AS si
                    INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 4
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                    INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                    WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                    ) AS challenged_count,
                    (
                    SELECT COUNT(si.sample_type_code) 
                    FROM sample_inward AS si
                    INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 9
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                    WHERE si.entry_type = 'sub_sample' AND sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                    ) AS ilc_count,
                    (
                    SELECT COUNT(si.sample_type_code) 
                    FROM sample_inward AS si
                    INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 2
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                    WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                    ) AS research_count,
                    (
                    SELECT COUNT(si.sample_type_code) 
                    FROM sample_inward AS si
                    INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code AND sa.test_n_r = 'R' 
                    INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                    WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year'  AND sa.alloc_to_user_code = '$user_code'
                    ) AS retesting_count,
                    (
                    SELECT COUNT(ct.test_code)
                    FROM commodity_test ct
                    INNER JOIN m_sample_allocate AS msa ON msa.commodity_code = ct.commodity_code AND msa.lab_code = '$ral_lab_no'
                    INNER JOIN sample_inward si ON msa.org_sample_code = si.org_sample_code AND msa.commodity_code = si.commodity_code
                    WHERE ct.commodity_code = $commodity_code AND msa.alloc_to_user_code = '$user_code' AND EXTRACT(MONTH
                    FROM msa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM msa.alloc_date):: INTEGER = '$year'
                    ) AS no_of_param,
                    (
                    
                    SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
                    FROM dmi_users AS u
                    INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
                    INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
                    WHERE u.status = 'active' AND o.id = '$ral_lab_no'
                    GROUP BY ral_lab
                    ) AS lab_name
    
                   
                    FROM sample_inward AS si
                    INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
                    INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
                    INNER JOIN dmi_ro_offices AS ml ON ml.id=sa.lab_code
                    INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
                    WHERE sa.lab_code='$ral_lab_no' AND Extract(month from sa.alloc_date)::INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND sa.alloc_to_user_code = '$user_code'
                    GROUP BY mc.commodity_name,  sa.alloc_date, sa.recby_ch_date, sa.commencement_date, name_chemist, mst.sample_type_desc, si.received_date
                    ORDER BY si.received_date ASC");
                $records = $query->fetchAll('assoc');
    
                $query->closeCursor();
                if (!empty($records)) {
                    foreach ($records as $record) {
                        
                        $name_chemist            = $record['name_chemist'];
                        $check_apex_count        = $record['check_apex_count'];
                        $challenged_count        = $record['challenged_count'];
                        $retesting_count         = $record['retesting_count'];
                        $ilc_count               = $record['ilc_count'];
                        $other_private_sample    = 'NA';
                        $research_count          = $record['research_count'];
                        $project_sample          = 'NA';
                        $smpl_analysed_instrn    = 'NA';
                        $check_count             = $record['check_count'];
                        $report_date             = date("d/m/Y");
                        $total_no                = 'NA';
                        $lab_name                = $record['lab_name'];
                        $sample_type_desc        = $record['sample_type_desc'];
                        $date                    = date("d/m/Y");
                        $i                       = $i + 1;
                        $total_no                = $check_count + $check_apex_count + $challenged_count + $ilc_count + $research_count + $retesting_count;
    
                        $insert = $con->execute("INSERT INTO temp_consolidated_reporte_analyzed_by_chemists (sr_no, user_id, lab_name, name_chemist, sample_type_desc, project_sample, check_count, check_apex_count, challenged_count, ilc_count, research_count, retesting_count,other_private_sample, smpl_analysed_instrn, total_no, report_date,created,modified) 
                        VALUES (
                        '$i','$user_id','$lab_name','$name_chemist', '$sample_type_desc', '$project_sample', '$check_count', '$check_apex_count', '$challenged_count', '$ilc_count','$research_count','$retesting_count','$other_private_sample','$smpl_analysed_instrn', '$total_no', '$report_date', '$date', '$date')");
    
                        $update = $con->execute("UPDATE temp_consolidated_reporte_analyzed_by_chemists SET counts = (SELECT COUNT(user_id) FROM temp_consolidated_reporte_analyzed_by_chemists WHERE user_id = '$user_id') WHERE user_id = '$user_id'");
                    }
                  
                    $record_insert = 1;
                }
            }
    
            if ($record_insert == 1) {
                return 1;
            } else {
                return 0;
            }
        }



}

?>
