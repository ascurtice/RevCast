<?php
namespace Upload\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Lnr implements InputFilterAwareInterface
{

    public $id;
	public $reportkey;
	public $date;
	public $market_category;
	public $market_segment;
	public $market_prefix;
	public $rate_code;
	public $rate_program;
	public $rooms;
	public $adr;
	public $revenue;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this -> id = (isset($data['id'])) ? $data['id'] : null;
		$this -> reportkey = (isset($data['reportkey'])) ? $data['reportkey'] : null;
		$this -> date = (isset($data['date'])) ? $data['date'] : null;
		$this -> market_category = (isset($data['market_category'])) ? $data['market_category'] : null;
		$this -> market_segment = (isset($data['market_segment'])) ? $data['market_segment'] : null;
		$this -> market_prefix = (isset($data['market_prefix'])) ? $data['market_prefix'] : null;
		$this -> rate_code = (isset($data['rate_code'])) ? $data['rate_code'] : null;
		$this -> rate_program = (isset($data['rate_program'])) ? $data['rate_program'] : null;
		$this -> rooms = (isset($data['rooms'])) ? $data['rooms'] : null;
		$this -> adr = (isset($data['adr'])) ? $data['adr'] : null;
		$this -> revenue = (isset($data['revenue'])) ? $data['revenue'] : null;
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
			$inputFilter->add(
                $factory->createInput(array(
                    'name'     => 'reportkey',
                    'required' => false,
                ))
            );
			$inputFilter->add(
                $factory->createInput(array(
                    'name'     => 'date',
                    'required' => false,
                ))
            );
			$inputFilter->add(
                $factory->createInput(array(
                    'name'     => 'market_category',
                    'required' => false,
                ))
            );
			$inputFilter->add(
                $factory->createInput(array(
                    'name'     => 'market_segment',
                    'required' => false,
                ))
            );
			$inputFilter->add(
                $factory->createInput(array(
                    'name'     => 'market_prefix',
                    'required' => false,
                ))
            );
			$inputFilter->add(
                $factory->createInput(array(
                    'name'     => 'rate_code',
                    'required' => false,
                ))
            );
			$inputFilter->add(
                $factory->createInput(array(
                    'name'     => 'rate_program',
                    'required' => false,
                ))
            );
			$inputFilter->add(
                $factory->createInput(array(
                    'name'     => 'rooms',
                    'required' => false,
                ))
            );
			$inputFilter->add(
                $factory->createInput(array(
                    'name'     => 'adr',
                    'required' => false,
                ))
            );
			$inputFilter->add(
                $factory->createInput(array(
                    'name'     => 'revenue',
                    'required' => false,
                ))
            );

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}