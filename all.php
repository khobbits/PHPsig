<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
 <HEAD>
  <TITLE> All Sigs </TITLE>
   </HEAD>
 <BODY>
<TABLE>
<?php
$user = $_GET['user'];
if ($user == '')
    $user = "khobbits";

$handle = opendir('./01/');
$exts   = explode(' ', 'jpg jpeg png gif');
while (false !== ($file = readdir($handle))) {
    foreach ($exts as $ext) { // for each extension check the extension
        if (preg_match('/\.' . $ext . '$/i', $file, $test)) {
            echo "<TR><TD>$file</TD><TD><IMG SRC='sig.php?user=" . $user . "&rand=" . $file . "' BORDER='0' /></TD></TR>";
        }
    }
}
closedir($handle);

?>  
</TABLE>
 </BODY>
</HTML>
