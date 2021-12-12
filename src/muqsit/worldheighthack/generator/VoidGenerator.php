<?php

declare(strict_types=1);

namespace muqsit\worldheighthack\generator;

use pocketmine\world\ChunkManager;
use pocketmine\world\generator\Generator;

class VoidGenerator extends Generator{

	public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ) : void{
	}

	public function populateChunk(ChunkManager $world, int $chunkX, int $chunkZ) : void{
	}
}