<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
 <HEAD>
  <TITLE> Sig Generation Log </TITLE>
 </HEAD>
 <BODY>
<table border="1" bordercolor="000000" style="background-color:FFFFFF" width="95%" cellpadding="3" cellspacing="0">
	<tr>
		<th>Date</th>
		<th>Request Path</th>
		<th>Image</th>
		<th>PHP Action</th>
		<th>XML Action</th>
		<th>Exec Time</th>
		<th>Image Load</th>
		<th>Server Load</th>
		<th>User</th>
		<th>Referer</th>
	</tr>

<?
$fp = @fopen("genlog.txt", "r");
if ($fp) {
    while (!feof($fp)) {
        $buffer = fgets($fp);
        echo $buffer;
    }
    fclose($fp);
} else {
    echo "<tr><td>No Data found</td></tr>";
}
?>
</table>

 </BODY>
</HTML>