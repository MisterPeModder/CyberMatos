<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class UniqueValueInEntity extends Constraint
{
    public string $message = 'This value is already used.';
    public string $entityClass;
    public string $field;

    public function __construct(array $options = null, string $entityClass = null, string $field = null, string $message = null, array $groups = null, mixed $payload = null)
    {
        parent::__construct($options ?? [], $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->entityClass = $entityClass ?? $this->entityClass;
        $this->field = $field ?? $this->field;
    }

    public function getRequiredOptions(): array
    {
        return ['entityClass', 'field'];
    }

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return get_class($this).'Validator';
    }
}
