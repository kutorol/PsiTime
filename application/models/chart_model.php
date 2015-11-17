<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Chart_model extends CI_Model {


    public function getCountComplexity($idUser, $segment = 'ru')
    {
        return $this->db->select('task.complexity_id, task.status, complexity.name_complexity_'.$segment)
                    ->where('task.performer_id', $idUser)
                    ->join("complexity", "complexity.id_complexity = task.complexity_id", "left")
                    ->order_by("task.complexity_id", "asc")
                    ->get("task")
                    ->result_array();
/*
        return $this->db->select('task.complexity_id, COUNT(*) as count_complexity, task.status, complexity.name_complexity_'.$segment)
                    ->where('task.performer_id', $idUser)
                    ->group_by("task.complexity_id")
                    ->join("complexity", "complexity.id_complexity = task.complexity_id", "left")
                    ->order_by("task.complexity_id", "asc")
                    ->get("task")
                    ->result_array();*/
    }

}