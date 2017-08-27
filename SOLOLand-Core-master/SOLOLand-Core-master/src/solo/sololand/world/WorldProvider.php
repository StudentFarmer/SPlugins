<?php

namespace solo\sololand\world;

use pocketmine\utils\Config;

class WorldProvider implements IProvider{
	
	public $path;
	
	public function __construct(string $path){
		$this->path = $path;
	}
	
	public function getDefaultProperties() : array{
		return [
				"world" => [
						"protect" => true,
						"invensave" => true,
						"explosion" => false,
						"pvp" => false
				],
				"land" => [
						"allowCreate" => false,
						"allowCombine" => true,
						"allowResize" => true,
						"defaultPrice" => 20000,
						"pricePerBlock" => 20,
						"maxCountPerPlayer" => 4,
						"minLength" => 5,
						"maxLength" => 100
				],
				"room" => [
						"allowCreate" => true,
						"defaultPrice" => 4000,
						"pricePerBlock" => 10,
						"maxCountPerLand" => 50,
						"minLength" => 3,
						"maxLength" => 50
				]
		];
	}
	
	public function load($condition = null) : array{
		$config = new Config($this->path . "properties.yml", Config::YAML, $this->getDefaultProperties());
		return $config->getAll();
	}
	
	public function save(array $properties, $condition = null){
		$config = new Config($this->path . "properties.yml", Config::YAML);
		$config->setAll($properties);
		$config->save();
	}
}