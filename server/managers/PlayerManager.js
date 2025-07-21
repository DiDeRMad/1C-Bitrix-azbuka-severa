/**
 * Player Manager - Handles all player operations and state management
 */

const Player = require('../models/Player');
const Character = require('../models/Character');
const winston = require('winston');

class PlayerManager {
    constructor(io) {
        this.io = io;
        this.onlinePlayers = new Map(); // playerId -> player data
        this.playerSockets = new Map(); // playerId -> socket
        this.playerPositions = new Map(); // playerId -> position
        this.playerStates = new Map(); // playerId -> state (idle, moving, combat, crafting, etc.)
        
        this.logger = winston.createLogger({
            level: 'info',
            format: winston.format.combine(
                winston.format.timestamp(),
                winston.format.json()
            ),
            defaultMeta: { service: 'player-manager' },
            transports: [
                new winston.transports.File({ filename: 'logs/player-manager.log' }),
                new winston.transports.Console()
            ]
        });

        // Player constants
        this.PLAYER_CONFIG = {
            maxLevel: 1000,
            startingLevel: 1,
            startingHealth: 100,
            startingMana: 50,
            startingStamina: 100,
            startingGold: 1000,
            movementSpeed: 5.0, // units per second
            maxInventorySlots: 50,
            maxBankSlots: 200,
            respawnTime: 30000, // 30 seconds
            afkTimeout: 15 * 60 * 1000, // 15 minutes
            logoutSaveDelay: 10000 // 10 seconds
        };

        // Character classes and their stats
        this.CHARACTER_CLASSES = {
            warrior: {
                name: 'Warrior',
                description: 'Strong melee fighter with high defense',
                startingStats: {
                    strength: 20,
                    agility: 10,
                    intelligence: 5,
                    vitality: 18,
                    luck: 7
                },
                statGrowth: {
                    strength: 2.5,
                    agility: 1.2,
                    intelligence: 0.8,
                    vitality: 2.0,
                    luck: 1.0
                },
                skills: ['sword_mastery', 'shield_bash', 'taunt', 'berserker_rage']
            },
            mage: {
                name: 'Mage',
                description: 'Powerful spellcaster with high magical damage',
                startingStats: {
                    strength: 5,
                    agility: 8,
                    intelligence: 22,
                    vitality: 10,
                    luck: 15
                },
                statGrowth: {
                    strength: 0.8,
                    agility: 1.2,
                    intelligence: 2.8,
                    vitality: 1.2,
                    luck: 1.5
                },
                skills: ['fireball', 'ice_shard', 'heal', 'mana_shield']
            },
            archer: {
                name: 'Archer',
                description: 'Agile ranged fighter with high critical chance',
                startingStats: {
                    strength: 12,
                    agility: 20,
                    intelligence: 8,
                    vitality: 12,
                    luck: 18
                },
                statGrowth: {
                    strength: 1.5,
                    agility: 2.5,
                    intelligence: 1.0,
                    vitality: 1.5,
                    luck: 2.0
                },
                skills: ['arrow_shot', 'multishot', 'trap', 'stealth']
            },
            rogue: {
                name: 'Rogue',
                description: 'Sneaky assassin with high speed and critical damage',
                startingStats: {
                    strength: 15,
                    agility: 18,
                    intelligence: 12,
                    vitality: 10,
                    luck: 15
                },
                statGrowth: {
                    strength: 1.8,
                    agility: 2.2,
                    intelligence: 1.5,
                    vitality: 1.2,
                    luck: 1.8
                },
                skills: ['backstab', 'poison_blade', 'smoke_bomb', 'lockpicking']
            },
            paladin: {
                name: 'Paladin',
                description: 'Holy warrior with healing and protection abilities',
                startingStats: {
                    strength: 16,
                    agility: 8,
                    intelligence: 14,
                    vitality: 20,
                    luck: 12
                },
                statGrowth: {
                    strength: 2.0,
                    agility: 1.0,
                    intelligence: 1.8,
                    vitality: 2.2,
                    luck: 1.3
                },
                skills: ['holy_strike', 'heal', 'blessing', 'divine_protection']
            }
        };

        this.setupUpdateLoop();
    }

    setupUpdateLoop() {
        // Update player states every 100ms
        setInterval(() => {
            this.updatePlayerStates();
        }, 100);

        // Update player regeneration every second
        setInterval(() => {
            this.updatePlayerRegeneration();
        }, 1000);

        // Check for AFK players every minute
        setInterval(() => {
            this.checkAfkPlayers();
        }, 60000);
    }

    async playerConnected(playerId, socket) {
        try {
            // Load player data from database
            const player = await Player.findById(playerId).populate('characters');
            if (!player) {
                throw new Error('Player not found');
            }

            // Load active character
            let activeCharacter = null;
            if (player.activeCharacterId) {
                activeCharacter = await Character.findById(player.activeCharacterId);
            }

            const playerData = {
                id: playerId,
                username: player.username,
                email: player.email,
                level: player.level,
                experience: player.experience,
                gold: player.gold,
                premiumUntil: player.premiumUntil,
                lastLogin: new Date(),
                character: activeCharacter,
                stats: activeCharacter ? this.calculatePlayerStats(activeCharacter) : null,
                position: activeCharacter ? activeCharacter.position : { x: 0, y: 0, zone: 'starter_town' },
                health: activeCharacter ? activeCharacter.health : this.PLAYER_CONFIG.startingHealth,
                mana: activeCharacter ? activeCharacter.mana : this.PLAYER_CONFIG.startingMana,
                stamina: activeCharacter ? activeCharacter.stamina : this.PLAYER_CONFIG.startingStamina
            };

            // Add to online players
            this.onlinePlayers.set(playerId, playerData);
            this.playerSockets.set(playerId, socket);
            this.playerPositions.set(playerId, playerData.position);
            this.playerStates.set(playerId, 'idle');

            // Update last login
            await Player.findByIdAndUpdate(playerId, { lastLogin: new Date() });

            // Send initial game state to player
            socket.emit('player:initialized', {
                player: playerData,
                world: await this.getWorldStateForPlayer(playerId)
            });

            // Notify other players in the same area
            this.broadcastToArea(playerData.position.zone, 'player:joined', {
                playerId,
                username: playerData.username,
                character: playerData.character,
                position: playerData.position
            }, playerId);

            this.logger.info(`Player ${playerData.username} connected`, { playerId });

        } catch (error) {
            this.logger.error('Error connecting player:', error);
            throw error;
        }
    }

    async playerDisconnected(playerId) {
        try {
            const playerData = this.onlinePlayers.get(playerId);
            if (!playerData) return;

            // Save player state to database
            await this.savePlayerState(playerId);

            // Notify other players in the same area
            const position = this.playerPositions.get(playerId);
            if (position) {
                this.broadcastToArea(position.zone, 'player:left', {
                    playerId,
                    username: playerData.username
                }, playerId);
            }

            // Remove from online players
            this.onlinePlayers.delete(playerId);
            this.playerSockets.delete(playerId);
            this.playerPositions.delete(playerId);
            this.playerStates.delete(playerId);

            this.logger.info(`Player ${playerData.username} disconnected`, { playerId });

        } catch (error) {
            this.logger.error('Error disconnecting player:', error);
        }
    }

    async handleMove(playerId, data) {
        try {
            const { x, y, zone } = data;
            const playerData = this.onlinePlayers.get(playerId);
            if (!playerData) return;

            // Validate movement
            if (!this.isValidPosition(x, y, zone)) {
                return this.sendError(playerId, 'Invalid position');
            }

            // Check movement speed (anti-cheat)
            const currentPosition = this.playerPositions.get(playerId);
            if (currentPosition && !this.isValidMovementSpeed(currentPosition, { x, y, zone })) {
                return this.sendError(playerId, 'Movement too fast');
            }

            // Update position
            const newPosition = { x, y, zone };
            this.playerPositions.set(playerId, newPosition);
            playerData.position = newPosition;

            // Change state to moving
            this.playerStates.set(playerId, 'moving');

            // Broadcast movement to other players in the area
            this.broadcastToArea(zone, 'player:moved', {
                playerId,
                position: newPosition,
                timestamp: Date.now()
            }, playerId);

            // Check for area change
            if (currentPosition && currentPosition.zone !== zone) {
                await this.handleAreaChange(playerId, currentPosition.zone, zone);
            }

        } catch (error) {
            this.logger.error('Error handling player movement:', error);
            this.sendError(playerId, 'Movement failed');
        }
    }

    async handleAction(playerId, data) {
        try {
            const { action, target, parameters } = data;
            const playerData = this.onlinePlayers.get(playerId);
            if (!playerData) return;

            switch (action) {
                case 'attack':
                    await this.handlePlayerAttack(playerId, target, parameters);
                    break;
                case 'use_skill':
                    await this.handleSkillUse(playerId, target, parameters);
                    break;
                case 'use_item':
                    await this.handleItemUse(playerId, target, parameters);
                    break;
                case 'interact':
                    await this.handleInteraction(playerId, target, parameters);
                    break;
                case 'emote':
                    await this.handleEmote(playerId, parameters);
                    break;
                default:
                    this.sendError(playerId, 'Unknown action');
            }

        } catch (error) {
            this.logger.error('Error handling player action:', error);
            this.sendError(playerId, 'Action failed');
        }
    }

    async handlePlayerAttack(playerId, targetId, parameters) {
        const playerData = this.onlinePlayers.get(playerId);
        if (!playerData || !playerData.character) return;

        // Check if player can attack
        if (this.playerStates.get(playerId) === 'combat') {
            return this.sendError(playerId, 'Already in combat');
        }

        // Set combat state
        this.playerStates.set(playerId, 'combat');

        // Calculate damage and send to combat manager
        const damage = this.calculateAttackDamage(playerData.character, parameters);
        
        // Emit combat event
        this.io.emit('combat:playerAttack', {
            attackerId: playerId,
            targetId,
            damage,
            skillUsed: parameters.skill,
            timestamp: Date.now()
        });
    }

    async handleSkillUse(playerId, targetId, parameters) {
        const playerData = this.onlinePlayers.get(playerId);
        if (!playerData || !playerData.character) return;

        const { skillId, level } = parameters;
        
        // Check if player has the skill
        if (!this.hasSkill(playerData.character, skillId)) {
            return this.sendError(playerId, 'Skill not learned');
        }

        // Check mana cost
        const manaCost = this.getSkillManaCost(skillId, level);
        if (playerData.mana < manaCost) {
            return this.sendError(playerId, 'Not enough mana');
        }

        // Consume mana
        playerData.mana -= manaCost;

        // Execute skill effect
        await this.executeSkill(playerId, targetId, skillId, level);

        // Broadcast skill use
        const position = this.playerPositions.get(playerId);
        this.broadcastToArea(position.zone, 'player:skillUsed', {
            playerId,
            targetId,
            skillId,
            level,
            timestamp: Date.now()
        });
    }

    async handleItemUse(playerId, itemId, parameters) {
        const playerData = this.onlinePlayers.get(playerId);
        if (!playerData) return;

        // Check if player has the item
        // This would interact with InventoryManager
        // For now, just broadcast the action
        const position = this.playerPositions.get(playerId);
        this.broadcastToArea(position.zone, 'player:itemUsed', {
            playerId,
            itemId,
            timestamp: Date.now()
        });
    }

    async handleInteraction(playerId, targetId, parameters) {
        const playerData = this.onlinePlayers.get(playerId);
        if (!playerData) return;

        const { interactionType } = parameters;
        
        // Broadcast interaction
        const position = this.playerPositions.get(playerId);
        this.broadcastToArea(position.zone, 'player:interaction', {
            playerId,
            targetId,
            interactionType,
            timestamp: Date.now()
        });
    }

    async handleEmote(playerId, parameters) {
        const playerData = this.onlinePlayers.get(playerId);
        if (!playerData) return;

        const { emoteId } = parameters;
        
        // Broadcast emote
        const position = this.playerPositions.get(playerId);
        this.broadcastToArea(position.zone, 'player:emote', {
            playerId,
            username: playerData.username,
            emoteId,
            timestamp: Date.now()
        });
    }

    async handleAreaChange(playerId, fromZone, toZone) {
        // Notify players in old area
        this.broadcastToArea(fromZone, 'player:leftArea', {
            playerId,
            toZone
        }, playerId);

        // Notify players in new area
        const playerData = this.onlinePlayers.get(playerId);
        this.broadcastToArea(toZone, 'player:enteredArea', {
            playerId,
            username: playerData.username,
            character: playerData.character,
            position: playerData.position,
            fromZone
        }, playerId);

        // Send area data to player
        const socket = this.playerSockets.get(playerId);
        socket.emit('area:changed', {
            newZone: toZone,
            areaData: await this.getAreaData(toZone)
        });
    }

    async handleAddFriend(playerId, data) {
        const { targetPlayerId } = data;
        
        try {
            // Add friend logic here
            await Player.findByIdAndUpdate(playerId, {
                $addToSet: { friends: targetPlayerId }
            });

            const socket = this.playerSockets.get(playerId);
            socket.emit('social:friendAdded', { friendId: targetPlayerId });

        } catch (error) {
            this.sendError(playerId, 'Failed to add friend');
        }
    }

    async handleRemoveFriend(playerId, data) {
        const { targetPlayerId } = data;
        
        try {
            // Remove friend logic here
            await Player.findByIdAndUpdate(playerId, {
                $pull: { friends: targetPlayerId }
            });

            const socket = this.playerSockets.get(playerId);
            socket.emit('social:friendRemoved', { friendId: targetPlayerId });

        } catch (error) {
            this.sendError(playerId, 'Failed to remove friend');
        }
    }

    async handleBlockPlayer(playerId, data) {
        const { targetPlayerId } = data;
        
        try {
            // Block player logic here
            await Player.findByIdAndUpdate(playerId, {
                $addToSet: { blockedPlayers: targetPlayerId }
            });

            const socket = this.playerSockets.get(playerId);
            socket.emit('social:playerBlocked', { blockedId: targetPlayerId });

        } catch (error) {
            this.sendError(playerId, 'Failed to block player');
        }
    }

    async handleUnblockPlayer(playerId, data) {
        const { targetPlayerId } = data;
        
        try {
            // Unblock player logic here
            await Player.findByIdAndUpdate(playerId, {
                $pull: { blockedPlayers: targetPlayerId }
            });

            const socket = this.playerSockets.get(playerId);
            socket.emit('social:playerUnblocked', { unblockedId: targetPlayerId });

        } catch (error) {
            this.sendError(playerId, 'Failed to unblock player');
        }
    }

    // Update methods
    updatePlayerStates() {
        for (const [playerId, state] of this.playerStates) {
            // Auto-transition from moving to idle after a period
            if (state === 'moving') {
                // Check if player stopped moving (this is simplified)
                setTimeout(() => {
                    if (this.playerStates.get(playerId) === 'moving') {
                        this.playerStates.set(playerId, 'idle');
                    }
                }, 2000);
            }
        }
    }

    updatePlayerRegeneration() {
        for (const [playerId, playerData] of this.onlinePlayers) {
            if (!playerData.character) continue;

            const maxHealth = this.getMaxHealth(playerData.character);
            const maxMana = this.getMaxMana(playerData.character);
            const maxStamina = this.getMaxStamina(playerData.character);

            // Regenerate health, mana, and stamina
            if (playerData.health < maxHealth) {
                playerData.health = Math.min(maxHealth, playerData.health + this.getHealthRegen(playerData.character));
            }

            if (playerData.mana < maxMana) {
                playerData.mana = Math.min(maxMana, playerData.mana + this.getManaRegen(playerData.character));
            }

            if (playerData.stamina < maxStamina) {
                playerData.stamina = Math.min(maxStamina, playerData.stamina + this.getStaminaRegen(playerData.character));
            }

            // Send updated stats to player
            const socket = this.playerSockets.get(playerId);
            if (socket) {
                socket.emit('player:statsUpdated', {
                    health: playerData.health,
                    mana: playerData.mana,
                    stamina: playerData.stamina
                });
            }
        }
    }

    checkAfkPlayers() {
        const now = Date.now();
        for (const [playerId, playerData] of this.onlinePlayers) {
            const lastActivity = playerData.lastActivity || playerData.lastLogin;
            if (now - lastActivity > this.PLAYER_CONFIG.afkTimeout) {
                // Mark player as AFK
                this.playerStates.set(playerId, 'afk');
                
                // Notify other players
                const position = this.playerPositions.get(playerId);
                this.broadcastToArea(position.zone, 'player:afk', {
                    playerId,
                    username: playerData.username
                }, playerId);
            }
        }
    }

    // Utility methods
    calculatePlayerStats(character) {
        const classData = this.CHARACTER_CLASSES[character.class];
        const level = character.level;
        
        const stats = {};
        for (const [stat, baseValue] of Object.entries(classData.startingStats)) {
            const growth = classData.statGrowth[stat];
            stats[stat] = Math.floor(baseValue + (level - 1) * growth);
        }
        
        return stats;
    }

    calculateAttackDamage(character, parameters) {
        const stats = this.calculatePlayerStats(character);
        const baseDamage = stats.strength * 2;
        const critChance = stats.luck / 100;
        const isCrit = Math.random() < critChance;
        
        let damage = baseDamage;
        if (isCrit) {
            damage *= 2;
        }
        
        return {
            damage: Math.floor(damage),
            isCritical: isCrit
        };
    }

    isValidPosition(x, y, zone) {
        // Simple validation - in a real game this would check against map boundaries
        return x >= -1000 && x <= 1000 && y >= -1000 && y <= 1000;
    }

    isValidMovementSpeed(oldPos, newPos) {
        if (oldPos.zone !== newPos.zone) return true; // Zone change is always valid
        
        const distance = Math.sqrt(
            Math.pow(newPos.x - oldPos.x, 2) + 
            Math.pow(newPos.y - oldPos.y, 2)
        );
        
        const maxDistance = this.PLAYER_CONFIG.movementSpeed * 2; // Allow some leeway
        return distance <= maxDistance;
    }

    hasSkill(character, skillId) {
        return character.skills && character.skills.includes(skillId);
    }

    getSkillManaCost(skillId, level) {
        // Simple mana cost calculation
        return Math.floor(10 * level * 1.2);
    }

    async executeSkill(playerId, targetId, skillId, level) {
        // Skill execution logic would go here
        // This would typically interact with the combat system
    }

    getMaxHealth(character) {
        const stats = this.calculatePlayerStats(character);
        return this.PLAYER_CONFIG.startingHealth + (stats.vitality * 10);
    }

    getMaxMana(character) {
        const stats = this.calculatePlayerStats(character);
        return this.PLAYER_CONFIG.startingMana + (stats.intelligence * 5);
    }

    getMaxStamina(character) {
        const stats = this.calculatePlayerStats(character);
        return this.PLAYER_CONFIG.startingStamina + (stats.agility * 2);
    }

    getHealthRegen(character) {
        const stats = this.calculatePlayerStats(character);
        return Math.floor(stats.vitality / 10) + 1;
    }

    getManaRegen(character) {
        const stats = this.calculatePlayerStats(character);
        return Math.floor(stats.intelligence / 8) + 1;
    }

    getStaminaRegen(character) {
        const stats = this.calculatePlayerStats(character);
        return Math.floor(stats.agility / 5) + 2;
    }

    async getWorldStateForPlayer(playerId) {
        // Return initial world state data for the player
        return {
            areas: await this.getAvailableAreas(),
            npcs: await this.getNearbyNpcs(playerId),
            monsters: await this.getNearbyMonsters(playerId),
            players: await this.getNearbyPlayers(playerId)
        };
    }

    async getAreaData(zone) {
        // Return area-specific data
        return {
            zone,
            npcs: [],
            monsters: [],
            objects: [],
            spawns: []
        };
    }

    async getAvailableAreas() {
        return ['starter_town', 'forest_of_shadows', 'crystal_caves', 'dragon_valley'];
    }

    async getNearbyNpcs(playerId) {
        // Return NPCs near the player
        return [];
    }

    async getNearbyMonsters(playerId) {
        // Return monsters near the player
        return [];
    }

    async getNearbyPlayers(playerId) {
        const playerData = this.onlinePlayers.get(playerId);
        if (!playerData) return [];

        const nearbyPlayers = [];
        const playerPosition = this.playerPositions.get(playerId);

        for (const [otherPlayerId, otherPlayerData] of this.onlinePlayers) {
            if (otherPlayerId === playerId) continue;

            const otherPosition = this.playerPositions.get(otherPlayerId);
            if (otherPosition && otherPosition.zone === playerPosition.zone) {
                nearbyPlayers.push({
                    id: otherPlayerId,
                    username: otherPlayerData.username,
                    character: otherPlayerData.character,
                    position: otherPosition,
                    state: this.playerStates.get(otherPlayerId)
                });
            }
        }

        return nearbyPlayers;
    }

    broadcastToArea(zone, event, data, excludePlayerId = null) {
        for (const [playerId, position] of this.playerPositions) {
            if (position.zone === zone && playerId !== excludePlayerId) {
                const socket = this.playerSockets.get(playerId);
                if (socket) {
                    socket.emit(event, data);
                }
            }
        }
    }

    sendError(playerId, message) {
        const socket = this.playerSockets.get(playerId);
        if (socket) {
            socket.emit('error', { message });
        }
    }

    async savePlayerState(playerId) {
        try {
            const playerData = this.onlinePlayers.get(playerId);
            if (!playerData) return;

            // Save player position and stats
            if (playerData.character) {
                await Character.findByIdAndUpdate(playerData.character._id, {
                    position: playerData.position,
                    health: playerData.health,
                    mana: playerData.mana,
                    stamina: playerData.stamina,
                    lastSaved: new Date()
                });
            }

            // Update player last logout
            await Player.findByIdAndUpdate(playerId, {
                lastLogout: new Date()
            });

        } catch (error) {
            this.logger.error('Error saving player state:', error);
        }
    }

    async applyDeathPenalty(playerId, penaltyRate) {
        try {
            const playerData = this.onlinePlayers.get(playerId);
            if (!playerData) return;

            // Apply experience penalty
            const expLoss = Math.floor(playerData.experience * penaltyRate);
            playerData.experience = Math.max(0, playerData.experience - expLoss);

            // Set to low health
            playerData.health = Math.floor(this.getMaxHealth(playerData.character) * 0.1);

            // Save to database
            if (playerData.character) {
                await Character.findByIdAndUpdate(playerData.character._id, {
                    experience: playerData.experience,
                    health: playerData.health
                });
            }

            // Notify player
            const socket = this.playerSockets.get(playerId);
            if (socket) {
                socket.emit('player:deathPenalty', {
                    experienceLoss: expLoss,
                    newExperience: playerData.experience,
                    newHealth: playerData.health
                });
            }

        } catch (error) {
            this.logger.error('Error applying death penalty:', error);
        }
    }

    // Public API methods
    getOnlinePlayerCount() {
        return this.onlinePlayers.size;
    }

    getOnlinePlayers() {
        return Array.from(this.onlinePlayers.values());
    }

    getPlayerById(playerId) {
        return this.onlinePlayers.get(playerId);
    }

    getPlayerPosition(playerId) {
        return this.playerPositions.get(playerId);
    }

    getPlayerState(playerId) {
        return this.playerStates.get(playerId);
    }

    isPlayerOnline(playerId) {
        return this.onlinePlayers.has(playerId);
    }

    update() {
        // Called from game engine every tick
        // Perform any high-frequency updates here
    }
}

module.exports = PlayerManager;