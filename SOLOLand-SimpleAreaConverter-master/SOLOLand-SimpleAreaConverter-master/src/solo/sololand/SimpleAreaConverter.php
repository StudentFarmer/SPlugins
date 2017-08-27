<?php

namespace solo\sololand;

use pocketmine\plugin\PluginBase;
use pocketmine\math\Vector3;
use pocketmine\utils\Config;
use solo\sololand\world\World;
use solo\sololand\land\Land;

class SimpleAreaConverter extends PluginBase{
	
	public function onEnable(){
		$worldsDir = $this->getServer()->getDataPath() . "worlds/";
		if(!$dh = opendir($worldsDir)){
			//open fail
			return;
		}
		
		$files = [];
		while($file = readdir($dh)){
			if($file != '.' && $file != '..'){
				$files[] = $file;
			}
		}
		closedir($dh);
		
		foreach($files as $dir){
			if(is_dir($worldsDir . $dir)){
				if(file_exists($filePath = $worldsDir . $dir . "/protects.json")){
					$this->log($dir . " 월드에서 SimpleArea 땅 정보를 발견하였습니다.");
					$this->getServer()->loadLevel($dir);
					$world = World::getWorld($dir);
					if($world === null){
						$this->log($dir . " 월드가 로드되어 있지 않거나 잘못된 월드입니다.");
						continue;
					}
					
					$this->log($dir . " 월드의 SimpleArea땅을 SOLOLand땅으로 변환중...");
					$config = new Config($filePath, Config::JSON);
					$lands = $this->convert($world, $config->getAll());
					foreach($lands as $land){
						$world->getLandManager()->addLand($land);
					}
					$this->log($dir . " 월드에서 " . count($lands) . "개의 SimpleArea땅이 SOLOLand땅으로 변환되었습니다.");
					
					rename($filePath, $worldsDir . $dir . "/converted_protects.json");
				}
			}
		}
	}
	
	public function onDisable(){
		
	}
	
	public function log($message){
		$this->getServer()->getLogger()->info("§b[SimpleAreaConverter] " . $message);
	}
	
	public function convert(World $world, array $simpleAreaSectionData){
		$lands = [];
		$level = $world->getLevel();
		foreach($simpleAreaSectionData as $id => $data){
			if(!is_numeric($id)){
				continue;
			}
			++$id;
			
			$land = new Land($id);
			$land->owner = $data["owner"];
			foreach($data["resident"] as $member => $boolValue){
				if($member != $land->owner){
					$land->addMember($member);
				}
			}
			$land->startX = $data["startX"];
			$land->startZ = $data["startZ"];
			$land->endX = $data["endX"];
			$land->endZ = $data["endZ"];
			$land->price = ($data["owner"] == "") ? $data["areaPrice"] : -1;
			$land->welcomeMessage = $data["welcome"];
			$land->allowPVP = $data["pvpAllow"];
			$land->allowAccess = !$data["accessDeny"];
			
			$centerX = ($land->startX + $land->endX) / 2;
			$centerZ = ($land->startZ + $land->endZ) / 2;
			if(!$level->isChunkLoaded($centerX >> 4, $centerZ >> 4)){
				$level->loadChunk($centerX >> 4, $centerZ >> 4);
			}
			$land->spawnPoint = new Vector3($centerX, $level->getHighestBlockAt($centerX, $centerZ) + 1, $centerZ);
			
			$lands[$land->getId()] = $land;
		}
		return $lands;
	}
}