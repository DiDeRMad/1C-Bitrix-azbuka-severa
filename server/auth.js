const express = require('express');
const jwt = require('jsonwebtoken');
const { registerUser, validateUser } = require('./userStore');

const JWT_SECRET = process.env.JWT_SECRET || 'superpupersecret';
const router = express.Router();

function generateToken(username) {
  return jwt.sign({ username }, JWT_SECRET, { expiresIn: '7d' });
}

router.post('/signup', async (req, res) => {
  const { username, password } = req.body;
  if (!username || !password) return res.status(400).json({ error: 'Missing fields' });
  try {
    await registerUser(username, password);
    const token = generateToken(username);
    return res.json({ token });
  } catch (err) {
    return res.status(400).json({ error: err.message });
  }
});

router.post('/login', async (req, res) => {
  const { username, password } = req.body;
  if (!username || !password) return res.status(400).json({ error: 'Missing fields' });
  const valid = await validateUser(username, password);
  if (!valid) return res.status(401).json({ error: 'Invalid credentials' });
  const token = generateToken(username);
  return res.json({ token });
});

function verifySocket(socket, next) {
  const token = socket.handshake.auth?.token;
  if (!token) return next(new Error('No token'));
  try {
    const payload = jwt.verify(token, JWT_SECRET);
    socket.username = payload.username;
    return next();
  } catch (_) {
    return next(new Error('Invalid token'));
  }
}

module.exports = { router, verifySocket };