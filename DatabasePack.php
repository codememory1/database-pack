<?php

namespace Codememory\Components\Database\Pack;

use Codememory\Components\Database\Connection\Interfaces\ConnectionInterface;
use Codememory\Components\Database\Orm\EntityManager;
use Codememory\Components\Database\Orm\Interfaces\EntityManagerInterface;
use Codememory\Components\Database\Pack\Workers\ConnectionWorker;
use JetBrains\PhpStorm\Pure;

/**
 * Class DatabasePack
 *
 * @package Codememory\Components\Database
 *
 * @author  Codememory
 */
class DatabasePack
{

    /**
     * @var ConnectionWorker
     */
    private ConnectionWorker $connectionWorker;

    /**
     * @param ConnectionInterface $connection
     */
    public function __construct(ConnectionInterface $connection)
    {

        $this->connectionWorker = new ConnectionWorker($connection);

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns a connected worker
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return ConnectionWorker
     */
    public function getConnectionWorker(): ConnectionWorker
    {

        return $this->connectionWorker;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns a manager to work with an entity
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return EntityManagerInterface
     */
    #[Pure]
    public function getEntityManager(): EntityManagerInterface
    {

        return new EntityManager($this->connectionWorker->getConnector());

    }

}