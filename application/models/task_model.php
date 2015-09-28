<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Task_model extends CI_Model {

    /**
     * Проверяем, есть ли такой юзер
     * Checks whether a user
     *
     * @param string $str
     * @return mixed
     */
    public function getUser($str = '', $limit = 10)
    {
        return $this->db->select('login, name')->like('login', $str)->limit($limit)->get('users')->result_array();
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



}