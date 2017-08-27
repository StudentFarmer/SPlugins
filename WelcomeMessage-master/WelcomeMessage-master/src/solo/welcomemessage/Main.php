<?php

namespace solo\welcomemessage;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\scheduler\PluginTask;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener{

	public $config;

	public function onEnable(){
		@mkdir($this->getDataFolder());
		$this->config = new Config($this->getDataFolder() . "setting.yml", Config::YAML, [
			"message" => "",
			"popup" => "",
			"tip" => "",
			"title" => "안녕하세요!",
			"subtitle" => "{NAME}님, 서버에 오신것을 환영합니다!",
			"actionbar" => "튜토리얼은 꼭 읽어주세요!",
			"fadein" => 30,
			"stay" => 80,
			"fadeout" => 30
		]);

		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onDisable(){

	}

	public function onJoin(PlayerJoinEvent $event){
		$player = $event->getPlayer();
		if(($message = $this->config->get("message")) !== ""){
			$player->sendMessage($this->replaceParameters($player, $message));
		}
		if(($popup = $this->config->get("popup")) !== ""){
			$player->sendPopup($this->replaceParameters($player, $popup));
		}
		if(($tip = $this->config->get("tip")) !== ""){
			$player->sendTip($this->replaceParameters($player, $tip));
		}
		if($this->config->get("title") !== ""){
			$this->getServer()->getScheduler()->scheduleDelayedTask(new class($this, $event->getPlayer()) extends PluginTask{
				public $player;

				public function __construct(Main $plugin, Player $player){
					parent::__construct($plugin);
					$this->player = $player;
				}

				public function onRun($currentTick){
					$player = $this->player;
					$plugin = $this->owner;
					$config = $plugin->config;

					$title = $plugin->replaceParameters($player, $config->get("title"));
					$subtitle = $plugin->replaceParameters($player, $config->get("subtitle"));
					$actionbar = $plugin->replaceParameters($player, $config->get("actionbar"));

					$fadein = $config->get("fadein");
					$stay = $config->get("stay");
					$fadeout = $config->get("fadeout");
					switch($this->owner->getServer()->getName()){
						case "Tesseract":
							$player->sendTitle($title, $subtitle, $fadein, $fadeout, $stay);

						default: // PocketMine-MP
							$player->addTitle($title, $subtitle, $fadein, $stay, $fadeout);
							$player->addActionBarMessage($actionbar);
							//$this->player->addTitle($title, $subtitle, $fadein, $stay, $fadeout);
							break;
					}
				}
			}, 30);
		}
	}

	public function replaceParameters(Player $player, $string){
		return str_replace([
			"{NAME}",
			"{MOTD}"
		], [
			$player->getName(),
			$this->getServer()->getMotd()
		], $string);
	}
}