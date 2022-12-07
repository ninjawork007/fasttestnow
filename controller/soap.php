<?php

require_once("../model/soap.php");
session_start();

switch ($_POST['method']) {
    case 'insert':
        $data = array();
        $keys = array();

        foreach($_POST as $key=>$value)
        {
          
          if($key == 'method') {
            continue;
          }  else {
            array_push($keys, $key);
            array_push($data, $value);
          }
        }
        $Soap = new Soap($data);
        
        $result = $Soap->insert($keys);
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
        $Soap = new Soap($data);
        $result = $Soap->update($id);
        echo json_encode($result);

        break;

      case 'delete':
        $data = '';
        $id = $_POST['id'];
        
        $Soap = new Soap($data);
        $result = $Soap->delete($id);
        echo json_encode($result);

        break;

      case 'findAll':
        $data = '';
        
        $Soap = new Soap($data);
        $result = $Soap->findAll();
        echo json_encode($result);

        break;
        
      case 'findById':
        $data = '';
        $id = $_POST['id'];

        $Soap = new Soap($data);
        $result = $Soap->findById($id);
        echo json_encode($result);

        break;

      case 'generatePDF':
        $data = '';
        $id = $_POST['id'];
        $type_id = 12;

        $Soap = new Soap($data);
        $data = $Soap->findById($id);

        $Soap = new Soap($data);
        $result = $Soap->generatePDF($id, $type_id);

        echo json_encode($result);

        break;
    default:
         #code...
        break;
}

