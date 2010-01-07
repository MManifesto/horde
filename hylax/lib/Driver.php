<?php
/**
 * Hylax_Driver Class
 *
 * $Horde: incubator/hylax/lib/Driver.php,v 1.13 2009/01/06 17:50:48 jan Exp $
 *
 * Copyright 2003-2009 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (GPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 *
 * @author  Marko Djukic <marko@oblo.com>
 * @package Hylax
 */
class Hylax_Driver {

    var $_params;

    function Hylax_Driver($params)
    {
        global $conf;

        $this->_params = $params;
    }

    function getFolder($folder, $path = null)
    {
        return $this->_getFolder($folder, $path);
    }

    /**
     * Attempts to return a concrete Hylax_Driver instance based on $driver.
     *
     * @param string $driver  The type of concrete Hylax_Driver subclass to
     *                        return.
     * @param array $params   A hash containing any additional configuration or
     *                        connection parameters a subclass might need.
     *
     * @return Hylax_Driver   The newly created concrete Hylax_Driver
     *                        instance, or false on error.
     */
    function &factory($driver = null, $params = null)
    {
        if (is_null($driver)) {
            $driver = $GLOBALS['conf']['fax']['driver'];
        }

        $driver = basename($driver);

        include_once dirname(__FILE__) . '/Driver/' . $driver . '.php';
        $class = 'Hylax_Driver_' . $driver;
        if (class_exists($class)) {
            $hylax = &new $class($params);
            return $hylax;
        } else {
            Horde::fatal(PEAR::raiseError(sprintf(_("No such backend \"%s\" found"), $driver)), __FILE__, __LINE__);
        }
    }

    /**
     * Attempts to return a reference to a concrete Hylax_Driver instance
     * based on $driver. It will only create a new instance if no
     * Hylax_Driver instance with the same parameters currently exists.
     *
     * This should be used if multiple storage sources are required.
     *
     * This method must be invoked as: $var = &Hylax_Driver::singleton()
     *
     * @param string $driver  The type of concrete Hylax_Driver subclass to
     *                        return.
     * @param array $params   A hash containing any additional configuration or
     *                        connection parameters a subclass might need.
     *
     * @return mixed  The created concrete Hylax_Driver instance, or false on
     *                error.
     */
    function &singleton($driver = null, $params = null)
    {
        static $instances;

        if (is_null($driver)) {
            $driver = $GLOBALS['conf']['fax']['driver'];
        }

        if (!isset($instances)) {
            $instances = array();
        }
        $signature = serialize(array($driver, $params));

        if (!isset($instances[$signature])) {
            $instances[$signature] = &Hylax_Driver::factory($driver, $params);
        }
        return $instances[$signature];
    }

}
