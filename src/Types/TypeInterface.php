<?php

namespace TheRealGambo\Ramlfications\Types;

/**
 * Interface for RAML types
 *
 * @author Melvin Loos <m.loos@infopact.nl>
 */
interface TypeInterface
{
    /**
     * Returns the name of the Type.
     **/
    public function getName();

    /**
     * Returns true if type discriminator matched discriminatorValue for class
     */
    public function discriminate($value);

    /**
     * @param $value
     */
    public function validate($value);
}