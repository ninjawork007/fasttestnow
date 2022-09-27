<?php
error_reporting(E_ALL);
include('../includes/functions.php');
include('../postmark.php');

class Sendtofhod
{
    /**
     * Default column names for xl generation
     */
    private $columnNames = [
        'RecordID', 'FacilityID', 'CLIAID', 'PatientID', 'LastName', 'FirstName', 'MiddleName', 'DOB', 'SSN', 'StreetAddress', 'City', 'State', 'Zip', 'County', 'Gender', 'PhoneNumber', 'Ethnicity', 'RaceWhite', 'RaceBlack', 'RaceAmericanIndianAlaskanNative', 'RaceAsian', 'RaceNativeHawaiianOrOtherPacificIslander', 'RaceOther', 'RaceUnknown', 'RaceNoResponse', 'ProviderName', 'NPI', 'Pregnant', 'SchoolAssociation', 'SchoolName', 'MeetILIDefinition', 'PerformingLab', 'AccessionNumber', 'Pathogen', 'TestType', 'LOINC', 'LOINCShortName', 'SpecimenCollectionType', 'SpecimenSNOMED', 'SpecimenCollectedDate','Result', 'ResultSNOMED', 
    ];

    /**
     * Fax Api Username
     */
    private $faxUserName = 'fasttestnow';

    /**
     * Fax Api Password
     */
    private $faxPassword = 'Fasttestnow123!';

    /**
     * Fax Api inbound number
     */
    private $faxNumber = '7863962445';

    /**
     * Fax To Number
     */
    private $faxToNumber = '+18504146894';


    /**
     * Fax Client
     */
    private $faxClient = NULL;

    /**
     * Facility ID
     */
    private $facilityID = 4244;

    /**
     * Facility Name
     */
    private $facilityName = "HRMGTCORPDBAFASTTESTNOW";

    /**
     * Client ID
     */
    private $cliaid = '10D2214779';

    /**
     * Test Types
     */
    private $testTypes = [
        '1' => ['name' => 'PCR', 'pathogen' => 'SARS-CoV-2 (COVID-19)', 'lonic' => '94500-6', 'lonic_shortname' => 'SARS-CoV-2 RNA Resp Ql NAA+probe'],
        '2' => ['name' => 'Antigen', 'pathogen' => 'SARS-CoV-2 (COVID-19)', 'lonic' => '97097-0', 'lonic_shortname' => 'SARS-CoV-2 Ag Upper resp Ql IA.rapid'],
        '3' => ['name' => 'PCR', 'pathogen' => 'SARS-CoV-2 (COVID-19)', 'lonic' => '94500-6', 'lonic_shortname' => 'SARS-CoV-2 RNA Resp Ql NAA+probe'],
    ];

    /**
     * collection type
     */
    private $specimenCollectionType = 'Nasopharyngeal swab';

    /**
     * collection snomed
     */
    private $Specimensnomed = '258500001';

    /**
     * result snomed
     */
    private $resultSnomed = [
        '0' => '260385009',
        '1' => '10828004',
        '2' => '419984006'
    ];

    /**
     * Today Reports
     */
    private $todayReport = [];

    /**
     * Filename For XL
     */
    private $fileName = "../uploads/report";



    public function __construct()
    {
        $this->setTodayReport();
    }
    /**
     * set today reports from database
     */
    private function setTodayReport()
    {
        
        ini_set('max_execution_time', 0);
        $list=array();
$month = 8;
$year = 2022;

for($d=30; $d<= 30; $d++)
{
    $time=mktime(0,0,0,$month, $d, $year);          
    if (date('m', $time)==$month)       
        $list[]=date('Y-m-d H:i:s', $time);
}
        foreach($list as $fromDate){
            $this->todayReport = [];
            $toDate = date('Y-m-d', strtotime($fromDate));
            $values = getData("SELECT t1.*, t2.* FROM tbl_report t1, tbl_appointment t2 WHERE t1.patient_firstname = t2.firstName AND t1.patient_lastname = t2.lastName AND t1.patient_email = t2.email AND t1.type_id NOT IN('4','5') AND t1.report_created_at BETWEEN '".$fromDate."' AND '".$toDate." 11:59:59' ORDER BY t2.id");

          //echo "SELECT t1.*, t2.* FROM tbl_report t1, tbl_appointment t2 WHERE t1.patient_firstname = t2.firstName AND t1.patient_lastname = t2.lastName AND t1.patient_email = t2.email AND t1.type_id NOT IN('4','5') AND t1.report_created_at BETWEEN '" . $fromDate . "' AND '" . $toDate . " 11:59:59'"."<br /><br />"; exit;

        array_push($this->todayReport, $this->columnNames);

        foreach ($values as $key => $value) {
            array_push($this->todayReport, [
                ($key + 1),
                trim($this->facilityID),
                trim($this->cliaid),
                "",
                trim($value['patient_lastname']),
                trim($value['patient_firstname']),
                "",
                trim(date('m/d/Y',strtotime($value['patient_birth']))),
                "",
                ($value['address'] != '') ? trim($value['address']) : "Unknown",
                ($value['city'] !='') ? trim($value['city']) : "Unknown",
                "FL",
                ($value['zipcode'] != '') ? trim($this->getZipCode($value['zipcode'])) : "99999",
                "",
                trim($value['gender']) != '' ? $value['gender'] : "Unknown",
                ($value['patient_phone'] != '') ? trim($this->getPhoneNumber($value['patient_phone'])) : trim($this->faxNumber),
                "No Response",
                ($value['ethnicity'] == 'White') ? 1 : 0,
                "",
                ($value['ethnicity'] == 'American Indian or Alaska Native') ? 1 : 0,
                ($value['ethnicity'] == 'Asian') ? 1 : 0,
                ($value['ethnicity'] == 'Native Hawaiian or Other Pacific Islander') ? 1 : 0,
                ($value['ethnicity'] == 'Other') ? 1 : 0,
                "",
                ($value['ethnicity'] == '') ? 1 : 0,
                trim($value['patient_lastname']),
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                trim($this->testTypes[$value['type_id']]['pathogen']),
                trim($this->testTypes[$value['type_id']]['name']),
                trim($this->testTypes[$value['type_id']]['lonic']) ?? "",
                trim($this->testTypes[$value['type_id']]['lonic_shortname']) ?? "",
                trim($this->specimenCollectionType),
                trim($this->Specimensnomed),
                trim(date('m/d/Y', $this->getSpeDate($value['report_created_at']))),
                $value['report_results'] ? "Positive" : "Negative",
                trim($this->resultSnomed[$value['report_results']]),
            ]);
        }
        
        //$this->fileName = $this->facilityName."_".date('mdY', strtotime($fromDate))."_".time().".csv";
        $this->fileName = $this->facilityName . "_" . date('m', strtotime($fromDate)) . date('d', strtotime($fromDate)) . date('y') . "_" . time();
        $this->dataToXl(); 
        //$this->todayReport = [];
        
          }
        
    }


    private function dataToXl()
    {
        $this->array_to_csv_download($this->todayReport);
    }
    
    public function getZipCode($zipcode){
        if(is_numeric($zipcode[0]) || ($zipcode[0] == 'F' || $zipcode[0] == 'f'))
        {
            preg_match_all('/[0-9]/', $zipcode, $matches);
            $zipcode = implode('', $matches[0]); 
            $finalZip = substr(sprintf("%05d", $zipcode),0,5);
        } else {
            $finalZip = "99999";
        }
        return $finalZip;
    }
    public function getSpeDate($date){
        return strtotime($date.' - 3 days');
        $opening_date = strtotime("2022-06-01");
        $current_date = strtotime(date('Y-m-d H:i:s',time()).' -  days');
        if ($opening_date < $current_date)
        {
          return $current_date;
        }
        return $opening_date;
    }
   public function getPhoneNumber($phoneNumber) {
       if($phoneNumber == '' || (strlen($phoneNumber) < 10 ) || (strlen($phoneNumber) > 11 ) ){
           return '9999999999';
       }
       return $phoneNumber;
   }
   
   function array_to_csv_download($array, $delimiter="|") {
    //$f = fopen(__DIR__.'/Testing/Monthly-Report/'.$this->fileName, 'w'); 
    
    $splitedArray = array_chunk($this->todayReport,600);
    
    foreach ($splitedArray as $key => $splitLoop) { 
        $this->fileName = $this->fileName . $key .".csv";
        
        $f = fopen(__DIR__ . '/Monthly-Report/August/' . $this->fileName, 'w');
        foreach ($splitLoop as $csvLine => $line) {
            $return = fputcsv($f, $line, $delimiter); 
            if("\n" != $eol && 0 === fseek($f, -1, SEEK_CUR)) {
                fwrite($f, "\r\n");
            }
        }
    }
    fseek($f, 0);
   }
  }

$obj = new Sendtofhod();
