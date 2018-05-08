<?php

namespace TheRealGambo\Ramlfications\Console\Commands;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Cilex\Provider\Console\Command;
use Symfony\Component\Yaml\Yaml;
use TheRealGambo\Ramlfications\Nodes\ResourceNode;
use TheRealGambo\Ramlfications\Parameters\SecurityScheme;
use TheRealGambo\Ramlfications\Parser;

class ListSecuritySchemesCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('list:security-schemes')
            ->setDescription('List all available security schemes')
            ->addArgument('filename', InputArgument::REQUIRED, 'RAML file to parse');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('filename');

        if (!file_exists($file) || is_null($file)) {
            $output->writeln('<error>Invalid RAML file path/location specified.</error>');
            exit;
        }

        $contents = file_get_contents($file);

        if ($contents === false) {
            $output->writeln('<error>Error reading RAML file: ' . $file . '</error>');
            exit;
        }

        $parser = new Parser();
        $parsed = $parser->parseRaml(Yaml::parse($contents));

        $output->writeln('<info>API Title: </info>' . $parsed->getTitle());
        $output->writeln('<info>API Version: </info>' . $parsed->getVersion());
        $output->writeln('<info>Base URI: </info>' . $parsed->getBaseUri());
        $output->writeln('<info>Default Protocols: </info>' . implode(', ', $parsed->getProtocols()));
        $output->writeln('<info>Default Media-Types: </info>' . implode(', ', $parsed->getMediaType()));
        $output->writeln('<info>Security Schemes:</info> ' . implode(', ', array_keys($parsed->getSecuritySchemes())));


        $table = new Table($output);
        $table->setHeaders([
            'Code', 'Name', 'Type', 'Headers', 'Query Params', 'Query String', 'Responses'
        ]);

        foreach ($parsed->getSecuritySchemes() as $security) {
            /** @var SecurityScheme $security */
            $code = $security->getName();
            $name = $security->getDisplayName();
            $type = $security->getType();
            $headers = implode(', ', array_keys($security->getHeaders()));
            $queryParams = !is_null($security->getQueryParameters()) ? implode(', ', array_keys($security->getQueryParameters())) : '';
            $responses   = !is_null($security->getResponses()) ? implode(', ', array_keys($security->getResponses())) : '';
            $queryString = $security->getQueryString();

            $table->addRow([$code, $name, $type, $headers, $queryParams, $queryString, $responses]);
        }

        $table->render();
    }
}
