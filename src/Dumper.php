<?php

declare(strict_types = 1);

namespace Zyimm\HyperfDumpServer;

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\Server\Connection;

class Dumper
{
    /**
     * The connection.
     */
    private ?Connection $connection;

    /**
     * Dumper constructor.
     *
     * @param Connection|null $connection
     *
     * @return void
     */
    public function __construct(Connection $connection = null)
    {
        $this->connection = $connection;
    }

    /**
     * Dump a value with elegance.
     *
     * @param  mixed  $value
     * @return void
     */
    public function dump(mixed $value): void
    {
        if (class_exists(CliDumper::class)) {
            $data = (new VarCloner)->cloneVar($value);

            if ($this->connection === null || $this->connection->write($data) === false) {
                $dumper = in_array(PHP_SAPI, ['cli', 'phpdbg']) ? new CliDumper : new HtmlDumper;
                $dumper->dump($data);
            }
        } else {
            var_dump($value);
        }
    }
}
