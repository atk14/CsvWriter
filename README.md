CsvWriter
=========

[![Build Status](https://travis-ci.org/atk14/CsvWriter.svg?branch=master)](https://travis-ci.org/atk14/CsvWriter)

Write CSV into string output or a file. Also provides export to XLSX format.

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

The header can be added automatically with the "with_header" option set to "auto": 

    $writer = new CsvWriter();
    $writer->addRow(["k1" => "v1", "k2" => "v2"]);
    echo $writer->writeToString(["with_header" => "auto"]);
    // k1;k2
    // v2;v2

    $writer = new CsvWriter();
    $writer->addRow(["v1","v2"]);
    echo $writer->writeToString(["with_header" => "auto"]);
    // v2;v2

CsvWriter implements ArrayAccess for easier rows adding:

    $writer[] = ["k1" => "v1","k2" => "v2"];
    $writer[] = ["k1" => "v3","k2" => "v4"];


### XLSX format

    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=data.xlsx");
    echo $writer->writeToString(["format" => "xlsx"]);

### Export to a file

    $writer->writeToFile("/path/to/a/file.csv",["with_header" => true]);
    $writer->writeToFile("/path/to/a/file.xlsx",["with_header" => true, "format" => "xlsx"]);

Installation
------------

    composer require atk14/cvs-writer

Testing
-------

    composer update --dev
    ./vendor/bin/run_unit_tests test

[//]: # ( vim: set ts=2 et: )
