const mongoose = require('mongoose');
const winston = require('winston');

class DatabaseService {
    constructor() {
        this.connection = null;
        this.isConnected = false;
        this.logger = winston.createLogger({
            level: 'info',
            format: winston.format.combine(
                winston.format.timestamp(),
                winston.format.json()
            ),
            defaultMeta: { service: 'database' },
            transports: [
                new winston.transports.File({ filename: 'logs/database.log' }),
                new winston.transports.Console()
            ]
        });
    }

    async connect(uri) {
        try {
            this.logger.info('Connecting to MongoDB...');
            
            await mongoose.connect(uri, {
                useNewUrlParser: true,
                useUnifiedTopology: true,
                maxPoolSize: 10,
                serverSelectionTimeoutMS: 5000,
                socketTimeoutMS: 45000
            });

            this.connection = mongoose.connection;
            this.isConnected = true;

            // Set up connection event handlers
            this.connection.on('error', (error) => {
                this.logger.error('MongoDB connection error:', error);
                this.isConnected = false;
            });

            this.connection.on('disconnected', () => {
                this.logger.warn('MongoDB disconnected');
                this.isConnected = false;
            });

            this.connection.on('reconnected', () => {
                this.logger.info('MongoDB reconnected');
                this.isConnected = true;
            });

            this.logger.info('Connected to MongoDB successfully');
            return this.connection;

        } catch (error) {
            this.logger.error('Failed to connect to MongoDB:', error);
            throw error;
        }
    }

    async disconnect() {
        try {
            if (this.connection) {
                await mongoose.disconnect();
                this.isConnected = false;
                this.logger.info('Disconnected from MongoDB');
            }
        } catch (error) {
            this.logger.error('Error disconnecting from MongoDB:', error);
            throw error;
        }
    }

    getConnection() {
        return this.connection;
    }

    isConnectionHealthy() {
        return this.isConnected && this.connection.readyState === 1;
    }

    async checkConnection() {
        try {
            if (!this.isConnected) {
                throw new Error('Not connected to database');
            }
            
            await mongoose.connection.db.admin().ping();
            return true;
        } catch (error) {
            this.logger.error('Database health check failed:', error);
            return false;
        }
    }

    async executeTransaction(operations) {
        const session = await mongoose.startSession();
        session.startTransaction();
        
        try {
            const results = await operations(session);
            await session.commitTransaction();
            return results;
        } catch (error) {
            await session.abortTransaction();
            throw error;
        } finally {
            session.endSession();
        }
    }

    async createBackup() {
        // Implementation would depend on backup strategy
        this.logger.info('Database backup initiated');
    }

    async getStats() {
        try {
            const stats = await mongoose.connection.db.stats();
            return {
                collections: stats.collections,
                dataSize: stats.dataSize,
                storageSize: stats.storageSize,
                indexes: stats.indexes,
                indexSize: stats.indexSize,
                objects: stats.objects
            };
        } catch (error) {
            this.logger.error('Failed to get database stats:', error);
            throw error;
        }
    }
}

module.exports = new DatabaseService();