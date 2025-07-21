// Elements
const loginSection = document.getElementById('loginSection');
const lobbySection = document.getElementById('lobbySection');
const gameSection = document.getElementById('gameSection');

const loginUsername = document.getElementById('loginUsername');
const loginPassword = document.getElementById('loginPassword');
const signupBtn = document.getElementById('signupBtn');
const loginBtn = document.getElementById('loginBtn');
const loginError = document.getElementById('loginError');

const newRoomName = document.getElementById('newRoomName');
const createRoomBtn = document.getElementById('createRoomBtn');
const roomsList = document.getElementById('roomsList');

const roomTitle = document.getElementById('roomTitle');
const messagesEl = document.getElementById('messages');
const inputEl = document.getElementById('msgInput');
const sendBtn = document.getElementById('sendBtn');
const rpsControls = document.getElementById('rpsControls');
const rpsStatus = document.getElementById('rpsStatus');

let socket;
let currentRoomId = null;

function setSection(section) {
  loginSection.style.display = section === 'login' ? 'block' : 'none';
  lobbySection.style.display = section === 'lobby' ? 'block' : 'none';
  gameSection.style.display = section === 'game' ? 'block' : 'none';
}

function appendMessage(text) {
  const li = document.createElement('li');
  li.textContent = text;
  messagesEl.appendChild(li);
  messagesEl.scrollTop = messagesEl.scrollHeight;
}

async function api(path, body) {
  const res = await fetch(`/api/${path}`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(body),
  });
  return res.json();
}

async function handleAuth(isSignup) {
  const username = loginUsername.value.trim();
  const password = loginPassword.value.trim();
  if (!username || !password) return;
  const { token, error } = await api(isSignup ? 'signup' : 'login', { username, password });
  if (error) {
    loginError.textContent = error;
    return;
  }
  localStorage.setItem('token', token);
  initSocket(token);
  setSection('lobby');
}

signupBtn.addEventListener('click', () => handleAuth(true));
loginBtn.addEventListener('click', () => handleAuth(false));

function initSocket(token) {
  socket = io({ auth: { token } });

  socket.on('rooms:list', (rooms) => {
    roomsList.innerHTML = '';
    Object.entries(rooms).forEach(([id, room]) => {
      const li = document.createElement('li');
      li.textContent = `${room.name} (${room.players.length})`;
      li.style.cursor = 'pointer';
      li.addEventListener('click', () => joinRoom(id, room.name));
      roomsList.appendChild(li);
    });
  });

  socket.on('room:update', (room) => {
    if (roomTitle.dataset.roomId === currentRoomId) {
      roomTitle.textContent = `${room.name} — игроков: ${room.players.length}`;
    }
  });

  socket.on('chat:msg', ({ username, msg }) => {
    appendMessage(`${username}: ${msg}`);
  });

  socket.on('game:state', ({ moves }) => {
    const count = Object.keys(moves).length;
    rpsStatus.textContent = count === 1 ? 'Ожидаем ход соперника...' : '';
  });

  socket.on('game:result', ({ moves, result }) => {
    const entries = Object.entries(moves).map(([u, m]) => `${u}: ${m}`).join(', ');
    let text;
    if (result === 'draw') text = `Ничья! (${entries})`;
    else text = `Победил ${result}! (${entries})`;
    rpsStatus.textContent = text;
  });
}

createRoomBtn.addEventListener('click', () => {
  const name = newRoomName.value.trim();
  if (!name) return;
  socket.emit('room:create', name, ({ roomId }) => {
    joinRoom(roomId, name);
  });
});

function joinRoom(roomId, roomName) {
  socket.emit('room:join', roomId, (res) => {
    if (res?.error) return alert(res.error);
    currentRoomId = roomId;
    roomTitle.dataset.roomId = roomId;
    roomTitle.textContent = roomName;
    messagesEl.innerHTML = '';
    setSection('game');
  });
}

sendBtn.addEventListener('click', () => {
  const text = inputEl.value.trim();
  if (text) {
    socket.emit('chat:msg', text);
    inputEl.value = '';
  }
});

inputEl.addEventListener('keydown', (e) => {
  if (e.key === 'Enter') sendBtn.click();
});

// Handle RPS move buttons
rpsControls.addEventListener('click', (e) => {
  if (!e.target.classList.contains('moveBtn')) return;
  const move = e.target.dataset.move;
  if (!currentRoomId) return;
  socket.emit('game:move', currentRoomId, move);
  rpsStatus.textContent = 'Ждём результат...';
});

// Auto-login if token exists
const savedToken = localStorage.getItem('token');
if (savedToken) {
  setSection('lobby');
  initSocket(savedToken);
} else {
  setSection('login');
}