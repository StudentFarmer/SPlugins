<?php

namespace solo\sololand;

use pocketmine\plugin\PluginBase;
use pocketmine\level\generator\Generator;
use solo\sololand\generator\Island as IslandGenerator;
use solo\sololand\world\World;
use solo\sololand\world\Island;
use solo\sololand\world\IslandLandManager;

class IslandPatch extends PluginBase{
	
	public function onLoad(){
		Generator::addGenerator(IslandGenerator::class, "island");
		World::registerWorld("island", Island::class, null, null, IslandLandManager::class);
	}
	
	public function onEnable(){
		
	}
	
	public function onDisable(){
		
	}
}