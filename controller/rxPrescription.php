<?php

require_once("../model/rxPrescription.php");
session_start();

switch ($_POST['method']) {
    case 'insertRxOrder':
        $data = array();
        $keys = array();

        foreach($_POST as $key=>$value)
        {
          
          if($key == 'method') {
            continue;
          } else if ($key == 'ID') {
            continue;
          }else if ($key == 'type') {
            continue;
          }  else {
            array_push($keys, $key);
            array_push($data, $value);
          }
        }
        $RxOrder = new rxPrescription($data);
        $result = $RxOrder->insert($keys);
        echo json_encode($result);

        break;

    case 'updateRxOrder':
        $data = '';
        $id = 0;
        $i = 1;
        foreach($_POST as $key=>$value)
        {
          if($key == 'method') {
            continue;
          } else if ($key == 'ID') {
            $id = $_POST[$key];
            continue;
          }else if ($key == 'type') {
            continue;
          }  else {
            $data .= "$key='$value'";
            if($i != (count($_POST)-3)) 
              $data .= ", ";
          }
          $i ++;
        }
        $RxOrder = new rxPrescription($data);
        $result = $RxOrder->update($id);
        echo json_encode($result);

        break;

      case 'deleteRxOrder':
        $data = '';
        $id = $_POST['id'];
        
        $RxOrder = new rxPrescription($data);
        $result = $RxOrder->delete($id);
        echo json_encode($result);

        break;

      case 'findAllRxOrder':
        $data = '';
        
        $RxOrder = new rxPrescription($data);
        $result = $RxOrder->findAll();
        echo json_encode($result);

        break;
        
      case 'findRxOrderById':
        $data = '';
        $id = $_POST['id'];

        $RxOrder = new rxPrescription($data);
        $result = $RxOrder->findById($id);
        echo json_encode($result);

        break;

      case 'generatePDF':
        $data = '';
        $id = $_POST['id'];
        $type_id = 6;

        $RxOrder = new rxPrescription($data);
        $data = $RxOrder->findById($id);

        $RxOrder = new rxPrescription($data);
        $result = $RxOrder->generatePDF($id, $type_id);

        echo json_encode($result);

        break;
    default:
         #code...
        break;
}

