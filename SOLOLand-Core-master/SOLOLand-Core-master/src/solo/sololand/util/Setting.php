<?php

namespace solo\sololand\util;

use pocketmine\utils\Config;
use solo\sololand\Main;

class Setting{
	
	public static $loadAllWorldsOnEnable;
	
	private function __construct(){
		
	}
	
	public static function init(){
		self::load();
	}
	
	public static function load(){
		$config = new Config(Main::getInstance()->getDataFolder() . "setting.yml", Config::YAML, [
				"loadAllWorldsOnEnable" => true
		]);
		self::$loadAllWorldsOnEnable = (boolean) $config->get("loadAllWorldsOnEnable");
	}
}