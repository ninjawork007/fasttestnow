<?php

require_once("../model/thyroid.php");
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
        $Thyroid = new Thyroid($data);
        
        $result = $Thyroid->insert($keys);
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
        $Thyroid = new Thyroid($data);
        $result = $Thyroid->update($id);
        echo json_encode($result);

        break;

      case 'delete':
        $data = '';
        $id = $_POST['id'];
        
        $Thyroid = new Thyroid($data);
        $result = $Thyroid->delete($id);
        echo json_encode($result);

        break;

      case 'findAll':
        $data = '';
        
        $Thyroid = new Thyroid($data);
        $result = $Thyroid->findAll();
        echo json_encode($result);

        break;
        
      case 'findById':
        $data = '';
        $id = $_POST['id'];

        $Thyroid = new Thyroid($data);
        $result = $Thyroid->findById($id);
        echo json_encode($result);

        break;

      case 'generatePDF':
        $data = '';
        $id = $_POST['id'];
        $type_id = 13;

        $Thyroid = new Thyroid($data);
        $data = $Thyroid->findById($id);

        $Thyroid = new Thyroid($data);
        $result = $Thyroid->generatePDF($id, $type_id);

        echo json_encode($result);

        break;
    default:
         #code...
        break;
}

