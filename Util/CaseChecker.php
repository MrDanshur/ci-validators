<?php

namespace IS\CIValidatorsBundle\Util;

class CaseChecker
{
    /**
     * Check if string in format Snake_Camel_Case.
     * Used in table names.
     *
     * @param string $str
     *
     * @return bool
     */
    public static function isSnakeTrainCase(string $str): bool
    {
        $parts = \explode('_', $str);
        if (\count($parts) < 2) {
            return false;
        }

        foreach ($parts as $elem) {
            if (\ucfirst($elem) !== $elem) {
                return false;
            }
        }

        return true;
    }
}
