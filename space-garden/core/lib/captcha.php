<?php
	require_once '../../core/lib/Path.class.php';

    $length = rand (4,5);
    $code = "";
    while (strlen($code) < $length)
    {
        $generate = mt_rand(48, 90);
        if ($generate < 58 || $generate > 64) $code .= strtolower(chr($generate));
    }

    session_start();
	$_SESSION["captcha_code"] = strtolower($code);
					
	$image = imagecreatetruecolor (300, 60); // maakt de image met de groote van 300px breed, en 60px hoog
	$background = imagecolorallocate ($image, rand (190, 255), rand (190, 255), rand (190, 255));
	$second = imagecolorallocate ($image, rand (120, 190), rand (120, 190), rand (120, 190));
	$third = imagecolorallocate ($image, rand (120, 190), rand (120, 190), rand (120, 190));
	$forth = imagecolorallocate ($image, rand (211, 255), rand (211, 255), rand (211, 255));
	imageFilledRectangle($image, 0, 0, 300, 60, $background);
	imageFilledRectangle($image, rand (120, 160), rand (-20, 20), rand (200, 300), rand (40, 80), $second);
	imagefilledEllipse($image, rand (290, 310), rand (-10, 10), rand (200, 240), rand (200, 240), $third);
	$top = rand (-20, 50);
	imageFilledRectangle($image, $top, rand (-20, 20), rand (120, 160), rand ($top, 80), $third);
	imagefilledEllipse($image, rand (-10, 10), rand (50, 70), rand (200, 240), rand (200, 240), $second);
	$aFonts = array (Path::get_path() . 'fonts/1942.ttf', Path::get_path() . 'fonts/OldNewspaperTypes.ttf', Path::get_path() . 'fonts/hockey.ttf'); // zet alle beschikbare fonts in een array
	$aCode = str_split ($code); // zet alle karakters apart in een array
	
	for($i = 0; $i < 100; $i++)
	{
		$x = $i*rand (0, 3);
		imageline($image , $x, 0, $x + rand (-10, 10), 60, $forth);
	}
	
	for ($i = 0; $i < count ($aCode); $i++) // een for-lus maken voor het aantal karakters dat de $aCode array bevat
	{
	   $fontcolor = imagecolorallocate ($image, // kleurencombinatie maken voor de image variabel ($image)
	      rand (40, 120), // rood,
	      rand (40, 120), // groen,
	      rand (40, 120)); // blauw, deze geven de nieuwe kleur per karakter
	   if (count ($aCode) == 4) // de volgende locaties (x-as) aanmaken voor een code van 4 karakters lang
	   {
	      $pos[0] = rand (15, 55); // locatie aanmaken (x-as) voor de eerste karakter
	      $pos[1] = rand (80, 120); // locatie aanmaken (x-as) voor de tweede karakter
	      $pos[2] = rand (145, 185); // locatie aanmaken (x-as) voor de derde karakter
	      $pos[3] = rand (210, 250); // locatie aanmaken (x-as) voor de vierde karakter
	   }
	   if (count ($aCode) == 5) // de volgende locaties (x-as) aanmaken voor een code van 5 karakters lang
	   {
	      $pos[0] = rand (10, 45); // locatie aanmaken (x-as) voor de eerste karakter
	      $pos[1] = rand (65, 100); // locatie aanmaken (x-as) voor de tweede karakter
	      $pos[2] = rand (120, 155); // locatie aanmaken (x-as) voor de derde karakter
	      $pos[3] = rand (175, 210); // locatie aanmaken (x-as) voor de vierde karakter
	      $pos[4] = rand (230, 265); // locatie aanmaken (x-as) voor de vijfde karakter
	   }
	   imagettftext ($image, // image voorbereiden voor de image variabel ($image)
	   rand (40, 50), // fontgrootte, willekeurig getal laten kiezen tussen de 13 en 19
	   rand (-30, 30), // draaihoek, willekeur getal laten kiezen tussen de -31 en de 31
	   $pos[$i], // karakter positie breedte toewijzen, hebben we al voorbereid ($pos[])
	   rand (40,50), // karakter positie hoogte, kiezen tussen de 51 en de 19
	   $fontcolor, // fontkleur toewijzen, hebben we al voorbereid ($fontcolor)
	   $aFonts[rand (0, 2)], // font, willekeurig font toewijzen uit de array ($aFonts)
	   $aCode[$i]); // code toewijzen, op volgorde van de array
	}
	$image_resize = imagecreatetruecolor (150, 30); 
	imagecopyresampled($image_resize, $image, 0, 0, 0, 0, 150, 30, 300, 60);
	header('Content-type: image/png');
	imagepng ($image_resize); // de .png image aanmaken als captcha.png
	imagedestroy ($image); // de handel afronden, en klaar!
	imagedestroy ($image_resize);

?> 