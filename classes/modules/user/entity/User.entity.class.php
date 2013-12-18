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
 *	Наследуемая сущность пользователя из движка
 */

class PluginAdmin_ModuleUser_EntityUser extends PluginAdmin_Inherits_ModuleUser_EntityUser {

	/*
	 * кешированная сущность бана на время сессии
	 */
	private $oBan = null;


	/**
	 * Проверить забанен пользователь или нет (возвращает объект бана в случае успеха)
	 *
	 * @return mixed
	 */
	public function getBanned() {
		return $this->PluginAdmin_Users_GetUserBannedByUser($this);
	}


	/**
	 * Проверить забанен пользователь или нет (возвращает объект бана в случае успеха)
	 * tip: с использованием сессионного кеша
	 *
	 * @return mixed
	 */
	public function getBannedCached() {
		if (!$this->oBan) {
			$this->oBan = $this->getBanned();
		}
		return $this->oBan;
	}


	/**
	 * Забанен ли пользователь полностью (без доступа к сайту), возвращает объект бана в случае успеха
	 *
	 * @return object|null
	 */
	public function getBannedCachedFully() {
		if ($oBan = $this->getBannedCached() and $oBan->getIsFull()) {
			return $oBan;
		}
		return null;
	}


	/**
	 * Переведен ли пользователь в режим "только чтение", без возможности что либо публиковать, возвращает объект бана в случае успеха
	 *
	 * @return object|null
	 */
	public function getBannedCachedForReadOnly() {
		if ($oBan = $this->getBannedCached() and $oBan->getIsReadOnly()) {
			return $oBan;
		}
		return null;
	}

}

?>