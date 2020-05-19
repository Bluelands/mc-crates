<?php

declare(strict_types=1);

namespace Bluelands\mc crates\commands;

use Bluelands\mc crates\libs\CortexPE\Commando\args\IntegerArgument;
use Bluelands\mc crates\libs\CortexPE\Commando\args\RawStringArgument;
use Bluelands\mc crates\libs\CortexPE\Commando\BaseCommand;
use Bluelands\mc crates\libs\CortexPE\Commando\exception\ArgumentOrderException;
use Bluelands\mc crates\mc crates;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class KeyAllCommand extends BaseCommand
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
        if (!isset($args["type"])) {
            $sender->sendMessage("Usage: /keyall <type>");
            return;
        }
        /** @var int $amount */
        $amount = $args["amount"] ?? 1;
        if (!is_numeric($amount)) {
            $sender->sendMessage(TextFormat::RED . "Amount must be numeric.");
            return;
        }
        $crate = mc crates::getCrate($args["type"]);
        if ($crate === null) {
            $sender->sendMessage(TextFormat::RED . "Invalid crate type.");
            return;
        }
        foreach ($this->plugin->getServer()->getOnlinePlayers() as $player) {
            $crate->giveKey($player, $amount);
            $player->sendMessage(TextFormat::GREEN . "You've received the " . $crate->getName() . " key.");
        }
        $sender->sendMessage(TextFormat::GREEN . "You've given all online players the " . $crate->getName() . " key.");

    }

    /**
     * @throws ArgumentOrderException
     */
    public function prepare(): void
    {
        $this->setPermission("piggycrates.command.keyall");
        $this->registerArgument(0, new RawStringArgument("type"));
        $this->registerArgument(1, new IntegerArgument("amount", true));
    }
}