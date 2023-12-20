<?
//php_interface/init.php

use Bitrix\Main,
	Bitrix\Main\Loader,
	Bitrix\Main\EventManager;

//autoload class
Loader::registerAutoLoadClasses(null, array(   
	"Prop2ParamTK" => "/php_interface/prop2ParamTK.php"
)); 
$eventManager = EventManager::getInstance();
$eventManager->addEventHandler('catalog', '\Bitrix\Catalog\Product::OnBeforeUpdate', ["Prop2ParamTK",  "dimentions"]);

//php_interface/prop2ParamTK.php

class Prop2ParamTK{
	/**
	 * Перенести из свойств товара в параметры торгового каталога
	 *
	 */
	static function dimentions($event){
		if (1 || isset($_GET['type'], $_GET['mode']) && $_GET['type'] === 'catalog' && $_GET['mode'] === 'import') {
			// данное условие оставлено, если модификацию будет необходимо использовать только при обмене с 1С

			$id = $event->getParameter('primary')['ID'];
			$arFields = $event->getParameter('fields');

			$arSelect = Array("ID", "IBLOCK_ID", "NAME", "PROPERTY_*");
			$arFilter = Array("IBLOCK_ID"=>10, "ID" => $id);
			$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
			if($ob = $res->GetNextElement()) $arProps = $ob->GetProperties();

			$arFields["WIDTH"] =  $arProps["SHIRINA_1"]["VALUE"];
			$arFields["LENGTH"] =  $arProps["GLUBINA_1"]["VALUE"];
			$arFields["HEIGHT"] =  $arProps["VYSOTA_1"]["VALUE"];

			/**
			 * Установим полученные значения
			 */
			$result = new \Bitrix\Main\ORM\EventResult;
			$result->modifyFields($arFields);

			return  $result;
		}
	}
}