<?php

namespace solo\sitemdb\command;

use pocketmine\Player;
use pocketmine\command\CommandSender;

use solo\sitemdb\SItemDB;
use solo\sitemdb\SItemDBCommand;

class ItemGetCommand extends SItemDBCommand{

  private $owner;

  public function __construct(SItemDB $owner){
    parent::__construct("itemget", "get item from itemdb", "/itemget <item name> [count]");
    $this->setPermission("sitemdb.command.itemget");

    $this->owner = $owner;
  }

  public function _execute(CommandSender $sender, string $label, array $args) : bool{
    if(!$sender instanceof Player){
      $sender->sendMessage(SItemDB::$prefix . "인게임에서만 사용할 수 있습니다.");
      return true;
    }
    if(!$sender->hasPermission($this->getPermission())){
      $sender->sendMessage(SItemDB::$prefix . "이 명령을 사용할 권한이 없습니다.");
      return true;
    }
    if(!isset($args[0])){
      $sender->sendMessage(SItemDB::$prefix . "사용법 : " . $this->getUsage());
      return true;
    }
    $info = $this->owner->getItemInfo($args[0]);
    if($info === null){
      $sender->sendMessage(SItemDB::$prefix . $args[0] . " 아이템은 존재하지 않습니다.");
      $items = $this->owner->searchItem($args[0]);
      if(count($items) > 0){
        $result = [];
        foreach($items as $item){
          if(($info = $this->owner->getItemInfoByItem($item)) !== null){
            $result[] = $info->getName();
          }
        }
        $sender->sendMessage(SItemDB::$prefix . "\"" . $args[0] . "\" 단어가 포함된 아이템 : " . implode(", ", $result));
      }
      return true;
    }
    $item = $info->getItem();
    $count = $args[1] ?? $item->getMaxStackSize();
    if(!is_numeric($count)){
      $sender->sendMessage(SItemDB::$prefix . "수량은 숫자를 입력해주세요.");
      return true;
    }
    $item->setCount($count);
    $sender->getInventory()->addItem($item);
    $sender->sendMessage(SItemDB::$prefix . $info->getName() . " 을(를) " . $count . "개 만큼 얻었습니다.");
    return true;
  }
}
