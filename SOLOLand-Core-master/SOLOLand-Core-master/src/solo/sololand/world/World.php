<?php

namespace solo\sololand\world;

use pocketmine\Server;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\level\generator\Generator;
use pocketmine\utils\Config;
use solo\sololand\Main;
use solo\sololand\event\world\WorldCreationEvent;
use solo\sololand\event\world\WorldInitEvent;
use solo\solocore\util\Debug;

class World{
	
	public static $classes = [];
	public static $worlds = [];
	
	public static function registerWorldClass($generatorName, $worldClass, $landProviderClass = null){
		self::$classes[$generatorName] = [
				"worldClass" => $worldClass
		];
		if($landProviderClass !== null){
			self::$classes[$generatorName]["landProviderClass"] = $landProviderClass;
		}
	}
	
	public static function createWorld($worldName, $generator, $options) : bool{
		if(self::loadWorld($worldName)){
			return false;
		}
		$worldClass = self::$classes[$generator]["worldClass"] ?? World::class;
		$landProviderClass = self::$classes[$generator]["landProviderClass"] ?? LandProvider::class;
		
		$levelPath = Server::getInstance()->getDataPath() . "worlds/" . $worldName . "/";
		@mkdir($levelPath);
		
		$config = new Config($levelPath . "properties.yml", Config::YAML, [
				"class" => [
						"worldClass" => $worldClass,
						"landProviderClass" => $landProviderClass
				]
		]);
		$config->save();
		
		return Server::getInstance()->generateLevel($worldName, null, Generator::getGenerator($generator), $options);
	}
	
	public static function loadWorld($level) : bool{
		$server = Server::getInstance();
		if(!$level instanceof Level){
			$success = $server->loadLevel($level);
			if(!$success){
				return false;
			}
			$level = $server->getLevelByName($level);
		}
		if($level === null){
			return false;
		}
		if(isset(self::$worlds[$level->getFolderName()])){
			return false;
		}
		$worldProperties = new WorldProperties($level);
		
		//$generator = $level->getProvider()->getGenerator();
		//$ev = new WorldCreationEvent(
		//		$level,
		//		self::$classes[$generator]["world"] ?? World::class,
		//		self::$classes[$generator]["worldProvider"] ?? WorldProvider::class,
		//		self::$classes[$generator]["landProvider"] ?? LandProvider::class,
		//		self::$classes[$generator]["landManager"] ?? LandManager::class
		//		);
		//Server::getInstance()->getPluginManager()->callEvent($ev);
		//if($ev->isCancelled()){
		//	return false;
		//}
		
		//$worldClass = $ev->getWorldClass();
		//$worldProviderClass = $ev->getWorldProviderClass();
		//$landProviderClass = $ev->getLandProviderClass();
		//$landManagerClass = $ev->getLandManagerClass();
		
		$worldClass = $worldProperties->getClassProperties()->getWorldClass();
		if(!class_exists($worldClass)){
			Debug::critical(Main::getInstance(), $level->getFolderName() . " 월드를 로드하던 중 에러가 발생하였습니다 : " . $worldClass . " 클래스가 존재하지 않습니다.");
			$worldClass = World::class;
		}
		
		$world = new $worldClass($worldProperties);
		
		$ev = new WorldInitEvent($world);
		$server->getPluginManager()->callEvent($ev);
		if($ev->isCancelled()){
			return false;
		}
		
		self::$worlds[$world->getName()] = $world;
		return true;
	}
	
	public static function unloadWorld(World $world) : bool{
		if(isset(self::$worlds[$world->getName()])){
			self::$worlds[$world->getName()]->save();
			unset(self::$worlds[$world->getName()]);
			return true;
		}
		return false;
	}
	
	public static function getWorld($object){
		if($object instanceof Position){
			return self::$worlds[$object->getLevel()->getFolderName()] ?? null;
		}else if($object instanceof Level){
			return self::$worlds[$object->getFolderName()] ?? null;
		}else{
			return self::$worlds[$object] ?? null;
		}
		return null;
	}
	
	public static function getWorlds($condition = null) : array{
		if($condition === null){
			return self::$worlds;
		}else{
			$ret = [];
			foreach(self::$worlds as $world){
				if($condition($world)){
					$ret[] = $world;
				}
			}
			return $ret;
		}
	}

	
	
	
	
	//instance
	private $name;
	private $properties;

	//lands
	private $landProvider;

	public function __construct(WorldProperties $properties){
		$this->properties = $properties;
		$server = Server::getInstance();
		
		$this->name = $this->properties->getLevel()->getFolderName();
		
		$this->levelPath = $server->getDataPath() . "worlds/" . $properties->getLevel()->getFolderName() . "/";
		$this->landPath = $this->levelPath . "lands/";
		@mkdir($this->levelPath);
		@mkdir($this->landPath);
		
		$landProviderClass = $this->properties->getClassProperties()->getLandProviderClass();
		if(!class_exists($landProviderClass)){
			Debug::critical(Main::getInstance(), $this->properties->getLevel()->getFolderName() . " 월드를 로드하던 중 에러가 발생하였습니다 : " . $landProviderClass . " 클래스가 존재하지 않습니다.");
			$landProviderClass = LandProvider::class;
		}
		$this->landProvider = new $landProviderClass($this);
	}
	
	
	
	
	
	public function getName() : string{
		return $this->name;
	}

	public function getLevel() : Level{
		return $this->properties->getLevel();
	}

	
	
	public function getProperties(){
		return $this->properties;
	}
	
	public function getWorldProperties(){
		return $this->properties->getWorldProperties();
	}
	
	public function getLandProperties(){
		return $this->properties->getLandProperties();
	}
	
	public function getRoomProperties(){
		return $this->properties->getRoomProperties();
	}
	
	

	public function getLandProvider() : ILandProvider{
		return $this->landProvider;
	}
	
	public function save(){
		$this->properties->getClassProperties()->setWorldClass(get_class($this));
		$this->properties->getClassProperties()->setLandProviderClass(get_class($this->landProvider));
		
		$this->properties->save();
		$this->landProvider->save();
	}
}