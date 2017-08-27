<?php

namespace solo\schedulerestarter;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\scheduler\PluginTask;
use pocketmine\utils\Config;

class Main extends PluginBase{

	public static $trigger = false;

	public $log;
	public $config;

	public function onEnable(){
		@mkdir($this->getDataFolder());

		$this->log = new Config($this->getDataFolder() . "log.yml", Config::YAML);

		$this->config = new Config($this->getDataFolder() . "setting.yml", Config::YAML, [
			"schedule" => [
				"5:00", "10:00", "15:00", "20:00"
			],
			"reboot-message" =>
				"§a===========================§r\n" .
				" 서버가 10초후 재부팅됩니다.\n" .
				" 5 ~ 10초후 재접속해주세요.\n" .
				"§a===========================§r\n",
			"kick-message" =>
				"§a서버가 곧 재부팅됩니다.\n" .
				"§a5 ~ 10초 후 재접속해주세요."
		]);

		$this->getServer()->getScheduler()->scheduleRepeatingTask(new class($this) extends PluginTask{
			public function onRun($currentTick){
				foreach($this->owner->config->get("schedule") as $rebootTime){
					if(
						explode(":", $rebootTime)[0] == date("G")
						&& explode(":", $rebootTime)[1] == date("i")
						){
						$timestamp = $this->timestamp();
						if(!$this->owner->log->exists($timestamp)){
							$this->owner->log->set($timestamp, true);
							$this->owner->log->save();
							$this->owner->restart();
						}
					}
				}
			}

			public function timestamp(){
				return date("Y-m-d G-i");
			}
		}, 20);
	}

	public function onDisable(){

	}

	public function restart(){
		if(Main::$trigger){
			return;
		}
		Main::$trigger = true;
				
		$server = $this->getServer();
		$server->broadcastMessage($this->config->get("reboot-message"));
		$server->getScheduler()->scheduleDelayedTask(new class($this) extends PluginTask{
			public function onRun($currentTick){
				$server = $this->owner->getServer();
				foreach($server->getOnlinePlayers() as $player){
					$player->save();
					$player->kick($this->owner->config->get("kick-message"), false);
				}
				foreach($server->getLevels() as $level){
					$level->save(true);
				}

				$server->shutdown();
			}
		}, 200);
	}

	public function onCommand(CommandSender $sender, Command $command, $label, array $args){
		$this->restart();
		return true;
	}
}