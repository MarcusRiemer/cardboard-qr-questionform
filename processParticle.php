<?php
  // import the data-model.
  require_once('data.php');
  
  
  // create particle system from POST data
  $submittedParticleSystem = new ParticleSystem(
                              $_POST["particleId"],
                              $_POST["startColor"],
                              $_POST["endColor"]
                            );					
  $submittedParticleSystem->saveToFile(DataType::PARTICLESYSTEM);

  
  // redirect back to previous page
  header('Location: ' . $_SERVER['HTTP_REFERER']);
?>
