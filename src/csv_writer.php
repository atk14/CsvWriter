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
		$bytes_writen = $this->_writeToString($stream,$options);
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
		$bytes_writen = $this->_writeToString($stream,$options);
		fclose($stream);
	}

	protected function _writeToString($stream,$options){
		$options += $this->default_options;
		$options += array(
			"with_header" => false,
		);

		$bytes_writen = 0;
		if($options["with_header"] || $options["with_header"]===array()){
			$header = is_array($options["with_header"]) ? $options["with_header"] : $this->header;
			$bw = fputcsv($stream,$header,$options["delimiter"],$options["quote"],$options["escape_char"]);
			$bytes_writen += $bw;
		}
		foreach($this->rows as $row){
			$bw = fputcsv($stream,$row,$options["delimiter"],$options["quote"],$options["escape_char"]);
			$bytes_writen += $bw;
		}

		return $bytes_writen;
	}
}
