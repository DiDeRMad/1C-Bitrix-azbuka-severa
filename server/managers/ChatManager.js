const EventEmitter = require('events');
const { v4: uuidv4 } = require('uuid');
const _ = require('lodash');

const CHAT_CONFIG = {
    CHANNELS: {
        GLOBAL: 'global',
        LOCAL: 'local',
        GUILD: 'guild',
        PARTY: 'party',
        TRADE: 'trade',
        HELP: 'help',
        SYSTEM: 'system',
        PRIVATE: 'private',
        MODERATOR: 'moderator',
        ADMIN: 'admin'
    },
    MESSAGE_TYPES: {
        CHAT: 'chat',
        EMOTE: 'emote',
        COMMAND: 'command',
        SYSTEM: 'system',
        ANNOUNCEMENT: 'announcement',
        WHISPER: 'whisper'
    },
    CHAT_LIMITS: {
        MESSAGE_LENGTH: 500,
        MESSAGES_PER_MINUTE: 10,
        PRIVATE_MESSAGES_PER_MINUTE: 5,
        CHANNEL_HISTORY: 100,
        PRIVATE_HISTORY: 50
    },
    MODERATION: {
        MUTE_DURATIONS: {
            WARNING: 60000,      // 1 minute
            MINOR: 300000,       // 5 minutes
            MODERATE: 1800000,   // 30 minutes
            SEVERE: 3600000,     // 1 hour
            MAJOR: 86400000      // 24 hours
        },
        AUTO_MODERATION: {
            SPAM_THRESHOLD: 5,
            CAPS_THRESHOLD: 0.7,
            REPEAT_THRESHOLD: 3
        }
    },
    PERMISSIONS: {
        MUTE: 'mute',
        KICK: 'kick',
        BAN: 'ban',
        MODERATE: 'moderate',
        ANNOUNCE: 'announce',
        ADMIN: 'admin'
    }
};

class ChatManager extends EventEmitter {
    constructor(gameEngine) {
        super();
        this.gameEngine = gameEngine;
        this.channels = new Map();
        this.privateConversations = new Map(); // playerId -> Map<targetId, conversation>
        this.playerMutes = new Map(); // playerId -> muteData
        this.playerChannels = new Map(); // playerId -> [channels]
        this.messageHistory = new Map(); // channelId -> [messages]
        this.chatFilters = new Map(); // playerId -> filters
        this.moderationLog = [];
        this.bannedWords = new Set();
        this.chatCommands = new Map();
        
        this.initializeChannels();
        this.initializeChatCommands();
        this.initializeBannedWords();
        this.setupEventListeners();
        this.startChatUpdates();
    }

    initializeChannels() {
        // Global channel
        this.channels.set(CHAT_CONFIG.CHANNELS.GLOBAL, {
            id: CHAT_CONFIG.CHANNELS.GLOBAL,
            name: 'Global',
            description: 'Global chat for all players',
            type: 'public',
            persistent: true,
            moderated: true,
            levelRequirement: 1,
            permissions: [],
            members: new Set(),
            moderators: new Set(),
            settings: {
                allowEmotes: true,
                allowLinks: false,
                slowMode: 0,
                maxMembers: -1
            }
        });

        // Local/Area chat
        this.channels.set(CHAT_CONFIG.CHANNELS.LOCAL, {
            id: CHAT_CONFIG.CHANNELS.LOCAL,
            name: 'Local',
            description: 'Chat with nearby players',
            type: 'proximity',
            persistent: false,
            moderated: false,
            levelRequirement: 1,
            permissions: [],
            members: new Set(),
            moderators: new Set(),
            settings: {
                allowEmotes: true,
                allowLinks: true,
                slowMode: 0,
                range: 100
            }
        });

        // Trade channel
        this.channels.set(CHAT_CONFIG.CHANNELS.TRADE, {
            id: CHAT_CONFIG.CHANNELS.TRADE,
            name: 'Trade',
            description: 'Buy, sell, and trade items',
            type: 'public',
            persistent: true,
            moderated: true,
            levelRequirement: 5,
            permissions: [],
            members: new Set(),
            moderators: new Set(),
            settings: {
                allowEmotes: false,
                allowLinks: false,
                slowMode: 30000, // 30 seconds
                maxMembers: -1
            }
        });

        // Help channel
        this.channels.set(CHAT_CONFIG.CHANNELS.HELP, {
            id: CHAT_CONFIG.CHANNELS.HELP,
            name: 'Help',
            description: 'Get help from other players and moderators',
            type: 'public',
            persistent: true,
            moderated: true,
            levelRequirement: 1,
            permissions: [],
            members: new Set(),
            moderators: new Set(),
            settings: {
                allowEmotes: true,
                allowLinks: true,
                slowMode: 0,
                maxMembers: -1
            }
        });

        // System channel
        this.channels.set(CHAT_CONFIG.CHANNELS.SYSTEM, {
            id: CHAT_CONFIG.CHANNELS.SYSTEM,
            name: 'System',
            description: 'System messages and announcements',
            type: 'system',
            persistent: true,
            moderated: false,
            levelRequirement: 1,
            permissions: [CHAT_CONFIG.PERMISSIONS.ADMIN],
            members: new Set(),
            moderators: new Set(),
            settings: {
                allowEmotes: false,
                allowLinks: false,
                slowMode: 0,
                readOnly: true
            }
        });

        // Initialize message history for each channel
        for (const channelId of this.channels.keys()) {
            this.messageHistory.set(channelId, []);
        }
    }

    initializeChatCommands() {
        this.chatCommands.set('/help', {
            name: 'help',
            description: 'Show available commands',
            usage: '/help [command]',
            permissions: [],
            handler: this.handleHelpCommand.bind(this)
        });

        this.chatCommands.set('/whisper', {
            name: 'whisper',
            description: 'Send a private message',
            usage: '/whisper <player> <message>',
            aliases: ['/w', '/tell', '/pm'],
            permissions: [],
            handler: this.handleWhisperCommand.bind(this)
        });

        this.chatCommands.set('/who', {
            name: 'who',
            description: 'List online players',
            usage: '/who [channel]',
            permissions: [],
            handler: this.handleWhoCommand.bind(this)
        });

        this.chatCommands.set('/join', {
            name: 'join',
            description: 'Join a chat channel',
            usage: '/join <channel>',
            permissions: [],
            handler: this.handleJoinCommand.bind(this)
        });

        this.chatCommands.set('/leave', {
            name: 'leave',
            description: 'Leave a chat channel',
            usage: '/leave <channel>',
            permissions: [],
            handler: this.handleLeaveCommand.bind(this)
        });

        this.chatCommands.set('/mute', {
            name: 'mute',
            description: 'Mute a player',
            usage: '/mute <player> [duration] [reason]',
            permissions: [CHAT_CONFIG.PERMISSIONS.MODERATE],
            handler: this.handleMuteCommand.bind(this)
        });

        this.chatCommands.set('/unmute', {
            name: 'unmute',
            description: 'Unmute a player',
            usage: '/unmute <player>',
            permissions: [CHAT_CONFIG.PERMISSIONS.MODERATE],
            handler: this.handleUnmuteCommand.bind(this)
        });

        this.chatCommands.set('/kick', {
            name: 'kick',
            description: 'Kick a player from channel',
            usage: '/kick <player> [reason]',
            permissions: [CHAT_CONFIG.PERMISSIONS.KICK],
            handler: this.handleKickCommand.bind(this)
        });

        this.chatCommands.set('/announce', {
            name: 'announce',
            description: 'Send server announcement',
            usage: '/announce <message>',
            permissions: [CHAT_CONFIG.PERMISSIONS.ANNOUNCE],
            handler: this.handleAnnounceCommand.bind(this)
        });

        this.chatCommands.set('/me', {
            name: 'me',
            description: 'Send an emote action',
            usage: '/me <action>',
            permissions: [],
            handler: this.handleEmoteCommand.bind(this)
        });
    }

    initializeBannedWords() {
        // Add common inappropriate words to filter
        const commonBannedWords = [
            // Add actual banned words here based on your community guidelines
            'spam', 'scam', 'hack', 'cheat', 'bot'
        ];
        
        for (const word of commonBannedWords) {
            this.bannedWords.add(word.toLowerCase());
        }
    }

    setupEventListeners() {
        this.gameEngine.on('playerConnected', this.handlePlayerConnected.bind(this));
        this.gameEngine.on('playerDisconnected', this.handlePlayerDisconnected.bind(this));
        this.gameEngine.on('playerLocationChanged', this.handlePlayerLocationChanged.bind(this));
        this.gameEngine.on('guildJoined', this.handleGuildJoined.bind(this));
        this.gameEngine.on('guildLeft', this.handleGuildLeft.bind(this));
    }

    startChatUpdates() {
        // Clean up old messages and expired mutes
        setInterval(() => {
            this.cleanupOldMessages();
            this.processExpiredMutes();
        }, 60000); // Every minute

        // Process moderation queue
        setInterval(() => {
            this.processAutoModeration();
        }, 10000); // Every 10 seconds
    }

    // Core Chat Functions
    async sendMessage(playerId, channelId, content, messageType = CHAT_CONFIG.MESSAGE_TYPES.CHAT) {
        try {
            const player = await this.gameEngine.getPlayer(playerId);
            if (!player) {
                throw new Error('Player not found');
            }

            // Check if player is muted
            if (this.isPlayerMuted(playerId)) {
                const muteData = this.playerMutes.get(playerId);
                throw new Error(`You are muted until ${new Date(muteData.expiresAt).toLocaleString()}`);
            }

            // Check rate limiting
            if (!this.checkRateLimit(playerId, channelId)) {
                throw new Error('You are sending messages too quickly');
            }

            // Validate message content
            const processedContent = this.processMessageContent(content);
            if (!processedContent) {
                throw new Error('Message content is invalid');
            }

            // Check channel permissions
            const channel = this.channels.get(channelId);
            if (!channel) {
                throw new Error('Channel not found');
            }

            if (!this.canSendToChannel(playerId, channel)) {
                throw new Error('You do not have permission to send messages to this channel');
            }

            // Create message object
            const message = {
                id: uuidv4(),
                playerId,
                playerName: player.username,
                channelId,
                content: processedContent,
                type: messageType,
                timestamp: new Date(),
                edited: false,
                reactions: new Map(),
                metadata: {
                    playerLevel: player.level,
                    playerClass: player.class,
                    guild: player.guild?.name || null
                }
            };

            // Handle different channel types
            await this.deliverMessage(message, channel);

            // Add to message history
            this.addToHistory(channelId, message);

            // Update rate limiting
            this.updateRateLimit(playerId, channelId);

            this.emit('messageSent', { message, channel });
            return message;

        } catch (error) {
            throw error;
        }
    }

    async sendPrivateMessage(senderId, targetId, content) {
        try {
            const sender = await this.gameEngine.getPlayer(senderId);
            const target = await this.gameEngine.getPlayer(targetId);

            if (!sender || !target) {
                throw new Error('Player not found');
            }

            // Check if sender is muted
            if (this.isPlayerMuted(senderId)) {
                throw new Error('You are muted and cannot send private messages');
            }

            // Check if target has blocked sender
            if (this.isPlayerBlocked(targetId, senderId)) {
                throw new Error('You cannot send messages to this player');
            }

            // Check private message rate limiting
            if (!this.checkPrivateRateLimit(senderId)) {
                throw new Error('You are sending private messages too quickly');
            }

            const processedContent = this.processMessageContent(content);
            if (!processedContent) {
                throw new Error('Message content is invalid');
            }

            const message = {
                id: uuidv4(),
                senderId,
                senderName: sender.username,
                targetId,
                targetName: target.username,
                content: processedContent,
                type: CHAT_CONFIG.MESSAGE_TYPES.WHISPER,
                timestamp: new Date(),
                read: false
            };

            // Store in private conversation
            this.addToPrivateConversation(senderId, targetId, message);

            // Send to both players if online
            this.deliverPrivateMessage(message);

            this.emit('privateMessageSent', { message });
            return message;

        } catch (error) {
            throw error;
        }
    }

    processMessageContent(content) {
        if (!content || typeof content !== 'string') {
            return null;
        }

        // Trim and check length
        content = content.trim();
        if (content.length === 0 || content.length > CHAT_CONFIG.CHAT_LIMITS.MESSAGE_LENGTH) {
            return null;
        }

        // Filter banned words
        content = this.filterBannedWords(content);

        // Basic sanitization
        content = this.sanitizeMessage(content);

        return content;
    }

    filterBannedWords(content) {
        let filteredContent = content;
        
        for (const bannedWord of this.bannedWords) {
            const regex = new RegExp(`\\b${bannedWord}\\b`, 'gi');
            filteredContent = filteredContent.replace(regex, '*'.repeat(bannedWord.length));
        }
        
        return filteredContent;
    }

    sanitizeMessage(content) {
        // Remove potentially harmful content
        return content
            .replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '[SCRIPT REMOVED]')
            .replace(/<[^>]*>/g, '') // Remove HTML tags
            .replace(/javascript:/gi, '') // Remove javascript: URLs
            .trim();
    }

    async deliverMessage(message, channel) {
        const recipients = new Set();

        switch (channel.type) {
            case 'public':
                // Send to all channel members
                for (const memberId of channel.members) {
                    recipients.add(memberId);
                }
                break;

            case 'proximity':
                // Send to nearby players
                const nearbyPlayers = await this.getNearbyPlayers(
                    message.playerId, 
                    channel.settings.range || 100
                );
                for (const playerId of nearbyPlayers) {
                    recipients.add(playerId);
                }
                break;

            case 'guild':
                // Send to guild members
                const guild = this.gameEngine.guildManager.getPlayerGuild(message.playerId);
                if (guild) {
                    for (const memberId of guild.members.keys()) {
                        recipients.add(memberId);
                    }
                }
                break;

            case 'party':
                // Send to party members
                const party = this.gameEngine.partyManager.getPlayerParty(message.playerId);
                if (party) {
                    for (const memberId of party.members) {
                        recipients.add(memberId);
                    }
                }
                break;

            case 'system':
                // Send to all online players
                const onlinePlayers = this.gameEngine.playerManager.getOnlinePlayers();
                for (const playerId of onlinePlayers) {
                    recipients.add(playerId);
                }
                break;
        }

        // Send message to recipients
        for (const recipientId of recipients) {
            this.sendMessageToPlayer(recipientId, message);
        }
    }

    sendMessageToPlayer(playerId, message) {
        const socket = this.gameEngine.playerManager.getPlayerSocket(playerId);
        if (socket) {
            socket.emit('chat_message', {
                message,
                channel: this.channels.get(message.channelId)
            });
        }
    }

    deliverPrivateMessage(message) {
        // Send to sender
        const senderSocket = this.gameEngine.playerManager.getPlayerSocket(message.senderId);
        if (senderSocket) {
            senderSocket.emit('private_message', {
                message,
                type: 'sent'
            });
        }

        // Send to target
        const targetSocket = this.gameEngine.playerManager.getPlayerSocket(message.targetId);
        if (targetSocket) {
            targetSocket.emit('private_message', {
                message,
                type: 'received'
            });
        }
    }

    // Channel Management
    async joinChannel(playerId, channelId) {
        const player = await this.gameEngine.getPlayer(playerId);
        const channel = this.channels.get(channelId);

        if (!player || !channel) {
            throw new Error('Player or channel not found');
        }

        // Check permissions
        if (!this.canJoinChannel(playerId, channel)) {
            throw new Error('You cannot join this channel');
        }

        // Check level requirement
        if (player.level < channel.levelRequirement) {
            throw new Error(`You need to be level ${channel.levelRequirement} to join this channel`);
        }

        // Check max members
        if (channel.settings.maxMembers > 0 && channel.members.size >= channel.settings.maxMembers) {
            throw new Error('Channel is full');
        }

        // Add to channel
        channel.members.add(playerId);

        // Add to player's channel list
        if (!this.playerChannels.has(playerId)) {
            this.playerChannels.set(playerId, new Set());
        }
        this.playerChannels.get(playerId).add(channelId);

        this.emit('playerJoinedChannel', { playerId, channelId });
        return true;
    }

    async leaveChannel(playerId, channelId) {
        const channel = this.channels.get(channelId);
        if (!channel) {
            throw new Error('Channel not found');
        }

        // Remove from channel
        channel.members.delete(playerId);

        // Remove from player's channel list
        if (this.playerChannels.has(playerId)) {
            this.playerChannels.get(playerId).delete(channelId);
        }

        this.emit('playerLeftChannel', { playerId, channelId });
        return true;
    }

    canJoinChannel(playerId, channel) {
        // Check if channel requires special permissions
        if (channel.permissions.length > 0) {
            return this.hasPermissions(playerId, channel.permissions);
        }
        return true;
    }

    canSendToChannel(playerId, channel) {
        // Check if player is in channel (for non-proximity channels)
        if (channel.type !== 'proximity' && !channel.members.has(playerId)) {
            return false;
        }

        // Check if channel is read-only
        if (channel.settings.readOnly) {
            return this.hasPermissions(playerId, [CHAT_CONFIG.PERMISSIONS.ADMIN]);
        }

        return true;
    }

    // Rate Limiting
    checkRateLimit(playerId, channelId) {
        const now = Date.now();
        const key = `${playerId}_${channelId}`;
        
        if (!this.rateLimits) {
            this.rateLimits = new Map();
        }

        const limit = this.rateLimits.get(key) || { count: 0, resetTime: now + 60000 };
        
        if (now > limit.resetTime) {
            limit.count = 0;
            limit.resetTime = now + 60000;
        }

        return limit.count < CHAT_CONFIG.CHAT_LIMITS.MESSAGES_PER_MINUTE;
    }

    updateRateLimit(playerId, channelId) {
        const key = `${playerId}_${channelId}`;
        
        if (!this.rateLimits) {
            this.rateLimits = new Map();
        }

        const now = Date.now();
        const limit = this.rateLimits.get(key) || { count: 0, resetTime: now + 60000 };
        
        limit.count++;
        this.rateLimits.set(key, limit);
    }

    checkPrivateRateLimit(playerId) {
        const now = Date.now();
        const key = `private_${playerId}`;
        
        if (!this.privateRateLimits) {
            this.privateRateLimits = new Map();
        }

        const limit = this.privateRateLimits.get(key) || { count: 0, resetTime: now + 60000 };
        
        if (now > limit.resetTime) {
            limit.count = 0;
            limit.resetTime = now + 60000;
        }

        if (limit.count < CHAT_CONFIG.CHAT_LIMITS.PRIVATE_MESSAGES_PER_MINUTE) {
            limit.count++;
            this.privateRateLimits.set(key, limit);
            return true;
        }

        return false;
    }

    // Moderation
    mutePlayer(playerId, targetId, duration, reason = '') {
        const muteData = {
            mutedBy: playerId,
            reason,
            mutedAt: new Date(),
            expiresAt: new Date(Date.now() + duration),
            duration
        };

        this.playerMutes.set(targetId, muteData);

        this.moderationLog.push({
            action: 'mute',
            moderator: playerId,
            target: targetId,
            reason,
            duration,
            timestamp: new Date()
        });

        this.emit('playerMuted', { targetId, muteData });
        return muteData;
    }

    unmutePlayer(playerId, targetId) {
        if (!this.playerMutes.has(targetId)) {
            throw new Error('Player is not muted');
        }

        this.playerMutes.delete(targetId);

        this.moderationLog.push({
            action: 'unmute',
            moderator: playerId,
            target: targetId,
            timestamp: new Date()
        });

        this.emit('playerUnmuted', { targetId });
        return true;
    }

    isPlayerMuted(playerId) {
        const muteData = this.playerMutes.get(playerId);
        if (!muteData) return false;

        if (Date.now() > muteData.expiresAt.getTime()) {
            this.playerMutes.delete(playerId);
            return false;
        }

        return true;
    }

    isPlayerBlocked(playerId, blockerId) {
        // This would integrate with the social system
        // For now, return false
        return false;
    }

    hasPermissions(playerId, permissions) {
        // This would integrate with the player permissions system
        // For now, return true for basic permissions
        return permissions.length === 0;
    }

    // Chat Commands
    async handleChatCommand(playerId, commandText) {
        const parts = commandText.split(' ');
        const command = parts[0].toLowerCase();
        const args = parts.slice(1);

        const commandData = this.chatCommands.get(command);
        if (!commandData) {
            throw new Error('Unknown command');
        }

        // Check permissions
        if (commandData.permissions.length > 0 && !this.hasPermissions(playerId, commandData.permissions)) {
            throw new Error('You do not have permission to use this command');
        }

        return await commandData.handler(playerId, args);
    }

    async handleHelpCommand(playerId, args) {
        const availableCommands = Array.from(this.chatCommands.values())
            .filter(cmd => cmd.permissions.length === 0 || this.hasPermissions(playerId, cmd.permissions));

        if (args.length > 0) {
            const specificCommand = this.chatCommands.get(`/${args[0]}`);
            if (specificCommand) {
                return {
                    type: 'command_response',
                    content: `${specificCommand.usage}\n${specificCommand.description}`
                };
            }
        }

        const commandList = availableCommands.map(cmd => cmd.usage).join('\n');
        return {
            type: 'command_response',
            content: `Available commands:\n${commandList}`
        };
    }

    async handleWhisperCommand(playerId, args) {
        if (args.length < 2) {
            throw new Error('Usage: /whisper <player> <message>');
        }

        const targetName = args[0];
        const message = args.slice(1).join(' ');

        const target = await this.gameEngine.playerManager.findPlayerByName(targetName);
        if (!target) {
            throw new Error('Player not found');
        }

        await this.sendPrivateMessage(playerId, target.id, message);
        return { type: 'command_success', content: `Message sent to ${targetName}` };
    }

    async handleWhoCommand(playerId, args) {
        const channelId = args[0] || CHAT_CONFIG.CHANNELS.GLOBAL;
        const channel = this.channels.get(channelId);
        
        if (!channel) {
            throw new Error('Channel not found');
        }

        const memberCount = channel.members.size;
        return {
            type: 'command_response',
            content: `${channel.name}: ${memberCount} players online`
        };
    }

    async handleJoinCommand(playerId, args) {
        if (args.length === 0) {
            throw new Error('Usage: /join <channel>');
        }

        const channelId = args[0];
        await this.joinChannel(playerId, channelId);
        return { type: 'command_success', content: `Joined channel: ${channelId}` };
    }

    async handleLeaveCommand(playerId, args) {
        if (args.length === 0) {
            throw new Error('Usage: /leave <channel>');
        }

        const channelId = args[0];
        await this.leaveChannel(playerId, channelId);
        return { type: 'command_success', content: `Left channel: ${channelId}` };
    }

    async handleMuteCommand(playerId, args) {
        if (args.length < 1) {
            throw new Error('Usage: /mute <player> [duration] [reason]');
        }

        const targetName = args[0];
        const duration = args[1] ? parseInt(args[1]) * 60000 : CHAT_CONFIG.MODERATION.MUTE_DURATIONS.MINOR;
        const reason = args.slice(2).join(' ') || 'No reason provided';

        const target = await this.gameEngine.playerManager.findPlayerByName(targetName);
        if (!target) {
            throw new Error('Player not found');
        }

        this.mutePlayer(playerId, target.id, duration, reason);
        return { 
            type: 'command_success', 
            content: `${targetName} has been muted for ${duration / 60000} minutes` 
        };
    }

    async handleUnmuteCommand(playerId, args) {
        if (args.length === 0) {
            throw new Error('Usage: /unmute <player>');
        }

        const targetName = args[0];
        const target = await this.gameEngine.playerManager.findPlayerByName(targetName);
        if (!target) {
            throw new Error('Player not found');
        }

        this.unmutePlayer(playerId, target.id);
        return { type: 'command_success', content: `${targetName} has been unmuted` };
    }

    async handleKickCommand(playerId, args) {
        // Implementation for kicking players from channels
        return { type: 'command_success', content: 'Kick command executed' };
    }

    async handleAnnounceCommand(playerId, args) {
        if (args.length === 0) {
            throw new Error('Usage: /announce <message>');
        }

        const message = args.join(' ');
        await this.sendSystemAnnouncement(message);
        return { type: 'command_success', content: 'Announcement sent' };
    }

    async handleEmoteCommand(playerId, args) {
        if (args.length === 0) {
            throw new Error('Usage: /me <action>');
        }

        const action = args.join(' ');
        const player = await this.gameEngine.getPlayer(playerId);
        
        // Send as emote to current channel (local by default)
        await this.sendMessage(
            playerId, 
            CHAT_CONFIG.CHANNELS.LOCAL, 
            `${player.username} ${action}`, 
            CHAT_CONFIG.MESSAGE_TYPES.EMOTE
        );
        
        return { type: 'command_success' };
    }

    // Utility Methods
    async sendSystemAnnouncement(message) {
        const announcement = {
            id: uuidv4(),
            playerId: 'system',
            playerName: 'System',
            channelId: CHAT_CONFIG.CHANNELS.SYSTEM,
            content: message,
            type: CHAT_CONFIG.MESSAGE_TYPES.ANNOUNCEMENT,
            timestamp: new Date(),
            metadata: {}
        };

        // Send to all online players
        const onlinePlayers = this.gameEngine.playerManager.getOnlinePlayers();
        for (const playerId of onlinePlayers) {
            this.sendMessageToPlayer(playerId, announcement);
        }

        this.addToHistory(CHAT_CONFIG.CHANNELS.SYSTEM, announcement);
        this.emit('systemAnnouncement', { announcement });
    }

    async getNearbyPlayers(playerId, range) {
        // This would integrate with the world/position system
        // For now, return all online players
        return this.gameEngine.playerManager.getOnlinePlayers();
    }

    addToHistory(channelId, message) {
        if (!this.messageHistory.has(channelId)) {
            this.messageHistory.set(channelId, []);
        }

        const history = this.messageHistory.get(channelId);
        history.push(message);

        // Limit history size
        const maxHistory = CHAT_CONFIG.CHAT_LIMITS.CHANNEL_HISTORY;
        if (history.length > maxHistory) {
            history.splice(0, history.length - maxHistory);
        }
    }

    addToPrivateConversation(senderId, targetId, message) {
        if (!this.privateConversations.has(senderId)) {
            this.privateConversations.set(senderId, new Map());
        }
        if (!this.privateConversations.has(targetId)) {
            this.privateConversations.set(targetId, new Map());
        }

        const senderConversations = this.privateConversations.get(senderId);
        const targetConversations = this.privateConversations.get(targetId);

        if (!senderConversations.has(targetId)) {
            senderConversations.set(targetId, []);
        }
        if (!targetConversations.has(senderId)) {
            targetConversations.set(senderId, []);
        }

        senderConversations.get(targetId).push(message);
        targetConversations.get(senderId).push(message);

        // Limit conversation history
        const maxHistory = CHAT_CONFIG.CHAT_LIMITS.PRIVATE_HISTORY;
        const senderHistory = senderConversations.get(targetId);
        const targetHistory = targetConversations.get(senderId);

        if (senderHistory.length > maxHistory) {
            senderHistory.splice(0, senderHistory.length - maxHistory);
        }
        if (targetHistory.length > maxHistory) {
            targetHistory.splice(0, targetHistory.length - maxHistory);
        }
    }

    cleanupOldMessages() {
        // Clean up old message history
        const cutoffTime = Date.now() - (24 * 60 * 60 * 1000); // 24 hours ago

        for (const [channelId, messages] of this.messageHistory.entries()) {
            const validMessages = messages.filter(msg => msg.timestamp.getTime() > cutoffTime);
            this.messageHistory.set(channelId, validMessages);
        }
    }

    processExpiredMutes() {
        const now = Date.now();
        const expiredMutes = [];

        for (const [playerId, muteData] of this.playerMutes.entries()) {
            if (now > muteData.expiresAt.getTime()) {
                expiredMutes.push(playerId);
            }
        }

        for (const playerId of expiredMutes) {
            this.playerMutes.delete(playerId);
            this.emit('muteExpired', { playerId });
        }
    }

    processAutoModeration() {
        // Implement automatic moderation checks
        // This could include spam detection, inappropriate content, etc.
    }

    // Event Handlers
    handlePlayerConnected(data) {
        const { playerId } = data;
        
        // Auto-join default channels
        this.joinChannel(playerId, CHAT_CONFIG.CHANNELS.GLOBAL).catch(() => {});
        this.joinChannel(playerId, CHAT_CONFIG.CHANNELS.LOCAL).catch(() => {});
    }

    handlePlayerDisconnected(data) {
        const { playerId } = data;
        
        // Remove from all channels
        for (const channel of this.channels.values()) {
            channel.members.delete(playerId);
        }
        
        this.playerChannels.delete(playerId);
    }

    handlePlayerLocationChanged(data) {
        // Update local chat participants based on location
    }

    handleGuildJoined(data) {
        const { playerId, guildId } = data;
        // Auto-join guild channel if it exists
    }

    handleGuildLeft(data) {
        const { playerId, guildId } = data;
        // Remove from guild channel
    }

    // Public API Methods
    getChannelHistory(channelId, limit = 50) {
        const history = this.messageHistory.get(channelId) || [];
        return history.slice(-limit);
    }

    getPrivateConversation(playerId, targetId) {
        const conversations = this.privateConversations.get(playerId);
        if (!conversations) return [];
        
        return conversations.get(targetId) || [];
    }

    getPlayerChannels(playerId) {
        return Array.from(this.playerChannels.get(playerId) || []);
    }

    getAvailableChannels(playerId) {
        return Array.from(this.channels.values())
            .filter(channel => this.canJoinChannel(playerId, channel));
    }

    getChatStatistics() {
        const totalMessages = Array.from(this.messageHistory.values())
            .reduce((sum, history) => sum + history.length, 0);
        
        const activeChannels = Array.from(this.channels.values())
            .filter(channel => channel.members.size > 0).length;
        
        return {
            totalChannels: this.channels.size,
            activeChannels,
            totalMessages,
            activeMutes: this.playerMutes.size,
            totalModerationActions: this.moderationLog.length
        };
    }
}

module.exports = ChatManager;