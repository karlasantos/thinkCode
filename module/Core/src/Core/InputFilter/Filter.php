<?php
/**
 * XC ERP
 * @copyright Copyright (c) XC Ltda
 * @author Wagner Silveira <wagnerdevel@gmail.com>
 */
namespace Core\InputFilter;

use Zend\InputFilter\InputFilter;

class Filter extends InputFilter
{
    private $fieldsName = array();

    /**
     * Add an input to the input filter
     *
     * @param  array|Traversable|InputInterface|InputFilterInterface $input
     * @param  null|string $name
     * @return InputFilter
     */
    public function add($input, $name = null)
    {
        if (is_array($input) && isset($input['label']) && isset($input['name'])) {
            $this->fieldsName[$input['name']] = $input['label'];
        }

        return parent::add($input, $name);
    }

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
            if (isset($this->fieldsName[$name])) {
                $messages[$name] = array_merge(
                    array('label' => $this->fieldsName[$name]),
                    $input->getMessages()
                );
            } else {
                $messages[$name] = $input->getMessages();
            }
        }

        return $messages;
    }
}
