<?php

namespace TheRealGambo\Ramlfications\Console\Commands;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Cilex\Provider\Console\Command;
use Symfony\Component\Yaml\Yaml;
use TheRealGambo\Ramlfications\Nodes\ResourceMethodNode;
use TheRealGambo\Ramlfications\Nodes\ResourceNode;
use TheRealGambo\Ramlfications\Parser;

class ListRoutesCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('list:routes')
            ->setDescription('List all resources available')
            ->addArgument('filename', InputArgument::REQUIRED, 'RAML file to parse');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('filename');

        if (!file_exists($file) || is_null($file)) {
            $output->writeln('<error>Invalid RAML file path/location specified.</error>');
            exit;
        }

        $parser = new Parser();
        $data   = $parser->parseFile(basename($file), dirname($file));
        $parsed = $parser->parseRaml($data);

        $output->writeln('<info>API Title: </info>' . $parsed->getTitle());
        $output->writeln('<info>API Version: </info>' . $parsed->getVersion());
        $output->writeln('<info>Base URI: </info>' . $parsed->getBaseUri());
        $output->writeln('<info>Default Protocols: </info>' . implode(', ', $parsed->getProtocols()));
        $output->writeln('<info>Default Media-Types: </info>' . implode(', ', $parsed->getMediaType()));
        $output->writeln('<info>Security Schemes:</info> ' . implode(', ', array_keys($parsed->getSecuritySchemes())));

        $table = new Table($output);
        $table->setHeaders([
            'Route', 'Method', 'Protocols', 'Security', 'Media Type', 'Headers', 'Query Params', 'Responses'
        ]);

        $this->recurseResources($table, $parsed->getResources());

        $table->render();
    }

    private function recurseResources(Table $table, array $resources, $indent = '')
    {
        foreach ($resources as $resource) {
            /** @var ResourceNode $resource */
            $rn = $indent . $resource->getDisplayName();

            foreach ($resource->getMethods() as $method) {
                /** @var ResourceMethodNode $method */

                $m = $method->getMethod();
                $p = implode(', ', $method->getProtocols());
                $s = implode(', ', $method->getSecuredBy());
                $h = implode(', ', array_keys($method->getHeaders()));
                $q = implode(', ', array_keys($method->getQueryParameters()));
                $r = implode(', ', array_keys($method->getResponses()));
                $table->addRow([$rn, $m, $p, $s, '', $h, $q, $r]);
            }

            if (count($resource->getChildrenResources()) > 0) {
                $this->recurseResources($table, $resource->getChildrenResources(), $indent . '  ');
            }
        }
    }
}
