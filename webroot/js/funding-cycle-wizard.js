preventMultipleSubmit('#funding-cycle-form');

const checkbox = document.getElementById('wizard-checkbox');
const container = document.getElementById('wizard-container');
const form = document.getElementById('wizard-form');

const yearSelector = document.getElementById('wizard-year');
const quarterSelector = document.getElementById('wizard-quarter');

const applicationBegin = document.getElementById('application-begin');
const applicationEnd = document.getElementById('application-end');
const resubmitDeadline = document.getElementById('resubmit-deadline');
const voteBegin = document.getElementById('vote-begin');
const voteEnd = document.getElementById('vote-end');

checkbox.addEventListener('change', (event) => {
  container.style.display = event.target.checked ? 'block' : 'none';
});

function setDateTime(input, month, year, startOrEnd) {
  let date = new Date(input.value);
  month = month - 1;
  date.setUTCFullYear(year, month);
  if (startOrEnd === 'start') {
    date.setUTCDate(1);
    date.setUTCHours(0, 0, 0);
  } else {
    date.setUTCDate((new Date(year, month + 1, 0)).getUTCDate());
    date.setUTCHours(23, 59, 59);
  }
  input.value = date.toISOString().split('T')[0];
}

form.addEventListener('submit', (event) => {
  event.preventDefault();
  const year = yearSelector.value;
  const quarter = parseInt(quarterSelector.value);
  if (!year || !quarter) {
    alert('You have to select both a year and a quarter');
    return;
  }

  const applicationBeginMonth = ((quarter - 1) * 3) - 1;
  setDateTime(
    applicationBegin,
    quarter === 1 ? 11 : applicationBeginMonth,
    quarter === 1 ? year - 1 : year,
    'start'
  );
  const applicationEndMonth = applicationBeginMonth + 2;
  setDateTime(
    applicationEnd,
    quarter === 1 ? 1 : applicationEndMonth,
    year,
    'end'
  );
  setDateTime(
    resubmitDeadline,
    quarter === 1 ? 1 : applicationEndMonth,
    year,
    'end'
  );
  const voteBeginMonth = applicationBeginMonth + 4;
  setDateTime(
    voteBegin,
    quarter === 1 ? 3 : voteBeginMonth,
    year,
    'start'
  );
  setDateTime(
    voteEnd,
    quarter === 1 ? 3 : voteBeginMonth,
    year,
    'end'
  );
});
