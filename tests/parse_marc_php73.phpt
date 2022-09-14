--TEST--
On php 7.3 the is no error
--SKIPIF--
<?php include('tests/skipif.inc'); ?>
--FILE--
<?php
$dir = dirname(__FILE__);
require __DIR__ . '/bootstrap.php';
$marc_file = new File_MARC($dir . '/' . 'bad_example2.mrc');

while ($marc_record = $marc_file->next()) {
  print $marc_record;
  print "";
}
?>
--EXPECT--
Warning: unpack(): Type A: not enough input, need 4, have 2 in /var/www/File/MARC.php on line 304

Warning: unpack(): Type A: not enough input, need 4, have 2 in /var/www/File/MARC.php on line 305
LDR 015970028922450001000080
