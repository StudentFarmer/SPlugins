<?php

namespace solo\sololand\world;

use pocketmine\Server;
use pocketmine\math\Vector3;
use pocketmine\level\Level;
use solo\sololand\land\Land;
use solo\sololand\math\Square;
use solo\sololand\command\defaults\island\IslandCommand;

class Island extends World{
	
	public function __construct(Level $level, $worldProviderClass, $landProviderClass, $landManagerClass){
		parent::__construct($level, $worldProviderClass, $landProviderClass, $landManagerClass);
		
		Server::getInstance()->getCommandMap()->register($this->getName(), new IslandCommand($this));
	}
	
	public function createLand(){
		$id = $this->getLandManager()->getNextLandId();
		
		$property = (new \ReflectionObject($this->level))->getProperty("generatorInstance");
		$property->setAccessible(true);
		
		$generatorInstance = $property->getValue($this->level);
		
		$spaceWidth = $generatorInstance->getSpaceWidth();
		$spaceDepth = $generatorInstance->getSpaceDepth();
		
		$spaceX = $id % 2000;
		$spaceZ = floor($id / 2000) * 2000;
		
		$square = new Square();
		$square->startX = $spaceX * $spaceWidth;
		$square->startZ = $spaceZ * $spaceDepth;
		$square->endX = $square->startX + $spaceDepth - 1;
		$square->endZ = $square->startZ + $spaceDepth - 1;
		
		$land = new Land($id, $square);
		$land->setSpawnPoint(new Vector3(
				($land->getStartX() + $land->getEndX()) / 2,
				$generatorInstance->getHighestIslandBlock() + 1,
				($land->getStartZ() + $land->getEndZ()) / 2
				));
		return $land;
	}
}