<?php
include "../config.php";
include "../mal.php";
include "../image.php";

$time  = microtime(true);
$rand  = @$_GET['rand'];
$user = 'khobbits';
$h = @$_GET['h'];
if ($h == '' || $h > 140)
    $h = 140;
$w = @$_GET['w'];
if ($w == '' || $w > 600)
    $w = 600;
header('Content-Type: image/png');

if ($rand == '') {
    $rand = random01();
}

if (file_exists('./01/' . $rand) == false)
    $rand = random01();

if (checklastgen($user, $rand, $h, $w) != 1) {
    $time2 = microtime(true);
    readfile('mal_cache/' . $user . '_' . $rand . '_' . $h . '_' . $w . '.png');
    $time3   = microtime(true);
    $info    = 'Cached image';
} else {
    $profile = getUserStats($client_id, $client_secret, $redirect_uri, $challenge, $token_storage);
    $base = generateimage($user, $rand, $h, $w, $profile);
    $info  = 'Regenerated image';
    $time2 = microtime(true);
    imagepng($base);
    $time3 = microtime(true);
    ignore_user_abort(true);
    imagepng($base, 'mal_cache/' . $user . '_' . $rand . '_' . $h . '_' . $w . '.png');
    imagedestroy($base);
}

$load = sys_getloadavg();
$imagetime = ($time3 - $time2);
$gentime = (microtime(true) - $time - $imagetime);
$data = '<tr><td>' . gmdate('d/m/y H:i:s') . '</td><td>' . $_SERVER['REQUEST_URI'] . '</td><td>' . $rand . '</td><td>' . $info . '</td><td>' . round($gentime, 6) . 's</td><td>' . round($imagetime, 6) . 's</td><td>' . $load[0] . ' ' . $load[1] . ' ' . $load[2] . '</td><td>' . $_SERVER['REMOTE_ADDR'] . '</td><td>' . @$_SERVER['HTTP_REFERER'] . "</td></tr>\r\n";

$lines = file('genlog.txt');
if (sizeof($lines) >= 200)
    array_shift($lines);
$lines[] = $data;

$fp = fopen('genlog.txt', 'w');
fwrite($fp, implode('', $lines));
fclose($fp);

?>  