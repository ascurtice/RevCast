<?php
namespace Upload\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Upload\Form\UploadForm;
use Zend\Validator\File\Size;
use Zend\Session\Container;
use Upload\Model\Property;
use Upload\Model\Reportkey;
use Upload\Model\Lnr;

require_once '\public\Spreadsheet\Excel\Reader\reader.php';

    class HIController extends AbstractActionController
    {
    	protected $propertyTable;
    	protected $reportkeyTable;
    	protected $lnrTable;

        public function indexAction()
        {
        	error_reporting(0);
	        $session = new Container('userData');
	        $uId = $session -> userId;

	        //Get File Data
	        $fileSession = new Container('upload');
	        $filename = $fileSession->filename;
	        $path = 'C:\Program Files (x86)\Zend\Apache2\htdocs\RevCast\data\\';
	        $inputFileName = $path . $filename;

	        $fileNameExplode = explode('_', $filename);
	        $monthName = substr($fileNameExplode[2], 0, 3);
	        $month = date('m', strtotime($monthName));
	        $year = date('Y', strtotime('20' . substr($fileNameExplode[2], 3, 2)));
	        $fileDate = $year . '-' . $month . '-01';

	        $property = $this -> getPropertyTable() -> getPropertybyAbb($fileNameExplode[0]);
	        
	        $reportKey = new Reportkey();
	        $reportKey -> property_id = $property -> id; 
	        $reportKey -> report_date = $fileDate; 
	        $reportKey -> upload_by = $uId; 
	        $reportKey -> report_type = 3; 

	        $rKey = $this -> getReportkeyTable() -> saveReport($reportKey);
			
	        //Read File Data
	        $data = new \Spreadsheet_Excel_Reader();
	        $data -> read($inputFileName);

	        $reportData = $data->sheets[0];
	        $cells = $reportData['cells'];

	        $cells = array_values($cells);

	        $marketCategory = null;
	        $martketSegment = null;
	        
	     	for($i = 4; $i <= count($cells); $i++){
	     		$cells[$i] = array_values($cells[$i]);

	     		if(($cells[$i][0] == 'Rooms') && (count($cells[$i + 3]) == 1) && (!(preg_match('/.*Total/', $cells[$i -1])))){
	     			for($n = $i; $n >= 4; $n--){
	     				if((count($cells[$n]) == 1) && (count($cells[$n + 1]) == 1) && (count($cells[$n + 2]) > 1)){
	     					$marketSegment = end($cells[$n]);
	     				}
	     			}
	     			$rateCode = explode(' ', end($cells[$i - 1]));
	     			$lnr = new Lnr();
					$lnr -> reportkey = $rKey;
					$lnr -> date = $fileDate;
					$lnr -> market_category = "";
					$lnr -> market_prefix = "";
	     			$lnr -> market_segment = $marketSegment;
	     			$lnr -> rate_code = $rateCode[0];
	     			$lnr -> rate_program = end($cells[$i - 1]);
	     			$lnr -> rooms = end($cells[$i]);	     			
	     			$lnr -> adr = end($cells[$i + 1]);
	     			$lnr -> revenue = end($cells[$i + 2]);
	     			$this -> getLnrTable() -> saveReport($lnr);
	     		}
	     	} 
	     	
	     	return $this->redirect()->toRoute('report');
        }

	    public function getPropertyTable()
	    {
	        if(!$this->propertyTable){
	            $sm = $this->getServiceLocator();
	            $this->propertyTable = $sm->get('Admin\Model\PropertyTable');
	        }
	        return $this->propertyTable;
	    }

	    public function getReportkeyTable()
	     {
	        if(!$this->reportkeyTable){
	            $sm = $this->getServiceLocator();
	            $this->reportkeyTable = $sm->get('Upload\Model\ReportkeyTable');
	        }
	        return $this->reportkeyTable;
	    }

	    public function getLnrTable()
	    {
	        if(!$this->lnrTable){
	            $sm = $this->getServiceLocator();
	            $this->lnrTable = $sm->get('Upload\Model\LnrTable');
	        }
	        return $this->lnrTable;
	    }
    }