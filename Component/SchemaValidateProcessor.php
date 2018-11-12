<?php

namespace IS\CIValidatorsBundle\Component;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Table;
use IS\CIValidatorsBundle\Validator\AbstractEntityValidator;
use IS\CIValidatorsBundle\Validator\ValidatorCollection;
use Symfony\Bridge\Doctrine\RegistryInterface;

class SchemaValidateProcessor
{
    /** @var RegistryInterface */
    private $doctrine;

    /** @var Reader */
    private $reader;

    /** @var array */
    private $validators;

    /**
     * SchemaValidateProcessor constructor.
     *
     * @param RegistryInterface   $doctrine
     * @param Reader              $reader
     * @param ValidatorCollection $validatorCollection
     */
    public function __construct(RegistryInterface $doctrine, Reader $reader, ValidatorCollection $validatorCollection)
    {
        $this->doctrine   = $doctrine;
        $this->reader     = $reader;
        $this->validators = $validatorCollection->getValidators();
    }

    /**
     * @param string $option
     * @param array  $config
     *
     * @return array
     */
    public function process(string $option, array $config)
    {
        $this->validateConfigFile($config);

        $em = $this->doctrine->getManager($option);

        $metas      = $em->getMetadataFactory()->getAllMetadata();
        $violations = [];

        /** @var ClassMetadata $meta */
        foreach ($metas as $meta) {
            if ($this->isExcludeFromAnalyse($meta, $config)) {
                continue;
            }

            $exclude = $this->getListOfExcludedRules($meta, $config);

            /** @var AbstractEntityValidator $validator */
            foreach ($this->validators as $validator) {
                if (\in_array($validator->getName(), $exclude, true)) {
                    continue;
                }

                $validator->validate($meta);
            }
        }

        foreach ($this->validators as $validator) {
            foreach ($validator->getViolations() as $entity => $violationByValidator) {
                foreach ($violationByValidator as $violation) {
                    $violations[$entity][] = $violation;
                }
            }
        }

        return $violations;
    }

    /**
     * Function to exclude entities that are not tables (virtual, models, etc).
     *
     * @param ClassMetadata $meta
     * @param array         $config
     *
     * @return bool
     */
    private function isExcludeFromAnalyse(ClassMetadata $meta, array $config): bool
    {
        $isEntity = $this->reader->getClassAnnotation($meta->getReflectionClass(), Table::class);

        if (!$isEntity) { //skip Relay health and other model
            return true;
        }

        // Don't handle entities marked as "excludes_analyse" from config file
        if (isset($config['excludes_analyse']['entities'])) {
            return \in_array($meta->getName(), $config['excludes_analyse']['entities'], true);
        }

        return false;
    }

    /**
     * @param ClassMetadata $meta
     * @param array         $config
     *
     * @return array
     */
    private function getListOfExcludedRules(ClassMetadata $meta, array $config): array
    {
        $exclude = [];

        if (isset($config['excludes_analyse']['rules'])) {
            foreach ($config['excludes_analyse']['rules'] as $rule => $entitites) {
                if (\is_array($entitites) && \in_array($meta->getName(), $entitites, true)) {
                    $exclude[] = \mb_strtoupper($rule);
                }
            }
        }

        return $exclude;
    }

    /**
     * @param array $config
     */
    private function validateConfigFile(array $config)
    {
        if (!isset($config['rules'])) {
            return; //Configuration file doesn't has rules. Will Validate all rules
        }

        $rules = $config['rules'];

        /** @var AbstractEntityValidator $validator */
        foreach ($this->validators as $id => $validator) {
            foreach ($rules as $rule) {
                if (false !== \mb_strripos($validator->getName(), $rule)) {
                    continue 2;
                }
            }

            unset($this->validators[$id]);
        }
    }
}
