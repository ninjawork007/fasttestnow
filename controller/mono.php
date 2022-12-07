<?php

require_once("../model/mono.php");
session_start();

switch ($_POST['method']) {
    case 'insertMono':
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
        $Mono = new Mono($data);
        
        $result = $Mono->insert($keys);
        echo json_encode($result);

        break;

    case 'updateMono':
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
        $Mono = new Mono($data);
        $result = $Mono->update($id);
        echo json_encode($result);

        break;

      case 'deleteMono':
        $data = '';
        $id = $_POST['id'];
        
        $Mono = new Mono($data);
        $result = $Mono->delete($id);
        echo json_encode($result);

        break;

      case 'findAllMono':
        $data = '';
        
        $Mono = new Mono($data);
        $result = $Mono->findAll();
        echo json_encode($result);

        break;
        
      case 'findMonoById':
        $data = '';
        $id = $_POST['id'];

        $Mono = new Mono($data);
        $result = $Mono->findById($id);
        echo json_encode($result);

        break;

      case 'generatePDF':
        $data = '';
        $id = $_POST['id'];
        $type_id = 7;

        $Mono = new Mono($data);
        $data = $Mono->findById($id);

        $Mono = new Mono($data);
        $result = $Mono->generatePDF($id, $type_id);

        echo json_encode($result);

        break;
    default:
         #code...
        break;
}

