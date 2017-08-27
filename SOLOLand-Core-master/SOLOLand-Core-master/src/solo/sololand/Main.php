<?php

namespace solo\sololand;

use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginLoadOrder;
use pocketmine\command\Command;
use pocketmine\scheduler\PluginTask;

use solo\sololand\handler\block\BlockModifyHandler;
use solo\sololand\handler\entity\EntityDamageHandler;
use solo\sololand\handler\entity\EntityLevelChangeHandler;
use solo\sololand\handler\entity\ExplosionPrimeHandler;
use solo\sololand\handler\inventory\InventoryPickupItemHandler;
use solo\sololand\handler\land\LandEnterHandler;
use solo\sololand\handler\level\LevelLoadHandler;
use solo\sololand\handler\level\LevelUnloadHandler;
use solo\sololand\handler\player\PlayerDeathHandler;
use solo\sololand\handler\player\PlayerFloorMoveHandler;
use solo\sololand\handler\player\PlayerInteractHandler;
use solo\sololand\handler\player\PlayerQuitHandler;
use solo\sololand\command\defaults\world\WorldCommand;
use solo\sololand\command\defaults\land\LandCommand;
use solo\sololand\command\defaults\test\TestCommand;
use solo\sololand\util\Setting;
use solo\sololand\world\World;

use solo\solocore\util\Debug;

class Main extends PluginBase{

	private static $instance = null;

	public static function getInstance(){
		return self::$instance;
	}

	public function onLoad(){
		self::$instance = $this;
		@mkdir($this->getDataFolder());
		
		$this->initialSetting();
		$this->initialGenerators();
	}

	public function onEnable(){
		//$this->initialPatches();
		$this->initialHandlers();
		$this->initialWorlds();
		$this->initialCommands();
		$this->initialTasks();
	}

	public function onDisable(){
		foreach(World::getWorlds() as $world){
			World::unloadWorld($world);
		}
	}
	
	
	
	

	// *************************** //
	// ********* initial ********* //
	// *************************** //
	private function initialSetting(){
		Setting::init();
	}
	
	private function initialGenerators(){
		//Generator.addGenerator(IslandGenerator.class, "island", IslandGenerator.TYPE_ISLAND);
		//Generator.addGenerator(GridLandGenerator.class, "gridland", GridLandGenerator.TYPE_GRID_LAND);
		//Generator.addGenerator(EmptyWorldGenerator.class, "emptyworld", EmptyWorldGenerator.TYPE_EMPTY_WORLD);
		//Generator.addGenerator(SkyBlockGenerator.class, "skyblock", SkyBlockGenerator.TYPE_SKY_BLOCK);
		//Generator.addGenerator(SkyGridGenerator.class, "skygrid", SkyGridGenerator.TYPE_SKY_GRID);
	}
	
	private function initialPatches(){
		$this->getServer()->getPluginManager()->registerInterface(PatchLoader::class);
		$this->getServer()->getPluginManager()->registerInterface(FolderPatchLoader::class);
		Debug::normal($this, "SOLOLand 패치 로더가 등록되었습니다.");
		$this->getServer()->getPluginManager()->loadPlugins($this->getServer()->getPluginPath(), [PatchLoader::class, FolderPatchLoader::class]);
		//$this->getServer()->enablePlugins(PluginLoadOrder::STARTUP);
	}
	
	private function initialWorlds(){
		if(Setting::$loadAllWorldsOnEnable){
			$levelDirectory = $this->getServer()->getDataPath() . "worlds";
			//TODO: Load all worlds
		}
		foreach($this->getServer()->getLevels() as $level){
			World::loadWorld($level);
		}
	}
	
	private function initialHandlers(){
		foreach([
				new BlockModifyHandler(),
				new EntityDamageHandler(),
				new EntityLevelChangeHandler(),
				new ExplosionPrimeHandler(),
				new InventoryPickupItemHandler(),
				new LandEnterHandler(),
				new LevelLoadHandler(),
				new LevelUnloadHandler(),
				new PlayerDeathHandler(),
				new PlayerFloorMoveHandler(),
				new PlayerInteractHandler(),
				new PlayerQuitHandler()
		] as $handler){
			$this->getServer()->getPluginManager()->registerEvents($handler, $this);
		}
	}
	
	private function initialTasks(){
		$this->getServer()->getScheduler()->scheduleDelayedRepeatingTask(new class($this) extends PluginTask{ // AutoSaveTask
			public function onRun($currentTick){
				foreach(World::getWorlds() as $world){
					$world->save(false);
				}
			}
		}, 36000, 36000);
	}
	
	private function initialCommands(){
		$server = $this->getServer();
		$registerFunc = function (Command $command) use ($server){ $server->getCommandMap()->register($command->getName(), $command); };
		foreach([
				new WorldCommand(),
				new LandCommand()
				//new TestCommand()
		] as $mainCommand){
			$registerFunc($mainCommand);
		}
	}
}
