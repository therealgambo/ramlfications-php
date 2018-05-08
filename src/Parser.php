<?php

namespace TheRealGambo\Ramlfications;

use Symfony\Component\Yaml\Yaml;
use TheRealGambo\Ramlfications\Exceptions\LoadRamlException;
use TheRealGambo\Ramlfications\Nodes\BaseNode;
use TheRealGambo\Ramlfications\Nodes\ResourceNode;
use TheRealGambo\Ramlfications\Nodes\RootNode;
use TheRealGambo\Ramlfications\Parsers\RamlHeaderParser;
use TheRealGambo\Ramlfications\Parsers\RamlVersionParser;

class Parser
{
    private $cachedFiles = [];

    public function parseString(string $ramlString, string $rootDir, $fileName)
    {
        // Parse the RAML header and extract the version
        $header = RamlHeaderParser::parse(strtok($ramlString, "\n"));
        $this->ramlVersion = $header->getVersion();

//        $data = Yaml::parse($ramlString, Yaml::PARSE_CUSTOM_TAGS & Yaml::PARSE_EXCEPTION_ON_INVALID_TYPE & Yaml::PARSE_OBJECT);
        $data = Yaml::parse($ramlString, Yaml::PARSE_CUSTOM_TAGS & Yaml::PARSE_OBJECT);

        if (!$data) {
            throw new LoadRamlException('The specified RAML file \'' . $fileName . '\' is empty.');
        }

        $parsedRaml = $this->includeAndParseFiles(
            $data,
            $rootDir
        );

        if (RamlVersionParser::RAML_10 === $header->getVersion()) {
//            return $this->parseRaml($parsedRaml);
            return $parsedRaml;
        }

        return null;
    }

    /**
     * Recurse through the RAML structure and load includes
     *
     * @param array|string $raml
     * @param string       $rootDir
     *
     * @return array|string
     */
    private function includeAndParseFiles($raml, string $rootDir)
    {
        if (is_array($raml)) {
            $result = [];
            foreach ($raml as $key => $element) {
                $result[$key] = $this->includeAndParseFiles($element, $rootDir);
            }
            return $result;
        } elseif (is_string($raml) && strpos($raml, '!include') === 0) {
            return $this->parseFile(str_replace('!include ', '', $raml), $rootDir);
        }

        return $raml;
    }

    public function parseUrl($url)
    {
        return $this->parseFile($url);
    }

    /**
     * Load and parse a file
     *
     * todo: url loading recursively
     *
     * @param string $fileName
     * @param string $rootDir
     *
     * @throws \Exception
     *
     * @return array|bool
     */
    public function parseFile(string $fileName, string $rootDir = '')
    {
        // Check if the file is local or remote
        if (parse_url($fileName, PHP_URL_HOST) === null) {
            // File is local
            $rootDir  = realpath($rootDir);
            $fullPath = realpath($rootDir . '/' . $fileName);

            if (is_readable($fullPath) === false) {
                throw new LoadRamlException('The specified file cannot be read: ' . $fileName);
            }

//            // Prevent LFI directory traversal attacks
//            if (!$this->configuration->isDirectoryTraversalAllowed() &&
//                substr($fullPath, 0, strlen($rootDir)) !== $rootDir
//            ) {
//                return false;
//            }
//            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        } else {
            // File is remote
            $fullPath = $fileName;
            $rootDir = '';

            if (strpos($fileName, 'http') === false) {
                $rootDir = dirname($fileName);
                $fullPath = $rootDir . DIRECTORY_SEPARATOR . $fileName;
            }
//            $fileHeaders = get_headers($fileName, true);
//            $mimeType = isset($fileHeaders['Content-Type']) ? $fileHeaders['Content-Type'] : '';
//            $mapping = [
//                'application/json'      => 'json',
//                'application/xml'       => 'xml',
//                'application/soap+xml'  => 'xml',
//                'text/xml'              => 'xml'
//            ];
//
////            var_dump($mimeType);
////            if (!array_key_exists($mimeType, $mapping)) {
////                return false;
////            }
////
////            $fileExtension = $mapping[$mimeType];
            $fileExtension = 'raml';
        }

        $cacheKey = md5($fullPath);

        // Cache based on file name, prevents including/parsing the same file multiple times
        if (isset($this->cachedFiles[$cacheKey])) {
            return $this->cachedFiles[$cacheKey];
        }

        $rootDir = dirname($rootDir . DIRECTORY_SEPARATOR . $fileName);

        // RAML and YAML files are always parsed
        $parsed   = $this->parseString(file_get_contents($fullPath), $rootDir, $fileName);
        $fileData = $this->includeAndParseFiles($parsed, $rootDir);

        // Cache before returning
        $this->cachedFiles[$cacheKey] = $fileData;

        return $fileData;
    }

















    /**
     * Parse raml
     *
     * @param array $raml
     *
     * @return RootNode
     */
    public function parseRaml(array $raml): RootNode
    {
        $rootNode = new RootNode($raml);

        $rootNode->applyInheritance();

        // Set Resources
        $rootNode->setResources($this->createResources($raml, $rootNode));

        return $rootNode;
    }

    /**
     * Parse resource nodes
     *
     * @param array             $raml
     * @param RootNode          $rootNode
     * @param ResourceNode|null $parentNode
     *
     * @return array
     */
    private function createResources(array $raml, RootNode $rootNode, ResourceNode $parentNode = null): array
    {
        $resources = [];

        // Iterate over each key, checking if it starts with '/'
        foreach ($raml as $path => $nodeRaml) {
            // If they key does not begin with '/', continue. This is not a valid resource.
            if (strpos($path, '/') !== 0) {
                continue;
            }

            // If the parent exists, we prepend the parents path to the start of
            // the key for storing in the array.
            if (!is_null($parentNode)) {
                $path = $parentNode->getPath() . $path;
            }

            // Get all keys for all top-level elements of this array
            $methods = array_keys($nodeRaml);

            // @todo: enable when types are done
//            if (in_array('type', array_keys($nodeRaml))) {
//                $type = $rootNode->getResourceTypeByKey($nodeRaml['type']);
//                if (!is_null($type) && property_exists($type, 'getMethods')) {
//                    $methods[] = $type;
//                }
//            }

            // Check if the key matches one of the available http methods
            // @codeCoverageIgnoreStart
            array_walk($methods, function (&$item, $key) use (&$methods) {
                if (!in_array($item, BaseNode::AVAILABLE_METHODS)){
                    unset($methods[$key]);
                }
            });
            // @codeCoverageIgnoreEnd

            if (count($methods) > 0) {
                $parent = null;
//                foreach ($methods as $method) {
////                    $parent = $this->createResourceNode($key, $nodeRaml, $method, $rootNode, $parentNode);
//                    $parent = $this->createResourceNode($nodeRaml, $rootNode, $key, $parentNode);
//                    $resources[$key. ':' . $method] = $parent;
//                }

                $parent = $this->createResourceNode($nodeRaml, $rootNode, $path, $parentNode);
                $resources[$path] = $parent;

//            } elseif (in_array('type', array_keys($nodeRaml))) {
//

            } else {
                // We create the resource node for this node and store it.
//                $parent = $this->createResourceNode($key, $nodeRaml, null, $rootNode, $parentNode);
                $parent = $this->createResourceNode($nodeRaml, $rootNode, $path, $parentNode);
                $resources[$path] = $parent;
            }

            // We then recursively try to create any children nodes, using the RAML of this node
            // as the starting point in the recursion. We then merge any children resources, with the
            // current resource and return it.
            $children = $this->createResources($nodeRaml, $rootNode, $parent);

            // Finally, we also reference the children in the parents node
            if (!is_null($parent)) {
                $parent->setChildrenResources($children);
            }
        }

        return $resources;
    }

    private function createResourceNode($raml, $rootNode, $path, $parentNode): ResourceNode
    {
        return new ResourceNode($raml, $rootNode, $path, $parentNode);
    }
}
