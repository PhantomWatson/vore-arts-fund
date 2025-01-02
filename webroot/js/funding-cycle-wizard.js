preventMultipleSubmit('#funding-cycle-form');

const checkbox = document.getElementById('wizard-checkbox');
const container = document.getElementById('wizard-container');
const form = document.getElementById('wizard-form');

const yearSelector = document.getElementById('wizard-year');
const monthSelector = document.getElementById('wizard-month');

const applicationBegin = document.getElementById('application-begin');
const applicationEnd = document.getElementById('application-end');
const resubmitDeadline = document.getElementById('resubmit-deadline');
const voteBegin = document.getElementById('vote-begin');
const voteEnd = document.getElementById('vote-end');

checkbox.addEventListener('change', (event) => {
  container.style.display = event.target.checked ? 'block' : 'none';
});

function setDateTime(input, month, year, dateDescription) {
  const monthIndex = month - 1;
  let date = new Date();
  date.setUTCFullYear(year, monthIndex);

  // Set date to the beginning, middle (end of 14th day), or end of month
  switch (dateDescription) {
    case 'start':
      date.setUTCDate(1);
      date.setUTCHours(0, 0, 0);
      break;
    case 'middle':
      date.setUTCDate(14);
      date.setUTCHours(23, 59, 59);
      break;
    case 'end':
      date.setUTCDate((new Date(year, monthIndex + 1, 0)).getUTCDate());
      date.setUTCHours(23, 59, 59);
      break;
  }

  input.value = date.toISOString().split('T')[0];
}

form.addEventListener('submit', (event) => {
  event.preventDefault();
  const year = yearSelector.value;
  const applicationBeginMonth = parseInt(monthSelector.value);
  if (!year || !applicationBeginMonth) {
    alert('You have to select both a year and a month');
    return;
  }

  setDateTime(applicationBegin, applicationBeginMonth, year, 'start');
  setDateTime(applicationEnd, applicationBeginMonth, year, 'end');
  setDateTime(resubmitDeadline, applicationBeginMonth + 1, year, 'middle');
  setDateTime(voteBegin, applicationBeginMonth + 2, year, 'start');
  setDateTime(voteEnd, applicationBeginMonth + 2, year, 'end');
});
