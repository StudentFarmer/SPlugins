<?php

namespace solo\sprefix\command;

use pocketmine\command\CommandSender;

use solo\sprefix\SPrefix;
use solo\sprefix\SPrefixCommand;

class PluginPrefixCommand extends SPrefixCommand{

  private $owner;

  public function __construct(SPrefix $owner){
    parent::__construct("pluginprefix", "set plugin's prefix", "/pluginprefix <plugin> <prefix...>");
    $this->setPermission("sprefix.command.pluginprefix");

    $this->owner = $owner;
  }

  public function _execute(CommandSender $sender, string $label, array $args) : bool{
    if(!$sender->hasPermission($this->getPermission())){
      $sender->sendMessage(SPrefix::$prefix . "이 명령을 실행할 권한이 없습니다.");
      return true;
    }
    if(!isset($args[1])){
      $sender->sendMessage(SPrefix::$prefix . "사용법 : " . $this->getUsage() . " - " . $this->getDescription());
      $sender->sendMessage(SPrefix::$prefix . "<prefix> 를 reset로 입력시 prefix 설정값을 해제합니다.");
      return true;
    }
    $find = str_replace("_", " ", array_shift($args));
    $plugin = null;
    foreach($this->owner->getServer()->getPluginManager()->getPlugins() as $check){
      if(strcasecmp($check->getName(), $find) == 0){
        $plugin = $check;
        break;
      }
    }
    if($plugin === null){
      $sender->sendMessage(SPrefix::$prefix . "플러그인 \"" . $find . "\" 을 찾을 수 없습니다.");
      return true;
    }
    if(!$this->owner->hasPrefixProperty($plugin)){
      $sender->sendMessage(SPrefix::$prefix . $plugin->getName() . " 플러그인은 Prefix 프로퍼티를 가지고 있지 않습니다.");
      return true;
    }
    $prefix = implode(" ", $args);
    if($prefix === "reset"){
      $this->owner->setPluginPrefix($plugin, null);
      $sender->sendMessage(SPrefix::$prefix . $plugin->getName() . " 플러그인의 Prefix를 원래값으로 사용하도록 설정하였습니다.");
    }else{
      $this->owner->setPluginPrefix($plugin, $prefix);
      $sender->sendMessage(SPrefix::$prefix . $plugin->getName() . " 플러그인의 Prefix를 \"" . $prefix . "\" 으로 설정하였습니다.");
    }
    $this->owner->updatePrefix();
    $this->owner->save();
    return true;
  }
}
