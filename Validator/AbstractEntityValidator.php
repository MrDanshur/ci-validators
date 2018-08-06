<?php

namespace IS\CIValidatorsBundle\Validator;

use Doctrine\ORM\Mapping\ClassMetadata;

abstract class AbstractEntityValidator
{
    public const MAX_TABLE_LENGTH = 20;

    public const MAX_INDEX_LENGTH = 20;

    public const MAX_COLUMN_LENGTH = 20;

    public const PREFIX_LENGTH = 3;

    protected $violations = [];

    public $prefixes = [];

    /**
     * @param ClassMetadata $meta
     *
     * @return mixed
     */
    abstract public function validate(ClassMetadata $meta);

    /**
     * @return array
     */
    public function getViolations(): array
    {
        return $this->violations;
    }

    /**
     * Get classname of Validator.
     *
     * @return null|mixed|string|string[]
     */
    public function getName()
    {
        try {
            $reflectionClass = new \ReflectionClass($this);
        } catch (\ReflectionException $e) {
            return '';
        }

        return \mb_strtoupper($reflectionClass->getShortName());
    }
}
