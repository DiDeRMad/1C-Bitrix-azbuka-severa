// Basic HTTP + Socket.IO server for the SPOG MVP

const path = require('path');
const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const bodyParser = require('express').json;
const { router: authRouter, verifySocket } = require('./server/auth');

const PORT = process.env.PORT || 3000;
const app = express();

// Serve static client files
app.use(express.static(path.join(__dirname, 'client')));
app.use(bodyParser());
app.use('/api', authRouter);

const server = http.createServer(app);
const io = new Server(server);

io.use(verifySocket);

// Lobby data
const rooms = {};

io.on('connection', (socket) => {
  console.log(`🔌 ${socket.username} connected: ${socket.id}`);

  // Send initial room list
  socket.emit('rooms:list', rooms);

  socket.on('chat:msg', (msg) => {
    io.emit('chat:msg', { username: socket.username, msg });
  });

  socket.on('room:create', (roomName, cb) => {
    const roomId = Date.now().toString(36);
    rooms[roomId] = { name: roomName, players: [] };
    io.emit('rooms:list', rooms);
    if (cb) cb({ roomId });
  });

  socket.on('room:join', (roomId, cb) => {
    const room = rooms[roomId];
    if (!room) return cb && cb({ error: 'Room not found' });
    socket.join(roomId);
    room.players.push(socket.username);
    io.to(roomId).emit('room:update', room);
    if (cb) cb({ ok: true });
  });

  socket.on('room:leave', (roomId, cb) => {
    const room = rooms[roomId];
    if (!room) return cb && cb({ error: 'Room not found' });
    socket.leave(roomId);
    room.players = room.players.filter((p) => p !== socket.username);
    io.to(roomId).emit('room:update', room);
    if (cb) cb({ ok: true });
  });

  socket.on('disconnect', () => {
    console.log(`❌ ${socket.username} disconnected`);
    // Remove from rooms
    for (const room of Object.values(rooms)) {
      room.players = room.players.filter((p) => p !== socket.username);
    }
    io.emit('rooms:list', rooms);
  });
});

server.listen(PORT, () => {
  /* eslint-disable no-console */
  console.log(`📡 Server listening on http://localhost:${PORT}`);
});