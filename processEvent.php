<?php
  // import the data-model.
  require_once('data.php');

  // create question from POST data
  $submittedThing = new Thing(
                            $_POST["thingId"],
                            $_POST["thing"],
							$_POST["start"],
							$_POST["location"]
                          );
  $submittedThing->saveToFile(DataType::THING);

  // redirect back to previous page
  header('Location: ' . $_SERVER['HTTP_REFERER']);
?>
