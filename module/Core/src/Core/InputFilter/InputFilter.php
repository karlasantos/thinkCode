<?php
/**
 * XC ERP
 * @copyright Copyright (c) XC Ltda
 * @author Wagner Silveira <wagnerdevel@gmail.com>
 */
namespace Core\InputFilter;

use Zend\InputFilter\InputFilter as Filter;

class InputFilter extends Filter
{
    /**
     * Return a list of validation failure messages
     *
     * Should return an associative array of named input/message list pairs.
     * Pairs should only be returned for inputs that failed validation.
     *
     * @return array
     */
    public function getMessages()
    {
        $messages = array();
        foreach ($this->getInvalidInput() as $name => $input) {
            $message = $input->getMessages();

            if (is_array($message)) {
                reset($message);
                $message = current($message);
            }

            $messages[$name] = $message;
        }

        return $messages;
    }
}
