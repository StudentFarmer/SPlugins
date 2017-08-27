<?php

namespace solo\scolor;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\server\ServerCommandEvent;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

use solo\scolor\color\Color;
use solo\scolor\style\Style;

class SColor extends PluginBase implements Listener{

  private static $instance = null;

  public function getInstance(){
    return self::$instance;
  }

  /** @var Color[] */
  private $knownColors = [];

  /** @var Style[] */
  private $knownStyles = [];

  /** @var Color|Style[] */
  private $list = [];

  private $allowList = [];

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

    foreach($this->config->get("allow-colors", []) as $code){
      $this->allowList[$code] = $code;
    }

    $this->registerColors();
    $this->registerStyles();

    $this->getServer()->getCommandMap()->register("scolor", new \solo\scolor\command\ColorCommand($this));

    $this->getServer()->getPluginManager()->registerEvents($this, $this);
  }

  public function onDisable(){
    self::$instance = null;
  }

  public function registerColor(Color $color){
    if(!isset($this->allowList[$color->getCode()])){
      return;
    }
    $this->knownColors[$color->getCode()] = $color;
    $this->list[$color->getCode()] = $color;
  }

  public function registerStyle(Style $style){
    if(!isset($this->allowList[$style->getCode()])){
      return;
    }
    $this->knownStyles[$style->getCode()] = $style;
    $this->list[$style->getCode()] = $style;
  }

  public function getRegisteredColors(){
    return $this->knownColors;
  }

  public function getRegisteredStyles(){
    return $this->knownStyles;
  }

  private function registerColors(){
    foreach([
      "Black", "DarkBlue", "DarkGreen", "DarkAqua", "DarkRed", "DarkPurple",
      "Gold", "Gray", "DarkGray", "Blue", "Green", "Aqua", "Red", "LightPurple",
      "Yellow", "White"
    ] as $color){
      $class = "\\solo\\scolor\\color\\" . $color;
      $colorInstance = new $class();
      $this->registerColor($colorInstance);
    }
  }

  private function registerStyles(){
    foreach([
      "Bold", "Italic", "Obfuscated", "Reset"
    ] as $style){
      $class = "\\solo\\scolor\\style\\" . $style;
      $styleInstance = new $class();
      $this->registerStyle($styleInstance);
    }
  }

  public function colorize(string $raw, CommandSender $sender = null){
    if(strpos($raw, '§') === false && strpos($raw, '&') === false){
      return $raw;
    }
    $len = strlen($raw);
    $offset = 0;
    $ret = '';
    while($offset < $len){
      //$token = mb_substr($raw, $offset, $offset + 1);
      $token = $raw{$offset};
      if($token == '&' || $token == '§'){
        $offset++;
        if($offset < $len){
          //$token = mb_substr($raw, $offset, $offset + 1);
          $code = $raw{$offset};
          $color = $this->list[$code] ?? null;
          if($color !== null){
            if($sender !== null && !$sender->hasPermission($color->getPermission())){
              // Can't use the color
              if($token == '&'){
                $ret .= '&';
              }
              $offset++;
              continue;
            }
            // has permission to use the color
            $ret .= '§' . $code;
            $offset++;
            continue;
          }
          // color not exists
          if($token == '&'){
            $ret .= '&';
          }
          $ret .= $code;
          $offset++;
          continue;
        }
        // offset out of range
        $offset++;
        continue;
      }
      $ret .= $token; // just character
      $offset++;
    }
    return $ret;
  }

  /**
   * @priority LOW
   *
   * @ignoreCancelled true
   */
  public function handlePlayerCommandPreprocess(PlayerCommandPreprocessEvent $event){
    if($event->getPlayer()->isOp() || $this->config->get("allow-color-on-chat")){
      $event->setMessage($this->colorize($event->getMessage(), $event->getPlayer()));
    }else{
      $event->setMessage(TextFormat::clean($event->getMessage()));
    }
  }

  /**
   * @priority LOW
   *
   * @ignoreCancelled true
   */
  public function handleSignChange(SignChangeEvent $event){
    if($event->getPlayer()->isOp() || $this->config->get("allow-color-on-sign")){
      for($i = 0; $i < 4; $i++){
        $event->setLine($i, $this->colorize($event->getLine($i), $event->getPlayer()));
      }
    }else{
      for($i = 0; $i < 4; $i++){
        $event->setLine($i, TextFormat::clean($event->getLine($i)));
      }
    }
  }

  /**
   * @priority LOW
   *
   * @ignoreCancelled true
   */
  public function handleServerCommand(ServerCommandEvent $event){
    $colorized = $this->colorize($event->getCommand());
    $event->setCommand($colorized);
  }
}
