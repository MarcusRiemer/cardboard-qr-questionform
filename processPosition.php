<?php
  // import the data-model.
  require_once('data.php');
  
  if (!file_exists("locations/")) {
	$count = 0;
  } else {
	$iterator = new FilesystemIterator("locations/", FilesystemIterator::SKIP_DOTS);
	$count = iterator_count($iterator);
  }
  
  $array = array();
  for ($i = 0; $i < $count; $i++)
  {
    $array[] = $_POST["destination$i"];
  }
  
  // create position from POST data
  $submittedPosition = new Position(
                            $_POST["positionId"],
                            $_POST["position"],
							$array
                          );
  $submittedPosition->saveToFile(DataType::POSITION);

  // redirect back to previous page
  header('Location: ' . $_SERVER['HTTP_REFERER']);
?>
