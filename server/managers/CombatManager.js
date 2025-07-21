/**
 * Combat Manager - Handles all combat mechanics including PvP and PvE
 */

const winston = require('winston');
const EventEmitter = require('events');

class CombatManager extends EventEmitter {
    constructor(io) {
        super();
        this.io = io;
        this.activeCombats = new Map(); // combatId -> combat instance
        this.playerCombats = new Map(); // playerId -> combatId
        this.combatQueue = new Map(); // playerId -> combat queue
        
        this.logger = winston.createLogger({
            level: 'info',
            format: winston.format.combine(
                winston.format.timestamp(),
                winston.format.json()
            ),
            defaultMeta: { service: 'combat-manager' },
            transports: [
                new winston.transports.File({ filename: 'logs/combat-manager.log' }),
                new winston.transports.Console()
            ]
        });

        // Combat configuration
        this.COMBAT_CONFIG = {
            maxCombatDuration: 10 * 60 * 1000, // 10 minutes
            turnDuration: 30000, // 30 seconds per turn
            criticalHitMultiplier: 2.0,
            blockReduction: 0.5,
            dodgeChance: 0.1,
            counterAttackChance: 0.15,
            comboMaxLength: 5,
            experienceMultiplier: 1.0,
            pvpExperienceMultiplier: 2.0,
            deathPenalty: 0.05
        };

        // Combat skills and their effects
        this.COMBAT_SKILLS = {
            // Warrior skills
            sword_mastery: {
                name: 'Sword Mastery',
                type: 'passive',
                effect: { damageBonus: 0.2, critChance: 0.1 },
                manaCost: 0,
                cooldown: 0
            },
            shield_bash: {
                name: 'Shield Bash',
                type: 'active',
                effect: { damage: 50, stun: 2000 },
                manaCost: 20,
                cooldown: 15000
            },
            taunt: {
                name: 'Taunt',
                type: 'active',
                effect: { threat: 100, duration: 5000 },
                manaCost: 15,
                cooldown: 10000
            },
            berserker_rage: {
                name: 'Berserker Rage',
                type: 'buff',
                effect: { damageBonus: 0.5, defenseReduction: 0.3, duration: 30000 },
                manaCost: 50,
                cooldown: 60000
            },

            // Mage skills
            fireball: {
                name: 'Fireball',
                type: 'damage',
                effect: { damage: 80, burnDamage: 10, burnDuration: 5000 },
                manaCost: 30,
                cooldown: 3000
            },
            ice_shard: {
                name: 'Ice Shard',
                type: 'damage',
                effect: { damage: 60, slow: 0.5, slowDuration: 8000 },
                manaCost: 25,
                cooldown: 2500
            },
            heal: {
                name: 'Heal',
                type: 'heal',
                effect: { healing: 100 },
                manaCost: 40,
                cooldown: 5000
            },
            mana_shield: {
                name: 'Mana Shield',
                type: 'buff',
                effect: { damageToMana: 0.5, duration: 60000 },
                manaCost: 80,
                cooldown: 120000
            },

            // Archer skills
            arrow_shot: {
                name: 'Arrow Shot',
                type: 'damage',
                effect: { damage: 70, range: 10 },
                manaCost: 15,
                cooldown: 1500
            },
            multishot: {
                name: 'Multishot',
                type: 'damage',
                effect: { damage: 45, arrows: 3, range: 8 },
                manaCost: 35,
                cooldown: 8000
            },
            trap: {
                name: 'Trap',
                type: 'utility',
                effect: { damage: 100, immobilize: 3000, duration: 30000 },
                manaCost: 40,
                cooldown: 20000
            },
            stealth: {
                name: 'Stealth',
                type: 'buff',
                effect: { invisible: true, damageBonus: 0.3, duration: 10000 },
                manaCost: 60,
                cooldown: 45000
            },

            // Rogue skills
            backstab: {
                name: 'Backstab',
                type: 'damage',
                effect: { damage: 120, critBonus: 0.5, positionBonus: true },
                manaCost: 30,
                cooldown: 6000
            },
            poison_blade: {
                name: 'Poison Blade',
                type: 'buff',
                effect: { poisonDamage: 15, poisonDuration: 10000, duration: 60000 },
                manaCost: 45,
                cooldown: 30000
            },
            smoke_bomb: {
                name: 'Smoke Bomb',
                type: 'utility',
                effect: { blind: 3000, escape: true },
                manaCost: 35,
                cooldown: 25000
            },

            // Paladin skills
            holy_strike: {
                name: 'Holy Strike',
                type: 'damage',
                effect: { damage: 90, extraVsUndead: 0.5 },
                manaCost: 35,
                cooldown: 4000
            },
            blessing: {
                name: 'Blessing',
                type: 'buff',
                effect: { allStatsBonus: 0.15, duration: 300000 },
                manaCost: 60,
                cooldown: 180000
            },
            divine_protection: {
                name: 'Divine Protection',
                type: 'buff',
                effect: { damageReduction: 0.4, duration: 15000 },
                manaCost: 70,
                cooldown: 60000
            }
        };

        // Status effects
        this.STATUS_EFFECTS = {
            burn: { type: 'dot', damage: true },
            poison: { type: 'dot', damage: true },
            slow: { type: 'debuff', movement: true },
            stun: { type: 'disable', action: true },
            immobilize: { type: 'disable', movement: true },
            blind: { type: 'debuff', accuracy: true },
            invisible: { type: 'buff', stealth: true },
            blessed: { type: 'buff', stats: true },
            rage: { type: 'buff', damage: true },
            protection: { type: 'buff', defense: true }
        };

        this.setupCombatLoop();
    }

    setupCombatLoop() {
        // Process combat updates every 100ms
        setInterval(() => {
            this.processCombatTick();
        }, 100);

        // Process damage over time effects every second
        setInterval(() => {
            this.processDotEffects();
        }, 1000);

        // Clean up expired combats every 30 seconds
        setInterval(() => {
            this.cleanupExpiredCombats();
        }, 30000);
    }

    async handleAttack(playerId, data) {
        try {
            const { targetId, attackType, skillId } = data;
            
            // Check if player is in combat
            let combatId = this.playerCombats.get(playerId);
            let combat = null;

            if (combatId) {
                combat = this.activeCombats.get(combatId);
                if (!combat || combat.isFinished) {
                    combatId = null;
                    combat = null;
                }
            }

            // Create new combat if needed
            if (!combat) {
                combat = await this.createCombat(playerId, targetId);
                if (!combat) {
                    return this.sendError(playerId, 'Cannot start combat');
                }
            }

            // Execute attack
            await this.executeAttack(combat, playerId, targetId, attackType, skillId);

        } catch (error) {
            this.logger.error('Error handling attack:', error);
            this.sendError(playerId, 'Attack failed');
        }
    }

    async handleDefend(playerId, data) {
        try {
            const { defenseType } = data;
            const combatId = this.playerCombats.get(playerId);
            if (!combatId) return;

            const combat = this.activeCombats.get(combatId);
            if (!combat || combat.isFinished) return;

            await this.executeDefense(combat, playerId, defenseType);

        } catch (error) {
            this.logger.error('Error handling defense:', error);
            this.sendError(playerId, 'Defense failed');
        }
    }

    async handleSkillUse(playerId, data) {
        try {
            const { targetId, skillId, level } = data;
            const combatId = this.playerCombats.get(playerId);
            
            if (combatId) {
                const combat = this.activeCombats.get(combatId);
                if (combat && !combat.isFinished) {
                    await this.executeSkill(combat, playerId, targetId, skillId, level);
                }
            } else {
                // Non-combat skill use
                await this.executeNonCombatSkill(playerId, targetId, skillId, level);
            }

        } catch (error) {
            this.logger.error('Error handling skill use:', error);
            this.sendError(playerId, 'Skill use failed');
        }
    }

    async handleFlee(playerId, data) {
        try {
            const combatId = this.playerCombats.get(playerId);
            if (!combatId) return;

            const combat = this.activeCombats.get(combatId);
            if (!combat || combat.isFinished) return;

            await this.attemptFlee(combat, playerId);

        } catch (error) {
            this.logger.error('Error handling flee:', error);
            this.sendError(playerId, 'Flee failed');
        }
    }

    async createCombat(attackerId, defenderId) {
        try {
            // Validate combat participants
            if (!this.canStartCombat(attackerId, defenderId)) {
                return null;
            }

            const combatId = `combat_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
            
            const combat = {
                id: combatId,
                type: this.getCombatType(attackerId, defenderId),
                participants: [attackerId, defenderId],
                startTime: Date.now(),
                currentTurn: attackerId,
                turnStartTime: Date.now(),
                round: 1,
                isFinished: false,
                winner: null,
                loser: null,
                totalDamageDealt: new Map(),
                statusEffects: new Map(), // participantId -> effects array
                combatLog: [],
                experience: 0,
                loot: []
            };

            // Initialize participant data
            for (const participantId of combat.participants) {
                combat.totalDamageDealt.set(participantId, 0);
                combat.statusEffects.set(participantId, []);
            }

            this.activeCombats.set(combatId, combat);
            
            // Map players to combat
            for (const participantId of combat.participants) {
                this.playerCombats.set(participantId, combatId);
            }

            // Notify participants
            this.broadcastToCombat(combat, 'combat:started', {
                combatId,
                type: combat.type,
                participants: combat.participants,
                currentTurn: combat.currentTurn
            });

            this.logger.info(`Combat started: ${combatId}`, {
                participants: combat.participants,
                type: combat.type
            });

            return combat;

        } catch (error) {
            this.logger.error('Error creating combat:', error);
            return null;
        }
    }

    async executeAttack(combat, attackerId, targetId, attackType, skillId) {
        // Check if it's attacker's turn
        if (combat.currentTurn !== attackerId) {
            return this.sendError(attackerId, 'Not your turn');
        }

        // Calculate base attack damage
        let damage = await this.calculateAttackDamage(attackerId, targetId, attackType, skillId);
        
        // Apply skill effects if using a skill
        if (skillId && this.COMBAT_SKILLS[skillId]) {
            damage = await this.applySkillEffects(combat, attackerId, targetId, skillId, damage);
        }

        // Apply defense calculations
        damage = await this.applyDefenseCalculations(combat, attackerId, targetId, damage);

        // Apply damage
        await this.applyDamage(combat, targetId, damage);

        // Log combat action
        this.logCombatAction(combat, attackerId, 'attack', {
            target: targetId,
            damage: damage.final,
            skillUsed: skillId,
            isCritical: damage.isCritical,
            wasBlocked: damage.wasBlocked,
            wasDodged: damage.wasDodged
        });

        // Check for combat end
        if (await this.checkCombatEnd(combat)) {
            return;
        }

        // Switch turns
        this.switchTurn(combat);

        // Broadcast combat update
        this.broadcastCombatUpdate(combat);
    }

    async executeDefense(combat, playerId, defenseType) {
        // Set defense stance for the player
        const participant = combat.participants.find(p => p === playerId);
        if (!participant) return;

        // Apply defense modifier
        const defenseData = {
            type: defenseType,
            modifier: this.getDefenseModifier(defenseType),
            duration: 5000 // 5 seconds
        };

        // Store defense state
        if (!combat.defenseStates) {
            combat.defenseStates = new Map();
        }
        combat.defenseStates.set(playerId, defenseData);

        this.logCombatAction(combat, playerId, 'defend', { defenseType });
        this.broadcastCombatUpdate(combat);
    }

    async executeSkill(combat, casterId, targetId, skillId, level) {
        const skill = this.COMBAT_SKILLS[skillId];
        if (!skill) {
            return this.sendError(casterId, 'Unknown skill');
        }

        // Check mana cost
        const manaCost = this.calculateManaCost(skill, level);
        if (!await this.hasEnoughMana(casterId, manaCost)) {
            return this.sendError(casterId, 'Not enough mana');
        }

        // Check cooldown
        if (!await this.isSkillOffCooldown(casterId, skillId)) {
            return this.sendError(casterId, 'Skill on cooldown');
        }

        // Consume mana
        await this.consumeMana(casterId, manaCost);

        // Execute skill effect
        await this.applySkillEffect(combat, casterId, targetId, skill, level);

        // Set cooldown
        await this.setSkillCooldown(casterId, skillId, skill.cooldown);

        this.logCombatAction(combat, casterId, 'skill', {
            skillId,
            target: targetId,
            level
        });

        this.broadcastCombatUpdate(combat);
    }

    async executeNonCombatSkill(playerId, targetId, skillId, level) {
        const skill = this.COMBAT_SKILLS[skillId];
        if (!skill || skill.type === 'damage') {
            return this.sendError(playerId, 'Cannot use combat skill outside of combat');
        }

        // Check requirements and execute
        const manaCost = this.calculateManaCost(skill, level);
        if (!await this.hasEnoughMana(playerId, manaCost)) {
            return this.sendError(playerId, 'Not enough mana');
        }

        if (!await this.isSkillOffCooldown(playerId, skillId)) {
            return this.sendError(playerId, 'Skill on cooldown');
        }

        await this.consumeMana(playerId, manaCost);
        await this.applyNonCombatSkillEffect(playerId, targetId, skill, level);
        await this.setSkillCooldown(playerId, skillId, skill.cooldown);

        // Broadcast skill use
        this.io.emit('skill:used', {
            casterId: playerId,
            targetId,
            skillId,
            level,
            timestamp: Date.now()
        });
    }

    async attemptFlee(combat, playerId) {
        // Calculate flee chance
        const fleeChance = this.calculateFleeChance(combat, playerId);
        const success = Math.random() < fleeChance;

        if (success) {
            // Remove player from combat
            await this.removePlayerFromCombat(combat, playerId);
            
            this.logCombatAction(combat, playerId, 'flee', { success: true });
            
            // Check if combat should end
            if (combat.participants.length <= 1) {
                await this.endCombat(combat, 'flee');
            }
        } else {
            this.logCombatAction(combat, playerId, 'flee', { success: false });
            // Failed flee, switch turn
            this.switchTurn(combat);
        }

        this.broadcastCombatUpdate(combat);
    }

    processCombatTick() {
        for (const [combatId, combat] of this.activeCombats) {
            if (combat.isFinished) continue;

            // Check turn timeout
            const turnDuration = Date.now() - combat.turnStartTime;
            if (turnDuration > this.COMBAT_CONFIG.turnDuration) {
                this.handleTurnTimeout(combat);
            }

            // Process status effects
            this.processStatusEffects(combat);

            // Check for combat timeout
            const combatDuration = Date.now() - combat.startTime;
            if (combatDuration > this.COMBAT_CONFIG.maxCombatDuration) {
                this.endCombat(combat, 'timeout');
            }
        }
    }

    processDotEffects() {
        for (const [combatId, combat] of this.activeCombats) {
            if (combat.isFinished) continue;

            for (const [participantId, effects] of combat.statusEffects) {
                for (const effect of effects) {
                    if (this.STATUS_EFFECTS[effect.type]?.damage) {
                        this.applyDotDamage(combat, participantId, effect);
                    }
                }
            }
        }
    }

    cleanupExpiredCombats() {
        const now = Date.now();
        for (const [combatId, combat] of this.activeCombats) {
            if (combat.isFinished && (now - combat.endTime > 300000)) { // 5 minutes
                this.activeCombats.delete(combatId);
                
                // Clean up player mappings
                for (const participantId of combat.participants) {
                    if (this.playerCombats.get(participantId) === combatId) {
                        this.playerCombats.delete(participantId);
                    }
                }
            }
        }
    }

    // Utility methods
    calculateAttackDamage(attackerId, targetId, attackType, skillId) {
        // This would integrate with PlayerManager to get player stats
        // For now, return mock damage calculation
        const baseDamage = 50;
        const randomFactor = 0.8 + Math.random() * 0.4; // ±20%
        const isCritical = Math.random() < 0.15; // 15% crit chance
        
        let damage = Math.floor(baseDamage * randomFactor);
        if (isCritical) {
            damage *= this.COMBAT_CONFIG.criticalHitMultiplier;
        }

        return {
            base: baseDamage,
            final: damage,
            isCritical,
            wasBlocked: false,
            wasDodged: false
        };
    }

    async applySkillEffects(combat, casterId, targetId, skillId, baseDamage) {
        const skill = this.COMBAT_SKILLS[skillId];
        let modifiedDamage = { ...baseDamage };

        if (skill.effect.damage) {
            modifiedDamage.final += skill.effect.damage;
        }

        if (skill.effect.damageBonus) {
            modifiedDamage.final *= (1 + skill.effect.damageBonus);
        }

        // Apply status effects
        if (skill.effect.burnDamage || skill.effect.poisonDamage) {
            await this.applyStatusEffect(combat, targetId, skill);
        }

        return modifiedDamage;
    }

    async applyDefenseCalculations(combat, attackerId, targetId, damage) {
        let modifiedDamage = { ...damage };
        
        // Check for defense stance
        const defenseState = combat.defenseStates?.get(targetId);
        if (defenseState) {
            const blockChance = defenseState.modifier.blockChance || 0;
            if (Math.random() < blockChance) {
                modifiedDamage.final *= this.COMBAT_CONFIG.blockReduction;
                modifiedDamage.wasBlocked = true;
            }
        }

        // Check for dodge
        const dodgeChance = this.COMBAT_CONFIG.dodgeChance;
        if (Math.random() < dodgeChance) {
            modifiedDamage.final = 0;
            modifiedDamage.wasDodged = true;
        }

        return modifiedDamage;
    }

    async applyDamage(combat, targetId, damage) {
        // This would integrate with PlayerManager to apply actual damage
        // For now, just log the damage
        this.logger.info(`Applied ${damage.final} damage to ${targetId}`, damage);
        
        // Update combat damage tracking
        const currentDamage = combat.totalDamageDealt.get(targetId) || 0;
        combat.totalDamageDealt.set(targetId, currentDamage + damage.final);
    }

    async applyStatusEffect(combat, targetId, skill) {
        const effects = combat.statusEffects.get(targetId) || [];
        
        if (skill.effect.burnDamage) {
            effects.push({
                type: 'burn',
                damage: skill.effect.burnDamage,
                duration: skill.effect.burnDuration,
                startTime: Date.now()
            });
        }

        if (skill.effect.poisonDamage) {
            effects.push({
                type: 'poison',
                damage: skill.effect.poisonDamage,
                duration: skill.effect.poisonDuration,
                startTime: Date.now()
            });
        }

        if (skill.effect.slow) {
            effects.push({
                type: 'slow',
                modifier: skill.effect.slow,
                duration: skill.effect.slowDuration,
                startTime: Date.now()
            });
        }

        combat.statusEffects.set(targetId, effects);
    }

    processStatusEffects(combat) {
        const now = Date.now();
        
        for (const [participantId, effects] of combat.statusEffects) {
            const activeEffects = effects.filter(effect => {
                return (effect.startTime + effect.duration) > now;
            });
            
            combat.statusEffects.set(participantId, activeEffects);
        }
    }

    applyDotDamage(combat, targetId, effect) {
        if (effect.lastTick && (Date.now() - effect.lastTick) < 1000) {
            return; // Already ticked this second
        }

        effect.lastTick = Date.now();
        
        this.applyDamage(combat, targetId, {
            base: effect.damage,
            final: effect.damage,
            isCritical: false,
            wasBlocked: false,
            wasDodged: false,
            isDot: true,
            effectType: effect.type
        });
    }

    handleTurnTimeout(combat) {
        // Skip turn if player doesn't act
        this.logCombatAction(combat, combat.currentTurn, 'timeout', {});
        this.switchTurn(combat);
        this.broadcastCombatUpdate(combat);
    }

    switchTurn(combat) {
        const currentIndex = combat.participants.indexOf(combat.currentTurn);
        const nextIndex = (currentIndex + 1) % combat.participants.length;
        combat.currentTurn = combat.participants[nextIndex];
        combat.turnStartTime = Date.now();
        
        // Increment round if back to first player
        if (nextIndex === 0) {
            combat.round++;
        }
    }

    async checkCombatEnd(combat) {
        // This would check player health status
        // For now, just check if combat has gone on too long
        const combatDuration = Date.now() - combat.startTime;
        if (combatDuration > this.COMBAT_CONFIG.maxCombatDuration) {
            await this.endCombat(combat, 'timeout');
            return true;
        }
        return false;
    }

    async endCombat(combat, reason) {
        combat.isFinished = true;
        combat.endTime = Date.now();
        combat.endReason = reason;

        // Determine winner based on damage dealt
        let winner = null;
        let maxDamage = 0;
        
        for (const [participantId, damage] of combat.totalDamageDealt) {
            if (damage > maxDamage) {
                maxDamage = damage;
                winner = participantId;
            }
        }

        combat.winner = winner;
        combat.loser = combat.participants.find(p => p !== winner);

        // Calculate rewards
        const rewards = await this.calculateCombatRewards(combat);
        combat.experience = rewards.experience;
        combat.loot = rewards.loot;

        // Notify participants
        this.broadcastToCombat(combat, 'combat:ended', {
            combatId: combat.id,
            winner: combat.winner,
            loser: combat.loser,
            reason,
            duration: combat.endTime - combat.startTime,
            rounds: combat.round,
            rewards
        });

        // Remove players from combat
        for (const participantId of combat.participants) {
            this.playerCombats.delete(participantId);
        }

        this.logger.info(`Combat ended: ${combat.id}`, {
            winner: combat.winner,
            reason,
            duration: combat.endTime - combat.startTime
        });

        // Emit to game engine
        this.emit('combat:end', {
            combatId: combat.id,
            winner: combat.winner,
            loser: combat.loser,
            experience: combat.experience,
            loot: combat.loot
        });
    }

    async removePlayerFromCombat(combat, playerId) {
        combat.participants = combat.participants.filter(p => p !== playerId);
        this.playerCombats.delete(playerId);
        
        // Switch turn if it was this player's turn
        if (combat.currentTurn === playerId && combat.participants.length > 0) {
            combat.currentTurn = combat.participants[0];
            combat.turnStartTime = Date.now();
        }
    }

    // Utility methods
    canStartCombat(attackerId, defenderId) {
        // Check if both players are online and not already in combat
        return !this.playerCombats.has(attackerId) && 
               !this.playerCombats.has(defenderId) &&
               attackerId !== defenderId;
    }

    getCombatType(attackerId, defenderId) {
        // Determine if it's PvP or PvE
        // For now, assume all combat is PvP
        return 'pvp';
    }

    calculateFleeChance(combat, playerId) {
        // Base flee chance decreases with combat duration
        const baseFlee = 0.7;
        const durationPenalty = Math.min(0.5, (Date.now() - combat.startTime) / 60000 * 0.1);
        return Math.max(0.2, baseFlee - durationPenalty);
    }

    getDefenseModifier(defenseType) {
        const modifiers = {
            block: { blockChance: 0.3, damageReduction: 0.2 },
            dodge: { dodgeChance: 0.4 },
            parry: { parryChance: 0.25, counterChance: 0.3 }
        };
        return modifiers[defenseType] || modifiers.block;
    }

    async calculateCombatRewards(combat) {
        const baseExperience = 100;
        const experienceMultiplier = combat.type === 'pvp' ? 
            this.COMBAT_CONFIG.pvpExperienceMultiplier : 
            this.COMBAT_CONFIG.experienceMultiplier;

        return {
            experience: Math.floor(baseExperience * experienceMultiplier),
            loot: [] // Would be calculated based on combat type and participants
        };
    }

    calculateManaCost(skill, level) {
        return Math.floor(skill.manaCost * (1 + level * 0.1));
    }

    async hasEnoughMana(playerId, manaCost) {
        // This would check with PlayerManager
        return true; // Mock implementation
    }

    async isSkillOffCooldown(playerId, skillId) {
        // This would check skill cooldowns
        return true; // Mock implementation
    }

    async consumeMana(playerId, amount) {
        // This would integrate with PlayerManager
    }

    async setSkillCooldown(playerId, skillId, cooldown) {
        // This would set skill cooldown
    }

    async applySkillEffect(combat, casterId, targetId, skill, level) {
        // Apply various skill effects based on skill type
        switch (skill.type) {
            case 'damage':
                const damage = this.calculateAttackDamage(casterId, targetId, 'skill', null);
                damage.final += skill.effect.damage * level;
                await this.applyDamage(combat, targetId, damage);
                break;
            case 'heal':
                // Apply healing
                break;
            case 'buff':
                await this.applyStatusEffect(combat, targetId, skill);
                break;
        }
    }

    async applyNonCombatSkillEffect(playerId, targetId, skill, level) {
        // Apply non-combat skill effects
        if (skill.type === 'heal') {
            // Apply healing outside combat
        }
    }

    logCombatAction(combat, playerId, action, data) {
        const logEntry = {
            timestamp: Date.now(),
            round: combat.round,
            playerId,
            action,
            data
        };
        
        combat.combatLog.push(logEntry);
        
        // Keep log size manageable
        if (combat.combatLog.length > 100) {
            combat.combatLog = combat.combatLog.slice(-50);
        }
    }

    broadcastToCombat(combat, event, data) {
        for (const participantId of combat.participants) {
            this.io.to(participantId).emit(event, data);
        }
    }

    broadcastCombatUpdate(combat) {
        const updateData = {
            combatId: combat.id,
            currentTurn: combat.currentTurn,
            round: combat.round,
            participants: combat.participants,
            statusEffects: Object.fromEntries(combat.statusEffects),
            recentActions: combat.combatLog.slice(-5)
        };
        
        this.broadcastToCombat(combat, 'combat:update', updateData);
    }

    sendError(playerId, message) {
        this.io.to(playerId).emit('error', { message });
    }

    // Public API methods
    getCombatByPlayer(playerId) {
        const combatId = this.playerCombats.get(playerId);
        return combatId ? this.activeCombats.get(combatId) : null;
    }

    isPlayerInCombat(playerId) {
        return this.playerCombats.has(playerId);
    }

    getActiveCombats() {
        return Array.from(this.activeCombats.values());
    }

    update() {
        // Called from game engine every tick
        // High-frequency combat updates are handled by internal loops
    }
}

module.exports = CombatManager;