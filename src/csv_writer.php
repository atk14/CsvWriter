<?php
class CsvWriter {

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
		$this->rows[] = $row;
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
			"with_header" => false, // true, array("Firstname","Surname")
		);

		$format = $options["format"]; // "csv", "xlsx"

		$rows = $this->rows;
		if($options["with_header"] || $options["with_header"]===array()){
			$header = is_array($options["with_header"]) ? $options["with_header"] : $this->header;
			array_unshift($rows,$header);
		}

		if($format == "csv"){

			$bytes_writen = 0;
			foreach($rows as $row){
				$bw = fputcsv($stream,$row,$options["delimiter"],$options["quote"],$options["escape_char"]);
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
}
