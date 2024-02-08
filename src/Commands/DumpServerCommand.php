<?php

declare(strict_types=1);

namespace Zyimm\HyperfDumpServer\Commands;

use Hyperf\Command\Command;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Container;
use InvalidArgumentException;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Component\VarDumper\Command\Descriptor\CliDescriptor;
use Symfony\Component\VarDumper\Command\Descriptor\HtmlDescriptor;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\Server\DumpServer;

class DumpServerCommand extends Command
{
    /**
     * The console command name.
     */
    protected ?string $signature = 'dump-server {--format=cli : The output format (cli,html).}';

    /**
     * @var DumpServer $server
     */
    protected DumpServer $server;

    public function __construct(string $name = null)
    {
        parent::__construct($name);

        $this->server = $this->getServer();
    }


    /**
     * @return void
     */
    public function handle(): void
    {
        $descriptor = match ($format = $this->input->getOption('format')) {
            'cli' => new CliDescriptor(new CliDumper),
            'html' => new HtmlDescriptor(new HtmlDumper),
            default => throw new InvalidArgumentException(sprintf('Unsupported format "%s".', $format)),
        };

        $io = new SymfonyStyle($this->input, $this->output);

        $errorIo = $io->getErrorStyle();
        $errorIo->title('Hyperf Var Dump Server');

        $this->server->start();

        $errorIo->success(sprintf('Server listening on %s', $this->server->getHost()));
        $errorIo->comment('Quit the server with CONTROL-C.');

        $this->server->listen(function (Data $data, array $context, int $clientId) use ($descriptor, $io) {
            $descriptor->describe($io, $data, $context, $clientId);
        });
    }

    public function configure()
    {
        parent::configure();

        $this->setDescription('Start the dump server to collect dump information.');
    }

    protected function getServer(): DumpServer
    {
        /** @var Container $container */
        $container = ApplicationContext::getContainer();
        $config = $container->get(ConfigInterface::class);

        return new DumpServer($config->get('dump-server.host', 'tcp://127.0.0.1:9912'));
    }
}
