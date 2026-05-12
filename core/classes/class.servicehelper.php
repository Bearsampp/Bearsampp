<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ServiceHelper
 *
 * Utility class for service management operations.
 * Provides centralized methods for service lookup, syntax check commands,
 * and common service operations to eliminate code duplication.
 */
class ServiceHelper
{
    /**
     * Service name to binary instance mapping
     * @var array
     */
    private static $serviceMap = null;

    /**
     * Service name to syntax check command mapping
     * @var array
     */
    private static $syntaxCheckMap = null;

    /**
     * Initialize service mappings
     *
     * @param object $bearsamppBins The bins object
     * @return void
     */
    private static function initializeMappings($bearsamppBins)
    {
        if (self::$serviceMap === null) {
            self::$serviceMap = [
                BinApache::SERVICE_NAME => $bearsamppBins->getApache(),
                BinMysql::SERVICE_NAME => $bearsamppBins->getMysql(),
                BinMariadb::SERVICE_NAME => $bearsamppBins->getMariadb(),
                BinMailpit::SERVICE_NAME => $bearsamppBins->getMailpit(),
                BinMemcached::SERVICE_NAME => $bearsamppBins->getMemcached(),
                BinPostgresql::SERVICE_NAME => $bearsamppBins->getPostgresql(),
                BinXlight::SERVICE_NAME => $bearsamppBins->getXlight(),
            ];
        }

        if (self::$syntaxCheckMap === null) {
            self::$syntaxCheckMap = [
                BinApache::SERVICE_NAME => BinApache::CMD_SYNTAX_CHECK,
                BinMysql::SERVICE_NAME => BinMysql::CMD_SYNTAX_CHECK,
                BinMariadb::SERVICE_NAME => BinMariadb::CMD_SYNTAX_CHECK,
            ];
        }
    }

    /**
     * Get binary instance from service name
     *
     * @param string $serviceName The service name
     * @param object $bearsamppBins The bins object
     * @return object|null The binary instance or null if not found
     */
    public static function getBinFromServiceName($serviceName, $bearsamppBins)
    {
        self::initializeMappings($bearsamppBins);
        return isset(self::$serviceMap[$serviceName]) ? self::$serviceMap[$serviceName] : null;
    }

    /**
     * Get syntax check command for a service
     *
     * @param string $serviceName The service name
     * @param object $bearsamppBins The bins object (for initialization)
     * @return string|null The syntax check command or null if not applicable
     */
    public static function getSyntaxCheckCmd($serviceName, $bearsamppBins = null)
    {
        if ($bearsamppBins !== null) {
            self::initializeMappings($bearsamppBins);
        }
        return isset(self::$syntaxCheckMap[$serviceName]) ? self::$syntaxCheckMap[$serviceName] : null;
    }

    /**
     * Get service display name (Name + Version + Service Name)
     *
     * @param object $bin The binary instance
     * @param object $service The service instance
     * @return string The formatted service name
     */
    public static function getServiceDisplayName($bin, $service)
    {
        return $bin->getName() . ' ' . $bin->getVersion() . ' (' . $service->getName() . ')';
    }

    /**
     * Process all services with a callback function
     *
     * @param object $bearsamppBins The bins object
     * @param callable $callback Function to call for each service: function($serviceName, $service, $bin, $syntaxCheckCmd)
     * @return void
     */
    public static function processServices($bearsamppBins, callable $callback)
    {
        self::initializeMappings($bearsamppBins);

        foreach ($bearsamppBins->getServices() as $serviceName => $service) {
            $bin = self::getBinFromServiceName($serviceName, $bearsamppBins);
            $syntaxCheckCmd = self::getSyntaxCheckCmd($serviceName);

            if ($bin !== null) {
                $callback($serviceName, $service, $bin, $syntaxCheckCmd);
            }
        }
    }

    /**
     * Start a service with optional syntax check
     *
     * @param object $bin The binary instance
     * @param string|null $syntaxCheckCmd The syntax check command (optional)
     * @param bool $showErrors Whether to show error messages (default: true)
     * @return bool True if service started successfully, false otherwise
     */
    public static function startService($bin, $syntaxCheckCmd = null, $showErrors = true)
    {
        return Util::startService($bin, $syntaxCheckCmd, $showErrors);
    }

    /**
     * Stop a service
     *
     * @param object $service The service instance
     * @return bool True if service stopped successfully, false otherwise
     */
    public static function stopService($service)
    {
        return $service->stop();
    }

    /**
     * Restart a service
     *
     * @param object $service The service instance
     * @return bool True if service restarted successfully, false otherwise
     */
    public static function restartService($service)
    {
        return $service->restart();
    }

    /**
     * Get all service names
     *
     * @return array Array of service name constants
     */
    public static function getAllServiceNames()
    {
        return [
            BinApache::SERVICE_NAME,
            BinMysql::SERVICE_NAME,
            BinMariadb::SERVICE_NAME,
            BinMailpit::SERVICE_NAME,
            BinMemcached::SERVICE_NAME,
            BinPostgresql::SERVICE_NAME,
            BinXlight::SERVICE_NAME,
        ];
    }

    /**
     * Check if a service has syntax check capability
     *
     * @param string $serviceName The service name
     * @return bool True if service supports syntax check
     */
    public static function hasSyntaxCheck($serviceName)
    {
        if (self::$syntaxCheckMap === null) {
            // Initialize with dummy bins object if needed
            self::$syntaxCheckMap = [
                BinApache::SERVICE_NAME => BinApache::CMD_SYNTAX_CHECK,
                BinMysql::SERVICE_NAME => BinMysql::CMD_SYNTAX_CHECK,
                BinMariadb::SERVICE_NAME => BinMariadb::CMD_SYNTAX_CHECK,
            ];
        }
        return isset(self::$syntaxCheckMap[$serviceName]);
    }

    /**
     * Get port for a service
     *
     * @param string $serviceName The service name
     * @param object $bearsamppBins The bins object
     * @return int The port number or 0 if not applicable
     */
    public static function getServicePort($serviceName, $bearsamppBins)
    {
        $bin = self::getBinFromServiceName($serviceName, $bearsamppBins);
        if ($bin === null) {
            return 0;
        }

        // Different services have different methods to get port
        if (method_exists($bin, 'getPort')) {
            return $bin->getPort();
        } elseif (method_exists($bin, 'getSmtpPort')) {
            return $bin->getSmtpPort();
        }

        return 0;
    }
}
