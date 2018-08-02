<?php

namespace IS\CIValidatorsBundle\Validator;

use Doctrine\ORM\Mapping\ClassMetadata;
use IS\CIValidatorsBundle\Util\CaseChecker;

class EntityTableValidator extends AbstractEntityValidator
{
    /**
     * @param ClassMetadata $meta
     *
     * @return mixed|void
     */
    public function validate(ClassMetadata $meta)
    {
        $tableName  = $meta->getTableName();
        $entityName = $meta->getName();

        $this->validateLength($tableName, $entityName);
        $this->validateCase($tableName, $entityName);
    }

    /**
     * @param string $tableName
     * @param string $entityName
     */
    private function validateLength(string $tableName, string $entityName): void
    {
        if (\mb_strlen($tableName) > self::MAX_TABLE_LENGTH) {
            $this->violations[$entityName][] = 'Table name ' . $tableName . ' should contain at most ' . self::MAX_TABLE_LENGTH . ' characters';
        }
    }

    /**
     * @param string $tableName
     * @param string $entityName
     */
    private function validateCase(string $tableName, string $entityName): void
    {
        if (!CaseChecker::isSnakeTrainCase($tableName)) {
            $this->violations[$entityName][] = 'Table name ' . $tableName . ' is not in Snake_Train_Case';
        }
    }
}
