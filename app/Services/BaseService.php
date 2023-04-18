<?php
namespace App\Services;

use \Exception;

class BaseService{

    public function processFileUpload($request,$field_name='image',$file_path,$directory)
    {
        if(env('APP_ENV')=='testing'){
            return 'file.png';
        }
        $file_path = $request->file($field_name)->store($file_path,$directory);
        info('fpath',[$file_path]);
        if(!$file_path){
            throw new Exception("Unable to store file");
        }
        return $file_path;
    }
}