<?php
if (isset($_POST['file'])) {
    $file = './uploads/' . str_replace(array('/', '\\'), '', $_POST['file'] . '.pdf');
	
    if(file_exists($file))
		unlink($file);
}