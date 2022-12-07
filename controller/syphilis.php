<?php

require_once("../model/syphilis.php");
session_start();

switch ($_POST['method']) {
    case 'insertSyphilis':
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
        $Syphilis = new Syphilis($data);
        
        $result = $Syphilis->insert($keys);
        echo json_encode($result);

        break;

    case 'updateSyphilis':
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
        $Syphilis = new Syphilis($data);
        $result = $Syphilis->update($id);
        echo json_encode($result);

        break;

      case 'deleteSyphilis':
        $data = '';
        $id = $_POST['id'];
        
        $Syphilis = new Syphilis($data);
        $result = $Syphilis->delete($id);
        echo json_encode($result);

        break;

      case 'findAllSyphilis':
        $data = '';
        
        $Syphilis = new Syphilis($data);
        $result = $Syphilis->findAll();
        echo json_encode($result);

        break;
        
      case 'findSyphilisById':
        $data = '';
        $id = $_POST['id'];

        $Syphilis = new Syphilis($data);
        $result = $Syphilis->findById($id);
        echo json_encode($result);

        break;

      case 'generatePDF':
        $data = '';
        $id = $_POST['id'];
        $type_id = 9;

        $Syphilis = new Syphilis($data);
        $data = $Syphilis->findById($id);

        $Syphilis = new Syphilis($data);
        $result = $Syphilis->generatePDF($id, $type_id);

        echo json_encode($result);

        break;
    default:
         #code...
        break;
}

