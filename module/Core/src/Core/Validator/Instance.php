<?php
namespace Core\Validator;

use Zend\Validator\AbstractValidator;

class Instance extends AbstractValidator
{
    const INVALID = 'msgInstance';

    protected $messageTemplates = array(
        self::INVALID => "type invalid"
    );

    /**
     * @var array
     */
    protected $messageVariables = array(
        'instanceOf' => array('options' => 'instanceOf'),
    );

    protected $options = array(
        'instanceOf' => null,
    );

    /**
     * Sets validator options
     *
     * @param  int|array|\Traversable $options
     */
    public function __construct($options = array())
    {
        parent::__construct($options);
    }

    public function setInstanceOf($instanceOf)
    {
        $this->options['instanceOf'] = $instanceOf;
    }

    public function getInstanceOf()
    {
        return $this->options['instanceOf'];
    }

    public function isValid($value)
    {
        $this->setValue($value);

        if (! $value instanceOf $this->options['instanceOf']) {
            $this->error(self::INVALID);

            return false;
        }

        return true;
    }
}
