<?php

namespace solo\sitemdb\command;

use pocketmine\Player;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;

use solo\sitemdb\SItemDB;
use solo\sitemdb\SItemDBCommand;

class ItemUnregisterCommand extends SItemDBCommand{

  private $owner;

  public function __construct(SItemDB $owner){
    parent::__construct("itemunregister", "unregister item in my hand or from name", "/itemunregister <item name>");
    $this->setPermission("sitemdb.command.itemunregister");

    $this->owner = $owner;
  }

  public function _execute(CommandSender $sender, string $label, array $args) : bool{
    if(!$sender->hasPermission($this->getPermission())){
      $sender->sendMessage(SItemDB::$prefix . "이 명령을 사용할 권한이 없습니다.");
      return true;
    }
    $info = null;
    if(isset($args[0])){
      $info = $this->owner->getItemInfo($args[0]);
      if($info === null){
        $sender->sendMessage(SItemDB::$prefix . "\"" . $args[0] . "\" 아이템은 데이터베이스에 존재하지 않습니다.");
        return true;
      }
    }else{
      if(!$sender instanceof Player){
        $sender->sendMessage(SItemDB::$prefix . "사용법 : " . $this->getUsage());
        return true;
      }
      $itemInHand = $sender->getInventory()->getItemInHand();
      if($itemInHand->getId() === Item::AIR){
        $sender->sendMessage(SItemDB::$prefix . "손에 아이템을 든 후 명령어를 실행해주세요.");
        return true;
      }
      $info = $this->owner->getItemInfoByItem($itemInHand);
      if($info === null){
        $sender->sendMessage(SItemDB::$prefix . "손에 들고 있는 아이템은 데이터베이스에 존재하지 않습니다.");
        return true;
      }
    }

    $this->owner->unregisterItemInfo($info);
    $sender->sendMessage(SItemDB::$prefix . "\"" . $info->getName() . "\" 아이템을 데이터베이스에서 등록해제 하였습니다.");
    return true;
  }
}
