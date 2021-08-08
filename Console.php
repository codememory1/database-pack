<?php

namespace Codememory\Components\Database\Pack;

use Codememory\Components\Database\Connection\Interfaces\ConnectionInterface;
use Codememory\Components\Database\Migrations\Commands\CreateMigrationCommand;
use Codememory\Components\Database\Migrations\Commands\GenerateMigrationsCommand;
use Codememory\Components\Database\Migrations\Commands\InfoCommand;
use Codememory\Components\Database\Migrations\Commands\ListCommand;
use Codememory\Components\Database\Migrations\Commands\MigrateCommand;
use Codememory\Components\Database\Migrations\Commands\RollbackCommand;
use Codememory\Components\Database\Orm\Commands\CheckConnectionCommand;
use Codememory\Components\Database\Orm\Commands\CreateDatabaseCommand;
use Codememory\Components\Database\Orm\Commands\CreateTableCommand;
use Codememory\Components\Database\Orm\Commands\DropTableCommand;
use Codememory\Components\Database\Orm\Commands\ListEntitiesCommand;
use Codememory\Components\Database\Orm\Commands\MakeEntityCommand;
use Codememory\Components\Database\Orm\Commands\UpdateTableCommand;
use Codememory\Components\Database\Pack\Workers\ConnectionWorker;
use Exception;
use Symfony\Component\Console\Application;

/**
 * Class Console
 *
 * @package Codememory\Components\Database\Pack
 *
 * @author  Codememory
 */
class Console
{

    /**
     * @var Application
     */
    private Application $app;

    /**
     * @var ConnectionWorker
     */
    private ConnectionWorker $connectionWorker;

    /**
     * @param Application         $application
     * @param ConnectionInterface $connection
     */
    public function __construct(Application $application, ConnectionInterface $connection)
    {

        $this->app = $application;
        $this->connectionWorker = new ConnectionWorker($connection);

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Adds all package commands working with the database to the application console
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @throws Exception
     */
    public function addCommands(): void
    {

        $connector = $this->connectionWorker->getConnector();
        $connection = $this->connectionWorker->getConnection();

        $this->app->addCommands([
            new CreateDatabaseCommand($connector, $connection),
            new CreateTableCommand($connector, $connection),
            new UpdateTableCommand($connector, $connection),
            new DropTableCommand($connector, $connection),
            new CheckConnectionCommand($connector, $connection),
            new GenerateMigrationsCommand($connector, $connection),
            new InfoCommand($connector, $connection),
            new MigrateCommand($connector, $connection),
            new RollbackCommand($connector, $connection),
            new CreateMigrationCommand(),
            new ListCommand(),
            new ListEntitiesCommand(),
            new MakeEntityCommand()
        ]);

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Add commands to the application console which in turn requires
     * a database connection dependency
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $commandNamespace
     *
     * @return void
     */
    public function addCommand(string $commandNamespace): void
    {

        $this->app->add(new $commandNamespace($this->connectionWorker->getConnector(), $this->connectionWorker->getConnection()));

    }

}