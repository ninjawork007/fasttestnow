<?php
include('../includes/functions.php');

class CustomerDetail {

    private $email;
    private $id;

    function __construct($email, $id)
    {
        $this->email = $email;
        $this->id = $id;
    }
    
    function getReportTypes() {
        $con = dbCon();
        $query = "SELECT * FROM tbl_type";
        $result = mysqli_query($con, $query);
        $types = array();
        while (($row = mysqli_fetch_assoc($result))) {
            $types[] = $row;
        }
        return $types;
    }
    function getNotes() {
        $conn = dbCon();
        $val = mysqli_query($conn,"SELECT * FROM tbl_notes LIMIT 1");
        if($val === FALSE) {
			return [];
        } else {
            $reports = array();
            if($this->id === NULL) {
                $reports = [];
            } else {
                $query = "SELECT * FROM tbl_notes WHERE appointment_id='".$this->id."'";
                $result = mysqli_query($conn, $query);
            
                while (($row = mysqli_fetch_assoc($result))) {
                    $reports[] = $row;
                }
            }
            
            return $reports;
        }
    }
    function getSoapNotes() {
        $conn = dbCon();
        $val = mysqli_query($conn,"SELECT * FROM tbl_soap LIMIT 1");
        if($val === FALSE) {
			return [];
        } else {
            $reports = array();
            if($this->id === NULL) {
                $reports = [];
            } else {
                $query = "SELECT * FROM tbl_soap WHERE appointment_id='".$this->id."'";
                $result = mysqli_query($conn, $query);
            
                while (($row = mysqli_fetch_assoc($result))) {
                    $reports[] = $row;
                }
            }
            
            return $reports;
        }
    }
    function getRxOrder() {
        $conn = dbCon();
        $val = mysqli_query($conn,"SELECT * FROM tbl_rx_order LIMIT 1");
        if($val === FALSE) {
			return [];
        } else {
            if($this->id === NULL) {
                $query = "SELECT * FROM tbl_rx_order WHERE email='".$this->email."'";
            } else {
                $query = "SELECT * FROM tbl_rx_order WHERE appointment_id='".$this->id."'";
            }
            
            $result = mysqli_query($conn, $query);
            $reports = array();
            while (($row = mysqli_fetch_assoc($result))) {
                $reports[] = $row;
            }
            return $reports;
        }
    }
    function getMono() {
        $conn = dbCon();
        $val = mysqli_query($conn,"SELECT * FROM tbl_mono LIMIT 1");
        if($val === FALSE) {
			return [];
        } else {
            if($this->id === NULL) {
                $query = "SELECT * FROM tbl_mono WHERE email='".$this->email."'";
            } else {
                $query = "SELECT * FROM tbl_mono WHERE appointment_id='".$this->id."'";
            }
            
            $result = mysqli_query($conn, $query);
            $reports = array();
            while (($row = mysqli_fetch_assoc($result))) {
                $reports[] = $row;
            }
            return $reports;
        }
    }
    function getSyphilis() {
        $conn = dbCon();
        $val = mysqli_query($conn,"SELECT * FROM tbl_syphilis LIMIT 1");
        if($val === FALSE) {
			return [];
        } else {
            if($this->id === NULL) {
                $query = "SELECT * FROM tbl_syphilis WHERE email='".$this->email."'";
            } else {
                $query = "SELECT * FROM tbl_syphilis WHERE appointment_id='".$this->id."'";
            }
            
            $result = mysqli_query($conn, $query);
            $reports = array();
            while (($row = mysqli_fetch_assoc($result))) {
                $reports[] = $row;
            }
            return $reports;
        }
    }
    function getHiv() {
        $conn = dbCon();
        $val = mysqli_query($conn,"SELECT * FROM tbl_hiv LIMIT 1");
        if($val === FALSE) {
			return [];
        } else {
            if($this->id === NULL) {
                $query = "SELECT * FROM tbl_hiv WHERE email='".$this->email."'";
            } else {
                $query = "SELECT * FROM tbl_hiv WHERE appointment_id='".$this->id."'";
            }
            
            $result = mysqli_query($conn, $query);
            $reports = array();
            while (($row = mysqli_fetch_assoc($result))) {
                $reports[] = $row;
            }
            return $reports;
        }
    }
    function getStrep() {
        $conn = dbCon();
        $val = mysqli_query($conn,"SELECT * FROM tbl_strep LIMIT 1");
        if($val === FALSE) {
			return [];
        } else {
            if($this->id === NULL) {
                $query = "SELECT * FROM tbl_strep WHERE email='".$this->email."'";
            } else {
                $query = "SELECT * FROM tbl_strep WHERE appointment_id='".$this->id."'";
            }
            
            $result = mysqli_query($conn, $query);
            $reports = array();
            while (($row = mysqli_fetch_assoc($result))) {
                $reports[] = $row;
            }
            return $reports;
        }
    }
    function getHemoglobin() {
        $conn = dbCon();
        $val = mysqli_query($conn,"SELECT * FROM tbl_hemoglobin LIMIT 1");
        if($val === FALSE) {
			return [];
        } else {
            if($this->id === NULL) {
                $query = "SELECT * FROM tbl_hemoglobin WHERE email='".$this->email."'";
            } else {
                $query = "SELECT * FROM tbl_hemoglobin WHERE appointment_id='".$this->id."'";
            }
            
            $result = mysqli_query($conn, $query);
            $reports = array();
            while (($row = mysqli_fetch_assoc($result))) {
                $reports[] = $row;
            }
            return $reports;
        }
    }
    function getRsv() {
        $conn = dbCon();
        $val = mysqli_query($conn,"SELECT * FROM tbl_rsv LIMIT 1");
        if($val === FALSE) {
			return [];
        } else {
            if($this->id === NULL) {
                $query = "SELECT * FROM tbl_rsv WHERE email='".$this->email."'";
            } else {
                $query = "SELECT * FROM tbl_rsv WHERE appointment_id='".$this->id."'";
            }
            
            $result = mysqli_query($conn, $query);
            $reports = array();
            while (($row = mysqli_fetch_assoc($result))) {
                $reports[] = $row;
            }
            return $reports;
        }
    }
    function getThyroid() {
        $conn = dbCon();
        $val = mysqli_query($conn,"SELECT * FROM tbl_thyroid LIMIT 1");
        if($val === FALSE) {
			return [];
        } else {
            if($this->id === NULL) {
                $query = "SELECT * FROM tbl_thyroid WHERE email='".$this->email."'";
            } else {
                $query = "SELECT * FROM tbl_thyroid WHERE appointment_id='".$this->id."'";
            }
            $result = mysqli_query($conn, $query);
            $reports = array();
            while (($row = mysqli_fetch_assoc($result))) {
                $reports[] = $row;
            }
            return $reports;
        }
    }
    function getUploaded() {
        $conn = dbCon();
        $val = mysqli_query($conn,"SELECT * FROM tbl_uploads LIMIT 1");
        if($val === FALSE) {
			return [];
        } else {
            if($this->id === NULL) {
                $query = "SELECT tbl_uploads.id, tbl_uploads.results, tbl_uploads.firstname, tbl_uploads.lastname, tbl_uploads.pdfLink, tbl_uploads.handled_at, tbl_type.name FROM tbl_uploads LEFT JOIN tbl_type ON tbl_uploads.type_id = tbl_type.id WHERE email='".$this->email."'";
            } else {
                $query = "SELECT tbl_uploads.id, tbl_uploads.results, tbl_uploads.firstname, tbl_uploads.lastname, tbl_uploads.pdfLink, tbl_uploads.handled_at, tbl_type.name FROM tbl_uploads LEFT JOIN tbl_type ON tbl_uploads.type_id = tbl_type.id WHERE appointment_id='".$this->id."'";
            }
            
            $result = mysqli_query($conn, $query);
            $reports = array();
            while (($row = mysqli_fetch_assoc($result))) {
                $reports[] = $row;
            }
            return $reports;
        }
    }
    function getUserFromAppointment() {
        $con = dbCon();
        if($this->id === NULL) {
            $query = "SELECT * FROM tbl_appointment where email='".$this->email."' ORDER BY created_at DESC";
        } else {
            $query = "SELECT * FROM tbl_appointment where id='".$this->id."' ORDER BY created_at DESC";
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
        if($this->id == NULL) {
            $query = <<<EOT
                    SELECT report_id, patient_id, patient_firstname, patient_lastname, name, patient_phone, patient_email, patient_birth, patient_passport, patient_gender, IF(tbl_report.report_results = 0, 'Negative', 'Positive') AS report_results, sample_taken, type_id, handled_at FROM  tbl_report
                    LEFT JOIN tbl_type ON tbl_report.type_id = tbl_type.id
                    WHERE patient_email='$this->email'
                    ORDER BY handled_at DESC
                    EOT;
        } else {
            $appointmentedUser = $this->getUserFromAppointment();
            $firstname = $appointmentedUser[0]["firstName"];
            $lastname = $appointmentedUser[0]["lastName"];
            $email = $appointmentedUser[0]["email"];
            $query = <<<EOT
                    SELECT report_id, patient_id, patient_firstname, patient_lastname, name, patient_phone, patient_email, patient_birth, patient_passport, patient_gender, IF(tbl_report.report_results = 0, 'Negative', 'Positive') AS report_results, sample_taken, type_id, handled_at FROM  tbl_report
                    LEFT JOIN tbl_type ON tbl_report.type_id = tbl_type.id
                    WHERE patient_firstname='$firstname' AND patient_lastname='$lastname' AND patient_email='$email'
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
        
        $array = array(
                    "GeneratedCovidFluReports"=>$reports,
                    "ToBeGeneratedCovidFluReports"=>$appointments,
                    "types"=>$this->getReportTypes(),
                    "RxOrder"=>$this->getRxOrder(),
                    "Mono"=>$this->getMono(),
                    "Syphilis"=>$this->getSyphilis(),
                    "Hiv"=>$this->getHiv(),
                    "Strep"=>$this->getStrep(),
                    "Hemoglobin"=>$this->getHemoglobin(),
                    "Rsv"=>$this->getRsv(),
                    "Thyroid"=>$this->getThyroid(),
                    "Uploaded"=>$this->getUploaded(),
                    "Soap"=>$this->getSoapNotes(),
                    "Notes"=>$this->getNotes()
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
            else if ( strpos( $typeStr, 'PCR' ) !== false ) {
                $tempArr["testType"] = "PCR";
            }
            array_push($getTestType, $tempArr);
        }
        endif;
        return $getTestType;
    }
}
?>