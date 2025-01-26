class ExpiredSessionHandler {
  constructor(formId) {
    if (!this.initializeProperties(formId)) {
      return;
    }

    this.initializeBaseFormSubmit(formId);
    this.initializeLoginFormSubmit();
    this.setUpModalClosePrevention();
  }

  async handleLogin() {
    const {result} = await fetch(
      '/api/users/login',
      {
        method: 'POST',
        body: new FormData(this.loginForm),
      }
    )
      .then((r) => r.json());

    return result ?? false;
  }

  async checkSession() {
    const { hasSession } = await fetch(
      '/api/users/has-session',
      {
        headers: { 'Content-Type': 'application/json' }
      }
    )
      .then((r) => r.json());

    return !!hasSession ?? false;
  }

  hideError() {
    if (this.errorMsgContainer) {
      this.errorMsgContainer.style.display = 'none';
    }
  }

  showError() {
    // Backup in case the element is missing
    if (this.errorMsgContainer) {
      this.errorMsgContainer.style.display = 'block';
      return;
    }

    alert('Incorrect email or password');
  }

  showFormSubmitLoadingIndicator(button) {
    button.classList.add('loading');

    const loadingIndicator = document.createElement('span');
    loadingIndicator.classList.add('loading-indicator');
    const icon = document.createElement('i');
    icon.classList.add('fa-solid', 'fa-spinner', 'fa-spin-pulse');
    loadingIndicator.appendChild(icon);

    // Add spinner to clicked button (doesn't work for input type="submit" because those can't have children)
    if (button.tagName === 'BUTTON') {
      button.append(loadingIndicator);
    } else {
      button.after(loadingIndicator);
    }

    // Disable all buttons (since there might be multiple)
    button.closest('form').querySelectorAll('button, input[type="submit"]').forEach(btn => {
      btn.disabled = true;
    });
  }

  hideFormSubmitLoadingIndicator(button) {
    // Remove loading indicator that may have been inserted as a child or sibling
    button.classList.remove('loading');
    button.parentElement.querySelector('.loading-indicator').remove();

    // Re-enable all submit buttons
    button.closest('form').querySelectorAll('button, input[type="submit"]').forEach(btn => {
      btn.disabled = false;
    });
  }

  initializeProperties(formId) {
    this.baseForm = document.getElementById(formId);
    if (!this.baseForm) {
      console.error('Form not found');
      return false;
    }

    this.modal = document.getElementById('login-modal');
    if (!this.modal) {
      console.error('Login modal not found');
      return false;
    }
    this.bsModal = new bootstrap.Modal(this.modal);
    this.errorMsgContainer = this.modal.querySelector('.alert');

    this.loginForm = this.modal.querySelector('form');
    if (!this.loginForm) {
      console.error('Login form not found in modal');
      return false;
    }

    // To avoid infinite checking loop
    this.sessionLastConfirmed = null;

    return true;
  }

  initializeBaseFormSubmit(formId) {
    this.baseForm.addEventListener('submit', async (e) => {
      e.preventDefault();

      this.baseSubmitBtn = e.submitter;
      this.showFormSubmitLoadingIndicator(this.baseSubmitBtn);

      // If we've confirmed a session recently, submit the form
      const date = new Date();
      const fiveMinutesAgo = date.setMinutes(date.getMinutes() - 5);
      if (this.sessionLastConfirmed >= fiveMinutesAgo) {
        this.baseForm.submit();
        return;
      }

      // Otherwise, if there's an active session, submit the form
      const hasSession = await this.checkSession();
      if (hasSession) {
        this.sessionLastConfirmed = new Date();
        this.baseForm.submit();
        return;
      }

      // Otherwise, halt submission and display a login popup
      this.bsModal.show();
      this.hideFormSubmitLoadingIndicator(this.baseSubmitBtn);
    });
  }

  initializeLoginFormSubmit() {
    this.loginForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      this.hideError();

      this.loginSubmitBtn = e.submitter;
      this.showFormSubmitLoadingIndicator(this.loginSubmitBtn);

      const loginResult = await this.handleLogin();
      if (loginResult) {
        this.sessionLastConfirmed = new Date();
        this.bsModal.hide();
        this.baseForm.submit();
        return;
      }

      this.showError();
      this.loginForm.querySelector('input[type="password"]').value = '';
      this.hideFormSubmitLoadingIndicator(this.loginSubmitBtn);
    });
  }

  setUpModalClosePrevention() {
    this.modal.addEventListener('hide.bs.modal', (e) => {
      const loadingIndicator = this.modal.querySelector('.loading-indicator');
      if (loadingIndicator) {
        e.preventDefault();
      }
    });
  }
}
