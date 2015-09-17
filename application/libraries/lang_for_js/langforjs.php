<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Langforjs
{
    /**
     * Возвращаем язык для js (ошибки, уведомления и прочее)
     * Return language js (bugs, notifications, etc.)
     * @param string $segment
     * @return mixed
     */
	public function getLang($segment = 'ru')
	{

        $lang['ru'] = [];
        $lang['en'] = [];


		
		return $lang[$segment];
	}
}
?>