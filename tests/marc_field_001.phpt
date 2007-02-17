--TEST--
marc_field_001: Exercise basic methods for File_MARC_Field class
--FILE--
<?php
$dir = dirname(__FILE__);
require 'File/MARC.php';

// create some subfields
$subfields[] = new File_MARC_Subfield('a', 'nothing');
$subfields[] = new File_MARC_Subfield('z', 'everything');

// test constructor
$field = new File_MARC_Data_Field('100', $subfields, '0');

// test basic getter methods
print "Tag: " . $field->getTag() . "\n";
print "Ind1: " . $field->getIndicator(1) . "\n";
print "Ind2: " . $field->getIndicator(2) . "\n";

// test pretty print
print $field;
print "\n";

// test raw print
print $field->toRaw();
?>
--EXPECT--
Tag: 100
Ind1: 0
Ind2:  
100 0  _anothing
       _zeverything
0 anothingzeverything
