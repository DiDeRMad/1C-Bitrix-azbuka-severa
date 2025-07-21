const winston = require('winston');

class FileService {
    constructor() {
        this.isInitialized = false;
        this.logger = winston.createLogger({
            level: 'info',
            format: winston.format.combine(
                winston.format.timestamp(),
                winston.format.json()
            ),
            defaultMeta: { service: 'file' },
            transports: [
                new winston.transports.File({ filename: 'logs/file.log' }),
                new winston.transports.Console()
            ]
        });
    }

    async initialize() {
        this.logger.info('File service initialized');
        this.isInitialized = true;
    }
}

module.exports = new FileService();