<?php
class TcCsvWriter extends TcBase {

	function test(){
		$writer = new CsvWriter([
			"delimiter" => ";",
			"quote" => '"',
			"escape_char" => "\\",
			"write_bom" => false,
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
		unlink($filename);

		$filename = __DIR__ . "/temp/output.xlsx";
		$writer->writeToFile($filename,array("with_header" => true, "format" => "xlsx"));
		$this->assertEquals("application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",Files::DetermineFileType($filename));
		unlink($filename);
	}

	function test_empty_csv(){
		$writer = new CsvWriter(["write_bom" => false]);

		$filename = Files::GetTempFilename();

		$writer->writeToFile($filename);
		$this->assertTrue(file_exists($filename));
		$this->assertEquals(0,filesize($filename));
		unlink($filename);

		$this->assertEquals("",$writer->writeToString());

		// --

		$writer->writeToFile($filename,array("with_header" => true));
		$this->assertTrue(file_exists($filename));
		$this->assertEquals(0,filesize($filename));
		unlink($filename);

		$this->assertEquals("",$writer->writeToString(array("with_header" => true)));

		// --

		$writer->writeToFile($filename,array("with_header" => array("k1","k2")));
		$this->assertTrue(file_exists($filename));
		$this->assertEquals(6,filesize($filename));
		unlink($filename);

		$this->assertEquals("k1;k2\n",$writer->writeToString(array("with_header" => array("k1","k2"))));
	}

	function test_automatic_header(){
		$writer = new CsvWriter(["write_bom" => false]);
		$writer->addRow(array("k1" => "v1","k2" => "v2"));
		$this->assertEquals("k1;k2\nv1;v2\n",$writer->writeToString(array("with_header" => "auto")));

		$writer = new CsvWriter(["write_bom" => false]);
		$writer->addRow(array("v1","v2"));
		$this->assertEquals("v1;v2\n",$writer->writeToString(array("with_header" => "auto")));

	}

	function test_addRows(){
		$writer = new CsvWriter(["write_bom" => false]);
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
		$writer = new CsvWriter(["write_bom" => false]);
		$this->assertEquals("","$writer");

		$writer = new CsvWriter(["write_bom" => false]);
		$writer->addRow(array(
			"k1" => "v1",
			"k2" => "v2"
		));
		$writer->addRow(array(
			"k1" => "v3",
			"k2" => "v4"
		));
		$this->assertEquals("k1;k2\nv1;v2\nv3;v4\n","$writer");

		$writer = new CsvWriter(["write_bom" => false]);
		$writer->addRow(array("v1","v2"));
		$writer->addRow(array("v3","v4"));
		$this->assertEquals("v1;v2\nv3;v4\n","$writer");
	}

	function test_array_access(){
		$writer = new CsvWriter(["write_bom" => false]);

		$writer[] = array("k1" => "v1", "k2" => "v2");
		$writer[] = array("k1" => "v2", "k2" => "v3");

		$csv = $this->assertEquals("v1;v2\nv2;v3\n",$writer->writeToString());
	}

	function test_write_bom(){
		$writer = new CsvWriter(["write_bom" => true]);
		$writer[] = ["a","b"];
		$csv = $writer->writeToString();
		$this->assertEquals("\xEF\xBB\xBFa;b\n",$csv);

		$writer = new CsvWriter();
		$writer[] = ["c","d"];
		$csv = $writer->writeToString();
		$this->assertEquals("\xEF\xBB\xBFc;d\n",$csv);
		//
		$csv = $writer->writeToString(["write_bom" => false]);
		$this->assertEquals("c;d\n",$csv);
	}
}
