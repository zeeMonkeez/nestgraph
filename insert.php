<?php

require 'inc/config.php';
require 'inc/class.db.php';
require 'collect.php';

try {
  $db = new DB($config);
  if ($result = $db->res->query("SELECT id, serial FROM devices")) {
    while ($row = mysqli_fetch_row($result)) {
  	$data = get_nest_data($row[1]);
	$wtf = $data['timestamp'];
	//echo "$row[0] $row[1] $wtf\n";
  	if (!empty($data['timestamp'])) {
    		if ($stmt = $db->res->prepare("REPLACE INTO data (device_id, timestamp, heating, cooling, fan, autoAway, manualAway, leaf, target, current, humidity, updated) VALUES (?,?,?,?,?,?,?,?,?,?,?,NOW())")) {
      		$stmt->bind_param("isiiiiiiddi", $row[0], $data['timestamp'], $data['heating'], $data['cooling'], $data['fan'], $data['autoAway'], $data['manualAway'], $data['leaf'], $data['target_temp'], $data['current_temp'], $data['humidity']);
		if (!$stmt->execute()) {
		    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
		}
      		$stmt->close();
    	}
  	}
    }
    mysqli_free_result($result);
  }  
  $db->close();
} catch (Exception $e) {
  $errors[] = ("DB connection error! <code>" . $e->getMessage() . "</code>.");
}

?>
