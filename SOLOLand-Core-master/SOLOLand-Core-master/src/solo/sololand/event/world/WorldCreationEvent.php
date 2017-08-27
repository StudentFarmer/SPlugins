<?php

namespace solo\sololand\event\world;

use pocketmine\event\Cancellable;
use pocketmine\level\Level;

class WorldCreationEvent extends WorldEvent implements Cancellable{
	
	public static $handlerList = null;
	
	protected $level;
	protected $worldClass;
	protected $worldProviderClass;
	protected $landProviderClass;
	protected $landManagerClass;
	
	public function __construct(Level $level, $worldClass, $worldProviderClass, $landProviderClass, $landManagerClass){
		$this->level = $level;
		$this->worldClass = $worldClass;
		$this->worldProviderClass = $worldProviderClass;
		$this->landProviderClass = $landProviderClass;
		$this->landManagerClass = $landManagerClass;
	}
	
	public function getLevel() : Level{
		return $this->level;
	}
	
	public function getWorldClass(){
		return $this->worldClass;
	}
	
	public function getWorldProviderClass(){
		return $this->worldProviderClass;
	}
	
	public function getLandProviderClass(){
		return $this->landProviderClass;
	}
	
	public function getLandManagerClass(){
		return $this->landManagerClass;
	}
	
	public function setWorldClass($worldClass){
		$this->worldClass = $worldClass;
	}
	
	public function setWorldProviderClass($worldProviderClass){
		$this->worldProviderClass = $worldProviderClass;
	}
	
	public function setLandProviderClass($landProviderClass){
		$this->landProviderClass = $landProviderClass;
	}
	
	public function setLandManagerClass($landManagerClass){
		$this->landManagerClass = $landManagerClass;
	}
}