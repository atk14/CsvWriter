<?php
class CsvWriter implements ArrayAccess {

	protected $default_options;
	protected $rows = array();
	protected $header = array();

	function __construct($options = array()){
		$options += array(
			"delimiter" => ";",
			"quote" => '"',
			"escape_char" => "\\",

			"format" => "csv", // "csv", "xlsx"
		);

		$this->default_options = $options;
	}

	function addRow($row){
		$this->_addRow($row);
	}

	protected function _addRow($row,$offset = null){
		if(is_null($offset)){
			$this->rows[] = $row;
		}else{
			$this->rows[$offset] = $row;
		}
		$this->header = $this->header + array_keys($row);
	}

	function addRows($rows){
		foreach($rows as $row){
			$this->addRow($row);
		}
	}

	/**
	 *
	 *	$writer->writeToString();
	 *	$writer->writeToString(["with_header" => true]);
	 */
	function writeToString($options = array()){
		$stream = fopen("php://temp","r+");
		$bytes_writen = $this->_writeToStream($stream,$options);
		rewind($stream);
		if($bytes_writen === 0){
			return "";
		}
		$out = fread($stream,$bytes_writen);

		return $out;
	}

	/**
	 *
	 *	$writer->writeToFile("/path/to/file.csv");
	 *	$writer->writeToFile("/path/to/file.csv",["with_header" => true]);
	 */
	function writeToFile($filename,$options = array()){
		$stream = fopen($filename,"w");
		$bytes_writen = $this->_writeToStream($stream,$options);
		fclose($stream);
	}

	protected function _writeToStream($stream,$options){
		$options += $this->default_options;
		$options += array(
			"with_header" => false, // true, false, "auto", array("Firstname","Surname")
		);

		if($options["with_header"] === "auto"){
			$options["with_header"] = $this->_isAssoc(array_combine($this->header,$this->header));
		}

		$format = $options["format"]; // "csv", "xlsx"

		$rows = $this->rows;
		if($options["with_header"] || $options["with_header"]===array()){
			$header = is_array($options["with_header"]) ? $options["with_header"] : $this->header;
			if($header || $rows){ // do not array_unshift() when $header==[] and $rows==[]
				array_unshift($rows,$header);
			}
		}

		if($format == "csv"){

			$bytes_writen = 0;
			foreach($rows as $row){
				if(PHP_MAJOR_VERSION==5 && (PHP_MINOR_VERSION<5 || (PHP_MINOR_VERSION==5 && PHP_RELEASE_VERSION<=4))){
					$bw = fputcsv($stream,$row,$options["delimiter"],$options["quote"]); // The escape_char parameter was added in PHP 5.5.4
				}else{
					$bw = fputcsv($stream,$row,$options["delimiter"],$options["quote"],$options["escape_char"]);
				}
				$bytes_writen += $bw;
			}

		}elseif($format == "xlsx"){

			$wExcel = new Ellumilel\ExcelWriter();
			$wExcel->writeSheet($rows,"Sheet 1");
			$src = $wExcel->writeToString();
			fwrite($stream,$src,strlen($src));
			$bytes_writen = strlen($src);

		}else{

			throw new Exception("CsvWriter: Invalid format: \"$format\" (expected \"csv\" or \"xlsx\")");
		}

		return $bytes_writen;
	}

	protected function _isAssoc($ary){
		if (array() === $ary) return false;
		return array_keys($ary) !== range(0, count($ary) - 1);
	}

	function toString(){
		return (string)$this->writeToString(array("with_header" => "auto"));
	}

	function __toString(){
		return $this->toString();
	}

	// -- ArrayAccess

	function offsetSet($offset,$value){
		$this->_addRow($value,$offset);
	}

	function offsetExists($offset){
		return isset($this->rows[$offset]);
	}

	function offsetUnset($offset){
		unset($this->rows[$offset]);
	}

	function offsetGet($offset){
		return isset($this->rows[$offset]) ? $this->rows[$offset] : null;
	}
}
