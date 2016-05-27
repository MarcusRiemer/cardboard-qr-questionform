<?php
  // import the data-model.
  require_once('data.php');

  // create location from POST data
  $submittedLocation = new Location(
                            $_POST["locationId"],
							$_POST["location"]
                          );
  $submittedLocation->saveToFile(DataType::LOCATION);

  // redirect back to previous page
  header('Location: ' . $_SERVER['HTTP_REFERER']);
?>
