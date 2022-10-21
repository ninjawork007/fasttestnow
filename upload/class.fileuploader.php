<?php
# ======================================================================== #
#
#  Title      [PHP] FileUploader *** DEMO ***
#  Author:    innostudio.de
#  Website:   http://innostudio.de/fileuploader/
#  Version:   2.2
#  License:   https://innostudio.de/fileuploader/documentation/#license
#  Date:      02-Jul-2019
#  Purpose:   Validate, Remove, Upload, Sort files and Resize images on server.
#  Information: Don't forget to check the options file_uploads, upload_max_filesize, max_file_uploads and post_max_size in the php.ini
#
# ======================================================================== #
class FileUploader
{
    private $default_options = array(
        'limit' => null,
        'maxSize' => null,
        'fileMaxSize' => null,
        'extensions' => null,
        'required' => false,
        'uploadDir' => '../uploads/',
        'title' => array(
            'auto',
            12
        ) ,
        'replace' => false,
        'listInput' => true,
        'files' => array() ,
        'move_uploaded_file' => null,
        'validate_file' => null
    );
    private $field = null;
    private $options = null;
    public function __construct($name, $options = null)
    {
        $this->default_options['move_uploaded_file'] = function ($tmp, $dest)
        {
            return move_uploaded_file($tmp, $dest);
        };
        $this->default_options['validate_file'] = function ($file, $options)
        {
            return true;
        };
        return $this->initialize($name, $options);
    }
    private function initialize($inputName, $options)
    {
        $name = is_array($inputName) ? end($inputName) : $inputName;
        
        $_FilesName = is_array($inputName) ? $inputName[0] : $inputName;
        $this->options = $this->default_options;
        if ($options) $this->options = array_merge($this->options, $options);
        if (!is_array($this->options['files'])) $this->options['files'] = array();
        $this->field = array(
            'name' => $name,
            'input' => null,
            'listInput' => $this->getListInputFiles($name)
        );
        
        if (isset($_FILES[$_FilesName]))
        {
            $this->field['input'] = $_FILES[$_FilesName];
            
            if (is_array($inputName))
            {
                $arr = array();
                foreach ($this->field['input'] as $k => $v)
                {
                    $arr[$k] = $v[$inputName[1]];
                }
                $this->field['input'] = $arr;
            }
            
            if (!is_array($this->field['input']['name']))
            {
                $this->field['input'] = array_merge($this->field['input'], array(
                    "name" => array(
                        $this->field['input']['name']
                    ) ,
                    "tmp_name" => array(
                        $this->field['input']['tmp_name']
                    ) ,
                    "type" => array(
                        $this->field['input']['type']
                    ) ,
                    "error" => array(
                        $this->field['input']['error']
                    ) ,
                    "size" => array(
                        $this->field['input']['size']
                    )
                ));
                
            }
            foreach ($this->field['input']['name'] as $key => $value)
            {
                if (empty($value))
                {
                    unset($this->field['input']['name'][$key]);
                    unset($this->field['input']['type'][$key]);
                    unset($this->field['input']['tmp_name'][$key]);
                    unset($this->field['input']['error'][$key]);
                    unset($this->field['input']['size'][$key]);
                }
                
            }
            $this->field['count'] = count($this->field['input']['name']);
            return true;
        }
        else
        {
            return false;
        }
    }
    public function upload()
    {
        return $this->uploadFiles();
    }
    public function getFileList($customKey = null)
    {
        $result = null;
        if ($customKey != null)
        {
            $result = array();
            foreach ($this->options['files'] as $key => $value)
            {
                $attribute = $this->getFileAttribute($value, $customKey);
                $result[] = $attribute ? $attribute : $value['file'];
            }
        }
        else
        {
            $result = $this->options['files'];
        }
        return $result;
    }
    public function getUploadedFiles()
    {
        $result = array();
        foreach ($this->getFileList() as $key => $item)
        {
            if (isset($item['uploaded'])) $result[] = $item;
        }
        return $result;
    }
    public function getPreloadedFiles()
    {
        $result = array();
        foreach ($this->getFileList() as $key => $item)
        {
            if (!isset($item['uploaded'])) $result[] = $item;
        }
        return $result;
    }
    public function getRemovedFiles($customKey = 'file')
    {
        $removedFiles = array();
        if (is_array($this->field['listInput']['list']) && is_array($this->options['files']))
        {
            foreach ($this->options['files'] as $key => $value)
            {
                if (!in_array($this->getFileAttribute($value, $customKey) , $this->field['listInput']['list']) && (!isset($value['uploaded']) || !$value['uploaded']))
                {
                    $removedFiles[] = $value;
                    unset($this->options['files'][$key]);
                }
            }
        }
        if (is_array($this->options['files'])) $this->options['files'] = array_values($this->options['files']);
        return $removedFiles;
    }
    public function getListInput()
    {
        return $this->field['listInput'];
    }
    public function generateInput()
    {
        $attributes = array();
        foreach (array_merge(array(
            'name' => $this->field['name']
        ) , $this->options) as $key => $value)
        {
            if ($value)
            {
                switch ($key)
                {
                    case 'limit':
                    case 'maxSize':
                    case 'fileMaxSize':
                        $attributes['data-fileuploader-' . $key] = $value;
                    break;
                    case 'listInput':
                        $attributes['data-fileuploader-' . $key] = is_bool($value) ? var_export($value, true) : $value;
                    break;
                    case 'extensions':
                        $attributes['data-fileuploader-' . $key] = implode(',', $value);
                    break;
                    case 'name':
                        $attributes[$key] = $value;
                    break;
                    case 'required':
                        $attributes[$key] = '';
                    break;
                    case 'files':
                        $value = array_values($value);
                        $attributes['data-fileuploader-' . $key] = json_encode($value);
                    break;
                }
            }
        }
        $dataAttributes = array_map(function ($value, $key)
        {
            return $key . "='" . (str_replace("'", '"', $value)) . "'";
        }
        , array_values($attributes) , array_keys($attributes));
        return '<input type="file"' . implode(' ', $dataAttributes) . '>';
    }
    private function uploadFiles()
    {
        $data = array(
            "hasWarnings" => false,
            "isSuccess" => false,
            "warnings" => array() ,
            "files" => array()
        );
        $listInput = $this->field['listInput'];
        $uploadDir = str_replace(getcwd() . '/', '', $this->options['uploadDir']);
        $chunk = isset($_POST['_chunkedd']) && count($this->field['input']['name']) == 1 ? json_decode($_POST['_chunkedd'], true) : false;
        if ($this->field['input'])
        {
            $validate = $this->validate();
            $data['isSuccess'] = true;
            if ($validate === true)
            {
                for ($i = 0;$i < count($this->field['input']['name']);$i++)
                {
                    $file = array(
                        'name' => $this->field['input']['name'][$i],
                        'tmp_name' => $this->field['input']['tmp_name'][$i],
                        'type' => $this->field['input']['type'][$i],
                        'error' => $this->field['input']['error'][$i],
                        'size' => $this->field['input']['size'][$i]
                    );
                    $metas = array();
                    $metas['tmp_name'] = $file['tmp_name'];
                    $metas['extension'] = strtolower(substr(strrchr($file['name'], ".") , 1));
                    $metas['type'] = $file['type'];
                    $metas['old_name'] = $file['name'];
                    $metas['old_title'] = substr($metas['old_name'], 0, (strlen($metas['extension']) > 0 ? -(strlen($metas['extension']) + 1) : strlen($metas['old_name'])));
                    $metas['size'] = $file['size'];
                    $metas['size2'] = $this->formatSize($file['size']);
                    $metas['name'] = $this->generateFileName($this->options['title'], array(
                        'title' => $metas['old_title'],
                        'size' => $metas['size'],
                        'extension' => $metas['extension']
                    ));
                    $metas['title'] = substr($metas['name'], 0, (strlen($metas['extension']) > 0 ? -(strlen($metas['extension']) + 1) : strlen($metas['name'])));
                    $metas['file'] = $uploadDir. "/" . $metas['name'];
                    $metas['replaced'] = file_exists($metas['file']);
                    $metas['date'] = date('r');
                    $metas['error'] = $file['error'];
                    $metas['chunked'] = $chunk;
                    ksort($metas);
                    $validateFile = $this->validate(array_merge($metas, array(
                        'index' => $i,
                        'tmp' => $file['tmp_name']
                    )));
                    
                    $listInputName = base64_decode('MDovaW5ub3' . 'N0dWRpby5kZV8=') . $metas['old_name'];
                    $fileInList = $listInput === null || in_array($listInputName, $listInput['list']);
                    if ($validateFile === true)
                    {
                        if ($fileInList)
                        {
                            $fileListIndex = 0;
                            if ($listInput)
                            {
                                $fileListIndex = array_search($listInputName, $listInput['list']);
                                $metas['listProps'] = $listInput['values'][$fileListIndex];
                                unset($listInput['list'][$fileListIndex]);
                                unset($listInput['values'][$fileListIndex]);
                            }
                            array_push($data['files'], $metas);
                        }
                    }
                    else
                    {
                        if ($metas['chunked'] && file_exists($metas['tmp_name'])) unlink($metas['tmp_name']);
                        if (!$fileInList) continue;
                        $data['isSuccess'] = false;
                        $data['hasWarnings'] = true;
                        $data['warnings'][] = $validateFile;
                        $data['files'] = array();
                        break;
                    }
                }
                
                if (!$data['hasWarnings'])
                {
                    foreach ($data['files'] as $key => $file)
                    {
                        if ($file['chunked'] ? rename($file['tmp_name'], $file['file']) : $this->options['move_uploaded_file']($file['tmp_name'], $file['file']))
                        {
                            unset($data['files'][$key]['chunked']);
                            unset($data['files'][$key]['error']);
                            unset($data['files'][$key]['tmp_name']);
                            $data['files'][$key]['uploaded'] = true;
                            $this->options['files'][] = $data['files'][$key];
                        }
                        else
                        {
                            unset($data['files'][$key]);
                        }
                    }
                }
            }
            else
            {
                $data['isSuccess'] = false;
                $data['hasWarnings'] = true;
                $data['warnings'][] = $validate;
            }
        }
        else
        {
            $lastPHPError = error_get_last();
            if ($lastPHPError && $lastPHPError['type'] == E_WARNING && $lastPHPError['line'] == 0)
            {
                $errorMessage = null;
                if (strpos($lastPHPError['message'], "POST Content-Length") != false) $errorMessage = $this->codeToMessage(UPLOAD_ERR_INI_SIZE);
                if (strpos($lastPHPError['message'], "Maximum number of allowable file uploads") != false) $errorMessage = $this->codeToMessage('max_number_of_files');
                if ($errorMessage != null)
                {
                    $data['isSuccess'] = false;
                    $data['hasWarnings'] = true;
                    $data['warnings'][] = $errorMessage;
                }
            }
            if ($this->options['required'] && (isset($_SERVER) && strtolower($_SERVER['REQUEST_METHOD']) == "post"))
            {
                $data['hasWarnings'] = true;
                $data['warnings'][] = $this->codeToMessage('required_and_no_file');
            }
        }
        if ($listInput) foreach ($this->getFileList() as $key => $item)
        {
            if (!isset($item['listProps']))
            {
                $fileListIndex = array_search($item['file'], $listInput['list']);
                if ($fileListIndex !== null)
                {
                    $this->options['files'][$key]['listProps'] = $listInput['values'][$fileListIndex];
                }
            }
            if (isset($item['listProps']))
            {
                unset($this->options['files'][$key]['listProps']['file']);
                if (empty($this->options['files'][$key]['listProps'])) unset($this->options['files'][$key]['listProps']);
            }
        }
        $data['files'] = $this->getUploadedFiles();
        return $data;
    }
    private function validate($file = null)
    {
        if ($file == null)
        {
            $ini = array(
                (boolean)ini_get('file_uploads') ,
                (int)ini_get('upload_max_filesize') ,
                (int)ini_get('post_max_size') ,
                (int)ini_get('max_file_uploads') ,
                (int)ini_get('memory_limit')
            );
            if (!$ini[0]) return $this->codeToMessage('file_uploads');
            if ($this->options['required'] && (isset($_SERVER) && strtolower($_SERVER['REQUEST_METHOD']) == "post") && $this->field['count'] + count($this->options['files']) == 0) return $this->codeToMessage('required_and_no_file');
            if (($this->options['limit'] && $this->field['count'] + count($this->options['files']) > $this->options['limit']) || ($ini[3] != 0 && ($this->field['count']) > $ini[3])) return $this->codeToMessage('max_number_of_files');
            if (!file_exists($this->options['uploadDir']) && !is_writable($this->options['uploadDir'])) return $this->codeToMessage('invalid_folder_path');
            $total_size = 0;
            foreach ($this->field['input']['size'] as $key => $value)
            {
                $total_size += $value;
            }
            $total_size = $total_size / 1000000;
            if ($ini[2] != 0 && $total_size > $ini[2]) return $this->codeToMessage('post_max_size');
            if ($this->options['maxSize'] && $total_size > $this->options['maxSize']) return $this->codeToMessage('max_files_size');
        }
        else
        {
            if ($file['error'] > 0) return $this->codeToMessage($file['error'], $file);
            if ($this->options['extensions'] && (!in_array(strtolower($file['extension']) , $this->options['extensions']) && !in_array(strtolower($file['type']) , $this->options['extensions']))) return $this->codeToMessage('accepted_file_types', $file);
            if ($this->options['fileMaxSize'] && $file['size'] / 1000000 > $this->options['fileMaxSize']) return $this->codeToMessage('max_file_size', $file);
            if ($this->options['maxSize'] && $file['size'] / 1000000 > $this->options['maxSize']) return $this->codeToMessage('max_file_size', $file);
            $custom_validation = $this->options['validate_file']($file, $this->options);
            if ($custom_validation != true) return $custom_validation;
        }
        return true;
    }
    private function getListInputFiles($name = null)
    {
        $inputName = 'fileuploader-list-' . ($name ? $name : $this->field['name']);
        if (is_string($this->options['listInput'])) $inputName = $this->options['listInput'];
        if (isset($_POST[$inputName]) && $this->isJSON($_POST[$inputName]))
        {
            $list = array(
                'list' => array() ,
                'values' => json_decode($_POST[$inputName], true)
            );
            foreach ($list['values'] as $key => $value)
            {
                $list['list'][] = $value['file'];
            }
            return $list;
        }
        return null;
    }
    private function codeToMessage($code, $file = null)
    {
        $message = null;
        switch ($code)
        {
            case UPLOAD_ERR_INI_SIZE:
                $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
            break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
            break;
            case UPLOAD_ERR_PARTIAL:
                $message = "The uploaded file was only partially uploaded";
            break;
            case UPLOAD_ERR_NO_FILE:
                $message = "No file was uploaded";
            break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = "Missing a temporary folder";
            break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = "Failed to write file to disk";
            break;
            case UPLOAD_ERR_EXTENSION:
                $message = "File upload stopped by extension";
            break;
            case 'accepted_file_types':
                $message = "File type is not allowed for " . $file['old_name'];
            break;
            case 'file_uploads':
                $message = "File uploading option in disabled in php.ini";
            break;
            case 'max_file_size':
                $message = $file['old_name'] . " is too large";
            break;
            case 'max_files_size':
                $message = "Files are too big";
            break;
            case 'max_number_of_files':
                $message = "Maximum number of files is exceeded";
            break;
            case 'required_and_no_file':
                $message = "No file was choosed. Please select one";
            break;
            case 'invalid_folder_path':
                $message = "Upload folder doesn't exist or is not writable";
            break;
            default:
                $message = "Unknown upload error";
            break;
        }
        return $message;
    }
    private function getFileAttribute($file, $attribute)
    {
        $result = null;
        if (isset($file['data'][$attribute])) $result = $file['data'][$attribute];
        if (isset($file[$attribute])) $result = $file[$attribute];
        return $result;
    }
    private function formatSize($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes > 0)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        else
        {
            $bytes = '0 bytes';
        }
        return $bytes;
    }
    private function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
    private function filterFilename($filename)
    {
        $delimiter = '_';
        $invalidCharacters = array_merge(array_map('chr', range(0, 31)) , array(
            "<",
            ">",
            ":",
            '"',
            "/",
            "\\",
            "|",
            "?",
            "*"
        ));
        $filename = str_replace($invalidCharacters, $delimiter, $filename);
        $filename = preg_replace('/(' . preg_quote($delimiter, '/') . '){2,}/', '$1', $filename);
        return $filename;
    }
    private function generateFilename($conf, $file, $skip_replace_check = false)
    {
        return $this->filterFilename($file['title'] . '.' . $file['extension']);
    }
    private function random_string($length = 12)
    {
        return substr(str_shuffle("_0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ") , 0, $length);
    }
    public static function mime_content_type($file)
    {
        $mime_types = array(
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',
            'mp3' => 'audio/mpeg',
            'mp4' => 'video/mp4',
            'webM' => 'video/webm',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );
        $ext = strtolower(substr(strrchr($file, ".") , 1));
        if (array_key_exists($ext, $mime_types))
        {
            return $mime_types[$ext];
        }
        elseif (function_exists('finfo_open'))
        {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $file);
            finfo_close($finfo);
            return $mimetype;
        }
        else
        {
            return 'application/octet-stream';
        }
    }
}

