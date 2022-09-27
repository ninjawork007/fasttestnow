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
        'RecordID', 'FacilityID', 'CLIAID', 'PatientID', 'LastName', 'FirstName', 'MiddleName', 'DOB', 'SSN', 'StreetAddress', 'City', 'State', 'Zip', 'County', 'Gender', 'PhoneNumber', 'Ethnicity', 'RaceWhite', 'RaceBlack', 'RaceAmericanIndianAlaskanNative', 'RaceAsian', 'RaceNativeHawaiianOrOtherPacificIslander', 'RaceOther', 'RaceUnknown', 'RaceNoResponse', 'ProviderName', 'NPI', 'Pregnant', 'SchoolAssociation', 'SchoolName', 'MeetILIDefinition', 'PerformingLab', 'AccessionNumber', 'Pathogen', 'TestType', 'LOINC', 'LOINCShortName', 'SpecimenCollectionType', 'SpecimenSNOMED', 'SpecimenCollectedDate', 'Result', 'ResultSNOMED'
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
    private $cliaids = [
        '10D2246193',
        '10D2246183',
        '10D2246198',
        '10D2214779',
        'Las Vegas',
    ];

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
     * subject
     */
    private $subject = '';

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
        array_push($this->todayReport, $this->columnNames);
        $lastIncrementVal = 0;
        foreach ($this->cliaids as $ckey => $cliaid) {
            $fromDate = date('Y-m-d H:i:s', strtotime("yesterday"));
            $toDate = date('Y-m-d H:i:s', time());
            $values = getData("SELECT t1.*, t2.* FROM tbl_report t1, tbl_appointment t2 WHERE t1.location LIKE '%" . $cliaid . "%' AND t1.patient_firstname = t2.firstName AND t1.patient_lastname = t2.lastName AND t1.patient_email = t2.email AND t1.type_id NOT IN('4','5') AND t1.report_created_at BETWEEN '" . $fromDate . "' AND '" . $toDate . "'");
           
            if (count($values) > 1) {
                foreach ($values as $key => $value) {
                    
                array_push($this->todayReport, [
                ($key + 1) + $lastIncrementVal,
                trim($this->facilityID),
                trim($cliaid),
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
                $lastIncrementVal = $key + 1 + $lastIncrementVal;
                $this->subject = 'Daily Report for Other labs';
                // if($cliaid == 'Las Vegas'){
                //     $this->subject = 'Daily Report for Las Vegas';
                // }else{
                //     $this->subject = 'Daily Report for Other labs';
                // }
                //if($ckey >= 4) {
                //    $this->dataTocsv();
                //}
                
            } // IF ENDS HERE
        } // MASTER FOREACH ENDS HERE
        $this->dataTocsv();
        //echo '<pre>';
        //print_r($this->todayReport);
        exit;
    }


    private function dataTocsv()
    {
        //$this->fileName =  $this->facilityName . "_" . date('m') . date('d') . date('y') . "_" . time() . ".csv";
        
        $splitedArray = array_chunk($this->todayReport,600);
        foreach($splitedArray as $key => $splitLoop) {
        $this->fileName = $this->facilityName . "_" . date('mdy',strtotime("-1 days")) . "_" . time() . $key .".csv";
            
        $f = fopen(__DIR__ . '/Monthly-Report/' . $this->fileName, 'w');
            foreach ($splitLoop as $csvLine => $line) {
                fputcsv($f, $line, '|');
                if("\n" != $eol && 0 === fseek($f, -1, SEEK_CUR)) {
                    fwrite($f, "\r\n");
                }
            }
            fseek($f, 0);
            $this->sendToMail();
        }
    }

    private function sendToMail()
    {
        if (count($this->todayReport) > 1) {
        $postmark = new Postmark("d1082cae-330f-4dec-a6f7-75c2afb80081", "result@fasttestnow.health");
        $result = $postmark
            ->to('carlos@fasttestnow.net, victorpolezhaevv@gmail.com')
            ->subject($this->subject)
            ->plain_message("You've received an encrypted message from FastTestNow&#174;")
            ->html_message('Report Data')
            ->attachment($this->fileName, base64_encode(file_get_contents(__DIR__ . '/Monthly-Report/' . $this->fileName)), 'text/csv')
            ->send();
        } else {
        $postmark = new Postmark("d1082cae-330f-4dec-a6f7-75c2afb80081", "result@fasttestnow.health");
        $result = $postmark
            ->to('carlos@fasttestnow.net, victorpolezhaevv@gmail.com')
            ->subject('Daily Report for Other labs')
            ->plain_message("You've received an encrypted message from FastTestNow&#174;")
            ->html_message('There is no record today')
            ->send();
        }
    }
    public function getZipCode($zipcode)
    {
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
    public function getSpeDate($date)
    {
        return strtotime($date.' - 3 days');
        $opening_date = strtotime($date);
        $current_date = strtotime(date('Y-m-d H:i:s', time()) . ' - 2 days');

        if ($opening_date > $current_date) {
            return $current_date;
        }
        return $opening_date;
    }
    public function getPhoneNumber($phoneNumber)
    {
        if ($phoneNumber == '' || (strlen($phoneNumber) < 10) || (strlen($phoneNumber) > 11)) {
            return $this->faxNumber;
        }
        return $phoneNumber;
    }
}

$obj = new Sendtofhod();
