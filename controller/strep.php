<?php

require_once("../model/strep.php");
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
        $Strep = new Strep($data);
        
        $result = $Strep->insert($keys);
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
        $Strep = new Strep($data);
        $result = $Strep->update($id);
        echo json_encode($result);

        break;

      case 'delete':
        $data = '';
        $id = $_POST['id'];
        
        $Strep = new Strep($data);
        $result = $Strep->delete($id);
        echo json_encode($result);

        break;

      case 'findAll':
        $data = '';
        
        $Strep = new Strep($data);
        $result = $Strep->findAll();
        echo json_encode($result);

        break;
        
      case 'findById':
        $data = '';
        $id = $_POST['id'];

        $Strep = new Strep($data);
        $result = $Strep->findById($id);
        echo json_encode($result);

        break;

      case 'generatePDF':
        $data = '';
        $id = $_POST['id'];
        $type_id = 8;

        $Strep = new Strep($data);
        $data = $Strep->findById($id);

        $Strep = new Strep($data);
        $result = $Strep->generatePDF($id, $type_id);

        echo json_encode($result);

        break;
    default:
         #code...
        break;
}

