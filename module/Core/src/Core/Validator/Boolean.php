<?php
namespace Core\Validator;

use Zend\Validator\AbstractValidator;

class Boolean extends AbstractValidator
{
    const MSG_BOOLEAN = 'msgBoolean';

    protected $messageTemplates = array(
        self::MSG_BOOLEAN => "'%value%' is not a boolean type"
    );

    public function isValid($value)
    {
        $this->setValue($value);

        if (! is_bool($value)) {
            $this->error(self::MSG_BOOLEAN);

            return false;
        }

        return true;
    }
}
