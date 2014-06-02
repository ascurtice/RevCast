<?php
namespace Upload\Model;

use Zend\Db\TableGateway\TableGateway;
 use Zend\Db\Sql\Where;
    use Zend\Db\Sql\Sql;

class LnrTable
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

    public function getData($reportKey){
        $resultSet = $this -> tableGateway -> select(array('reportkey' => $reportKey));
        return $resultSet;
    }

    public function saveReport(Lnr $report)
    {
        $data = array(
            'reportkey' => $report -> reportkey,
            'date' => $report -> date,
            'market_category' => $report -> market_category,
            'market_segment' => $report -> market_segment,
            'market_prefix' => $report -> market_prefix,
            'rate_code' => $report -> rate_code,
            'rate_program' => $report -> rate_program,
            'rooms' => $report -> rooms,
            'adr' => $report -> adr,
            'revenue' => $report -> revenue,
        );

        $id = (int)$report->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getReport($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }

    
}