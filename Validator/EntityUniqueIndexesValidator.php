<?php

namespace IS\CIValidatorsBundle\Validator;

use Doctrine\ORM\Mapping\ClassMetadata;
use IS\CIValidatorsBundle\Util\CaseChecker;

class EntityUniqueIndexesValidator extends AbstractEntityValidator
{
    /**
     * @param ClassMetadata $meta
     *
     * @return mixed|void
     */
    public function validate(ClassMetadata $meta)
    {
        if (!isset($meta->table['uniqueConstraints'])) {
            return;
        }

        $entityName = $meta->getName();
        $columns    = $meta->getColumnNames();
        $parts      = \explode('_', \array_shift($columns));
        $prefix     = $parts[0];

        foreach ($meta->table['uniqueConstraints'] as $key => $col) {
            $parts = \explode('_', $key);

            $this->validateLength($key, $entityName);
            $this->validateCase($key, $entityName);
            $this->validateName($parts, $key, $entityName);
            $this->validatePrefix($prefix, $parts, $key, $entityName);
        }
    }

    /**
     * @param array $parts
     *
     * @return bool
     */
    private function isCorrectName(array $parts): bool
    {
        return 4 !== \count($parts) || 'Unique' !== $parts[1] || 'Index' !== $parts[2] || !\ctype_digit($parts[3]);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    private function isCorrectLength(string $key): bool
    {
        return \mb_strlen($key) > self::MAX_INDEX_LENGTH;
    }

    /**
     * @param string $key
     * @param string $entityName
     */
    private function validateLength(string $key, string $entityName): void
    {
        if ($this->isCorrectLength($key)) {
            $this->violations[$entityName][] = 'Unique index ' . $key . ' expected to contain ' . self::PREFIX_LENGTH . ' characters';
        }
    }

    /**
     * @param string $key
     * @param string $entityName
     */
    private function validateCase(string $key, string $entityName): void
    {
        if (!CaseChecker::isSnakeTrainCase($key)) {
            $this->violations[$entityName][] = 'Unique index ' . $key . ' is not in Snake_Train_Case';
        }
    }

    /**
     * @param array  $parts
     * @param string $key
     * @param string $entityName
     */
    private function validateName(array $parts, string $key, string $entityName): void
    {
        if ($this->isCorrectName($parts)) {
            $this->violations[$entityName][] = 'Unique Index ' . $key . ' should has format <table_prefix>_Unique_Index_<number>';
        }
    }

    /**
     * @param string $prefix
     * @param array  $parts
     * @param string $key
     * @param string $entityName
     */
    private function validatePrefix(string $prefix, array $parts, string $key, string $entityName): void
    {
        if ($prefix !== $parts[0]) {
            $this->violations[$entityName][] = 'Prefix for unique index ' . $key . ' is not the same for prefix ' . $prefix . ' in a table';
        }
    }
}
