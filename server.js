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
    rooms[roomId] = {
      name: roomName,
      players: [],
      game: { moves: {} }, // Rock-Paper-Scissors state
    };
    io.emit('rooms:list', rooms);
    if (cb) cb({ roomId });
  });

  socket.on('room:join', (roomId, cb) => {
    const room = rooms[roomId];
    if (!room) return cb && cb({ error: 'Room not found' });
    socket.join(roomId);
    room.players.push(socket.username);
    io.to(roomId).emit('room:update', room);
    // Reset game if more than 2 players or someone joins mid-round
    room.game.moves = {};
    if (cb) cb({ ok: true });
  });

  socket.on('room:leave', (roomId, cb) => {
    const room = rooms[roomId];
    if (!room) return cb && cb({ error: 'Room not found' });
    socket.leave(roomId);
    room.players = room.players.filter((p) => p !== socket.username);
    io.to(roomId).emit('room:update', room);
    // Reset game state if player leaves
    room.game.moves = {};
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

  // Mini-game: Rock-Paper-Scissors
  socket.on('game:move', (roomId, move) => {
    const room = rooms[roomId];
    if (!room) return;
    const validMoves = ['rock', 'paper', 'scissors'];
    if (!validMoves.includes(move)) return;

    room.game.moves[socket.username] = move;

    const playerNames = Object.keys(room.game.moves);
    if (playerNames.length < 2) {
      // Wait for opponent
      io.to(roomId).emit('game:state', { moves: room.game.moves });
      return;
    }

    const [p1, p2] = playerNames;
    const m1 = room.game.moves[p1];
    const m2 = room.game.moves[p2];

    function beats(a, b) {
      return (
        (a === 'rock' && b === 'scissors') ||
        (a === 'scissors' && b === 'paper') ||
        (a === 'paper' && b === 'rock')
      );
    }

    let result;
    if (m1 === m2) result = 'draw';
    else if (beats(m1, m2)) result = p1;
    else result = p2;

    io.to(roomId).emit('game:result', {
      moves: room.game.moves,
      result,
    });

    // Reset for next round
    room.game.moves = {};
  });
});

server.listen(PORT, () => {
  /* eslint-disable no-console */
  console.log(`📡 Server listening on http://localhost:${PORT}`);
});