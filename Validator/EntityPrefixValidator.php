<?php

namespace IS\CIValidatorsBundle\Validator;

use Doctrine\ORM\Mapping\ClassMetadata;

class EntityPrefixValidator extends AbstractEntityValidator
{
    /**
     * @param ClassMetadata $meta
     *
     * @return mixed|void
     */
    public function validate(ClassMetadata $meta)
    {
        $columns    = $meta->getColumnNames();
        $entityName = $meta->getName();
        $parts      = \explode('_', \array_shift($columns));
        $prefix     = $parts[0];

        $this->validateLength($prefix, $entityName);
        $this->validateUniqueness($prefix, $entityName);
    }

    /**
     * @param string $prefix
     * @param string $entityName
     */
    private function validateLength(string $prefix, string $entityName): void
    {
        if (self::PREFIX_LENGTH !== \mb_strlen($prefix)) {
            $this->violations[$entityName][] = 'Prefix ' . $prefix . ' expected to contain ' . self::PREFIX_LENGTH . ' characters';
        }
    }

    /**
     * @param string $prefix
     * @param string $entityName
     */
    private function validateUniqueness(string $prefix, string $entityName): void
    {
        if (\in_array($prefix, $this->prefixes, true)) {
            $this->violations[$entityName][] = 'Prefix ' . $prefix . ' is not unique';

            return;
        }
        $this->prefixes[] = $prefix;
    }
}
