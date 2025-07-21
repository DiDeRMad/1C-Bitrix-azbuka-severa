/**
 * Super Online Game - Main Server Entry Point
 * Epic MMORPG with real-time multiplayer, crafting, trading, and PvP
 */

require('dotenv').config();

const express = require('express');
const http = require('http');
const socketIo = require('socket.io');
const cors = require('cors');
const helmet = require('helmet');
const compression = require('compression');
const path = require('path');
const mongoose = require('mongoose');

// Core game systems
const GameEngine = require('./core/GameEngine');
const PlayerManager = require('./managers/PlayerManager');
const WorldManager = require('./managers/WorldManager');
const CombatManager = require('./managers/CombatManager');
const EconomyManager = require('./managers/EconomyManager');
const GuildManager = require('./managers/GuildManager');
const QuestManager = require('./managers/QuestManager');
const CraftingManager = require('./managers/CraftingManager');
const InventoryManager = require('./managers/InventoryManager');
const ChatManager = require('./managers/ChatManager');
const EventManager = require('./managers/EventManager');
const SecurityManager = require('./managers/SecurityManager');
const AdminManager = require('./managers/AdminManager');

// Services
const DatabaseService = require('./services/DatabaseService');
const RedisService = require('./services/RedisService');
const EmailService = require('./services/EmailService');
const FileService = require('./services/FileService');
const LoggingService = require('./services/LoggingService');
const MetricsService = require('./services/MetricsService');
const BackupService = require('./services/BackupService');

// Routes
const authRoutes = require('./routes/auth');
const playerRoutes = require('./routes/player');
const gameRoutes = require('./routes/game');
const adminRoutes = require('./routes/admin');
const economyRoutes = require('./routes/economy');
const guildRoutes = require('./routes/guild');
const questRoutes = require('./routes/quest');
const leaderboardRoutes = require('./routes/leaderboard');

// Middleware
const authMiddleware = require('./middleware/auth');
const rateLimitMiddleware = require('./middleware/rateLimit');
const validationMiddleware = require('./middleware/validation');
const errorMiddleware = require('./middleware/error');

// Constants
const PORT = process.env.PORT || 3001;
const NODE_ENV = process.env.NODE_ENV || 'development';
const MONGODB_URI = process.env.MONGODB_URI || 'mongodb://localhost:27017/super-online-game';
const REDIS_URI = process.env.REDIS_URI || 'redis://localhost:6379';

class SuperOnlineGameServer {
    constructor() {
        this.app = express();
        this.server = http.createServer(this.app);
        this.io = socketIo(this.server, {
            cors: {
                origin: process.env.CLIENT_URL || "http://localhost:3000",
                methods: ["GET", "POST"],
                credentials: true
            },
            pingTimeout: 60000,
            pingInterval: 25000
        });

        this.connectedPlayers = new Map();
        this.gameRooms = new Map();
        this.gameEngine = null;
        
        this.initializeServices();
        this.initializeGameSystems();
        this.setupMiddleware();
        this.setupRoutes();
        this.setupSocketHandlers();
        this.setupErrorHandling();
    }

    async initializeServices() {
        try {
            // Initialize logging first
            this.logger = LoggingService.getInstance();
            this.logger.info('Starting Super Online Game Server...');

            // Initialize database connections
            await DatabaseService.connect(MONGODB_URI);
            await RedisService.connect(REDIS_URI);

            // Initialize other services
            await EmailService.initialize();
            await FileService.initialize();
            await MetricsService.initialize();
            await BackupService.initialize();

            this.logger.info('All services initialized successfully');
        } catch (error) {
            console.error('Failed to initialize services:', error);
            process.exit(1);
        }
    }

    initializeGameSystems() {
        // Initialize core game systems
        this.gameEngine = new GameEngine();
        this.playerManager = new PlayerManager(this.gameEngine);
        this.worldManager = new WorldManager(this.gameEngine);
        this.combatManager = new CombatManager(this.gameEngine);
        this.economyManager = new EconomyManager(this.gameEngine);
        this.guildManager = new GuildManager(this.gameEngine);
        this.questManager = new QuestManager(this.gameEngine);
        this.craftingManager = new CraftingManager(this.gameEngine);
        this.inventoryManager = new InventoryManager(this.gameEngine);
        this.chatManager = new ChatManager(this.gameEngine);

        // Cross-reference managers for inter-system communication
        this.gameEngine.setManagers({
            playerManager: this.playerManager,
            worldManager: this.worldManager,
            combatManager: this.combatManager,
            economyManager: this.economyManager,
            guildManager: this.guildManager,
            questManager: this.questManager,
            craftingManager: this.craftingManager,
            inventoryManager: this.inventoryManager,
            chatManager: this.chatManager
        });

        this.logger.info('Game systems initialized');
    }

    setupMiddleware() {
        // Security middleware
        this.app.use(helmet({
            contentSecurityPolicy: {
                directives: {
                    defaultSrc: ["'self'"],
                    scriptSrc: ["'self'", "'unsafe-inline'"],
                    styleSrc: ["'self'", "'unsafe-inline'"],
                    imgSrc: ["'self'", "data:", "https:"],
                    connectSrc: ["'self'", "ws:", "wss:"]
                }
            }
        }));

        // CORS
        this.app.use(cors({
            origin: process.env.CLIENT_URL || "http://localhost:3000",
            credentials: true
        }));

        // Compression
        this.app.use(compression());

        // Body parsing
        this.app.use(express.json({ limit: '10mb' }));
        this.app.use(express.urlencoded({ extended: true, limit: '10mb' }));

        // Rate limiting
        this.app.use(rateLimitMiddleware);

        // Static files for client
        if (NODE_ENV === 'production') {
            this.app.use(express.static(path.join(__dirname, '../client/build')));
        }

        // Request logging
        this.app.use((req, res, next) => {
            this.logger.info(`${req.method} ${req.url}`, {
                ip: req.ip,
                userAgent: req.get('User-Agent')
            });
            next();
        });
    }

    setupRoutes() {
        // API routes
        this.app.use('/api/auth', authRoutes);
        this.app.use('/api/player', authMiddleware, playerRoutes);
        this.app.use('/api/game', authMiddleware, gameRoutes);
        this.app.use('/api/economy', authMiddleware, economyRoutes);
        this.app.use('/api/guild', authMiddleware, guildRoutes);
        this.app.use('/api/quest', authMiddleware, questRoutes);
        this.app.use('/api/leaderboard', leaderboardRoutes);
        this.app.use('/api/admin', authMiddleware, adminRoutes);

        // Health check
        this.app.get('/health', (req, res) => {
            res.json({
                status: 'OK',
                timestamp: new Date().toISOString(),
                uptime: process.uptime(),
                memory: process.memoryUsage(),
                env: NODE_ENV,
                connectedPlayers: this.connectedPlayers.size,
                activeRooms: this.gameRooms.size
            });
        });

        // Serve client app for all other routes (SPA)
        if (NODE_ENV === 'production') {
            this.app.get('*', (req, res) => {
                res.sendFile(path.join(__dirname, '../client/build/index.html'));
            });
        }
    }

    setupSocketHandlers() {
        this.io.use(async (socket, next) => {
            try {
                // Authenticate socket connection
                const token = socket.handshake.auth.token;
                const player = await this.securityManager.authenticateSocket(token);
                socket.playerId = player.id;
                socket.playerData = player;
                next();
            } catch (error) {
                next(new Error('Authentication failed'));
            }
        });

        this.io.on('connection', (socket) => {
            this.handlePlayerConnection(socket);
        });
    }

    async handlePlayerConnection(socket) {
        const playerId = socket.playerId;
        const playerData = socket.playerData;

        try {
            // Add player to connected players
            this.connectedPlayers.set(playerId, {
                socket,
                playerData,
                connectedAt: new Date(),
                lastActivity: new Date()
            });

            // Initialize player in game world
            await this.playerManager.playerConnected(playerId, socket);
            await this.worldManager.addPlayerToWorld(playerId, playerData);

            this.logger.info(`Player ${playerData.username} connected`, { playerId });

            // Socket event handlers
            this.setupPlayerSocketHandlers(socket, playerId);

            // Handle disconnect
            socket.on('disconnect', () => {
                this.handlePlayerDisconnection(playerId);
            });

        } catch (error) {
            this.logger.error('Error handling player connection:', error);
            socket.emit('error', { message: 'Connection failed' });
            socket.disconnect();
        }
    }

    setupPlayerSocketHandlers(socket, playerId) {
        // Player actions
        socket.on('player:move', (data) => this.playerManager.handleMove(playerId, data));
        socket.on('player:action', (data) => this.playerManager.handleAction(playerId, data));
        socket.on('player:interact', (data) => this.worldManager.handleInteraction(playerId, data));
        socket.on('player:teleport', (data) => this.worldManager.handleTeleport(playerId, data));

        // Combat actions
        socket.on('combat:attack', (data) => this.combatManager.handleAttack(playerId, data));
        socket.on('combat:defend', (data) => this.combatManager.handleDefend(playerId, data));
        socket.on('combat:useSkill', (data) => this.combatManager.handleSkillUse(playerId, data));
        socket.on('combat:flee', (data) => this.combatManager.handleFlee(playerId, data));

        // Inventory actions
        socket.on('inventory:useItem', (data) => this.inventoryManager.handleUseItem(playerId, data));
        socket.on('inventory:dropItem', (data) => this.inventoryManager.handleDropItem(playerId, data));
        socket.on('inventory:equipItem', (data) => this.inventoryManager.handleEquipItem(playerId, data));
        socket.on('inventory:unequipItem', (data) => this.inventoryManager.handleUnequipItem(playerId, data));

        // Crafting actions
        socket.on('crafting:startCraft', (data) => this.craftingManager.handleStartCraft(playerId, data));
        socket.on('crafting:cancelCraft', (data) => this.craftingManager.handleCancelCraft(playerId, data));
        socket.on('crafting:learnRecipe', (data) => this.craftingManager.handleLearnRecipe(playerId, data));

        // Economy actions
        socket.on('economy:trade', (data) => this.economyManager.handleTrade(playerId, data));
        socket.on('economy:marketBuy', (data) => this.economyManager.handleMarketBuy(playerId, data));
        socket.on('economy:marketSell', (data) => this.economyManager.handleMarketSell(playerId, data));
        socket.on('economy:auction', (data) => this.economyManager.handleAuction(playerId, data));

        // Guild actions
        socket.on('guild:create', (data) => this.guildManager.handleCreateGuild(playerId, data));
        socket.on('guild:join', (data) => this.guildManager.handleJoinGuild(playerId, data));
        socket.on('guild:leave', (data) => this.guildManager.handleLeaveGuild(playerId, data));
        socket.on('guild:invite', (data) => this.guildManager.handleInvitePlayer(playerId, data));
        socket.on('guild:promote', (data) => this.guildManager.handlePromotePlayer(playerId, data));

        // Quest actions
        socket.on('quest:accept', (data) => this.questManager.handleAcceptQuest(playerId, data));
        socket.on('quest:complete', (data) => this.questManager.handleCompleteQuest(playerId, data));
        socket.on('quest:abandon', (data) => this.questManager.handleAbandonQuest(playerId, data));

        // Chat actions
        socket.on('chat:message', (data) => this.chatManager.handleMessage(playerId, data));
        socket.on('chat:whisper', (data) => this.chatManager.handleWhisper(playerId, data));
        socket.on('chat:joinChannel', (data) => this.chatManager.handleJoinChannel(playerId, data));
        socket.on('chat:leaveChannel', (data) => this.chatManager.handleLeaveChannel(playerId, data));

        // Social actions
        socket.on('social:addFriend', (data) => this.playerManager.handleAddFriend(playerId, data));
        socket.on('social:removeFriend', (data) => this.playerManager.handleRemoveFriend(playerId, data));
        socket.on('social:blockPlayer', (data) => this.playerManager.handleBlockPlayer(playerId, data));
        socket.on('social:unblockPlayer', (data) => this.playerManager.handleUnblockPlayer(playerId, data));

        // Events and activities
        socket.on('event:join', (data) => this.eventManager.handleJoinEvent(playerId, data));
        socket.on('event:leave', (data) => this.eventManager.handleLeaveEvent(playerId, data));

        // Admin actions (if player has admin rights)
        socket.on('admin:kick', (data) => this.adminManager.handleKickPlayer(playerId, data));
        socket.on('admin:ban', (data) => this.adminManager.handleBanPlayer(playerId, data));
        socket.on('admin:mute', (data) => this.adminManager.handleMutePlayer(playerId, data));
        socket.on('admin:broadcast', (data) => this.adminManager.handleBroadcast(playerId, data));

        // Update last activity
        socket.onAny(() => {
            const playerConnection = this.connectedPlayers.get(playerId);
            if (playerConnection) {
                playerConnection.lastActivity = new Date();
            }
        });
    }

    async handlePlayerDisconnection(playerId) {
        try {
            const playerConnection = this.connectedPlayers.get(playerId);
            if (playerConnection) {
                const { playerData } = playerConnection;
                
                // Remove player from game world
                await this.playerManager.playerDisconnected(playerId);
                await this.worldManager.removePlayerFromWorld(playerId);
                
                // Remove from connected players
                this.connectedPlayers.delete(playerId);
                
                this.logger.info(`Player ${playerData.username} disconnected`, { playerId });
            }
        } catch (error) {
            this.logger.error('Error handling player disconnection:', error);
        }
    }

    setupErrorHandling() {
        // Global error handler
        this.app.use(errorMiddleware);

        // Socket error handling
        this.io.engine.on("connection_error", (err) => {
            this.logger.error('Socket connection error:', err);
        });

        // Process error handling
        process.on('uncaughtException', (error) => {
            this.logger.error('Uncaught Exception:', error);
            this.gracefulShutdown();
        });

        process.on('unhandledRejection', (reason, promise) => {
            this.logger.error('Unhandled Rejection at:', promise, 'reason:', reason);
            this.gracefulShutdown();
        });

        process.on('SIGINT', () => {
            this.logger.info('Received SIGINT, starting graceful shutdown...');
            this.gracefulShutdown();
        });

        process.on('SIGTERM', () => {
            this.logger.info('Received SIGTERM, starting graceful shutdown...');
            this.gracefulShutdown();
        });
    }

    async gracefulShutdown() {
        try {
            this.logger.info('Starting graceful shutdown...');
            
            // Stop accepting new connections
            this.server.close();
            
            // Disconnect all players gracefully
            for (const [playerId, playerConnection] of this.connectedPlayers) {
                try {
                    await this.handlePlayerDisconnection(playerId);
                    playerConnection.socket.disconnect();
                } catch (error) {
                    this.logger.error(`Error disconnecting player ${playerId}:`, error);
                }
            }
            
            // Save game state
            await this.gameEngine.saveGameState();
            
            // Close database connections
            await DatabaseService.disconnect();
            await RedisService.disconnect();
            
            this.logger.info('Graceful shutdown completed');
            process.exit(0);
        } catch (error) {
            this.logger.error('Error during graceful shutdown:', error);
            process.exit(1);
        }
    }

    async start() {
        try {
            // Start game engine
            await this.gameEngine.start();
            
            // Start the server
            this.server.listen(PORT, () => {
                this.logger.info(`Super Online Game Server running on port ${PORT}`);
                this.logger.info(`Environment: ${NODE_ENV}`);
                this.logger.info(`Client URL: ${process.env.CLIENT_URL || 'http://localhost:3000'}`);
            });

            // Start periodic tasks
            this.startPeriodicTasks();
            
        } catch (error) {
            this.logger.error('Failed to start server:', error);
            process.exit(1);
        }
    }

    startPeriodicTasks() {
        // Save game state every 5 minutes
        setInterval(async () => {
            try {
                await this.gameEngine.saveGameState();
                this.logger.info('Game state saved');
            } catch (error) {
                this.logger.error('Error saving game state:', error);
            }
        }, 5 * 60 * 1000);

        // Clean up inactive connections every minute
        setInterval(() => {
            const now = new Date();
            for (const [playerId, playerConnection] of this.connectedPlayers) {
                const timeSinceLastActivity = now - playerConnection.lastActivity;
                if (timeSinceLastActivity > 30 * 60 * 1000) { // 30 minutes
                    this.logger.info(`Disconnecting inactive player: ${playerId}`);
                    playerConnection.socket.disconnect();
                }
            }
        }, 60 * 1000);

        // Update game metrics every 30 seconds
        setInterval(async () => {
            try {
                await MetricsService.updateMetrics({
                    connectedPlayers: this.connectedPlayers.size,
                    activeRooms: this.gameRooms.size,
                    memoryUsage: process.memoryUsage(),
                    uptime: process.uptime()
                });
            } catch (error) {
                this.logger.error('Error updating metrics:', error);
            }
        }, 30 * 1000);
    }
}

// Create and start the server
const gameServer = new SuperOnlineGameServer();
gameServer.start().catch(error => {
    console.error('Failed to start Super Online Game Server:', error);
    process.exit(1);
});

module.exports = SuperOnlineGameServer;