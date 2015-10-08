<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Common_model extends CI_Model {


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
                //если оба массивы
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
                //если первый - массив
                foreach($where as $k=>$v)
                    $this->db->where($where[$k], $where_2);
            }
        }
        else
        {
            if(is_array($where_2))
            {
                //если второй - массив
                foreach($where_2 as $k=>$v)
                    $this->db->where($where, $where_2[$k]);
            }
            else
                $this->db->where($where, $where_2); //оба строки
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
     * Обновляем базу
     * Update db
     *
     * @param array $new - данные для обновления (update data)
     * @param $where - тут поля в базе данных (here the fields in the database)
     * @param $where_2 - а тут значения, которые ищем (but here the values that are looking for)
     * @param $table - таблица, в которой обновляем
     * @param $is_in - если true, то делаем where_in
     * @param $return - если true, то возвращаем данные
     */
    public function updateData($new = array(), $where, $where_2, $table = 'users', $return = false, $is_in = false)
    {
        if($is_in === false)
            $this->_checkWhere($where, $where_2);
        else
            $this->db->where_in($where, $where_2);
        $this->db->update($table, $new);
        if($return === true)
            return $this->db->affected_rows();
    }

    /**
     * Получаем результат выборки из бд
     * Get the result set from the database
     *
     * @param $table
     * @param string $where - тут поля в базе данных (here the fields in the database)
     * @param string $where_2 - а тут значения, которые ищем (but here the values that are looking for)
     * @param $return - в каком виде возвращать (result_array для многих строк, row_array для одной строки)
     * @param $select - какие столбцы хотим получить
     * @param $order_1 - по какому столбцу сортировать
     * @param $order_2 -  с каким аттрибутом сортировать
     * @param $is_in - если true, то делаем where_in
     * @return mixed
     */
    public function getResult($table, $where = '', $where_2 = '', $return = 'result_array', $select = null, $order_1 = null, $order_2 = 'desc', $is_in = false)
    {
        $this->_checkSelect($select);

        if($is_in === false)
            $this->_checkWhere($where, $where_2);
        else
            $this->db->where_in($where, $where_2);

        if($order_1 !== null)
            $this->db->order_by($order_1, $order_2);

        return $this->db->get($table)->$return();
    }




    /**
     * Добавляем в базу данные и если нужно, возвращаем определенные данные
     * Adding data to the database and, if necessary, return certain data
     * @param $table
     * @param array $new
     * @param bool $return
     * @param bool $last_id
     * @return mixed
     */
    public function insertData($table, $new = [], $return = false, $last_id = false)
    {
        $this->db->insert($table, $new);

        if($last_id === true)
            return $this->db->insert_id();

        if($return === true)
            return $this->_returnOperation();
    }

    /**
     * Удаляем данные
     * @param $table
     * @param string $where
     * @param string $where_2
     * @param bool $return
     * @param $is_in - если true, то делаем where_in
     * @return mixed
     */
    public function deleteData($table, $where = '', $where_2 = '', $return = false, $is_in = false)
    {
        if($is_in === false)
            $this->_checkWhere($where, $where_2);
        else
            $this->db->where_in($where, $where_2);

        $this->db->delete($table);

        if($return === true)
            return $this->_returnOperation();
    }

    /**
     * Возвращает количество проделанных операций с бд
     * Returns the number of performed transactions to the database
     * @return mixed
     */
    private function _returnOperation()
    {
        return $this->db->affected_rows();
    }

}