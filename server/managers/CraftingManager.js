const EventEmitter = require('events');
const { v4: uuidv4 } = require('uuid');
const _ = require('lodash');

const CRAFTING_CONFIG = {
    PROFESSIONS: {
        BLACKSMITHING: 'blacksmithing',
        TAILORING: 'tailoring',
        LEATHERWORKING: 'leatherworking',
        ALCHEMY: 'alchemy',
        ENCHANTING: 'enchanting',
        COOKING: 'cooking',
        JEWELCRAFTING: 'jewelcrafting',
        ENGINEERING: 'engineering',
        INSCRIPTION: 'inscription',
        WOODWORKING: 'woodworking'
    },
    STATIONS: {
        FORGE: 'forge',
        LOOM: 'loom',
        TANNING_RACK: 'tanning_rack',
        ALCHEMY_TABLE: 'alchemy_table',
        ENCHANTING_TABLE: 'enchanting_table',
        COOKING_FIRE: 'cooking_fire',
        JEWELING_BENCH: 'jeweling_bench',
        ENGINEERING_BENCH: 'engineering_bench',
        SCRIBE_DESK: 'scribe_desk',
        WORKBENCH: 'workbench'
    },
    QUALITY_LEVELS: {
        POOR: 1,
        COMMON: 2,
        UNCOMMON: 3,
        RARE: 4,
        EPIC: 5,
        LEGENDARY: 6,
        ARTIFACT: 7
    },
    MAX_SKILL_LEVEL: 1000,
    SKILL_POINTS_PER_LEVEL: 100,
    CRITICAL_CRAFT_CHANCE: 0.05, // 5% base chance
    DISCOVERY_CHANCE: 0.01, // 1% chance to discover new recipe
    QUALITY_BONUS_MULTIPLIER: {
        1: 1.0,   // Poor
        2: 1.1,   // Common
        3: 1.25,  // Uncommon
        4: 1.5,   // Rare
        5: 2.0,   // Epic
        6: 3.0,   // Legendary
        7: 5.0    // Artifact
    }
};

class CraftingManager extends EventEmitter {
    constructor(gameEngine) {
        super();
        this.gameEngine = gameEngine;
        this.recipes = new Map();
        this.playerSkills = new Map(); // playerId -> { profession: { level, experience, recipes } }
        this.craftingStations = new Map(); // stationId -> stationData
        this.activeCrafting = new Map(); // playerId -> craftingSession
        this.discoveries = new Map(); // playerId -> [discoveredRecipes]
        this.craftingQueues = new Map(); // playerId -> [queuedCrafts]
        
        this.initializeRecipes();
        this.initializeCraftingStations();
        this.setupEventListeners();
        this.startCraftingUpdates();
    }

    initializeRecipes() {
        // Blacksmithing Recipes
        this.recipes.set('iron_sword', {
            id: 'iron_sword',
            name: 'Iron Sword',
            description: 'A sturdy sword made from iron.',
            profession: CRAFTING_CONFIG.PROFESSIONS.BLACKSMITHING,
            skillRequired: 50,
            station: CRAFTING_CONFIG.STATIONS.FORGE,
            craftTime: 30000, // 30 seconds
            materials: [
                { itemId: 'iron_ingot', quantity: 3 },
                { itemId: 'leather_grip', quantity: 1 },
                { itemId: 'wood_handle', quantity: 1 }
            ],
            result: {
                itemId: 'iron_sword',
                quantity: 1,
                qualityRange: [2, 4] // Common to Rare
            },
            skillGain: 5,
            category: 'weapons',
            unlocked: false,
            reagents: [],
            tools: ['smithing_hammer'],
            discoverable: false
        });

        this.recipes.set('steel_armor', {
            id: 'steel_armor',
            name: 'Steel Armor',
            description: 'Protective steel armor for warriors.',
            profession: CRAFTING_CONFIG.PROFESSIONS.BLACKSMITHING,
            skillRequired: 150,
            station: CRAFTING_CONFIG.STATIONS.FORGE,
            craftTime: 120000, // 2 minutes
            materials: [
                { itemId: 'steel_ingot', quantity: 8 },
                { itemId: 'leather_padding', quantity: 4 },
                { itemId: 'metal_buckles', quantity: 6 }
            ],
            result: {
                itemId: 'steel_armor',
                quantity: 1,
                qualityRange: [3, 5] // Uncommon to Epic
            },
            skillGain: 15,
            category: 'armor',
            unlocked: false,
            reagents: [],
            tools: ['smithing_hammer', 'anvil'],
            discoverable: false
        });

        // Alchemy Recipes
        this.recipes.set('health_potion', {
            id: 'health_potion',
            name: 'Health Potion',
            description: 'Restores health when consumed.',
            profession: CRAFTING_CONFIG.PROFESSIONS.ALCHEMY,
            skillRequired: 25,
            station: CRAFTING_CONFIG.STATIONS.ALCHEMY_TABLE,
            craftTime: 15000, // 15 seconds
            materials: [
                { itemId: 'red_herb', quantity: 2 },
                { itemId: 'pure_water', quantity: 1 },
                { itemId: 'glass_vial', quantity: 1 }
            ],
            result: {
                itemId: 'health_potion',
                quantity: 3,
                qualityRange: [1, 3] // Poor to Uncommon
            },
            skillGain: 3,
            category: 'potions',
            unlocked: true, // Basic recipe, starts unlocked
            reagents: ['alchemical_salt'],
            tools: ['mortar_pestle'],
            discoverable: false
        });

        this.recipes.set('invisibility_potion', {
            id: 'invisibility_potion',
            name: 'Invisibility Potion',
            description: 'Grants temporary invisibility.',
            profession: CRAFTING_CONFIG.PROFESSIONS.ALCHEMY,
            skillRequired: 300,
            station: CRAFTING_CONFIG.STATIONS.ALCHEMY_TABLE,
            craftTime: 60000, // 1 minute
            materials: [
                { itemId: 'ghost_essence', quantity: 1 },
                { itemId: 'shadow_herb', quantity: 3 },
                { itemId: 'crystal_vial', quantity: 1 },
                { itemId: 'moonstone_dust', quantity: 2 }
            ],
            result: {
                itemId: 'invisibility_potion',
                quantity: 1,
                qualityRange: [4, 6] // Rare to Legendary
            },
            skillGain: 25,
            category: 'potions',
            unlocked: false,
            reagents: ['void_catalyst'],
            tools: ['master_alembic'],
            discoverable: true
        });

        // Cooking Recipes
        this.recipes.set('bread', {
            id: 'bread',
            name: 'Fresh Bread',
            description: 'Nourishing bread that restores hunger.',
            profession: CRAFTING_CONFIG.PROFESSIONS.COOKING,
            skillRequired: 10,
            station: CRAFTING_CONFIG.STATIONS.COOKING_FIRE,
            craftTime: 20000, // 20 seconds
            materials: [
                { itemId: 'wheat_flour', quantity: 2 },
                { itemId: 'water', quantity: 1 },
                { itemId: 'salt', quantity: 1 }
            ],
            result: {
                itemId: 'bread',
                quantity: 4,
                qualityRange: [1, 2] // Poor to Common
            },
            skillGain: 2,
            category: 'food',
            unlocked: true,
            reagents: [],
            tools: [],
            discoverable: false
        });

        // Enchanting Recipes
        this.recipes.set('weapon_sharpness', {
            id: 'weapon_sharpness',
            name: 'Enchantment: Sharpness',
            description: 'Increases weapon damage.',
            profession: CRAFTING_CONFIG.PROFESSIONS.ENCHANTING,
            skillRequired: 100,
            station: CRAFTING_CONFIG.STATIONS.ENCHANTING_TABLE,
            craftTime: 45000, // 45 seconds
            materials: [
                { itemId: 'arcane_crystal', quantity: 1 },
                { itemId: 'essence_of_power', quantity: 2 },
                { itemId: 'enchanted_ink', quantity: 1 }
            ],
            result: {
                itemId: 'sharpness_enchant',
                quantity: 1,
                qualityRange: [3, 5] // Uncommon to Epic
            },
            skillGain: 10,
            category: 'enchantments',
            unlocked: false,
            reagents: ['mana_dust'],
            tools: ['enchanting_focus'],
            discoverable: false,
            enchantment: {
                type: 'weapon',
                effect: 'damage_increase',
                power: [5, 15] // 5-15% damage increase based on quality
            }
        });

        // Engineering Recipes
        this.recipes.set('mechanical_golem', {
            id: 'mechanical_golem',
            name: 'Mechanical Golem',
            description: 'A mechanical companion that assists in combat.',
            profession: CRAFTING_CONFIG.PROFESSIONS.ENGINEERING,
            skillRequired: 400,
            station: CRAFTING_CONFIG.STATIONS.ENGINEERING_BENCH,
            craftTime: 300000, // 5 minutes
            materials: [
                { itemId: 'steel_plates', quantity: 10 },
                { itemId: 'copper_wiring', quantity: 5 },
                { itemId: 'power_core', quantity: 1 },
                { itemId: 'precision_gears', quantity: 8 }
            ],
            result: {
                itemId: 'mechanical_golem',
                quantity: 1,
                qualityRange: [4, 6] // Rare to Legendary
            },
            skillGain: 50,
            category: 'constructs',
            unlocked: false,
            reagents: ['engineering_oil'],
            tools: ['precision_tools', 'welding_kit'],
            discoverable: true
        });
    }

    initializeCraftingStations() {
        this.craftingStations.set('forge_001', {
            id: 'forge_001',
            type: CRAFTING_CONFIG.STATIONS.FORGE,
            name: 'Master Forge',
            level: 3,
            location: { zone: 'starter_town', x: 100, y: 150 },
            bonuses: {
                craftSpeed: 1.2,   // 20% faster crafting
                criticalChance: 0.02, // +2% critical chance
                qualityBonus: 0.1     // +10% quality bonus
            },
            requirements: {
                profession: CRAFTING_CONFIG.PROFESSIONS.BLACKSMITHING,
                minSkill: 0
            },
            inUse: false,
            usedBy: null,
            durability: 1000,
            maxDurability: 1000
        });

        this.craftingStations.set('alchemy_001', {
            id: 'alchemy_001',
            type: CRAFTING_CONFIG.STATIONS.ALCHEMY_TABLE,
            name: 'Arcane Alchemy Station',
            level: 2,
            location: { zone: 'starter_town', x: 200, y: 100 },
            bonuses: {
                craftSpeed: 1.1,
                criticalChance: 0.03,
                qualityBonus: 0.15
            },
            requirements: {
                profession: CRAFTING_CONFIG.PROFESSIONS.ALCHEMY,
                minSkill: 0
            },
            inUse: false,
            usedBy: null,
            durability: 800,
            maxDurability: 800
        });
    }

    setupEventListeners() {
        this.gameEngine.on('playerLevelUp', this.handlePlayerLevelUp.bind(this));
        this.gameEngine.on('itemGathered', this.handleItemGathered.bind(this));
        this.gameEngine.on('monsterKilled', this.handleMonsterKilled.bind(this));
    }

    startCraftingUpdates() {
        // Process active crafting sessions
        setInterval(() => {
            this.processActiveCrafting();
            this.processCraftingQueues();
        }, 1000); // Every second

        // Repair and maintain stations
        setInterval(() => {
            this.maintainCraftingStations();
        }, 300000); // Every 5 minutes
    }

    // Player Skill Management
    initializePlayerSkills(playerId) {
        if (!this.playerSkills.has(playerId)) {
            const skills = {};
            
            for (const profession of Object.values(CRAFTING_CONFIG.PROFESSIONS)) {
                skills[profession] = {
                    level: 1,
                    experience: 0,
                    recipes: [],
                    discoveries: 0,
                    totalCrafted: 0,
                    criticalCrafts: 0,
                    specializations: []
                };
            }
            
            this.playerSkills.set(playerId, skills);
            
            // Unlock basic recipes
            this.unlockBasicRecipes(playerId);
        }
    }

    unlockBasicRecipes(playerId) {
        const basicRecipes = Array.from(this.recipes.values())
            .filter(recipe => recipe.unlocked);
        
        const playerSkills = this.playerSkills.get(playerId);
        
        for (const recipe of basicRecipes) {
            const professionSkill = playerSkills[recipe.profession];
            if (!professionSkill.recipes.includes(recipe.id)) {
                professionSkill.recipes.push(recipe.id);
            }
        }
    }

    addSkillExperience(playerId, profession, amount) {
        this.initializePlayerSkills(playerId);
        
        const playerSkills = this.playerSkills.get(playerId);
        const skill = playerSkills[profession];
        
        skill.experience += amount;
        
        // Check for level up
        const expNeeded = skill.level * CRAFTING_CONFIG.SKILL_POINTS_PER_LEVEL;
        if (skill.experience >= expNeeded && skill.level < CRAFTING_CONFIG.MAX_SKILL_LEVEL) {
            skill.level++;
            skill.experience -= expNeeded;
            
            this.emit('craftingSkillLevelUp', {
                playerId,
                profession,
                newLevel: skill.level,
                oldLevel: skill.level - 1
            });
            
            // Unlock new recipes based on skill level
            this.checkRecipeUnlocks(playerId, profession, skill.level);
        }
    }

    checkRecipeUnlocks(playerId, profession, skillLevel) {
        const playerSkills = this.playerSkills.get(playerId);
        const professionSkill = playerSkills[profession];
        
        const newRecipes = Array.from(this.recipes.values())
            .filter(recipe => 
                recipe.profession === profession &&
                recipe.skillRequired <= skillLevel &&
                !recipe.discoverable &&
                !professionSkill.recipes.includes(recipe.id)
            );
        
        for (const recipe of newRecipes) {
            professionSkill.recipes.push(recipe.id);
            this.emit('recipeUnlocked', {
                playerId,
                recipeId: recipe.id,
                profession
            });
        }
    }

    // Crafting Process
    async startCrafting(playerId, recipeId, quantity = 1, stationId = null) {
        try {
            const recipe = this.recipes.get(recipeId);
            if (!recipe) {
                throw new Error('Recipe not found');
            }

            this.initializePlayerSkills(playerId);
            
            const playerSkills = this.playerSkills.get(playerId);
            const professionSkill = playerSkills[recipe.profession];
            
            // Check if player knows the recipe
            if (!professionSkill.recipes.includes(recipeId)) {
                throw new Error('Recipe not known');
            }
            
            // Check skill requirement
            if (professionSkill.level < recipe.skillRequired) {
                throw new Error('Insufficient skill level');
            }
            
            // Check if player is already crafting
            if (this.activeCrafting.has(playerId)) {
                throw new Error('Already crafting');
            }
            
            // Find and reserve crafting station
            const station = await this.reserveCraftingStation(playerId, recipe.station, stationId);
            
            // Check materials
            const hasAllMaterials = await this.checkMaterials(playerId, recipe, quantity);
            if (!hasAllMaterials) {
                this.releaseCraftingStation(station.id);
                throw new Error('Insufficient materials');
            }
            
            // Consume materials
            await this.consumeMaterials(playerId, recipe, quantity);
            
            // Calculate craft time with bonuses
            let craftTime = recipe.craftTime * quantity;
            if (station.bonuses.craftSpeed) {
                craftTime = Math.floor(craftTime / station.bonuses.craftSpeed);
            }
            
            // Create crafting session
            const session = {
                playerId,
                recipeId,
                quantity,
                stationId: station.id,
                startTime: Date.now(),
                endTime: Date.now() + craftTime,
                profession: recipe.profession
            };
            
            this.activeCrafting.set(playerId, session);
            
            this.emit('craftingStarted', { session, recipe, station });
            return session;
            
        } catch (error) {
            throw error;
        }
    }

    async reserveCraftingStation(playerId, stationType, preferredStationId = null) {
        let station = null;
        
        if (preferredStationId) {
            station = this.craftingStations.get(preferredStationId);
            if (!station || station.type !== stationType || station.inUse) {
                throw new Error('Preferred crafting station not available');
            }
        } else {
            // Find any available station of the required type
            for (const [stationId, stationData] of this.craftingStations.entries()) {
                if (stationData.type === stationType && !stationData.inUse) {
                    station = stationData;
                    break;
                }
            }
        }
        
        if (!station) {
            throw new Error('No available crafting station');
        }
        
        // Reserve the station
        station.inUse = true;
        station.usedBy = playerId;
        
        return station;
    }

    releaseCraftingStation(stationId) {
        const station = this.craftingStations.get(stationId);
        if (station) {
            station.inUse = false;
            station.usedBy = null;
        }
    }

    async checkMaterials(playerId, recipe, quantity) {
        for (const material of recipe.materials) {
            const required = material.quantity * quantity;
            const playerHas = await this.gameEngine.inventoryManager.getItemQuantity(
                playerId, 
                material.itemId
            );
            
            if (playerHas < required) {
                return false;
            }
        }
        
        return true;
    }

    async consumeMaterials(playerId, recipe, quantity) {
        for (const material of recipe.materials) {
            const required = material.quantity * quantity;
            await this.gameEngine.inventoryManager.removeItem(
                playerId,
                material.itemId,
                required
            );
        }
    }

    processActiveCrafting() {
        const now = Date.now();
        const completedSessions = [];
        
        for (const [playerId, session] of this.activeCrafting.entries()) {
            if (now >= session.endTime) {
                completedSessions.push(session);
            }
        }
        
        for (const session of completedSessions) {
            this.completeCrafting(session);
        }
    }

    async completeCrafting(session) {
        try {
            const recipe = this.recipes.get(session.recipeId);
            const station = this.craftingStations.get(session.stationId);
            
            // Remove from active crafting
            this.activeCrafting.delete(session.playerId);
            
            // Release crafting station
            this.releaseCraftingStation(session.stationId);
            
            // Calculate results
            const results = this.calculateCraftingResults(session, recipe, station);
            
            // Add items to player inventory
            for (const result of results) {
                await this.gameEngine.inventoryManager.addItem(
                    session.playerId,
                    result.itemId,
                    result.quantity,
                    result.quality
                );
            }
            
            // Grant skill experience
            const expGained = recipe.skillGain * session.quantity;
            this.addSkillExperience(session.playerId, recipe.profession, expGained);
            
            // Update statistics
            const playerSkills = this.playerSkills.get(session.playerId);
            const professionSkill = playerSkills[recipe.profession];
            professionSkill.totalCrafted += session.quantity;
            
            // Check for critical crafts
            const criticalCrafts = results.filter(r => r.critical).length;
            professionSkill.criticalCrafts += criticalCrafts;
            
            // Check for recipe discoveries
            await this.checkRecipeDiscovery(session.playerId, recipe.profession);
            
            this.emit('craftingCompleted', {
                session,
                results,
                expGained,
                criticalCrafts
            });
            
            // Trigger quest updates
            this.gameEngine.emit('itemCrafted', {
                playerId: session.playerId,
                itemId: recipe.result.itemId,
                quantity: results.reduce((sum, r) => sum + r.quantity, 0)
            });
            
        } catch (error) {
            console.error('Error completing crafting:', error);
            this.activeCrafting.delete(session.playerId);
            this.releaseCraftingStation(session.stationId);
        }
    }

    calculateCraftingResults(session, recipe, station) {
        const results = [];
        
        for (let i = 0; i < session.quantity; i++) {
            // Calculate critical chance
            let criticalChance = CRAFTING_CONFIG.CRITICAL_CRAFT_CHANCE;
            if (station.bonuses.criticalChance) {
                criticalChance += station.bonuses.criticalChance;
            }
            
            const isCritical = Math.random() < criticalChance;
            
            // Calculate quality
            let quality = _.random(recipe.result.qualityRange[0], recipe.result.qualityRange[1]);
            if (station.bonuses.qualityBonus) {
                quality = Math.min(quality + Math.floor(quality * station.bonuses.qualityBonus), 7);
            }
            
            // Calculate quantity (critical crafts may yield more)
            let quantity = recipe.result.quantity;
            if (isCritical) {
                quantity = Math.ceil(quantity * 1.5);
            }
            
            results.push({
                itemId: recipe.result.itemId,
                quantity,
                quality,
                critical: isCritical
            });
        }
        
        return results;
    }

    async checkRecipeDiscovery(playerId, profession) {
        const playerSkills = this.playerSkills.get(playerId);
        const professionSkill = playerSkills[profession];
        
        if (Math.random() < CRAFTING_CONFIG.DISCOVERY_CHANCE) {
            const discoverableRecipes = Array.from(this.recipes.values())
                .filter(recipe => 
                    recipe.profession === profession &&
                    recipe.discoverable &&
                    recipe.skillRequired <= professionSkill.level &&
                    !professionSkill.recipes.includes(recipe.id)
                );
            
            if (discoverableRecipes.length > 0) {
                const discoveredRecipe = _.sample(discoverableRecipes);
                professionSkill.recipes.push(discoveredRecipe.id);
                professionSkill.discoveries++;
                
                this.emit('recipeDiscovered', {
                    playerId,
                    recipeId: discoveredRecipe.id,
                    profession
                });
            }
        }
    }

    // Crafting Queue System
    addToCraftingQueue(playerId, recipeId, quantity = 1) {
        if (!this.craftingQueues.has(playerId)) {
            this.craftingQueues.set(playerId, []);
        }
        
        const queue = this.craftingQueues.get(playerId);
        queue.push({
            id: uuidv4(),
            recipeId,
            quantity,
            addedAt: Date.now()
        });
        
        this.emit('addedToCraftingQueue', { playerId, recipeId, quantity });
    }

    processCraftingQueues() {
        for (const [playerId, queue] of this.craftingQueues.entries()) {
            if (queue.length > 0 && !this.activeCrafting.has(playerId)) {
                const nextCraft = queue.shift();
                
                // Try to start the next craft
                this.startCrafting(playerId, nextCraft.recipeId, nextCraft.quantity)
                    .catch(error => {
                        console.log(`Could not start queued craft for player ${playerId}:`, error.message);
                        // Re-add to front of queue to try again later
                        queue.unshift(nextCraft);
                    });
            }
        }
    }

    // Enchanting System
    async enchantItem(playerId, itemId, enchantmentId) {
        try {
            const enchantment = this.recipes.get(enchantmentId);
            if (!enchantment || !enchantment.enchantment) {
                throw new Error('Invalid enchantment');
            }
            
            const item = await this.gameEngine.inventoryManager.getItem(playerId, itemId);
            if (!item) {
                throw new Error('Item not found');
            }
            
            // Check if item can be enchanted
            if (item.type !== enchantment.enchantment.type) {
                throw new Error('Item type incompatible with enchantment');
            }
            
            // Apply enchantment
            const power = _.random(
                enchantment.enchantment.power[0],
                enchantment.enchantment.power[1]
            );
            
            await this.gameEngine.inventoryManager.enchantItem(
                playerId,
                itemId,
                {
                    type: enchantment.enchantment.effect,
                    power: power,
                    source: enchantmentId
                }
            );
            
            this.emit('itemEnchanted', {
                playerId,
                itemId,
                enchantmentId,
                power
            });
            
            return true;
            
        } catch (error) {
            throw error;
        }
    }

    // Station Management
    maintainCraftingStations() {
        for (const [stationId, station] of this.craftingStations.entries()) {
            // Reduce durability over time
            if (station.inUse) {
                station.durability = Math.max(0, station.durability - 1);
                
                if (station.durability <= 0) {
                    // Station breaks down
                    station.inUse = false;
                    station.usedBy = null;
                    
                    this.emit('craftingStationBroken', { stationId, station });
                }
            } else {
                // Slowly repair when not in use
                station.durability = Math.min(
                    station.maxDurability,
                    station.durability + 2
                );
            }
        }
    }

    async repairCraftingStation(stationId, playerId) {
        const station = this.craftingStations.get(stationId);
        if (!station) {
            throw new Error('Station not found');
        }
        
        const repairCost = Math.floor((station.maxDurability - station.durability) * 10);
        
        const hasGold = await this.gameEngine.economyManager.hasGold(playerId, repairCost);
        if (!hasGold) {
            throw new Error('Insufficient gold for repair');
        }
        
        await this.gameEngine.economyManager.deductGold(playerId, repairCost);
        station.durability = station.maxDurability;
        
        this.emit('craftingStationRepaired', { stationId, playerId, cost: repairCost });
        return repairCost;
    }

    // Event Handlers
    handlePlayerLevelUp(data) {
        const { playerId, newLevel } = data;
        
        // Grant bonus crafting experience on level up
        this.initializePlayerSkills(playerId);
        const playerSkills = this.playerSkills.get(playerId);
        
        for (const profession of Object.values(CRAFTING_CONFIG.PROFESSIONS)) {
            this.addSkillExperience(playerId, profession, newLevel * 5);
        }
    }

    handleItemGathered(data) {
        const { playerId, itemId } = data;
        
        // Some gathered items might unlock recipes
        this.checkGatheringRecipeUnlocks(playerId, itemId);
    }

    handleMonsterKilled(data) {
        const { playerId, monsterId } = data;
        
        // Rare monsters might drop crafting recipes
        this.checkMonsterRecipeDrops(playerId, monsterId);
    }

    checkGatheringRecipeUnlocks(playerId, itemId) {
        // Implementation for unlocking recipes based on gathered items
        // This could be expanded based on specific game logic
    }

    checkMonsterRecipeDrops(playerId, monsterId) {
        // Implementation for recipe drops from monsters
        // This could be expanded based on specific game logic
    }

    // Public API Methods
    getPlayerSkills(playerId) {
        this.initializePlayerSkills(playerId);
        return this.playerSkills.get(playerId);
    }

    getKnownRecipes(playerId, profession = null) {
        this.initializePlayerSkills(playerId);
        const playerSkills = this.playerSkills.get(playerId);
        
        let allRecipes = [];
        
        if (profession) {
            const professionSkill = playerSkills[profession];
            allRecipes = professionSkill.recipes.map(recipeId => this.recipes.get(recipeId));
        } else {
            for (const professionSkill of Object.values(playerSkills)) {
                const recipes = professionSkill.recipes.map(recipeId => this.recipes.get(recipeId));
                allRecipes.push(...recipes);
            }
        }
        
        return allRecipes.filter(Boolean);
    }

    getRecipe(recipeId) {
        return this.recipes.get(recipeId);
    }

    getAvailableStations(stationType = null) {
        const stations = Array.from(this.craftingStations.values());
        
        if (stationType) {
            return stations.filter(station => 
                station.type === stationType && !station.inUse
            );
        }
        
        return stations.filter(station => !station.inUse);
    }

    getCraftingStatus(playerId) {
        const session = this.activeCrafting.get(playerId);
        if (!session) {
            return null;
        }
        
        const recipe = this.recipes.get(session.recipeId);
        const timeRemaining = Math.max(0, session.endTime - Date.now());
        const progress = Math.min(100, Math.floor(
            ((session.endTime - session.startTime) - timeRemaining) / 
            (session.endTime - session.startTime) * 100
        ));
        
        return {
            recipe,
            progress,
            timeRemaining,
            stationId: session.stationId
        };
    }

    getCraftingQueue(playerId) {
        return this.craftingQueues.get(playerId) || [];
    }

    cancelCrafting(playerId) {
        const session = this.activeCrafting.get(playerId);
        if (!session) {
            throw new Error('No active crafting session');
        }
        
        // Release station
        this.releaseCraftingStation(session.stationId);
        
        // Remove session
        this.activeCrafting.delete(playerId);
        
        // Could implement partial material refund here
        
        this.emit('craftingCancelled', { session });
        return session;
    }

    getCraftingStatistics() {
        let totalActiveCrafting = this.activeCrafting.size;
        let totalStations = this.craftingStations.size;
        let stationsInUse = Array.from(this.craftingStations.values())
            .filter(station => station.inUse).length;
        
        return {
            totalRecipes: this.recipes.size,
            totalActiveCrafting,
            totalStations,
            stationsInUse,
            stationUtilization: totalStations > 0 ? stationsInUse / totalStations : 0
        };
    }
}

module.exports = CraftingManager;