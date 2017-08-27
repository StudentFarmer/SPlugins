<?php

namespace solo\sololand\math;

use pocketmine\math\Vector3;

class Cuboid extends Square{
	
	public static function create(Vector3 $point1, Vector3 $point2) : Cuboid{
		$cuboid = new Cuboid();
		$cuboid->startX = min($point1->getFloorX(), $point2->getFloorX());
		$cuboid->startY = min($point1->getFloorY(), $point2->getFloorY());
		$cuboid->startZ = min($point1->getFloorZ(), $point2->getFloorZ());
		$cuboid->endX = max($point1->getFloorX(), $point2->getFloorX());
		$cuboid->endY = max($point1->getFloorY(), $point2->getFloorY());
		$cuboid->endZ = max($point1->getFloorZ(), $point2->getFloorZ());
		return $cuboid;
	}
	
	public $startY;
	public $endY;

	public function set($cuboid){
		$this->startX = $cuboid->startX;
		$this->startY = $cuboid->startY;
		$this->startZ = $cuboid->startZ;
		$this->endX = $cuboid->endX;
		$this->endY = $cuboid->endY;
		$this->endZ = $cuboid->endZ;
	}
	
	public function getStartY() : int{
		return $this->startY;
	}
	
	public function getEndY() : int{
		return $this->endY;
	}
	
	public function isInside($object) : bool{
		if($object instanceof Cuboid){
			return (
					$this->startX <= $object->startX
					&& $this->startY <= $object->startY
					&& $this->startZ <= $object->startZ
					&& $this->endX >= $object->endX
					&& $this->endY >= $object->endY
					&& $this->endZ >= $object->endZ
				);
		}else if($object instanceof Vector3){
			return (
					$this->startX <= $object->getFloorX()
					&& $this->startY <= $object->getFloorY()
					&& $this->startZ <= $object->getFloorZ()
					&& $this->endX >= $object->getFloorX()
					&& $this->endY >= $object->getFloorY()
					&& $this->endZ >= $object->getFloorZ()
				);
		}
		return false; ///....?
	}
	
	public function isOverlap($cuboid) : bool{
		return !(
				$this->startX > $cuboid->endX
				|| $this->startY > $cuboid->endY
				|| $this->startZ > $cuboid->endZ
				|| $this->endX < $cuboid->startX
				|| $this->endY < $cuboid->startY
				|| $this->endZ < $cuboid->startZ
			);
	}
	
	public function expand($object){
		if($object instanceof Cuboid){
			$this->startX = min($this->startX, $object->startX);
			$this->startY = min($this->startY, $object->startY);
			$this->startZ = min($this->startZ, $object->startZ);
			$this->endX = max($this->endX, $object->endX);
			$this->endY = max($this->endY, $object->endY);
			$this->endZ = max($this->endZ, $object->endZ);
		}else if($object instanceof Vector3){
			$this->startX = min($this->startX, $object->getFloorX());
			$this->startY = min($this->startY, $object->getFloorY());
			$this->startZ = min($this->startZ, $object->getFloorZ());
			$this->endX = max($this->endX, $object->getFloorX());
			$this->endY = max($this->endY, $object->getFloorY());
			$this->endZ = max($this->endZ, $object->getFloorZ());
		}
	}
	
	public function reduce(Vector3 $vec){
		if(!$this->isInside($vec)){
			return; // cannot reduce
		}
		$newStartX = $this->startX;
		$newStartY = $this->startY;
		$newStartZ = $this->startZ;
		$newEndX = $this->endX;
		$newEndY = $this->endY;
		$newEndZ = $this->endZ;
	
		if(abs($this->startX - $vec->getFloorX()) > abs($this->endX - $vec->getFloorX())){
			$newEndX = $vec->getFloorX();
		}else{
			$newStartX = $vec->getFloorX();
		}
		if(abs($this->startY - $vec->getFloorY()) > abs($this->endY - $vec->getFloorY())){
			$newEndX = $vec->getFloorY();
		}else{
			$newStartX = $vec->getFloorY();
		}
		if(abs($this->startZ - $vec->getFloorZ()) > abs($this->endZ - $vec->getFloorZ())){
			$newEndZ = $vec->getFloorZ();
		}else{
			$newStartZ = $vec->getFloorZ();
		}
		
		$this->startX = $newStartX;
		$this->startY = $newStartY;
		$this->startZ = $newStartZ;
		$this->endX = $newEndX;
		$this->endY = $newEndY;
		$this->endZ = $newEndZ;
	}
	
	/*
	 * returns abs(startY - endY) + 1
	 */
	public function getHeight() : int{
		return abs($this->startY - $this->endY) + 1;
	}
	
	public function getSize() : int{
		return $this->getWidth() * $this->getDepth() * $this->getHeight();
	}
	
	public function clone() : Cuboid{
		$cuboid = new Cuboid();
		$cuboid->startX = $this->startX;
		$cuboid->startY = $this->startY;
		$cuboid->startZ = $this->startZ;
		$cuboid->endX = $this->endX;
		$cuboid->endY = $this->endY;
		$cuboid->endZ = $this->endZ;
		return $cuboid;
	}
}