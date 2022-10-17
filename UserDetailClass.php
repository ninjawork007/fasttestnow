<?php

class UserDetail {
    private $firstName;
    private $lastName;
    private $email;

    function __construct($fName, $lName, $email)
    {
        $this->firstName = $fName;
        $this->lastName = $lName;
        $this->email = $email;
    }
    function getUserFromAppointment() {
        $con = dbCon();
        if($this->firstName === NULL && $this->lastName === NULL) {
            $query = "SELECT * FROM tbl_appointment where email='".$this->email."' ORDER BY created_at DESC";
        } else {
            $query = "SELECT * FROM tbl_appointment where firstName='".$this->firstName."' and lastName='".$this->lastName."'  and email='".$this->email."' ORDER BY created_at DESC";
        }
        
        $result = mysqli_query($con, $query);
        $getUserFromAppointment = array();
        while (($row = mysqli_fetch_assoc($result))) {
            $getUserFromAppointment[] = $row;
        }
        return $getUserFromAppointment;
    }
    function getUserFromReport() {
        $con = dbCon();
        if($this->firstName === NULL && $this->lastName === NULL) {
            $query = <<<EOT
                    SELECT report_id, patient_id, patient_firstname, patient_lastname, name, patient_phone, patient_email, patient_birth, patient_passport, patient_gender, IF(tbl_report.report_results = 0, 'Negative', 'Positive') AS report_results, sample_taken, type_id, handled_at FROM  tbl_report
                    LEFT JOIN tbl_type ON tbl_report.type_id = tbl_type.id
                    WHERE patient_email='$this->email'
                    ORDER BY handled_at DESC
                    EOT;
        } else {
            $query = <<<EOT
                    SELECT report_id, patient_id, patient_firstname, patient_lastname, name, patient_phone, patient_email, patient_birth, patient_passport, patient_gender, IF(tbl_report.report_results = 0, 'Negative', 'Positive') AS report_results, sample_taken, type_id, handled_at FROM  tbl_report
                    LEFT JOIN tbl_type ON tbl_report.type_id = tbl_type.id
                    WHERE patient_firstname='$this->firstName' AND patient_lastname='$this->lastName' AND patient_email='$this->email'
                    ORDER BY handled_at DESC
                    EOT;
        }
        
        $result = mysqli_query($con, $query);
        $userFromReport = array();
        while (($row = mysqli_fetch_assoc($result))) {
            $userFromReport[] = $row;
        }
        return $userFromReport;
    }
    function getUserTotalInfo() {
        
        $array = array();

        $appointments = $this->getUserFromAppointment();
        $reports = $this->getUserFromReport();

        /**
         * getting pending appointment 
         * slashed because the database logic was wrong.
         */
        // if(count($reports) > 0) {

        //     if(count($appointments) > 0) {
        //         for($i = 0; $i < count($appointments); $i++) {
        //             $id = $appointments[$i]['id'];

        //             for($j = 0; $j < count($reports); $j++) {
        //                 $patient_id = $reports[$j]['patient_id'];
        //                 if($id == $patient_id) {
        //                     array_splice($appointments, $i, 1);
        //                 }
        //             }
        //         }
        //     }
        //     var_dump($appointments);die;
        //     $toBeGeneratedReports = $this->getToBeGeneratedReports($appointments);
        //     // Tie an array
        //     $array = array(
        //         "GeneratedReports"=> $reports,
        //         "ToBeGeneratedReports" => $toBeGeneratedReports
        //     );
        // } else {
        //     $toBeGeneratedReports = $this->getToBeGeneratedReports($appointments);
        //     // Tie an array
        //     $array = array(
        //         "GeneratedReports"=> $reports,
        //         "ToBeGeneratedReports" => $toBeGeneratedReports
        //     );
        // }
        
        $array = array(
                    "GeneratedReports"=> $reports,
                    "ToBeGeneratedReports" => $appointments[0]
                );
        return $array;
        
    }
    function getToBeGeneratedReports($arr = array()) {
        
        $getTestType = array();
        if(count($arr) > 0) :
        for($i = 0; $i < count($arr); $i ++) {
            $tempArr = $arr[$i];
            $typeStr = $arr[$i]['appointment_type'];
            if ( strpos( $typeStr, 'Antibody' ) !== false ) {
                $tempArr["testType"] = "Antibody";
            }
            else if ( strpos( $typeStr, 'RT-PCR' ) !== false ) {
                $tempArr["testType"] = "Accula";
            }
            else if ( strpos( $typeStr, 'Antigen' ) !== false ) {
                $tempArr["testType"] = "Antigen";
            }
            else if ( strpos( $typeStr, 'Flu' ) !== false ) {
                $tempArr["testType"] = "Flu";
            }
            else if ( strpos( $typeStr, 'Antigen' ) !== false ) {
                $tempArr["testType"] = "Antigen";
            }
            array_push($getTestType, $tempArr);
        }
        endif;
        return $getTestType;
    }
}
?>