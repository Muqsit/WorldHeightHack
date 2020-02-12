# WorldHeightHack
A plugin that stacks worlds to create an infinite height effect (https://forums.pocketmine.net/threads/exceeding-128-block-limit-height.16456/ 👀).

# How It Works
The world starts from bedrock level (world: `world`, worldY: `0`) and can go as high as possible (world: `world.n`, worldY: `n`). Currently there's a hard-limit of 4 but can easily be modified.<br>
4 subchunks (64 blocks) from the top (i.e from y=(256-64) to y=256) are used to synchronize the chunks from the world above to smoothen the transition effect when moving from one world to another.<br>
Similarly, 4 subchunks from the bottom are used to synchronize subchunks from the world below.<br>
So you are left with 8 subchunks (128 blocks) of modifyable space in each world (except the first and the last worlds — they'll have 4 extra subchunks of modifyable space as there's either no world above or below).
