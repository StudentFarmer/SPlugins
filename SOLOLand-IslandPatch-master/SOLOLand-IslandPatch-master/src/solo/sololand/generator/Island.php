<?php

namespace solo\sololand\generator;

use pocketmine\level\ChunkManager;
use pocketmine\level\generator\Generator;
use pocketmine\level\generator\Flat;
use pocketmine\utils\Random;
use pocketmine\math\Vector3;
use solo\sololand\generator\populator\{
	Cuboid,
	Cylinder,
	Sphere
};

class Island extends Generator{
	/** @var ChunkManager */
	private $level;
	/** @var Chunk */
	private $chunk;
	/** @var Random */
	private $random;
	/** @var Populator[] */
	private $populators = [];
	private $shapePopulator;
	private $structure, $chunks, $options, $floorLevel, $preset, $biome;
	
	public $highestBlock, $spaceWidth, $spaceDepth;
	
	public function getSettings(){
		return $this->options;
	}
	
	public function getSpaceWidth(){
		return $this->spaceWidth;
	}
	
	public function getSpaceDepth(){
		return $this->spaceDepth;
	}
	
	public function getName(){
		return "island";
	}
	
	public function __construct(array $options = []){
		$default = "1;7,2x9;1;island,decoration,tree,grass";
		$this->preset = "1;0;1;island(type=cylinder),decoration,tree,grass"; //empty world
		$this->options = $options;
		$this->chunk = null;
	}
	
	public function getHighestIslandBlock(){
		return $this->highestBlock;
	}
	
	protected function parsePreset($preset){//, $chunkX, $chunkZ){
		// COPY & PASTE from Flat Generator
		$this->preset = $preset;
		$preset = explode(";", $preset);
		$version = (int) $preset[0];
		$blocks = $preset[1] ?? "";
		$this->biome = $preset[2] ?? 1;
		$options = $preset[3] ?? "";
		$this->structure = Flat::parseLayers($blocks);
		$this->chunks = [];
		$this->floorLevel = $y = count($this->structure);
		for(; $y < 0xFF; ++$y){
			$this->structure[$y] = [0, 0];
		}
		//$this->chunk = clone $this->level->getChunk($chunkX, $chunkZ);
		//$this->chunk->setGenerated();
		//for($Z = 0; $Z < 16; ++$Z){
		//	for($X = 0; $X < 16; ++$X){
		//		$this->chunk->setBiomeId($X, $Z, $this->biome);
		//		for($y = 0; $y < 128; ++$y){
		//			$this->chunk->setBlock($X, $y, $Z, ...$this->structure[$y]);
		//		}
		//	}
		//}
		
		// new options parser
		$chars = str_split($options);
		$chars[] = ',';
		
		$mode = 0;
		$option = "";
		$subOptions = [];
		$subOption = "";
		$subOptionValue = "";
		
		foreach($chars as $char){
			if($char == '('){
				$mode = 1;
				continue;
			}
			
			if($mode == 0 && $char == ','){
				$this->options[$option] = $subOptions;
				$option = "";
				$subOptions = [];
				$subOption = "";
				$subOptionValue = "";
				continue;
			}else if($mode == 1 && $char == '='){
				$mode = 2;
				continue;
			}else if(($mode == 2 && $char == ' ') || $char == ')'){
				$subOptions[$subOption] = $subOptionValue;
				$subOption = "";
				$subOptionValue = "";
				$char == ')' ? $mode = 0 : $mode = 1;
				continue;
			}
			
			if($mode == 0){
				$option .= $char;
			}else if($mode == 1){
				$subOption .= $char;
			}else if($mode == 2){
				$subOptionValue .= $char;
			}
		}
		//preg_match_all('#(([0-9a-z_]{1,})\(?([0-9a-z_ =:]{0,})\)?),?#', $options, $matches);
		//foreach($matches[2] as $i => $option){
		//	$params = true;
		//	if($matches[3][$i] !== ""){
		//		$params = [];
		//		$p = explode(" ", $matches[3][$i]);
		//		foreach($p as $k){
		//			$k = explode("=", $k);
		//			if(isset($k[1])){
		//				$params[$k[0]] = $k[1];
		//			}
		//		}
		//	}
		//	$this->options[$option] = $params;
		//}
		
		// island generation part
		if(isset($this->options["island"])){
			$shapePopulator;
			switch(strtolower($this->options["island"]["type"] ?? "sphere")){
				case "cuboid":
					$shapePopulator = new Cuboid(
						$this->options["island"]["x_interval"] ?? 200,
						$this->options["island"]["z_interval"] ?? 200,
						$this->options["island"]["width"] ?? 20,
						$this->options["island"]["depth"] ?? 20,
						$this->options["island"]["layer"] ?? "7,2x1,2x3,2",
						$this->options["island"]["altitude"] ?? 1
					);
					$this->highestBlock = $shapePopulator->altitude + count($shapePopulator->getLayer());
					break;
					
				case "cylinder":
					$shapePopulator = new Cylinder(
						$this->options["island"]["x_interval"] ?? 200,
						$this->options["island"]["z_interval"] ?? 200,
						$this->options["island"]["radius"] ?? 8,
						$this->options["island"]["layer"] ?? "7,3x1,2x3,2",
						$this->options["island"]["altitude"] ?? 1
					);
					$this->highestBlock = $shapePopulator->altitude + count($shapePopulator->getLayer());
					break;
					
				case "sphere":
				default:
					$shapePopulator = new Sphere(
						$this->options["island"]["x_interval"] ?? 200,
						$this->options["island"]["z_interval"] ?? 200,
						$this->options["island"]["radius"] ?? 6,
						$this->options["island"]["layer"] ?? "7,6x1,3x3,2",
						$this->options["island"]["altitude"] ?? 10
					);
					$this->highestBlock = $shapePopulator->altitude + $shapePopulator->radius;
			}
			$this->populators["island"] = $shapePopulator;
		}
		
		echo $this->spaceWidth = $this->options["island"]["x_interval"] ?? 200;
		echo $this->spaceDepth = $this->options["island"]["z_interval"] ?? 200;
		
		if(isset($this->options["decoration"])){
			$ores = new \pocketmine\level\generator\populator\Ore();
			$ores->setOreTypes([
					new \pocketmine\level\generator\object\OreType(new \pocketmine\block\CoalOre(), 20, 16, 0, 128),
					new \pocketmine\level\generator\object\OreType(new \pocketmine\block\IronOre(), 20, 8, 0, 64),
					new \pocketmine\level\generator\object\OreType(new \pocketmine\block\RedstoneOre(), 8, 7, 0, 16),
					new \pocketmine\level\generator\object\OreType(new \pocketmine\block\LapisOre(), 1, 6, 0, 32),
					new \pocketmine\level\generator\object\OreType(new \pocketmine\block\GoldOre(), 2, 8, 0, 32),
					new \pocketmine\level\generator\object\OreType(new \pocketmine\block\DiamondOre(), 1, 7, 0, 16),
					new \pocketmine\level\generator\object\OreType(new \pocketmine\block\Dirt(), 20, 32, 0, 128),
					new \pocketmine\level\generator\object\OreType(new \pocketmine\block\Gravel(), 10, 16, 0, 128)
			]);
			$this->populators["decoration"] = $ores;
		}
		if(isset($this->options["tree"])){
			$treePopulator = new \solo\sololand\generator\populator\Tree();
			$treePopulator->setBaseAmount(4);
			$treePopulator->setRandomAmount(4);
			$this->populators["tree"] = $treePopulator;
		}
		if(isset($this->options["grass"])){
			$grassPopulator = new \pocketmine\level\generator\populator\TallGrass();
			$grassPopulator->setBaseAmount(50);
			$grassPopulator->setRandomAmount(30);
			$this->populators["grass"] = $grassPopulator;
		}
	}
	
	public function init(ChunkManager $level, Random $random){
		$this->level = $level;
		$this->random = $random;
		
		if(isset($this->options["preset"]) && $this->options["preset"] != ""){
			$this->parsePreset($this->options["preset"]);
		}else{
			$this->parsePreset($this->preset);
		}
	}
	
	public function generateChunk($chunkX, $chunkZ){
		//if($this->chunk === null){
		//	if(isset($this->options["preset"]) && $this->options["preset"] != ""){
		//		$this->parsePreset($this->options["preset"], $chunkX, $chunkZ);
		//	}else{
		//		$this->parsePreset($this->preset, $chunkX, $chunkZ);
		//	}
		//}
		$chunk = $this->level->getChunk($chunkX, $chunkZ);
		for($z = 0; $z < 16; ++$z){
			for($x = 0; $x < 16; ++$x){
				$chunk->setBiomeId($x, $z, $this->biome);
				for($y = 0; $y < 128; ++$y){
					$chunk->setBlock($x, $y, $z, ...$this->structure[$y]);
				}
			}
		}
		//$this->shapePopulator->populate($this->level, $chunkX, $chunkZ, $this->random);
		//$chunk = clone $this->chunk;
		//$chunk->setX($chunkX);
		//$chunk->setZ($chunkZ);
		//$this->level->setChunk($chunkX, $chunkZ, $chunk);
	}
	
	public function populateChunk($chunkX, $chunkZ){
		$this->random->setSeed(0xdeadbeef ^ ($chunkX << 8) ^ $chunkZ ^ $this->level->getSeed());
		foreach($this->populators as $populator){
			$populator->populate($this->level, $chunkX, $chunkZ, $this->random);
		}
	}
	
	public function getSpawn(){
		return new Vector3(100, $this->getHighestIslandBlock(), 100);
	}
}
