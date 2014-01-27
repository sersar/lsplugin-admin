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
 * @author Maxim Mzhelskiy <rus.engine@gmail.com>
 *
 */

/**
 * Запрещаем напрямую через браузер обращение к этому файлу.
 */
if (!class_exists('Plugin')) {
	die('Hacking attempt!');
}

class PluginArticle extends Plugin {

	protected $sPropertyTargetType='article';

	public function Init() {

	}

	public function Activate() {
		if (!$this->isTableExists('prefix_article')) {
			/**
			 * При активации выполняем SQL дамп
			 */
			$this->ExportSQL(dirname(__FILE__).'/dump.sql');
		}
		/**
		 * Создаем новый тип для дополнительных полей
		 * Третий параметр true ознает перезапись параметров, если такой тип уже есть в БД
		 */
		if (!$this->Property_CreateTargetType($this->sPropertyTargetType,array('entity'=>'PluginArticle_ModuleMain_EntityArticle','name'=>'Статьи'),true)) {
			return false;
		}
		/**
		 * Добавляем новые поля к статьям, далее пользователь может делать это через интерфейс админки
		 */
		$aProperties=array(
			array(
				'data'=>array(
					'type'=>ModuleProperty::PROPERTY_TYPE_INT,
					'title'=>'Номер',
					'code'=>'number',
					'sort'=>100
				),
				'validate_rule'=>array(
					'min'=>10
				),
				'params'=>array(),
				'additional'=>array()
			)
		);
		foreach($aProperties as $aProperty) {
			$sResultMsg=$this->Property_CreateTargetProperty($this->sPropertyTargetType,$aProperty['data'],true,$aProperty['validate_rule'],$aProperty['params'],$aProperty['additional']);
			if ($sResultMsg!==true and !is_object($sResultMsg)) {
				if (is_string($sResultMsg)) {
					$this->Message_AddErrorSingle($sResultMsg, $this->Lang_Get('error'), true);
				}
				/**
				 * Отменяем добавление типа
				 */
				$this->Property_RemoveTargetType($this->sPropertyTargetType,ModuleProperty::TARGET_STATE_NOT_ACTIVE);
				return false;
			}
		}

		return true;
	}

	public function Deactivate() {
		$this->Property_RemoveTargetType($this->sPropertyTargetType,ModuleProperty::TARGET_STATE_NOT_ACTIVE);
		return true;
	}
}