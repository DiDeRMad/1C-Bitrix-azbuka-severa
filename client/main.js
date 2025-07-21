// SPOG Client – connects to Socket.IO server
const socket = io();

const messagesEl = document.getElementById('messages');
const inputEl = document.getElementById('msgInput');
const sendBtn = document.getElementById('sendBtn');

function appendMessage(text) {
  const li = document.createElement('li');
  li.textContent = text;
  messagesEl.appendChild(li);
  messagesEl.scrollTop = messagesEl.scrollHeight;
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

socket.on('chat:msg', ({ id, msg }) => {
  appendMessage(`${id.substring(0, 4)}: ${msg}`);
});