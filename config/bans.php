<?php
/**
 * LiveStreet CMS
 * Copyright © 2013 OOO "ЛС-СОФТ"
 * 
 * ------------------------------------------------------
 * 
 * Official site: www.livestreetcms.com
 * Contact e-mail: office@livestreetcms.com
 * 
 * GNU General Public License, version 2:
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * 
 * ------------------------------------------------------
 * 
 * @link http://www.livestreetcms.com
 * @copyright 2013 OOO "ЛС-СОФТ"
 * @author Serge Pustovit (PSNet) <light.feel@gmail.com>
 * 
 */

/*
 *
 * Настройка банов
 *
 */

$config = array();

/*
 * количество банов на страницу
 */
$config['bans']['per_page'] = 10;

/*
 * Автоматически удалять старые записи банов если дата окончания бана уже прошла
 */
$config['auto_delete_old_ban_records'] = true;

/*
 * Собирать статистку по срабатываниям банов
 */
$config['gather_bans_running_stats'] = true;

/*
 * Список методов из модуля ACL для которых не нужно подсчитывать количество срабатываний бана типа "только чтение"
 * (может уменьшить нагрузку)
 */
$config['bans']['acl_exclude_methods_from_gather_stats'] = array(
	/*
	 * эти методы по нескольку раз запускаются на странице для вывода ссылок "редактировать" и "удалить" для каждого топика
	 */
	'IsAllowEditTopic',
	'IsAllowDeleteTopic',
);

return $config;

?>