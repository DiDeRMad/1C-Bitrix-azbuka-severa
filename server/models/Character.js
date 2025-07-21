/**
 * Character Model - Player character data
 */

const mongoose = require('mongoose');

const characterSchema = new mongoose.Schema({
    // Basic character info
    name: {
        type: String,
        required: true,
        trim: true,
        minlength: 2,
        maxlength: 16,
        match: /^[a-zA-Z0-9_]+$/
    },
    player: {
        type: mongoose.Schema.Types.ObjectId,
        ref: 'Player',
        required: true
    },
    
    // Character class and appearance
    class: {
        type: String,
        required: true,
        enum: ['warrior', 'mage', 'archer', 'rogue', 'paladin']
    },
    gender: {
        type: String,
        required: true,
        enum: ['male', 'female']
    },
    appearance: {
        hairStyle: {
            type: Number,
            default: 1,
            min: 1,
            max: 10
        },
        hairColor: {
            type: String,
            default: '#8B4513',
            match: /^#[0-9A-F]{6}$/i
        },
        skinColor: {
            type: String,
            default: '#FDBCB4',
            match: /^#[0-9A-F]{6}$/i
        },
        eyeColor: {
            type: String,
            default: '#654321',
            match: /^#[0-9A-F]{6}$/i
        },
        faceStyle: {
            type: Number,
            default: 1,
            min: 1,
            max: 5
        }
    },
    
    // Core stats
    level: {
        type: Number,
        default: 1,
        min: 1,
        max: 1000
    },
    experience: {
        type: Number,
        default: 0,
        min: 0
    },
    
    // Attributes
    attributes: {
        strength: {
            type: Number,
            default: 10,
            min: 1
        },
        agility: {
            type: Number,
            default: 10,
            min: 1
        },
        intelligence: {
            type: Number,
            default: 10,
            min: 1
        },
        vitality: {
            type: Number,
            default: 10,
            min: 1
        },
        luck: {
            type: Number,
            default: 10,
            min: 1
        }
    },
    
    // Health, Mana, Stamina
    health: {
        type: Number,
        default: 100,
        min: 0
    },
    maxHealth: {
        type: Number,
        default: 100,
        min: 1
    },
    mana: {
        type: Number,
        default: 50,
        min: 0
    },
    maxMana: {
        type: Number,
        default: 50,
        min: 0
    },
    stamina: {
        type: Number,
        default: 100,
        min: 0
    },
    maxStamina: {
        type: Number,
        default: 100,
        min: 1
    },
    
    // Position and world state
    position: {
        x: {
            type: Number,
            default: 0
        },
        y: {
            type: Number,
            default: 0
        },
        zone: {
            type: String,
            default: 'starter_town'
        }
    },
    lastPosition: {
        x: Number,
        y: Number,
        zone: String
    },
    
    // Skills and abilities
    skills: [{
        skillId: {
            type: String,
            required: true
        },
        level: {
            type: Number,
            default: 1,
            min: 1,
            max: 100
        },
        experience: {
            type: Number,
            default: 0,
            min: 0
        },
        learnedAt: {
            type: Date,
            default: Date.now
        }
    }],
    
    // Talent points and builds
    talentPoints: {
        type: Number,
        default: 0,
        min: 0
    },
    talents: [{
        talentId: String,
        rank: {
            type: Number,
            min: 1,
            max: 5
        },
        learnedAt: {
            type: Date,
            default: Date.now
        }
    }],
    
    // Equipment
    equipment: {
        weapon: {
            type: mongoose.Schema.Types.ObjectId,
            ref: 'Item',
            default: null
        },
        shield: {
            type: mongoose.Schema.Types.ObjectId,
            ref: 'Item',
            default: null
        },
        helmet: {
            type: mongoose.Schema.Types.ObjectId,
            ref: 'Item',
            default: null
        },
        armor: {
            type: mongoose.Schema.Types.ObjectId,
            ref: 'Item',
            default: null
        },
        gloves: {
            type: mongoose.Schema.Types.ObjectId,
            ref: 'Item',
            default: null
        },
        boots: {
            type: mongoose.Schema.Types.ObjectId,
            ref: 'Item',
            default: null
        },
        ring1: {
            type: mongoose.Schema.Types.ObjectId,
            ref: 'Item',
            default: null
        },
        ring2: {
            type: mongoose.Schema.Types.ObjectId,
            ref: 'Item',
            default: null
        },
        necklace: {
            type: mongoose.Schema.Types.ObjectId,
            ref: 'Item',
            default: null
        },
        cape: {
            type: mongoose.Schema.Types.ObjectId,
            ref: 'Item',
            default: null
        }
    },
    
    // Inventory
    inventory: {
        slots: {
            type: Number,
            default: 50
        },
        items: [{
            itemId: {
                type: mongoose.Schema.Types.ObjectId,
                ref: 'Item'
            },
            quantity: {
                type: Number,
                default: 1,
                min: 1
            },
            slot: {
                type: Number,
                min: 0
            },
            durability: {
                type: Number,
                default: 100,
                min: 0,
                max: 100
            },
            enchantments: [{
                enchantmentId: String,
                level: Number,
                appliedAt: {
                    type: Date,
                    default: Date.now
                }
            }]
        }]
    },
    
    // Bank storage
    bank: {
        slots: {
            type: Number,
            default: 100
        },
        items: [{
            itemId: {
                type: mongoose.Schema.Types.ObjectId,
                ref: 'Item'
            },
            quantity: {
                type: Number,
                default: 1,
                min: 1
            },
            slot: {
                type: Number,
                min: 0
            }
        }]
    },
    
    // Quests
    activeQuests: [{
        questId: {
            type: String,
            required: true
        },
        progress: {
            type: Map,
            of: Number,
            default: {}
        },
        startedAt: {
            type: Date,
            default: Date.now
        },
        completedObjectives: [{
            objectiveId: String,
            completedAt: Date
        }]
    }],
    completedQuests: [{
        questId: String,
        completedAt: {
            type: Date,
            default: Date.now
        },
        rewards: {
            experience: Number,
            gold: Number,
            items: [String]
        }
    }],
    
    // Combat state
    combatState: {
        isInCombat: {
            type: Boolean,
            default: false
        },
        combatTarget: {
            type: String,
            default: null
        },
        lastCombatAction: {
            type: Date,
            default: null
        }
    },
    
    // Status effects
    statusEffects: [{
        effectId: {
            type: String,
            required: true
        },
        duration: {
            type: Number,
            required: true
        },
        startTime: {
            type: Date,
            default: Date.now
        },
        stacks: {
            type: Number,
            default: 1
        },
        source: {
            type: String,
            default: 'unknown'
        }
    }],
    
    // Cooldowns
    cooldowns: {
        type: Map,
        of: Date,
        default: {}
    },
    
    // PvP state
    pvpState: {
        isEnabled: {
            type: Boolean,
            default: true
        },
        kills: {
            type: Number,
            default: 0
        },
        deaths: {
            type: Number,
            default: 0
        },
        honor: {
            type: Number,
            default: 0
        },
        dishonorable: {
            type: Boolean,
            default: false
        },
        lastPvpAction: {
            type: Date,
            default: null
        }
    },
    
    // Character statistics
    statistics: {
        damageDealt: {
            type: Number,
            default: 0
        },
        damageReceived: {
            type: Number,
            default: 0
        },
        monstersKilled: {
            type: Number,
            default: 0
        },
        playersKilled: {
            type: Number,
            default: 0
        },
        deaths: {
            type: Number,
            default: 0
        },
        questsCompleted: {
            type: Number,
            default: 0
        },
        itemsCrafted: {
            type: Number,
            default: 0
        },
        distanceTraveled: {
            type: Number,
            default: 0
        },
        timePlayedOnCharacter: {
            type: Number,
            default: 0
        }
    },
    
    // Character state
    isAlive: {
        type: Boolean,
        default: true
    },
    deathTime: {
        type: Date,
        default: null
    },
    lastDeathLocation: {
        x: Number,
        y: Number,
        zone: String
    },
    
    // Respawn settings
    respawnLocation: {
        x: {
            type: Number,
            default: 0
        },
        y: {
            type: Number,
            default: 0
        },
        zone: {
            type: String,
            default: 'starter_town'
        }
    },
    
    // Crafting
    craftingState: {
        isActive: {
            type: Boolean,
            default: false
        },
        recipe: {
            type: String,
            default: null
        },
        startTime: {
            type: Date,
            default: null
        },
        endTime: {
            type: Date,
            default: null
        }
    },
    
    // Timestamps
    createdAt: {
        type: Date,
        default: Date.now
    },
    lastPlayed: {
        type: Date,
        default: Date.now
    },
    lastSaved: {
        type: Date,
        default: Date.now
    },
    
    // Character settings
    settings: {
        autoAttack: {
            type: Boolean,
            default: true
        },
        autoLoot: {
            type: Boolean,
            default: true
        },
        showHelmet: {
            type: Boolean,
            default: true
        },
        showCape: {
            type: Boolean,
            default: true
        }
    }
}, {
    timestamps: true,
    toJSON: { virtuals: true },
    toObject: { virtuals: true }
});

// Indexes for performance
characterSchema.index({ player: 1 });
characterSchema.index({ name: 1 });
characterSchema.index({ level: -1 });
characterSchema.index({ class: 1 });
characterSchema.index({ 'position.zone': 1 });
characterSchema.index({ player: 1, name: 1 }, { unique: true });

// Virtual for total attribute points
characterSchema.virtual('totalAttributes').get(function() {
    return this.attributes.strength + 
           this.attributes.agility + 
           this.attributes.intelligence + 
           this.attributes.vitality + 
           this.attributes.luck;
});

// Virtual for combat power
characterSchema.virtual('combatPower').get(function() {
    const base = this.level * 100;
    const attributeBonus = this.totalAttributes * 10;
    const equipmentBonus = 0; // Would calculate from equipment
    return base + attributeBonus + equipmentBonus;
});

// Virtual for health percentage
characterSchema.virtual('healthPercentage').get(function() {
    return this.maxHealth > 0 ? (this.health / this.maxHealth) * 100 : 0;
});

// Virtual for mana percentage
characterSchema.virtual('manaPercentage').get(function() {
    return this.maxMana > 0 ? (this.mana / this.maxMana) * 100 : 0;
});

// Virtual for stamina percentage
characterSchema.virtual('staminaPercentage').get(function() {
    return this.maxStamina > 0 ? (this.stamina / this.maxStamina) * 100 : 0;
});

// Virtual for kill/death ratio
characterSchema.virtual('kdRatio').get(function() {
    if (this.pvpState.deaths === 0) {
        return this.pvpState.kills;
    }
    return (this.pvpState.kills / this.pvpState.deaths).toFixed(2);
});

// Virtual for next level experience requirement
characterSchema.virtual('experienceToNextLevel').get(function() {
    if (this.level >= 1000) return 0;
    const baseExp = 1000;
    const nextLevelExp = Math.floor(baseExp * Math.pow(1.5, this.level - 1));
    return nextLevelExp - this.experience;
});

// Pre-save middleware
characterSchema.pre('save', function(next) {
    // Update lastSaved
    this.lastSaved = new Date();
    
    // Ensure health/mana/stamina don't exceed max values
    this.health = Math.min(this.health, this.maxHealth);
    this.mana = Math.min(this.mana, this.maxMana);
    this.stamina = Math.min(this.stamina, this.maxStamina);
    
    // Update max values based on attributes and level
    this.updateMaxValues();
    
    next();
});

// Methods
characterSchema.methods.updateMaxValues = function() {
    // Calculate max health based on vitality and level
    const baseHealth = 100;
    const healthPerVitality = 10;
    const healthPerLevel = 5;
    this.maxHealth = baseHealth + (this.attributes.vitality * healthPerVitality) + (this.level * healthPerLevel);
    
    // Calculate max mana based on intelligence and level
    const baseMana = 50;
    const manaPerIntelligence = 5;
    const manaPerLevel = 3;
    this.maxMana = baseMana + (this.attributes.intelligence * manaPerIntelligence) + (this.level * manaPerLevel);
    
    // Calculate max stamina based on agility and level
    const baseStamina = 100;
    const staminaPerAgility = 2;
    const staminaPerLevel = 2;
    this.maxStamina = baseStamina + (this.attributes.agility * staminaPerAgility) + (this.level * staminaPerLevel);
};

characterSchema.methods.heal = function(amount) {
    this.health = Math.min(this.maxHealth, this.health + amount);
    return this.save();
};

characterSchema.methods.damage = function(amount) {
    this.health = Math.max(0, this.health - amount);
    if (this.health === 0) {
        this.die();
    }
    return this.save();
};

characterSchema.methods.restoreMana = function(amount) {
    this.mana = Math.min(this.maxMana, this.mana + amount);
    return this.save();
};

characterSchema.methods.consumeMana = function(amount) {
    if (this.mana < amount) {
        throw new Error('Not enough mana');
    }
    this.mana -= amount;
    return this.save();
};

characterSchema.methods.restoreStamina = function(amount) {
    this.stamina = Math.min(this.maxStamina, this.stamina + amount);
    return this.save();
};

characterSchema.methods.consumeStamina = function(amount) {
    if (this.stamina < amount) {
        throw new Error('Not enough stamina');
    }
    this.stamina -= amount;
    return this.save();
};

characterSchema.methods.addExperience = function(amount) {
    this.experience += amount;
    
    // Check for level up
    const baseExp = 1000;
    let leveledUp = false;
    
    while (this.level < 1000) {
        const nextLevelExp = Math.floor(baseExp * Math.pow(1.5, this.level - 1));
        if (this.experience >= nextLevelExp) {
            this.level++;
            this.talentPoints += 1;
            leveledUp = true;
            
            // Restore health/mana/stamina on level up
            this.updateMaxValues();
            this.health = this.maxHealth;
            this.mana = this.maxMana;
            this.stamina = this.maxStamina;
        } else {
            break;
        }
    }
    
    return { leveledUp, newLevel: this.level };
};

characterSchema.methods.die = function() {
    this.isAlive = false;
    this.deathTime = new Date();
    this.lastDeathLocation = { ...this.position };
    this.health = 0;
    this.pvpState.deaths++;
    this.statistics.deaths++;
    
    // Remove some buffs on death
    this.statusEffects = this.statusEffects.filter(effect => 
        !['buff', 'temporary'].includes(effect.effectId)
    );
    
    return this.save();
};

characterSchema.methods.respawn = function(location = null) {
    this.isAlive = true;
    this.deathTime = null;
    this.health = Math.floor(this.maxHealth * 0.25); // Respawn with 25% health
    this.mana = Math.floor(this.maxMana * 0.25);
    this.stamina = this.maxStamina;
    
    // Set position
    if (location) {
        this.position = location;
    } else {
        this.position = { ...this.respawnLocation };
    }
    
    return this.save();
};

characterSchema.methods.addSkill = function(skillId, level = 1) {
    const existingSkill = this.skills.find(s => s.skillId === skillId);
    if (!existingSkill) {
        this.skills.push({
            skillId,
            level,
            experience: 0,
            learnedAt: new Date()
        });
    }
    return this.save();
};

characterSchema.methods.upgradeSkill = function(skillId, experience) {
    const skill = this.skills.find(s => s.skillId === skillId);
    if (skill) {
        skill.experience += experience;
        
        // Check for skill level up
        const expRequired = skill.level * 100;
        if (skill.experience >= expRequired && skill.level < 100) {
            skill.level++;
            skill.experience = 0;
        }
    }
    return this.save();
};

characterSchema.methods.addStatusEffect = function(effectId, duration, stacks = 1, source = 'unknown') {
    const existingEffect = this.statusEffects.find(e => e.effectId === effectId);
    
    if (existingEffect) {
        // Refresh duration and add stacks
        existingEffect.duration = duration;
        existingEffect.startTime = new Date();
        existingEffect.stacks = Math.min(10, existingEffect.stacks + stacks);
    } else {
        this.statusEffects.push({
            effectId,
            duration,
            startTime: new Date(),
            stacks,
            source
        });
    }
    
    return this.save();
};

characterSchema.methods.removeStatusEffect = function(effectId) {
    this.statusEffects = this.statusEffects.filter(e => e.effectId !== effectId);
    return this.save();
};

characterSchema.methods.cleanupExpiredEffects = function() {
    const now = new Date();
    this.statusEffects = this.statusEffects.filter(effect => {
        const endTime = new Date(effect.startTime.getTime() + effect.duration);
        return endTime > now;
    });
    return this.save();
};

characterSchema.methods.setCooldown = function(skillId, duration) {
    const endTime = new Date(Date.now() + duration);
    this.cooldowns.set(skillId, endTime);
    return this.save();
};

characterSchema.methods.isOnCooldown = function(skillId) {
    const cooldownEnd = this.cooldowns.get(skillId);
    return cooldownEnd && cooldownEnd > new Date();
};

characterSchema.methods.getCooldownRemaining = function(skillId) {
    const cooldownEnd = this.cooldowns.get(skillId);
    if (!cooldownEnd || cooldownEnd <= new Date()) {
        return 0;
    }
    return cooldownEnd.getTime() - Date.now();
};

characterSchema.methods.addQuest = function(questId) {
    const existingQuest = this.activeQuests.find(q => q.questId === questId);
    if (!existingQuest) {
        this.activeQuests.push({
            questId,
            progress: new Map(),
            startedAt: new Date(),
            completedObjectives: []
        });
    }
    return this.save();
};

characterSchema.methods.updateQuestProgress = function(questId, objectiveId, progress) {
    const quest = this.activeQuests.find(q => q.questId === questId);
    if (quest) {
        quest.progress.set(objectiveId, progress);
    }
    return this.save();
};

characterSchema.methods.completeQuest = function(questId, rewards) {
    // Remove from active quests
    this.activeQuests = this.activeQuests.filter(q => q.questId !== questId);
    
    // Add to completed quests
    this.completedQuests.push({
        questId,
        completedAt: new Date(),
        rewards
    });
    
    // Apply rewards
    if (rewards.experience) {
        this.addExperience(rewards.experience);
    }
    
    this.statistics.questsCompleted++;
    
    return this.save();
};

characterSchema.methods.addItem = function(itemId, quantity = 1, slot = null) {
    // Find empty slot or existing item
    let targetSlot = slot;
    
    if (!targetSlot) {
        // Try to stack with existing item
        const existingItem = this.inventory.items.find(item => 
            item.itemId.equals(itemId) && item.quantity < 99
        );
        
        if (existingItem) {
            existingItem.quantity = Math.min(99, existingItem.quantity + quantity);
            return this.save();
        }
        
        // Find empty slot
        const usedSlots = this.inventory.items.map(item => item.slot);
        for (let i = 0; i < this.inventory.slots; i++) {
            if (!usedSlots.includes(i)) {
                targetSlot = i;
                break;
            }
        }
    }
    
    if (targetSlot !== null && targetSlot < this.inventory.slots) {
        this.inventory.items.push({
            itemId,
            quantity,
            slot: targetSlot,
            durability: 100
        });
        return this.save();
    }
    
    throw new Error('Inventory full');
};

characterSchema.methods.removeItem = function(itemId, quantity = 1) {
    const item = this.inventory.items.find(item => item.itemId.equals(itemId));
    if (!item) {
        throw new Error('Item not found');
    }
    
    if (item.quantity <= quantity) {
        this.inventory.items = this.inventory.items.filter(item => !item.itemId.equals(itemId));
    } else {
        item.quantity -= quantity;
    }
    
    return this.save();
};

characterSchema.methods.equipItem = function(itemId, slot) {
    // Remove from inventory
    const inventoryItem = this.inventory.items.find(item => item.itemId.equals(itemId));
    if (!inventoryItem) {
        throw new Error('Item not found in inventory');
    }
    
    // Unequip current item in slot if exists
    if (this.equipment[slot]) {
        this.addItem(this.equipment[slot], 1);
    }
    
    // Equip new item
    this.equipment[slot] = itemId;
    
    // Remove from inventory
    this.removeItem(itemId, 1);
    
    return this.save();
};

characterSchema.methods.unequipItem = function(slot) {
    if (!this.equipment[slot]) {
        throw new Error('No item equipped in slot');
    }
    
    const itemId = this.equipment[slot];
    this.equipment[slot] = null;
    this.addItem(itemId, 1);
    
    return this.save();
};

characterSchema.methods.teleport = function(x, y, zone) {
    this.lastPosition = { ...this.position };
    this.position = { x, y, zone };
    return this.save();
};

characterSchema.methods.updateLastPlayed = function() {
    this.lastPlayed = new Date();
    return this.save();
};

// Static methods
characterSchema.statics.findByPlayerAndName = function(playerId, name) {
    return this.findOne({ player: playerId, name: new RegExp(`^${name}$`, 'i') });
};

characterSchema.statics.findByPlayer = function(playerId) {
    return this.find({ player: playerId }).sort({ lastPlayed: -1 });
};

characterSchema.statics.findInZone = function(zone) {
    return this.find({ 
        'position.zone': zone,
        isAlive: true
    }).populate('player', 'username');
};

characterSchema.statics.getTopCharacters = function(limit = 10) {
    return this.find({ isAlive: true })
        .sort({ level: -1, experience: -1 })
        .limit(limit)
        .populate('player', 'username')
        .select('name class level experience statistics player');
};

characterSchema.statics.searchCharacters = function(query, limit = 20) {
    return this.find({
        name: new RegExp(query, 'i'),
        isAlive: true
    })
    .limit(limit)
    .populate('player', 'username')
    .select('name class level player');
};

module.exports = mongoose.model('Character', characterSchema);