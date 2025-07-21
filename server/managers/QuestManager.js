const EventEmitter = require('events');
const { v4: uuidv4 } = require('uuid');
const _ = require('lodash');
const moment = require('moment');

const QUEST_CONFIG = {
    TYPES: {
        STORY: 'story',
        DAILY: 'daily',
        WEEKLY: 'weekly',
        GUILD: 'guild',
        DUNGEON: 'dungeon',
        PVP: 'pvp',
        EXPLORATION: 'exploration',
        CRAFTING: 'crafting',
        GATHERING: 'gathering',
        ACHIEVEMENT: 'achievement'
    },
    OBJECTIVES: {
        KILL_MONSTERS: 'kill_monsters',
        COLLECT_ITEMS: 'collect_items',
        REACH_LOCATION: 'reach_location',
        TALK_TO_NPC: 'talk_to_npc',
        CRAFT_ITEMS: 'craft_items',
        GATHER_RESOURCES: 'gather_resources',
        WIN_PVP: 'win_pvp',
        COMPLETE_DUNGEON: 'complete_dungeon',
        REACH_LEVEL: 'reach_level',
        EARN_GOLD: 'earn_gold',
        USE_SKILL: 'use_skill',
        VISIT_ZONES: 'visit_zones'
    },
    DIFFICULTY: {
        TRIVIAL: 1,
        EASY: 2,
        NORMAL: 3,
        HARD: 4,
        EXPERT: 5,
        LEGENDARY: 6
    },
    REWARDS: {
        EXPERIENCE: 'experience',
        GOLD: 'gold',
        ITEMS: 'items',
        REPUTATION: 'reputation',
        SKILLS: 'skills',
        TITLES: 'titles',
        UNLOCKS: 'unlocks'
    },
    MAX_ACTIVE_QUESTS: 20,
    DAILY_QUEST_RESET: 24 * 60 * 60 * 1000, // 24 hours
    WEEKLY_QUEST_RESET: 7 * 24 * 60 * 60 * 1000 // 7 days
};

class QuestManager extends EventEmitter {
    constructor(gameEngine) {
        super();
        this.gameEngine = gameEngine;
        this.questTemplates = new Map();
        this.activeQuests = new Map(); // playerId -> [quests]
        this.completedQuests = new Map(); // playerId -> [questIds]
        this.dailyQuests = new Map(); // date -> [questTemplates]
        this.weeklyQuests = new Map(); // week -> [questTemplates]
        this.guildQuests = new Map(); // guildId -> [quests]
        this.questChains = new Map(); // questId -> [followUpQuests]
        this.questGivers = new Map(); // npcId -> [questIds]
        this.questProgression = new Map(); // playerId -> questData
        
        this.initializeQuestTemplates();
        this.setupEventListeners();
        this.startQuestUpdates();
    }

    initializeQuestTemplates() {
        // Story Quests
        this.questTemplates.set('story_001', {
            id: 'story_001',
            name: 'Welcome to the World',
            description: 'Begin your journey by exploring the starter town and talking to key NPCs.',
            type: QUEST_CONFIG.TYPES.STORY,
            difficulty: QUEST_CONFIG.DIFFICULTY.TRIVIAL,
            level: 1,
            prerequisites: [],
            objectives: [
                {
                    id: 'obj_001',
                    type: QUEST_CONFIG.OBJECTIVES.TALK_TO_NPC,
                    target: 'npc_mayor',
                    current: 0,
                    required: 1,
                    description: 'Talk to the Town Mayor'
                },
                {
                    id: 'obj_002',
                    type: QUEST_CONFIG.OBJECTIVES.REACH_LOCATION,
                    target: 'fountain_square',
                    current: 0,
                    required: 1,
                    description: 'Visit the Town Square'
                }
            ],
            rewards: {
                experience: 100,
                gold: 50,
                items: ['starter_sword', 'health_potion']
            },
            timeLimit: null,
            repeatable: false,
            chain: 'starter_chain'
        });

        this.questTemplates.set('story_002', {
            id: 'story_002',
            name: 'First Steps in Combat',
            description: 'Learn the basics of combat by defeating some weak monsters.',
            type: QUEST_CONFIG.TYPES.STORY,
            difficulty: QUEST_CONFIG.DIFFICULTY.EASY,
            level: 2,
            prerequisites: ['story_001'],
            objectives: [
                {
                    id: 'obj_003',
                    type: QUEST_CONFIG.OBJECTIVES.KILL_MONSTERS,
                    target: 'forest_slime',
                    current: 0,
                    required: 5,
                    description: 'Defeat 5 Forest Slimes'
                }
            ],
            rewards: {
                experience: 250,
                gold: 100,
                items: ['leather_armor']
            },
            timeLimit: null,
            repeatable: false,
            chain: 'starter_chain'
        });

        // Daily Quests
        this.questTemplates.set('daily_001', {
            id: 'daily_001',
            name: 'Monster Slayer',
            description: 'Defeat various monsters throughout the world.',
            type: QUEST_CONFIG.TYPES.DAILY,
            difficulty: QUEST_CONFIG.DIFFICULTY.NORMAL,
            level: 5,
            prerequisites: [],
            objectives: [
                {
                    id: 'obj_daily_001',
                    type: QUEST_CONFIG.OBJECTIVES.KILL_MONSTERS,
                    target: 'any',
                    current: 0,
                    required: 10,
                    description: 'Defeat 10 monsters'
                }
            ],
            rewards: {
                experience: 500,
                gold: 200,
                reputation: { faction: 'adventurers_guild', amount: 50 }
            },
            timeLimit: QUEST_CONFIG.DAILY_QUEST_RESET,
            repeatable: true,
            chain: null
        });

        this.questTemplates.set('daily_002', {
            id: 'daily_002',
            name: 'Resource Gatherer',
            description: 'Collect valuable resources from around the world.',
            type: QUEST_CONFIG.TYPES.DAILY,
            difficulty: QUEST_CONFIG.DIFFICULTY.EASY,
            level: 3,
            prerequisites: [],
            objectives: [
                {
                    id: 'obj_daily_002',
                    type: QUEST_CONFIG.OBJECTIVES.GATHER_RESOURCES,
                    target: 'any',
                    current: 0,
                    required: 20,
                    description: 'Gather 20 resources'
                }
            ],
            rewards: {
                experience: 300,
                gold: 150,
                items: ['gathering_tool']
            },
            timeLimit: QUEST_CONFIG.DAILY_QUEST_RESET,
            repeatable: true,
            chain: null
        });

        // Crafting Quests
        this.questTemplates.set('craft_001', {
            id: 'craft_001',
            name: 'Master Blacksmith',
            description: 'Prove your smithing skills by crafting various weapons.',
            type: QUEST_CONFIG.TYPES.CRAFTING,
            difficulty: QUEST_CONFIG.DIFFICULTY.HARD,
            level: 15,
            prerequisites: [],
            objectives: [
                {
                    id: 'obj_craft_001',
                    type: QUEST_CONFIG.OBJECTIVES.CRAFT_ITEMS,
                    target: 'iron_sword',
                    current: 0,
                    required: 3,
                    description: 'Craft 3 Iron Swords'
                },
                {
                    id: 'obj_craft_002',
                    type: QUEST_CONFIG.OBJECTIVES.CRAFT_ITEMS,
                    target: 'steel_armor',
                    current: 0,
                    required: 1,
                    description: 'Craft 1 Steel Armor'
                }
            ],
            rewards: {
                experience: 1000,
                gold: 500,
                skills: { smithing: 100 },
                titles: ['Apprentice Smith']
            },
            timeLimit: null,
            repeatable: false,
            chain: 'crafting_chain'
        });

        // PvP Quests
        this.questTemplates.set('pvp_001', {
            id: 'pvp_001',
            name: 'Arena Champion',
            description: 'Prove yourself in player vs player combat.',
            type: QUEST_CONFIG.TYPES.PVP,
            difficulty: QUEST_CONFIG.DIFFICULTY.EXPERT,
            level: 20,
            prerequisites: [],
            objectives: [
                {
                    id: 'obj_pvp_001',
                    type: QUEST_CONFIG.OBJECTIVES.WIN_PVP,
                    target: 'any',
                    current: 0,
                    required: 5,
                    description: 'Win 5 PvP battles'
                }
            ],
            rewards: {
                experience: 2000,
                gold: 1000,
                items: ['pvp_trophy', 'champion_ring'],
                titles: ['Arena Warrior']
            },
            timeLimit: null,
            repeatable: true,
            chain: null
        });

        // Guild Quests
        this.questTemplates.set('guild_001', {
            id: 'guild_001',
            name: 'Guild Unity',
            description: 'Work together with your guild members to achieve common goals.',
            type: QUEST_CONFIG.TYPES.GUILD,
            difficulty: QUEST_CONFIG.DIFFICULTY.NORMAL,
            level: 10,
            prerequisites: [],
            objectives: [
                {
                    id: 'obj_guild_001',
                    type: QUEST_CONFIG.OBJECTIVES.KILL_MONSTERS,
                    target: 'boss_dragon',
                    current: 0,
                    required: 1,
                    description: 'Defeat the Ancient Dragon as a guild (requires 5+ members)'
                }
            ],
            rewards: {
                experience: 1500,
                gold: 2000,
                reputation: { faction: 'guild', amount: 200 },
                unlocks: ['guild_hall_upgrade']
            },
            timeLimit: null,
            repeatable: false,
            chain: null,
            guildQuest: true,
            minMembers: 5
        });

        this.initializeQuestChains();
    }

    initializeQuestChains() {
        this.questChains.set('starter_chain', ['story_001', 'story_002', 'story_003']);
        this.questChains.set('crafting_chain', ['craft_001', 'craft_002', 'craft_003']);
        this.questChains.set('exploration_chain', ['explore_001', 'explore_002', 'explore_003']);
    }

    setupEventListeners() {
        this.gameEngine.on('playerLevelUp', this.handlePlayerLevelUp.bind(this));
        this.gameEngine.on('monsterKilled', this.handleMonsterKilled.bind(this));
        this.gameEngine.on('itemCrafted', this.handleItemCrafted.bind(this));
        this.gameEngine.on('itemGathered', this.handleItemGathered.bind(this));
        this.gameEngine.on('locationReached', this.handleLocationReached.bind(this));
        this.gameEngine.on('npcInteraction', this.handleNpcInteraction.bind(this));
        this.gameEngine.on('pvpWin', this.handlePvpWin.bind(this));
        this.gameEngine.on('skillUsed', this.handleSkillUsed.bind(this));
        this.gameEngine.on('goldEarned', this.handleGoldEarned.bind(this));
    }

    startQuestUpdates() {
        // Daily quest reset
        setInterval(() => {
            this.resetDailyQuests();
        }, 60000); // Check every minute

        // Weekly quest reset
        setInterval(() => {
            this.resetWeeklyQuests();
        }, 3600000); // Check every hour

        // Quest cleanup and validation
        setInterval(() => {
            this.cleanupExpiredQuests();
            this.validateQuestProgress();
        }, 300000); // Every 5 minutes
    }

    // Quest Assignment
    async assignQuest(playerId, questId, giver = null) {
        try {
            const template = this.questTemplates.get(questId);
            if (!template) {
                throw new Error('Quest template not found');
            }

            const player = await this.gameEngine.getPlayer(playerId);
            if (!player) {
                throw new Error('Player not found');
            }

            if (player.level < template.level) {
                throw new Error('Player level too low for this quest');
            }

            // Check prerequisites
            const completedQuests = this.completedQuests.get(playerId) || [];
            for (const prereq of template.prerequisites) {
                if (!completedQuests.includes(prereq)) {
                    throw new Error(`Missing prerequisite quest: ${prereq}`);
                }
            }

            // Check if player already has this quest
            const activeQuests = this.activeQuests.get(playerId) || [];
            if (activeQuests.find(q => q.templateId === questId)) {
                throw new Error('Quest already active');
            }

            // Check if quest is completed and not repeatable
            if (!template.repeatable && completedQuests.includes(questId)) {
                throw new Error('Quest already completed');
            }

            // Check active quest limit
            if (activeQuests.length >= QUEST_CONFIG.MAX_ACTIVE_QUESTS) {
                throw new Error('Too many active quests');
            }

            // Create quest instance
            const quest = {
                id: uuidv4(),
                templateId: questId,
                playerId,
                name: template.name,
                description: template.description,
                type: template.type,
                difficulty: template.difficulty,
                objectives: template.objectives.map(obj => ({ ...obj })), // Deep copy
                rewards: { ...template.rewards },
                assignedAt: new Date(),
                expiresAt: template.timeLimit ? new Date(Date.now() + template.timeLimit) : null,
                giver: giver,
                status: 'active',
                progress: 0
            };

            // Add to active quests
            if (!this.activeQuests.has(playerId)) {
                this.activeQuests.set(playerId, []);
            }
            this.activeQuests.get(playerId).push(quest);

            this.emit('questAssigned', { quest, playerId });
            return quest;

        } catch (error) {
            throw error;
        }
    }

    // Quest Progress
    updateQuestProgress(playerId, objectiveType, target, amount = 1) {
        const activeQuests = this.activeQuests.get(playerId) || [];
        const updatedQuests = [];

        for (const quest of activeQuests) {
            let questUpdated = false;

            for (const objective of quest.objectives) {
                if (objective.type === objectiveType && 
                    (objective.target === target || objective.target === 'any')) {
                    
                    const oldCurrent = objective.current;
                    objective.current = Math.min(objective.current + amount, objective.required);
                    
                    if (objective.current > oldCurrent) {
                        questUpdated = true;
                        this.emit('questProgressUpdated', {
                            quest,
                            objective,
                            playerId,
                            oldProgress: oldCurrent,
                            newProgress: objective.current
                        });
                    }
                }
            }

            if (questUpdated) {
                this.updateQuestStatus(quest);
                updatedQuests.push(quest);
            }
        }

        return updatedQuests;
    }

    updateQuestStatus(quest) {
        const totalObjectives = quest.objectives.length;
        const completedObjectives = quest.objectives.filter(obj => obj.current >= obj.required).length;
        
        quest.progress = Math.floor((completedObjectives / totalObjectives) * 100);

        if (completedObjectives === totalObjectives) {
            this.completeQuest(quest);
        }
    }

    async completeQuest(quest) {
        try {
            quest.status = 'completed';
            quest.completedAt = new Date();

            // Remove from active quests
            const activeQuests = this.activeQuests.get(quest.playerId);
            const questIndex = activeQuests.findIndex(q => q.id === quest.id);
            if (questIndex !== -1) {
                activeQuests.splice(questIndex, 1);
            }

            // Add to completed quests
            if (!this.completedQuests.has(quest.playerId)) {
                this.completedQuests.set(quest.playerId, []);
            }
            this.completedQuests.get(quest.playerId).push(quest.templateId);

            // Grant rewards
            await this.grantQuestRewards(quest.playerId, quest.rewards);

            // Check for follow-up quests
            await this.checkQuestChain(quest);

            this.emit('questCompleted', { quest, playerId: quest.playerId });
            return quest;

        } catch (error) {
            console.error('Error completing quest:', error);
            throw error;
        }
    }

    async grantQuestRewards(playerId, rewards) {
        const player = await this.gameEngine.getPlayer(playerId);
        if (!player) return;

        if (rewards.experience) {
            await this.gameEngine.playerManager.addExperience(playerId, rewards.experience);
        }

        if (rewards.gold) {
            await this.gameEngine.economyManager.addGold(playerId, rewards.gold);
        }

        if (rewards.items) {
            for (const itemId of rewards.items) {
                await this.gameEngine.inventoryManager.addItem(playerId, itemId);
            }
        }

        if (rewards.skills) {
            for (const [skill, amount] of Object.entries(rewards.skills)) {
                await this.gameEngine.playerManager.addSkillExperience(playerId, skill, amount);
            }
        }

        if (rewards.titles) {
            for (const title of rewards.titles) {
                await this.gameEngine.playerManager.addTitle(playerId, title);
            }
        }

        if (rewards.reputation) {
            await this.gameEngine.playerManager.addReputation(
                playerId, 
                rewards.reputation.faction, 
                rewards.reputation.amount
            );
        }

        this.emit('questRewardsGranted', { playerId, rewards });
    }

    async checkQuestChain(completedQuest) {
        const template = this.questTemplates.get(completedQuest.templateId);
        if (!template.chain) return;

        const chain = this.questChains.get(template.chain);
        if (!chain) return;

        const currentIndex = chain.indexOf(completedQuest.templateId);
        if (currentIndex !== -1 && currentIndex < chain.length - 1) {
            const nextQuestId = chain[currentIndex + 1];
            
            // Auto-assign next quest in chain
            try {
                await this.assignQuest(completedQuest.playerId, nextQuestId, completedQuest.giver);
            } catch (error) {
                console.log('Could not auto-assign next quest in chain:', error.message);
            }
        }
    }

    // Quest Abandonment
    abandonQuest(playerId, questId) {
        const activeQuests = this.activeQuests.get(playerId) || [];
        const questIndex = activeQuests.findIndex(q => q.id === questId);
        
        if (questIndex === -1) {
            throw new Error('Quest not found in active quests');
        }

        const quest = activeQuests[questIndex];
        activeQuests.splice(questIndex, 1);

        this.emit('questAbandoned', { quest, playerId });
        return quest;
    }

    // Daily/Weekly Quest Management
    generateDailyQuests() {
        const today = moment().format('YYYY-MM-DD');
        
        if (this.dailyQuests.has(today)) {
            return this.dailyQuests.get(today);
        }

        const dailyQuestTemplates = Array.from(this.questTemplates.values())
            .filter(template => template.type === QUEST_CONFIG.TYPES.DAILY);

        // Randomly select 3-5 daily quests
        const selectedQuests = _.sampleSize(dailyQuestTemplates, _.random(3, 5));
        
        this.dailyQuests.set(today, selectedQuests);
        return selectedQuests;
    }

    generateWeeklyQuests() {
        const thisWeek = moment().format('YYYY-[W]WW');
        
        if (this.weeklyQuests.has(thisWeek)) {
            return this.weeklyQuests.get(thisWeek);
        }

        const weeklyQuestTemplates = Array.from(this.questTemplates.values())
            .filter(template => template.type === QUEST_CONFIG.TYPES.WEEKLY);

        // Randomly select 2-3 weekly quests
        const selectedQuests = _.sampleSize(weeklyQuestTemplates, _.random(2, 3));
        
        this.weeklyQuests.set(thisWeek, selectedQuests);
        return selectedQuests;
    }

    resetDailyQuests() {
        const today = moment().format('YYYY-MM-DD');
        const yesterday = moment().subtract(1, 'day').format('YYYY-MM-DD');
        
        // Remove yesterday's quests
        this.dailyQuests.delete(yesterday);
        
        // Generate today's quests if not already done
        if (!this.dailyQuests.has(today)) {
            this.generateDailyQuests();
            this.emit('dailyQuestsReset', { date: today });
        }
    }

    resetWeeklyQuests() {
        const thisWeek = moment().format('YYYY-[W]WW');
        const lastWeek = moment().subtract(1, 'week').format('YYYY-[W]WW');
        
        // Remove last week's quests
        this.weeklyQuests.delete(lastWeek);
        
        // Generate this week's quests if not already done
        if (!this.weeklyQuests.has(thisWeek)) {
            this.generateWeeklyQuests();
            this.emit('weeklyQuestsReset', { week: thisWeek });
        }
    }

    // Event Handlers
    handlePlayerLevelUp(data) {
        const { playerId, newLevel } = data;
        this.updateQuestProgress(playerId, QUEST_CONFIG.OBJECTIVES.REACH_LEVEL, newLevel);
    }

    handleMonsterKilled(data) {
        const { playerId, monsterId } = data;
        this.updateQuestProgress(playerId, QUEST_CONFIG.OBJECTIVES.KILL_MONSTERS, monsterId);
        this.updateQuestProgress(playerId, QUEST_CONFIG.OBJECTIVES.KILL_MONSTERS, 'any');
    }

    handleItemCrafted(data) {
        const { playerId, itemId, quantity = 1 } = data;
        this.updateQuestProgress(playerId, QUEST_CONFIG.OBJECTIVES.CRAFT_ITEMS, itemId, quantity);
        this.updateQuestProgress(playerId, QUEST_CONFIG.OBJECTIVES.CRAFT_ITEMS, 'any', quantity);
    }

    handleItemGathered(data) {
        const { playerId, itemId, quantity = 1 } = data;
        this.updateQuestProgress(playerId, QUEST_CONFIG.OBJECTIVES.GATHER_RESOURCES, itemId, quantity);
        this.updateQuestProgress(playerId, QUEST_CONFIG.OBJECTIVES.GATHER_RESOURCES, 'any', quantity);
    }

    handleLocationReached(data) {
        const { playerId, locationId } = data;
        this.updateQuestProgress(playerId, QUEST_CONFIG.OBJECTIVES.REACH_LOCATION, locationId);
        this.updateQuestProgress(playerId, QUEST_CONFIG.OBJECTIVES.VISIT_ZONES, locationId);
    }

    handleNpcInteraction(data) {
        const { playerId, npcId } = data;
        this.updateQuestProgress(playerId, QUEST_CONFIG.OBJECTIVES.TALK_TO_NPC, npcId);
    }

    handlePvpWin(data) {
        const { playerId } = data;
        this.updateQuestProgress(playerId, QUEST_CONFIG.OBJECTIVES.WIN_PVP, 'any');
    }

    handleSkillUsed(data) {
        const { playerId, skillId } = data;
        this.updateQuestProgress(playerId, QUEST_CONFIG.OBJECTIVES.USE_SKILL, skillId);
        this.updateQuestProgress(playerId, QUEST_CONFIG.OBJECTIVES.USE_SKILL, 'any');
    }

    handleGoldEarned(data) {
        const { playerId, amount } = data;
        this.updateQuestProgress(playerId, QUEST_CONFIG.OBJECTIVES.EARN_GOLD, 'any', amount);
    }

    // Utility Methods
    cleanupExpiredQuests() {
        const now = new Date();
        
        for (const [playerId, quests] of this.activeQuests.entries()) {
            const validQuests = quests.filter(quest => {
                if (quest.expiresAt && quest.expiresAt <= now) {
                    this.emit('questExpired', { quest, playerId });
                    return false;
                }
                return true;
            });
            
            this.activeQuests.set(playerId, validQuests);
        }
    }

    validateQuestProgress() {
        // Validate quest progress against actual player data
        for (const [playerId, quests] of this.activeQuests.entries()) {
            for (const quest of quests) {
                this.validateQuestObjectives(quest);
            }
        }
    }

    async validateQuestObjectives(quest) {
        // This would check actual player data against quest objectives
        // to prevent cheating and ensure data consistency
        const player = await this.gameEngine.getPlayer(quest.playerId);
        if (!player) return;

        // Example validation logic would go here
        // For now, just ensure progress doesn't exceed requirements
        for (const objective of quest.objectives) {
            objective.current = Math.min(objective.current, objective.required);
        }
    }

    // Public API Methods
    getActiveQuests(playerId) {
        return this.activeQuests.get(playerId) || [];
    }

    getCompletedQuests(playerId) {
        return this.completedQuests.get(playerId) || [];
    }

    getAvailableQuests(playerId) {
        const player = this.gameEngine.getPlayer(playerId);
        if (!player) return [];

        const completedQuests = this.completedQuests.get(playerId) || [];
        const activeQuests = this.activeQuests.get(playerId) || [];
        
        return Array.from(this.questTemplates.values()).filter(template => {
            // Check level requirement
            if (player.level < template.level) return false;
            
            // Check if already active
            if (activeQuests.find(q => q.templateId === template.id)) return false;
            
            // Check if completed and not repeatable
            if (!template.repeatable && completedQuests.includes(template.id)) return false;
            
            // Check prerequisites
            for (const prereq of template.prerequisites) {
                if (!completedQuests.includes(prereq)) return false;
            }
            
            return true;
        });
    }

    getQuestTemplate(questId) {
        return this.questTemplates.get(questId);
    }

    getDailyQuests() {
        return this.generateDailyQuests();
    }

    getWeeklyQuests() {
        return this.generateWeeklyQuests();
    }

    getQuestsByType(type) {
        return Array.from(this.questTemplates.values())
            .filter(template => template.type === type);
    }

    getQuestStatistics() {
        let totalActive = 0;
        let totalCompleted = 0;
        
        for (const quests of this.activeQuests.values()) {
            totalActive += quests.length;
        }
        
        for (const completed of this.completedQuests.values()) {
            totalCompleted += completed.length;
        }
        
        return {
            totalTemplates: this.questTemplates.size,
            totalActive,
            totalCompleted,
            questChains: this.questChains.size
        };
    }

    searchQuests(query) {
        const searchTerm = query.toLowerCase();
        return Array.from(this.questTemplates.values())
            .filter(template => 
                template.name.toLowerCase().includes(searchTerm) ||
                template.description.toLowerCase().includes(searchTerm)
            );
    }

    // Admin Methods
    createQuestTemplate(questData) {
        const questId = questData.id || uuidv4();
        this.questTemplates.set(questId, {
            ...questData,
            id: questId
        });
        
        this.emit('questTemplateCreated', { questId, questData });
        return questId;
    }

    updateQuestTemplate(questId, updates) {
        const template = this.questTemplates.get(questId);
        if (!template) {
            throw new Error('Quest template not found');
        }
        
        Object.assign(template, updates);
        this.emit('questTemplateUpdated', { questId, updates });
        return template;
    }

    deleteQuestTemplate(questId) {
        const deleted = this.questTemplates.delete(questId);
        if (deleted) {
            this.emit('questTemplateDeleted', { questId });
        }
        return deleted;
    }
}

module.exports = QuestManager;