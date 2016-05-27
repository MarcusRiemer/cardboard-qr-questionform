<?php
  // Import the data-model.
  require_once("data.php");

  /**
   * Function to handle the "/questions/" REST-GET call to get all questions.
   *
   * It looks for all existing questions on the file-system and
   * returns them all in JSON-format.
   * The JSON will be an JSON-object, containing an JSON-object "questions",
   * which contains an array of all questions in JSON.
   *
   * @return string - The JSON answer.
   */
  function get_questions() {
    if (!file_exists("questions/")) {
      return '{"questions": []}';
    }

    $questions = array();

    $iterator = new FilesystemIterator("questions/", FilesystemIterator::SKIP_DOTS);
    while($iterator->valid()) {

      $file = fopen($iterator->getPathname(), "r");
      $content = fread($file, filesize($iterator->getPathname()));
      fclose($file);
      array_push($questions, $content);

      $iterator->next();
    }

    return '{"questions":' . "[" . implode(",", $questions) . "]" . "}";
  }
  
  /**
   * Function to handle the "/positions/" REST-GET call to get all positions.
   *
   * It looks for all existing things on the file-system and
   * returns them all in JSON-format.
   * The JSON will be an JSON-object, containing an JSON-object "positions",
   * which contains an array of all positions in JSON.
   *
   * @return string - The JSON answer.
   */
  function get_positions() {
    if (!file_exists("positions/")) {
      return '{"positions": []}';
    }

    $positions = array();

    $iterator = new FilesystemIterator("positions/", FilesystemIterator::SKIP_DOTS);
    while($iterator->valid()) {

      $file = fopen($iterator->getPathname(), "r");
      $content = fread($file, filesize($iterator->getPathname()));
      fclose($file);
      array_push($positions, $content);

      $iterator->next();
    }

    return '{"positions":' . "[" . implode(",", $positions) . "]" . "}";
  }
  
    /**
   * Function to handle the "/things/" REST-GET call to get all positions.
   *
   * It looks for all existing things on the file-system and
   * returns them all in JSON-format.
   * The JSON will be an JSON-object, containing an JSON-object "things",
   * which contains an array of all things in JSON.
   *
   * @return string - The JSON answer.
   */
  function get_things() {
    if (!file_exists("things/")) {
      return '{"things": []}';
    }

    $things = array();

    $iterator = new FilesystemIterator("things/", FilesystemIterator::SKIP_DOTS);
    while($iterator->valid()) {

      $file = fopen($iterator->getPathname(), "r");
      $content = fread($file, filesize($iterator->getPathname()));
      fclose($file);
      array_push($things, $content);

      $iterator->next();
    }

    return '{"things":' . "[" . implode(",", $things) . "]" . "}";
  }
  
     /**
   * Function to handle the "/locations/" REST-GET call to get all locations.
   *
   * It looks for all existing locations on the file-system and
   * returns them all in JSON-format.
   * The JSON will be an JSON-object, containing an JSON-object "locations",
   * which contains an array of all locations in JSON.
   *
   * @return string - The JSON answer.
   */
  function get_locations() {
    if (!file_exists("locations/")) {
      return '{"locations": []}';
    }

    $locations = array();

    $iterator = new FilesystemIterator("locations/", FilesystemIterator::SKIP_DOTS);
    while($iterator->valid()) {

      $file = fopen($iterator->getPathname(), "r");
      $content = fread($file, filesize($iterator->getPathname()));
      fclose($file);
      array_push($locations, $content);

      $iterator->next();
    }

    return '{"locations":' . "[" . implode(",", $locations) . "]" . "}";
  }

  /**
   * Class to hold google-api qr-code urls for a pair (question, coin).
   *
   * It implements the JsonSerializable-interface.
   */
  class QRCodeUrls implements JsonSerializable {
    private $question;
    private $coin;

    /**
     * Constructor.
     *
     * @param string      $questionUrl       The question qr-code url.
     * @param string      $coinUrl           The coin qr-code url.
     *
     * @return QRCodeUrls
     */
    public function __construct($questionUrl, $coinUrl) {
      $this->question = $questionUrl;
      $this->coin = $coinUrl;
    }

    public function jsonSerialize() {
      return get_object_vars($this);
    }

    public function toJson() {
      return json_encode($this);
    }
  }
  
    /**
   * Class to hold google-api qr-code urls for a position.
   *
   * It implements the JsonSerializable-interface.
   */
  class PositionQRCodeUrl implements JsonSerializable {
    private $position;

    /**
     * Constructor.
     *
     * @param string      $positionUrl       The position qr-code url.
     *
     * @return PositionQRCodeUrl
     */
    public function __construct($positionUrl) {
      $this->position = $positionUrl;
    }

    public function jsonSerialize() {
      return get_object_vars($this);
    }

    public function toJson() {
      return json_encode($this);
    }
  }

  /**
   * Function to handle the "/qrcodes/<id>" and "/qrcodesprint/<id>" REST-GET calls.
   *
   * It returns a JSON-string in which the google-api qr-code urls for both
   *  - the question
   *  - the coin
   * are contained in fields with respective names.
   *
   * @param int         $id             The question id.
   * @param int         $dimension      The wanted qr-code dimension.
   *
   * @return string - The answer JSON-string.
   */
  function get_qrcodes_by_id($id, $dimension) {
    $questionFilename = "questions/".$id.".json";

    if (!file_exists($questionFilename)) {
      http_response_code(404);
      return "";
    }

    $questionShortObject = (object) ['id' => $id, 'type' => DataType::QUESTION];

    $questionUrl = "https://chart.googleapis.com/chart?cht=qr&choe=UTF-8"
          ."&chs=".$dimension."x".$dimension
          ."&chl=".urlencode(json_encode($questionShortObject));

    $coinFilename = "coins/".$id.".json";

    if (!file_exists($coinFilename)) {
      http_response_code(404);
      return "";
    }

    $file = fopen($coinFilename, "r");
    $coinContent = fread($file, filesize($coinFilename));
    fclose($file);

    $coinUrl = "https://chart.googleapis.com/chart?cht=qr&choe=UTF-8"
          ."&chs=".$dimension."x".$dimension
          ."&chl=".urlencode($coinContent);

    $qrCodes = new QRCodeUrls($questionUrl, $coinUrl);
    return $qrCodes->toJson();
  }
  
    /**
   * Function to handle the "/qrcodes2/<id>" and "/qrcodesprint2/<id>" REST-GET calls.
   *
   * It returns a JSON-string in which the google-api qr-code urls for both
   *  - the position
   * are contained in fields with respective names.
   *
   * @param int         $id             The question id.
   * @param int         $dimension      The wanted qr-code dimension.
   *
   * @return string - The answer JSON-string.
   */
  function get_position_qrcode_by_id($id, $dimension) {
    $positionFilename = "positions/".$id.".json";

    if (!file_exists($positionFilename)) {
      http_response_code(404);
      return "";
    }

    $positionShortObject = (object) ['id' => $id, 'type' => DataType::POSITION];

    $positionUrl = "https://chart.googleapis.com/chart?cht=qr&choe=UTF-8"
          ."&chs=".$dimension."x".$dimension
          ."&chl=".urlencode(json_encode($positionShortObject));

    $qrCodes2 = new PositionQRCodeUrl($positionUrl);
    return $qrCodes2->toJson();
  }

  /**
   * Function to handle the "/questioncount" REST-GET call.
   *
   * It returns the count of currently existing questions as an string,
   * representing the integer value of the sum.
   *
   * @return string - The sum of questions.
   */
  function get_question_count() {
    if (!file_exists("questions/")) {
      return "0";
    }

    $iterator = new FilesystemIterator("questions/", FilesystemIterator::SKIP_DOTS);
    return "".iterator_count($iterator);
  }
  
    /**
   * Function to handle the "/locationcount" REST-GET call.
   *
   * It returns the count of currently existing locations as an string,
   * representing the integer value of the sum.
   *
   * @return string - The sum of locations.
   */
  function get_location_count() {
    if (!file_exists("locations/")) {
      return "0";
    }

    $iterator = new FilesystemIterator("locations/", FilesystemIterator::SKIP_DOTS);
    return "".iterator_count($iterator);
  }

  /**
   * Function to handle the "/questions/<id>" REST-DELETE call.
   * It deletes the question with the given id, if existing.
   * The same for the respective coin.
   * Otherwise nothing happens.
   *
   * @param int         $id             The question id.
   *
   * @return string - an empty string.
   */
  function delete_question($id) {
    $questionFilename = "questions/".$id.".json";
    $coinFilename = "coins/".$id.".json";

    if (file_exists($questionFilename)) {
      unlink($questionFilename);
    }

    if (file_exists($coinFilename)) {
      unlink($coinFilename);
    }

    return "";
  }
  
    /**
   * Function to handle the "/positions/<id>" REST-DELETE call.
   * It deletes the position with the given id, if existing.
   * Otherwise nothing happens.
   *
   * @param int         $id             The position id.
   *
   * @return string - an empty string.
   */
  function delete_position($id) {
    $positionFilename = "positions/".$id.".json";

    if (file_exists($positionFilename)) {
      unlink($positionFilename);
    }

    return "";
  }
  
      /**
   * Function to handle the "/things/<id>" REST-DELETE call.
   * It deletes the things with the given id, if existing.
   * Otherwise nothing happens.
   *
   * @param int         $id             The thing id.
   *
   * @return string - an empty string.
   */
  function delete_thing($id) {
    $thingFilename = "things/".$id.".json";

    if (file_exists($thingFilename)) {
      unlink($thingFilename);
    }

    return "";
  }
  
        /**
   * Function to handle the "/locations/<id>" REST-DELETE call.
   * It deletes the locations with the given id, if existing.
   * Otherwise nothing happens.
   *
   * @param int         $id             The location id.
   *
   * @return string - an empty string.
   */
  function delete_location($id) {
    $locationFilename = "locations/".$id.".json";

    if (file_exists($locationFilename)) {
      unlink($locationFilename);
    }

    return "";
  }

  /**
   * Function to handle the "/questions/<id>" REST-GET call.
   * It returns the questions json, if a question with the given id exists.
   * Otherwise it returns an empty 404 HttpMessage.
   *
   * @param int         $id             The question id.
   *
   * @return string - The JSON-string for the question.
   */
  function get_question_by_id($id) {
    $filePath = "questions/".$id.".json";

    if (file_exists($filePath)) {
      $file = fopen($filePath, "r");
      $content = fread($file, filesize($filePath));
      fclose($file);
      return $content;
    }

    http_response_code(404);
    return "";
  }
  
  /**
   * Function to handle the "/positions/<id>" REST-GET call.
   * It returns the positions json, if a position with the given id exists.
   * Otherwise it returns an empty 404 HttpMessage.
   *
   * @param int         $id             The position id.
   *
   * @return string - The JSON-string for the position.
   */
  function get_position_by_id($id) {
    $filePath = "positions/".$id.".json";

    if (file_exists($filePath)) {
      $file = fopen($filePath, "r");
      $content = fread($file, filesize($filePath));
      fclose($file);
      return $content;
    }

    http_response_code(404);
    return "";
  }
  
    /**
   * Function to handle the "/things/<id>" REST-GET call.
   * It returns the things json, if a thing with the given id exists.
   * Otherwise it returns an empty 404 HttpMessage.
   *
   * @param int         $id             The thing id.
   *
   * @return string - The JSON-string for the position.
   */
  function get_thing_by_id($id) {
    $filePath = "things/".$id.".json";

    if (file_exists($filePath)) {
      $file = fopen($filePath, "r");
      $content = fread($file, filesize($filePath));
      fclose($file);
      return $content;
    }

    http_response_code(404);
    return "";
  }
  
      /**
   * Function to handle the "/locations/<id>" REST-GET call.
   * It returns the locations json, if a location with the given id exists.
   * Otherwise it returns an empty 404 HttpMessage.
   *
   * @param int         $id             The location id.
   *
   * @return string - The JSON-string for the location.
   */
  function get_location_by_id($id) {
    $filePath = "locations/".$id.".json";

    if (file_exists($filePath)) {
      $file = fopen($filePath, "r");
      $content = fread($file, filesize($filePath));
      fclose($file);
      return $content;
    }

    http_response_code(404);
    return "";
  }

  /**
   * Function to handle an error.
   * It simply returns an empty 400 HttpMessage.
   *
   * @return void
   */
  function handle_error() {
    http_response_code(400);
    exit("");
  }

  $method = $_SERVER['REQUEST_METHOD'];
  $request = explode("/", substr(@$_SERVER['PATH_INFO'], 1));
  $requestSize = sizeof($request);

  switch ($method) {
    case 'GET':
      switch ($request[0]) {
        case 'questions':
          if ($requestSize == 1) {
            exit(get_questions());
          }

          if ($requestSize == 2) {
            if ($request[1] == "") {
              // special case.
              // http://localhost/cardboard-qr-marker-frontend/api.php/questions/
              exit(get_questions());
            }
            exit(get_question_by_id($request[1]));
          }

          if ($requestSize == 3) {
            if ($request[2] == "") {
              exit(get_question_by_id($request[1]));
            }
          }

          handle_error();
		case 'positions':
          if ($requestSize == 1) {
            exit(get_positions());
          }

          if ($requestSize == 2) {
            if ($request[1] == "") {
              // special case.
              // http://localhost/cardboard-qr-marker-frontend/api.php/positions/
              exit(get_positions());
            }
            exit(get_position_by_id($request[1]));
          }

          if ($requestSize == 3) {
            if ($request[2] == "") {
              exit(get_position_by_id($request[1]));
            }
          }

          handle_error();  
		case 'things':
          if ($requestSize == 1) {
            exit(get_things());
          }

          if ($requestSize == 2) {
            if ($request[1] == "") {
              // special case.
              // http://localhost/cardboard-qr-marker-frontend/api.php/positions/
              exit(get_things());
            }
            exit(get_thing_by_id($request[1]));
          }

          if ($requestSize == 3) {
            if ($request[2] == "") {
              exit(get_thing_by_id($request[1]));
            }
          }

          handle_error(); 
		case 'locations':
          if ($requestSize == 1) {
            exit(get_locations());
          }

          if ($requestSize == 2) {
            if ($request[1] == "") {
              // special case.
              // http://localhost/cardboard-qr-marker-frontend/api.php/positions/
              exit(get_locations());
            }
            exit(get_location_by_id($request[1]));
          }

          if ($requestSize == 3) {
            if ($request[2] == "") {
              exit(get_location_by_id($request[1]));
            }
          }

          handle_error();  
        case 'qrcodes':
          exit(get_qrcodes_by_id($request[1], 200));
        case 'qrcodesprint':
          exit(get_qrcodes_by_id($request[1], 400));
        case 'questioncount':
          exit(get_question_count());
		case 'qrcodes2':
          exit(get_position_qrcode_by_id($request[1], 200));
        case 'qrcodesprint2':
          exit(get_position_qrcode_by_id($request[1], 400));
		case 'locationcount':
          exit(get_location_count());	
        default:
          handle_error();
      }
      break;
    case 'DELETE':
      switch ($request[0]) {
        case 'questions':
          exit(delete_question($request[1]));
		case 'positions':
		  exit(delete_position($request[1]));
		case 'things':
		  exit(delete_thing($request[1]));  
		case 'locations':
		  exit(delete_location($request[1]));
        default:
          handle_error();
      }
      break;
    default:
      handle_error();
      break;
  }
?>
