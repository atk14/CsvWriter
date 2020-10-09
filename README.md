CsvWriter
=========

Write CSV into output or a file. Also provides export to XLSX format.

Basic usage
-----------

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

    echo $writer->writeToString();
    //  CAN_G1X;4999
    //  CAN_G15;2099.99

    echo $writer->writeToString(["with_header" => true]);
    //  product_no;price
    //  CAN_G1X;4999
    //  CAN_G15;2099.99

    echo $writer->writeToString(["with_header" => ["Product No.","Price"]]);
    //  "Product No.";Price
    //  CAN_G1X;4999
    //  CAN_G15;2099.99

### XLSX format

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="data.xls"');
    echo $writer->writeToString(["format" => "xlsx"]);

Installation
------------

    composer require atk14/cvs-writer

Testing
-------

    composer update --dev
    ./vendor/bin/run_unit_tests test

[//]: # ( vim: set ts=2 et: )
