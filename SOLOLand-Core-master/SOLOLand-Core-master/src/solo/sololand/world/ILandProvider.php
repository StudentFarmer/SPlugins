<?php

namespace solo\sololand\world;

use solo\sololand\land\Land;
use pocketmine\math\Vector3;

interface ILandProvider{
	
	public function getNextLandId() : int;
	
	public function addLand(Land $land);
	
	public function removeLand(int $id) : bool;
	
	public function getLand(Vector3 $vec);
	
	public function getLandById(int $id);
	
	public function getLands($condition = null) : array;
	
	public function save();
	
}