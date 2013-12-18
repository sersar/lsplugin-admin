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
 * ACL (Access Control List)
 *
 */

/*
 * tip:
 * здесь использован хитрый трюк: цепочка наследования разрывается т.к. модуль наследует Module, а не PluginAdmin_Inherits_ModuleACL как должно быть,
 * потом через __call он сравнивает методы и если это не те методы, которые ожидаются, то вызыв передается дальше по цепочке (в PluginAdmin_Inherits_ModuleACL)
 */

/*
 * нужно создать ещё один класс т.к class_alias есть в пхп 5.3, а в лс (который может и на 5.2 работать) есть его эмулятор,
 * который обьявляет алиас как абстрактный класс, который нельзя new
 */
class Not_Abstract_PluginAdmin_Inherits_ModuleACL extends PluginAdmin_Inherits_ModuleACL {}


/*
 * tip:
 *
 * важно помнить что админка не имеет четко определенной позиции в списке активированных плагинов и поэтому могут быть другие плагины,
 * которые встанут в цепочку наследования модуля ACL и если они были после админки, то этот модуль перехватит их вызов, если после,
 * то они будут "выше" в цепочке и их методы должны будут вручную проверять бан пользователя (типа "только чтение"). Это одна неточность в логике.
 *
 * Вторая - это то, что данный перехват работает по принципу "запретить", а если какой-то плагин инвертирует свою логику и добавил в АКЛ метод "ЗапрещеноЛиДействие",
 * то выход, что админка разрешит это действие т.к. вернет false. Это вторая неточность.
 *
 * Выходит, что данный подход красиво выглядит только в оригинальной экосистеме лс (без сторонних плагинов), но имеет недостатки при использовании других плагинов т.к.:
 * нужно сделать так, чтобы админка (из-за этого модуля) активировалась всегда последней чтобы быть в цепочке наследования "сверху" и
 * все методы из АКЛ были логически типа "РазрешитьЛиДействие" т.е. задать стандарт написания методов для этого модуля, а это уже ограничение и затраты на переделку плагинов
 *
 * Поэтому проще, логичнее и в духе "ЛС" наследовать все методы из АКЛ движка, которые отвечают за действия и в них выполнять проверку на бан, чем пытаться универсализировать перехват,
 * как сделано сейчас.
 *
 * Этот способ носит больше академическую ценность.
 *
 * Данный комментарий оставляю чтобы позже не возникло желания переделать все по этому же принципу.
 *
 */


class PluginAdmin_ModuleACL extends Module {

	/*
	 * объект наследумого модуля (для вызова цепочки наследования дальше)
	 */
	private $oInheritedParentClass = null;


	public function Init() {
		/*
		 * сразу нужно получить "вторую часть" цепочки
		 */
		$this->oInheritedParentClass = new Not_Abstract_PluginAdmin_Inherits_ModuleACL($this->oEngine);

		/*
		 * это наследование
		 */
		//parent::Init();
	}


	/**
	 * Обработчик для реализации механизма "read only" банов, захватывающий на себя все разрешения в движке
	 *
	 * @param $sName		имя вызываемого метода
	 * @param $aArgs		массив аргументов
	 * @return mixed
	 */
	public function __call($sName, $aArgs) {
		/*
		 * tip: все методы, которые разрешают определенное действие (публикация топика или комментария) начинаются на "Can" или "Is" (CanCreateBlog, CanAddTopic, IsAllowEditTopic и т.п.)
		 */
		if (stripos($sName, 'Can') === 0 or stripos($sName, 'Is') === 0) {
			/*
			 * если пользователь переведен в режим "только чтение" - запретить ему любое действие
			 */
			if ($oBan = $this->PluginAdmin_Users_IsCurrentUserBannedForReadOnly()) {
				/*
				 * пополнить статистику срабатываний
				 */
				//$this->AddBanStats($oBan);//todo:
				/*
				 * сообщение пользователю в зависимости от типа бана (временный или постоянный)
				 */
				$this->Message_AddError($oBan->getBanMessageForUser(), '403');
				return false;
			}
		}
		/*
		 * продолжить вызов по цепочке
		 */
		$aArgsRef = array();
		foreach ($aArgs as $key => $v) {
			$aArgsRef[] = &$aArgs[$key];
		}
		return call_user_func_array(array($this->oInheritedParentClass, $sName), $aArgsRef);
	}

}

?>