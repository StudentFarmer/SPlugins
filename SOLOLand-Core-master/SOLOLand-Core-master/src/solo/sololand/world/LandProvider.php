<?php

namespace solo\sololand\world;

use pocketmine\math\Vector3;
use pocketmine\utils\Config;
use solo\sololand\Main;
use solo\sololand\land\Land;
use solo\solocore\util\Debug;

class LandProvider implements ILandProvider{

	public $world;
	
	public $lands = [];
	public $sections = [];
	
	public $lastRemember = 1;
	
	public function __construct(World $world){
		$this->world = $world;
		
		// load all lands
		$files = [];
		$handle = opendir($this->world->getProperties()->getLandPath());
		while($file = readdir($handle)){ // read all files
			if($file != "." && $file != ".." && is_dir($file) != "1"){
				$files[] = $file;
			}
		}
		closedir($handle);
		
		foreach($files as $file){
			$config = new Config($world->getProperties()->getLandPath() . $file, Config::YAML);
			foreach($config->getAll() as $id => $landData){
				$landClass = Land::class;
				if(isset($landData["class"])){
					$landClass = $landData["class"];
					if(!class_exists($landClass)){
						$landClass = Land::class;
					}
				}
				$land = new $landClass($id);
				$this->lands[$id] = $land->unserialize($landData);
			}
		}
	}
	
	public function getSection(Vector3 $vec) : Section{
		$sectionX = Section::getSectionX($vec->getFloorX());
		$sectionZ = Section::getSectionZ($vec->getFloorZ());
		$sectionHash = $sectionX . ":" . $sectionZ;
		
		if(!isset($this->sections[$sectionHash])){
			$this->sections[$sectionHash] = new Section($sectionX, $sectionZ, $this);
			Debug::normal(Main::getInstance(), "Section (" . $sectionX . ":" . $sectionZ . ") 생성됨");
		}
		return $this->sections[$sectionHash];
	}
	
	public function getNextLandId() : int{
		if(!isset($this->lands[$this->lastRemember])){
			return $this->lastRemember;
		}else if(!isset($this->lands[++$this->lastRemember])){
			return $this->lastRemember;
		}
		$id = 0;
		while(isset($this->lands[++$id])){
			// :)
		}
		$this->lastRemember = $id;
		return $id;
	}
	
	public function addLand(Land $land) : Land{
		if(isset($this->lands[$land->getId()])){
			$this->removeLand($land->getId());
		}
		$this->lands[$land->getId()] = $land;
		foreach($this->sections as $section){
			if($section->isOverlap($land)){
				$section->addLand($land->getId());
			}
		}
		return $land;
	}
	
	public function removeLand(int $id) : bool{
		if(isset($this->lands[$id])){
			unset($this->lands[$id]);
			foreach($this->sections as $section){
				$section->removeLand($id);
			}
			return true;
		}
		return false;
	}
	
	public function getLandById(int $id){
		return $this->lands[$id] ?? null;
	}
	
	public function getLand(Vector3 $vec){
		return $this->getSection($vec)->getLand($vec);
	}
	
	public function getLands($condition = null) : array{
		if($condition === null){
			return $this->lands;
		}else{
			$ret = [];
			foreach($this->lands as $land){
				if($condition($land)){
					$ret[$land->getId()] = $land;
				}
			}
			return $ret;
		}
	}
	
	public function save(){
		$chunkSize = 500; // WARNING : NEVER CHANGED!
		
		$serialize = [];
		foreach($this->lands as $land){
			$fileIndex = (int) $land->getId() / $chunkSize;
			if(!isset($serialize[$fileIndex])){
				$serialize[$fileIndex] = [];
			}
			$serialize[$fileIndex][$land->getid()] = $land->serialize();
		}
		
		foreach($serialize as $key => $chunk){
			$config = new Config($this->world->getProperties()->getLandPath() . "lands." . $key . ".yml", Config::YAML);
			$config->setAll($chunk);
			$config->save();
		}
	}
}