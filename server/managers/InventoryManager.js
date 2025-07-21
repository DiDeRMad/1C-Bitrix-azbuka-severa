const EventEmitter = require('events');
const { v4: uuidv4 } = require('uuid');
const _ = require('lodash');

const INVENTORY_CONFIG = {
    DEFAULT_INVENTORY_SIZE: 30,
    DEFAULT_BANK_SIZE: 50,
    MAX_INVENTORY_SIZE: 100,
    MAX_BANK_SIZE: 200,
    EQUIPMENT_SLOTS: {
        HEAD: 'head',
        CHEST: 'chest',
        LEGS: 'legs',
        FEET: 'feet',
        HANDS: 'hands',
        MAIN_HAND: 'main_hand',
        OFF_HAND: 'off_hand',
        RING_1: 'ring_1',
        RING_2: 'ring_2',
        NECKLACE: 'necklace',
        TRINKET_1: 'trinket_1',
        TRINKET_2: 'trinket_2'
    },
    ITEM_TYPES: {
        WEAPON: 'weapon',
        ARMOR: 'armor',
        ACCESSORY: 'accessory',
        CONSUMABLE: 'consumable',
        MATERIAL: 'material',
        TOOL: 'tool',
        QUEST: 'quest',
        CONTAINER: 'container',
        BOOK: 'book',
        KEY: 'key'
    },
    ITEM_RARITIES: {
        POOR: 1,
        COMMON: 2,
        UNCOMMON: 3,
        RARE: 4,
        EPIC: 5,
        LEGENDARY: 6,
        ARTIFACT: 7
    },
    SORT_TYPES: {
        NAME: 'name',
        TYPE: 'type',
        RARITY: 'rarity',
        VALUE: 'value',
        LEVEL: 'level',
        STACK_SIZE: 'stack_size'
    },
    MAX_STACK_SIZE: 999
};

class InventoryManager extends EventEmitter {
    constructor(gameEngine) {
        super();
        this.gameEngine = gameEngine;
        this.playerInventories = new Map(); // playerId -> inventory
        this.playerBanks = new Map(); // playerId -> bank
        this.playerEquipment = new Map(); // playerId -> equipment
        this.itemTemplates = new Map(); // itemId -> template
        this.itemInstances = new Map(); // instanceId -> item instance
        
        this.initializeItemTemplates();
        this.setupEventListeners();
    }

    initializeItemTemplates() {
        // Weapons
        this.itemTemplates.set('starter_sword', {
            id: 'starter_sword',
            name: 'Starter Sword',
            description: 'A basic sword for new adventurers.',
            type: INVENTORY_CONFIG.ITEM_TYPES.WEAPON,
            subtype: 'sword',
            rarity: INVENTORY_CONFIG.ITEM_RARITIES.COMMON,
            level: 1,
            value: 10,
            stackSize: 1,
            equipSlot: INVENTORY_CONFIG.EQUIPMENT_SLOTS.MAIN_HAND,
            stats: {
                damage: 10,
                attackSpeed: 1.2,
                durability: 100,
                maxDurability: 100
            },
            requirements: {
                level: 1,
                class: null,
                stats: { strength: 5 }
            },
            effects: [],
            enchantments: []
        });

        this.itemTemplates.set('iron_sword', {
            id: 'iron_sword',
            name: 'Iron Sword',
            description: 'A sturdy iron sword.',
            type: INVENTORY_CONFIG.ITEM_TYPES.WEAPON,
            subtype: 'sword',
            rarity: INVENTORY_CONFIG.ITEM_RARITIES.COMMON,
            level: 5,
            value: 50,
            stackSize: 1,
            equipSlot: INVENTORY_CONFIG.EQUIPMENT_SLOTS.MAIN_HAND,
            stats: {
                damage: 25,
                attackSpeed: 1.2,
                durability: 200,
                maxDurability: 200
            },
            requirements: {
                level: 5,
                class: null,
                stats: { strength: 15 }
            },
            effects: [],
            enchantments: []
        });

        // Armor
        this.itemTemplates.set('leather_armor', {
            id: 'leather_armor',
            name: 'Leather Armor',
            description: 'Basic leather protection.',
            type: INVENTORY_CONFIG.ITEM_TYPES.ARMOR,
            subtype: 'light_armor',
            rarity: INVENTORY_CONFIG.ITEM_RARITIES.COMMON,
            level: 2,
            value: 25,
            stackSize: 1,
            equipSlot: INVENTORY_CONFIG.EQUIPMENT_SLOTS.CHEST,
            stats: {
                armor: 15,
                durability: 150,
                maxDurability: 150
            },
            requirements: {
                level: 2,
                class: null,
                stats: {}
            },
            effects: [],
            enchantments: []
        });

        this.itemTemplates.set('steel_armor', {
            id: 'steel_armor',
            name: 'Steel Armor',
            description: 'Heavy steel protection for warriors.',
            type: INVENTORY_CONFIG.ITEM_TYPES.ARMOR,
            subtype: 'heavy_armor',
            rarity: INVENTORY_CONFIG.ITEM_RARITIES.UNCOMMON,
            level: 15,
            value: 200,
            stackSize: 1,
            equipSlot: INVENTORY_CONFIG.EQUIPMENT_SLOTS.CHEST,
            stats: {
                armor: 50,
                durability: 400,
                maxDurability: 400
            },
            requirements: {
                level: 15,
                class: ['warrior', 'paladin'],
                stats: { strength: 30 }
            },
            effects: [],
            enchantments: []
        });

        // Consumables
        this.itemTemplates.set('health_potion', {
            id: 'health_potion',
            name: 'Health Potion',
            description: 'Restores health when consumed.',
            type: INVENTORY_CONFIG.ITEM_TYPES.CONSUMABLE,
            subtype: 'potion',
            rarity: INVENTORY_CONFIG.ITEM_RARITIES.COMMON,
            level: 1,
            value: 5,
            stackSize: 50,
            effects: [
                {
                    type: 'heal',
                    value: 50,
                    duration: 0 // Instant
                }
            ],
            cooldown: 5000 // 5 seconds
        });

        this.itemTemplates.set('bread', {
            id: 'bread',
            name: 'Fresh Bread',
            description: 'Nourishing bread that restores hunger.',
            type: INVENTORY_CONFIG.ITEM_TYPES.CONSUMABLE,
            subtype: 'food',
            rarity: INVENTORY_CONFIG.ITEM_RARITIES.POOR,
            level: 1,
            value: 2,
            stackSize: 20,
            effects: [
                {
                    type: 'restore_hunger',
                    value: 25,
                    duration: 0
                }
            ]
        });

        // Materials
        this.itemTemplates.set('iron_ingot', {
            id: 'iron_ingot',
            name: 'Iron Ingot',
            description: 'Refined iron suitable for crafting.',
            type: INVENTORY_CONFIG.ITEM_TYPES.MATERIAL,
            subtype: 'metal',
            rarity: INVENTORY_CONFIG.ITEM_RARITIES.COMMON,
            level: 1,
            value: 8,
            stackSize: 100
        });

        this.itemTemplates.set('red_herb', {
            id: 'red_herb',
            name: 'Red Herb',
            description: 'A crimson herb with healing properties.',
            type: INVENTORY_CONFIG.ITEM_TYPES.MATERIAL,
            subtype: 'herb',
            rarity: INVENTORY_CONFIG.ITEM_RARITIES.COMMON,
            level: 1,
            value: 3,
            stackSize: 200
        });

        // Tools
        this.itemTemplates.set('gathering_tool', {
            id: 'gathering_tool',
            name: 'Basic Gathering Tool',
            description: 'A simple tool for gathering resources.',
            type: INVENTORY_CONFIG.ITEM_TYPES.TOOL,
            subtype: 'gathering',
            rarity: INVENTORY_CONFIG.ITEM_RARITIES.COMMON,
            level: 1,
            value: 15,
            stackSize: 1,
            stats: {
                gatheringSpeed: 1.2,
                durability: 100,
                maxDurability: 100
            }
        });

        // Accessories
        this.itemTemplates.set('champion_ring', {
            id: 'champion_ring',
            name: 'Champion\'s Ring',
            description: 'A ring that marks a true champion.',
            type: INVENTORY_CONFIG.ITEM_TYPES.ACCESSORY,
            subtype: 'ring',
            rarity: INVENTORY_CONFIG.ITEM_RARITIES.RARE,
            level: 20,
            value: 500,
            stackSize: 1,
            equipSlot: INVENTORY_CONFIG.EQUIPMENT_SLOTS.RING_1,
            stats: {
                strength: 5,
                agility: 5,
                intellect: 5
            },
            requirements: {
                level: 20,
                class: null,
                stats: {}
            },
            effects: [
                {
                    type: 'stat_bonus',
                    stat: 'experience_gain',
                    value: 10,
                    duration: -1 // Permanent while equipped
                }
            ]
        });
    }

    setupEventListeners() {
        this.gameEngine.on('playerConnected', this.handlePlayerConnected.bind(this));
        this.gameEngine.on('playerLevelUp', this.handlePlayerLevelUp.bind(this));
    }

    // Initialize player inventory
    initializePlayerInventory(playerId) {
        if (!this.playerInventories.has(playerId)) {
            this.playerInventories.set(playerId, {
                items: [],
                size: INVENTORY_CONFIG.DEFAULT_INVENTORY_SIZE,
                lastSorted: null,
                filters: {}
            });
        }

        if (!this.playerBanks.has(playerId)) {
            this.playerBanks.set(playerId, {
                items: [],
                size: INVENTORY_CONFIG.DEFAULT_BANK_SIZE,
                tabs: {
                    general: { name: 'General', items: [] },
                    equipment: { name: 'Equipment', items: [] },
                    materials: { name: 'Materials', items: [] }
                }
            });
        }

        if (!this.playerEquipment.has(playerId)) {
            const equipment = {};
            for (const slot of Object.values(INVENTORY_CONFIG.EQUIPMENT_SLOTS)) {
                equipment[slot] = null;
            }
            this.playerEquipment.set(playerId, equipment);
        }
    }

    // Item Instance Creation
    createItemInstance(templateId, quantity = 1, quality = null, customProperties = {}) {
        const template = this.itemTemplates.get(templateId);
        if (!template) {
            throw new Error('Item template not found');
        }

        const instanceId = uuidv4();
        const instance = {
            id: instanceId,
            templateId,
            quantity: Math.min(quantity, template.stackSize),
            quality: quality || template.rarity,
            createdAt: new Date(),
            customProperties: { ...customProperties },
            enchantments: [],
            durability: template.stats?.maxDurability || null,
            isLocked: false,
            isSoulbound: false
        };

        this.itemInstances.set(instanceId, instance);
        return instance;
    }

    // Add item to inventory
    async addItem(playerId, templateId, quantity = 1, quality = null, toBank = false) {
        this.initializePlayerInventory(playerId);
        
        const template = this.itemTemplates.get(templateId);
        if (!template) {
            throw new Error('Item template not found');
        }

        const inventory = toBank ? this.playerBanks.get(playerId) : this.playerInventories.get(playerId);
        let remainingQuantity = quantity;

        // Try to stack with existing items first
        if (template.stackSize > 1) {
            for (const item of inventory.items) {
                if (item.templateId === templateId && 
                    item.quantity < template.stackSize &&
                    item.quality === (quality || template.rarity)) {
                    
                    const canAdd = Math.min(remainingQuantity, template.stackSize - item.quantity);
                    item.quantity += canAdd;
                    remainingQuantity -= canAdd;
                    
                    if (remainingQuantity <= 0) break;
                }
            }
        }

        // Create new stacks for remaining quantity
        while (remainingQuantity > 0) {
            const stackSize = Math.min(remainingQuantity, template.stackSize);
            
            // Check if inventory has space
            if (inventory.items.length >= inventory.size) {
                throw new Error('Inventory is full');
            }

            const instance = this.createItemInstance(templateId, stackSize, quality);
            inventory.items.push(instance);
            remainingQuantity -= stackSize;
        }

        this.emit('itemAdded', { playerId, templateId, quantity, toBank });
        return true;
    }

    // Remove item from inventory
    async removeItem(playerId, templateId, quantity = 1, fromBank = false) {
        this.initializePlayerInventory(playerId);
        
        const inventory = fromBank ? this.playerBanks.get(playerId) : this.playerInventories.get(playerId);
        let remainingQuantity = quantity;

        // Find items to remove (LIFO - last in, first out)
        for (let i = inventory.items.length - 1; i >= 0 && remainingQuantity > 0; i--) {
            const item = inventory.items[i];
            
            if (item.templateId === templateId && !item.isLocked) {
                const canRemove = Math.min(remainingQuantity, item.quantity);
                item.quantity -= canRemove;
                remainingQuantity -= canRemove;
                
                if (item.quantity <= 0) {
                    inventory.items.splice(i, 1);
                    this.itemInstances.delete(item.id);
                }
            }
        }

        if (remainingQuantity > 0) {
            throw new Error('Insufficient items to remove');
        }

        this.emit('itemRemoved', { playerId, templateId, quantity, fromBank });
        return true;
    }

    // Get item quantity
    getItemQuantity(playerId, templateId, fromBank = false) {
        this.initializePlayerInventory(playerId);
        
        const inventory = fromBank ? this.playerBanks.get(playerId) : this.playerInventories.get(playerId);
        
        return inventory.items
            .filter(item => item.templateId === templateId)
            .reduce((total, item) => total + item.quantity, 0);
    }

    // Get specific item instance
    getItem(playerId, instanceId) {
        this.initializePlayerInventory(playerId);
        
        const inventory = this.playerInventories.get(playerId);
        const bank = this.playerBanks.get(playerId);
        
        let item = inventory.items.find(item => item.id === instanceId);
        if (!item) {
            item = bank.items.find(item => item.id === instanceId);
        }
        
        return item;
    }

    // Equipment Management
    async equipItem(playerId, instanceId) {
        this.initializePlayerInventory(playerId);
        
        const inventory = this.playerInventories.get(playerId);
        const equipment = this.playerEquipment.get(playerId);
        
        const itemInstance = inventory.items.find(item => item.id === instanceId);
        if (!itemInstance) {
            throw new Error('Item not found in inventory');
        }

        const template = this.itemTemplates.get(itemInstance.templateId);
        if (!template.equipSlot) {
            throw new Error('Item is not equippable');
        }

        // Check requirements
        const player = await this.gameEngine.getPlayer(playerId);
        if (!this.checkItemRequirements(player, template)) {
            throw new Error('Player does not meet item requirements');
        }

        // Unequip current item in slot if exists
        const currentEquipped = equipment[template.equipSlot];
        if (currentEquipped) {
            await this.unequipItem(playerId, template.equipSlot);
        }

        // Move item from inventory to equipment
        const itemIndex = inventory.items.indexOf(itemInstance);
        inventory.items.splice(itemIndex, 1);
        equipment[template.equipSlot] = itemInstance;

        // Apply item effects
        await this.applyItemEffects(playerId, template, true);

        this.emit('itemEquipped', { playerId, instanceId, slot: template.equipSlot });
        return true;
    }

    async unequipItem(playerId, slot) {
        this.initializePlayerInventory(playerId);
        
        const inventory = this.playerInventories.get(playerId);
        const equipment = this.playerEquipment.get(playerId);
        
        const equippedItem = equipment[slot];
        if (!equippedItem) {
            throw new Error('No item equipped in that slot');
        }

        // Check if inventory has space
        if (inventory.items.length >= inventory.size) {
            throw new Error('Inventory is full');
        }

        const template = this.itemTemplates.get(equippedItem.templateId);
        
        // Remove item effects
        await this.applyItemEffects(playerId, template, false);

        // Move item from equipment to inventory
        equipment[slot] = null;
        inventory.items.push(equippedItem);

        this.emit('itemUnequipped', { playerId, instanceId: equippedItem.id, slot });
        return true;
    }

    checkItemRequirements(player, template) {
        if (template.requirements) {
            // Check level requirement
            if (template.requirements.level && player.level < template.requirements.level) {
                return false;
            }

            // Check class requirement
            if (template.requirements.class && template.requirements.class.length > 0) {
                if (!template.requirements.class.includes(player.class)) {
                    return false;
                }
            }

            // Check stat requirements
            if (template.requirements.stats) {
                for (const [stat, required] of Object.entries(template.requirements.stats)) {
                    if (player.stats[stat] < required) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    async applyItemEffects(playerId, template, equipping) {
        // This would integrate with the player stats system
        // For now, just emit an event that other systems can listen to
        this.emit('itemEffectsChanged', {
            playerId,
            template,
            equipping,
            effects: template.effects || []
        });
    }

    // Inventory Organization
    sortInventory(playerId, sortType = INVENTORY_CONFIG.SORT_TYPES.TYPE, ascending = true) {
        this.initializePlayerInventory(playerId);
        
        const inventory = this.playerInventories.get(playerId);
        
        inventory.items.sort((a, b) => {
            const templateA = this.itemTemplates.get(a.templateId);
            const templateB = this.itemTemplates.get(b.templateId);
            
            let comparison = 0;
            
            switch (sortType) {
                case INVENTORY_CONFIG.SORT_TYPES.NAME:
                    comparison = templateA.name.localeCompare(templateB.name);
                    break;
                case INVENTORY_CONFIG.SORT_TYPES.TYPE:
                    comparison = templateA.type.localeCompare(templateB.type) ||
                               templateA.name.localeCompare(templateB.name);
                    break;
                case INVENTORY_CONFIG.SORT_TYPES.RARITY:
                    comparison = templateB.rarity - templateA.rarity ||
                               templateA.name.localeCompare(templateB.name);
                    break;
                case INVENTORY_CONFIG.SORT_TYPES.VALUE:
                    comparison = (templateB.value * b.quantity) - (templateA.value * a.quantity);
                    break;
                case INVENTORY_CONFIG.SORT_TYPES.LEVEL:
                    comparison = templateB.level - templateA.level ||
                               templateA.name.localeCompare(templateB.name);
                    break;
                case INVENTORY_CONFIG.SORT_TYPES.STACK_SIZE:
                    comparison = b.quantity - a.quantity ||
                               templateA.name.localeCompare(templateB.name);
                    break;
            }
            
            return ascending ? comparison : -comparison;
        });

        inventory.lastSorted = {
            type: sortType,
            ascending,
            timestamp: new Date()
        };

        this.emit('inventorySorted', { playerId, sortType, ascending });
        return inventory.items;
    }

    // Item Search
    searchInventory(playerId, query, includeBank = false) {
        this.initializePlayerInventory(playerId);
        
        const inventory = this.playerInventories.get(playerId);
        const bank = includeBank ? this.playerBanks.get(playerId) : null;
        
        const searchTerm = query.toLowerCase();
        const results = [];
        
        const searchItems = (items, location) => {
            return items.filter(item => {
                const template = this.itemTemplates.get(item.templateId);
                return template.name.toLowerCase().includes(searchTerm) ||
                       template.description.toLowerCase().includes(searchTerm) ||
                       template.type.toLowerCase().includes(searchTerm);
            }).map(item => ({ ...item, location }));
        };

        results.push(...searchItems(inventory.items, 'inventory'));
        
        if (bank) {
            results.push(...searchItems(bank.items, 'bank'));
        }

        return results;
    }

    // Item Enhancement
    async enchantItem(playerId, instanceId, enchantment) {
        const item = this.getItem(playerId, instanceId);
        if (!item) {
            throw new Error('Item not found');
        }

        const template = this.itemTemplates.get(item.templateId);
        if (template.type !== INVENTORY_CONFIG.ITEM_TYPES.WEAPON && 
            template.type !== INVENTORY_CONFIG.ITEM_TYPES.ARMOR) {
            throw new Error('Item cannot be enchanted');
        }

        // Check if item already has this enchantment type
        const existingEnchantment = item.enchantments.find(e => e.type === enchantment.type);
        if (existingEnchantment) {
            // Upgrade existing enchantment
            existingEnchantment.power = enchantment.power;
            existingEnchantment.source = enchantment.source;
        } else {
            // Add new enchantment
            item.enchantments.push({
                ...enchantment,
                id: uuidv4(),
                appliedAt: new Date()
            });
        }

        this.emit('itemEnchanted', { playerId, instanceId, enchantment });
        return item;
    }

    // Item Repair
    async repairItem(playerId, instanceId, repairCost) {
        const item = this.getItem(playerId, instanceId);
        if (!item) {
            throw new Error('Item not found');
        }

        if (item.durability === null) {
            throw new Error('Item cannot be repaired');
        }

        const template = this.itemTemplates.get(item.templateId);
        if (item.durability >= template.stats.maxDurability) {
            throw new Error('Item is already at full durability');
        }

        // Check if player has enough gold
        const hasGold = await this.gameEngine.economyManager.hasGold(playerId, repairCost);
        if (!hasGold) {
            throw new Error('Insufficient gold for repair');
        }

        await this.gameEngine.economyManager.deductGold(playerId, repairCost);
        item.durability = template.stats.maxDurability;

        this.emit('itemRepaired', { playerId, instanceId, cost: repairCost });
        return item;
    }

    // Item Durability Management
    damageItem(playerId, instanceId, damage) {
        const item = this.getItem(playerId, instanceId);
        if (!item || item.durability === null) {
            return false;
        }

        item.durability = Math.max(0, item.durability - damage);
        
        if (item.durability === 0) {
            this.emit('itemBroken', { playerId, instanceId });
        }

        return item;
    }

    // Bank Management
    async depositToBank(playerId, instanceId, quantity = null) {
        this.initializePlayerInventory(playerId);
        
        const inventory = this.playerInventories.get(playerId);
        const bank = this.playerBanks.get(playerId);
        
        const item = inventory.items.find(item => item.id === instanceId);
        if (!item) {
            throw new Error('Item not found in inventory');
        }

        const template = this.itemTemplates.get(item.templateId);
        const depositQuantity = quantity || item.quantity;

        if (depositQuantity > item.quantity) {
            throw new Error('Cannot deposit more than available');
        }

        // Try to stack with existing bank items
        let remainingQuantity = depositQuantity;
        
        if (template.stackSize > 1) {
            for (const bankItem of bank.items) {
                if (bankItem.templateId === item.templateId && 
                    bankItem.quantity < template.stackSize &&
                    bankItem.quality === item.quality) {
                    
                    const canAdd = Math.min(remainingQuantity, template.stackSize - bankItem.quantity);
                    bankItem.quantity += canAdd;
                    remainingQuantity -= canAdd;
                    
                    if (remainingQuantity <= 0) break;
                }
            }
        }

        // Create new bank item if needed
        if (remainingQuantity > 0) {
            if (bank.items.length >= bank.size) {
                throw new Error('Bank is full');
            }

            const newBankItem = this.createItemInstance(
                item.templateId, 
                remainingQuantity, 
                item.quality
            );
            bank.items.push(newBankItem);
        }

        // Update or remove inventory item
        item.quantity -= depositQuantity;
        if (item.quantity <= 0) {
            const itemIndex = inventory.items.indexOf(item);
            inventory.items.splice(itemIndex, 1);
            this.itemInstances.delete(item.id);
        }

        this.emit('itemDeposited', { playerId, templateId: item.templateId, quantity: depositQuantity });
        return true;
    }

    async withdrawFromBank(playerId, instanceId, quantity = null) {
        this.initializePlayerInventory(playerId);
        
        const inventory = this.playerInventories.get(playerId);
        const bank = this.playerBanks.get(playerId);
        
        const item = bank.items.find(item => item.id === instanceId);
        if (!item) {
            throw new Error('Item not found in bank');
        }

        const withdrawQuantity = quantity || item.quantity;

        if (withdrawQuantity > item.quantity) {
            throw new Error('Cannot withdraw more than available');
        }

        // Add to inventory using existing method
        await this.addItem(playerId, item.templateId, withdrawQuantity, item.quality);

        // Update or remove bank item
        item.quantity -= withdrawQuantity;
        if (item.quantity <= 0) {
            const itemIndex = bank.items.indexOf(item);
            bank.items.splice(itemIndex, 1);
            this.itemInstances.delete(item.id);
        }

        this.emit('itemWithdrawn', { playerId, templateId: item.templateId, quantity: withdrawQuantity });
        return true;
    }

    // Item Use/Consumption
    async useItem(playerId, instanceId, targetId = null) {
        const item = this.getItem(playerId, instanceId);
        if (!item) {
            throw new Error('Item not found');
        }

        const template = this.itemTemplates.get(item.templateId);
        
        if (template.type !== INVENTORY_CONFIG.ITEM_TYPES.CONSUMABLE) {
            throw new Error('Item is not consumable');
        }

        // Check cooldown if applicable
        if (template.cooldown) {
            const player = await this.gameEngine.getPlayer(playerId);
            const lastUsed = player.lastItemUse?.[template.id] || 0;
            const now = Date.now();
            
            if (now - lastUsed < template.cooldown) {
                throw new Error('Item is on cooldown');
            }
        }

        // Apply item effects
        for (const effect of template.effects || []) {
            await this.applyConsumableEffect(playerId, effect, targetId);
        }

        // Update cooldown
        if (template.cooldown) {
            const player = await this.gameEngine.getPlayer(playerId);
            if (!player.lastItemUse) player.lastItemUse = {};
            player.lastItemUse[template.id] = Date.now();
        }

        // Consume the item
        await this.removeItem(playerId, template.id, 1);

        this.emit('itemUsed', { playerId, templateId: template.id, targetId });
        return true;
    }

    async applyConsumableEffect(playerId, effect, targetId) {
        switch (effect.type) {
            case 'heal':
                await this.gameEngine.playerManager.healPlayer(playerId, effect.value);
                break;
            case 'restore_mana':
                await this.gameEngine.playerManager.restoreMana(playerId, effect.value);
                break;
            case 'restore_hunger':
                await this.gameEngine.playerManager.restoreHunger(playerId, effect.value);
                break;
            case 'buff':
                await this.gameEngine.playerManager.applyBuff(playerId, effect);
                break;
            default:
                console.log(`Unknown consumable effect type: ${effect.type}`);
        }
    }

    // Event Handlers
    handlePlayerConnected(data) {
        const { playerId } = data;
        this.initializePlayerInventory(playerId);
    }

    handlePlayerLevelUp(data) {
        const { playerId, newLevel } = data;
        
        // Possibly expand inventory size based on level
        if (newLevel % 10 === 0) { // Every 10 levels
            this.expandInventory(playerId, 5);
        }
    }

    expandInventory(playerId, additionalSlots) {
        this.initializePlayerInventory(playerId);
        
        const inventory = this.playerInventories.get(playerId);
        const newSize = Math.min(
            inventory.size + additionalSlots,
            INVENTORY_CONFIG.MAX_INVENTORY_SIZE
        );
        
        inventory.size = newSize;
        
        this.emit('inventoryExpanded', { playerId, newSize, additionalSlots });
        return newSize;
    }

    // Public API Methods
    getInventory(playerId) {
        this.initializePlayerInventory(playerId);
        
        const inventory = this.playerInventories.get(playerId);
        return {
            ...inventory,
            items: inventory.items.map(item => ({
                ...item,
                template: this.itemTemplates.get(item.templateId)
            }))
        };
    }

    getBank(playerId) {
        this.initializePlayerInventory(playerId);
        
        const bank = this.playerBanks.get(playerId);
        return {
            ...bank,
            items: bank.items.map(item => ({
                ...item,
                template: this.itemTemplates.get(item.templateId)
            }))
        };
    }

    getEquipment(playerId) {
        this.initializePlayerInventory(playerId);
        
        const equipment = this.playerEquipment.get(playerId);
        const result = {};
        
        for (const [slot, item] of Object.entries(equipment)) {
            result[slot] = item ? {
                ...item,
                template: this.itemTemplates.get(item.templateId)
            } : null;
        }
        
        return result;
    }

    getItemTemplate(templateId) {
        return this.itemTemplates.get(templateId);
    }

    getInventoryStatistics(playerId) {
        this.initializePlayerInventory(playerId);
        
        const inventory = this.playerInventories.get(playerId);
        const bank = this.playerBanks.get(playerId);
        const equipment = this.playerEquipment.get(playerId);
        
        const totalValue = inventory.items.reduce((sum, item) => {
            const template = this.itemTemplates.get(item.templateId);
            return sum + (template.value * item.quantity);
        }, 0);
        
        const equippedCount = Object.values(equipment).filter(Boolean).length;
        
        return {
            inventoryUsed: inventory.items.length,
            inventorySize: inventory.size,
            bankUsed: bank.items.length,
            bankSize: bank.size,
            equippedItems: equippedCount,
            totalValue,
            totalItems: inventory.items.reduce((sum, item) => sum + item.quantity, 0)
        };
    }
}

module.exports = InventoryManager;