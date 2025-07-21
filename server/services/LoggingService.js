const winston = require('winston');
const path = require('path');

class LoggingService {
    constructor() {
        this.logger = null;
        this.isInitialized = false;
    }

    getInstance() {
        if (!this.logger) {
            this.logger = winston.createLogger({
                level: process.env.LOG_LEVEL || 'info',
                format: winston.format.combine(
                    winston.format.timestamp(),
                    winston.format.errors({ stack: true }),
                    winston.format.json()
                ),
                defaultMeta: { service: 'super-online-game' },
                transports: [
                    new winston.transports.File({ 
                        filename: path.join('logs', 'error.log'), 
                        level: 'error' 
                    }),
                    new winston.transports.File({ 
                        filename: path.join('logs', 'combined.log') 
                    }),
                    new winston.transports.Console({
                        format: winston.format.combine(
                            winston.format.colorize(),
                            winston.format.simple()
                        )
                    })
                ]
            });
            this.isInitialized = true;
        }
        return this.logger;
    }

    info(message, meta = {}) {
        this.getInstance().info(message, meta);
    }

    error(message, meta = {}) {
        this.getInstance().error(message, meta);
    }

    warn(message, meta = {}) {
        this.getInstance().warn(message, meta);
    }

    debug(message, meta = {}) {
        this.getInstance().debug(message, meta);
    }
}

module.exports = new LoggingService();