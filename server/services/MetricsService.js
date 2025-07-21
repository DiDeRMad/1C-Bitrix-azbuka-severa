const winston = require('winston');

class MetricsService {
    constructor() {
        this.isInitialized = false;
        this.metrics = new Map();
        this.logger = winston.createLogger({
            level: 'info',
            format: winston.format.combine(
                winston.format.timestamp(),
                winston.format.json()
            ),
            defaultMeta: { service: 'metrics' },
            transports: [
                new winston.transports.File({ filename: 'logs/metrics.log' }),
                new winston.transports.Console()
            ]
        });
    }

    async initialize() {
        this.logger.info('Metrics service initialized');
        this.isInitialized = true;
    }

    recordMetric(name, value, tags = {}) {
        const metric = {
            name,
            value,
            tags,
            timestamp: new Date()
        };
        
        if (!this.metrics.has(name)) {
            this.metrics.set(name, []);
        }
        
        this.metrics.get(name).push(metric);
        this.logger.debug(`Metric recorded: ${name} = ${value}`, tags);
    }

    getMetrics(name) {
        return this.metrics.get(name) || [];
    }

    getAllMetrics() {
        return Object.fromEntries(this.metrics);
    }
}

module.exports = new MetricsService();