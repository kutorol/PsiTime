<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Welcome_model extends CI_Model {

    /**
     * Проверяем, есть ли такой юзер
     * Checks whether a user
     * @param string $login
     * @param string $pass
     * @return bool
     */
    public function checkUser($login = '', $pass = '')
    {
        $q = $this->db->select('id_user, hash, password')->where('login', $login)->get('users')->row_array();
        if(!empty($q))
        {
            if($q['password'] == sha1(md5($pass.$q['hash'])))
                return true;
        }

        return false;
    }


}