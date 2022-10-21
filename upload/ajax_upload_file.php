<?php
    include('class.fileuploader.php');
	
	// initialize FileUploader
    $FileUploader = new FileUploader('files', array(
        'uploadDir' => '../uploads',
    ));
	
	// call to upload the files
    $data = $FileUploader->upload();
    // var_dump($data);die;
	// export to js
	echo json_encode($data);
	exit;