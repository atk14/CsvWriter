<?php
class TcCsvWriter extends TcBase {

	function test(){
		$writer = new CsvWriter([
			"delimiter" => ";",
			"quote" => '"',
			"escape_char" => "\\",
		]);

		$writer->addRow([
			"product_no" => "CAN_G1X",
			"price" => 4999.0,
		]);

		$writer->addRow([
			"product_no" => "CAN_G15",
			"price" => 2099.99,
		]);

		$csv = $writer->writeToString();
		$this->assertEquals("CAN_G1X;4999\nCAN_G15;2099.99\n",$csv);

		$csv = $writer->writeToString(array("delimiter" => ","));
		$this->assertEquals("CAN_G1X,4999\nCAN_G15,2099.99\n",$csv);

		$csv = $writer->writeToString(array("with_header" => true));
		$this->assertEquals("product_no;price\nCAN_G1X;4999\nCAN_G15;2099.99\n",$csv);

		$csv = $writer->writeToString(array("with_header" => array("Product No.","Price")));
		$this->assertEquals("\"Product No.\";Price\nCAN_G1X;4999\nCAN_G15;2099.99\n",$csv);

		$filename = __DIR__ . "/temp/output.csv";
		$writer->writeToFile($filename,array("delimiter" => "|", "with_header" => array("Product No.","Price")));
		$this->assertEquals("\"Product No.\"|Price\nCAN_G1X|4999\nCAN_G15|2099.99\n",file_get_contents($filename));
		$this->assertEquals("text/csv",Files::DetermineFileType($filename));

		// xlsx

		$xlsx = $writer->writeToString(array("format" => "xlsx"));
		$this->assertTrue(strlen($xlsx)>0);
		$filename = Files::WriteToTemp($xlsx);
		$this->assertEquals("application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",Files::DetermineFileType($filename,array("original_filename" => "data.xlsx")));

		$filename = __DIR__ . "/temp/output.xlsx";
		$writer->writeToFile($filename,array("with_header" => true, "format" => "xlsx"));
		$this->assertEquals("application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",Files::DetermineFileType($filename));
	}
}
