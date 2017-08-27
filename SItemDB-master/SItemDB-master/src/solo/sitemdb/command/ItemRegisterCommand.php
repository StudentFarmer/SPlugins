<?php

namespace solo\sitemdb\command;

use pocketmine\Player;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;

use solo\sitemdb\SItemDB;
use solo\sitemdb\SItemDBCommand;

class ItemRegisterCommand extends SItemDBCommand{

  private $owner;

  public function __construct(SItemDB $owner){
    parent::__construct("itemregister", "register item in my hand", "/itemregister [등록할이름]");
    $this->setPermission("sitemdb.command.itemregister");

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
    $itemInHand = $sender->getInventory()->getItemInHand();
    if($itemInHand->getId() === Item::AIR){
      $sender->sendMessage(SItemDB::$prefix . "손에 아이템을 든 후 명령어를 실행해주세요.");
      return true;
    }

    $name;
    if(isset($args[0])){
      $name = implode(' ', $args);
    }else if($itemInHand->hasCustomName()){
      $name = $itemInHand->getCustomName();
    }else{
      $name = $itemInHand->getName();
    }

    $item = $this->owner->getItem($name);

    if($item !== null){
      $sender->sendMessage(SItemDB::$prefix . "\"" . $name . "\" 이름은 이미 데이터베이스에 등록되어 있습니다.");
      $sender->sendMessage(SItemDB::$prefix . "해당 아이템의 등록을 해제한 뒤 진행해주세요.");
      return true;
    }

    $info = $this->owner->getItemInfoByItem($itemInHand);
    if($info !== null){
      $sender->sendMessage(SItemDB::$prefix . "해당 아이템과 동일한 아이템이 이미 데이터베이스에 \"" . $info->getName() . "\" 으로 등록되어있습니다.");
      $sender->sendMessage(SItemDB::$prefix . "해당 아이템의 등록을 해제한 뒤 진행해주세요.");
      return true;
    }

    $this->owner->registerItem($itemInHand, $name);
    $sender->sendMessage(SItemDB::$prefix . "성공적으로 데이터베이스에 등록하였습니다.");
    return true;
  }
}
