<?php

$filepath = 'docs' ;

//https://www.youtube.com/watch?v=23KufSqo6cQ

$server = "localhost";
$dbusername = "root";
$dbpassword = "1projectK!";
$dbname = "oodoo_current";
$table = "device_details";
$con = new mysqli($server, $dbusername, $dbpassword, $dbname);
	if($con->connect_error) {
		var_dump("Connection error: ". $con->connect_error );
	}

	if (!$con->set_charset("utf8")) {
	    printf("Error loading character set utf8: %s\n", $con->error);
	    exit();
	} else {
	    printf("Current character set: %s\n", $con->character_set_name());
	}	



	if($handle = opendir($filepath)) {
		while( false !== ($file = readdir($handle))) {
			if(is_dir($file) == true){
				break;
			}	
			$path = realpath(dirname(__FILE__).'/docs');
			$now = $path.'/'.$file;
			//var_dump($now); die;		
/*			$img_exist = mysqli_query($con, "SELECT * FROM device_details WHERE name = '.$file.'");
			$count = mysqli_num_rows($img_exist);
			var_dump($count); die;*/
			$first_lat = substr($file, strpos($file, "___")+3);
			$first_long = substr($first_lat, strpos($first_lat, "_")+1);
			$sec_lat_long = substr($first_long, strpos($first_long, "__")+2);
			$arr = explode("_", substr($sec_lat_long,0, strpos($sec_lat_long, "___")));
			$latitude = floatval($first_lat);
			$longitude = floatval($first_long);
			$latitude1 = $arr[0];
			$longitude1 = $arr[1];

			if(mysqli_query($con, "DESCRIBE `device_details`")) {
				$result = mysqli_query($con, "SELECT device_id FROM device_details order by id desc");
				$num_rows = mysqli_num_rows($result);
				if($num_rows >= 1){
					$device_id = $result->fetch_object()->device_id;
					$device_no = intval(preg_replace('/[^0-9]+/', '', $device_id), 10)+1;
					$device_id = 'OFONU'.$device_no;
				}else{
					$device_id = 'OFONU1087600';
				}
			}else{
				$sql = "CREATE TABLE device_details (
				id int NOT NULL AUTO_INCREMENT,
				image_name varchar(255) NOT NULL,
				device_id varchar(20) NOT NULL,
				serial_no varchar(255),
				mac_address varchar(255),
				image_id varchar(255),
				latitude double(11,8) NOT NULL,
				longitude double(11,8) NOT NULL,
				latitude1 varchar(255) NOT NULL,
				longitude1 varchar(255) NOT NULL,
				primary key(id)) ENGINE=InnoDB DEFAULT CHARSET=utf8";

				if(mysqli_query($con, $sql)) {
					echo "Table Created successfully";
				}else{
					echo "Error Creating Table :". mysqli_error($con);
					break;
				}
				$device_id = 'OFCHN1087600';
			}
			$image_id = hash('sha256',$file);
			$image[] = array(
				'image_id' => $image_id,
				'image_location' => realpath(dirname(__FILE__).'/docs/').$file
			);
			$sql = "INSERT INTO device_details (image_name, device_id, image_id, latitude, longitude, latitude1, longitude1)
			VALUES ('20170317_042004(PM)___12.888112_80.231323__12°53′17″ N_80°13′52″ E___[map].jpg', '$device_id', '$image_id', '$latitude', '$longitude', '$latitude1', '$longitude1')";
			if(mysqli_query($con, $sql)){
				echo "Device Detail Added ".$file;
				echo "\n";
			}else{
				echo "Error Adding Details :". mysqli_error($con); 
				break;
			}
		}
	}








/*$sql = "INSERT INTO MyGuests (firstname, lastname, email)
VALUES ('John', 'Doe', 'john@example.com')";

if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();*/



?>