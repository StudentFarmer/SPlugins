<?php

namespace solo\sololand\generator\populator;

use pocketmine\level\ChunkManager;
use pocketmine\level\generator\Flat;
use pocketmine\level\generator\populator\Populator;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;
use solo\sololand\math\Square;

class Sphere extends Populator{
	
	public $spaceWidth;
	public $spaceDepth;
	
	public $radius;
	public $layer;
	public $altitude;
	
	private $boundingBox;
	
	public function __construct(int $spaceWidth = 200, int $spaceDepth = 200, int $radius = 5, $layer = "7,5x1,3x3,2", $altitude = 8){
		$this->spaceWidth = $spaceWidth;
		$this->spaceDepth = $spaceDepth;

		$this->layer = Flat::parseLayers($layer);
		$this->radius = $radius;
		$this->altitude = $altitude;
		
		$square = new Square();
		$square->startX = ((int) $this->spaceWidth / 2) - $radius;
		$square->startZ = ((int) $this->spaceDepth / 2) - $radius;
		$square->endX = ((int) $this->spaceWidth / 2) + $radius;
		$square->endZ = ((int) $this->spaceDepth / 2) + $radius;
		$this->boundingBox = $square;
	}
	
	public function populate(ChunkManager $level, $chunkX, $chunkZ, Random $random){
		if($chunkX < 0 || $chunkZ < 0){
			return;
		}
		$chunkBB = new Square();
		$chunkBB->startX = ($chunkX * 16) % $this->spaceWidth;
		$chunkBB->startZ = ($chunkZ * 16) % $this->spaceDepth;
		$chunkBB->endX = $chunkBB->startX + 15;
		$chunkBB->endZ = $chunkBB->startZ + 15;
		
		if($chunkBB->isOverlap($this->boundingBox)){
			$centerX = floor($chunkX * 16 / $this->spaceWidth) * $this->spaceWidth + (int) $this->spaceWidth / 2;
			$centerZ = floor($chunkZ * 16 / $this->spaceDepth) * $this->spaceDepth + (int) $this->spaceDepth / 2;
			
			$minX = max($chunkX * 16, $centerX - $this->radius);
			$minY = max(0, $this->altitude - $this->radius);
			$minZ = max($chunkZ * 16, $centerZ - $this->radius);
			$maxX = min($chunkX * 16 + 15, $centerX + $this->radius);
			$maxY = min(255, $this->altitude + $this->radius);
			$maxZ = min($chunkZ * 16 + 15, $centerZ + $this->radius);
			$centerVector = new Vector3($centerX, $this->altitude, $centerZ);
			
			$xzCount = [];
			$maxLayer = count($this->layer);
			for($x = $minX; $x <= $maxX; $x++){
				for($y = $maxY; $y >= $minY; $y--){
					for($z = $minZ; $z <= $maxZ; $z++){
						if($centerVector->distance(new Vector3($x, $y, $z)) < $this->radius){
							if(!isset($xzCount[$hash = $x . ":" . $z])){
								$xzCount[$hash] = 0;
							}
							$block;
							$current = $maxLayer - $xzCount[$hash];
							if(isset($this->layer[$current - 1])){
								$block = $this->layer[$current - 1];
								$xzCount[$hash]++;
							}else{
								$block = $this->layer[$current];
							}
							if($level->getBlockIdAt($x, $y, $z) != 7){
								$level->setBlockIdAt($x, $y, $z, $block[0] ?? 0);
								$level->setBlockDataAt($x, $y, $z, $block[1] ?? 0);
							}
						}
					}
				}
			}
		}
	}
	
	public function getLayer(){
		return $this->layer;
	}
}