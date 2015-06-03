<?php

class User extends Phalcon\Mvc\Model
{
    public function initialize()
    {
        $this->belongsTo("idAccount", "Account", "idAccount", array(
            "foreignKey" => true,
        ));
    }
}
