<?php

declare(strict_types=1);

namespace muqsit\worldheighthack;

use muqsit\worldheighthack\generator\VoidGenerator;
use muqsit\worldheighthack\world\WorldManager;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;
use pocketmine\world\generator\GeneratorManager;

final class Loader extends PluginBase{

	/** @var WorldManager */
	private $world_manager;

	protected function onLoad() : void{
        GeneratorManager::getInstance()->addGenerator(VoidGenerator::class,"void", fn() => null, true);
		#GeneratorManager::addGenerator(, "void");
	}

	protected function onEnable() : void{
		$this->world_manager = new WorldManager($this);

		$this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function() : void{
			foreach($this->getServer()->getOnlinePlayers() as $player){
				$pos = $player->getPosition();
				$player->sendTip(
					"World: " . $pos->getWorld()->getFolderName() . TextFormat::EOL .
					"Position: " . $pos->x . ", ". (($pos->y - (4 << 4)) + ($this->world_manager->get($pos->world)->getY() << 7)) . ", " . $pos->z . TextFormat::EOL .
					"Actual Position: " . $pos->x . ", ". $pos->y . ", " . $pos->z
				);
			}
		}), 1);
	}
}