/**
 * Economy Manager - Handles trading, marketplace, and economic systems
 */

const winston = require('winston');
const EventEmitter = require('events');

class EconomyManager extends EventEmitter {
    constructor(io) {
        super();
        this.io = io;
        this.activeTrades = new Map(); // tradeId -> trade data
        this.marketListings = new Map(); // listingId -> listing data
        this.auctions = new Map(); // auctionId -> auction data
        this.playerTrades = new Map(); // playerId -> tradeId
        this.economyStats = {
            totalTransactions: 0,
            totalGoldTraded: 0,
            totalItemsTraded: 0,
            averageTransactionValue: 0,
            topSellingItems: new Map(),
            economyHealth: 100
        };

        this.logger = winston.createLogger({
            level: 'info',
            format: winston.format.combine(
                winston.format.timestamp(),
                winston.format.json()
            ),
            defaultMeta: { service: 'economy-manager' },
            transports: [
                new winston.transports.File({ filename: 'logs/economy-manager.log' }),
                new winston.transports.Console()
            ]
        });

        // Economy configuration
        this.ECONOMY_CONFIG = {
            marketTaxRate: 0.05, // 5% transaction tax
            auctionTaxRate: 0.03, // 3% auction tax
            tradeTimeout: 300000, // 5 minutes
            auctionDuration: 86400000, // 24 hours
            maxListingsPerPlayer: 20,
            maxTradeSlots: 8,
            minimumBid: 1,
            priceFluctuationRate: 0.1, // 10% price changes
            economyRebalanceInterval: 3600000 // 1 hour
        };

        // Item price database (base prices)
        this.itemPrices = new Map([
            // Basic items
            ['health_potion', { base: 50, current: 50, demand: 1.0 }],
            ['mana_potion', { base: 30, current: 30, demand: 1.0 }],
            ['stamina_potion', { base: 25, current: 25, demand: 0.8 }],
            
            // Weapons
            ['basic_sword', { base: 200, current: 200, demand: 0.9 }],
            ['iron_sword', { base: 500, current: 500, demand: 1.1 }],
            ['steel_sword', { base: 1200, current: 1200, demand: 1.3 }],
            ['magic_sword', { base: 3000, current: 3000, demand: 1.5 }],
            
            // Armor
            ['leather_armor', { base: 150, current: 150, demand: 0.8 }],
            ['iron_armor', { base: 400, current: 400, demand: 1.0 }],
            ['steel_armor', { base: 1000, current: 1000, demand: 1.2 }],
            ['magic_armor', { base: 2500, current: 2500, demand: 1.4 }],
            
            // Materials
            ['iron_ore', { base: 10, current: 10, demand: 1.1 }],
            ['steel_ingot', { base: 25, current: 25, demand: 1.2 }],
            ['magic_crystal', { base: 100, current: 100, demand: 1.5 }],
            ['dragon_scale', { base: 1000, current: 1000, demand: 2.0 }],
            
            // Rare items
            ['rare_gem', { base: 500, current: 500, demand: 1.8 }],
            ['legendary_scroll', { base: 2000, current: 2000, demand: 2.5 }],
            ['ancient_artifact', { base: 10000, current: 10000, demand: 3.0 }]
        ]);

        this.setupEconomyLoop();
        this.initializeMarketplace();
    }

    setupEconomyLoop() {
        // Update market prices every 10 minutes
        setInterval(() => {
            this.updateMarketPrices();
        }, 600000);

        // Process auctions every minute
        setInterval(() => {
            this.processAuctions();
        }, 60000);

        // Clean up expired trades and listings every 5 minutes
        setInterval(() => {
            this.cleanupExpiredTrades();
        }, 300000);

        // Rebalance economy every hour
        setInterval(() => {
            this.rebalanceEconomy();
        }, this.ECONOMY_CONFIG.economyRebalanceInterval);

        // Update economy statistics every 30 minutes
        setInterval(() => {
            this.updateEconomyStats();
        }, 1800000);
    }

    initializeMarketplace() {
        // Create some initial market listings for basic items
        const initialListings = [
            { item: 'health_potion', quantity: 50, price: 55, seller: 'npc_merchant' },
            { item: 'mana_potion', quantity: 30, price: 32, seller: 'npc_merchant' },
            { item: 'basic_sword', quantity: 10, price: 220, seller: 'npc_blacksmith' },
            { item: 'leather_armor', quantity: 8, price: 165, seller: 'npc_armorer' }
        ];

        for (const listing of initialListings) {
            this.createMarketListing(listing.seller, listing.item, listing.quantity, listing.price, true);
        }

        this.logger.info('Marketplace initialized with basic listings');
    }

    async handleTrade(playerId, data) {
        try {
            const { targetPlayerId, action, tradeData } = data;

            switch (action) {
                case 'initiate':
                    await this.initiateTrade(playerId, targetPlayerId);
                    break;
                case 'accept_invite':
                    await this.acceptTradeInvite(playerId, tradeData.tradeId);
                    break;
                case 'decline_invite':
                    await this.declineTradeInvite(playerId, tradeData.tradeId);
                    break;
                case 'add_item':
                    await this.addItemToTrade(playerId, tradeData.itemId, tradeData.quantity);
                    break;
                case 'remove_item':
                    await this.removeItemFromTrade(playerId, tradeData.itemId);
                    break;
                case 'set_gold':
                    await this.setTradeGold(playerId, tradeData.gold);
                    break;
                case 'ready':
                    await this.setTradeReady(playerId, true);
                    break;
                case 'unready':
                    await this.setTradeReady(playerId, false);
                    break;
                case 'confirm':
                    await this.confirmTrade(playerId);
                    break;
                case 'cancel':
                    await this.cancelTrade(playerId);
                    break;
                default:
                    this.sendError(playerId, 'Unknown trade action');
            }

        } catch (error) {
            this.logger.error('Error handling trade:', error);
            this.sendError(playerId, 'Trade action failed');
        }
    }

    async handleMarketBuy(playerId, data) {
        try {
            const { listingId, quantity } = data;
            await this.buyFromMarket(playerId, listingId, quantity);

        } catch (error) {
            this.logger.error('Error handling market buy:', error);
            this.sendError(playerId, 'Purchase failed');
        }
    }

    async handleMarketSell(playerId, data) {
        try {
            const { itemId, quantity, price } = data;
            await this.sellToMarket(playerId, itemId, quantity, price);

        } catch (error) {
            this.logger.error('Error handling market sell:', error);
            this.sendError(playerId, 'Listing failed');
        }
    }

    async handleAuction(playerId, data) {
        try {
            const { action, auctionData } = data;

            switch (action) {
                case 'create':
                    await this.createAuction(playerId, auctionData.itemId, auctionData.startingBid, auctionData.duration);
                    break;
                case 'bid':
                    await this.placeBid(playerId, auctionData.auctionId, auctionData.bidAmount);
                    break;
                case 'cancel':
                    await this.cancelAuction(playerId, auctionData.auctionId);
                    break;
                default:
                    this.sendError(playerId, 'Unknown auction action');
            }

        } catch (error) {
            this.logger.error('Error handling auction:', error);
            this.sendError(playerId, 'Auction action failed');
        }
    }

    async initiateTrade(initiatorId, targetId) {
        // Check if both players are online and not in trade
        if (this.playerTrades.has(initiatorId) || this.playerTrades.has(targetId)) {
            return this.sendError(initiatorId, 'One of the players is already in a trade');
        }

        const tradeId = `trade_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
        
        const trade = {
            id: tradeId,
            initiator: initiatorId,
            target: targetId,
            status: 'pending',
            startTime: Date.now(),
            expiresAt: Date.now() + this.ECONOMY_CONFIG.tradeTimeout,
            initiatorItems: new Map(),
            targetItems: new Map(),
            initiatorGold: 0,
            targetGold: 0,
            initiatorReady: false,
            targetReady: false,
            initiatorConfirmed: false,
            targetConfirmed: false
        };

        this.activeTrades.set(tradeId, trade);
        this.playerTrades.set(initiatorId, tradeId);
        this.playerTrades.set(targetId, tradeId);

        // Send trade invite to target
        this.io.to(targetId).emit('trade:invite', {
            tradeId,
            initiatorId,
            expiresAt: trade.expiresAt
        });

        // Notify initiator
        this.io.to(initiatorId).emit('trade:initiated', {
            tradeId,
            targetId
        });

        this.logger.info(`Trade initiated: ${tradeId}`, { initiatorId, targetId });
    }

    async acceptTradeInvite(playerId, tradeId) {
        const trade = this.activeTrades.get(tradeId);
        if (!trade || trade.target !== playerId) {
            return this.sendError(playerId, 'Invalid trade');
        }

        if (trade.status !== 'pending') {
            return this.sendError(playerId, 'Trade no longer available');
        }

        trade.status = 'active';

        // Notify both players
        this.broadcastToTrade(trade, 'trade:started', {
            tradeId,
            participants: [trade.initiator, trade.target]
        });

        this.logger.info(`Trade accepted: ${tradeId}`);
    }

    async declineTradeInvite(playerId, tradeId) {
        const trade = this.activeTrades.get(tradeId);
        if (!trade || trade.target !== playerId) return;

        this.cancelTrade(playerId, 'declined');
    }

    async addItemToTrade(playerId, itemId, quantity) {
        const tradeId = this.playerTrades.get(playerId);
        const trade = this.activeTrades.get(tradeId);

        if (!trade || trade.status !== 'active') {
            return this.sendError(playerId, 'No active trade');
        }

        // Determine which side of the trade this is
        const isInitiator = trade.initiator === playerId;
        const items = isInitiator ? trade.initiatorItems : trade.targetItems;

        // Check if player has the item (this would integrate with inventory system)
        if (!await this.playerHasItem(playerId, itemId, quantity)) {
            return this.sendError(playerId, 'You do not have enough of this item');
        }

        // Add item to trade
        items.set(itemId, (items.get(itemId) || 0) + quantity);

        // Reset ready states
        trade.initiatorReady = false;
        trade.targetReady = false;

        // Broadcast trade update
        this.broadcastTradeUpdate(trade);

        this.logger.info(`Item added to trade: ${itemId} x${quantity}`, { playerId, tradeId });
    }

    async removeItemFromTrade(playerId, itemId) {
        const tradeId = this.playerTrades.get(playerId);
        const trade = this.activeTrades.get(tradeId);

        if (!trade || trade.status !== 'active') return;

        const isInitiator = trade.initiator === playerId;
        const items = isInitiator ? trade.initiatorItems : trade.targetItems;

        items.delete(itemId);

        // Reset ready states
        trade.initiatorReady = false;
        trade.targetReady = false;

        this.broadcastTradeUpdate(trade);
    }

    async setTradeGold(playerId, goldAmount) {
        const tradeId = this.playerTrades.get(playerId);
        const trade = this.activeTrades.get(tradeId);

        if (!trade || trade.status !== 'active') return;

        // Check if player has enough gold
        if (!await this.playerHasGold(playerId, goldAmount)) {
            return this.sendError(playerId, 'Not enough gold');
        }

        const isInitiator = trade.initiator === playerId;
        if (isInitiator) {
            trade.initiatorGold = goldAmount;
        } else {
            trade.targetGold = goldAmount;
        }

        // Reset ready states
        trade.initiatorReady = false;
        trade.targetReady = false;

        this.broadcastTradeUpdate(trade);
    }

    async setTradeReady(playerId, ready) {
        const tradeId = this.playerTrades.get(playerId);
        const trade = this.activeTrades.get(tradeId);

        if (!trade || trade.status !== 'active') return;

        const isInitiator = trade.initiator === playerId;
        if (isInitiator) {
            trade.initiatorReady = ready;
        } else {
            trade.targetReady = ready;
        }

        this.broadcastTradeUpdate(trade);

        // If both players are ready, allow confirmation
        if (trade.initiatorReady && trade.targetReady) {
            this.broadcastToTrade(trade, 'trade:readyToConfirm', {});
        }
    }

    async confirmTrade(playerId) {
        const tradeId = this.playerTrades.get(playerId);
        const trade = this.activeTrades.get(tradeId);

        if (!trade || trade.status !== 'active') return;

        if (!trade.initiatorReady || !trade.targetReady) {
            return this.sendError(playerId, 'Both players must be ready');
        }

        const isInitiator = trade.initiator === playerId;
        if (isInitiator) {
            trade.initiatorConfirmed = true;
        } else {
            trade.targetConfirmed = true;
        }

        this.broadcastTradeUpdate(trade);

        // If both confirmed, execute trade
        if (trade.initiatorConfirmed && trade.targetConfirmed) {
            await this.executeTrade(trade);
        }
    }

    async cancelTrade(playerId, reason = 'cancelled') {
        const tradeId = this.playerTrades.get(playerId);
        const trade = this.activeTrades.get(tradeId);

        if (!trade) return;

        // Notify both players
        this.broadcastToTrade(trade, 'trade:cancelled', { reason });

        // Clean up trade
        this.cleanupTrade(trade);

        this.logger.info(`Trade cancelled: ${tradeId}`, { reason });
    }

    async executeTrade(trade) {
        try {
            // Validate both players still have the items and gold
            if (!await this.validateTradeItems(trade)) {
                this.broadcastToTrade(trade, 'trade:failed', { reason: 'Items no longer available' });
                this.cleanupTrade(trade);
                return;
            }

            // Execute the exchange
            await this.exchangeTradeItems(trade);

            // Update statistics
            this.updateTradeStats(trade);

            // Notify players of successful trade
            this.broadcastToTrade(trade, 'trade:completed', {
                initiatorReceived: {
                    items: Object.fromEntries(trade.targetItems),
                    gold: trade.targetGold
                },
                targetReceived: {
                    items: Object.fromEntries(trade.initiatorItems),
                    gold: trade.initiatorGold
                }
            });

            // Clean up trade
            this.cleanupTrade(trade);

            // Emit trade completion event
            this.emit('economy:trade', {
                tradeId: trade.id,
                participants: [trade.initiator, trade.target],
                value: this.calculateTradeValue(trade)
            });

            this.logger.info(`Trade completed: ${trade.id}`);

        } catch (error) {
            this.logger.error('Error executing trade:', error);
            this.broadcastToTrade(trade, 'trade:failed', { reason: 'Execution failed' });
            this.cleanupTrade(trade);
        }
    }

    async buyFromMarket(playerId, listingId, quantity) {
        const listing = this.marketListings.get(listingId);
        if (!listing) {
            return this.sendError(playerId, 'Listing not found');
        }

        if (listing.seller === playerId) {
            return this.sendError(playerId, 'Cannot buy your own listing');
        }

        if (quantity > listing.quantity) {
            return this.sendError(playerId, 'Not enough items available');
        }

        const totalCost = listing.price * quantity;
        const tax = Math.floor(totalCost * this.ECONOMY_CONFIG.marketTaxRate);
        const totalWithTax = totalCost + tax;

        // Check if player has enough gold
        if (!await this.playerHasGold(playerId, totalWithTax)) {
            return this.sendError(playerId, 'Not enough gold');
        }

        // Execute purchase
        await this.executeMarketPurchase(playerId, listing, quantity, totalCost, tax);

        this.logger.info(`Market purchase: ${listing.item} x${quantity}`, {
            buyer: playerId,
            seller: listing.seller,
            cost: totalCost,
            tax
        });
    }

    async sellToMarket(playerId, itemId, quantity, price) {
        // Check listing limits
        const playerListings = Array.from(this.marketListings.values()).filter(l => l.seller === playerId);
        if (playerListings.length >= this.ECONOMY_CONFIG.maxListingsPerPlayer) {
            return this.sendError(playerId, 'Maximum listings reached');
        }

        // Check if player has the item
        if (!await this.playerHasItem(playerId, itemId, quantity)) {
            return this.sendError(playerId, 'You do not have enough of this item');
        }

        // Create market listing
        const listingId = await this.createMarketListing(playerId, itemId, quantity, price);

        this.io.to(playerId).emit('market:listingCreated', {
            listingId,
            item: itemId,
            quantity,
            price
        });

        this.logger.info(`Market listing created: ${itemId} x${quantity} @ ${price}`, { seller: playerId });
    }

    async createAuction(playerId, itemId, startingBid, duration) {
        // Validate duration
        if (duration > this.ECONOMY_CONFIG.auctionDuration) {
            duration = this.ECONOMY_CONFIG.auctionDuration;
        }

        // Check if player has the item
        if (!await this.playerHasItem(playerId, itemId, 1)) {
            return this.sendError(playerId, 'You do not have this item');
        }

        const auctionId = `auction_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
        
        const auction = {
            id: auctionId,
            seller: playerId,
            item: itemId,
            startingBid,
            currentBid: startingBid,
            currentBidder: null,
            startTime: Date.now(),
            endTime: Date.now() + duration,
            bids: [],
            status: 'active'
        };

        this.auctions.set(auctionId, auction);

        // Broadcast new auction
        this.io.emit('auction:created', {
            auctionId,
            item: itemId,
            startingBid,
            endTime: auction.endTime
        });

        this.logger.info(`Auction created: ${itemId}`, { seller: playerId, startingBid });
    }

    async placeBid(playerId, auctionId, bidAmount) {
        const auction = this.auctions.get(auctionId);
        if (!auction || auction.status !== 'active') {
            return this.sendError(playerId, 'Auction not found or inactive');
        }

        if (auction.seller === playerId) {
            return this.sendError(playerId, 'Cannot bid on your own auction');
        }

        if (Date.now() > auction.endTime) {
            return this.sendError(playerId, 'Auction has ended');
        }

        if (bidAmount <= auction.currentBid) {
            return this.sendError(playerId, 'Bid must be higher than current bid');
        }

        // Check if player has enough gold
        if (!await this.playerHasGold(playerId, bidAmount)) {
            return this.sendError(playerId, 'Not enough gold');
        }

        // Return gold to previous bidder
        if (auction.currentBidder) {
            await this.returnGoldToPlayer(auction.currentBidder, auction.currentBid);
        }

        // Reserve gold from new bidder
        await this.reserveGoldFromPlayer(playerId, bidAmount);

        // Update auction
        auction.currentBid = bidAmount;
        auction.currentBidder = playerId;
        auction.bids.push({
            bidder: playerId,
            amount: bidAmount,
            timestamp: Date.now()
        });

        // Broadcast bid update
        this.io.emit('auction:bidPlaced', {
            auctionId,
            currentBid: bidAmount,
            bidder: playerId
        });

        this.logger.info(`Bid placed: ${bidAmount}`, { auctionId, bidder: playerId });
    }

    // Market and pricing methods
    updateMarketPrices() {
        for (const [itemId, priceData] of this.itemPrices) {
            // Calculate price based on demand and supply
            const demandFactor = priceData.demand;
            const randomFactor = 0.9 + Math.random() * 0.2; // ±10% random variation
            
            let newPrice = priceData.base * demandFactor * randomFactor;
            
            // Apply gradual price changes
            const priceDiff = newPrice - priceData.current;
            const maxChange = priceData.base * this.ECONOMY_CONFIG.priceFluctuationRate;
            
            if (Math.abs(priceDiff) > maxChange) {
                newPrice = priceData.current + (priceDiff > 0 ? maxChange : -maxChange);
            }
            
            priceData.current = Math.max(1, Math.floor(newPrice));
        }

        // Broadcast price updates to all players
        this.io.emit('economy:priceUpdate', Object.fromEntries(this.itemPrices));
    }

    processAuctions() {
        const now = Date.now();
        
        for (const [auctionId, auction] of this.auctions) {
            if (auction.status === 'active' && now > auction.endTime) {
                this.finalizeAuction(auction);
            }
        }
    }

    async finalizeAuction(auction) {
        auction.status = 'completed';

        if (auction.currentBidder) {
            // Transfer item to winner
            await this.transferItemToPlayer(auction.currentBidder, auction.item, 1);
            
            // Calculate and transfer gold to seller
            const tax = Math.floor(auction.currentBid * this.ECONOMY_CONFIG.auctionTaxRate);
            const sellerAmount = auction.currentBid - tax;
            await this.giveGoldToPlayer(auction.seller, sellerAmount);

            // Notify winner and seller
            this.io.to(auction.currentBidder).emit('auction:won', {
                auctionId: auction.id,
                item: auction.item,
                finalBid: auction.currentBid
            });

            this.io.to(auction.seller).emit('auction:sold', {
                auctionId: auction.id,
                item: auction.item,
                finalBid: auction.currentBid,
                received: sellerAmount,
                tax
            });

            // Emit auction completion event
            this.emit('economy:auction', {
                auctionId: auction.id,
                winner: auction.currentBidder,
                seller: auction.seller,
                item: auction.item,
                finalPrice: auction.currentBid
            });
        } else {
            // No bids, return item to seller
            await this.returnItemToPlayer(auction.seller, auction.item, 1);
            
            this.io.to(auction.seller).emit('auction:noBids', {
                auctionId: auction.id,
                item: auction.item
            });
        }

        this.logger.info(`Auction finalized: ${auction.id}`, {
            winner: auction.currentBidder,
            finalBid: auction.currentBid
        });
    }

    cleanupExpiredTrades() {
        const now = Date.now();
        
        for (const [tradeId, trade] of this.activeTrades) {
            if (now > trade.expiresAt) {
                this.broadcastToTrade(trade, 'trade:expired', {});
                this.cleanupTrade(trade);
            }
        }
    }

    rebalanceEconomy() {
        // Adjust item demand based on trading patterns
        for (const [itemId, priceData] of this.itemPrices) {
            const soldCount = this.economyStats.topSellingItems.get(itemId) || 0;
            
            // Increase demand for popular items
            if (soldCount > 10) {
                priceData.demand = Math.min(3.0, priceData.demand * 1.1);
            } else if (soldCount < 2) {
                // Decrease demand for unpopular items
                priceData.demand = Math.max(0.5, priceData.demand * 0.95);
            }
        }

        // Reset selling statistics for next period
        this.economyStats.topSellingItems.clear();
        
        this.logger.info('Economy rebalanced');
    }

    updateEconomyStats() {
        // Calculate average transaction value
        if (this.economyStats.totalTransactions > 0) {
            this.economyStats.averageTransactionValue = 
                this.economyStats.totalGoldTraded / this.economyStats.totalTransactions;
        }

        // Calculate economy health based on various factors
        const transactionVolume = this.economyStats.totalTransactions;
        const priceStability = this.calculatePriceStability();
        
        this.economyStats.economyHealth = Math.min(100, 
            (transactionVolume / 100) * 30 + priceStability * 70);

        // Broadcast economy stats
        this.io.emit('economy:stats', this.economyStats);
    }

    calculatePriceStability() {
        let stability = 0;
        let count = 0;
        
        for (const [itemId, priceData] of this.itemPrices) {
            const variation = Math.abs(priceData.current - priceData.base) / priceData.base;
            stability += Math.max(0, 1 - variation);
            count++;
        }
        
        return count > 0 ? stability / count : 1;
    }

    // Utility methods (these would integrate with other systems)
    async playerHasItem(playerId, itemId, quantity) {
        // This would check with the InventoryManager
        return true; // Mock implementation
    }

    async playerHasGold(playerId, amount) {
        // This would check with the PlayerManager
        return true; // Mock implementation
    }

    async transferItemToPlayer(playerId, itemId, quantity) {
        // This would integrate with InventoryManager
        this.io.to(playerId).emit('inventory:itemReceived', { itemId, quantity });
    }

    async removeItemFromPlayer(playerId, itemId, quantity) {
        // This would integrate with InventoryManager
        this.io.to(playerId).emit('inventory:itemRemoved', { itemId, quantity });
    }

    async giveGoldToPlayer(playerId, amount) {
        // This would integrate with PlayerManager
        this.io.to(playerId).emit('player:goldReceived', { amount });
    }

    async takeGoldFromPlayer(playerId, amount) {
        // This would integrate with PlayerManager
        this.io.to(playerId).emit('player:goldSpent', { amount });
    }

    async reserveGoldFromPlayer(playerId, amount) {
        // Reserve gold for auction bids
        await this.takeGoldFromPlayer(playerId, amount);
    }

    async returnGoldToPlayer(playerId, amount) {
        // Return reserved gold
        await this.giveGoldToPlayer(playerId, amount);
    }

    async returnItemToPlayer(playerId, itemId, quantity) {
        await this.transferItemToPlayer(playerId, itemId, quantity);
    }

    async validateTradeItems(trade) {
        // Validate both players still have their items and gold
        const initiatorValid = await this.validatePlayerTradeItems(trade.initiator, trade.initiatorItems, trade.initiatorGold);
        const targetValid = await this.validatePlayerTradeItems(trade.target, trade.targetItems, trade.targetGold);
        
        return initiatorValid && targetValid;
    }

    async validatePlayerTradeItems(playerId, items, gold) {
        // Check if player has all items and gold
        for (const [itemId, quantity] of items) {
            if (!await this.playerHasItem(playerId, itemId, quantity)) {
                return false;
            }
        }
        
        return await this.playerHasGold(playerId, gold);
    }

    async exchangeTradeItems(trade) {
        // Remove items and gold from both players
        for (const [itemId, quantity] of trade.initiatorItems) {
            await this.removeItemFromPlayer(trade.initiator, itemId, quantity);
        }
        for (const [itemId, quantity] of trade.targetItems) {
            await this.removeItemFromPlayer(trade.target, itemId, quantity);
        }
        
        if (trade.initiatorGold > 0) {
            await this.takeGoldFromPlayer(trade.initiator, trade.initiatorGold);
        }
        if (trade.targetGold > 0) {
            await this.takeGoldFromPlayer(trade.target, trade.targetGold);
        }

        // Give items and gold to recipients
        for (const [itemId, quantity] of trade.initiatorItems) {
            await this.transferItemToPlayer(trade.target, itemId, quantity);
        }
        for (const [itemId, quantity] of trade.targetItems) {
            await this.transferItemToPlayer(trade.initiator, itemId, quantity);
        }
        
        if (trade.initiatorGold > 0) {
            await this.giveGoldToPlayer(trade.target, trade.initiatorGold);
        }
        if (trade.targetGold > 0) {
            await this.giveGoldToPlayer(trade.initiator, trade.targetGold);
        }
    }

    async executeMarketPurchase(buyerId, listing, quantity, cost, tax) {
        // Take gold from buyer
        await this.takeGoldFromPlayer(buyerId, cost + tax);
        
        // Give gold to seller
        await this.giveGoldToPlayer(listing.seller, cost);
        
        // Transfer item to buyer
        await this.transferItemToPlayer(buyerId, listing.item, quantity);
        
        // Update listing
        listing.quantity -= quantity;
        if (listing.quantity <= 0) {
            this.marketListings.delete(listing.id);
        }

        // Update item demand
        const priceData = this.itemPrices.get(listing.item);
        if (priceData) {
            priceData.demand = Math.min(3.0, priceData.demand * 1.05);
        }

        // Update statistics
        this.economyStats.totalTransactions++;
        this.economyStats.totalGoldTraded += cost;
        this.economyStats.totalItemsTraded += quantity;
        
        const currentCount = this.economyStats.topSellingItems.get(listing.item) || 0;
        this.economyStats.topSellingItems.set(listing.item, currentCount + quantity);

        // Notify seller if not NPC
        if (!listing.seller.startsWith('npc_')) {
            this.io.to(listing.seller).emit('market:itemSold', {
                item: listing.item,
                quantity,
                price: listing.price,
                buyer: buyerId,
                received: cost
            });
        }

        // Notify buyer
        this.io.to(buyerId).emit('market:purchaseComplete', {
            item: listing.item,
            quantity,
            cost: cost + tax,
            tax
        });
    }

    createMarketListing(sellerId, itemId, quantity, price, isNpc = false) {
        const listingId = `listing_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
        
        const listing = {
            id: listingId,
            seller: sellerId,
            item: itemId,
            quantity,
            price,
            createdAt: Date.now(),
            isNpc
        };

        this.marketListings.set(listingId, listing);

        // Broadcast new listing
        this.io.emit('market:newListing', listing);

        return listingId;
    }

    calculateTradeValue(trade) {
        let totalValue = trade.initiatorGold + trade.targetGold;
        
        for (const [itemId, quantity] of trade.initiatorItems) {
            const priceData = this.itemPrices.get(itemId);
            if (priceData) {
                totalValue += priceData.current * quantity;
            }
        }
        
        for (const [itemId, quantity] of trade.targetItems) {
            const priceData = this.itemPrices.get(itemId);
            if (priceData) {
                totalValue += priceData.current * quantity;
            }
        }
        
        return totalValue;
    }

    updateTradeStats(trade) {
        const tradeValue = this.calculateTradeValue(trade);
        
        this.economyStats.totalTransactions++;
        this.economyStats.totalGoldTraded += tradeValue;
        
        // Count items traded
        let itemsTraded = 0;
        for (const quantity of trade.initiatorItems.values()) {
            itemsTraded += quantity;
        }
        for (const quantity of trade.targetItems.values()) {
            itemsTraded += quantity;
        }
        this.economyStats.totalItemsTraded += itemsTraded;
    }

    broadcastToTrade(trade, event, data) {
        this.io.to(trade.initiator).emit(event, data);
        this.io.to(trade.target).emit(event, data);
    }

    broadcastTradeUpdate(trade) {
        const tradeData = {
            id: trade.id,
            initiatorItems: Object.fromEntries(trade.initiatorItems),
            targetItems: Object.fromEntries(trade.targetItems),
            initiatorGold: trade.initiatorGold,
            targetGold: trade.targetGold,
            initiatorReady: trade.initiatorReady,
            targetReady: trade.targetReady,
            initiatorConfirmed: trade.initiatorConfirmed,
            targetConfirmed: trade.targetConfirmed
        };

        this.broadcastToTrade(trade, 'trade:updated', tradeData);
    }

    cleanupTrade(trade) {
        this.activeTrades.delete(trade.id);
        this.playerTrades.delete(trade.initiator);
        this.playerTrades.delete(trade.target);
    }

    sendError(playerId, message) {
        this.io.to(playerId).emit('error', { message });
    }

    // Public API methods
    getMarketListings(itemId = null) {
        if (itemId) {
            return Array.from(this.marketListings.values()).filter(l => l.item === itemId);
        }
        return Array.from(this.marketListings.values());
    }

    getActiveAuctions() {
        return Array.from(this.auctions.values()).filter(a => a.status === 'active');
    }

    getItemPrice(itemId) {
        return this.itemPrices.get(itemId);
    }

    getEconomyStats() {
        return this.economyStats;
    }

    isPlayerInTrade(playerId) {
        return this.playerTrades.has(playerId);
    }

    update() {
        // Called from game engine every tick
        // High-frequency updates are handled by internal loops
    }
}

module.exports = EconomyManager;