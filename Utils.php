<?php

namespace Codememory\Components\Database\Pack;

use Codememory\Components\Configuration\Configuration;
use Codememory\Components\Configuration\Interfaces\ConfigInterface;
use Codememory\Components\Database\Connection\Interfaces\DriverInterface;
use Codememory\Components\GlobalConfig\GlobalConfig;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class Utils
 *
 * @package Codememory\Components\Database
 *
 * @author  Codememory
 */
class Utils
{

    /**
     * @var ConfigInterface
     */
    private ConfigInterface $config;

    /**
     * Utils Construct.
     */
    public function __construct()
    {

        $this->config = Configuration::getInstance()->open(GlobalConfig::get('database-pack.configName'));

    }

    /**
     * @return array
     */
    public function getConnectors(): array
    {

        $connectors = $this->config->get('pack.connectors') ?: [];

        foreach ($connectors as &$connector) {
            $connector = $this->connectorStructure(
                $connector['host'] ?? null,
                $connector['port'] ?? null,
                $connector['username'] ?? null,
                $connector['password'] ?? null,
                $connector['dbname'] ?? null,
                $connector['driver'] ?? null,
            );
        }

        return $connectors;

    }

    /**
     * @param string $connectorName
     *
     * @return bool
     */
    public function existConnector(string $connectorName): bool
    {

        return array_key_exists($connectorName, $this->getConnectors());

    }

    /**
     * @param string $connectorName
     *
     * @return array
     */
    public function getConnector(string $connectorName): array
    {

        if ($this->existConnector($connectorName)) {
            return $this->getConnectors()[$connectorName];
        }

        return [];

    }

    /**
     * @return string|null
     */
    public function getConnectionName(): ?string
    {

        return $this->config->get('pack.defaultConnection');

    }

    /**
     * @param string|null $host
     * @param int|null    $port
     * @param string|null $username
     * @param string|null $password
     * @param string|null $dbname
     * @param string|null $driver
     *
     * @return array
     */
    #[ArrayShape(['host' => "mixed|string", 'port' => "int|null", 'username' => "mixed|string", 'password' => "mixed|string", 'dbname' => "mixed|string", 'driver' => "\Codememory\Components\Database\Connection\Interfaces\DriverInterface"])]
    private function connectorStructure(?string $host, ?int $port, ?string $username, ?string $password, ?string $dbname, ?string $driver): array
    {

        return [
            'host'     => $host ?: GlobalConfig::get('database-pack.defaultHost'),
            'port'     => $port,
            'username' => $username ?: GlobalConfig::get('database-pack.defaultUsername'),
            'password' => $password ?: GlobalConfig::get('database-pack.defaultPassword'),
            'dbname'   => $dbname ?: GlobalConfig::get('database-pack.defaultDbname'),
            'driver'   => $this->getObjectDriver($driver ?: GlobalConfig::get('database-pack.defaultDriver'))
        ];

    }

    /**
     * @param string $namespace
     *
     * @return DriverInterface
     */
    private function getObjectDriver(string $namespace): DriverInterface
    {

        return new $namespace();

    }

}