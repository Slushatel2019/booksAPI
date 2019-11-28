<?php
namespace SiteApp\Models;

use SiteApp\Models\DB;

class Auth {

    public function __construct($loger)
    {
        $this->log = $loger;
        $this->db = DB::getInstance();
    }
    public function signIn($a=false){
        if(!isset($_SERVER['PHP_AUTH_USER'])&&!isset($_SERVER['PHP_AUTH_PW']) 
        or $_SERVER['PHP_AUTH_USER']=='' or $_SERVER['PHP_AUTH_PW']=='' or $a) {
            header('WWW-Authenticate: Basic realm');
            return false;
        }
        return ['login'=>$_SERVER['PHP_AUTH_USER'],'password'=>$_SERVER['PHP_AUTH_PW']];
        }
        public function auth() {
           $user=$this->signIn();
           $query = "SELECT id FROM users WHERE 
           BINARY login = :login AND BINARY password = :password";
           if ($this->db->executeAuth($query,$user)===0) {
            $a=true;   
            $this->signIn($a);
           }
           return ($this->db->executeAuth($query,$user)===1) ? true : false;
        }
        
        }
