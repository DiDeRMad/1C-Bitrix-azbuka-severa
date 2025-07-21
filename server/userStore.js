const bcrypt = require('bcryptjs');

// In-memory user database { username: { passwordHash } }
const users = {};

async function registerUser(username, password) {
  if (users[username]) throw new Error('User already exists');
  const passwordHash = await bcrypt.hash(password, 10);
  users[username] = { passwordHash };
}

async function validateUser(username, password) {
  const user = users[username];
  if (!user) return false;
  return bcrypt.compare(password, user.passwordHash);
}

module.exports = {
  registerUser,
  validateUser,
};