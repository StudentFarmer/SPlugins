<?php

namespace solo\sitemdb\command;

use pocketmine\Player;
use pocketmine\command\CommandSender;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\item\Item;

use solo\sitemdb\SItemDB;
use solo\sitemdb\SItemDBCommand;
use solo\sitemdb\Process;

class ItemViewCommand extends SItemDBCommand{

  private $owner;

  public function __construct(SItemDB $owner){
    parent::__construct("itemview", "see item info in your hand or touch to see block info", "/itemview");
    $this->setPermission("sitemdb.command.itemview");

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
    if($this->owner->getProcessManager()->getProcess($sender) instanceof ItemViewProcess){
      $this->owner->getProcessManager()->removeProcess($sender);
      $sender->sendMessage(SItemDB::$prefix . "ItemDB 정보 확인 모드를 껐습니다.");
      return true;
    }
    $this->owner->getProcessManager()->setProcess($sender, new ItemViewProcess($sender));
    return true;
  }
}

class ItemViewProcess extends Process{

  public function __construct(Player $player){
    parent::__construct($player);

    $this->player->sendMessage(SItemDB::$prefix . "블럭을 터치하면 블럭의 정보를 확인할 수 있습니다.");
    $this->player->sendMessage(SItemDB::$prefix . "아이템을 들면 아이템의 정보를 확인할 수 있습니다.");
    $this->player->sendMessage(SItemDB::$prefix . "명령어를 한번 더 입력하면 진행중이던 작업이 중지됩니다.");
  }

  public function getName() : string{
    return "ItemDB 정보 확인 모드";
  }

  public function handlePlayerInteract(PlayerInteractEvent $event){
    if($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK){
      $block = $event->getBlock();

      $info = SItemDB::getInstance()->getItemInfoByItem(Item::get($block->getId(), $block->getDamage()));
      $message = [
        ($info === null ? "데이터베이스에 등록되지 않은 아이템입니다." : "데이터베이스에 등록된 이름 : " . $info->getName()) . " (" . $block->getName() . ")",
        "id : " . $block->getId() . ", Damage : " . $block->getDamage()
      ];
      $this->player->sendPopup("§b" . implode("\n", $message));
    }
  }

  public function handlePlayerItemHeld(PlayerItemHeldEvent $event){
    $item = $event->getItem();
    $info = SItemDB::getInstance()->getItemInfoByItem($item);
    $message = [
      ($info === null ? "데이터베이스에 등록되지 않은 아이템입니다." : "데이터베이스에 등록된 이름 : " . $info->getName()) . " (" . $item->getName() . ")",
      "id : " . $item->getId() . ", Damage : " . $item->getDamage()
    ];
    $this->player->sendPopup("§b" . implode("\n", $message));
  }
}
