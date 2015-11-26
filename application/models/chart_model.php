<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Работа с бд для графиков
 * Working with databases for charts
 *
 * Class Chart_model
 */
class Chart_model extends CI_Model {


    /**
     * Возвращаем только те задачи, которые есть только в моих проектах или же в тех проектах, в которые я приписан
     * Returns only those tasks that are only in my projects or in those projects in which I was assigned
     * @param $performer_id - ид исполнителя
     * @param $projects - ид проектов, где есть мои задачи
     * @return mixed
     */
    public function getAllTaskForMyProjects($performer_id, $projects)
    {
        $sql = "SELECT task.project_id, task.pause, task.pause_for_complite, users.hoursInDayToWork, users.name, users.login
                FROM task
                LEFT JOIN users ON (users.id_user = task.performer_id)
                WHERE task.performer_id = ".$performer_id."
                AND task.status = 2
                AND task.id_task IN(
                    SELECT task.id_task FROM task WHERE task.project_id IN(".$projects.")
                )";
        return $this->db->query($sql)->result_array();
    }


    /**
     * Дополнительные данные, которые мы хотим получить вместе с каждой задачей
     * Additional information that we want to get along with each task
     */
    public function forMyTaskJoin()
    {
        $this->db->join("projects", "projects.id_project = task.project_id", "left");
        $this->db->join("complexity", "complexity.id_complexity = task.complexity_id", "left");
        $this->db->join("priority", "priority.id_priority = task.priority_id", "left");
    }

}