<?php

namespace solo\sitemdb\command;

use pocketmine\Player;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;

use solo\sitemdb\SItemDB;
use solo\sitemdb\SItemDBCommand;

class ItemNameCommand extends SItemDBCommand{

  private $owner;

  public function __construct(SItemDB $owner){
    parent::__construct("itemname", "change item name", "/itemname <custom name>");
    $this->setPermission("sitemdb.command.itemname");

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
      $sender->sendMessage(SItemDB::$prefix . "손에 아이템을 든 후 명령어를 실행해주세요.");
      return true;
    }
    if(!isset($args[0])){
      $sender->sendMessage(SItemDB::$prefix . $this->getUsage() . " - " . $this->getDescription());
      return true;
    }

    $item->setCustomName(implode(" ", $args));
    $sender->getInventory()->setItemInHand($item);
    $sender->sendMessage(SItemDB::$prefix . "아이템의 이름을 변경하였습니다 : " . $item->getCustomName());

    return true;
  }
}
