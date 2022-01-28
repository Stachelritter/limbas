<?php
/*
 * Copyright notice
 * (c) 1998-2021 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 4.3.36.1319
 */

/*
 * ID: 154
 */

set_time_limit(3600); #60min
ob_implicit_flush();

?>
<div class="lmbPositionContainerMain small">
<?php

$sqlquery = "SELECT LMB_CONF_TABLES.TABELLE,LMB_CONF_TABLES.TAB_ID,LMB_CONF_TABLES.BESCHREIBUNG,LMB_RULES_TABLES.SPECIFICPRIVILEGE 
FROM LMB_CONF_TABLES,LMB_RULES_TABLES 
WHERE LMB_RULES_TABLES.TAB_ID = LMB_CONF_TABLES.TAB_ID AND GROUP_ID = ".$session["group_id"]." AND LMB_CONF_TABLES.USERRULES = ".LMB_DBDEF_TRUE;
$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
while(lmbdb_fetch_row($rs)) {
	$gtabsid = lmbdb_result($rs,"TAB_ID");
	$gtabs["tablename"][$gtabsid] = lmbdb_result($rs,"TABELLE");
	$gtabs["tabledesc"][$gtabsid] = lmbdb_result($rs,"BESCHREIBUNG");
	if(lmbdb_result($rs,"SPECIFICPRIVILEGE")){$gtabs["specific_userrules"][$gtabsid] = 1;}
}

# -------------------- Standard Ordner anlegen ---------------
function createDefaultDirs(){
	global $session;
	global $db;

	$sqlquery = "SELECT USER_ID,GROUP_ID,USERNAME FROM LMB_USERDB WHERE DEL = ".LMB_DBDEF_FALSE;
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs){$commit = 1;}
	while(lmbdb_fetch_row($rs)){
		create_default_dir(lmbdb_result($rs,"USER_ID"),lmbdb_result($rs,"USER_ID"),lmbdb_result($rs,"USERNAME"));
	}
}

# -------------------- Standard Ordner anlegen ---------------
function create_default_dir($userid,$groupid,$username){
	global $session;
	global $db;
	global $lang;

	$NEXTID = next_db_id('LDMS_STRUCTURE');
	$NEXTRID = next_db_id('LDMS_RULES');

	# Öffentlicher Ordner
	$sqlquery = "SELECT ID FROM LDMS_STRUCTURE WHERE ERSTUSER = 1 AND TYP = 1 AND FIX = ".LMB_DBDEF_TRUE." AND LEVEL = 0";
	$rs0 = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs0){$commit = 1;}
	if(!lmbdb_result($rs0,"ID")){

		$sqlquery = "INSERT INTO LDMS_STRUCTURE (ID,NAME,LEVEL,ERSTUSER,ERSTGROUP,TYP,FIX,FIELD_ID,TAB_ID) VALUES ($NEXTID,'$lang[813]',0,1,$groupid,1,".LMB_DBDEF_TRUE.",0,0)";
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		if(!$rs){$commit = 1;}
		$sqlquery = "INSERT INTO LDMS_RULES (ID,GROUP_ID,FILE_ID,LMVIEW,LMADD,DEL,ADDF,EDIT) VALUES ($NEXTRID,1,$NEXTID,".LMB_DBDEF_TRUE.",".LMB_DBDEF_TRUE.",".LMB_DBDEF_TRUE.",".LMB_DBDEF_TRUE.",".LMB_DBDEF_TRUE.")";
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		if(!$rs){$commit = 1;}
		$info .= ".. public folder created for user <b>$username</b><br>";

		# Dokumente
		$sqlquery = "INSERT INTO LDMS_STRUCTURE (ID,NAME,LEVEL,ERSTUSER,ERSTGROUP,TYP,FIX,FIELD_ID,TAB_ID) VALUES (".($NEXTID+1).",'$lang[1705]',$NEXTID,1,$groupid,1,".LMB_DBDEF_FALSE.",0,0)";
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		$NEXTRID++;
		$sqlquery = "INSERT INTO LDMS_RULES (ID,GROUP_ID,FILE_ID,LMVIEW,LMADD,DEL,ADDF,EDIT) VALUES ($NEXTRID,1,".($NEXTID+1).",".LMB_DBDEF_TRUE.",".LMB_DBDEF_TRUE.",".LMB_DBDEF_TRUE.",".LMB_DBDEF_TRUE.",".LMB_DBDEF_TRUE.")";
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		if(!$rs){$commit = 1;}
		$info .= ".. document folder created for user <b>$username</b><br>";

		# Bilder
		$sqlquery = "INSERT INTO LDMS_STRUCTURE (ID,NAME,LEVEL,ERSTUSER,ERSTGROUP,TYP,FIX,FIELD_ID,TAB_ID) VALUES (".($NEXTID+2).",'$lang[1706]',$NEXTID,1,$groupid,1,".LMB_DBDEF_FALSE.",0,0)";
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		$NEXTRID++;
		$sqlquery = "INSERT INTO LDMS_RULES (ID,GROUP_ID,FILE_ID,LMVIEW,LMADD,DEL,ADDF,EDIT) VALUES ($NEXTRID,1,".($NEXTID+2).",".LMB_DBDEF_TRUE.",".LMB_DBDEF_TRUE.",".LMB_DBDEF_TRUE.",".LMB_DBDEF_TRUE.",".LMB_DBDEF_TRUE.")";
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		if(!$rs){$commit = 1;}
		$info .= ".. picture folder created <b>$username</b><br>";
	}

	$NEXTID = ($NEXTID+3);

	# Eigene Dateien
	$sqlquery = "SELECT ID FROM LDMS_STRUCTURE WHERE ERSTUSER = $userid AND TYP = 4 AND FIX = ".LMB_DBDEF_TRUE." AND LEVEL = 0";
	$rs0 = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs0){$commit = 1;}
	if(!lmbdb_result($rs0,"ID")){
		$sqlquery = "INSERT INTO LDMS_STRUCTURE (ID,NAME,LEVEL,ERSTUSER,ERSTGROUP,TYP,FIX,TAB_ID,SORT) VALUES ($NEXTID,'$lang[812]',0,".$userid.",".$groupid.",4,".LMB_DBDEF_TRUE.",0,2)";
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		if(!$rs){$commit = 1;}
		$NEXTRID++;
		$info .= ".. user folder created for user <b>$username</b><br>";
	}

	echo $info;
}



function refreshHtaccess(){
	global $umgvar;

	$sub = read_dir($umgvar['pfad']."/USER",0);
	foreach ($sub["path"] as $key => $value){
		if($sub["typ"][$key] == "dir" AND is_numeric($sub["name"][$key])){
			if($psswd = lmb_htaccess($sub["name"][$key],0,0)){
				$psswdlist[] = $psswd;
			}
		}
	}
	
	$path = $umgvar["pfad"]."/TEMP";

	if($psswdlist AND $umgvar["protect_temp_directory"]){
		$htaccess_file = fopen($path."/.htaccess","w");
		$value = "AuthName ByPassword\nAuthType Basic\nAuthUserFile ".$path."/.htpasswd\nrequire valid-user\nallow from all";
		fputs($htaccess_file,$value);
		fclose($htaccess_file);

		$value = implode("\n",$psswdlist);
		$htpasswd_file = fopen($path."/.htpasswd","w");
		fputs($htpasswd_file,$value);
		fclose($htpasswd_file);
	}else{
		$htaccess_file = fopen($path."/.htaccess","w");
		$value = "allow from all";
		fputs($htaccess_file,$value);
		fclose($htaccess_file);
	}
}


function resetarguments(){
    global $db;
	global $session;
	global $db;
	global $gfield;
	global $gtab;

    set_time_limit(1800); # 30min
    require_once ('admin/tables/argument.dao');

    $sqlquery = "SELECT LMB_CONF_TABLES.TABELLE,LMB_CONF_FIELDS.FIELD_NAME,LMB_CONF_FIELDS.TAB_ID,LMB_CONF_FIELDS.FIELD_ID,LMB_CONF_FIELDS.ARGUMENT FROM LMB_CONF_FIELDS,LMB_CONF_TABLES WHERE LMB_CONF_FIELDS.ARGUMENT_TYP = 15 AND LMB_CONF_FIELDS.TAB_ID = LMB_CONF_TABLES.TAB_ID";
    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
    while(lmbdb_fetch_row($rs)){
        $tab_id = lmbdb_result($rs,"TAB_ID");
        $field_id = lmbdb_result($rs,"FIELD_ID");
        $argument = lmbdb_result($rs,"ARGUMENT");

        if($argument) {
            if(arg_refresh($tab_id, $field_id, $argument)){
                lmb_alert('Rebuild complete for '. lmbdb_result($rs,"TABELLE"). ' '. lmbdb_result($rs,"FIELD_NAME"));
            }
        }
    }

}



if($refresh_htaccess){
	refreshHtaccess();
}


if($umgvar["lock"]){
	$user_lock = "CHECKED";
}else{
	$user_lock = "";
}

if($locking){
	if($syslock){
		$sqlquery = "UPDATE LMB_UMGVAR SET NORM = 1 WHERE FORM_NAME = 'lock'";
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

		#$sqlquery = "UPDATE LMB_SESSION SET LOGOUT = ".LMB_DBDEF_TRUE." WHERE USER_ID != ".$session["user_id"];
		#$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

		$sqlquery = "DELETE FROM LMB_SESSION WHERE USER_ID != ".$session["user_id"];
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

		$user_lock = "CHECKED";
		$_SESSION["umgvar"]["lock"] = 1;
	}else{
		$sqlquery = "UPDATE LMB_UMGVAR SET NORM = 0 WHERE FORM_NAME = 'lock'";
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		$user_lock = "";
		$_SESSION["umgvar"]["lock"] = 0;
	}
}


if($newsystem){

	# delete all trigger
	lmb_dropAllDBTrigger();

	# delete all constrains
	lmb_dropAllForeignKeys();

	# drop views
	lmb_dropView();
	
	# Tabellen löschen
	$odbc_table = dbf_20(array($DBA["DBSCHEMA"],null,"'TABLE'"));
	foreach($odbc_table["table_name"] as $tkey => $tablename) {
		if(lmb_strtoupper(lmb_substr($tablename,0,4)) != "LMB_" AND lmb_strtoupper(lmb_substr($tablename,0,5)) != "LDMS_"){
		    lmb_dropTable($tablename);
		}
	}

	# systemtabellen leeren
	$sqlquery = "DELETE FROM LMB_ACTION_DEPEND";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_ATTRIBUTE_D";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_ATTRIBUTE_P";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_ATTRIBUTE_W";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_CHARTS";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_CONF_FIELDS";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_CONF_GROUPS";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_CONF_TABLES";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_CONF_VIEWS";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_CONF_VIEWFIELDS";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_CRONTAB";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_FONTS";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_FORMS";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_FORM_LIST";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_GROUPS WHERE GROUP_ID != 1";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_GTAB_GROUPDAT";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_GTAB_PATTERN";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_GTAB_ROWSIZE";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_REMINDER";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_REMINDER_LIST";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_HISTORY_ACTION";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_HISTORY_BACKUP";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_HISTORY_UPDATE";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_HISTORY_USER";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_INDIZE_D";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_INDIZE_DS";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_INDIZE_F";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_INDIZE_FS";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_INDIZE_HISTORY";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_INDIZE_W";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_LANG_DEPEND";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_REPORTS";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_REPORT_LIST";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_RULES_ACTION WHERE GROUP_ID != 1";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_RULES_DATASET";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_RULES_FIELDS";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_RULES_REPFORM";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_RULES_TABLES";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_SELECT_D";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_SELECT_P";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_SELECT_W";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_SESSION";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_SNAP";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_SNAP_SHARED";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_TABLETREE";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_TRIGGER";
	$rs = lmbdb_exec($db,$sqlquery);
    $sqlquery = "DELETE FROM LMB_CUSTVAR";
    $rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_USERDB WHERE USER_ID != 1";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_USER_COLORS WHERE USERID != 1";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_USRGRP_LST";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_REVISION";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_WFL";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_WFL_HISTORY";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_WFL_INST";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_WFL_TASK";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LDMS_FAVORITES";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LDMS_RULES";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LDMS_STRUCTURE";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "UPDATE LMB_ACTION SET EXTENSION = ''";
	$rs = lmbdb_exec($db,$sqlquery);

	lmb_dropTable('LDMS_FILES');
	lmb_dropTable('LDMS_META');


	# Dateitabellen anlegen
	create_default_files();
	
	# Dateisystem neu anlegen für User 1
	createDefaultDirs();

    # Eigene globale Variablen Tabelle anlegen
    create_custvar_table();

	# Menürechte neu einlesen für User 1
	#$check_all = 1;
	#require("admin/tools/grusrref.php");
	
	# Dateien löschen
	$delpath = array($umgvar["temp"]."/log/",$umgvar["temp"]."/thumpnails/",$umgvar["temp"]."/txt/");
	foreach ($delpath as $key => $value){
		rmdirr($value);
	}

}


function create_default_files(){
	global $db;

	$tab_group = 0;

	require_once("admin/tables/tab.lib");
	require_once("extra/explorer/filestructure.lib");

	# Alte Tabellen löschen
	$sqlquery = "SELECT TAB_ID,TABELLE,TAB_GROUP FROM LMB_CONF_TABLES WHERE UPPER(TABELLE) LIKE 'LDMS_%'";
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	while(lmbdb_fetch_row($rs)){
		if(lmb_strtoupper(lmbdb_result($rs,"TABELLE")) == lmb_strtoupper("LDMS_FILES")){delete_tab(lmbdb_result($rs,"TAB_ID"));}
		elseif(lmb_strtoupper(lmbdb_result($rs,"TABELLE")) == lmb_strtoupper("LDMS_META")){delete_tab(lmbdb_result($rs,"TAB_ID"));}
		$tab_group = lmbdb_result($rs,"TAB_GROUP");
	}
	
	$odbc_table = dbf_20(array($DBA["DBSCHEMA"],"LDMS_%","'TABLE'"));
	foreach($odbc_table["table_name"] as $tkey => $tablename) {
		if(lmb_strtoupper($tablename) == "LDMS_FILES"){
		    lmb_dropTable('LDMS_FILES');
		}
		if(lmb_strtoupper($tablename) == "LDMS_META"){
		    lmb_dropTable('LDMS_META');
		}
	}
	
	#  delete user-definition
	$prepare_string = "UPDATE LMB_USERDB SET UFILE = ?";
	if(!lmb_PrepareSQL($prepare_string,array(''),__FILE__,__LINE__)){$commit = 1;}

	# Gruppe anlegen
	$sqlquery = "SELECT * FROM LMB_CONF_GROUPS WHERE ID = $tab_group";
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(lmbdb_fetch_row($rs)){
	    $group_id = lmbdb_result($rs,"ID");
	}else{
	    $group_id = add_tabgroup("Limbassys","Limbas Systemtabellen");
	}

	if($group_id){
		# Tabelle LDMS_FILES anlegen
		if($tab_id = add_tab("LDMS_FILES",$group_id,"Files",0,3,1)){
			# Limbas Felder anlegen
			$xfield = extended_fields_files();
			add_extended_fields($xfield[0],$tab_id[0],1);
			# Systemfelder anlegen
			add_extended_fields_sys("LDMS_FILES",$xfield[1]);
			# Sonderfelder anlegen
		}
		# Tabelle LDMS_META anlegen
		if($tab_id = add_tab("LDMS_META",$group_id,"Files_Meta",$tab_id[0],3,1)){
			# Felder anlegen
			$nfield = extended_fields_filesmeta();
			add_extended_fields($nfield,$tab_id[0],1);
		}
	}

}

function edit_lockmessage($value){
	global $umgvar;

	# --- update lock.txt ----------------------------
	$file = fopen($umgvar["pfad"]."/TEMP/lock.txt","w");
	fputs($file,$value);
	fclose($file);
}

function get_lockmessage(){
	global $umgvar;

	# --- get lock.txt ----------------------------
	if(file_exists($umgvar["pfad"]."/TEMP/lock.txt")){
		$content = file($umgvar["pfad"]."/TEMP/lock.txt");
		return implode("",$content);
	}
}

function create_custvar_table(){
    global $db;

    require_once("admin/tables/tab.lib");

    lmb_dropTable('LMB_CUSTVAR_DEPEND');

    $tab_group = 0;
    $sqlquery = "SELECT TAB_GROUP FROM LMB_CONF_TABLES WHERE UPPER(TABELLE) LIKE 'LDMS_%' LIMIT 1";
    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
    while(lmbdb_fetch_row($rs)){
        $tab_group = lmbdb_result($rs,"TAB_GROUP");
    }

    if($tab_group){
        # Tabelle LMB_CUSTVAR_DEPEND anlegen
        if($tab_id = add_tab("LMB_CUSTVAR_DEPEND",$tab_group,"Custom Vars",0,3,1)){
            # Felder anlegen
            $nfield = extended_fields_custvar();
            add_extended_fields($nfield,$tab_id[0],1);
        }
    }

}

/*

# already done in gtab_array.lib (SNAP_loadInSession)
function snaprefresh(){
	global $db;

	require_once("extra/snapshot/snapshot.lib");

	$sqlquery = "SELECT * FROM LMB_SNAP";
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

	while(lmbdb_fetch_row($rs)){
		$snapfilter = SNAP_revalidate(lmbdb_result($rs,"FILTER"),lmbdb_result($rs,"TABID"),lmbdb_result($rs,"ID"));
	}
}
*/

function drulesReset($tabid){
	global $db;
	
	if(!is_numeric($tabid)){return false;}
	
	$sqlquery = "DELETE FROM LMB_RULES_DATASET WHERE TABID = $tabid";
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs){$commit = 1;}

	if(!$commit){return true;}else{return false;}
}

function specificUserrules($tabid){
	global $db;
	global $gtabs;
	global $session;
	
	if(!is_numeric($tabid)){return false;}
	require_once("gtab/gtab.lib");
	
	$sqlquery = "SELECT ID FROM ".$gtabs["tablename"][$tabid];
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs){$commit = 1;}
	while(lmbdb_fetch_row($rs)){
		$rec[] = lmbdb_result($rs,"ID");
	}
	if(!add_GtabUserRules($tabid,$rec,array(null,"g","v",null,2),1)){$commit = 1;}
	
	if(!$commit){return true;}else{return false;}
}


/* --- Auswahlpools neu sortieren ------------------------------- */
function resortSelect(){
	global $db;
	
	$sqlquery = "SELECT ID FROM LMB_SELECT_P";
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs) {$commit = 1;}
	while(lmbdb_fetch_row($rs)) {
		$sqlquery1 = "SELECT ID FROM LMB_SELECT_W WHERE POOL = ".lmbdb_result($rs, "ID")." ORDER BY LEVEL,SORT";
		$rs1 = lmbdb_exec($db,$sqlquery1) or errorhandle(lmbdb_errormsg($db),$sqlquery1,$action,__FILE__,__LINE__);
		if(!$rs1) {$commit = 1;}
		$NEXTID=1;
		while(lmbdb_fetch_row($rs1)) {
			$sqlquery2 = "UPDATE LMB_SELECT_W SET SORT = $NEXTID WHERE ID = ".lmbdb_result($rs1, "ID");
			$rs2 = lmbdb_exec($db,$sqlquery2) or errorhandle(lmbdb_errormsg($db),$sqlquery2,$action,__FILE__,__LINE__);
			if(!$rs2) {$commit = 1;}
			$NEXTID++;
		}
	}

	$sqlquery = "SELECT ID FROM LMB_ATTRIBUTE_P";
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs) {$commit = 1;}
	while(lmbdb_fetch_row($rs)) {
		$sqlquery1 = "SELECT ID FROM LMB_ATTRIBUTE_W WHERE POOL = ".lmbdb_result($rs, "ID")." ORDER BY LEVEL,SORT";
		$rs1 = lmbdb_exec($db,$sqlquery1) or errorhandle(lmbdb_errormsg($db),$sqlquery1,$action,__FILE__,__LINE__);
		if(!$rs1) {$commit = 1;}
		$NEXTID=1;
		while(lmbdb_fetch_row($rs1)) {
			$sqlquery2 = "UPDATE LMB_ATTRIBUTE_W SET SORT = $NEXTID WHERE ID = ".lmbdb_result($rs1, "ID");
			$rs2 = lmbdb_exec($db,$sqlquery2) or errorhandle(lmbdb_errormsg($db),$sqlquery2,$action,__FILE__,__LINE__);
			if(!$rs2) {$commit = 1;}
			$NEXTID++;
		}
	}
	
	if(!$commit){return true;}else{return false;}
}




if($mselect){
	require_once("admin/tools/multiselect_refresh.lib");
	multiselectRefreshCount();
	relationRefreshCount();
}

if($refresh_squences){
	if(lmb_rebuildSequences()){lmb_alert("sequences successfully created");}else{lmb_alert("failed to create sequences!");}
}

if($specific_userrules){
	specificUserrules($specific_userrules);
}

if($drules_reset){
	drulesReset($drules_reset);
}

if($sessiondel){
	$sqlquery = "DELETE FROM LMB_SESSION";
	$rs = lmbdb_exec($db,$sqlquery);
	ini_set('soap.wsdl_cache_ttl', 1);
}

# --- Dateisystem Files / Meta erstellen ----
if($filetabs){
	create_default_files();
}

# Default Ordner
if($filestructure){
	createDefaultDirs();
}


if($deleteuserfilesave){
    lmb_delete_user_filesave();
}

#if($snaprefresh){
#	snaprefresh();
#}

if($refresh_foreignkey){
	lmb_rebuildForeignKey(1);
}

if($refresh_trigger){
	lmb_rebuildTrigger(1);
}

# lock message
if($lockmessage){
	edit_lockmessage($lockmessage);
}

if($delete_thumbs){
	rmdirr($umgvar["pfad"]."/TEMP/thumpnails/",0,1);
}

if($delete_files){
	rmdirr($umgvar["pfad"]."/TEMP/txt/",0,1);
}

if($refresh_thumps){
	$sqlquery = "UPDATE LDMS_FILES SET THUMB_OK = TRUE";
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
}

if($refresh_procedures){
	if(!dbq_16(array($DBA["DBSCHEMA"],1))){
		lmb_alert("some errors by rebuild database procedures, check error logs..");
	}else{
		lmb_alert("database procedures successfully build!");
	}
}

if($refresh_indexes){
	if(!lmb_rebuildIndex()){
		lmb_alert("ERROR by rebuild database indexes, check error logs..");
	}
}

if($resortselect){
	if(!resortSelect()){
		lmb_alert("ERROR by sorting Selectpools");
	}
}

if($createExifConf){
    require_once('extra/explorer/metadata.lib');
	create_exif_conf();
	
}

if($resetarguments){
	resetarguments();
}



$lock_message = get_lockmessage();


?>

<SCRIPT LANGUAGE="JavaScript">

function mselect(evt) {
	link = confirm("<?=$lang[2033]?>");
	if(link) {
		limbasWaitsymbol(evt,1);
		document.location.href="main_admin.php?action=setup_sysupdate&mselect=1";
	}
}

function resetarguments(evt) {
	link = confirm("<?=$lang[3069]?>");
	if(link) {
		limbasWaitsymbol(evt,1);
		document.location.href="main_admin.php?action=setup_sysupdate&resetarguments=1";
	}
}

function resetthumps(evt) {
	link = confirm("<?=$lang[2549]?>");
	if(link) {
		limbasWaitsymbol(evt,1);
		document.location.href="main_admin.php?action=setup_sysupdate&refresh_thumps=1";
	}
}

function deletethumbs(evt) {
	link = confirm("<?=$lang[2548]?>");
	if(link) {
		limbasWaitsymbol(evt,1);
		document.location.href="main_admin.php?action=setup_sysupdate&delete_thumbs=1";
	}
}

function deletefiles(evt) {
	link = confirm("<?=$lang[2694]?>");
	if(link) {
		limbasWaitsymbol(evt,1);
		document.location.href="main_admin.php?action=setup_sysupdate&delete_files=1";
	}
}

function foreignrefresh(evt) {
	link = confirm("<?=$lang[2477]?>");
	if(link) {
		limbasWaitsymbol(evt,1);
		document.location.href="main_admin.php?action=setup_sysupdate&refresh_foreignkey=1";
	}
}

function triggerrefresh(evt) {
	link = confirm("<?=$lang[2696]?>");
	if(link) {
		limbasWaitsymbol(evt,1);
		document.location.href="main_admin.php?action=setup_sysupdate&refresh_trigger=1";
	}
}

function procedurerefresh(evt) {
	link = confirm("<?= $lang[2653] ?>");
	if(link) {
		limbasWaitsymbol(evt,1);
		document.location.href="main_admin.php?action=setup_sysupdate&refresh_procedures=1";
	}
}

function indexrefresh(evt) {
	link = confirm("<?= $lang[2722] ?>");
	if(link) {
		limbasWaitsymbol(evt,1);
		document.location.href="main_admin.php?action=setup_sysupdate&refresh_indexes=1";
	}
}

function squencerefresh(evt) {
	link = confirm("<?= $lang[2663] ?>");
	if(link) {
		limbasWaitsymbol(evt,1);
		document.location.href="main_admin.php?action=setup_sysupdate&refresh_squences=1";
	}
}

function lrefresh() {
	if(confirm("<?=$lang[1265]?>")) {
         $("<div id='iframelayer' style='width:550px;height:400px;'><iframe src='main_admin.php?action=setup_linkref' style='width:100%;height:100%;overflow:auto;'></iframe></div>").dialog({
            title: '<?=$lang[1056]?>',
            width:560,
            height:410,
            resizable: false,
            modal: true
        });
	}
}

function gurefresh(DATA) {
	if(confirm("<?=$lang[1264]?>")) {
         $("<div id='iframelayer' style='width:550px;height:400px;'><iframe src='main_admin.php?action=setup_grusrref&check_all=" + DATA +"' style='width:100%;height:100%;overflow:auto;'></iframe></div>").dialog({
            title: '<?=$lang[1054]?>',
            width:560,
            height:410,
            resizable: false,
            modal: true
        });
	}
}

function drulesReset(evt) {
	link = confirm("<?= $lang[2551]."?\\n".$lang[2553] ?>");
	if(link) {
		limbasWaitsymbol(evt,1);
		document.location.href="main_admin.php?action=setup_sysupdate&drules_reset="+document.getElementById('drules_reset').value;
	}
}

function specificUserrules(evt) {
	link = confirm("<?= $lang[2552]."?" ?>");
	if(link) {
		limbasWaitsymbol(evt,1);
		document.location.href="main_admin.php?action=setup_sysupdate&specific_userrules="+document.getElementById('specific_userrules').value;
	}
}

function filestructure(evt) {
	link = confirm("<?=$lang[2697]?>");
	if(link) {
		limbasWaitsymbol(evt,1);
		document.location.href="main_admin.php?action=setup_sysupdate&filestructure=1";
	}
}

function deleteuserfilesave(evt) {
	link = confirm("<?=$lang[2328]?>");
	if(link) {
		limbasWaitsymbol(evt,1);
		document.location.href="main_admin.php?action=setup_sysupdate&deleteuserfilesave=1";
	}
}

function snaprefresh(evt) {
	snap = confirm("<?=$lang[2345]?>");
	if(snap) {
		limbasWaitsymbol(evt,1);
		document.location.href="main_admin.php?action=setup_sysupdate&snaprefresh=1";
	}
}

function refresh_htaccess(evt) {
	link = confirm("<?=$lang[2329]?>");
	if(link) {
		limbasWaitsymbol(evt,1);
		document.location.href="main_admin.php?action=setup_sysupdate&refresh_htaccess=1";
	}
}

function sessiondel(evt) {
	link = confirm("<?=$lang[1048]?>");
	if(link) {
		limbasWaitsymbol(evt,1);
		document.location.href="main_admin.php?action=setup_sysupdate&sessiondel=1";
	}
}

function filetabs(evt) {
	link = confirm("<?=$lang[1991]?>");
	if(link) {
		limbasWaitsymbol(evt,1);
		document.location.href="main_admin.php?action=setup_sysupdate&filetabs=1";
	}
}

function newsystem(evt) {
	link = confirm("<?=$lang[1594]?>");
	if(link) {
		limbasWaitsymbol(evt,1);
		document.location.href="main_admin.php?action=setup_sysupdate&newsystem=1";
	}
}

function resortselect(evt) {
	link = confirm("<?=$lang[2734]?>?");
	if(link) {
		limbasWaitsymbol(evt,1);
		document.location.href="main_admin.php?action=setup_sysupdate&resortselect=1";
	}
}

function createExifConf(evt) {

	limbasWaitsymbol(evt,1);
	document.location.href="main_admin.php?action=setup_sysupdate&createExifConf=1";
	
}


function showprocess(id,value){
	document.getElementById(id).style.width = value;
}







</SCRIPT>


<FORM ACTION="main_admin.php" METHOD="post" name="form1">
<INPUT TYPE="hidden" NAME="action" VALUE="setup_sysupdate">
<INPUT TYPE="hidden" NAME="locking">


<TABLE class="tabfringe" BORDER="0" width="600" cellspacing="2" cellpadding="2">
<TR class="tabSubHeader"><TD class="tabSubHeaderItem" colspan="3"><?=$lang[2481]?></TD></TR>
<TR class="tabBody"><TD valign="top"><?=$lang[2030]?></TD>
    <TD align="RIGHT" valign="top"><input type="button" value="OK" onclick="mselect(event);"></TD></TR>
<TR class="tabBody"><TD valign="top"><?=$lang[3069]?></TD>
    <TD align="RIGHT" valign="top"><input type="button" value="OK" onclick="resetarguments(event);"></TD></TR>
<TR class="tabBody"><TD valign="top"><?=$lang[2547]?></TD>
    <TD align="RIGHT" valign="top"><input type="button" value="OK" onclick="resetthumps(event);"></TD></TR>
<TR class="tabBody"><TD valign="top"><?=$lang[2546]?></TD>
    <TD align="RIGHT" valign="top"><input type="button" value="OK" onclick="deletethumbs(event);"></TD></TR>
<TR class="tabBody"><TD valign="top"><?=$lang[2695]?></TD>
    <TD align="RIGHT" valign="top"><input type="button" value="OK" onclick="deletefiles(event);"></TD></TR>
<TR class="tabBody"><TD valign="top"><?=$lang[2734]?></TD>
    <TD align="RIGHT" valign="top"><input type="button" value="OK" onclick="resortselect(event);"></TD></TR>
<TR class="tabBody"><TD valign="top"><?=$lang[2862]?></TD>
    <TD align="RIGHT" valign="top"><input type="button" value="OK" onclick="createExifConf(event);"></TD></TR>


<TR class="tabSubHeader"><TD class="tabSubHeaderItem" colspan="3"><?=$lang[2482]?></TD></TR>

<TR class="tabBody"><TD valign="top"><?=$lang[2476]?></TD>
    <TD align="RIGHT" valign="top"><input type="button" value="OK" onclick="foreignrefresh(event);"></TD></TR>

<?php if(LMB_DBFUNC_PROCEDUREHANDLE){?>
<TR class="tabBody"><TD valign="top"><?=$lang[2721]?></TD>
    <TD align="RIGHT" valign="top"><input type="button" value="OK" onclick="indexrefresh(event);"></TD></TR>
<TR class="tabBody"><TD valign="top"><?=$lang[2652]?></TD>
    <TD align="RIGHT" valign="top"><input type="button" value="OK" onclick="procedurerefresh(event);"></TD></TR>
<TR class="tabBody"><TD valign="top"><?=$lang[2488]?></TD>
    <TD align="RIGHT" valign="top"><input type="button" value="OK" onclick="triggerrefresh(event);"></TD></TR>
<?php if(LMB_DBFUNC_SEQUENCE){?>
<TR class="tabBody"><TD valign="top"><?=$lang[2662]?></TD>
<TD align="RIGHT" valign="top"><input type="button" value="OK" onclick="squencerefresh(event);"></TD></TR>
<?php }}?>

<TR class="tabSubHeader"><TD class="tabSubHeaderItem" colspan="3"><?=$lang[2484]?></TD></TR>

<TR class="tabBody"><TD valign="top"><?=$lang[1056]?></TD>
    <TD align="RIGHT" valign="top"><input type="button" value="OK" onclick="lrefresh();"></TD></TR>

<TR class="tabBody"><TD valign="top"><?=$lang[1054]?></TD>
    <TD align="RIGHT" valign="top"><input type="button" value="OK" onclick="gurefresh('1');"></TD></TR>

<?php if($gtabs["tabledesc"]){?>
<TR class="tabBody"><TD valign="top"><?=$lang[2551]?></TD>
<TD align="RIGHT" valign="top">
<select id="drules_reset"><option></option>
<?php
foreach ($gtabs["tabledesc"] as $key => $val){
	echo "<option value=\"$key\">".$lang[$val]."</option>";
}
?>
</select>
<input type="button" value="OK" onclick="drulesReset(event);" style="color:red"></TD></TR>

<?php if($gtabs["specific_userrules"]){?>
<TR class="tabBody"><TD valign="top"><?=$lang[2552]?></TD>
<TD align="RIGHT" valign="top">
<select id="specific_userrules"><option></option>
<?php
foreach ($gtabs["tabledesc"] as $key => $val){
	if($gtabs["specific_userrules"][$key]){
		echo "<option value=\"$key\">".$lang[$val]."</option>";
	}
}
?>
</select>
<input type="button" value="OK" onclick="specificUserrules(event);" style="color:red"></TD></TR>
<?php }}?>


<TR class="tabSubHeader"><TD class="tabSubHeaderItem" colspan="3"><?=$lang[2485]?></TD></TR>

<TR class="tabBody"><TD valign="top"><?=$lang[1849]?></TD>
    <TD align="RIGHT" valign="top"><input type="button" value="OK" onclick="filestructure(event);"></TD></TR>

<TR class="tabBody"><TD valign="top"><?=$lang[2367]?></TD>
    <TD align="RIGHT" valign="top"><input type="button" value="OK" onclick="deleteuserfilesave(event);"></TD></TR>

<?php /*
<TR class="tabBody"><TD valign="top"><?=$lang[2343]?></TD>
<TD align="RIGHT" valign="top"><input type="button" value="OK" onclick="snaprefresh(event);"></TD></TR>
*/?>

<TR class="tabBody"><TD valign="top"><?=$lang[2327]?></TD>
    <TD align="RIGHT" valign="top"><input type="button" value="OK" onclick="refresh_htaccess(event);"></TD></TR>

<TR class="tabBody"><TD valign="top"><?=$lang[1057]?></TD>
    <TD align="RIGHT" valign="top"><input type="button" value="OK" onclick="sessiondel(event);"></TD></TR>


<TR><TD colspan="3"><HR></TD></TR>

<TR class="tabBody"><TD valign="top"><?=$lang[1420]?></TD>
    <TD align="RIGHT" valign="top"><INPUT TYPE="CHECKBOX" NAME="syslock" VALUE="1" OnClick="this.form.locking.value=1;this.form.submit();" STYLE="border:none;background-color:transparent;" <?=$user_lock?>></TD></TR>

<TR class="tabBody"><TD><?=$lang[2368]?></TD><TD ALIGN="right"><TEXTAREA NAME="lockmessage" OnChange="this.form.submit();" STYLE="width:250px;height:50px;border:1px solid black;"><?=$lock_message?></TEXTAREA></TD></TR>


<TR><TD class="tabHeader" colspan="3"><b style="color:red"><?=$lang[2483]?></b></TD></TR>

<TR class="tabBody"><TD valign="top"><?=$lang[1992]?></TD>
    <TD align="RIGHT" valign="top"><input type="button" value="OK" onclick="filetabs(event);" style="color:red"></TD></TR>

<TR class="tabBody"><TD valign="top"><?=$lang[1593]?></TD>
    <TD align="RIGHT" valign="top"><input type="button" value="OK" onclick="newsystem(event);" style="color:red"></TD></TR>


<TR><TD class="tabFooter" colspan="3"></TR>
</TABLE>


</FORM>
</div>