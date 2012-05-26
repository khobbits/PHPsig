<?php 
$nolog = 0;
$time = microtime(true);
$rand = @$_GET['rand'];
$nolog = @$_GET['nolog'];
$user = @$_GET['user'];
if ($user == '') $user = 'khobbits';
putenv('GDFONTPATH=' . realpath('.'));
$h = @$_GET['h'];
if ($h == '' || $h > 140) $h = 140;
$w = @$_GET['w'];
if ($w == '' || $w > 600) $w = 600;
header('Content-Type: image/png');

function random01 () {
	$files = array(); $i = -1;
	$handle = opendir('./01/');
	$exts = explode(' ', 'jpg jpeg png gif');
	while (false !== ($file = readdir($handle))) {
		foreach($exts as $ext) { // for each extension check the extension
			if (preg_match('/\.'.$ext.'$/i', $file, $test)) {
				$files[] = $file;
				++$i;
			}
		}
	}
	closedir($handle); 
	$rand = mt_rand(0, $i);
	return $files[$rand];
}

function checklastgen ( $user, $rand, $h, $w ) {
			$timenow = date( 'U' ); 
			if( file_exists( 'mal_cache/' . $user . '_' . $rand . '_'.$h.'_'.$w.'.png' ) == true ) {
				if( $timenow > ( filemtime( 'mal_cache/' . $user . '_' . $rand . '_'.$h.'_'.$w.'.png' ) + ( 60 * 30 ) ) ) {
					return 1;
				} else {
					return 0;
				}
			} else {
				return 1;
			}	
}

function generateimage ( $user, $rand, $h, $w) {
	include 'malparse.class.php';
	$x = new mal_data;
	$profile = $x->GetProfile($user);
	$x->CloseSocket();

	$base = ImageCreateTrueColor($w,$h);
	$left = ImageCreateFromPng('./01/'.$rand);
	$top = ImageCreateFromPng('./02.png');
	$mid = ImageCreateFromPng('./03.png');
	$right = ImageCreateFromPng('./04.png');

	$text = ImageCreateTrueColor(260,94);
	$text2 = ImageCreateTrueColor(260,94);
	$white = ImageColorAllocate ($text, 255, 255, 255);
	imageFilledRectangle($text, 0,0, 260, 94, $white); 
	imageFilledRectangle($text2, 0,0, 260, 94, $white); 

	$font1 = 'FGAaron';
	$font2 = 'PlayStation';
	$colour1 = imagecolorexact($mid,138,0,136);
	$colour2 = imagecolorexact($mid,0,0,0);

	foreach (array($text2,$text,$mid) as $layer) {
    
    $y = 12;		
    imagefttext($layer,11,0,2,$y,$colour1,$font1,'Watching:');
		imagefttext($layer,11,0,130,$y,$colour1,$font1,$profile['user_watching']);
    $y += 15;
		imagefttext($layer,11,0,2,$y,$colour1,$font1,'Complete:');
		imagefttext($layer,11,0,130,$y,$colour1,$font1,$profile['user_completed']);
    $y += 15;
    if ($h >= 125) {
      imagefttext($layer,11,0,2,$y,$colour1,$font1,'On Hold:');
      imagefttext($layer,11,0,130,$y,$colour1,$font1,$profile['user_onhold']);
      $y += 15;
      imagefttext($layer,11,0,2,$y,$colour1,$font1,'Planned:');
      imagefttext($layer,11,0,130,$y,$colour1,$font1,$profile['user_plantowatch']);
      $y += 15;
    }
    else if ($h >= 110) {
      imagefttext($layer,11,0,2,$y,$colour1,$font1,'Planned:');
      $plancount = $profile['user_plantowatch'] + $profile['user_onhold'];
      imagefttext($layer,11,0,130,$y,$colour1,$font1,$plancount);
      $y += 15;
    }
    if ($h >= 140) {
      imagefttext($layer,11,0,2,$y,$colour1,$font1,'Dropped:');
      imagefttext($layer,11,0,130,$y,$colour1,$font1,$profile['user_dropped']);
      $y += 15;
    }		
		imagefttext($layer,11,0,2,$y,$colour1,$font1,'Life Wasted:');
		imagefttext($layer,11,0,130,$y,$colour1,$font1,$profile['user_days_spent_watching'].' days');

	}

	imagefilter($text, IMG_FILTER_SMOOTH, 1000);
	imagefilter($text, IMG_FILTER_SMOOTH, 1000);
	imagefilter($text2, IMG_FILTER_SMOOTH, 1000);

	imageColorTransparent($text, $white);
	imageColorTransparent($text2, $white);
	imageCopyMerge($mid,$text,0,0,0,0,260,94,40);
	imageCopyMerge($mid,$text2,0,0,0,0,260,94,65);
	
	imagefttext($top,12,0,2,10,$colour2,$font2,'http://myanimelist.net/animelist/'.$user);

	imagecopy($base,$left,0,0,0,0,202,140);
	imagecopy($base,$top,202,0,0,0,398,46);
	imagecopy($base,$right,462,46,0,0,138,94);
	imagecopy($base,$mid,202,46,0,0,260,94);

	imagedestroy($mid);
	imagedestroy($left);
	imagedestroy($top);
	imagedestroy($right);
	return array ($base, $profile['socketed'] . @$profile['error']);
}

if ($rand == '') { 
	$rand = random01();
}

if( file_exists('./01/'.$rand) == false ) $rand = random01();

if (checklastgen ( $user, $rand, $h, $w ) != 1) {
	$time2 = microtime(true);
	readfile ('mal_cache/'.$user.'_'.$rand.'_'.$h.'_'.$w.'.png');
	$time3 = microtime(true);
	$info = 'Cached image';
	$xmlinfo = 'No XML Call';
} else {
	list ($base, $xmlinfo) = generateimage ( $user, $rand, $h, $w);
	$info = 'Regenerated image';
	$time2 = microtime(true);
	imagepng($base);
	$time3 = microtime(true);
	ignore_user_abort (true);
	imagepng($base, 'mal_cache/' . $user . '_' . $rand .'_'.$h.'_'.$w.'.png');
	imagedestroy($base);
}

if ($nolog == 0) {
	$load = sys_getloadavg();
	$imagetime = ($time3 - $time2);
	$gentime = (microtime(true) - $time - $imagetime);
	$data = '<tr><td>'.gmdate('d/m/y H:i:s').'</td><td>'.$_SERVER['REQUEST_URI'].'</td><td>'.$rand.'</td><td>'.$info.'</td><td>'.$xmlinfo.'</td><td>'.round($gentime,6).'s</td><td>'.round($imagetime,6).'s</td><td>'.$load[0].' '.$load[1].' '.$load[2].'</td><td>'.$_SERVER['REMOTE_ADDR'].'</td><td>'.@$_SERVER['HTTP_REFERER']."</td></tr>\r\n";

	$lines = file('genlog.txt');
	if(sizeof($lines) >= 200) array_shift($lines);
	$lines[] = $data;

	$fp = fopen('genlog.txt','w');
	fwrite ($fp,implode('', $lines));
	fclose($fp);
}
?>  