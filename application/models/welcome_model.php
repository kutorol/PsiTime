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
        $q = $this->db->select('id_user, hash, password, status')->where('login', $login)->get('users')->row_array();

        if(!empty($q))
        {
            if($q['password'] == sha1(md5($pass.$q['hash'])))
            {
                if($q['status'] == 0)
                    return ['bad_status'=>''];
                else
                    return true;
            }
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


    /**
     * Функция проверяет массивы это или же какой то из них массив или же оба просто значения и после делает запрос в базу
     * This function checks arrays, or any of them, or an array of values, both just after making a request to the database
     * @param $where
     * @param $where_2
     */
    private function _checkWhere($where, $where_2)
    {
        if(is_array($where))
        {
            if(is_array($where_2))
            {
                foreach($where as $k=>$v)
                {
                    if(isset($where_2[$k]))
                        $this->db->where($where[$k], $where_2[$k]);
                    else
                        $this->db->where($where[$k], $where_2[count($where_2)-1]);
                }
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
     * Проверяем нужен ли нам селект
     * @param null $select
     */
    private function _checkSelect($select = null)
    {
        if($select !== null)
        {
            if(is_array($select))
                $select = implode(', ', $select);
            else
                if(is_numeric($select))
                    $select =  '*';
        }
        else
            $select =  '*';

        $this->db->select($select);
    }

    /**
     * Обновляем юзера при множественном where
     * Update the user in the plural where
     * @param array $new - данные для обновления
     * @param $where - тут поля в базе данных (here the fields in the database)
     * @param $where_2 - а тут значения, которые ищем (but here the values that are looking for)
     */
    public function updateData($new = array(), $where, $where_2, $table = 'users')
    {
        $this->_checkWhere($where, $where_2);
        $this->db->update($table, $new);
        return $this->db->affected_rows();
    }

    /**
     * Получаем результат выборки из бд
     * Get the result set from the database
     *
     * @param $table
     * @param string $where - тут поля в базе данных (here the fields in the database)
     * @param string $where_2 - а тут значения, которые ищем (but here the values that are looking for)
     * @return mixed
     */
    public function getResult($table, $where = '', $where_2 = '', $return = 'result_array', $select = null)
    {
        $this->_checkSelect($select);
        $this->_checkWhere($where, $where_2);
        return $this->db->get($table)->$return();
    }

	/**
	* Добавляем нового юзера
	*/
	public function insertUser($new = [])
	{
		$this->db->insert('users', $new);
		return $this->db->affected_rows();
	}


}