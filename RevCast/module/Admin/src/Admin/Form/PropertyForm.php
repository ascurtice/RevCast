<?php
namespace Admin\Form;

use Zend\Form\Form;

class PropertyForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('brand');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
        $this->add(array(
            'name' => 'location',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'Location',
            ),
        ));
        $this->add(array(
            'name' => 'abbreviation',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'Abbreviation',
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Go',
                'id' => 'submitbutton',
            ),
        ));
    }
}