<?php

namespace solo\sitemdb\command;

use pocketmine\Player;
use pocketmine\command\CommandSender;

use solo\sitemdb\SItemDB;
use solo\sitemdb\SItemDBCommand;

class ItemGiveCommand extends SItemDBCommand{

  private $owner;

  public function __construct(SItemDB $owner){
    parent::__construct("itemgive", "give player item from itemdb", "/itemgive <player> <item name> [count]");
    $this->setPermission("sitemdb.command.itemgive");

    $this->owner = $owner;
  }

  public function _execute(CommandSender $sender, string $label, array $args) : bool{
    if(!isset($args[1])){
      $sender->sendMessage(SItemDB::$prefix . "사용법 : " . $this->getUsage());
      return true;
    }
    if(!$sender->hasPermission($this->getPermission())){
      $sender->sendMessage(SItemDB::$prefix . "이 명령을 사용할 권한이 없습니다.");
      return true;
    }
    $player = $this->owner->getServer()->getPlayer($args[0]);
    if($player === null){
      $sender->sendMessage(SItemDB::$prefix . $args[0] . " 플레이어는 존재하지 않습니다.");
      return true;
    }
    $info = $this->owner->getItemInfo($args[1]);
    if($info === null){
      $sender->sendMessage(SItemDB::$prefix . $args[1] . " 아이템은 존재하지 않습니다.");
      $items = $this->owner->searchItem($args[1]);
      if(count($items) > 0){
        $result = [];
        foreach($items as $item){
          if(($info = $this->owner->getItemInfoByItem($item)) !== null){
            $result[] = $info->getName();
          }
        }
        $sender->sendMessage(SItemDB::$prefix . "\"" . $args[1] . "\" 단어가 포함된 아이템 : " . implode(", ", $result));
      }
      return true;
    }
    $item = $info->getItem();
    $count = $args[2] ?? $item->getMaxStackSize();
    if(!is_numeric($count)){
      $sender->sendMessage(SItemDB::$prefix . "수량은 숫자를 입력해주세요.");
      return true;
    }
    $item->setCount($count);
    $player->getInventory()->addItem($item);
    $sender->sendMessage(SItemDB::$prefix . $player->getName() . " 에게 " . $info->getName() . " 을(를) " . $count . "개 만큼 주었습니다.");
    return true;
  }
}
