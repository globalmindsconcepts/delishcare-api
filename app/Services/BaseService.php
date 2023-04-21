<?php
namespace App\Services;

use \Exception;
use Illuminate\Support\Facades\Log;

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

    public function logger(Exception $e,string $msg)
    {
        Log::error($msg, [$e]);
        $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
        return [ 'message' => $message, 'status' => 500];
    }
}