
<?php
$nolog = 0;
$time  = microtime(true);
$rand  = $_GET['rand'];
$user  = $_GET['user'];
if ($user == '')
    $user = "khobbits";
putenv('GDFONTPATH=' . realpath('.'));
$h = $_GET['h'];
if ($h == '')
    $h = 140;
$w = $_GET['w'];
if ($w == '')
    $w = 600;

function random01()
{
    $files  = array();
    $i      = -1;
    $handle = opendir('./01/');
    $exts   = explode(' ', 'jpg jpeg png gif');
    while (false !== ($file = readdir($handle))) {
        foreach ($exts as $ext) { // for each extension check the extension
            if (preg_match('/\.' . $ext . '$/i', $file, $test)) {
                $files[] = $file;
                ++$i;
            }
        }
    }
    closedir($handle);
    $rand = mt_rand(0, $i);
    return $files[$rand];
}

function checklastgen($user, $rand, $h, $w)
{
    $timenow = date("U");
    if (file_exists("mal_cache/" . $user . "_" . $rand . "_" . $h . "_" . $w . ".png") == true) {
        if ($timenow > (filemtime("mal_cache/" . $user . "_" . $rand . "_" . $h . "_" . $w . ".png") + (60 * 10))) {
            return 1;
        } else {
            return 0;
        }
    } else {
        return 1;
    }
}


function generateimage($user, $rand, $h, $w)
{
    include 'malparse.class.php';
    $x       = new mal_data;
    $profile = $x->GetProfile($user);
    $x->CloseSocket();
    
    $base  = ImageCreateTrueColor($w, $h);
    $left  = ImageCreateFromPng('./01/' . $rand);
    $top   = ImageCreateFromPng('./02.png');
    $mid   = ImageCreateFromPng('./03.png');
    $right = ImageCreateFromPng('./04.png');
    
    $text  = ImageCreateTrueColor(260, 94);
    $text2 = ImageCreateTrueColor(260, 94);
    $white = ImageColorAllocate($text, 255, 255, 255);
    imageFilledRectangle($text, 0, 0, 260, 94, $white);
    imageFilledRectangle($text2, 0, 0, 260, 94, $white);
    
    $font1   = 'FGAaron';
    $font2   = 'PlayStation';
    $colour1 = imagecolorexact($mid, 138, 0, 136);
    $colour2 = imagecolorexact($mid, 0, 0, 0);
    
    foreach (array(
        $text2,
        $text,
        $mid
    ) as $layer) {
        imagefttext($layer, 11, 0, 2, 12, $colour1, $font1, 'Watching:');
        imagefttext($layer, 11, 0, 130, 12, $colour1, $font1, $profile['user_watching']);
        imagefttext($layer, 11, 0, 2, 27, $colour1, $font1, 'Complete:');
        imagefttext($layer, 11, 0, 130, 27, $colour1, $font1, $profile['user_completed']);
        imagefttext($layer, 11, 0, 2, 42, $colour1, $font1, 'Unwatched:');
        imagefttext($layer, 11, 0, 130, 42, $colour1, $font1, $profile['user_onhold']);
        imagefttext($layer, 11, 0, 2, 57, $colour1, $font1, 'Dropped:');
        imagefttext($layer, 11, 0, 130, 57, $colour1, $font1, $profile['user_dropped']);
        imagefttext($layer, 11, 0, 2, 72, $colour1, $font1, 'Planned:');
        imagefttext($layer, 11, 0, 130, 72, $colour1, $font1, $profile['user_plantowatch']);
        imagefttext($layer, 11, 0, 2, 87, $colour1, $font1, 'Life Wasted:');
        imagefttext($layer, 11, 0, 130, 87, $colour1, $font1, $profile['user_days_spent_watching'] . ' days');
        
    }
    
    imagefilter($text, IMG_FILTER_SMOOTH, 1000);
    imagefilter($text, IMG_FILTER_SMOOTH, 1000);
    imagefilter($text2, IMG_FILTER_SMOOTH, 1000);
    
    imageColorTransparent($text, $white);
    imageColorTransparent($text2, $white);
    imageCopyMerge($mid, $text, 0, 0, 0, 0, 260, 94, 40);
    imageCopyMerge($mid, $text2, 0, 0, 0, 0, 260, 94, 65);
    
    imagefttext($top, 12, 0, 2, 10, $colour2, $font2, 'http://myanimelist.net/animelist/' . $user);
    
    imagecopy($base, $left, 0, 0, 0, 0, 202, 140);
    imagecopy($base, $top, 202, 0, 0, 0, 398, 46);
    imagecopy($base, $right, 462, 46, 0, 0, 138, 94);
    imagecopy($base, $mid, 202, 46, 0, 0, 260, 94);
    
    echo "XMLAction: " . $profile["socketed"];
    echo "<br /><br />Username: " . $profile["user_name"];
    echo "<br />Watching: " . $profile["user_watching"];
    echo "<br />Completed: " . $profile["user_completed"];
    echo "<br />On Hold: " . $profile["user_onhold"];
    echo "<br />Dropped: " . $profile["user_dropped"];
    echo "<br />Plan to watch: " . $profile["user_plantowatch"];
    echo "<br />Life wasted on anime: " . $profile["user_days_spent_watching"] . " days";
    echo "<br /><br />Rand: $rand";
    echo "<table><tr><td><IMG SRC='./01/" . $rand . "' BORDER='0' /></td><td>";
    echo "<IMG SRC='./02.png' BORDER='0' />";
    echo "<br /><IMG SRC='./03.png' BORDER='0' />";
    echo "<IMG SRC='./04.png' BORDER='0' /></td></table></tr>";
    imagepng($top, "./002.png");
    echo "<br /><br /><IMG SRC='./002.png' BORDER='0' />";
    imagepng($mid, "./003.png");
    echo "<br /><br /><IMG SRC='./003.png' BORDER='0' />";
    
    imagedestroy($mid);
    imagedestroy($left);
    imagedestroy($top);
    imagedestroy($right);
    return array(
        $base,
        $xmlinfo
    );
}
if ($rand == '') {
    $rand = random01();
} else {
    $nolog = 1;
}
if (file_exists("./01/" . $rand) == false)
    $rand = random01();

list($base, $xmlinfo) = generateimage($user, $rand, $h, $w);
$info  = "Regenerated image";
$time2 = microtime(true);
ignore_user_abort(true);
imagepng($base, "mal_cache/" . $user . "_" . $rand . "_" . $h . "_" . $w . ".png");
imagedestroy($base);
echo "<br /><br /><IMG SRC='mal_cache/" . $user . "_" . $rand . "_" . $h . "_" . $w . ".png' BORDER='0' />";
$time3 = microtime(true);

$load      = sys_getloadavg();
$imagetime = ($time3 - $time2);
$gentime   = (microtime(true) - $time - $imagetime);
echo ("<br /><br />Image generation: " . round($gentime, 6) . "s<br />Server load: " . $load[0] . " " . $load[1] . " " . $load[2] . "");

?>  