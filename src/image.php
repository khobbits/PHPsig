<?php

putenv('GDFONTPATH=' . realpath('../fonts'));

function random01()
{
    $files = array();
    $i = -1;
    $handle = opendir('./01/');
    $exts = explode(' ', 'jpg jpeg png gif');
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
    $timenow = date('U');
    if (file_exists('mal_cache/' . $user . '_' . $rand . '_' . $h . '_' . $w . '.png') == true) {
        if ($timenow > (filemtime('mal_cache/' . $user . '_' . $rand . '_' . $h . '_' . $w . '.png') + (60 * 30))) {
            return 1;
        } else {
            return 0;
        }
    } else {
        return 1;
    }
}

function generateimage($user, $rand, $h, $w, $profile)
{

    $base = ImageCreateTrueColor($w, $h);
    $left = ImageCreateFromPng('./01/' . $rand);
    $top = ImageCreateFromPng('./02.png');
    $mid = ImageCreateFromPng('./03.png');
    $right = ImageCreateFromPng('./04.png');

    $text = ImageCreateTrueColor(260, 94);
    $text2 = ImageCreateTrueColor(260, 94);
    $text3 = ImageCreateTrueColor(398, 46);
    $white = ImageColorAllocate($text, 255, 255, 255);
    imageFilledRectangle($text, 0, 0, 260, 94, $white);
    imageFilledRectangle($text2, 0, 0, 260, 94, $white);
    imageFilledRectangle($text3, 0, 0, 398, 46, $white);

    $font1 = 'Audiowide';
    $font2 = 'Control';
    $colour1 = imagecolorexact($mid, 138, 0, 136);
    $colour2 = imagecolorexact($mid, 0, 0, 0);

    $x1 = 10;
    $x2 = 120;
    $ts = 10;
    foreach (array(
                 $text2,
                 $text,
                 $mid
             ) as $layer) {
        $y = 12;
        imagefttext($layer, $ts, 0, $x1, $y, $colour1, $font1, 'Watching:');
        imagefttext($layer, $ts, 0, $x2, $y, $colour1, $font1, $profile['user_watching']);
        $y += 15;
        imagefttext($layer, $ts, 0, $x1, $y, $colour1, $font1, 'Complete:');
        imagefttext($layer, $ts, 0, $x2, $y, $colour1, $font1, $profile['user_completed']);
        $y += 15;
        if ($h >= 125) {
            imagefttext($layer, $ts, 0, $x1, $y, $colour1, $font1, 'On Hold:');
            imagefttext($layer, $ts, 0, $x2, $y, $colour1, $font1, $profile['user_onhold']);
            $y += 15;
            imagefttext($layer, $ts, 0, $x1, $y, $colour1, $font1, 'Planned:');
            imagefttext($layer, $ts, 0, $x2, $y, $colour1, $font1, $profile['user_plantowatch']);
            $y += 15;
        } else if ($h >= 110) {
            imagefttext($layer, $ts, 0, $x1, $y, $colour1, $font1, 'Planned:');
            $plancount = $profile['user_plantowatch'] + $profile['user_onhold'];
            imagefttext($layer, $ts, 0, $x2, $y, $colour1, $font1, $plancount);
            $y += 15;
        }
        if ($h >= 140) {
            imagefttext($layer, $ts, 0, $x1, $y, $colour1, $font1, 'Dropped:');
            imagefttext($layer, $ts, 0, $x2, $y, $colour1, $font1, $profile['user_dropped']);
            $y += 15;
        }
        imagefttext($layer, $ts, 0, $x1, $y, $colour1, $font1, 'Life Wasted:');
        imagefttext($layer, $ts, 0, $x2, $y, $colour1, $font1, $profile['user_days_spent_watching'] . ' days');

    }

    imagefilter($text, IMG_FILTER_SMOOTH, 1000);
    imagefilter($text, IMG_FILTER_SMOOTH, 1000);
    imagefilter($text2, IMG_FILTER_SMOOTH, 1000);

    imageColorTransparent($text, $white);
    imageColorTransparent($text2, $white);
    imageCopyMerge($mid, $text, 0, 0, 0, 0, 260, 94, 40);
    imageCopyMerge($mid, $text2, 0, 0, 0, 0, 260, 94, 65);

    imagefttext($top, 12, 0, 1, 12, $colour2, $font2, 'http://myanimelist.net/animelist/' . $user);
    imagefttext($text3, 12, 0, 1, 12, $colour2, $font2, 'http://myanimelist.net/animelist/' . $user);

    imagefilter($text3, IMG_FILTER_SMOOTH, 1000);
    imageColorTransparent($text3, $white);
    imageCopyMerge($top, $text3, 0, 0, 0, 0, 398, 46, 60);


    imagecopy($base, $left, 0, 0, 0, 0, 202, 140);
    imagecopy($base, $top, 202, 0, 0, 0, 398, 46);
    imagecopy($base, $right, 462, 46, 0, 0, 138, 94);
    imagecopy($base, $mid, 202, 46, 0, 0, 260, 94);

    imagedestroy($mid);
    imagedestroy($left);
    imagedestroy($top);
    imagedestroy($right);
    return $base;
}