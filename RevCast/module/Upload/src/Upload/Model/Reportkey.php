<?php
namespace Upload\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class ReportKey implements InputFilterAwareInterface
{
    public $id;
    public $property_id;
    public $report_date;
    public $timestamp;
    public $upload_by;
    public $report_type;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->property_id = (isset($data['property_id'])) ? $data['property_id'] : null;
        $this->report_date = (isset($data['report_date'])) ? $data['report_date'] : null;
        $this->timestamp = (isset($data['timestamp'])) ? $data['timestamp'] : null;
        $this->upload_by = (isset($data['upload_by'])) ? $data['upload_by'] : null;
        $this->report_type = (isset($data['report_type'])) ? $data['report_type'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                'name'     => 'id',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'property_id',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'report_date',
                'required' => true,
            )));

            $inputFilter -> add($factory -> createInput(array(
                'name' => 'timestamp',
                'required' => true
            )));
                
            $inputFilter -> add($factory -> createInput(array(
                'name' => 'upload_by',
                'required' => true
            )));

            $inputFilter -> add($factory -> createInput(array(
                'name' => 'report_type',
                'required' => true
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}