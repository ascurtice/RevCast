<?php
namespace Upload\Form;

use Zend\Form\Form;

class UploadForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('Upload');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype','multipart/form-data');

        $this->add(array(
        'name' => 'fileupload',
            'attributes' => array(
            'type'  => 'file',
            ),
        ));


        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
            'type'  => 'submit',
            'value' => 'Upload Now'
            ),
        ));
    }
}