<?php

namespace Codememory\Components\Database\Pack\Workers;

use Codememory\Components\Database\Connection\Interfaces\ConnectionInterface;
use Codememory\Components\Database\Connection\Interfaces\ConnectorConfigurationInterface;
use Codememory\Components\Database\Connection\Interfaces\ConnectorInterface;
use Codememory\Components\Database\Pack\Utils;

/**
 * Class ConnectionWorker
 *
 * @package Codememory\Components\Database\Workers
 *
 * @author  Codememory
 */
class ConnectionWorker
{

    /**
     * @var ConnectionInterface
     */
    private ConnectionInterface $connection;

    /**
     * @var Utils
     */
    private Utils $utils;

    /**
     * @var ConnectorInterface
     */
    private ConnectorInterface $connector;

    /**
     * @param ConnectionInterface $connection
     */
    public function __construct(ConnectionInterface $connection)
    {

        $this->connection = $connection;
        $this->utils = new Utils();

        $this->initConnectors();
        $this->selectConnector($this->utils->getConnectionName());

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Select a connector, by default the connector is selected
     * from the "defaultConnection" option configuration
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $connectorName
     */
    public function selectConnector(string $connectorName): void
    {

        $this->connector = $this->connection->getConnector($connectorName);

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns the currently active connector
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return ConnectorInterface
     */
    public function getConnector(): ConnectorInterface
    {

        return $this->connector;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns a connection in order to add connectors or get a specific connector
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return ConnectionInterface
     */
    public function getConnection(): ConnectionInterface
    {

        return $this->connection;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Initializing all connectors from configuration
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return void
     */
    private function initConnectors(): void
    {

        foreach ($this->utils->getConnectors() as $connectorName => $connector) {
            $this->connection->addConnector($connectorName, function (ConnectorConfigurationInterface $configuration) use ($connector) {
                if (null !== $host = $connector['host']) {
                    $configuration->host($host);
                }

                if (null !== $port = $connector['port']) {
                    $configuration->port($port);
                }

                if (null !== $username = $connector['username']) {
                    $configuration->username($username);
                }

                if (null !== $password = $connector['password']) {
                    $configuration->password($password);
                }

                $configuration
                    ->dbname($connector['dbname'])
                    ->driver($connector['driver']);
            });
        }

    }

}