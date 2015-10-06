<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Task_model extends CI_Model {

    /**
     * (для автокомплита)
     * Проверяем, есть ли такой юзер
     *
     * (for autocomplete)
     * Checks whether a user
     *
     * @param string $str
     * @return mixed
     */
    public function getUser($str = '', $limit = 10)
    {
        $limit = ($limit <= 0) ? 10 : $limit;
        return $this->db->select('login, name, id_user')->like('login', $str)->or_like('name', $str)->limit($limit)->get('users')->result_array();
    }




}