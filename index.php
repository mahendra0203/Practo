<?php

include ("practo.php");
/*Tesitng the apis*/
$a = new Practo(array("username"=>"mahendra0203","password"=>"practodemo"));
echo "auth_token:" . $a->auth_token . '<br><br>';
$profile = $a->get_practice_profile();
echo "Profile<br>";
echo "Name: " . $profile['name'] .'<br><br>';
$settings = $a->get_practice_settings();
echo "Settings<br>";
echo "Practice_id:" . $settings['practice_id'] .'<br><br>';
$subscription = $a->get_practice_subscription();
echo "Subscription<br>";
echo "Subscription Id:" . $subscription['id'] .'<br><br>';
?>

<html>
<body>
	
</body>
</html>