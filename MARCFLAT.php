<?php

/* vim: set expandtab shiftwidth=4 tabstop=4 softtabstop=4 foldmethod=marker: */

/**
 * Parser for MARC records
 *
 * This package is based on the PHP MARC package, originally called "php-marc",
 * that is part of the Emilda Project (http://www.emilda.org). Christoffer
 * Landtman generously agreed to make the "php-marc" code available under the
 * GNU LGPL so it could be used as the basis of this PEAR package.
 *
 * PHP version 5
 *
 * LICENSE: This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation; either version 2.1 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category  File_Formats
 * @package   File_MARC
 * @author    Christoffer Landtman <landtman@realnode.com>
 * @author    Andrew Nagy <andrew.nagy@villanova.edu>
 * @copyright 2007-2008 Oy Realnode Ab, Andrew Nagy
 * @license   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/File_MARC
 */

require_once 'PEAR/Exception.php';
require_once 'File/MARC.php';
require_once 'File/MARC/Record.php';
require_once 'File/MARC/Field.php';
require_once 'File/MARC/Control_Field.php';
require_once 'File/MARC/Data_Field.php';
require_once 'File/MARC/Subfield.php';
require_once 'File/MARC/Exception.php';
require_once 'File/MARC/List.php';

// {{{ class File_MARCFlat
/**
 * The main File_MARCFlat class enables you to return File_MARC_Record
 * objects from a stream or string of flat text MARC data.
 *
 * @category File_Formats
 * @package  File_MARC
 * @author   Andrew Nagy <andrew.nagy@villanova.edu>
 * @license  http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @link     http://pear.php.net/package/File_MARC
 */
class File_MARCFlat
{

    // {{{ constants

    /**
     * MARC records retrieved from a file
     */
    const SOURCE_FILE = 1;

    /**
     * MARC records retrieved from a binary string
     */
    const SOURCE_STRING = 2;
    // }}}

    // {{{ properties
    /**
     * Source containing raw records
     *
     * @var resource
     */
    protected $source;

    /**
     * Source type (SOURCE_FILE or SOURCE_STRING)
     *
     * @var int
     */
    protected $type;

    /**
     * Counter for MARCXML records in a collection
     *
     * @var int
     */
    protected $counter;
    // }}}

    // {{{ Constructor: function __construct()
    /**
     * Read in MARC records
     *
     * This function reads in files or strings that
     * contain one or more MARC records.
     *
     * <code>
     * // Retrieve MARC records from a file
     * $journals = new File_MARCFLAT('journals.mrc', SOURCE_FILE);
     *
     * // Retrieve MARC records from a string (e.g. Z39 query results)
     * $monographs = new File_MARCFLAT($raw_marc, SOURCE_STRING);
     * </code>
     *
     * @param string $source Name of the file, or a MARC string
     * @param int    $type   Source of the input, either SOURCE_FILE or SOURCE_STRING
     */
    function __construct($source, $type = self::SOURCE_FILE)
    {
        $this->counter = 0;

        switch ($type) {
        case self::SOURCE_FILE:
            $this->type = self::SOURCE_FILE;
            if (is_readable($source)) {
                $this->source = fopen($source, 'rb');
            } else {
                 $errorMessage = File_MARC_Exception::formatError(File_MARC_Exception::$messages[File_MARC_Exception::ERROR_INVALID_FILE], array('filename' => $source));
                 throw new File_MARC_Exception($errorMessage, File_MARC_Exception::ERROR_INVALID_FILE);
            }
            break;

        case self::SOURCE_STRING:
            $this->type = self::SOURCE_STRING;
            $this->source = explode(File_MARC::END_OF_RECORD, $source);
            break;

        default:
             throw new File_MARC_Exception(File_MARC_Exception::$messages[File_MARC_Exception::ERROR_INVALID_SOURCE], File_MARC_Exception::ERROR_INVALID_SOURCE);
        }

        if (!$this->source) {
            $errorMessage = File_MARC_Exception::formatError(File_MARC_Exception::$messages[File_MARC_Exception::ERROR_INVALID_FILE], array('filename' => $source));
            throw new File_MARC_Exception($errorMessage, File_MARC_Exception::ERROR_INVALID_FILE);
        }
    }
    // }}}

    // {{{ nextRaw()
    /**
     * Return the next raw MARC record
     *
     * Returns the next raw MARC record, unless all records already have
     * been read.
     *
     * @return string Either a raw record or false
     */
    function nextRaw()
    {
        if ($this->type == self::SOURCE_FILE) {
            $record = stream_get_line($this->source,
                                      File_MARC::MAX_RECORD_LENGTH,
                                      File_MARC::END_OF_RECORD);

            // Removes new line, carriage return, null from records
            $record = preg_replace('/^[\\x0a\\x0d\\x00]+/', '', $record);
        } elseif ($this->type == self::SOURCE_STRING) {
            $record = array_shift($this->source);
        }

        // Exit if we are at the end of the file
        if (!$record) {
            return false;
        }

        return $record;
    }
    // }}}

    // {{{ next()
    /**
     * Return next {@link File_MARC_Record} object
     *
     * Decodes the next raw MARC record and returns the {@link File_MARC_Record}
     * object.
     * <code>
     * <?php
     * // Retrieve a set of MARC records from a file
     * $journals = new File_MARC('journals.mrc', SOURCE_FILE);
     *
     * // Iterate through the retrieved records
     * while ($record = $journals->next()) {
     *     print $record;
     *     print "\n";
     * }
     *
     * ?>
     * </code>
     *
     * @return File_MARC_Record next record, or false if there are
     * no more records
     */
    function next()
    {
        $raw = $this->nextRaw();
        if ($raw) {
            return $this->_decode($raw);
        } else {
            return false;
        }
    }
    // }}}

    // {{{ _decode()
    /**
     * Decode a given raw MARC record
     *
     * @param string $text Raw MARC record
     *
     * @return File_MARC_Record Decoded File_MARC_Record object
     */
    private function _decode($text)
    {
        $marc = new File_MARC_Record();

        // Store leader
        $marc->setLeader(substr($text, 0, File_MARC::LEADER_LEN));
        
        // Process Fields
        $marcArray = explode("\n", substr(trim($text), strpos($text, "\n")+1));
        foreach ($marcArray as $line) {
            $tag = substr($line, 0, 3);
            if (substr($line, 0, 2) == "00") {
                // Control Field
                $marc->appendField(new File_MARC_Control_Field($tag,
                                                               substr($line, 4)));
            } else {
                // Data Field
                $ind1 = substr($line, 4, 1);
                $ind2 = substr($line, 5, 1);

                $subfields = preg_split('/\$([a-z0-9])/', substr($line, 5), -1,
                                        PREG_SPLIT_DELIM_CAPTURE);
                $subfield_data = array();
                for ($i = 1; $i < count($subfields); $i=$i+2) {
                    $subfield_data[] = new File_MARC_Subfield($subfields[$i],
                                                              $subfields[$i+1]);
                }
                $marc->appendField(new File_MARC_Data_Field($tag,
                                                            $subfield_data,
                                                            $ind1, $ind2));
            }
        }

        return $marc;
    }
    // }}}

}
// }}}
