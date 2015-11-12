<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Task_model extends CI_Model {

    /**
     * (для автокомплита)
     * Проверяем, есть ли такой юзер
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

    /**
     * Получаем все задачи для всех проектов
     * We get all the tasks for all projects
     * @param array $allIdProjects
     * @param string $segment
     * @param array $config - тут передаются данные для постраничной навигации
     * @return mixed
     */
    public function getAllTasks($allIdProjects = [], $segment = 'ru', $config = [])
    {
        if(!empty($config))
        {
            $this->db->where_in('task.project_id', $allIdProjects);
            $this->_additionalInfoTask($segment);
            return $this->db->get('task', $config[0], $config[1])->result_array();
        }
        else
            return $this->db->where_in('project_id', $allIdProjects)->select('id_task')->get('task')->num_rows();
    }

    /**
     * Тут общая информация, получаемая при получении всех заданий и при получении только одного задания
     * There is general information obtained in the preparation of all tasks and getting only one task
     * @param string $segment
     */
    private function _additionalInfoTask($segment = 'ru')
    {
        $this->db->select(
                'task.id_task, task.complexity_id, task.user_id, task.title, task.status, task.performer_id, task.time_for_complete,
                                        task.time_for_complete_value, complexity.id_complexity, complexity.name_complexity_'.$segment.', complexity.color, users.name, users.login, users.img as user_img,
                                        users2.name as name_performer, users2.img as img_performer, users2.login as login_performer, priority.title_'.$segment.' as title_priority, priority.icon as icon_priority, priority.color as color_priority,
                                        priority.id_priority, projects.title as title_project')
                ->join('complexity', 'complexity.id_complexity = task.complexity_id', 'left')
                ->join('users', 'users.id_user = task.user_id', 'left')
                ->join('users users2', 'users2.id_user = task.performer_id', 'left')
                ->join('priority', 'priority.id_priority = task.priority_id', 'left')
                ->join('projects', 'projects.id_project = task.project_id', 'left')
                ->order_by('task.id_task', 'desc');
    }

    /**
     * Получаем все данные по одному заданию
     * We get all the data on one task
     * @param int $idTask
     * @param string $segment
     * @return mixed
     */
    public function getInfoTask($idTask = 0, $segment = 'ru')
    {
        $this->db->where('task.id_task', $idTask);
        $this->_additionalInfoTask($segment);
        //дополнительно получаем еще некоторые значения
        $this->db->select("task.text, task.time_add, task.time_start, task.time_end, task.project_id, projects.team_ids, task.pause_for_complite, task.pause");
        return $this->db->get('task')->row_array();
    }


}