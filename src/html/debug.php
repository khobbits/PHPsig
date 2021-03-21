<?php
include "../config.php";
include "../mal.php";
include "../image.php";

$time  = microtime(true);
$rand  = urlencode(@$_GET['rand']);
$user = "khobbits";
$h = intval(@$_GET['h']);
if ($h == '' || $h > 140)
    $h = 140;
$w = intval(@$_GET['w']);
if ($w == '' || $w > 600)
    $w = 600;

if ($rand == '') {
    $rand = random01();
}

if (file_exists("./01/" . $rand) == false)
    $rand = random01();

$profile = getUserStats($client_id, $client_secret, $redirect_uri, $challenge, $token_storage);

$base = generateimage($user, $rand, $h, $w, $profile);
$time2 = microtime(true);

ignore_user_abort(true);
imagepng($base, "mal_cache/" . $user . "_" . $rand . "_" . $h . "_" . $w . ".png");
imagedestroy($base);

echo "<br /><br /><IMG SRC='mal_cache/" . $user . "_" . $rand . "_" . $h . "_" . $w . ".png' BORDER='0' />";

$time3 = microtime(true);

$load      = sys_getloadavg();
$imagetime = ($time3 - $time2);
$gentime   = (microtime(true) - $time - $imagetime);

echo "<br /><br />Username: " . $profile["user_name"];
echo "<br />Watching: " . $profile["user_watching"];
echo "<br />Completed: " . $profile["user_completed"];
echo "<br />On Hold: " . $profile["user_onhold"];
echo "<br />Dropped: " . $profile["user_dropped"];
echo "<br />Plan to watch: " . $profile["user_plantowatch"];
echo "<br />Life wasted on anime: " . $profile["user_days_spent_watching"] . " days";
echo "<br /><br />Rand: $rand";


echo ("<br /><br />Image generation: " . round($gentime, 6) . "s<br />Server load: " . $load[0] . " " . $load[1] . " " . $load[2] . "");

?>  