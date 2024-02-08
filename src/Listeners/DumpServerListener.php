<?php

declare(strict_types=1);

namespace Zyimm\HyperfDumpServer\Listeners;

use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Container;
use Hyperf\Di\Exception\NotFoundException;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\AfterWorkerStart;
use Hyperf\Framework\Event\OnStart;
use Hyperf\HttpServer\Request;
use Symfony\Component\VarDumper\Dumper\ContextProvider\SourceContextProvider;
use Symfony\Component\VarDumper\Server\Connection;
use Symfony\Component\VarDumper\Server\DumpServer;
use Symfony\Component\VarDumper\VarDumper;
use Zyimm\HyperfDumpServer\Dumper;
use Zyimm\HyperfDumpServer\RequestContextProvider;

class DumpServerListener implements ListenerInterface
{
    public function listen(): array
    {
        return [
            AfterWorkerStart::class,
            OnStart::class,
        ];
    }

    /**
     * process
     *
     * @param object $event
     *
     * @throws NotFoundException
     */
    public function process(object $event): void
    {
        /**
         * @var Container $container
         */
        $container = ApplicationContext::getContainer();
        $config    = $container->get(ConfigInterface::class);
        $host      = $config->get('dump-server.host');
        $container->set(DumpServer::class, function () use ($host) {
            return new DumpServer($host);
        });

        $connection = new Connection($host, [
            'request' => new RequestContextProvider($container->make(Request::class)),
            'source'  => new SourceContextProvider('utf-8', BASE_PATH),
        ]);

        VarDumper::setHandler(function ($var) use ($connection) {
            (new Dumper($connection))->dump($var);
        });
    }
}