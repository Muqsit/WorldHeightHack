<?php

declare(strict_types=1);

namespace muqsit\worldheighthack\world;

use pocketmine\block\VanillaBlocks;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockSpreadEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\world\ChunkLoadEvent;
use pocketmine\event\world\ChunkUnloadEvent;
use pocketmine\event\world\WorldLoadEvent;
use pocketmine\event\world\WorldUnloadEvent;
use pocketmine\math\Vector3;
use pocketmine\world\Position;

final class WorldListener implements Listener{

	/** @var WorldManager */
	private $manager;

	public function __construct(WorldManager $manager){
		$this->manager = $manager;
	}

	/**
	 * @param WorldLoadEvent $event
	 * @priority MONITOR
	 */
	public function onWorldLoad(WorldLoadEvent $event) : void{
		$this->manager->onWorldLoad($event->getWorld());
	}

	/**
	 * @param WorldUnloadEvent $event
	 * @priority MONITOR
	 */
	public function onWorldUnload(WorldUnloadEvent $event) : void{
		$this->manager->onWorldUnload($event->getWorld());
	}

	/**
	 * @param ChunkLoadEvent $event
	 * @priority MONITOR
	 */
	public function onChunkLoad(ChunkLoadEvent $event) : void{
		$chunk = $event->getChunk();
		$this->manager->get($event->getWorld())->onChunkLoad($chunk->getX(), $chunk->getZ());
	}

	/**
	 * @param ChunkUnloadEvent $event
	 * @priority MONITOR
	 */
	public function onChunkUnload(ChunkUnloadEvent $event) : void{
		$chunk = $event->getChunk();
		$this->manager->get($event->getWorld())->onChunkUnload($chunk->getX(), $chunk->getZ());
	}

	/**
	 * @param BlockPlaceEvent $event
	 * @priority HIGHEST
	 */
	public function onBlockPlace(BlockPlaceEvent $event) : void{
		$block = $event->getBlock();
		$pos = $block->getPos();
		$new_pos = $this->manager->translateCoordinates($pos);
		if($pos->world !== $new_pos->world){
			$event->setCancelled();
			$new_pos->world->setBlock($new_pos, $block);
		}
	}

	/**
	 * @param BlockBreakEvent $event
	 * @priority HIGHEST
	 */
	public function onBlockBreak(BlockBreakEvent $event) : void{
		$block = $event->getBlock();
		$pos = $block->getPos();
		$new_pos = $this->manager->translateCoordinates($pos);
		if($pos->world !== $new_pos->world){
			$event->setCancelled();
			$new_pos->world->setBlock($new_pos, VanillaBlocks::AIR());
		}
	}

	/**
	 * @param PlayerMoveEvent $event
	 * @priority LOWEST
	 */
	public function onPlayerMove(PlayerMoveEvent $event) : void{
		$from = $event->getFrom();
		$to = $event->getTo();

		$from_y = $from->getFloorY();
		$to_y = $to->getFloorY();
		if($from_y !== $to_y){
			$above_lim = (16 - 4) << 4;
			if($to_y >= $above_lim){
				$from_world_y = $this->manager->get($from->getWorld())->getY();
				$to_world_y = $this->manager->getAt($from_world_y + 1);
				if($to_world_y !== null){
					$location = clone $to;
					$location->y = 4 << 4;
					$location->setWorld($to_world_y->getWorld());
					$event->setTo($location);
					return;
				}
			}

			$below_lim = 4 << 4;
			if($to_y < $below_lim){
				$from_world_y = $this->manager->get($from->getWorld())->getY();
				$to_world_y = $this->manager->getAt($from_world_y - 1);
				if($to_world_y !== null){
					$location = clone $to;
					$location->y = (16 - 4) << 4;
					$location->setWorld($to_world_y->getWorld());
					$event->setTo($location);
					return;
				}
			}
		}
	}
}