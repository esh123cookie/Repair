<?php

namespace repair;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class Main extends PluginBase
{
    public function onEnable()
    {
        $this->getLogger()->info(TextFormat::GREEN."Repair aktviert.");
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {


        if($command->getName() === "repair") {
            $index = $sender->getInventory()->getHeldItemIndex();
            $item = $sender->getInventory()->getItem($index);
            $repairPrice = 500;
            if($sender->hasPermission("repair.repair.free")) {
                $repairPrice = 0;
            }
            if (!isset($args[0])) {
                if($repairPrice == 0) {
                    $sender->sendMessage(TextFormat::AQUA . "[Repair] Eine Item Reparatur ist kostenlos für dich.");
                    return true;
                }
                $sender->sendMessage(TextFormat::AQUA . "[Repair] Eine Item Reparatur kostet dich derzeit " . $repairPrice . "$, wenn du fortfahren möchtest führe /repair confirm aus.");
                return true;
            }
            if ($args[0] === "confirm") {
                if ($item->getDamage() > 0) {
                    $senderMoney = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI")->myMoney($sender->getName());
                    if($senderMoney < $repairPrice) {
                        $sender->sendMessage(TextFormat::RED . "[Fehler] Du benötigst ". $repairPrice ."$ um ein Item zu reparieren.");
                        return true;
                    }

                    $sender->getInventory()->setItem($index, $item->setDamage(0));
                    $this->getServer()->getPluginManager()->getPlugin("EconomyAPI")->reduceMoney($sender->getName(), $repairPrice);
                    $sender->sendMessage(TextFormat::GREEN . "[Repair] Item wurde erfolgreich repariert.");

                    return true;
                } else {
                    $sender->sendMessage(TextFormat::RED . "[Fehler] Dieses Item ist nicht beschädigt oder kann nicht repariert werden.");
                    return true;
                }
                return true;
            }
            return true;

        } else if($command->getName() === "rename") {
            $sender->sendMessage("Demnächst verfügbar!");
            return true;
            $index = $sender->getInventory()->getHeldItemIndex();
            $item = $sender->getInventory()->getItem($index);
            $renamePrice = 500;

            $senderMoney = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI")->myMoney($sender->getName());
            if($senderMoney < $renamePrice) {
                $sender->sendMessage(TextFormat::RED . "[Fehler] Du benötigst ".$renamePrice."$ um ein Item umzubenennen!");
                return true;
            }
            if(!$args[0]) {
                $sender->sendMessage(TextFormat::RED."[Fehler] Bitte gebe einen Namen für das Item an!");
                return true;
            }
            $name = implode(" ", $args);
            if(strlen($name) > 32) {
                $sender->sendMessage(TextFormat::RED."[Fehler] Der Name darf nicht länger als 32 Zeichen sein!");
                return true;
            }
            $sender->getInventory()->setItem($index, $item->setCustomName(§name));
            $this->getServer()->getPluginManager()->getPlugin("EconomyAPI")->reduceMoney($sender->getName(), $renamePrice);
        }
    }
}