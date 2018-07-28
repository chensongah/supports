<?php

namespace SmartJson\Supports;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class Log
{
    /**
     * Logger instance.
     *
     * @var LoggerInterface
     */
    protected static $logger;

    /**
     * Return the logger instance.
     *
     * @author smartjson <me@smartjson.cn>
     *
     * @return LoggerInterface
     */
    public static function getLogger()
    {
        return self::$logger ?: self::$logger = self::createDefaultLogger();
    }

    /**
     * Set logger.
     *
     * @author smartjson <me@smartjson.cn>
     *
     * @param LoggerInterface $logger
     */
    public static function setLogger(LoggerInterface $logger)
    {
        self::$logger = $logger;
    }

    /**
     * Tests if logger exists.
     *
     * @author smartjson <me@smartjson.cn>
     *
     * @return bool
     */
    public static function hasLogger(): bool
    {
        return self::$logger ? true : false;
    }

    /**
     * Make a default log instance.
     *
     * @author smartjson <me@smartjson.cn>
     *
     * @return \Monolog\Logger
     */
    protected static function createDefaultLogger()
    {
        $handler = new RotatingFileHandler(sys_get_temp_dir().'/logs/smartjson.supports.log', 30);
        $handler->setFormatter(
            new LineFormatter("%datetime% > %level_name% > %message% %context% %extra%\n\n", null, false, true)
        );

        $logger = new Logger('smartjson.supports');
        $logger->pushHandler($handler);

        return $logger;
    }

    /**
     * Forward call.
     *
     * @author smartjson <me@smartjson.cn>
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        return forward_static_call_array([self::getLogger(), $method], $args);
    }

    /**
     * Forward call.
     *
     * @author smartjson <me@smartjson.cn>
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        return call_user_func_array([self::getLogger(), $method], $args);
    }
}
