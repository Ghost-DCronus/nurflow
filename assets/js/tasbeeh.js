// assets/js/tasbeeh.js — Digital Tasbeeh Counter Engine

let currentCount = 0;
let targetCount = 33;

const counterDisplay = document.getElementById('tasbeehCount');
const targetDisplay = document.getElementById('targetDisplay');
const dhikrSelect = document.getElementById('dhikrSelect');

function incrementCounter() {
  currentCount++;
  updateUI();

  // Optional haptic feedback for supported devices
  if (window.navigator && window.navigator.vibrate) {
    window.navigator.vibrate(40);
  }
}

function resetCounter() {
  if (currentCount > 0) {
    saveDhikrProgress(currentCount);
  }
  currentCount = 0;
  updateUI();
}

function setTarget(newTarget) {
  targetCount = parseInt(newTarget, 10);
  targetDisplay.innerText = targetCount;
  resetCounter();
}

function updateUI() {
  if (counterDisplay) {
    counterDisplay.innerText = currentCount;
  }
}

function saveDhikrProgress(countToSave) {
  const selectedDhikr = dhikrSelect ? dhikrSelect.value : 'SubhanAllah';

  fetch('../api/save_progress.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      type: 'dhikr',
      dhikr_title: selectedDhikr,
      count: countToSave,
      target: targetCount
    })
  })
  .then(res => res.json())
  .then(data => {
    if (data.status !== 'success') {
      console.error('Failed to log dhikr:', data.message);
    }
  })
  .catch(err => console.error('Error saving dhikr:', err));
}

// Global click event bound to counter area
document.addEventListener('DOMContentLoaded', () => {
  updateUI();
});