<?php

namespace App\Service;

use App\Models\Client as Model;

class Client{
    public function registerClient($aData){
        $model = (new Model);

        foreach($aData as $sColumn => $sValue){
            $model->{$sColumn} = $sValue;
        }
        
        $model->client_id = bin2hex(openssl_random_pseudo_bytes(2));
        $model->client_secret = bin2hex(openssl_random_pseudo_bytes(32));

        return $model->save() ? $model : false;
    }

    public function getClientByClientID(string $clientID){
        return (new Model)->where('client_id',$clientID)->first();
    }

    public function findById(int $id){
        return (new Model)->where('c_id', $id)->first();
    }
}