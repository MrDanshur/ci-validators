<?php

namespace IS\CIValidatorsBundle\Validator;

class ValidatorCollection
{
    /** @var array */
    private $validators;

    public function __construct()
    {
        $this->validators = [];
    }

    /**
     * @param AbstractEntityValidator $validator
     */
    public function addValidator(AbstractEntityValidator $validator): void
    {
        $this->validators[] = $validator;
    }

    /**
     * @return array
     */
    public function getValidators(): array
    {
        return $this->validators;
    }
}
