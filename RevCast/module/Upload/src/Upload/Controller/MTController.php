<?php
namespace Upload\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Upload\Form\UploadForm;
use Zend\Validator\File\Size;
use Zend\Session\Container;
use Upload\Model\Property;
use Upload\Model\Lnr;
use Upload\Model\Reportkey;

require_once '\public\Spreadsheet\Excel\Reader\reader.php';

    class MTController extends AbstractActionController
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

	        //Read File Data
	        $data = new \Spreadsheet_Excel_Reader();
	        $data -> read($inputFileName);

	        $reportData = $data->sheets[0];
	        $cells = $reportData['cells'];

	        //create reportKey
	        $reportKey = new Reportkey();
	        $fileNameExplode = explode('_', $filename);
	        $monthName = substr($fileNameExplode[2], 0, 3);
	        $month = date('m', strtotime($monthName));
        	$year = date('Y', strtotime('20' . substr($fileNameExplode[2], 3, 2)));
	        $fileDate = $year . '-' . $month . '-01';
	        
	        $property = $this -> getPropertyTable() -> getPropertybyAbb($fileNameExplode[0]);
	        
	        foreach($cells as $c){
	        	$cell = array_values($c);
	        }

	        $reportKey -> property_id = $property -> id;
	        $reportKey -> report_date = $fileDate;
	        $reportKey -> upload_by = $uId;
	        $reportKey -> report_type = 1;
	        
	       	$rKey = $this -> getReportkeyTable() -> saveReport($reportKey);
	       
	        foreach($cells as $cell){
	        	$c = array_values($cell);
	        	
	        	if (count($c) > 6){
	        		if(preg_match('/^\d+$/', $c[0])){
	        			$lnrDate = gmdate('Y-m-d', ($c[0] -25569) * 86400);
	        		} else {
	        			$lnrDate = $c[0];
	        		}
	        		$lnr = new Lnr();
	        		$lnr -> reportkey = $rKey;
					$lnr -> date = $lnrDate;
					$lnr -> market_category = $c[1];
					$lnr -> market_segment = $c[2];
					$lnr -> market_prefix = $c[3];
					$rateBreakup = explode('- ', $c[4]);
					$lnr -> rate_code = $rateBreakup[0];
					$lnr -> rate_program = $rateBreakup[1];
					$lnr -> rooms = $c[5];
					$lnr -> adr = $c[6];
					$lnr -> revenue = $c[7];
					
	        		$this -> getLnrTable() -> saveReport($lnr);
	        		
	        	}	        	
	        }
	         return $this->redirect()->toRoute('report');
        }

        function ExcelToPHP($dateValue, $ExcelBaseDate = 1900) {
		    if ($ExcelBaseDate == 1900) {
		        $myExcelBaseDate = 25569;
		        //    Adjust for the spurious 29-Feb-1900 (Day 60)
		        if ($dateValue < 60) {
		            --$myExcelBaseDate;
		        }
		    } else {
		        $myExcelBaseDate = 24107;
		    }

		    // Perform conversion
		    if ($dateValue >= 1) {
		        $utcDays = $dateValue - $myExcelBaseDate;
		        $returnValue = round($utcDays * 86400);
		        if (($returnValue <= PHP_INT_MAX) && ($returnValue >= -PHP_INT_MAX)) {
		            $returnValue = (integer) $returnValue;
		        }
		    } else {
		        $hours = round($dateValue * 24);
		        $mins = round($dateValue * 1440) - round($hours * 60);
		        $secs = round($dateValue * 86400) - round($hours * 3600) - round($mins * 60);
		        $returnValue = (integer) gmmktime($hours, $mins, $secs);
		    }

		    // Return
		    return $returnValue;
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