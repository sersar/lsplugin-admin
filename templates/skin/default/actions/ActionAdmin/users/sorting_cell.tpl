	{*

		Сортировка по полям с учетом фильтра

		Передаваемые переменные:

			$sCellClassName - имя класса тега th таблицы ('name')
			$sSortingOrder - имя поля для сортировки текущей ячейки ('u.user_login')
			$sLinkHtml - отображаемый хтмл ссылки ('имя')
			$sBaseUrl - базовый путь ссылки для сортировки ({router page='admin/users/list'})

		Также уже должны существовать вспомогательные переменные:

			$sReverseOrder - обратная сортировка от текущей (или той, что идет по-умолчанию в случае отсутствия выбранной)
			$sOrder - имя поля для сортировки текущее
			$sWay - текущее направление сортировки (или та, что идет по-умолчанию в случае отсутствия выбранной)

	*}

	<th class="{$sCellClassName} {if $sOrder==$sSortingOrder}active{/if}">
		{assign var="sWayForThisOrder" value="{if $sOrder==$sSortingOrder}{$sReverseOrder}{else}{$sWay}{/if}"}

		<a href="{$sBaseUrl}{request_filter
			name=array('order_field', 'order_way')
			value=array($sSortingOrder, $sWayForThisOrder)
		}">{$sLinkHtml}</a>

		<span class="current-way">
			{if $sWay=='asc'}
				&uarr;
			{elseif $sWay=='desc'}
				&darr;
			{/if}
		</span>
	</th>
