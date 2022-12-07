<?php

require_once("../model/hemoglobin.php");
session_start();

switch ($_POST['method']) {
    case 'insert':
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
        $Hemoglobin = new Hemoglobin($data);
        
        $result = $Hemoglobin->insert($keys);
        echo json_encode($result);

        break;

    case 'update':
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
        $Hemoglobin = new Hemoglobin($data);
        $result = $Hemoglobin->update($id);
        echo json_encode($result);

        break;

      case 'delete':
        $data = '';
        $id = $_POST['id'];
        
        $Hemoglobin = new Hemoglobin($data);
        $result = $Hemoglobin->delete($id);
        echo json_encode($result);

        break;

      case 'findAll':
        $data = '';
        
        $Hemoglobin = new Hemoglobin($data);
        $result = $Hemoglobin->findAll();
        echo json_encode($result);

        break;
        
      case 'findById':
        $data = '';
        $id = $_POST['id'];

        $Hemoglobin = new Hemoglobin($data);
        $result = $Hemoglobin->findById($id);
        echo json_encode($result);

        break;

      case 'generatePDF':
        $data = '';
        $id = $_POST['id'];
        $type_id = 10;

        $Hemoglobin = new Hemoglobin($data);
        $data = $Hemoglobin->findById($id);

        $Hemoglobin = new Hemoglobin($data);
        $result = $Hemoglobin->generatePDF($id, $type_id);

        echo json_encode($result);

        break;
    default:
         #code...
        break;
}

