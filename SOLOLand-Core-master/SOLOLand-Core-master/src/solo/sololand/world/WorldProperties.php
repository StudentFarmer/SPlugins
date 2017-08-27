<?php

namespace solo\sololand\world;

use pocketmine\Server;
use pocketmine\level\Level;
use pocketmine\utils\Config;

class WorldProperties{
	
	public $level;
	
	// data management
	public $config;
	public $data;
	
	public $levelPath;
	public $landPath;
	
	// properties list
	public $classProperties;
	public $worldProperties;
	public $landProperties;
	public $roomProperties;
	
	public function __construct(Level $level){
		$this->level = $level;
		
		$this->levelPath = Server::getInstance()->getDataPath() . "worlds/" . $level->getFolderName() . "/";
		$this->landPath = $this->levelPath . "lands/";
		
		@mkdir($this->levelPath);
		@mkdir($this->landPath);
		
		$this->config = new Config($this->levelPath . "properties.yml", Config::YAML, $this->getDefaultProperties());
		$this->data = $this->config->getAll();
		
		$this->classProperties = new ClassProperties($this->data["class"]);
		$this->worldProperties = new WorldSettingProperties($this->data["world"]);
		$this->landProperties = new LandProperties($this->data["land"]);
		$this->roomProperties = new RoomProperties($this->data["room"]);
	}
	
	public function getLevel(){
		return $this->level;
	}
	
	public function getLevelPath(){
		return $this->levelPath;
	}
	
	public function getLandPath(){
		return $this->landPath;
	}
	
	public function getDefaultProperties(){
		return [
				"class" => [
						"worldClass" => World::class,
						"landProviderClass" => LandProvider::class
				],
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
	
	public function getClassProperties(){
		return $this->classProperties;
	}
	
	public function getWorldProperties(){
		return $this->worldProperties;
	}
	
	public function getLandProperties(){
		return $this->landProperties;
	}
	
	public function getRoomProperties(){
		return $this->roomProperties;
	}
	
	public function save(){
		$this->config->setAll($this->data);
		$this->config->save();
	}
}






class ClassProperties{
	public $data;
	
	public function __construct(array &$data){
		$this->data = $data;
	}
	
	public function getWorldClass(){
		return $this->data["worldClass"];
	}
	
	public function setWorldClass(string $class){
		$this->data["worldClass"] = $class;
	}
	
	public function getLandProviderClass(){
		return $this->data["landProviderClass"];
	}
	
	public function setLandProviderClass(string $class){
		$this->data["landProviderClass"] = $class;
	}
}

class WorldSettingProperties{
	public $data;
	
	public function __construct(array &$data){
		$this->data = $data;
	}
	
	public function isProtected() : bool{
		return $this->data["protect"];
	}
	
	public function setProtected(bool $bool){
		$this->data["protect"] = $bool;
	}
	
	public function isInvensave() : bool{
		return $this->data["invensave"];
	}
	
	public function setInvensave(bool $bool){
		$this->data["invensave"] = $bool;
	}
	
	public function isAllowExplosion() : bool{
		return $this->data["explosion"];
	}
	
	public function setAllowExplosion(bool $bool){
		$this->data["explosion"] = $bool;
	}
	
	public function isAllowPVP() : bool{
		return $this->data["pvp"];
	}
	
	public function setAllowPVP(bool $bool){
		$this->data["pvp"] = $bool;
	}
}

class LandProperties{
	public $data;
	
	public function __construct(array &$data){
		$this->data = $data;
	}
	
	public function isAllowCreate() : bool{
		return $this->data["allowCreate"];
	}
	
	public function setAllowCreate(bool $bool){
		$this->data["allowCreate"] = $bool;
	}
	
	public function isAllowCombine() : bool{
		return $this->data["allowCombine"];
	}
	
	public function setAllowCombine(bool $bool){
		$this->data["allowCombine"] = $bool;
	}
	
	public function isAllowResize() : bool{
		return $this->data["allowResize"];
	}
	
	public function setAllowResize(bool $bool){
		$this->data["allowResize"] = $bool;
	}
	
	public function getDefaultPrice(){
		return $this->data["defaultPrice"];
	}
	
	public function setDefaultPrice($price){
		$this->data["defaultPrice"] = $price;
	}
	
	public function getPricePerBlock(){
		return $this->data["pricePerBlock"];
	}
	
	public function setPricePerBlock($price){
		$this->data["pricePerBlock"] = $price;
	}
	
	public function getMaxCountPerPlayer() : int{
		return $this->data["maxCountPerPlayer"];
	}
	
	public function setMaxCountPerPlayer(int $count){
		$this->data["maxCountPerPlayer"] = $count;
	}
	
	public function getMinLength() : int{
		return $this->data["minLength"];
	}
	
	public function setMinLength(int $length){
		$this->data["minLength"] = $length;
	}
	
	public function getMaxLength() : int{
		return $this->data["maxLength"];
	}
	
	public function setMaxLength(int $length){
		$this->data["maxLength"] = $length;
	}
}

class RoomProperties{
	public $data;
	
	public function __construct(array &$data){
		$this->data = $data;
	}
	
	public function isAllowCreate() : bool{
		return $this->data["allowCreate"];
	}
	
	public function setAllowCreate(bool $bool){
		$this->data["allowCreate"] = $bool;
	}
	
	public function getDefaultPrice(){
		return $this->data["defaultPrice"];
	}
	
	public function setDefaultPrice($price){
		$this->data["defaultPrice"] = $price;
	}
	
	public function getPricePerBlock(){
		return $this->data["pricePerBlock"];
	}
	
	public function setPricePerBlock($price){
		$this->data["pricePerBlock"] = $price;
	}
	
	public function getMaxCountPerLand() : int{
		return $this->data["maxCountPerLand"];
	}
	
	public function setMaxCountPerLand(int $count){
		$this->data["maxCountPerLand"] = $count;
	}
	
	public function getMinLength() : int{
		return $this->data["minLength"];
	}
	
	public function setMinLength(int $length){
		$this->data["minLength"] = $length;
	}
	
	public function getMaxLength() : int{
		return $this->data["maxLength"];
	}
	
	public function setMaxLength(int $length){
		$this->data["maxLength"] = $length;
	}
}