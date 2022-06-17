<?php
/*

 * File:        DatabaseEngine.php
 *
 * Base class for handling generic updating functionality
 * to the database where details of an operation, a data view name
 * a key and values are passed within the URL
 *
 * @link http://www.reportico.org/
 * @copyright 2010-2014 Peter Deed
 * @author Peter Deed <info@reportico.org>
 * @package Reportico
 * @version $Id: DatabaseEngine.php,v 1.8 2014/05/17 15:12:31 peter Exp $
 */

class DatabaseEngine
{
	public $pdo;
	public $stmt;
	public $last_sql;
	public $errorno;
	public $errormsg;
	public $errortext;

    function __construct($in_pdo)
    {
	    $this->pdo = $in_pdo;
    }
    
    function executeSQL( $in_sql, $no_rows_is_error = false )
    {
            $this->last_sql = $in_sql;
            $this->stmt =  $this->pdo->query($in_sql);
            
            if ( !$this->stmt )
            {
		           $this->storeErrorMessage();
                   return ( $this->stmt);
            }

            if ( $no_rows_is_error )
            {
                if ( $this->getRowsAffected() == 0 )
                {
                    $this->errorno = "100";
                    $this->errormsg = "Warning - No data was affected by the operation";
                    $this->last_sql = "";
                    return false;
                }
                
            }
            return $this->stmt;
    }
    
    function fetch()
    {
            $result = $this->stmt->fetch();
            return $result;
    }
    
    function close()
    {
            $this->stmt = null;
    }
    
    function storeErrorMessage()
    {
            $arr = $this->pdo->errorInfo();
            $this->errorno = $arr[0];
            $this->errormsg = $arr[2];
    }

    function getErrorMessage($add_sql = true)
    {
        return "Error ". $this->errorno. " - <BR>".$this->errormsg."<BR><BR>".$this->last_sql;
    }

    function showPDOError( )
    {
            $info = $this->pdo->errorInfo();
            $msg =  "Error ".$info[1]."<BR>".
                    $info[2];
            trigger_error("$msg", E_USER_NOTICE);
    }
    
    function getRowsAffected( )
    {
            return $this->stmt->rowCount();
    }
    
    function rptSetDirtyRead()
    {
	    $sql = "SET ISOLATION TO DIRTY READ";
	    return $this->pdo->Execute($sql);
    }
    
    
    function performProjectModifications ($project)
    {
        $filename = ReporticoUtility::findBestLocationInIncludePath( "projects/".$project."/modification_rules.php");
        $return_status = array (
                    "errstat" => 0,
                    "msgtext" => "Modification sucessful"
                    );

        if ( is_file ( $filename ) )
        {
            require_once($filename);
            custom_project_modifications($this, $return_status);
        }
        else
        {
            $return_status["errstat"] = -1;
            $return_status["msgtext"] = "No modifcation rules were found";
        }

        return $return_status;
    }

}
?>
