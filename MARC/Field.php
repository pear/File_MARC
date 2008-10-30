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
 * @author    Dan Scott <dscott@laurentian.ca>
 * @copyright 2003-2008 Oy Realnode Ab, Dan Scott
 * @license   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/File_MARC
 */

// {{{ class File_MARC_Field extends Structures_LinkedList_DoubleNode
/**
 * The File_MARC_Field class is expected to be extended to reflect the
 * requirements of control and data fields.
 *
 * Every MARC field contains a tag name.
 *
 * @category File_Formats
 * @package  File_MARC
 * @author   Christoffer Landtman <landtman@realnode.com>
 * @author   Dan Scott <dscott@laurentian.ca>
 * @license  http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @link     http://pear.php.net/package/File_MARC
 */
class File_MARC_Field extends Structures_LinkedList_DoubleNode
{

    // {{{ properties
    /**
     * The tag name of the Field
     * @var string
     */
    protected $tag;
    // }}}

    // {{{ Constructor: function __construct()
    /**
     * File_MARC_Field constructor
     *
     * Create a new {@link File_MARC_Field} object from passed arguments. We
     * define placeholders for the arguments required by child classes.
     *
     * @param string $tag       tag
     * @param string $subfields placeholder for subfields or control data
     * @param string $ind1      placeholder for first indicator
     * @param string $ind2      placeholder for second indicator
     */
    function __construct($tag, $subfields = null, $ind1 = null, $ind2 = null) 
    {
        $this->tag = $tag;

        // Check if valid tag
        if (!preg_match("/^[0-9A-Za-z]{3}$/", $tag)) {
             $errorMessage = File_MARC_Exception::formatError(File_MARC_Exception::$messages[File_MARC_Exception::ERROR_INVALID_TAG], array("tag" => $tag));
             throw new File_MARC_Exception($errorMessage, File_MARC_Exception::ERROR_INVALID_TAG);
        }

    }
    // }}}

    // {{{ Destructor: function __destruct()
    /**
     * Destroys the data field
     */
    function __destruct()
    {
        $this->tag = null;
        parent::__destruct();
    }
    // }}}

    // {{{ getTag()
    /**
     * Returns the tag for this {@link File_MARC_Field} object
     *
     * @return string returns the tag number of the field
     */
    function getTag()
    {
        return $this->tag;
    }
    // }}}

    // {{{ setTag()
    /**
     * Sets the tag for this {@link File_MARC_Field} object
     *
     * @param string $tag new value for the tag
     *
     * @return string returns the tag number of the field
     */
    function setTag($tag)
    {
        $this->tag = $tag;
        return $this->getTag();
    }
    // }}}

    // {{{ isEmpty()
    /**
     * Is empty
     *
     * Checks if the field is empty.
     *
     * @return bool Returns true if the field is empty, otherwise false
     */
    function isEmpty()
    {
        if ($this->getTag()) {
            return false;
        }
        // It is empty
        return true;
    }
    // }}}

    /**
     * ========== OUTPUT METHODS ==========
     */

    // {{{ __toString()
    /**
     * Return Field formatted
     *
     * Return Field as a formatted string.
     *
     * @return string Formatted output of Field
     */
    function __toString()
    {
        return $this->getTag();
    }
    // }}}

    // {{{ toRaw()
    /**
     * Return field in raw MARC format (stub)
     *
     * Return the field formatted in raw MARC for saving into MARC files. This
     * stub method is extended by the child classes.
     *
     * @return bool Raw MARC
     */
    function toRaw()
    {
        return false;
    }
    // }}}
}
// }}}

