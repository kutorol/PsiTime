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

    /**
     * Получаем id юзера по email
     * Get the id of the user via email
     * @param string $email
     * @return mixed
     */
    public function checkEmail($email = '')
    {
        $q = $this->db->select('id_user, login')->where('email', $email)->get('users')->row_array();
        return $q;
    }


    private function _checkWhere($where, $where_2)
    {
        if(is_array($where))
        {
            if(is_array($where_2))
            {
                foreach($where as $k=>$v)
                    $this->db->where($where[$k], $where_2[$k]);
            }
            else
            {
                foreach($where as $k=>$v)
                    $this->db->where($where[$k], $where_2);
            }
        }
        else
        {
            if(is_array($where_2))
            {
                foreach($where_2 as $k=>$v)
                    $this->db->where($where, $where_2[$k]);
            }
            else
                $this->db->where($where, $where_2);
        }
    }
    /**
     * Обновляем юзера при множественном where
     * Update the user in the plural where
     * @param array $new - данные для обновления
     * @param $where - тут поля в базе данных (here the fields in the database)
     * @param $where_2 - а тут значения, которые ищем (but here the values that are looking for)
     */
    public function updateUser($new = array(), $where, $where_2)
    {
        $this->_checkWhere($where, $where_2);
        $this->db->update('users', $new);
        return $this->db->affected_rows();
    }


}