Change Log
==========

All notable changes to the CsvWriter will be documented in this file.

## [1.2.1] - 2023-12-11

* 14a8581 - PHP 8.1 issues

## [1.2] - 2021-08-02

- The BOM is written at the beginning of the CSV export; it can be suppressed with option write_bom set to false
- Added option sheet_name for xlsx export (the default is "Sheet 1")

## [1.1.2] - 2021-05-31

- Writing empty CSV fixed

## [1.1.1] - 2021-05-23

- Header can be added automatically with the option "with_header" set to "auto"
- The option "with_header" is set to "auto" in methods  CsvWriter::toString() and CsvWriter::__toString()

## [1.1] - 2021-05-23

- Added methods CsvWriter::toString() and CsvWriter::__toString()
- CsvWriter implements ArrayAccess, so it's ok to call ```$csv_writer[] = ["v1","v2"];```

## [1.0.2] - 2020-11-15

Project is compatible with PHP 8

## [1.0.1] - 2020-10-10

CsvWriter was tagged as compatible with PHP>=5.4

## [1.0] - 2020-10-10

First tagged release
