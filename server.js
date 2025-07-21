// Basic HTTP + Socket.IO server for the SPOG MVP

const path = require('path');
const express = require('express');
const http = require('http');
const { Server } = require('socket.io');

const PORT = process.env.PORT || 3000;
const app = express();

// Serve static client files
app.use(express.static(path.join(__dirname, 'client')));

const server = http.createServer(app);
const io = new Server(server);

io.on('connection', (socket) => {
  console.log(`🔌 New client connected: ${socket.id}`);

  // Relay chat messages to all clients
  socket.on('chat:msg', (msg) => {
    io.emit('chat:msg', { id: socket.id, msg });
  });

  socket.on('disconnect', () => {
    console.log(`❌ Client disconnected: ${socket.id}`);
  });
});

server.listen(PORT, () => {
  /* eslint-disable no-console */
  console.log(`📡 Server listening on http://localhost:${PORT}`);
});