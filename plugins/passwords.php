<?php

/*
 * Object passwords tab by Gjermund Jensvoll
 * Version 0.7
 *
 *
 * INSTALL:
 *
 *      1. create ObjectPWs Table in your RackTables database
 *

CREATE TABLE IF NOT EXISTS `ObjectPWs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `object_id` int(10) unsigned NOT NULL,
  `user_name` char(64) DEFAULT NULL,
  `password_hash` char(64) DEFAULT NULL,
  `comment` text,
  PRIMARY KEY (`id`),
  KEY `object_id` (`object_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1490 ;

 *      2. copy passwords.php to plugins directory
 *  3. change $key variable to something random (important!)
 *  4. make sure passwords.php is only readable by webserver and root (also important!)
 *
 */


$tab['object']['passwords'] = 'Passwords';
$tabhandler['object']['passwords'] = 'passwordsTabHandler';

//$ophandler['object']['passwords']['addPW'] = 'addPW';
//registerOpHandler (object, passwords, addPW, addPW, after);

$key = "hj9kai2uru1Hoo6eezooyeeghohy9Ielah5aek9wa3quaek3tohjerie3iuPo6chu6ahj";

// Function for adding new passwords
function commitNewPassword($object_id, $user_name, $password, $comment) {
        global $key;
    /* TODO check permissions */

    $encrypted = openssl_encrypt($password, "AES-256-CBC", $key);
    return usePreparedExecuteBlade
    (
        "INSERT INTO ObjectPWs (object_id, user_name, password_hash, comment) VALUES (?, ?, ?, ?)",
        array ($object_id, $user_name, $encrypted, $comment)
    );

} /* commitNewPassword */


// Function for updating existing passwords
function commitUpdatePassword($pw_id, $user_name, $password, $comment) {
        global $key;

    /* TODO check permissions */

    $encrypted = openssl_encrypt($password, "AES-256-CBC", $key);
    return usePreparedExecuteBlade
    (
        "UPDATE ObjectPWs SET user_name=?, password_hash = ?, comment = ? WHERE id = ?",
        array ($user_name, $encrypted, $comment, $pw_id)
    );                        

} /* commitUpdatePassword */


// Function for deleting passwords
function commitDeletePassword($pw_id) {

    /* TODO check permissions */

    return usePreparedExecuteBlade
    (
        "DELETE FROM ObjectPWs WHERE id = ?",
        array ($pw_id)
    );

} /* commitDeletePassword */


// Main function
function passwordsTabHandler () {
        global $key;


// Show warning for IE users
echo "<SCRIPT language=\"JavaScript\">
<!--
var browserName = navigator.appName;
var bN = navigator.appCodeName;
if (browserName == \"Microsoft Internet Explorer\") {
document.write(\"<center><br><br><font color=red>This page requires a html5 capable browser. Turn off compatability mode in Internet Explorer.</font></center>\");
}
//-->
</SCRIPT>"; /* IE warning */

// JS toogle show/hide password
echo "<SCRIPT language='JavaScript'>
function ShowHide(pwfieldId,buttonId)
{
        if(document.getElementById(pwfieldId).type != 'password')
        {
                document.getElementById(pwfieldId).type = 'password';
                document.getElementById(buttonId).value = 'show';
        }
        else
        {
                document.getElementById(pwfieldId).type = 'text';
                document.getElementById(buttonId).value = 'hide';
        }
}
</SCRIPT>
"; /* toogle show/hide password */


        $display ="<center><br><br><br>\n";

// Debug _POST array
//if (isset($_POST['object_id']))
//print_r ($_POST);


        if (isset($_POST['op'])) {
                if ($_POST['op'] == "addPW") {
                        commitNewPassword($_POST['object_id'], $_POST['user_name'], $_POST['password'], $_POST['comment']);
                }
        if ($_POST['op'] == "editPW") {
            commitUpdatePassword($_POST['pw_id'], $_POST['user_name'], $_POST['password'], $_POST['comment']);
        }
        }

        if (isset($_GET['op'])) {
            if ($_GET['op'] == "delPW") {
                commitDeletePassword($_GET['pw_id']);
                }
        }



        // Table header -> display
        $display .= "<table cellspacing=0 cellpadding='5' align='center' class='widetable'>";
        $display .= "<tr><th>&nbsp;</th>";
        $display .= "<th class=tdleft>Username</th>";
        $display .= "<th class=tdleft>Password</th>";
        $display .= "<th class=tdleft>Comment</th>";
        $display .= "<th>&nbsp;</th></tr>";

        assertUIntArg ('object_id', __FUNCTION__);
        $object = spotEntity ('object', $_REQUEST['object_id']);

        // Existing passwords -> display
        $query = "SELECT * FROM ObjectPWs WHERE object_id = '$object[id]'";
        $result = NULL;
        $result = usePreparedSelectBlade ($query);
        while ($row = $result->fetch (PDO::FETCH_ASSOC)) {
                $pw_id = $row['id'];
                $object_id = $row['object_id'];
                $user_name = $row['user_name'];
                $password = openssl_decrypt($row['password_hash'], "AES-256-CBC", $key);
                $comment = $row['comment'];


                $display .= "<form method=post id=editPW name=editPW autocomplete=off action=\"\">";
                $display .= "<input type=hidden name=\"pw_id\" value=\"".$pw_id."\">";
        $display .= "<input type=hidden name=\"op\" value=\"editPW\">";
        $display .= "<input type=hidden name=\"object_id\" value=\"".$object_id."\">";
                $display .= "<tr><td><a href='?page=object&tab=passwords&object_id=".$object_id."&op=delPW&pw_id=".$pw_id."' onclick=\"javascript:return confirm('Are you sure you want to delete this password?')\">";
                $display .= "<img src='?module=chrome&uri=pix/tango-list-remove.png' width=16 height=16 border=0 title='Delete this password'></a></td>";
                $display .= "<td class='tdleft' NOWRAP><input type=text name=user_name value='".$user_name."' size=20></td>";

                $display .= "<td class='tdleft' NOWRAP><input type='password' id='password".$pw_id."' name='password' value='".$password."' size=30 required>";
                $display .= "<input type='button' id='button".$pw_id."' value='show' onclick=\"javascript:ShowHide('password".$pw_id."','button".$pw_id."')\"></td>\n";


                $display .= "<td class='tdleft' NOWRAP><input type=text name=comment value='".$comment."' size=30></td>";
                $display .= "<td><input type=image name=submit class=icon src='?module=chrome&uri=pix/tango-document-save-16x16.png' border=0 title='Save changes' onclick=\"javascript:return confirm('Are you sure you want to edit this password?')\"></td></form></tr>";

        }        


        // Form to add new password -> display
        $display .= "<form action=\"\" method=post autocomplete=off id=\"addPW\" name=\"addPW\">";
        $display .= "<input type=hidden name=\"object_id\" value=\"".$object['id']."\">";
        $display .= "<input type=hidden name=\"op\" value=\"addPW\">";
    $display .= "<tr><td><input type=image name=submit class=icon src='?module=chrome&uri=pix/tango-list-add.png' border=0  title='add a new password'></td>";
    $display .= "<td class='tdleft'><input type=text size=20 name=user_name tabindex=100></td>";

        $display .= "<td class='tdleft'><input type=text name='password' id=newpassword tabindex=101 size=30 required>";
        $display .= "<input type='button' id='newbutton' value='hide' onclick=\"javascript:ShowHide('newpassword','newbutton')\"></td>\n";

    $display .= "<td class='tdleft'><input type=text size=30 name=comment tabindex=102></td>";
    $display .= "<td><input type=image name=submit class=icon src='?module=chrome&uri=pix/tango-list-add.png' border=0 tabindex=103 title='add a new password'></td></tr>";
    $display .= "</form>";
    $display .= "</table><br></center>";


    // Output all display
    echo $display;

} /* passwordsTabHandler */

?>
