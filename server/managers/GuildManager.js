const EventEmitter = require('events');
const { v4: uuidv4 } = require('uuid');
const _ = require('lodash');

const GUILD_CONFIG = {
    MAX_MEMBERS: {
        1: 10,   // Level 1 guild
        2: 20,   // Level 2 guild
        3: 35,   // Level 3 guild
        4: 50,   // Level 4 guild
        5: 75,   // Level 5 guild
        6: 100   // Max level guild
    },
    CREATION_COST: 5000,
    LEVEL_UP_COSTS: {
        2: 10000,
        3: 25000,
        4: 50000,
        5: 100000,
        6: 200000
    },
    ROLES: {
        LEADER: 'leader',
        OFFICER: 'officer',
        MEMBER: 'member',
        RECRUIT: 'recruit'
    },
    PERMISSIONS: {
        INVITE: 'invite',
        KICK: 'kick',
        PROMOTE: 'promote',
        DEMOTE: 'demote',
        EDIT_INFO: 'edit_info',
        MANAGE_HALL: 'manage_hall',
        WITHDRAW_FUNDS: 'withdraw_funds',
        DECLARE_WAR: 'declare_war',
        ACCEPT_ALLIANCE: 'accept_alliance'
    },
    WAR_DURATION: 7 * 24 * 60 * 60 * 1000, // 7 days
    ALLIANCE_COOLDOWN: 24 * 60 * 60 * 1000, // 24 hours
    HALL_UPGRADE_COSTS: {
        treasury: { 2: 15000, 3: 30000, 4: 60000, 5: 120000 },
        armory: { 2: 20000, 3: 40000, 4: 80000, 5: 160000 },
        library: { 2: 25000, 3: 50000, 4: 100000, 5: 200000 },
        barracks: { 2: 30000, 3: 60000, 4: 120000, 5: 240000 }
    }
};

class GuildManager extends EventEmitter {
    constructor(gameEngine) {
        super();
        this.gameEngine = gameEngine;
        this.guilds = new Map();
        this.guildMembers = new Map(); // playerId -> guildId
        this.guildInvites = new Map(); // playerId -> [invites]
        this.guildWars = new Map(); // guildId -> [wars]
        this.guildAlliances = new Map(); // guildId -> [alliances]
        this.guildHalls = new Map(); // guildId -> hall data
        
        this.setupEventListeners();
        this.startGuildUpdates();
    }

    setupEventListeners() {
        this.gameEngine.on('playerLevelUp', this.handlePlayerLevelUp.bind(this));
        this.gameEngine.on('playerKill', this.handlePlayerKill.bind(this));
        this.gameEngine.on('playerDeath', this.handlePlayerDeath.bind(this));
    }

    startGuildUpdates() {
        // Guild wars and events updates
        setInterval(() => {
            this.updateGuildWars();
            this.updateGuildAlliances();
            this.updateGuildHalls();
            this.processGuildTaxes();
        }, 60000); // Every minute

        // Guild statistics updates
        setInterval(() => {
            this.updateGuildStatistics();
            this.checkGuildActivity();
            this.processGuildDecay();
        }, 300000); // Every 5 minutes
    }

    // Guild Creation and Management
    async createGuild(playerId, guildData) {
        try {
            const player = await this.gameEngine.getPlayer(playerId);
            if (!player) {
                throw new Error('Player not found');
            }

            if (this.guildMembers.has(playerId)) {
                throw new Error('Player is already in a guild');
            }

            if (player.gold < GUILD_CONFIG.CREATION_COST) {
                throw new Error('Insufficient gold to create guild');
            }

            const guildId = uuidv4();
            const guild = {
                id: guildId,
                name: guildData.name,
                tag: guildData.tag,
                description: guildData.description || '',
                leaderId: playerId,
                level: 1,
                experience: 0,
                gold: 0,
                members: new Map(),
                invites: new Map(),
                createdAt: new Date(),
                lastActivity: new Date(),
                stats: {
                    totalKills: 0,
                    totalDeaths: 0,
                    warsWon: 0,
                    warsLost: 0,
                    membersJoined: 1,
                    membersLeft: 0
                },
                settings: {
                    openInvites: false,
                    levelRequirement: 1,
                    taxRate: 0.1,
                    warMode: true,
                    allianceMode: true
                },
                hall: {
                    level: 1,
                    rooms: {
                        treasury: { level: 1, capacity: 50000 },
                        armory: { level: 1, slots: 20 },
                        library: { level: 1, books: 5 },
                        barracks: { level: 1, capacity: 10 }
                    },
                    decorations: [],
                    npcs: []
                }
            };

            // Add leader as member
            guild.members.set(playerId, {
                playerId,
                role: GUILD_CONFIG.ROLES.LEADER,
                joinedAt: new Date(),
                contributions: {
                    gold: 0,
                    experience: 0,
                    kills: 0
                },
                permissions: Object.values(GUILD_CONFIG.PERMISSIONS)
            });

            this.guilds.set(guildId, guild);
            this.guildMembers.set(playerId, guildId);
            this.guildHalls.set(guildId, guild.hall);

            // Deduct creation cost
            await this.gameEngine.economyManager.deductGold(playerId, GUILD_CONFIG.CREATION_COST);

            this.emit('guildCreated', { guild, playerId });
            return guild;

        } catch (error) {
            throw error;
        }
    }

    async invitePlayer(guildId, inviterId, targetPlayerId) {
        try {
            const guild = this.guilds.get(guildId);
            if (!guild) {
                throw new Error('Guild not found');
            }

            const inviter = guild.members.get(inviterId);
            if (!inviter || !inviter.permissions.includes(GUILD_CONFIG.PERMISSIONS.INVITE)) {
                throw new Error('No permission to invite players');
            }

            if (this.guildMembers.has(targetPlayerId)) {
                throw new Error('Player is already in a guild');
            }

            const targetPlayer = await this.gameEngine.getPlayer(targetPlayerId);
            if (!targetPlayer) {
                throw new Error('Target player not found');
            }

            if (targetPlayer.level < guild.settings.levelRequirement) {
                throw new Error('Player does not meet level requirement');
            }

            if (guild.members.size >= GUILD_CONFIG.MAX_MEMBERS[guild.level]) {
                throw new Error('Guild is full');
            }

            // Create invite
            const invite = {
                id: uuidv4(),
                guildId,
                guildName: guild.name,
                inviterId,
                targetPlayerId,
                createdAt: new Date(),
                expiresAt: new Date(Date.now() + 7 * 24 * 60 * 60 * 1000) // 7 days
            };

            if (!this.guildInvites.has(targetPlayerId)) {
                this.guildInvites.set(targetPlayerId, []);
            }
            this.guildInvites.get(targetPlayerId).push(invite);

            this.emit('guildInviteSent', { invite, guild, inviter: inviterId, target: targetPlayerId });
            return invite;

        } catch (error) {
            throw error;
        }
    }

    async acceptInvite(playerId, inviteId) {
        try {
            const invites = this.guildInvites.get(playerId) || [];
            const inviteIndex = invites.findIndex(inv => inv.id === inviteId);
            
            if (inviteIndex === -1) {
                throw new Error('Invite not found');
            }

            const invite = invites[inviteIndex];
            if (invite.expiresAt < new Date()) {
                invites.splice(inviteIndex, 1);
                throw new Error('Invite has expired');
            }

            const guild = this.guilds.get(invite.guildId);
            if (!guild) {
                throw new Error('Guild not found');
            }

            if (guild.members.size >= GUILD_CONFIG.MAX_MEMBERS[guild.level]) {
                throw new Error('Guild is full');
            }

            // Add player to guild
            guild.members.set(playerId, {
                playerId,
                role: GUILD_CONFIG.ROLES.RECRUIT,
                joinedAt: new Date(),
                contributions: {
                    gold: 0,
                    experience: 0,
                    kills: 0
                },
                permissions: []
            });

            this.guildMembers.set(playerId, guild.id);
            guild.stats.membersJoined++;

            // Remove all invites for this player
            this.guildInvites.delete(playerId);

            this.emit('playerJoinedGuild', { guild, playerId });
            return guild;

        } catch (error) {
            throw error;
        }
    }

    async leaveGuild(playerId) {
        try {
            const guildId = this.guildMembers.get(playerId);
            if (!guildId) {
                throw new Error('Player is not in a guild');
            }

            const guild = this.guilds.get(guildId);
            if (!guild) {
                throw new Error('Guild not found');
            }

            const member = guild.members.get(playerId);
            if (member.role === GUILD_CONFIG.ROLES.LEADER) {
                // Transfer leadership or disband guild
                const officers = Array.from(guild.members.values())
                    .filter(m => m.role === GUILD_CONFIG.ROLES.OFFICER);
                
                if (officers.length > 0) {
                    // Promote first officer to leader
                    const newLeader = officers[0];
                    newLeader.role = GUILD_CONFIG.ROLES.LEADER;
                    newLeader.permissions = Object.values(GUILD_CONFIG.PERMISSIONS);
                    guild.leaderId = newLeader.playerId;
                } else if (guild.members.size > 1) {
                    // Promote first member to leader
                    const members = Array.from(guild.members.values())
                        .filter(m => m.playerId !== playerId);
                    if (members.length > 0) {
                        const newLeader = members[0];
                        newLeader.role = GUILD_CONFIG.ROLES.LEADER;
                        newLeader.permissions = Object.values(GUILD_CONFIG.PERMISSIONS);
                        guild.leaderId = newLeader.playerId;
                    }
                } else {
                    // Disband guild
                    return this.disbandGuild(guildId);
                }
            }

            guild.members.delete(playerId);
            this.guildMembers.delete(playerId);
            guild.stats.membersLeft++;

            this.emit('playerLeftGuild', { guild, playerId });
            return true;

        } catch (error) {
            throw error;
        }
    }

    async kickPlayer(guildId, kickerId, targetPlayerId) {
        try {
            const guild = this.guilds.get(guildId);
            if (!guild) {
                throw new Error('Guild not found');
            }

            const kicker = guild.members.get(kickerId);
            if (!kicker || !kicker.permissions.includes(GUILD_CONFIG.PERMISSIONS.KICK)) {
                throw new Error('No permission to kick players');
            }

            const target = guild.members.get(targetPlayerId);
            if (!target) {
                throw new Error('Target player is not in guild');
            }

            if (target.role === GUILD_CONFIG.ROLES.LEADER) {
                throw new Error('Cannot kick guild leader');
            }

            guild.members.delete(targetPlayerId);
            this.guildMembers.delete(targetPlayerId);
            guild.stats.membersLeft++;

            this.emit('playerKicked', { guild, kickerId, targetPlayerId });
            return true;

        } catch (error) {
            throw error;
        }
    }

    // Guild Wars
    async declareWar(attackerGuildId, defenderGuildId, playerId) {
        try {
            const attackerGuild = this.guilds.get(attackerGuildId);
            const defenderGuild = this.guilds.get(defenderGuildId);

            if (!attackerGuild || !defenderGuild) {
                throw new Error('Guild not found');
            }

            const declarer = attackerGuild.members.get(playerId);
            if (!declarer || !declarer.permissions.includes(GUILD_CONFIG.PERMISSIONS.DECLARE_WAR)) {
                throw new Error('No permission to declare war');
            }

            // Check for existing wars
            const existingWar = this.guildWars.get(attackerGuildId)?.find(war => 
                war.defenderGuildId === defenderGuildId && war.status === 'active'
            );

            if (existingWar) {
                throw new Error('War already exists between these guilds');
            }

            const war = {
                id: uuidv4(),
                attackerGuildId,
                defenderGuildId,
                declaredBy: playerId,
                declaredAt: new Date(),
                endsAt: new Date(Date.now() + GUILD_CONFIG.WAR_DURATION),
                status: 'active',
                kills: {
                    [attackerGuildId]: 0,
                    [defenderGuildId]: 0
                },
                participants: {
                    [attackerGuildId]: new Set(),
                    [defenderGuildId]: new Set()
                }
            };

            if (!this.guildWars.has(attackerGuildId)) {
                this.guildWars.set(attackerGuildId, []);
            }
            if (!this.guildWars.has(defenderGuildId)) {
                this.guildWars.set(defenderGuildId, []);
            }

            this.guildWars.get(attackerGuildId).push(war);
            this.guildWars.get(defenderGuildId).push(war);

            this.emit('warDeclared', { war, attackerGuild, defenderGuild });
            return war;

        } catch (error) {
            throw error;
        }
    }

    // Guild Alliances
    async proposeAlliance(proposerGuildId, targetGuildId, playerId) {
        try {
            const proposerGuild = this.guilds.get(proposerGuildId);
            const targetGuild = this.guilds.get(targetGuildId);

            if (!proposerGuild || !targetGuild) {
                throw new Error('Guild not found');
            }

            const proposer = proposerGuild.members.get(playerId);
            if (!proposer || !proposer.permissions.includes(GUILD_CONFIG.PERMISSIONS.ACCEPT_ALLIANCE)) {
                throw new Error('No permission to propose alliance');
            }

            const alliance = {
                id: uuidv4(),
                proposerGuildId,
                targetGuildId,
                proposedBy: playerId,
                proposedAt: new Date(),
                expiresAt: new Date(Date.now() + GUILD_CONFIG.ALLIANCE_COOLDOWN),
                status: 'pending',
                benefits: {
                    sharedResources: false,
                    mutualDefense: true,
                    tradeBonuses: true
                }
            };

            if (!this.guildAlliances.has(proposerGuildId)) {
                this.guildAlliances.set(proposerGuildId, []);
            }

            this.guildAlliances.get(proposerGuildId).push(alliance);

            this.emit('allianceProposed', { alliance, proposerGuild, targetGuild });
            return alliance;

        } catch (error) {
            throw error;
        }
    }

    // Guild Hall Management
    async upgradeGuildHall(guildId, playerId, roomType) {
        try {
            const guild = this.guilds.get(guildId);
            if (!guild) {
                throw new Error('Guild not found');
            }

            const member = guild.members.get(playerId);
            if (!member || !member.permissions.includes(GUILD_CONFIG.PERMISSIONS.MANAGE_HALL)) {
                throw new Error('No permission to manage guild hall');
            }

            const hall = this.guildHalls.get(guildId);
            const room = hall.rooms[roomType];
            
            if (!room) {
                throw new Error('Invalid room type');
            }

            if (room.level >= 5) {
                throw new Error('Room is already at maximum level');
            }

            const upgradeCost = GUILD_CONFIG.HALL_UPGRADE_COSTS[roomType][room.level + 1];
            if (guild.gold < upgradeCost) {
                throw new Error('Insufficient guild funds');
            }

            // Upgrade room
            room.level++;
            guild.gold -= upgradeCost;

            // Apply room benefits
            switch (roomType) {
                case 'treasury':
                    room.capacity *= 1.5;
                    break;
                case 'armory':
                    room.slots += 10;
                    break;
                case 'library':
                    room.books += 3;
                    break;
                case 'barracks':
                    room.capacity += 5;
                    break;
            }

            this.emit('guildHallUpgraded', { guild, roomType, newLevel: room.level });
            return hall;

        } catch (error) {
            throw error;
        }
    }

    // Event Handlers
    handlePlayerLevelUp(data) {
        const { playerId, newLevel } = data;
        const guildId = this.guildMembers.get(playerId);
        
        if (guildId) {
            const guild = this.guilds.get(guildId);
            const member = guild.members.get(playerId);
            
            if (member) {
                member.contributions.experience += 100 * newLevel;
                guild.experience += 100 * newLevel;
                
                this.checkGuildLevelUp(guild);
            }
        }
    }

    handlePlayerKill(data) {
        const { killerId, victimId } = data;
        const killerGuildId = this.guildMembers.get(killerId);
        const victimGuildId = this.guildMembers.get(victimId);
        
        if (killerGuildId) {
            const guild = this.guilds.get(killerGuildId);
            const member = guild.members.get(killerId);
            
            if (member) {
                member.contributions.kills++;
                guild.stats.totalKills++;
                
                // Check for guild war kills
                if (victimGuildId && killerGuildId !== victimGuildId) {
                    this.processWarKill(killerGuildId, victimGuildId, killerId);
                }
            }
        }
    }

    handlePlayerDeath(data) {
        const { playerId } = data;
        const guildId = this.guildMembers.get(playerId);
        
        if (guildId) {
            const guild = this.guilds.get(guildId);
            if (guild) {
                guild.stats.totalDeaths++;
            }
        }
    }

    // Utility Methods
    checkGuildLevelUp(guild) {
        const requiredExp = guild.level * 10000;
        const requiredGold = GUILD_CONFIG.LEVEL_UP_COSTS[guild.level + 1];
        
        if (guild.experience >= requiredExp && guild.gold >= requiredGold && guild.level < 6) {
            guild.level++;
            guild.experience -= requiredExp;
            guild.gold -= requiredGold;
            
            this.emit('guildLevelUp', { guild, newLevel: guild.level });
        }
    }

    processWarKill(killerGuildId, victimGuildId, killerId) {
        const killerWars = this.guildWars.get(killerGuildId) || [];
        const activeWar = killerWars.find(war => 
            (war.attackerGuildId === victimGuildId || war.defenderGuildId === victimGuildId) &&
            war.status === 'active'
        );
        
        if (activeWar) {
            activeWar.kills[killerGuildId]++;
            activeWar.participants[killerGuildId].add(killerId);
            
            this.emit('warKill', { war: activeWar, killerId, killerGuildId, victimGuildId });
        }
    }

    updateGuildWars() {
        const now = new Date();
        
        for (const [guildId, wars] of this.guildWars.entries()) {
            for (const war of wars) {
                if (war.status === 'active' && war.endsAt <= now) {
                    this.endWar(war);
                }
            }
        }
    }

    endWar(war) {
        war.status = 'ended';
        war.endedAt = new Date();
        
        const attackerKills = war.kills[war.attackerGuildId];
        const defenderKills = war.kills[war.defenderGuildId];
        
        if (attackerKills > defenderKills) {
            war.winner = war.attackerGuildId;
            this.guilds.get(war.attackerGuildId).stats.warsWon++;
            this.guilds.get(war.defenderGuildId).stats.warsLost++;
        } else if (defenderKills > attackerKills) {
            war.winner = war.defenderGuildId;
            this.guilds.get(war.defenderGuildId).stats.warsWon++;
            this.guilds.get(war.attackerGuildId).stats.warsLost++;
        } else {
            war.winner = null; // Draw
        }
        
        this.emit('warEnded', { war });
    }

    updateGuildAlliances() {
        const now = new Date();
        
        for (const [guildId, alliances] of this.guildAlliances.entries()) {
            alliances.forEach(alliance => {
                if (alliance.status === 'pending' && alliance.expiresAt <= now) {
                    alliance.status = 'expired';
                }
            });
        }
    }

    updateGuildHalls() {
        // Process hall maintenance, NPC activities, etc.
        for (const [guildId, hall] of this.guildHalls.entries()) {
            const guild = this.guilds.get(guildId);
            
            if (guild) {
                // Generate passive income from hall
                const income = hall.rooms.treasury.level * 100;
                guild.gold += income;
                
                // Maintain hall (cost gold)
                const maintenance = hall.level * 50;
                guild.gold = Math.max(0, guild.gold - maintenance);
            }
        }
    }

    processGuildTaxes() {
        for (const [guildId, guild] of this.guilds.entries()) {
            // Collect taxes from members based on their recent earnings
            // This would integrate with player activity tracking
        }
    }

    updateGuildStatistics() {
        for (const [guildId, guild] of this.guilds.entries()) {
            guild.lastActivity = new Date();
            
            // Calculate guild power rating
            guild.powerRating = this.calculateGuildPower(guild);
        }
    }

    calculateGuildPower(guild) {
        let power = 0;
        power += guild.level * 1000;
        power += guild.members.size * 100;
        power += guild.stats.totalKills * 10;
        power += guild.stats.warsWon * 500;
        power += guild.gold / 100;
        
        return Math.floor(power);
    }

    checkGuildActivity() {
        const inactiveThreshold = 30 * 24 * 60 * 60 * 1000; // 30 days
        const now = new Date();
        
        for (const [guildId, guild] of this.guilds.entries()) {
            if (now - guild.lastActivity > inactiveThreshold) {
                // Mark guild as inactive or initiate decay
                this.processGuildDecay();
            }
        }
    }

    processGuildDecay() {
        // Reduce gold, experience, etc. for inactive guilds
        for (const [guildId, guild] of this.guilds.entries()) {
            if (guild.members.size === 0) {
                this.disbandGuild(guildId);
            }
        }
    }

    disbandGuild(guildId) {
        const guild = this.guilds.get(guildId);
        if (!guild) return;
        
        // Remove all members
        for (const playerId of guild.members.keys()) {
            this.guildMembers.delete(playerId);
        }
        
        // Clean up data
        this.guilds.delete(guildId);
        this.guildWars.delete(guildId);
        this.guildAlliances.delete(guildId);
        this.guildHalls.delete(guildId);
        
        this.emit('guildDisbanded', { guildId, guild });
    }

    // Public API Methods
    getGuild(guildId) {
        return this.guilds.get(guildId);
    }

    getPlayerGuild(playerId) {
        const guildId = this.guildMembers.get(playerId);
        return guildId ? this.guilds.get(guildId) : null;
    }

    getGuildInvites(playerId) {
        return this.guildInvites.get(playerId) || [];
    }

    getGuildWars(guildId) {
        return this.guildWars.get(guildId) || [];
    }

    getGuildAlliances(guildId) {
        return this.guildAlliances.get(guildId) || [];
    }

    getTopGuilds(limit = 10) {
        return Array.from(this.guilds.values())
            .sort((a, b) => (b.powerRating || 0) - (a.powerRating || 0))
            .slice(0, limit);
    }

    searchGuilds(query) {
        const results = [];
        const searchTerm = query.toLowerCase();
        
        for (const guild of this.guilds.values()) {
            if (guild.name.toLowerCase().includes(searchTerm) ||
                guild.tag.toLowerCase().includes(searchTerm)) {
                results.push(guild);
            }
        }
        
        return results;
    }

    getGuildStatistics() {
        return {
            totalGuilds: this.guilds.size,
            totalMembers: this.guildMembers.size,
            activeWars: Array.from(this.guildWars.values())
                .flat()
                .filter(war => war.status === 'active').length,
            activeAlliances: Array.from(this.guildAlliances.values())
                .flat()
                .filter(alliance => alliance.status === 'active').length
        };
    }
}

module.exports = GuildManager;