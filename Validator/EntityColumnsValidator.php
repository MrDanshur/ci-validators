<?php

namespace IS\CIValidatorsBundle\Validator;

use Doctrine\ORM\Mapping\ClassMetadata;
use IS\CIValidatorsBundle\Util\CaseChecker;

class EntityColumnsValidator extends AbstractEntityValidator
{
    /**
     * @param ClassMetadata $meta
     *
     * @return mixed|void
     */
    public function validate(ClassMetadata $meta)
    {
        $entityName = $meta->getName();
        $columns    = $meta->getColumnNames();
        $parts      = \explode('_', \array_shift($columns));
        $prefix     = $parts[0];

        foreach ($columns as $column) {
            $this->validateLength($column, $entityName);
            $this->validateCase($column, $entityName);
            $this->validatePrefix($column, $prefix, $entityName);
        }
    }

    /**
     * @param string $column
     * @param string $entityName
     */
    private function validateLength(string $column, string $entityName): void
    {
        if (\mb_strlen($column) > self::MAX_TABLE_LENGTH) {
            $this->violations[$entityName][] = 'Column ' . $column . ' should contain at most ' . self::MAX_TABLE_LENGTH . ' characters';
        }
    }

    /**
     * @param string $column
     * @param string $entityName
     */
    private function validateCase(string $column, string $entityName): void
    {
        if (!CaseChecker::isSnakeTrainCase($column)) {
            $this->violations[$entityName][] = 'Column ' . $column . ' is not in Snake_Train_Case';
        }
    }

    /**
     * @param string $column
     * @param string $prefix
     * @param string $entityName
     */
    private function validatePrefix(string $column, string $prefix, string $entityName): void
    {
        $parts     = \explode('_', $column);
        $colPrefix = $parts[0];

        if ($colPrefix !== $prefix) {
            $this->violations[$entityName][] = 'Prefix for column ' . $column . ' is not the same for prefix ' . $prefix . ' in a table';
        }
    }
}
