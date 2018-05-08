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
use TheRealGambo\Ramlfications\Parser;

class InspectRouteCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('inspect:route')
            ->setDescription('Inspect a specific route')
            ->addArgument('filename', InputArgument::REQUIRED, 'RAML file to parse')
            ->addArgument('route', InputArgument::REQUIRED, 'Route to inspect');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('filename');
        $route = $input->getArgument('route');

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

        /** @var ResourceNode $resource */
        $resource = $parsed->getResourceByPath($route);


        $routeTable = new Table($output);
        $routeTable->setHeaders([
            'Route', 'Method', 'Protocols', 'Security', 'Media Type', 'Children', 'Parent', 'Query Params', 'Responses'
        ]);

        $routeName   = $resource->getName();
        $securedBy   = implode(', ', $resource->getSecuredBy());
        $method      = strtoupper($resource->getMethod());
        $proto       = implode(', ', $resource->getProtocols());
        $hasChildren = count($resource->getChildrenResources()) > 0 ? count($resource->getChildrenResources()) : 'no';
        $parent      = !is_null($resource->getParent()) ? $resource->getParent()->getName() : '';
        $queryParams = implode(', ', array_keys($resource->getQueryParameters()));
        $responses   = !is_null($resource->getResponses()) ? implode(', ', array_keys($resource->getResponses())) : '';
        $media = is_array($resource->getMediaType()) ? implode(', ', $resource->getMediaType()) : $resource->getMediaType();
        $rType = !is_null($resource->getResourceType()) ? $resource->getResourceType()->getName() : '';

        $routeTable->addRow([$routeName, $method, $proto, $securedBy, $media, $hasChildren, $parent, $queryParams, count($resource->getResponses()), $rType]);
        $routeTable->render();


        // security scheme
        $schemeTable = new Table($output);
        $schemeTable->setHeaders(
            ['Security Scheme', 'Name', 'Type', 'Headers', 'Query Parameters', 'Responses']
        );

        foreach ($resource->getSecuredBy() as $secured) {
            if ($secured === 'null') {
                continue;
            }
            $scheme = $resource->getSecuritySchemeByKey($secured);
//            var_dump($scheme);
            $h = implode(', ', array_keys($scheme->getHeaders()));
            $q = implode(', ', array_keys($scheme->getQueryParameters()));


            $schemeTable->addRow([$secured, $scheme->getDisplayName(), $scheme->getType(), $h, $q, count($scheme->getResponses())]);
        }


        $schemeTable->render();

        $p = $parsed->getResourceByPathAndMethod('/resource1/parent', 'get');
//        print_r($p->getMediaType());
    }
}
