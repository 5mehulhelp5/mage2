<?php

/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

//namespace Mage;
/*
include 'Logger/Log.php';
include 'Debug/Dump.php';
include 'Debug/Trace.php';
include 'Core/Regestry.php';
*/

use \Mage\Logger\Log;
use \Mage\Debug\Dump;
use \Mage\Debug\Trace;
use \Mage\DB\DB;
use \Mage\DB\DB2;
use \Mage\Core\Regestry;

\Kint\Renderer\RichRenderer::$theme = 'aante-dark.css';
\Kint\Renderer\RichRenderer::$folder = true;
((new DB2()));

use Magento\Framework\App\ObjectManager;

class Mage
{
    use Log;
    use Regestry;
    use Trace;
    use Dump;
    use DB;

    public static $objectManager = null;
    public static $om = null;

    public static $classRegestry = [];

    public static function get(string $className, bool $new = false)
    {
        if ($new === false) {
            if (isset(self::$classRegestry[$className])) {
                return self::$classRegestry[$className];
            }
            if (!isset(self::$objectManager)) {
                self::omInit();
            }
            self::$classRegestry[$className] = self::$objectManager->get($className);
            return self::$classRegestry[$className];
        }

        return self::$objectManager->create($className);
    }

    public static function omInit()
    {
        if (!isset(self::$objectManager)) {
            self::$objectManager = ObjectManager::getInstance();
            self::$om = self::$objectManager;
        }
        return self::$om;
    }

    //Alias function
    public static function om(string $className, bool $new = false)
    {
        return self::get($className, $new);
    }

    public static function create(string $className)
    {
        self::get($className, true);
    }

    static function getVersion($key = 'version')
    {
        $composerJson = json_decode(file_get_contents(BP . '/composer.json'), true);
        return $composerJson[$key];
    }

    static function getMediaURL()
    {
        if (!isset(self::$regestry['media_url'])) {
            $storeManager = Mage::get(\Magento\Store\Model\StoreManagerInterface::class);
            self::$regestry['media_url'] = $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        }
        return self::$regestry['media_url'];
    }
    static function getBasePath()
    {
        return BP;
    }

    public static function getUrl($path = '/')
    {
        return self::get('\Magento\Framework\UrlInterface')->getUrl($path);
    }

    public static function dispatchEvent($eventName, $data = [])
    {
        self::get('Magento\Framework\Event\Manager')->dispatch($eventName, $data);
    }


    public static function getDBConnection($connectionName = 'default')
    {
        return self::get('\Magento\Framework\App\ResourceConnection')->getConnection($connectionName);
    }

    public static function getMode()
    {
        return self::get('\Magento\Framework\App\State')->getMode();
    }

    public static function getObjectManager()
    {
        return self::omInit();
    }

    public static function getOM()
    {
        return self::getObjectManager();
    }
    /**
     * Retrieve config value for store by path
     */
    public static function getConfigValue($path, $storeId = null)
    {
        return self::get('\Magento\Framework\App\Config\ScopeConfigInterface')->getValue($path, '\Magento\Store\Model\ScopeInterface::SCOPE_STORE', $storeId);
    }
}