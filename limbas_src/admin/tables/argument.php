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
 * ID: 101
 */


 // change Argument
if($argument_change){
	$argument = trim(parse_db_string($argument));
	$sqlquery = "UPDATE LMB_CONF_FIELDS".$tab." SET ARGUMENT = '$argument' WHERE FIELD_ID = $fieldid AND TAB_GROUP = $tab_group AND TAB_ID = $tab_id";
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
}

// get Argument
$sqlquery = "SELECT ARGUMENT_TYP,ARGUMENT,ID FROM LMB_CONF_FIELDS WHERE FIELD_ID = $fieldid AND TAB_GROUP = $tab_group AND TAB_ID = $tab_id";
$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
if(lmbdb_fetch_row($rs)) {
	$result_argument["argument"] = lmbdb_result($rs, "ARGUMENT");
	$result_argument["argument_typ"] = lmbdb_result($rs, "ARGUMENT_TYP");
	$result_argument["id"] = lmbdb_result($rs, "ID");
}


/* --- Kopf --------------------------------------- */
echo '<div style="text-align:center;font-weight:bold;text-decoration:underline;margin-bottom:10px">', $lang[517], '</div>';

?>
<script src="extern/codemirror/lib/codemirror.js?v=<?=$umgvar["version"]?>"></script>
<link rel="stylesheet" href="extern/codemirror/lib/codemirror.css?v=<?=$umgvar["version"]?>">
<script src="extern/codemirror/edit/matchbrackets.js?v=<?=$umgvar["version"]?>"></script>
<script src="extern/codemirror/edit/matchtags.js?v=<?=$umgvar["version"]?>"></script>
<script src="extern/codemirror/mode/javascript/javascript.js?v=<?=$umgvar["version"]?>"></script>
<script src="extern/codemirror/mode/php/php.js?v=<?=$umgvar["version"]?>"></script>
<script src="extern/codemirror/mode/sql/sql.js?v=<?=$umgvar["version"]?>"></script>
<script type="text/javascript">
    $(function() {
        editor = CodeMirror.fromTextArea(document.getElementById('argument'), {
            lineNumbers: true,
            matchBrackets: true,
            mode: "<?= ($result_argument["argument_typ"] == 15) ? 'text/x-php' : 'text/x-sql' ?>",
            indentWithTabs: true,
            smartIndent: true,
            autofocus: true
        });
    });

    function insertText(val) {
        editor.replaceRange(val, editor.getCursor());
    }
</script>
<style>
    .CodeMirror {
        border: 1px solid <?=$farbschema['WEB3']?>;
        height: auto;
    }
</style>

<TABLE BORDER="0" cellspacing="0" cellpadding="2" style="margin: 0 auto;">

<FORM NAME="form0">
<?php
if($result_argument["argument_typ"] == 15){
	echo "<TR><TD><B>$lang[518]</B></TD>";
	echo "<TD><SELECT NAME=\"umgvariablen\" OnChange=\"insertText(this.value);\"><OPTION>";
	echo "<OPTION VALUE=\"\$session['username']\">$lang[519]";
	echo "<OPTION VALUE=\"\$session['vorname'] \$session['name']\">$lang[520]";
	echo "<OPTION VALUE=\"\$session['email']\">$lang[521]";
	echo "</SELECT></TD></TR>";


	echo "<TR><TD><B>$lang[168]</B></TD>";
	echo "<TD><SELECT OnChange=\"insertText(this.value);\"><OPTION>";
	if($gfield[$tab_id]["field_id"]){
        foreach($gfield[$tab_id]["field_id"] as $key => $value){
            echo "<OPTION VALUE=\"#*".$gfield[$tab_id]['field_id'][$key]."#\">"."(".$gfield[$tab_id]['field_id'][$key].") ".$gfield[$tab_id]['field_name'][$key];
        }
	}
	echo "</SELECT>";
	echo "</TR></TD>";
}
?>

</FORM>

<FORM ACTION="main_admin.php" METHOD="post" NAME="form1">
<input type="hidden" name="action" value="setup_argument">
<input type="hidden" name="atid" value="<?= $atid ?>">
<input type="hidden" name="tab_id" value="<?= $tab_id ?>">
<input type="hidden" name="tab_group" value="<?= $tab_group ?>">
<input type="hidden" name="fieldid" value="<?= $fieldid ?>">
<input type="hidden" name="typ" value="<?= $typ ?>">

<?php
/* --- Ergebnisliste --------------------------------------- */
if($gfield[$tab_id]["argument_typ"][$fieldid] == 15 OR $argument_typ == 15){
    echo "<TR><TD COLSPAN=\"2\">eg ( return \"hello world #*1#\"; )</TD></TR>";
} else {
    echo "<TR><TD COLSPAN=\"2\">eg ( CUSTOMER.FIRSTNAME ".LMB_DBFUNC_CONCAT." CUSTOMER.LASTNAME )</TD></TR>";
}
echo "<TR><TD COLSPAN=\"2\"><TEXTAREA NAME=\"argument\" id=\"argument\" ROWS=\"10\" COLS=\"50\">".str_replace("\"","&quot;",$result_argument['argument'])."</TEXTAREA></TD></TR>";
echo "<TR><TD COLSPAN=\"2\" ALIGN=\"CENTER\"><INPUT TYPE=\"submit\" VALUE=\"$lang[522]\" name=\"argument_change\">&nbsp;&nbsp;";
if($gfield[$tab_id]["argument_typ"][$fieldid] == 15){
    echo "<INPUT TYPE=\"submit\" VALUE=\"$lang[1304]\" name=\"argument_refresh\"></TD></TR>";
}

?>

</TABLE>
</FORM>

<?
// rebuild Argument
if($result_argument["argument"] AND $argument_refresh){
    set_time_limit(900); #15min
	if(arg_refresh($tab_id,$fieldid,$result_argument['argument'])){
		echo '<br><div style="width:100%;text-align:center;color:green;"><b>Rebuild complete!</b></div>';
    }
}
?>