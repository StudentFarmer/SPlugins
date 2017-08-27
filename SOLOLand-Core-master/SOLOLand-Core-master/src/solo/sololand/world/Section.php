<?php

namespace solo\sololand\world;

use pocketmine\math\Vector3;
use solo\sololand\math\Square;

class Section extends Square{
	
	const WIDTH = 64;
	const DEPTH = 64;
	
	public static function getSectionX(int $x) : int{
		return floor($x / self::WIDTH);
	}
	
	public static function getSectionZ(int $z) : int{
		return floor($z / self::DEPTH);
	}
	
	

	public $provider;
	public $lands = [];
	
	public function __construct(int $sectionX, int $sectionZ, LandProvider $provider){
		$this->startX = $sectionX * self::WIDTH;
		$this->startZ = $sectionZ * self::DEPTH;
		$this->endX = $sectionX * self::WIDTH + self::WIDTH - 1;
		$this->endZ = $sectionZ * self::DEPTH + self::DEPTH - 1;
		
		$this->provider = $provider;
		$section = $this;
		$condition = function($land) use ($section){
			return $section->isOverlap($land);
		};
		
		foreach($provider->getLands($condition) as $land){
			$this->addLand($land->getId());
		}
	}
	
	public function addLand(int $id){
		$this->lands[$id] = $id;
	}
	
	public function getLand(Vector3 $vec){
		foreach($this->lands as $id => $fake){
			$land = $this->provider->getLandById($id);
			if($land->isInside($vec)){
				return $land;
			}
		}
		return null;
	}
	
	public function getLands($condition = null){
		if($condition === null){
			return $this->lands;
		}else{
			$ret = [];
			foreach($this->lands as $id){
				if($condition($id)){
					$ret[$id] = $id;
				}
			}
			return $ret;
		}
	}
	
	public function removeLand(int $id){
		unset($this->lands[$id]);
	}
	
	public final function expand($object){
		
	}
	
	public final function reduce(Vector3 $vec){
		
	}
}