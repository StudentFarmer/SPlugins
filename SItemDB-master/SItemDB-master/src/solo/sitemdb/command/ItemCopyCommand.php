<?php

namespace solo\sitemdb\command;

use pocketmine\Player;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;

use solo\sitemdb\SItemDB;
use solo\sitemdb\SItemDBCommand;

class ItemCopyCommand extends SItemDBCommand{

  private $owner;

  public function __construct(SItemDB $owner){
    parent::__construct("itemcopy", "copy item in my hand", "/itemcopy [count]");
    $this->setPermission("sitemdb.command.itemcopy");

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
    $item = $sender->getInventory()->getItemInHand();
    if($item->getId() === Item::AIR){
      $sender->sendMessage(SItemDB::$prefix . "아이템을 손에 든 후 명령을 실행해주세요.");
      return true;
    }
    $count = intval($args[0]) ?? $item->getMaxStackSize();
    if(!preg_match("/[0-9]+/", $count) || $count < 1){
      $sender->sendMessage(SItemDB::$prefix . "수량은 숫자로 입력해주세요.");
      return true;
    }

    $sender->getInventory()->addItem(Item::get($item->getId(), $item->getDamage(), $count, $item->getCompoundTag()));
    $sender->sendMessage(SItemDB::$prefix . "아이템을 " . $count . "개 만큼 복제하였습니다.");

    return true;
  }
}
