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
  
  // create question from POST data
  $submittedPosition = new Position(
                            $_POST["positionId"],
                            $_POST["position"],
							$array
                            //array($_POST["destination1"],
                              //    $_POST["destination2"],
                                //  $_POST["destination3"],
                                  //$_POST["destination4"])
                          );
  $submittedPosition->saveToFile(DataType::POSITION);

  // redirect back to previous page
  header('Location: ' . $_SERVER['HTTP_REFERER']);
?>
