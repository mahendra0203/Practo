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


//echo "Create Patient";
//$patient_info_up = array("age"=>19);
//$patient_info = array("name"=>"first patient","primary_mobile"=>"9198667556","primary_email"=>"doctest@gmail.com");
//$res = $a->patient->create($patient_info);
//$res = $a->patient->edit(1147098,$patient_info_up);
echo "<br><br><br> get all patients";
$res = $a->patient->read_all();
echo json_encode($res);
//$res = $a->patient->delete(1146990);
$date = new DateTime("2012-10-12 24:13:13");
//echo $date->format("Y-m-d H:i:s");

echo "<br><br><br> after";
$res_a = $a->patient->modified_after($date);
echo json_encode($res_a);
echo "<br><br><br> before";
$res_b = $a->patient->modified_before($date);
echo json_encode($res_b);
echo "<br><br><br> read first 100";
$res = $a->patient->read_till(100);
echo json_encode($res);

echo "<br><br><br> get the count of patients";
$res = $a->patient->count();
echo json_encode($res);

$after = new DateTime("2009-10-12 24:13:13");
$before = new DateTime("2012-10-12 24:13:13");
$with_deleted = true;
$limit = 10;

$ip = array("modified_before"=>$before,"modified_after"=>$after,"with_deleted"=>true);
echo "<br><br><br> get";
$res = $a->patient->get_custom($ip);
echo json_encode($res);

?>

<html>
<body>
	
</body>
</html>