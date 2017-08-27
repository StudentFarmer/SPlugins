<?php

namespace solo\smotd;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class SMotd extends PluginBase{

  public static $prefix = "§b§l[SMotd] §r§7";

  private static $instance = null;

  public static function getInstance(){
    return self::$instance;
  }



  private $config;

  private $interval;

  private $index = 0;

  private $motdList = [];

  public function onLoad(){
    if(self::$instance !== null){
      throw new \InvalidStateException();
    }
    self::$instance = $this;
  }

  public function onEnable(){
    @mkdir($this->getDataFolder());
    $this->saveResource("setting.yml");

    $this->config = new Config($this->getDataFolder() . "setting.yml", Config::YAML);
    $this->motdList = $this->config->get("motd-list", []);
    if(count($this->motdList) == 0){
      $this->getServer()->getLogger()->info("§c[SMotd] Motd 리스트가 비어있습니다. setting.yml 에서 Motd를 추가해주세요.");
      return;
    }
    $this->interval = $this->config->get("motd-change-interval", 80);

    foreach([
      "MotdAddCommand",
      "MotdIntervalCommand",
      "MotdListCommand",
      "MotdRemoveCommand"
    ] as $class){
      $class = "\\solo\\smotd\\command\\" . $class;
      $this->getServer()->getCommandMap()->register("smotd", new $class($this));
    }

    $this->getServer()->getScheduler()->scheduleRepeatingTask(new MotdChangeTask($this), 1);
  }

  public function onDisable(){
    self::$instance = null;
  }

  public function addMotd(string $motd){
    $this->motdList[] = $motd;
  }

  public function getAllMotd(){
    return $this->motdList;
  }

  public function removeMotd(int $index){
    if(isset($this->motdList[$index])){
      unset($this->motdList[$index]);
      $this->motdList = array_values($this->motdList);
      return true;
    }
    return false;
  }

  public function getCurrentMotd(){
    return $this->motdList[$this->index] ?? $this->motdList[($this->index = 0)];
  }

  public function next(){
    if(!isset($this->motdList[++$this->index])){
      $this->index = 0;
    }
  }

  public function getChangeInterval(){
    return $this->interval;
  }

  public function setChangeInterval(int $interval){
    $this->interval = $interval;
  }

  public function save(){
    $this->config->setAll([
      "motd-change-interval" => $this->interval,
      "motd-list" => $this->motdList
    ]);
    $this->config->save();
  }
}
