<?php declare(strict_types=1);

namespace XoopsModules\Tag;

/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    XOOPS Project (https://xoops.org)
 * @license      GNU GPL 2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @author       XOOPS Development Team
 */

/**
 * Class Helper
 */
class Helper extends \Xmf\Module\Helper
{
    public $debug;

    /**
     * @param bool $debug
     */
    public function __construct($debug = false)
    {
        if (null === $this->dirname) {
            $dirname       = \basename(\dirname(__DIR__));
            $this->dirname = $dirname;
        }
        parent::__construct($this->dirname);
    }

    public static function getInstance(bool $debug = false): self
    {
        static $instance;
        if (null === $instance) {
            $instance = new static($debug);
        }

        return $instance;
    }

    public function getDirname(): string
    {
        return $this->dirname;
    }

    /**
     * @param string|null $name
     * @param string|null $value
     *
     */
    public function setConfig(string $name = null, string $value = null): ?string
    {
        if (null === $this->configs) {
            $this->initConfig();
        }
        $this->configs[$name] = $value;
        $this->addLog("Setting config '{$name}' : " . $this->configs[$name]);

        return $this->configs[$name];
    }

    /**
     * Get an Object Handler
     *
     * @param string $name name of handler to load
     *
     * @return \XoopsPersistableObjectHandler
     */
    public function getHandler($name): ?\XoopsPersistableObjectHandler
    {
        $ret = null;

        $class = __NAMESPACE__ . '\\' . \ucfirst($name) . 'Handler';
        if (!\class_exists($class)) {
            throw new \RuntimeException("Class '$class' not found");
        }
        /** @var \XoopsMySQLDatabase $db */
        $db     = \XoopsDatabaseFactory::getDatabaseConnection();
        $helper = self::getInstance();
        $ret    = new $class($db, $helper);
        $this->addLog("Getting handler '$name'");

        return $ret;
    }
}
