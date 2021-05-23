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

	function test_addRows(){
		$writer = new CsvWriter();
		$writer->addRows(array(
			array(
				"h1" => "a",
				"h2" => "b",
			),array(
				"h1" => "c",
				"h2" => "d",
			)
		));
		$this->assertEquals("a;b\nc;d\n",$writer->writeToString());
	}

	function test_toString(){
		$writer = new CsvWriter();
		$this->assertEquals("","$writer");

		$writer->addRow(array(
			"k1" => "v1",
			"k2" => "v2"
		));
		$writer->addRow(array(
			"k1" => "v3",
			"k2" => "v4"
		));
		$this->assertEquals("v1;v2\nv3;v4\n","$writer");
	}

	function test_array_access(){
		$writer = new CsvWriter();

		$writer[] = array("k1" => "v1", "k2" => "v2");
		$writer[] = array("k1" => "v2", "k2" => "v3");

		$csv = $this->assertEquals("v1;v2\nv2;v3\n",$writer->writeToString());
	}
}
