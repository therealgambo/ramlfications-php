<?php

namespace TheRealGambo\Ramlfications\Parameters;

/**
 * Class Header
 *
 * Header with properties defined by the RAML spec's 'Named Parameters'
 * section, e.g.:
 *      `curl -H 'X-Some-Header: foobar' ...`
 * where `X-Some-Header` is the Header name.
 *
 * @package TheRealGambo\Ramlfications\Parameters
 */
class Header extends BaseParameter
{
    private $namedParams = [
        'description', 'type', 'enum', 'pattern', 'minimum', 'maximum', 'example',
        'default', 'required', 'displayName', 'maxLength',
        'minLength'
    ];

    private $required = false;

    public function __construct(array $raml, $key)
    {
        parent::__construct($raml, $key);

        if (isset($raml['required'])) {
            $this->setRequired($raml['required']);
        }
    }

    public function inheritTypeProperties($inheritedParams)
    {
//        params = NAMED_PARAMS + ["method"]
//        for param in inherited_param:
//            for n in params:
//                attr = getattr(self, n, None)
//                if attr is None:
//                    attr = getattr(param, n, None)
//                    setattr(self, n, attr)

        $namedParams = array_merge($this->namedParams, ['method']);

        foreach ($inheritedParams as $param) {
            foreach ($namedParams as $namedParam) {
                $functionSet = 'set' . ucfirst($namedParam);
                $functionGet = 'get' . ucfirst($namedParam);
                if (property_exists($this, $namedParam) &&
                    method_exists($param, $functionGet) &&
                    is_null($this->$namedParam)) {
                    $this->$functionSet($param->$functionGet());
                }
            }
        }
    }

    /**
     * Get value of Required
     *
     * @return bool
     */
    public function getRequired(): bool
    {
        return $this->required;
    }

    /**
     * Set the value of Required
     *
     * @param bool $required
     *
     * @return self
     */
    public function setRequired(bool $required)
    {
        $this->required = $required;
        return $this;
    }
}
