<?php

declare(strict_types=1);

namespace Bluelands\mc crates\commands;

use Bluelands\mc crates\libs\CortexPE\Commando\args\RawStringArgument;
use Bluelands\mc crates\libs\CortexPE\Commando\BaseCommand;
use Bluelands\mc crates\libs\CortexPE\Commando\exception\ArgumentOrderException;
use Bluelands\mc crates\mc crates;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class CrateCommand extends BaseCommand
{
    /** @var mc crates */
    private $plugin;

    /**
     * @param string[] $aliases
     */
    public function __construct(mc crates $plugin, string $name, string $description = "", array $aliases = [])
    {
        $this->plugin = $plugin;
        parent::__construct($name, $description, $aliases);
    }

    /**
     * @param array $args
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "Please use this in-game.");
            return;
        }
        if (!isset($args["type"])) {
            $sender->sendMessage("Usage: /crate <type>");
            return;
        }
        if ($args["type"] === "cancel") {
            if (!mc crates::inCrateCreationMode($sender)) {
                $sender->sendMessage(TextFormat::RED . "You are not in crate creation mode.");
                return;
            }
            mc crates::setInCrateCreationMode($sender, null);
            $sender->sendMessage(TextFormat::GREEN . "Crate creation cancelled.");
            return;
        }
        $crate = mc crates::getCrate($args["type"]);
        if ($crate === null) {
            $sender->sendMessage(TextFormat::RED . "Invalid crate.");
            return;
        }
        mc crates::setInCrateCreationMode($sender, $crate);
        $sender->sendMessage(TextFormat::GREEN . "Please tap a chest block to create a crate, or use /crate cancel to cancel.");
    }

    /**
     * @throws ArgumentOrderException
     */
    public function prepare(): void
    {
        $this->setPermission("mc crates.command.crate");
        $this->registerArgument(0, new RawStringArgument("type"));
    }
}