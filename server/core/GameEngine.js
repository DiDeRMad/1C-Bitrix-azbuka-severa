/**
 * Game Engine - Core game loop and systems coordinator
 * Manages game state, updates, and inter-system communication
 */

const EventEmitter = require('events');
const winston = require('winston');

class GameEngine extends EventEmitter {
    constructor() {
        super();
        this.managers = {};
        this.gameState = {
            isRunning: false,
            startTime: null,
            currentTick: 0,
            tickRate: 60, // 60 FPS
            worldTime: 0,
            weather: 'clear',
            season: 'spring',
            events: [],
            globalStats: {
                playersOnline: 0,
                totalPlayers: 0,
                guildsActive: 0,
                itemsTraded: 0,
                questsCompleted: 0,
                monstersKilled: 0
            }
        };
        
        this.gameLoop = null;
        this.secondaryLoop = null;
        this.saveStateInterval = null;
        
        this.logger = winston.createLogger({
            level: 'info',
            format: winston.format.combine(
                winston.format.timestamp(),
                winston.format.json()
            ),
            defaultMeta: { service: 'game-engine' },
            transports: [
                new winston.transports.File({ filename: 'logs/game-engine.log' }),
                new winston.transports.Console()
            ]
        });

        // Game world constants
        this.WORLD_CONFIG = {
            dayLength: 24 * 60 * 1000, // 24 minutes = 1 day
            weatherChangeInterval: 5 * 60 * 1000, // Weather changes every 5 minutes
            seasonLength: 7 * 24 * 60 * 1000, // 7 days = 1 season
            maxPlayersPerArea: 50,
            maxGuildSize: 100,
            maxLevel: 1000,
            baseExperienceRequired: 1000
        };

        // Combat and progression constants
        this.GAME_BALANCE = {
            combatTickRate: 10, // Combat updates 10 times per second
            regenTickRate: 1, // Health/mana regen once per second
            questCompleteBonus: 1.2,
            groupExperienceBonus: 1.5,
            pvpExperienceBonus: 2.0,
            deathPenalty: 0.05, // 5% experience loss on death
            craftingSuccessBase: 0.8,
            tradeTaxRate: 0.02 // 2% tax on trades
        };

        this.initializeEventHandlers();
    }

    setManagers(managers) {
        this.managers = managers;
        this.logger.info('Game managers set:', Object.keys(managers));
    }

    setManagers(managers) {
        this.managers = managers;
        
        // Create convenient references
        this.playerManager = managers.playerManager;
        this.worldManager = managers.worldManager;
        this.combatManager = managers.combatManager;
        this.economyManager = managers.economyManager;
        this.guildManager = managers.guildManager;
        this.questManager = managers.questManager;
        this.craftingManager = managers.craftingManager;
        this.inventoryManager = managers.inventoryManager;
        this.chatManager = managers.chatManager;
        
        this.logger.info('Game managers set successfully');
    }

    async getPlayer(playerId) {
        if (this.playerManager) {
            return await this.playerManager.getPlayer(playerId);
        }
        return null;
    }

    initializeEventHandlers() {
        // Handle player events
        this.on('player:levelUp', this.handlePlayerLevelUp.bind(this));
        this.on('player:death', this.handlePlayerDeath.bind(this));
        this.on('player:login', this.handlePlayerLogin.bind(this));
        this.on('player:logout', this.handlePlayerLogout.bind(this));

        // Handle combat events
        this.on('combat:start', this.handleCombatStart.bind(this));
        this.on('combat:end', this.handleCombatEnd.bind(this));
        this.on('combat:kill', this.handleMonsterKill.bind(this));

        // Handle economy events
        this.on('economy:trade', this.handleTradeComplete.bind(this));
        this.on('economy:auction', this.handleAuctionComplete.bind(this));

        // Handle quest events
        this.on('quest:complete', this.handleQuestComplete.bind(this));
        this.on('quest:failed', this.handleQuestFailed.bind(this));

        // Handle guild events
        this.on('guild:created', this.handleGuildCreated.bind(this));
        this.on('guild:dissolved', this.handleGuildDissolved.bind(this));

        // Handle world events
        this.on('world:weatherChange', this.handleWeatherChange.bind(this));
        this.on('world:seasonChange', this.handleSeasonChange.bind(this));
        this.on('world:eventStart', this.handleWorldEventStart.bind(this));
        this.on('world:eventEnd', this.handleWorldEventEnd.bind(this));
    }

    async start() {
        try {
            this.logger.info('Starting Game Engine...');
            
            // Load game state from database
            await this.loadGameState();
            
            // Initialize world time and weather
            this.initializeWorldSystems();
            
            // Start game loops
            this.startGameLoop();
            this.startSecondaryLoop();
            this.startSaveStateLoop();
            
            this.gameState.isRunning = true;
            this.gameState.startTime = new Date();
            
            this.logger.info('Game Engine started successfully');
            this.emit('serverStatusChanged', { status: 'running', message: 'Game server is online!' });
            
        } catch (error) {
            this.logger.error('Failed to start Game Engine:', error);
            throw error;
        }
    }

    async stop() {
        try {
            this.logger.info('Stopping Game Engine...');
            
            this.gameState.isRunning = false;
            
            // Clear all intervals
            if (this.gameLoop) clearInterval(this.gameLoop);
            if (this.secondaryLoop) clearInterval(this.secondaryLoop);
            if (this.saveStateInterval) clearInterval(this.saveStateInterval);
            
            // Save final game state
            await this.saveGameState();
            
            this.logger.info('Game Engine stopped successfully');
            this.emit('serverStatusChanged', { status: 'stopping', message: 'Game server is shutting down...' });
            
        } catch (error) {
            this.logger.error('Error stopping Game Engine:', error);
            throw error;
        }
    }

    startGameLoop() {
        const tickInterval = 1000 / this.gameState.tickRate; // 16.67ms for 60 FPS
        
        this.gameLoop = setInterval(() => {
            this.gameTick();
        }, tickInterval);
        
        this.logger.info(`Game loop started at ${this.gameState.tickRate} FPS`);
    }

    startSecondaryLoop() {
        // Secondary loop for less frequent updates (once per second)
        this.secondaryLoop = setInterval(() => {
            this.secondaryTick();
        }, 1000);
        
        this.logger.info('Secondary game loop started (1 Hz)');
    }

    startSaveStateLoop() {
        // Save game state every 5 minutes
        this.saveStateInterval = setInterval(async () => {
            await this.saveGameState();
        }, 5 * 60 * 1000);
        
        this.logger.info('Save state loop started (every 5 minutes)');
    }

    gameTick() {
        if (!this.gameState.isRunning) return;
        
        try {
            this.gameState.currentTick++;
            
            // Update world time
            this.updateWorldTime();
            
            // Update game managers that need high-frequency updates
            if (this.managers.combat) {
                this.managers.combat.update();
            }
            
            if (this.managers.player) {
                this.managers.player.update();
            }
            
            if (this.managers.world) {
                this.managers.world.update();
            }
            
            // Emit game tick event for other systems
            this.emit('game:tick', this.gameState.currentTick);
            
        } catch (error) {
            this.logger.error('Error in game tick:', error);
        }
    }

    secondaryTick() {
        if (!this.gameState.isRunning) return;
        
        try {
            // Update world systems
            this.updateWeatherSystem();
            this.updateSeasonSystem();
            this.updateWorldEvents();
            
            // Update game managers that need lower-frequency updates
            if (this.managers.economy) {
                this.managers.economy.update();
            }
            
            if (this.managers.quest) {
                this.managers.quest.update();
            }
            
            if (this.managers.guild) {
                this.managers.guild.update();
            }
            
            if (this.managers.crafting) {
                this.managers.crafting.update();
            }
            
            if (this.managers.event) {
                this.managers.event.update();
            }
            
            // Update global statistics
            this.updateGlobalStats();
            
            // Emit secondary tick event
            this.emit('game:secondaryTick');
            
            // Broadcast world state to all players
            this.broadcastWorldState();
            
        } catch (error) {
            this.logger.error('Error in secondary tick:', error);
        }
    }

    initializeWorldSystems() {
        // Initialize world time (starts at dawn)
        this.gameState.worldTime = 6 * 60 * 60 * 1000; // 6 AM
        
        // Initialize weather system
        this.gameState.weather = this.getRandomWeather();
        
        // Initialize season (start with spring)
        this.gameState.season = 'spring';
        
        this.logger.info('World systems initialized', {
            worldTime: this.gameState.worldTime,
            weather: this.gameState.weather,
            season: this.gameState.season
        });
    }

    updateWorldTime() {
        const timeIncrement = (1000 / this.gameState.tickRate) * 60; // Real seconds to game minutes
        this.gameState.worldTime += timeIncrement;
        
        // Reset day if 24 hours passed
        if (this.gameState.worldTime >= this.WORLD_CONFIG.dayLength) {
            this.gameState.worldTime = 0;
            this.emit('world:newDay');
        }
    }

    updateWeatherSystem() {
        // Random weather changes
        if (Math.random() < 0.01) { // 1% chance per second to change weather
            const newWeather = this.getRandomWeather();
            if (newWeather !== this.gameState.weather) {
                this.gameState.weather = newWeather;
                this.emit('world:weatherChange', newWeather);
            }
        }
    }

    updateSeasonSystem() {
        // Season changes based on world time
        const totalTime = Date.now() - this.gameState.startTime;
        const seasonIndex = Math.floor(totalTime / this.WORLD_CONFIG.seasonLength) % 4;
        const seasons = ['spring', 'summer', 'autumn', 'winter'];
        const newSeason = seasons[seasonIndex];
        
        if (newSeason !== this.gameState.season) {
            this.gameState.season = newSeason;
            this.emit('world:seasonChange', newSeason);
        }
    }

    updateWorldEvents() {
        // Manage world events (raids, invasions, festivals, etc.)
        this.gameState.events = this.gameState.events.filter(event => {
            if (Date.now() > event.endTime) {
                this.emit('world:eventEnd', event);
                return false;
            }
            return true;
        });
        
        // Random chance to start new world events
        if (Math.random() < 0.001 && this.gameState.events.length < 3) { // 0.1% chance per second
            const newEvent = this.generateRandomWorldEvent();
            this.gameState.events.push(newEvent);
            this.emit('world:eventStart', newEvent);
        }
    }

    updateGlobalStats() {
        if (this.managers.player) {
            this.gameState.globalStats.playersOnline = this.managers.player.getOnlinePlayerCount();
        }
        
        // Other stats are updated through event handlers
    }

    broadcastWorldState() {
        const worldState = {
            time: this.gameState.worldTime,
            weather: this.gameState.weather,
            season: this.gameState.season,
            events: this.gameState.events.map(event => ({
                id: event.id,
                type: event.type,
                name: event.name,
                description: event.description,
                timeLeft: event.endTime - Date.now(),
                location: event.location
            })),
            stats: this.gameState.globalStats
        };
        
        this.emit('worldStateChanged', worldState);
    }

    getRandomWeather() {
        const weathers = ['clear', 'cloudy', 'rainy', 'stormy', 'foggy', 'snowy'];
        const seasonWeights = {
            spring: { clear: 0.4, cloudy: 0.3, rainy: 0.2, stormy: 0.05, foggy: 0.05, snowy: 0 },
            summer: { clear: 0.6, cloudy: 0.2, rainy: 0.1, stormy: 0.08, foggy: 0.02, snowy: 0 },
            autumn: { clear: 0.3, cloudy: 0.4, rainy: 0.15, stormy: 0.05, foggy: 0.1, snowy: 0 },
            winter: { clear: 0.2, cloudy: 0.3, rainy: 0.1, stormy: 0.05, foggy: 0.05, snowy: 0.3 }
        };
        
        const weights = seasonWeights[this.gameState.season];
        const random = Math.random();
        let cumulative = 0;
        
        for (const weather of weathers) {
            cumulative += weights[weather];
            if (random <= cumulative) {
                return weather;
            }
        }
        
        return 'clear';
    }

    generateRandomWorldEvent() {
        const events = [
            {
                type: 'monster_invasion',
                name: 'Monster Invasion',
                description: 'Dangerous monsters have invaded the peaceful lands!',
                duration: 30 * 60 * 1000, // 30 minutes
                rewards: { experience: 2.0, gold: 1.5 }
            },
            {
                type: 'merchant_festival',
                name: 'Merchant Festival',
                description: 'Special merchants have arrived with rare goods!',
                duration: 60 * 60 * 1000, // 1 hour
                rewards: { tradingDiscount: 0.2 }
            },
            {
                type: 'double_experience',
                name: 'Experience Boost',
                description: 'The gods smile upon adventurers today!',
                duration: 45 * 60 * 1000, // 45 minutes
                rewards: { experience: 2.0 }
            },
            {
                type: 'treasure_hunt',
                name: 'Treasure Hunt',
                description: 'Ancient treasures have been discovered!',
                duration: 20 * 60 * 1000, // 20 minutes
                rewards: { lootChance: 2.0 }
            },
            {
                type: 'guild_wars',
                name: 'Guild Wars',
                description: 'Guilds compete for dominance!',
                duration: 2 * 60 * 60 * 1000, // 2 hours
                rewards: { guildExperience: 3.0, glory: 1000 }
            }
        ];
        
        const eventTemplate = events[Math.floor(Math.random() * events.length)];
        const locations = ['Forest of Shadows', 'Crystal Caves', 'Dragon Valley', 'Mystic Lake', 'Ancient Ruins'];
        
        return {
            id: `event_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`,
            ...eventTemplate,
            startTime: Date.now(),
            endTime: Date.now() + eventTemplate.duration,
            location: locations[Math.floor(Math.random() * locations.length)]
        };
    }

    // Event Handlers
    async handlePlayerLevelUp(data) {
        const { playerId, newLevel, oldLevel } = data;
        this.logger.info(`Player ${playerId} leveled up from ${oldLevel} to ${newLevel}`);
        
        // Broadcast to other players
        this.emit('playerLevelUp', {
            playerId,
            newLevel,
            timestamp: Date.now()
        });
    }

    async handlePlayerDeath(data) {
        const { playerId, killedBy, location } = data;
        this.logger.info(`Player ${playerId} died at ${location}`, { killedBy });
        
        // Apply death penalty
        if (this.managers.player) {
            await this.managers.player.applyDeathPenalty(playerId, this.GAME_BALANCE.deathPenalty);
        }
    }

    async handlePlayerLogin(data) {
        const { playerId } = data;
        this.gameState.globalStats.playersOnline++;
        
        this.logger.info(`Player ${playerId} logged in`);
    }

    async handlePlayerLogout(data) {
        const { playerId } = data;
        this.gameState.globalStats.playersOnline = Math.max(0, this.gameState.globalStats.playersOnline - 1);
        
        this.logger.info(`Player ${playerId} logged out`);
    }

    async handleCombatStart(data) {
        const { combatId, participants } = data;
        this.logger.info(`Combat started: ${combatId}`, { participants });
    }

    async handleCombatEnd(data) {
        const { combatId, winner, loser, experience, loot } = data;
        this.logger.info(`Combat ended: ${combatId}`, { winner, loser, experience, loot });
    }

    async handleMonsterKill(data) {
        const { playerId, monsterId, experience, loot } = data;
        this.gameState.globalStats.monstersKilled++;
        
        this.logger.info(`Monster killed by ${playerId}`, { monsterId, experience, loot });
    }

    async handleTradeComplete(data) {
        const { tradeId, buyer, seller, item, price } = data;
        this.gameState.globalStats.itemsTraded++;
        
        this.logger.info(`Trade completed: ${tradeId}`, { buyer, seller, item, price });
    }

    async handleAuctionComplete(data) {
        const { auctionId, winner, item, finalPrice } = data;
        this.gameState.globalStats.itemsTraded++;
        
        this.logger.info(`Auction completed: ${auctionId}`, { winner, item, finalPrice });
    }

    async handleQuestComplete(data) {
        const { playerId, questId, rewards } = data;
        this.gameState.globalStats.questsCompleted++;
        
        this.logger.info(`Quest completed by ${playerId}`, { questId, rewards });
    }

    async handleQuestFailed(data) {
        const { playerId, questId, reason } = data;
        this.logger.info(`Quest failed by ${playerId}`, { questId, reason });
    }

    async handleGuildCreated(data) {
        const { guildId, guildName, founderId } = data;
        this.gameState.globalStats.guildsActive++;
        
        this.logger.info(`Guild created: ${guildName}`, { guildId, founderId });
    }

    async handleGuildDissolved(data) {
        const { guildId, guildName } = data;
        this.gameState.globalStats.guildsActive = Math.max(0, this.gameState.globalStats.guildsActive - 1);
        
        this.logger.info(`Guild dissolved: ${guildName}`, { guildId });
    }

    async handleWeatherChange(newWeather) {
        this.logger.info(`Weather changed to: ${newWeather}`);
        
        // Apply weather effects
        const weatherEffects = this.getWeatherEffects(newWeather);
        this.emit('weatherChanged', weatherEffects);
    }

    async handleSeasonChange(newSeason) {
        this.logger.info(`Season changed to: ${newSeason}`);
        
        // Apply seasonal effects
        const seasonalEffects = this.getSeasonalEffects(newSeason);
        this.emit('seasonChanged', seasonalEffects);
    }

    async handleWorldEventStart(event) {
        this.logger.info(`World event started: ${event.name}`, event);
        
        this.emit('worldEventStarted', {
            event: {
                id: event.id,
                type: event.type,
                name: event.name,
                description: event.description,
                duration: event.endTime - event.startTime,
                location: event.location,
                rewards: event.rewards
            }
        });
    }

    async handleWorldEventEnd(event) {
        this.logger.info(`World event ended: ${event.name}`, event);
        
        this.emit('worldEventEnded', {
            eventId: event.id,
            eventName: event.name
        });
    }

    getWeatherEffects(weather) {
        const effects = {
            clear: { visibility: 1.0, movement: 1.0, combat: 1.0 },
            cloudy: { visibility: 0.9, movement: 1.0, combat: 1.0 },
            rainy: { visibility: 0.7, movement: 0.9, combat: 0.95 },
            stormy: { visibility: 0.5, movement: 0.8, combat: 0.9 },
            foggy: { visibility: 0.3, movement: 0.95, combat: 0.85 },
            snowy: { visibility: 0.6, movement: 0.7, combat: 0.9 }
        };
        
        return effects[weather] || effects.clear;
    }

    getSeasonalEffects(season) {
        const effects = {
            spring: { growth: 1.2, harvest: 1.0, monster: 1.0, magic: 1.1 },
            summer: { growth: 1.0, harvest: 1.3, monster: 1.1, magic: 1.0 },
            autumn: { growth: 0.8, harvest: 1.5, monster: 0.9, magic: 1.2 },
            winter: { growth: 0.5, harvest: 0.7, monster: 1.2, magic: 1.3 }
        };
        
        return effects[season] || effects.spring;
    }

    async loadGameState() {
        try {
            // Load from database or create default state
            // This would typically load from MongoDB
            this.logger.info('Game state loaded successfully');
        } catch (error) {
            this.logger.error('Error loading game state:', error);
            // Use default state
        }
    }

    async saveGameState() {
        try {
            // Save to database
            // This would typically save to MongoDB
            this.logger.info('Game state saved successfully');
        } catch (error) {
            this.logger.error('Error saving game state:', error);
        }
    }

    // Public API methods
    getGameState() {
        return {
            ...this.gameState,
            uptime: this.gameState.startTime ? Date.now() - this.gameState.startTime : 0
        };
    }

    isRunning() {
        return this.gameState.isRunning;
    }

    getCurrentTick() {
        return this.gameState.currentTick;
    }

    getWorldTime() {
        return this.gameState.worldTime;
    }

    getWeather() {
        return this.gameState.weather;
    }

    getSeason() {
        return this.gameState.season;
    }

    getActiveEvents() {
        return this.gameState.events;
    }

    getGlobalStats() {
        return this.gameState.globalStats;
    }
}

module.exports = GameEngine;