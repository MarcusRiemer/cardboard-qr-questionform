<?php
  /**
    * Enumeration-Class to describe the type of the data.
    */
  abstract class DataType
  {
      const COIN  = 0;
      const QUESTION = 1;
	  const POSITION = 2;
	  const THING = 3;
	  const LOCATION = 4;
  }

  /**
   * Get the next available question id on the file-system.
   *
   * The id-range is [0 - MAX_INT].
   * This function does not fill id-gaps, it looks for the highest existing id
   * and takes the next.
   *
   * @return int - the next available id.
   */
  function get_new_id($type) {
	if ($type == DataType::QUESTION) {
		if (!file_exists("questions/")) {
		return 0;
		}

		$files = array();
		$iterator = new FilesystemIterator("questions/", FilesystemIterator::SKIP_DOTS);
	}
	if	($type == DataType::POSITION) {
		if (!file_exists("positions/")) {
		return 0;
		}

		$files = array();
		$iterator = new FilesystemIterator("positions/", FilesystemIterator::SKIP_DOTS);
	}
	if	($type == DataType::THING) {
		if (!file_exists("things/")) {
		return 0;
		}

		$files = array();
		$iterator = new FilesystemIterator("things/", FilesystemIterator::SKIP_DOTS);
	}
	if	($type == DataType::LOCATION) {
		if (!file_exists("locations/")) {
		return 0;
		}

		$files = array();
		$iterator = new FilesystemIterator("locations/", FilesystemIterator::SKIP_DOTS);
	}

    while($iterator->valid()) {
      array_push($files, $iterator->getFileName());
      $iterator->next();
    }

    if (sizeof($files) == 0) {
      return 0;
    }

    natsort($files);

    $lastFile = end($files);
    $name = explode(".", $lastFile);

    return intval($name[0]) + 1;
  }

  /**
   * Abstract class to hold abstract data information.
   *
   * It implements the JsonSerializable-interface.
   */
  abstract class Data implements JsonSerializable {
    protected $id;
    protected $type;

    abstract public function jsonSerialize();

    abstract public function toJson();

    public function getId() {
      return $this->id;
    }

    public function setId($id) {
      $this->id = $id;
    }

    /**
     * Returns the directory-path, in which the files of this data-type will be stored.
     *
     * @return string - The directory-path.
     */
    protected abstract function getPath();

    /**
     * Saves the data-object to a file.
     *
     * The content of the file will be the JSON, describing the content of the
     * data-object. The filename will be <id>.json.
     *
     * For this function to work, php needs to have write-access on the
     * servers file-system folder.
     *
     * @return void
     */
    public function saveToFile($type) {
      // open questions file
      if (!file_exists($this->getPath())) {
          mkdir($this->getPath(), 0777, true);
      }

		if ($this->getId() === "") {
			$this->setId(get_new_id($type));
		} 

      $fileName = $this->getPath() . $this->getId() . ".json";

      // overwrite file with write access
      $dataFile = fopen($fileName, "w");

      // write new questions
      fwrite($dataFile, $this->toJson());

      // close file
      fclose($dataFile);
    }
  }
 /**
   * Concrete location-data class
   */
   class Location extends Data {
    private $location;
	
	    /**
     * Constructor for the location class.
     *
     * @param int       	$id            	The location id.
	 * @param string    	$location      	name of the location
     *
     * @return Location
     */
		public function __construct($id, $location) {
		$this->id = $id;
		$this->location = $location;
		$this->type = DataType::LOCATION;
		}

		public function jsonSerialize() {
		return get_object_vars($this);
		}

		public function toJson() {
		return json_encode($this);
		}

		protected function getPath() {
		return "locations/";
		}
	}
  /**
   * Concrete thing-data class
   */
   class Thing extends Data {
    private $thing;
	private $start;
	private $location;
	
	    /**
     * Constructor for the thing class.
     *
     * @param int       	$id            	The thing id.
	 * @param string		$thing			describtion of the thing
     * @param int    		$start      	starttime of the thing in minutes till midnight
	 * @param string    	$location      	location of the thing
     *
     * @return Thing
     */
		public function __construct($id, $thing, $start, $location) {
		$this->id = $id;
		$this->thing = $thing;
		$this->start = $start;
		$this->location = $location;
		$this->type = DataType::THING;
		}

		public function jsonSerialize() {
		return get_object_vars($this);
		}

		public function toJson() {
		return json_encode($this);
		}

		protected function getPath() {
		return "things/";
		}
	}
  /**
   * Concrete position-data class
   */
   class Position extends Data {
    private $position;
	private $arrows;
	
	    /**
     * Constructor for the position class.
     *
     * @param int       	$id            	The position id.
	 * @param string		$position		describtion of the position
     * @param [string]    	$arrows      	An array of arrowfiles.
     *
     * @return Position
     */
		public function __construct($id, $position, $arrows) {
		$this->id = $id;
		$this->position = $position;
		$this->arrows = $arrows;
		$this->type = DataType::POSITION;
		}

		public function jsonSerialize() {
		return get_object_vars($this);
		}

		public function toJson() {
		return json_encode($this);
		}

		protected function getPath() {
		return "positions/";
		}
	}
  /**
   * Concrete question-data class.
   */
  class Question extends Data {
    private $question;
    private $answers;
    private $correctAnswer;

    /**
     * Constructor for the question class.
     *
     * @param int         $id             The question id.
     * @param string      $question       The question-text.
     * @param [string]    $answers        An array of possible multiple-choice answers.
     * @param int         $correctAnswer  The index of the correct answer in the answers-array.
     *
     * @return Question
     */
    public function __construct($id, $question, $answers, $correctAnswer) {
      $this->id = $id;
      $this->question = $question;
      $this->answers = $answers;
      $this->correctAnswer = $correctAnswer;
      $this->type = DataType::QUESTION;
    }

    public function jsonSerialize() {
      return get_object_vars($this);
    }

    public function toJson() {
      return json_encode($this);
    }

    protected function getPath() {
      return "questions/";
    }
  }

  /**
   * Concrete coin-data class.
   */
  class Coin extends Data {

    /**
     * Constructor for the coin class.
     *
     * The coin-id is logically linked to the question-id.
     * The question with the equivalent id "unlocks" the respective coin in the
     * FH Wedel QR-Schnitzeljagd application.
     *
     * @param int         $id             The coin id.
     *
     * @return Coin
     */
    public function __construct($id) {
      $this->id = $id;
      $this->type = DataType::COIN;
    }

    public function jsonSerialize() {
      return get_object_vars($this);
    }

    public function toJson() {
      return json_encode($this);
    }

    protected function getPath() {
      return "coins/";
    }
  }
?>
