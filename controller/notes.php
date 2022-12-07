<?php

require_once("../model/notes.php");
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
        $Notes = new Notes($data);
        
        $result = $Notes->insert($keys);
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
        $Notes = new Notes($data);
        $result = $Notes->update($id);
        echo json_encode($result);

        break;

      case 'delete':
        $data = '';
        $id = $_POST['id'];
        
        $Notes = new Notes($data);
        $result = $Notes->delete($id);
        echo json_encode($result);

        break;

      case 'findAll':
        $data = '';
        
        $Notes = new Notes($data);
        $result = $Notes->findAll();
        echo json_encode($result);

        break;
        
      case 'findById':
        $data = '';
        $id = $_POST['id'];

        $Notes = new Notes($data);
        $result = $Notes->findById($id);
        echo json_encode($result);

        break;

      case 'generatePDF':
        $data = '';
        $id = $_POST['id'];
        $type_id = 12;

        $Notes = new Notes($data);
        $data = $Notes->findById($id);

        $Notes = new Notes($data);
        $result = $Notes->generatePDF($id, $type_id);

        echo json_encode($result);

        break;
    default:
         #code...
        break;
}

