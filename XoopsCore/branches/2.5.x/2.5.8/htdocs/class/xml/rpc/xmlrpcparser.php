<?php
// $Id$
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.myweb.ne.jp/, http://www.xoops.org/, http://jp.xoops.org/ //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //
defined("XOOPS_ROOT_PATH") || die("XOOPS root path not defined");
require_once XOOPS_ROOT_PATH.'/class/xml/saxparser.php';
require_once XOOPS_ROOT_PATH.'/class/xml/xmltaghandler.php';

/**
* Class RSS Parser
*
* This class offers methods to parse RSS Files
*
* @link      http://www.xoops.org/ Latest release of this class
* @package   class
* @copyright Copyright (c) 2001 xoops.org. All rights reserved.
* @author    Kazumi Ono <onokazu@xoops.org>
* @version   $Id$
* @access    public
*/

class XoopsXmlRpcParser extends SaxParser
{

    /**
    * @access private
    * @var    array
    */
    var $_param;

    /**
    * @access private
    * @var    string
    */
    var $_methodName;

    /**
    * @access private
    * @var    array
    */
    var $_tempName;

    /**
    * @access private
    * @var    array
    */
    var $_tempValue;

    /**
     * @access private
    * @var    array
    */
    var $_tempMember;

    /**
    * @access private
    * @var    array
    */
    var $_tempStruct;

    /**
    * @access private
    * @var    array
    */
    var $_tempArray;

    /**
    * @access private
    * @var    array
    */
    var $_workingLevel = array();

    /**
    * Constructor of the class
    * @access
    * @author
    * @see
    */
    function XoopsXmlRpcParser(&$input)
    {
        $this->SaxParser($input);
        $this->addTagHandler(new RpcMethodNameHandler());
        $this->addTagHandler(new RpcIntHandler());
        $this->addTagHandler(new RpcDoubleHandler());
        $this->addTagHandler(new RpcBooleanHandler());
        $this->addTagHandler(new RpcStringHandler());
        $this->addTagHandler(new RpcDateTimeHandler());
        $this->addTagHandler(new RpcBase64Handler());
        $this->addTagHandler(new RpcNameHandler());
        $this->addTagHandler(new RpcValueHandler());
        $this->addTagHandler(new RpcMemberHandler());
        $this->addTagHandler(new RpcStructHandler());
        $this->addTagHandler(new RpcArrayHandler());
    }

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @param $name
     *
     * @return void
     */
    function setTempName($name)
    {
        $this->_tempName[$this->getWorkingLevel()] = $name;
    }

    function getTempName()
    {
        return $this->_tempName[$this->getWorkingLevel()];
    }


    /**
     * @param $value
     */
    function setTempValue($value)
    {
        if (is_array($value)) {
            settype($this->_tempValue, 'array');
            foreach ($value as $k => $v) {
                $this->_tempValue[$k] = $v;
            }
        } elseif (is_string($value)) {
            if (isset($this->_tempValue)) {
                if (is_string($this->_tempValue)) {
                    $this->_tempValue .= $value;
                }
            } else {
                $this->_tempValue = $value;
            }
        } else {
            $this->_tempValue = $value;
        }
    }

    /**
     * @return array
     */
    function getTempValue()
    {
        return $this->_tempValue;
    }

/
    function resetTempValue()
    {
        unset($this->_tempValue);
    }

    /**
     * @param $name
     * @param $value
     */
    function setTempMember($name, $value)
    {
        $this->_tempMember[$this->getWorkingLevel()][$name] = $value;
    }

    function getTempMember()
    {
        return $this->_tempMember[$this->getWorkingLevel()];
    }

    function resetTempMember()
    {
        $this->_tempMember[$this->getCurrentLevel()] = array();
    }


    function setWorkingLevel()
    {
        array_push($this->_workingLevel, $this->getCurrentLevel());
    }


    function getWorkingLevel()
    {
        return $this->_workingLevel[count($this->_workingLevel) - 1];
    }


    function releaseWorkingLevel()
    {
        array_pop($this->_workingLevel);
    }


    /**
     * @param $member
     */
    function setTempStruct($member)
    {
        $key = key($member);
        $this->_tempStruct[$this->getWorkingLevel()][$key] = $member[$key];
    }


    function getTempStruct()
    {
        return $this->_tempStruct[$this->getWorkingLevel()];
    }


    function resetTempStruct()
    {
        $this->_tempStruct[$this->getCurrentLevel()] = array();
    }


    /**
     * @param $value
     */
    function setTempArray($value)
    {
        $this->_tempArray[$this->getWorkingLevel()][] = $value;
    }


    function getTempArray()
    {
        return $this->_tempArray[$this->getWorkingLevel()];
    }

    function resetTempArray()
    {
        $this->_tempArray[$this->getCurrentLevel()] = array();
    }


    /**
     * @param $methodName
     */
    function setMethodName($methodName)
    {
        $this->_methodName = $methodName;
    }


    /**
     * @return string
     */
    function getMethodName()
    {
        return $this->_methodName;
    }


    /**
     * @param $value
     */
    function setParam($value)
    {
        $this->_param[] = $value;
    }


    /**
     * @return array
     */
    function &getParam()
    {
        return $this->_param;
    }
}

/**
 * Class RpcMethodNameHandler
 */
class RpcMethodNameHandler extends XmlTagHandler
{


    /**
     * @return string
     */
    function getName()
    {
        return 'methodName';
    }


    /**
     * @param $parser
     * @param $data
     */
    function handleCharacterData(&$parser, &$data)
    {
        $parser->setMethodName($data);
    }
}

/**
 * Class RpcIntHandler
 */
class RpcIntHandler extends XmlTagHandler
{

    /**
     * @return array
     */
    function getName()
    {
        return array('int', 'i4');
    }

    /**
     * @param $parser
     * @param $data
     */
    function handleCharacterData(&$parser, &$data)
    {
        $parser->setTempValue(intval($data));
    }
}

/**
 * Class RpcDoubleHandler
 */
class RpcDoubleHandler extends XmlTagHandler
{

    /**
     * @return string
     */
    function getName()
    {
        return 'double';
    }

    /**
     * @param $parser
     * @param $data
     */
    function handleCharacterData(&$parser, &$data)
    {
        $data = (float) $data;
        $parser->setTempValue($data);
    }
}

/**
 * Class RpcBooleanHandler
 */
class RpcBooleanHandler extends XmlTagHandler
{

    /**
     * @return string
     */
    function getName()
    {
        return 'boolean';
    }

    /**
     * @param $parser
     * @param $data
     */
    function handleCharacterData(&$parser, &$data)
    {
        $data = (boolean) $data;
        $parser->setTempValue($data);
    }
}

/**
 * Class RpcStringHandler
 */
class RpcStringHandler extends XmlTagHandler
{

    /**
     * @return string
     */
    function getName()
    {
        return 'string';
    }

    /**
     * @param $parser
     * @param $data
     */
    function handleCharacterData(&$parser, &$data)
    {
        $parser->setTempValue(strval($data));
    }
}

/**
 * Class RpcDateTimeHandler
 */
class RpcDateTimeHandler extends XmlTagHandler
{

    /**
     * @return string
     */
    function getName()
    {
        return 'dateTime.iso8601';
    }

    /**
     * @param $parser
     * @param $data
     */
    function handleCharacterData(&$parser, &$data)
    {
        $matches = array();
        if (!preg_match("/^([0-9]{4})([0-9]{2})([0-9]{2})T([0-9]{2}):([0-9]{2}):([0-9]{2})$/", $data, $matches)) {
            $parser->setTempValue(time());
        } else {
            $parser->setTempValue(gmmktime($matches[4], $matches[5], $matches[6], $matches[2], $matches[3], $matches[1]));
        }
    }
}

/**
 * Class RpcBase64Handler
 */
class RpcBase64Handler extends XmlTagHandler
{

    /**
     * @return string
     */
    function getName()
    {
        return 'base64';
    }

    /**
     * @param $parser
     * @param $data
     */
    function handleCharacterData(&$parser, &$data)
    {
        $parser->setTempValue(base64_decode($data));
    }
}

/**
 * Class RpcNameHandler
 */
class RpcNameHandler extends XmlTagHandler
{

    /**
     * @return string
     */
    function getName()
    {
        return 'name';
    }

    /**
     * @param $parser
     * @param $data
     */
    function handleCharacterData(&$parser, &$data)
    {
        switch ($parser->getParentTag()) {
        case 'member':
            $parser->setTempName($data);
            break;
        default:
            break;
        }
    }
}

/**
 * Class RpcValueHandler
 */
class RpcValueHandler extends XmlTagHandler
{

    /**
     * @return string
     */
    function getName()
    {
        return 'value';
    }


    /**
     * @param $parser
     * @param $data
     */
    function handleCharacterData(&$parser, &$data)
    {
        switch ($parser->getParentTag()) {
        case 'member':
            $parser->setTempValue($data);
            break;
        case 'data':
        case 'array':
            $parser->setTempValue($data);
            break;
        default:
            break;
        }
    }

    /**
     * @param $parser
     * @param $attributes
     */
    function handleBeginElement(&$parser, &$attributes)
    {
        //$parser->resetTempValue();
    }


    /**
     * @param $parser
     */
    function handleEndElement(&$parser)
    {
        switch ($parser->getCurrentTag()) {
        case 'member':
            $parser->setTempMember($parser->getTempName(), $parser->getTempValue());
            break;
        case 'array':
        case 'data':
            $parser->setTempArray($parser->getTempValue());
            break;
        default:
            $parser->setParam($parser->getTempValue());
            break;
        }
        $parser->resetTempValue();
    }
}

/**
 * Class RpcMemberHandler
 */
class RpcMemberHandler extends XmlTagHandler
{

    /**
     * @return string
     */
    function getName()
    {
        return 'member';
    }

    /**
     * @param $parser
     * @param $attributes
     */
    function handleBeginElement(&$parser, &$attributes)
    {
        $parser->setWorkingLevel();
        $parser->resetTempMember();
    }

    /**
     * @param $parser
     */
    function handleEndElement(&$parser)
    {
        $member =& $parser->getTempMember();
        $parser->releaseWorkingLevel();
        $parser->setTempStruct($member);
    }
}

/**
 * Class RpcArrayHandler
 */
class RpcArrayHandler extends XmlTagHandler
{

    /**
     * @return string
     */
    function getName()
    {
        return 'array';
    }

    /**
     * @param $parser
     * @param $attributes
     */
    function handleBeginElement(&$parser, &$attributes)
    {
        $parser->setWorkingLevel();
        $parser->resetTempArray();
    }

    /**
     * @param $parser
     */
    function handleEndElement(&$parser)
    {
        $parser->setTempValue($parser->getTempArray());
        $parser->releaseWorkingLevel();
    }
}

/**
 * Class RpcStructHandler
 */
class RpcStructHandler extends XmlTagHandler
{
    /**
     * @return string
     */
    function getName()
    {
        return 'struct';
    }


    /**
     * @param $parser
     * @param $attributes
     */
    function handleBeginElement(&$parser, &$attributes)
    {
        $parser->setWorkingLevel();
        $parser->resetTempStruct();
    }


    /**
     * @param $parser
     */
    function handleEndElement(&$parser)
    {
        $parser->setTempValue($parser->getTempStruct());
        $parser->releaseWorkingLevel();
    }
}
