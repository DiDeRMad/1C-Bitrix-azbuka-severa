const redis = require('redis');
const winston = require('winston');

class RedisService {
    constructor() {
        this.client = null;
        this.isConnected = false;
        this.logger = winston.createLogger({
            level: 'info',
            format: winston.format.combine(
                winston.format.timestamp(),
                winston.format.json()
            ),
            defaultMeta: { service: 'redis' },
            transports: [
                new winston.transports.File({ filename: 'logs/redis.log' }),
                new winston.transports.Console()
            ]
        });
    }

    async connect(uri) {
        try {
            this.logger.info('Connecting to Redis...');
            
            this.client = redis.createClient({
                url: uri,
                retry_strategy: (times) => {
                    const delay = Math.min(times * 50, 2000);
                    return delay;
                }
            });

            this.client.on('error', (error) => {
                this.logger.error('Redis connection error:', error);
                this.isConnected = false;
            });

            this.client.on('connect', () => {
                this.logger.info('Connected to Redis');
                this.isConnected = true;
            });

            this.client.on('disconnect', () => {
                this.logger.warn('Disconnected from Redis');
                this.isConnected = false;
            });

            await this.client.connect();
            this.logger.info('Redis client connected successfully');
            
        } catch (error) {
            this.logger.error('Failed to connect to Redis:', error);
            throw error;
        }
    }

    async disconnect() {
        try {
            if (this.client) {
                await this.client.quit();
                this.isConnected = false;
                this.logger.info('Disconnected from Redis');
            }
        } catch (error) {
            this.logger.error('Error disconnecting from Redis:', error);
            throw error;
        }
    }

    async get(key) {
        try {
            return await this.client.get(key);
        } catch (error) {
            this.logger.error(`Error getting key ${key}:`, error);
            throw error;
        }
    }

    async set(key, value, expiration = null) {
        try {
            if (expiration) {
                return await this.client.setEx(key, expiration, value);
            } else {
                return await this.client.set(key, value);
            }
        } catch (error) {
            this.logger.error(`Error setting key ${key}:`, error);
            throw error;
        }
    }

    async delete(key) {
        try {
            return await this.client.del(key);
        } catch (error) {
            this.logger.error(`Error deleting key ${key}:`, error);
            throw error;
        }
    }

    async exists(key) {
        try {
            return await this.client.exists(key);
        } catch (error) {
            this.logger.error(`Error checking existence of key ${key}:`, error);
            throw error;
        }
    }

    async increment(key) {
        try {
            return await this.client.incr(key);
        } catch (error) {
            this.logger.error(`Error incrementing key ${key}:`, error);
            throw error;
        }
    }

    async expire(key, seconds) {
        try {
            return await this.client.expire(key, seconds);
        } catch (error) {
            this.logger.error(`Error setting expiration for key ${key}:`, error);
            throw error;
        }
    }

    getClient() {
        return this.client;
    }

    isConnectionHealthy() {
        return this.isConnected && this.client && this.client.isOpen;
    }
}

module.exports = new RedisService();