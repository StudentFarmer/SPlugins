<?php

namespace solo\sprefix;

use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;

class SPrefix extends PluginBase{

  public static $prefix = "§l§b[SPrefix] §r§7";

  private $prefixSetting;
  private $prefixBackup = [];

  public function onEnable(){
    @mkdir($this->getDataFolder());

    if(file_exists($this->getDataFolder() . "prefix.json")){
      $this->prefixSetting = json_decode(file_get_contents($this->getDataFolder() . "prefix.json"), true);
    }else{
      $this->prefixSetting = ["plugins" => []];
    }

    foreach([
      "DefaultPrefixCommand",
      "PluginPrefixCommand"
    ] as $class){
      $class = "\\solo\\sprefix\\command\\" . $class;
      $this->getServer()->getCommandMap()->register("sprefix", new $class($this));
    }
  }

  public function save(){
    file_put_contents($this->getDataFolder() . "prefix.json", json_encode($this->prefixSetting));
  }

  public function hasPrefixProperty(Plugin $plugin){
    try{
      (new \ReflectionClass(get_class($plugin)))->getProperty("prefix");
    }catch(\Throwable $e){
      return false;
    }
    return true;
  }

  public function getDefaultPrefix(){
    return $this->prefixSetting["default"] ?? null;
  }

  public function setDefaultPrefix(string $prefix = null){
    $this->prefixSetting["default"] = $prefix;
  }

  public function getPluginPrefix(Plugin $plugin){
    return $this->prefixSetting["plugins"][$plugin->getName()] ?? null;
  }

  public function setPluginPrefix(Plugin $plugin, string $prefix = null){
    if($prefix === null){
      unset($this->prefixSetting["plugins"][$plugin->getName()]);
      return;
    }
    $this->prefixSetting["plugins"][$plugin->getName()] = $prefix;
  }

  public function updatePrefix(){
    foreach($this->getServer()->getPluginManager()->getPlugins() as $plugin){
    	try{
    		$property = (new \ReflectionClass(get_class($plugin)))->getProperty("prefix");
    	}catch(\Throwable $e){
    		continue;
    	}
      $prefix = $this->getPluginPrefix($plugin);
      if($prefix === null){
        $prefix = $this->getDefaultPrefix();
        if($prefix === null){
          if(isset($this->prefixBackup[$plugin->getName()])){
            $property->setValue($plugin, $this->prefixBackup[$plugin->getName()]);
            unset($this->prefixBackup[$plugin->getName()]);
          }
          continue;
        }
      }
      $this->prefixBackup[$plugin->getName()] = $property->getValue($plugin); // backup
    	$property->setValue($plugin, $prefix);
    }
  }
}
