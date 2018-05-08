<?php

namespace TheRealGambo\Ramlfications\Utilities;

class InheritanceUtility
{
    public static function mapInheritanceFunctions($nodeType)
    {
        $functions = [
            'traits'   => '',
            'types'    => '',
            'method'   => 'getInheritanceFromRaml',
            'resource' => 'getResourceInheritance',
            'parent'   => 'getParentInheritance',
            'root'     => 'getRootInheritance'
        ];

        if (!isset($functions[$nodeType])) {
            // throw error
            die('dead buhhh');
        }

        return $functions[$nodeType];
    }

    public static function getInherited($item, array $inheritFrom)
    {
        $response = [];
        foreach ($inheritFrom as $nodeType => $object) {
            if (is_object($object) || is_array($object)) {
                $inheritFunction = self::mapInheritanceFunctions($nodeType);
                $response[$nodeType] = self::$inheritFunction($item, $object);
            }
        }

        return $response;
    }

    public static function getParentInheritance($item, $parent)
    {
        $method = 'get' . ucfirst($item);
        return method_exists($parent, $method) ? $parent->$method() : null;
    }

    public static function getRootInheritance($item, $root)
    {
        $method = 'get' . ucfirst($item);
        return method_exists($root, $method) ? $root->$method() : null;
    }

    public static function getResourceInheritance($item, $resource)
    {
        $method = 'get' . ucfirst($item);
        return method_exists($resource, $method) ? $resource->$method() : null;
    }

    public static function getInheritanceFromRaml($item, $raml)
    {
        return isset($raml[$item]) ? $raml[$item] : null;
    }
}