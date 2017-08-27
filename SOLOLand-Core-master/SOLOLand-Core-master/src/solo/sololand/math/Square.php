<?php

namespace solo\sololand\math;

use pocketmine\math\Vector3;

class Square{

	public static function create(Vector3 $point1, Vector3 $point2) : Square{
		$square = new Square();
		$square->startX = min($point1->getFloorX(), $point2->getFloorX());
		$square->startZ = min($point1->getFloorZ(), $point2->getFloorZ());
		$square->endX = max($point1->getFloorX(), $point2->getFloorX());
		$square->endZ = max($point1->getFloorZ(), $point2->getFloorZ());
		return $square;
	}
	
	public $startX;
	public $startZ;
	public $endX;
	public $endZ;
	
	public function set($square){
		$this->startX = $square->startX;
		$this->startZ = $square->startZ;
		$this->endX = $square->endX;
		$this->endZ = $square->endZ;
	}
	
	public function getStartX() : int{
		return $this->startX;
	}
	
	public function getStartZ() : int{
		return $this->startZ;
	}
	
	public function getEndX() : int{
		return $this->endX;
	}
	
	public function getEndZ() : int{
		return $this->endZ;
	}
	
	public function isInside($object) : bool{
		if($object instanceof Square){
			return (
					$this->startX <= $object->startX
					&& $this->startZ <= $object->startZ
					&& $this->endX >= $object->endX
					&& $this->endZ >= $object->endZ
				);
		}else if($object instanceof Vector3){
			return (
					$this->startX <= $object->getFloorX()
					&& $this->startZ <= $object->getFloorZ()
					&& $this->endX >= $object->getFloorX()
					&& $this->endZ >= $object->getFloorZ()
				);
		}
		return false; ///....?
	}
	
	public function isOverlap($square) : bool{
		return !(
				$this->startX > $square->endX
				|| $this->startZ > $square->endZ
				|| $this->endX < $square->startX
				|| $this->endZ < $square->startZ
			);
	}
	
	public function expand($object){
		if($object instanceof Square){
			$this->startX = min($this->startX, $object->startX);
			$this->startZ = min($this->startZ, $object->startZ);
			$this->endX = max($this->endX, $object->endX);
			$this->endZ = max($this->endZ, $object->endZ);
		}else if($object instanceof Vector3){
			$this->startX = min($this->startX, $object->getFloorX());
			$this->startZ = min($this->startZ, $object->getFloorZ());
			$this->endX = max($this->endX, $object->getFloorX());
			$this->endZ = max($this->endZ, $object->getFloorZ());
		}
	}
	
	public function reduce(Vector3 $vec){
		if(!$this->isInside($vec)){
			return; // cannot reduce
		}
		$newStartX = $this->startX;
		$newStartZ = $this->startZ;
		$newEndX = $this->endX;
		$newEndZ = $this->endZ;
		
		if(abs($this->startX - $vec->getFloorX()) > abs($this->endX - $vec->getFloorX())){
			$newEndX = $vec->getFloorX();
		}else{
			$newStartX = $vec->getFloorX();
		}
		if(abs($this->startZ - $vec->getFloorZ()) > abs($this->endZ - $vec->getFloorZ())){
			$newEndZ = $vec->getFloorZ();
		}else{
			$newStartZ = $vec->getFloorZ();
		}
		$this->startX = $newStartX;
		$this->startZ = $newStartZ;
		$this->endX = $newEndX;
		$this->endZ = $newEndZ;
	}
	
	/*
	 * returns abs(startX - endX) + 1
	 */
	public function getWidth() : int{
		return abs($this->startX - $this->endX) + 1;
	}
	
	/*
	 * returns abs(startZ - endZ) + 1
	 */
	public function getDepth() : int{
		return abs($this->startZ - $this->endZ) + 1;
	}
	
	public function getSize() : int{
		return $this->getWidth() * $this->getDepth();
	}
	
	public function clone() : Square{
		$square = new Square();
		$square->startX = $this->startX;
		$square->startY = $this->startY;
		$square->startZ = $this->startZ;
		$square->endX = $this->endX;
		$square->endY = $this->endY;
		$square->endZ = $this->endZ;
		return $square;
	}
}