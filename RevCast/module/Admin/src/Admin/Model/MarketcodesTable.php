<?php
namespace Admin\Model;

use Zend\Db\TableGateway\TableGateway;

class MarketcodesTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getMarketCode($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getCodebyCode($code){
        $rowset = $this -> tableGateway -> select(array('market_code' => $code));
        $row = $rowset -> current();
        return $row;
    }

    public function saveMarketCode(Marketcodes $mc)
    {
        $data = array(
            'market_code' => $mc->market_code,
            'market_code_desc' => $mc->market_code_desc,
        );

        $id = (int)$mc->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getMarketCode($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }
}