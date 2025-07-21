/**
 * World Manager - Handles game world, NPCs, monsters, and environment
 */

const winston = require('winston');
const EventEmitter = require('events');

class WorldManager extends EventEmitter {
    constructor(io) {
        super();
        this.io = io;
        this.gameWorld = new Map(); // zoneId -> zone data
        this.npcs = new Map(); // npcId -> npc instance
        this.monsters = new Map(); // monsterId -> monster instance
        this.worldObjects = new Map(); // objectId -> object instance
        this.playerZones = new Map(); // playerId -> zoneId
        this.zoneInstances = new Map(); // zoneId -> instance data
        
        this.logger = winston.createLogger({
            level: 'info',
            format: winston.format.combine(
                winston.format.timestamp(),
                winston.format.json()
            ),
            defaultMeta: { service: 'world-manager' },
            transports: [
                new winston.transports.File({ filename: 'logs/world-manager.log' }),
                new winston.transports.Console()
            ]
        });

        // World configuration
        this.WORLD_CONFIG = {
            maxPlayersPerZone: 50,
            npcRespawnTime: 30000, // 30 seconds
            monsterRespawnTime: 60000, // 1 minute
            objectInteractionCooldown: 5000, // 5 seconds
            weatherUpdateInterval: 300000, // 5 minutes
            dayNightCycleLength: 1440000, // 24 minutes = 1 day
            seasonLength: 10080000 // 1 week = 1 season
        };

        this.initializeWorld();
        this.setupWorldLoop();
    }

    initializeWorld() {
        // Define game zones
        this.createZone('starter_town', {
            name: 'Starter Town',
            description: 'A peaceful town where new adventurers begin their journey',
            type: 'town',
            level: 1,
            pvp: false,
            boundaries: { x: [-100, 100], y: [-100, 100] },
            spawns: [{ x: 0, y: 0, type: 'player' }],
            npcs: ['merchant_basic', 'quest_giver_novice', 'trainer_combat'],
            monsters: [],
            objects: ['fountain_health', 'bulletin_board', 'storage_chest']
        });

        this.createZone('forest_of_shadows', {
            name: 'Forest of Shadows',
            description: 'A dark forest filled with dangerous creatures',
            type: 'wilderness',
            level: 5,
            pvp: true,
            boundaries: { x: [-500, 500], y: [-500, 500] },
            spawns: [{ x: -400, y: -400, type: 'player' }],
            npcs: ['hermit_sage'],
            monsters: ['shadow_wolf', 'dark_spider', 'evil_tree'],
            objects: ['ancient_altar', 'treasure_chest', 'magic_spring']
        });

        this.createZone('crystal_caves', {
            name: 'Crystal Caves',
            description: 'Underground caves filled with precious crystals and monsters',
            type: 'dungeon',
            level: 15,
            pvp: true,
            boundaries: { x: [-300, 300], y: [-300, 300] },
            spawns: [{ x: 0, y: -280, type: 'player' }],
            npcs: ['crystal_merchant'],
            monsters: ['crystal_golem', 'cave_bat', 'underground_worm'],
            objects: ['crystal_node', 'ancient_rune', 'cave_painting']
        });

        this.createZone('dragon_valley', {
            name: 'Dragon Valley',
            description: 'A legendary valley where ancient dragons dwell',
            type: 'raid',
            level: 50,
            pvp: true,
            boundaries: { x: [-1000, 1000], y: [-1000, 1000] },
            spawns: [{ x: -800, y: -800, type: 'player' }],
            npcs: ['dragon_keeper'],
            monsters: ['young_dragon', 'dragon_knight', 'ancient_dragon'],
            objects: ['dragon_altar', 'legendary_chest', 'portal_stone']
        });

        this.createZone('mystic_lake', {
            name: 'Mystic Lake',
            description: 'A serene lake with magical properties',
            type: 'special',
            level: 25,
            pvp: false,
            boundaries: { x: [-200, 200], y: [-200, 200] },
            spawns: [{ x: 0, y: 150, type: 'player' }],
            npcs: ['water_spirit', 'fishing_master'],
            monsters: ['water_elemental', 'giant_fish'],
            objects: ['wishing_well', 'magical_boat', 'fishing_spot']
        });

        // Initialize NPCs
        this.initializeNPCs();
        
        // Initialize monsters
        this.initializeMonsters();
        
        // Initialize world objects
        this.initializeWorldObjects();

        this.logger.info('Game world initialized', {
            zones: this.gameWorld.size,
            npcs: this.npcs.size,
            monsters: this.monsters.size,
            objects: this.worldObjects.size
        });
    }

    createZone(zoneId, config) {
        const zone = {
            id: zoneId,
            ...config,
            players: new Set(),
            activeNPCs: new Set(),
            activeMonsters: new Set(),
            activeObjects: new Set(),
            lastUpdate: Date.now(),
            weather: 'clear',
            timeOfDay: 'day'
        };

        this.gameWorld.set(zoneId, zone);
        
        // Create zone instance
        this.zoneInstances.set(zoneId, {
            players: new Map(), // playerId -> player data
            npcs: new Map(), // npcId -> npc instance
            monsters: new Map(), // monsterId -> monster instance
            objects: new Map(), // objectId -> object instance
            events: [],
            lastCleanup: Date.now()
        });
    }

    initializeNPCs() {
        // Define NPC templates
        const npcTemplates = {
            merchant_basic: {
                name: 'Merchant Tom',
                type: 'merchant',
                level: 5,
                health: 100,
                dialogue: ['Welcome to my shop!', 'I have the finest goods!'],
                shop: {
                    items: ['health_potion', 'mana_potion', 'basic_sword', 'leather_armor'],
                    prices: { health_potion: 50, mana_potion: 30, basic_sword: 200, leather_armor: 150 }
                },
                position: { x: 20, y: 30 },
                respawn: true
            },
            quest_giver_novice: {
                name: 'Elder Maria',
                type: 'quest_giver',
                level: 10,
                health: 150,
                dialogue: ['I have tasks for brave adventurers!', 'Help us protect the town!'],
                quests: ['kill_rats', 'collect_herbs', 'deliver_message'],
                position: { x: -20, y: 40 },
                respawn: true
            },
            trainer_combat: {
                name: 'Captain Steel',
                type: 'trainer',
                level: 20,
                health: 300,
                dialogue: ['Train hard, fight harder!', 'I can teach you combat skills!'],
                skills: ['sword_mastery', 'shield_bash', 'combat_training'],
                position: { x: 50, y: -20 },
                respawn: true
            },
            hermit_sage: {
                name: 'Wise Hermit',
                type: 'sage',
                level: 50,
                health: 200,
                dialogue: ['Knowledge is power, young one', 'The forest holds many secrets'],
                services: ['identify_item', 'enhance_equipment'],
                position: { x: 200, y: 300 },
                respawn: true
            },
            crystal_merchant: {
                name: 'Crystal Trader',
                type: 'merchant',
                level: 30,
                health: 250,
                dialogue: ['These crystals are magnificent!', 'Rare gems for brave miners!'],
                shop: {
                    items: ['crystal_shard', 'magic_crystal', 'enhancement_stone'],
                    prices: { crystal_shard: 100, magic_crystal: 500, enhancement_stone: 1000 }
                },
                position: { x: 0, y: 0 },
                respawn: true
            }
        };

        // Create NPC instances for each zone
        for (const [zoneId, zone] of this.gameWorld) {
            for (const npcType of zone.npcs) {
                if (npcTemplates[npcType]) {
                    this.createNPC(zoneId, npcType, npcTemplates[npcType]);
                }
            }
        }
    }

    initializeMonsters() {
        const monsterTemplates = {
            shadow_wolf: {
                name: 'Shadow Wolf',
                type: 'beast',
                level: 8,
                health: 120,
                damage: 25,
                experience: 50,
                loot: ['wolf_pelt', 'wolf_fang'],
                aggressive: true,
                roamRange: 50,
                respawnTime: 60000
            },
            dark_spider: {
                name: 'Dark Spider',
                type: 'insect',
                level: 6,
                health: 80,
                damage: 20,
                experience: 35,
                loot: ['spider_silk', 'poison_gland'],
                aggressive: true,
                roamRange: 30,
                respawnTime: 45000
            },
            crystal_golem: {
                name: 'Crystal Golem',
                type: 'elemental',
                level: 20,
                health: 400,
                damage: 60,
                experience: 200,
                loot: ['crystal_core', 'golem_fragment'],
                aggressive: false,
                roamRange: 20,
                respawnTime: 300000
            },
            young_dragon: {
                name: 'Young Dragon',
                type: 'dragon',
                level: 45,
                health: 2000,
                damage: 150,
                experience: 1000,
                loot: ['dragon_scale', 'dragon_blood', 'dragon_claw'],
                aggressive: true,
                roamRange: 100,
                respawnTime: 1800000
            }
        };

        // Spawn monsters in zones
        for (const [zoneId, zone] of this.gameWorld) {
            for (const monsterType of zone.monsters) {
                if (monsterTemplates[monsterType]) {
                    const spawnCount = this.getMonsterSpawnCount(zone.type, monsterType);
                    for (let i = 0; i < spawnCount; i++) {
                        this.spawnMonster(zoneId, monsterType, monsterTemplates[monsterType]);
                    }
                }
            }
        }
    }

    initializeWorldObjects() {
        const objectTemplates = {
            fountain_health: {
                name: 'Healing Fountain',
                type: 'healing',
                effect: { heal: 100, mana: 50 },
                cooldown: 300000, // 5 minutes
                usable: true,
                position: { x: 0, y: 50 }
            },
            ancient_altar: {
                name: 'Ancient Altar',
                type: 'enchantment',
                effect: { buff: 'ancient_blessing', duration: 600000 },
                cooldown: 1800000, // 30 minutes
                usable: true,
                position: { x: 0, y: 0 }
            },
            treasure_chest: {
                name: 'Treasure Chest',
                type: 'loot',
                loot: ['gold', 'rare_gem', 'magic_scroll'],
                cooldown: 3600000, // 1 hour
                usable: true,
                respawns: true
            },
            crystal_node: {
                name: 'Crystal Mining Node',
                type: 'resource',
                resource: 'crystal_ore',
                yield: 3,
                cooldown: 600000, // 10 minutes
                usable: true,
                respawns: true
            }
        };

        // Place objects in zones
        for (const [zoneId, zone] of this.gameWorld) {
            for (const objectType of zone.objects) {
                if (objectTemplates[objectType]) {
                    this.createWorldObject(zoneId, objectType, objectTemplates[objectType]);
                }
            }
        }
    }

    setupWorldLoop() {
        // Update world state every 5 seconds
        setInterval(() => {
            this.updateWorldState();
        }, 5000);

        // Update NPCs every 10 seconds
        setInterval(() => {
            this.updateNPCs();
        }, 10000);

        // Update monsters every 3 seconds
        setInterval(() => {
            this.updateMonsters();
        }, 3000);

        // Clean up zones every minute
        setInterval(() => {
            this.cleanupZones();
        }, 60000);

        // Respawn dead entities every 30 seconds
        setInterval(() => {
            this.respawnEntities();
        }, 30000);
    }

    async addPlayerToWorld(playerId, playerData) {
        const zoneId = playerData.position?.zone || 'starter_town';
        const zone = this.gameWorld.get(zoneId);
        
        if (!zone) {
            throw new Error('Invalid zone');
        }

        // Check zone capacity
        if (zone.players.size >= this.WORLD_CONFIG.maxPlayersPerZone) {
            throw new Error('Zone is full');
        }

        // Add player to zone
        zone.players.add(playerId);
        this.playerZones.set(playerId, zoneId);

        // Add to zone instance
        const zoneInstance = this.zoneInstances.get(zoneId);
        zoneInstance.players.set(playerId, {
            ...playerData,
            joinTime: Date.now(),
            lastActivity: Date.now()
        });

        // Send zone data to player
        await this.sendZoneDataToPlayer(playerId, zoneId);

        // Notify other players in zone
        this.broadcastToZone(zoneId, 'world:playerJoined', {
            playerId,
            playerData: {
                username: playerData.username,
                level: playerData.level,
                position: playerData.position
            }
        }, playerId);

        this.logger.info(`Player ${playerId} added to zone ${zoneId}`);
    }

    async removePlayerFromWorld(playerId) {
        const zoneId = this.playerZones.get(playerId);
        if (!zoneId) return;

        const zone = this.gameWorld.get(zoneId);
        const zoneInstance = this.zoneInstances.get(zoneId);

        if (zone && zoneInstance) {
            // Remove from zone
            zone.players.delete(playerId);
            zoneInstance.players.delete(playerId);

            // Notify other players
            this.broadcastToZone(zoneId, 'world:playerLeft', { playerId }, playerId);
        }

        this.playerZones.delete(playerId);
        this.logger.info(`Player ${playerId} removed from world`);
    }

    async handleInteraction(playerId, data) {
        try {
            const { targetId, interactionType } = data;
            const zoneId = this.playerZones.get(playerId);
            
            if (!zoneId) {
                return this.sendError(playerId, 'Player not in any zone');
            }

            switch (interactionType) {
                case 'npc':
                    await this.handleNPCInteraction(playerId, targetId, zoneId);
                    break;
                case 'object':
                    await this.handleObjectInteraction(playerId, targetId, zoneId);
                    break;
                case 'monster':
                    await this.handleMonsterInteraction(playerId, targetId, zoneId);
                    break;
                default:
                    this.sendError(playerId, 'Unknown interaction type');
            }

        } catch (error) {
            this.logger.error('Error handling interaction:', error);
            this.sendError(playerId, 'Interaction failed');
        }
    }

    async handleTeleport(playerId, data) {
        try {
            const { targetZone, targetPosition } = data;
            
            // Validate teleport
            if (!this.canTeleport(playerId, targetZone)) {
                return this.sendError(playerId, 'Cannot teleport to that location');
            }

            // Remove from current zone
            await this.removePlayerFromWorld(playerId);

            // Add to new zone
            const playerData = { position: { ...targetPosition, zone: targetZone } };
            await this.addPlayerToWorld(playerId, playerData);

            this.logger.info(`Player ${playerId} teleported to ${targetZone}`);

        } catch (error) {
            this.logger.error('Error handling teleport:', error);
            this.sendError(playerId, 'Teleport failed');
        }
    }

    async handleNPCInteraction(playerId, npcId, zoneId) {
        const zoneInstance = this.zoneInstances.get(zoneId);
        const npc = zoneInstance?.npcs.get(npcId);

        if (!npc) {
            return this.sendError(playerId, 'NPC not found');
        }

        // Check distance
        if (!this.isInInteractionRange(playerId, npc.position, zoneId)) {
            return this.sendError(playerId, 'Too far from NPC');
        }

        // Handle different NPC types
        switch (npc.type) {
            case 'merchant':
                await this.handleMerchantInteraction(playerId, npc);
                break;
            case 'quest_giver':
                await this.handleQuestGiverInteraction(playerId, npc);
                break;
            case 'trainer':
                await this.handleTrainerInteraction(playerId, npc);
                break;
            default:
                await this.handleGenericNPCInteraction(playerId, npc);
        }
    }

    async handleObjectInteraction(playerId, objectId, zoneId) {
        const zoneInstance = this.zoneInstances.get(zoneId);
        const object = zoneInstance?.objects.get(objectId);

        if (!object) {
            return this.sendError(playerId, 'Object not found');
        }

        // Check cooldown
        if (object.lastUsed && (Date.now() - object.lastUsed) < object.cooldown) {
            const timeLeft = Math.ceil((object.cooldown - (Date.now() - object.lastUsed)) / 1000);
            return this.sendError(playerId, `Object on cooldown for ${timeLeft} seconds`);
        }

        // Check distance
        if (!this.isInInteractionRange(playerId, object.position, zoneId)) {
            return this.sendError(playerId, 'Too far from object');
        }

        // Apply object effect
        await this.applyObjectEffect(playerId, object);

        // Set cooldown
        object.lastUsed = Date.now();

        // Broadcast interaction
        this.broadcastToZone(zoneId, 'world:objectUsed', {
            playerId,
            objectId,
            objectName: object.name
        });
    }

    async handleMonsterInteraction(playerId, monsterId, zoneId) {
        // This would typically start combat
        this.emit('world:monsterInteraction', {
            playerId,
            monsterId,
            zoneId
        });
    }

    createNPC(zoneId, npcType, template) {
        const npcId = `${npcType}_${zoneId}_${Date.now()}`;
        const npc = {
            id: npcId,
            ...template,
            zoneId,
            spawnTime: Date.now(),
            lastInteraction: 0,
            isAlive: true
        };

        this.npcs.set(npcId, npc);
        
        const zoneInstance = this.zoneInstances.get(zoneId);
        if (zoneInstance) {
            zoneInstance.npcs.set(npcId, npc);
        }

        return npc;
    }

    spawnMonster(zoneId, monsterType, template) {
        const monsterId = `${monsterType}_${zoneId}_${Date.now()}_${Math.random().toString(36).substr(2, 5)}`;
        const zone = this.gameWorld.get(zoneId);
        
        // Generate random position within zone boundaries
        const position = this.generateRandomPosition(zone.boundaries);
        
        const monster = {
            id: monsterId,
            ...template,
            zoneId,
            position,
            spawnTime: Date.now(),
            lastAction: Date.now(),
            isAlive: true,
            currentHealth: template.health,
            target: null,
            state: 'idle' // idle, roaming, combat, fleeing
        };

        this.monsters.set(monsterId, monster);
        
        const zoneInstance = this.zoneInstances.get(zoneId);
        if (zoneInstance) {
            zoneInstance.monsters.set(monsterId, monster);
        }

        return monster;
    }

    createWorldObject(zoneId, objectType, template) {
        const objectId = `${objectType}_${zoneId}_${Date.now()}`;
        const object = {
            id: objectId,
            ...template,
            zoneId,
            spawnTime: Date.now(),
            lastUsed: 0,
            isActive: true
        };

        // Generate position if not specified
        if (!object.position) {
            const zone = this.gameWorld.get(zoneId);
            object.position = this.generateRandomPosition(zone.boundaries);
        }

        this.worldObjects.set(objectId, object);
        
        const zoneInstance = this.zoneInstances.get(zoneId);
        if (zoneInstance) {
            zoneInstance.objects.set(objectId, object);
        }

        return object;
    }

    updateWorldState() {
        for (const [zoneId, zone] of this.gameWorld) {
            // Update zone time and weather
            this.updateZoneTime(zone);
            this.updateZoneWeather(zone);
            
            // Update zone events
            this.updateZoneEvents(zoneId);
            
            zone.lastUpdate = Date.now();
        }
    }

    updateNPCs() {
        for (const [npcId, npc] of this.npcs) {
            if (!npc.isAlive) continue;

            // NPCs can have various behaviors here
            // For now, just update their state
            npc.lastUpdate = Date.now();
        }
    }

    updateMonsters() {
        for (const [monsterId, monster] of this.monsters) {
            if (!monster.isAlive) continue;

            // Update monster AI
            this.updateMonsterAI(monster);
            
            // Update monster position if roaming
            if (monster.state === 'roaming') {
                this.updateMonsterPosition(monster);
            }
            
            monster.lastAction = Date.now();
        }
    }

    updateMonsterAI(monster) {
        const zoneInstance = this.zoneInstances.get(monster.zoneId);
        if (!zoneInstance) return;

        // Check for nearby players if aggressive
        if (monster.aggressive && monster.state === 'idle') {
            const nearbyPlayers = this.getNearbyPlayers(monster.position, monster.zoneId, 50);
            if (nearbyPlayers.length > 0) {
                // Start combat with nearest player
                monster.target = nearbyPlayers[0].id;
                monster.state = 'combat';
                
                // Emit combat start event
                this.emit('world:monsterAggroed', {
                    monsterId: monster.id,
                    targetId: monster.target,
                    zoneId: monster.zoneId
                });
            }
        }

        // Random chance to start roaming
        if (monster.state === 'idle' && Math.random() < 0.1) {
            monster.state = 'roaming';
        }

        // Return to idle after roaming
        if (monster.state === 'roaming' && Math.random() < 0.05) {
            monster.state = 'idle';
        }
    }

    updateMonsterPosition(monster) {
        // Simple roaming behavior
        const moveDistance = 5;
        const angle = Math.random() * 2 * Math.PI;
        
        const newX = monster.position.x + Math.cos(angle) * moveDistance;
        const newY = monster.position.y + Math.sin(angle) * moveDistance;
        
        // Check boundaries
        const zone = this.gameWorld.get(monster.zoneId);
        if (newX >= zone.boundaries.x[0] && newX <= zone.boundaries.x[1] &&
            newY >= zone.boundaries.y[0] && newY <= zone.boundaries.y[1]) {
            
            monster.position.x = newX;
            monster.position.y = newY;
            
            // Broadcast position update
            this.broadcastToZone(monster.zoneId, 'world:monsterMoved', {
                monsterId: monster.id,
                position: monster.position
            });
        }
    }

    cleanupZones() {
        for (const [zoneId, zoneInstance] of this.zoneInstances) {
            // Remove inactive players
            for (const [playerId, playerData] of zoneInstance.players) {
                if (Date.now() - playerData.lastActivity > 30 * 60 * 1000) { // 30 minutes
                    this.removePlayerFromWorld(playerId);
                }
            }
        }
    }

    respawnEntities() {
        // Respawn dead NPCs
        for (const [npcId, npc] of this.npcs) {
            if (!npc.isAlive && npc.respawn && 
                (Date.now() - npc.deathTime) > this.WORLD_CONFIG.npcRespawnTime) {
                this.respawnNPC(npc);
            }
        }

        // Respawn dead monsters
        for (const [monsterId, monster] of this.monsters) {
            if (!monster.isAlive && 
                (Date.now() - monster.deathTime) > monster.respawnTime) {
                this.respawnMonster(monster);
            }
        }
    }

    // Utility methods
    generateRandomPosition(boundaries) {
        const x = boundaries.x[0] + Math.random() * (boundaries.x[1] - boundaries.x[0]);
        const y = boundaries.y[0] + Math.random() * (boundaries.y[1] - boundaries.y[0]);
        return { x, y };
    }

    getMonsterSpawnCount(zoneType, monsterType) {
        const spawnCounts = {
            town: 0,
            wilderness: 3,
            dungeon: 5,
            raid: 2,
            special: 1
        };
        return spawnCounts[zoneType] || 1;
    }

    canTeleport(playerId, targetZone) {
        // Add teleport restrictions here
        const zone = this.gameWorld.get(targetZone);
        return zone && zone.type !== 'raid'; // Can't teleport to raid zones
    }

    isInInteractionRange(playerId, targetPosition, zoneId) {
        const zoneInstance = this.zoneInstances.get(zoneId);
        const playerData = zoneInstance?.players.get(playerId);
        
        if (!playerData || !playerData.position) return false;
        
        const distance = Math.sqrt(
            Math.pow(playerData.position.x - targetPosition.x, 2) +
            Math.pow(playerData.position.y - targetPosition.y, 2)
        );
        
        return distance <= 10; // 10 unit interaction range
    }

    getNearbyPlayers(position, zoneId, range) {
        const zoneInstance = this.zoneInstances.get(zoneId);
        if (!zoneInstance) return [];

        const nearbyPlayers = [];
        for (const [playerId, playerData] of zoneInstance.players) {
            if (!playerData.position) continue;
            
            const distance = Math.sqrt(
                Math.pow(playerData.position.x - position.x, 2) +
                Math.pow(playerData.position.y - position.y, 2)
            );
            
            if (distance <= range) {
                nearbyPlayers.push({ id: playerId, ...playerData, distance });
            }
        }
        
        return nearbyPlayers.sort((a, b) => a.distance - b.distance);
    }

    async sendZoneDataToPlayer(playerId, zoneId) {
        const zone = this.gameWorld.get(zoneId);
        const zoneInstance = this.zoneInstances.get(zoneId);
        
        if (!zone || !zoneInstance) return;

        const zoneData = {
            zone: {
                id: zoneId,
                name: zone.name,
                description: zone.description,
                type: zone.type,
                level: zone.level,
                pvp: zone.pvp,
                weather: zone.weather,
                timeOfDay: zone.timeOfDay
            },
            npcs: Array.from(zoneInstance.npcs.values()).map(npc => ({
                id: npc.id,
                name: npc.name,
                type: npc.type,
                level: npc.level,
                position: npc.position,
                isAlive: npc.isAlive
            })),
            monsters: Array.from(zoneInstance.monsters.values()).map(monster => ({
                id: monster.id,
                name: monster.name,
                type: monster.type,
                level: monster.level,
                position: monster.position,
                isAlive: monster.isAlive,
                currentHealth: monster.currentHealth,
                maxHealth: monster.health
            })),
            objects: Array.from(zoneInstance.objects.values()).map(object => ({
                id: object.id,
                name: object.name,
                type: object.type,
                position: object.position,
                isActive: object.isActive
            })),
            players: Array.from(zoneInstance.players.values()).map(player => ({
                id: player.id,
                username: player.username,
                level: player.level,
                position: player.position
            })).filter(player => player.id !== playerId)
        };

        this.io.to(playerId).emit('world:zoneData', zoneData);
    }

    broadcastToZone(zoneId, event, data, excludePlayerId = null) {
        const zone = this.gameWorld.get(zoneId);
        if (!zone) return;

        for (const playerId of zone.players) {
            if (playerId !== excludePlayerId) {
                this.io.to(playerId).emit(event, data);
            }
        }
    }

    sendError(playerId, message) {
        this.io.to(playerId).emit('error', { message });
    }

    // Handler methods for different interaction types
    async handleMerchantInteraction(playerId, npc) {
        this.io.to(playerId).emit('npc:merchantInteraction', {
            npcId: npc.id,
            npcName: npc.name,
            dialogue: npc.dialogue,
            shop: npc.shop
        });
    }

    async handleQuestGiverInteraction(playerId, npc) {
        this.io.to(playerId).emit('npc:questGiverInteraction', {
            npcId: npc.id,
            npcName: npc.name,
            dialogue: npc.dialogue,
            availableQuests: npc.quests
        });
    }

    async handleTrainerInteraction(playerId, npc) {
        this.io.to(playerId).emit('npc:trainerInteraction', {
            npcId: npc.id,
            npcName: npc.name,
            dialogue: npc.dialogue,
            availableSkills: npc.skills
        });
    }

    async handleGenericNPCInteraction(playerId, npc) {
        const dialogue = npc.dialogue[Math.floor(Math.random() * npc.dialogue.length)];
        this.io.to(playerId).emit('npc:dialogue', {
            npcId: npc.id,
            npcName: npc.name,
            message: dialogue
        });
    }

    async applyObjectEffect(playerId, object) {
        switch (object.type) {
            case 'healing':
                this.io.to(playerId).emit('player:heal', object.effect);
                break;
            case 'enchantment':
                this.io.to(playerId).emit('player:buff', object.effect);
                break;
            case 'loot':
                this.io.to(playerId).emit('player:loot', { items: object.loot });
                break;
            case 'resource':
                this.io.to(playerId).emit('player:resource', {
                    resource: object.resource,
                    amount: object.yield
                });
                break;
        }
    }

    updateZoneTime(zone) {
        // Simple day/night cycle
        const cycleTime = Date.now() % this.WORLD_CONFIG.dayNightCycleLength;
        const cycleProgress = cycleTime / this.WORLD_CONFIG.dayNightCycleLength;
        
        if (cycleProgress < 0.25) {
            zone.timeOfDay = 'dawn';
        } else if (cycleProgress < 0.75) {
            zone.timeOfDay = 'day';
        } else if (cycleProgress < 0.9) {
            zone.timeOfDay = 'dusk';
        } else {
            zone.timeOfDay = 'night';
        }
    }

    updateZoneWeather(zone) {
        // Random weather changes
        if (Math.random() < 0.001) { // 0.1% chance per update
            const weathers = ['clear', 'cloudy', 'rainy', 'stormy', 'foggy'];
            const newWeather = weathers[Math.floor(Math.random() * weathers.length)];
            if (newWeather !== zone.weather) {
                zone.weather = newWeather;
                this.broadcastToZone(zone.id, 'world:weatherChanged', {
                    weather: newWeather
                });
            }
        }
    }

    updateZoneEvents(zoneId) {
        const zoneInstance = this.zoneInstances.get(zoneId);
        if (!zoneInstance) return;

        // Filter expired events
        zoneInstance.events = zoneInstance.events.filter(event => {
            return Date.now() < event.endTime;
        });
    }

    respawnNPC(npc) {
        npc.isAlive = true;
        npc.health = npc.maxHealth || 100;
        delete npc.deathTime;
        
        this.broadcastToZone(npc.zoneId, 'world:npcRespawned', {
            npcId: npc.id,
            npcName: npc.name
        });
    }

    respawnMonster(monster) {
        monster.isAlive = true;
        monster.currentHealth = monster.health;
        monster.state = 'idle';
        monster.target = null;
        delete monster.deathTime;
        
        // Respawn at original or random position
        const zone = this.gameWorld.get(monster.zoneId);
        monster.position = this.generateRandomPosition(zone.boundaries);
        
        this.broadcastToZone(monster.zoneId, 'world:monsterRespawned', {
            monsterId: monster.id,
            monsterName: monster.name,
            position: monster.position
        });
    }

    // Public API methods
    getZone(zoneId) {
        return this.gameWorld.get(zoneId);
    }

    getPlayerZone(playerId) {
        return this.playerZones.get(playerId);
    }

    getZonePlayersCount(zoneId) {
        const zone = this.gameWorld.get(zoneId);
        return zone ? zone.players.size : 0;
    }

    getAllZones() {
        return Array.from(this.gameWorld.values());
    }

    update() {
        // Called from game engine every tick
        // High-frequency updates are handled by internal loops
    }
}

module.exports = WorldManager;