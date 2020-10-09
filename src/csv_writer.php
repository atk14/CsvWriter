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

	function exportCsv($options = array()){
		$options += $this->default_options;
		$options += array(
			"with_header" => false,
		);

		$filename = "php://temp";

		$stream = fopen($filename,"r+");
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
		rewind($stream);


		$out = fread($stream,$bytes_writen);

		return $out;
	}
}
