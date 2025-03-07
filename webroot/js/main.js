function preventMultipleSubmit(formQuery) {
  const form = document.querySelector(formQuery);
  if (!form) {
    console.error('Form not found using query ' + formQuery);
    return;
  }
  form.addEventListener('submit', function () {
    const button = this.querySelector('button[type="submit"]');
    const loadingIndicator = document.createElement('i')
    loadingIndicator.className = 'fa-solid fa-spinner fa-spin-pulse';
    loadingIndicator.title = 'Loading';
    button.appendChild(loadingIndicator);
    button.setAttribute('disabled', 'disabled');
  }, false);
}
