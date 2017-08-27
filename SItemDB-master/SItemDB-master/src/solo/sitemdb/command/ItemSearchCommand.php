<?php

namespace solo\sitemdb\command;

use pocketmine\command\CommandSender;

use solo\sitemdb\SItemDB;
use solo\sitemdb\SItemDBCommand;

class ItemSearchCommand extends SItemDBCommand{

  private $owner;

  public function __construct(SItemDB $owner){
    parent::__construct("itemsearch", "search item from keyword in itemdb", "/itemsearch <item name>");
    $this->setPermission("sitemdb.command.itemsearch");

    $this->owner = $owner;
  }

  public function _execute(CommandSender $sender, string $label, array $args) : bool{
    if(!$sender->hasPermission($this->getPermission())){
      $sender->sendMessage(SItemDB::$prefix . "이 명령을 사용할 권한이 없습니다.");
      return true;
    }
    $keyword = $args[0] ?? "";
    if($keyword === ""){
      $sender->sendMessage(SItemDB::$prefix . "사용법 : " . $this->getUsage());
      return true;
    }
    $items = $this->owner->searchItem($keyword);
    $count = 0;
    foreach($items as $name => $item){
      $sender->sendMessage(SItemDB::$prefix . "[" . ++$count . "] " . str_replace($keyword, "§a" . $keyword . "§7", $name) . " => " . $item->getId() . ":" . $item->getDamage() . ", name:" . $item->getName() . ($item->hasCustomName() ? ", customname:" . $item->getCustomName() : ''));
    }
    $sender->sendMessage(SItemDB::$prefix . "\"" . $keyword . "\" (으)로 검색한 결과 총 " . $count . "개의 결과가 나왔습니다.");
    return true;
  }
}
