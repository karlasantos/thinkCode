<?php
namespace Core\Validator;

use DoctrineModule\Validator\NoObjectExists as DoctrineModuleNoObjectExists;

class NoObjectExists extends DoctrineModuleNoObjectExists
{
    protected $messageTemplates = array(
        self::ERROR_NO_OBJECT_FOUND    => "An object matching combination of fields was found",
    );
    /**
     * {@inheritDoc}
     */
    public function isValid($value)
    {
        if($value == null) {
            $this->error(self::ERROR_NO_OBJECT_FOUND, $value);
            return false;
        }

        $cleanedValue = $this->cleanSearchValue($value);
        $match        = $this->objectRepository->findOneBy($cleanedValue);

        if (is_object($match)) {
            return true;
        }

        $this->error(self::ERROR_NO_OBJECT_FOUND, $value);

        return false;
    }
}
