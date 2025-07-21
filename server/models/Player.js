/**
 * Player Model - Main player account data
 */

const mongoose = require('mongoose');
const bcrypt = require('bcryptjs');

const playerSchema = new mongoose.Schema({
    // Authentication
    username: {
        type: String,
        required: true,
        unique: true,
        trim: true,
        minlength: 3,
        maxlength: 20,
        match: /^[a-zA-Z0-9_]+$/
    },
    email: {
        type: String,
        required: true,
        unique: true,
        lowercase: true,
        trim: true,
        match: /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    },
    password: {
        type: String,
        required: true,
        minlength: 6
    },
    
    // Account status
    isActive: {
        type: Boolean,
        default: true
    },
    isVerified: {
        type: Boolean,
        default: false
    },
    isBanned: {
        type: Boolean,
        default: false
    },
    banReason: {
        type: String,
        default: null
    },
    banExpiresAt: {
        type: Date,
        default: null
    },
    
    // Premium status
    isPremium: {
        type: Boolean,
        default: false
    },
    premiumUntil: {
        type: Date,
        default: null
    },
    premiumType: {
        type: String,
        enum: ['basic', 'advanced', 'legendary'],
        default: null
    },
    
    // Account statistics
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
    gold: {
        type: Number,
        default: 1000,
        min: 0
    },
    gems: {
        type: Number,
        default: 0,
        min: 0
    },
    
    // Playtime tracking
    totalPlaytime: {
        type: Number,
        default: 0
    },
    lastLogin: {
        type: Date,
        default: Date.now
    },
    lastLogout: {
        type: Date,
        default: null
    },
    currentSession: {
        startTime: Date,
        lastActivity: Date
    },
    
    // Characters
    characters: [{
        type: mongoose.Schema.Types.ObjectId,
        ref: 'Character'
    }],
    activeCharacterId: {
        type: mongoose.Schema.Types.ObjectId,
        ref: 'Character',
        default: null
    },
    maxCharacters: {
        type: Number,
        default: 3
    },
    
    // Social features
    friends: [{
        type: mongoose.Schema.Types.ObjectId,
        ref: 'Player'
    }],
    blockedPlayers: [{
        type: mongoose.Schema.Types.ObjectId,
        ref: 'Player'
    }],
    
    // Guild
    guild: {
        type: mongoose.Schema.Types.ObjectId,
        ref: 'Guild',
        default: null
    },
    guildRank: {
        type: String,
        enum: ['member', 'officer', 'leader'],
        default: null
    },
    
    // Achievements
    achievements: [{
        achievementId: String,
        unlockedAt: {
            type: Date,
            default: Date.now
        },
        progress: {
            type: Number,
            default: 100
        }
    }],
    
    // Settings and preferences
    settings: {
        graphics: {
            quality: {
                type: String,
                enum: ['low', 'medium', 'high', 'ultra'],
                default: 'medium'
            },
            resolution: {
                type: String,
                default: '1920x1080'
            },
            fullscreen: {
                type: Boolean,
                default: false
            }
        },
        audio: {
            masterVolume: {
                type: Number,
                default: 0.8,
                min: 0,
                max: 1
            },
            musicVolume: {
                type: Number,
                default: 0.6,
                min: 0,
                max: 1
            },
            effectsVolume: {
                type: Number,
                default: 0.8,
                min: 0,
                max: 1
            }
        },
        gameplay: {
            autoLoot: {
                type: Boolean,
                default: true
            },
            showDamageNumbers: {
                type: Boolean,
                default: true
            },
            showPlayerNames: {
                type: Boolean,
                default: true
            },
            chatFilter: {
                type: Boolean,
                default: false
            }
        },
        privacy: {
            showOnline: {
                type: Boolean,
                default: true
            },
            allowFriendRequests: {
                type: Boolean,
                default: true
            },
            allowTradeRequests: {
                type: Boolean,
                default: true
            },
            allowGuildInvites: {
                type: Boolean,
                default: true
            }
        }
    },
    
    // Statistics
    statistics: {
        // Combat stats
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
        damageDealt: {
            type: Number,
            default: 0
        },
        damageReceived: {
            type: Number,
            default: 0
        },
        
        // Economy stats
        goldEarned: {
            type: Number,
            default: 0
        },
        goldSpent: {
            type: Number,
            default: 0
        },
        itemsTraded: {
            type: Number,
            default: 0
        },
        auctionsWon: {
            type: Number,
            default: 0
        },
        
        // Quest stats
        questsCompleted: {
            type: Number,
            default: 0
        },
        questsFailed: {
            type: Number,
            default: 0
        },
        
        // Social stats
        messagesWritten: {
            type: Number,
            default: 0
        },
        timeInGuild: {
            type: Number,
            default: 0
        }
    },
    
    // Administrative
    createdAt: {
        type: Date,
        default: Date.now
    },
    updatedAt: {
        type: Date,
        default: Date.now
    },
    
    // Account recovery
    emailVerificationToken: {
        type: String,
        default: null
    },
    passwordResetToken: {
        type: String,
        default: null
    },
    passwordResetExpires: {
        type: Date,
        default: null
    },
    
    // Security
    loginAttempts: {
        type: Number,
        default: 0
    },
    lockUntil: {
        type: Date,
        default: null
    },
    lastPasswordChange: {
        type: Date,
        default: Date.now
    },
    twoFactorSecret: {
        type: String,
        default: null
    },
    twoFactorEnabled: {
        type: Boolean,
        default: false
    },
    
    // IP tracking
    registrationIP: {
        type: String,
        default: null
    },
    lastLoginIP: {
        type: String,
        default: null
    },
    
    // Referral system
    referredBy: {
        type: mongoose.Schema.Types.ObjectId,
        ref: 'Player',
        default: null
    },
    referralCode: {
        type: String,
        unique: true,
        sparse: true
    },
    referrals: [{
        player: {
            type: mongoose.Schema.Types.ObjectId,
            ref: 'Player'
        },
        registeredAt: {
            type: Date,
            default: Date.now
        },
        rewardClaimed: {
            type: Boolean,
            default: false
        }
    }]
}, {
    timestamps: true,
    toJSON: { virtuals: true },
    toObject: { virtuals: true }
});

// Indexes for performance
playerSchema.index({ username: 1 });
playerSchema.index({ email: 1 });
playerSchema.index({ level: -1 });
playerSchema.index({ lastLogin: -1 });
playerSchema.index({ guild: 1 });
playerSchema.index({ referralCode: 1 });
playerSchema.index({ isActive: 1, isBanned: 1 });

// Virtual for account lock status
playerSchema.virtual('isLocked').get(function() {
    return !!(this.lockUntil && this.lockUntil > Date.now());
});

// Virtual for experience to next level
playerSchema.virtual('experienceToNextLevel').get(function() {
    const baseExp = 1000;
    const nextLevelExp = Math.floor(baseExp * Math.pow(1.5, this.level - 1));
    const currentLevelExp = this.level > 1 ? Math.floor(baseExp * Math.pow(1.5, this.level - 2)) : 0;
    return nextLevelExp - (this.experience - currentLevelExp);
});

// Virtual for kill/death ratio
playerSchema.virtual('kdRatio').get(function() {
    if (this.statistics.deaths === 0) {
        return this.statistics.playersKilled;
    }
    return (this.statistics.playersKilled / this.statistics.deaths).toFixed(2);
});

// Virtual for premium status
playerSchema.virtual('isPremiumActive').get(function() {
    return this.isPremium && this.premiumUntil && this.premiumUntil > new Date();
});

// Pre-save middleware
playerSchema.pre('save', async function(next) {
    // Hash password if modified
    if (this.isModified('password')) {
        this.password = await bcrypt.hash(this.password, 12);
        this.lastPasswordChange = new Date();
    }
    
    // Update updatedAt
    this.updatedAt = new Date();
    
    // Generate referral code if not exists
    if (!this.referralCode) {
        this.referralCode = this.username.toUpperCase() + Math.random().toString(36).substr(2, 4).toUpperCase();
    }
    
    next();
});

// Methods
playerSchema.methods.comparePassword = async function(candidatePassword) {
    return bcrypt.compare(candidatePassword, this.password);
};

playerSchema.methods.incrementLoginAttempts = function() {
    // If we have a previous lock that has expired, restart at 1
    if (this.lockUntil && this.lockUntil < Date.now()) {
        return this.updateOne({
            $unset: { lockUntil: 1 },
            $set: { loginAttempts: 1 }
        });
    }
    
    const updates = { $inc: { loginAttempts: 1 } };
    
    // Lock account after 5 failed attempts for 2 hours
    if (this.loginAttempts + 1 >= 5 && !this.isLocked) {
        updates.$set = { lockUntil: Date.now() + 2 * 60 * 60 * 1000 };
    }
    
    return this.updateOne(updates);
};

playerSchema.methods.resetLoginAttempts = function() {
    return this.updateOne({
        $unset: { loginAttempts: 1, lockUntil: 1 }
    });
};

playerSchema.methods.addExperience = function(amount) {
    this.experience += amount;
    this.statistics.goldEarned += amount; // Assuming some gold is earned with experience
    
    // Check for level up
    const baseExp = 1000;
    while (this.level < 1000) {
        const nextLevelExp = Math.floor(baseExp * Math.pow(1.5, this.level - 1));
        if (this.experience >= nextLevelExp) {
            this.level++;
            // Award level up bonus
            this.gold += this.level * 100;
        } else {
            break;
        }
    }
    
    return this.save();
};

playerSchema.methods.addGold = function(amount) {
    this.gold += amount;
    this.statistics.goldEarned += amount;
    return this.save();
};

playerSchema.methods.spendGold = function(amount) {
    if (this.gold < amount) {
        throw new Error('Insufficient gold');
    }
    this.gold -= amount;
    this.statistics.goldSpent += amount;
    return this.save();
};

playerSchema.methods.addFriend = function(friendId) {
    if (!this.friends.includes(friendId)) {
        this.friends.push(friendId);
    }
    return this.save();
};

playerSchema.methods.removeFriend = function(friendId) {
    this.friends = this.friends.filter(id => !id.equals(friendId));
    return this.save();
};

playerSchema.methods.blockPlayer = function(playerId) {
    if (!this.blockedPlayers.includes(playerId)) {
        this.blockedPlayers.push(playerId);
    }
    // Also remove from friends if they were friends
    this.friends = this.friends.filter(id => !id.equals(playerId));
    return this.save();
};

playerSchema.methods.unblockPlayer = function(playerId) {
    this.blockedPlayers = this.blockedPlayers.filter(id => !id.equals(playerId));
    return this.save();
};

playerSchema.methods.unlockAchievement = function(achievementId, progress = 100) {
    const existingAchievement = this.achievements.find(a => a.achievementId === achievementId);
    if (!existingAchievement) {
        this.achievements.push({
            achievementId,
            progress,
            unlockedAt: new Date()
        });
        return this.save();
    }
    return Promise.resolve(this);
};

playerSchema.methods.updateSetting = function(category, setting, value) {
    this.settings[category][setting] = value;
    return this.save();
};

playerSchema.methods.updateStatistic = function(stat, value) {
    this.statistics[stat] = (this.statistics[stat] || 0) + value;
    return this.save();
};

playerSchema.methods.startSession = function() {
    this.currentSession = {
        startTime: new Date(),
        lastActivity: new Date()
    };
    this.lastLogin = new Date();
    return this.save();
};

playerSchema.methods.endSession = function() {
    if (this.currentSession && this.currentSession.startTime) {
        const sessionDuration = Date.now() - this.currentSession.startTime;
        this.totalPlaytime += sessionDuration;
    }
    this.lastLogout = new Date();
    this.currentSession = undefined;
    return this.save();
};

playerSchema.methods.updateActivity = function() {
    if (this.currentSession) {
        this.currentSession.lastActivity = new Date();
        return this.save();
    }
    return Promise.resolve(this);
};

playerSchema.methods.toSafeObject = function() {
    const playerObject = this.toObject();
    delete playerObject.password;
    delete playerObject.emailVerificationToken;
    delete playerObject.passwordResetToken;
    delete playerObject.twoFactorSecret;
    delete playerObject.registrationIP;
    delete playerObject.lastLoginIP;
    return playerObject;
};

// Static methods
playerSchema.statics.findByUsername = function(username) {
    return this.findOne({ username: new RegExp(`^${username}$`, 'i') });
};

playerSchema.statics.findByEmail = function(email) {
    return this.findOne({ email: email.toLowerCase() });
};

playerSchema.statics.findOnlinePlayers = function() {
    const fiveMinutesAgo = new Date(Date.now() - 5 * 60 * 1000);
    return this.find({
        'currentSession.lastActivity': { $gte: fiveMinutesAgo },
        isActive: true,
        isBanned: false
    });
};

playerSchema.statics.getTopPlayers = function(limit = 10) {
    return this.find({ isActive: true, isBanned: false })
        .sort({ level: -1, experience: -1 })
        .limit(limit)
        .select('username level experience statistics.monstersKilled statistics.playersKilled');
};

playerSchema.statics.searchPlayers = function(query, limit = 20) {
    return this.find({
        username: new RegExp(query, 'i'),
        isActive: true,
        isBanned: false
    })
    .limit(limit)
    .select('username level guild');
};

module.exports = mongoose.model('Player', playerSchema);