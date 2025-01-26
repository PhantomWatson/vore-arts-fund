class ExpiredSessionHandler {
  constructor(formId) {
    this.form = document.getElementById(formId);
    if (!this.form) {
      console.error('Form not found');
      return;
    }

    this.modal = document.getElementById('login-modal');
    if (!this.modal) {
      console.error('Login modal not found');
      return;
    }
    this.bsModal = new bootstrap.Modal(this.modal);
    this.errorMsgContainer = this.modal.querySelector('.alert');

    this.modalForm = this.modal.querySelector('form');
    if (!this.modalForm) {
      console.error('Login form not found in modal');
      return;
    }

    // To avoid infinite checking loop
    this.sessionLastConfirmed = null;

    // Handle submitting the protected form
    this.form.addEventListener('submit', async (e) => {
      e.preventDefault();

      // If we've confirmed a session recently, submit the form
      const date = new Date();
      const fiveMinutesAgo = date.setMinutes(date.getMinutes() - 5);
      if (this.sessionLastConfirmed >= fiveMinutesAgo) {
        this.form.submit();
        return;
      }

      // Otherwise, if there's an active session, submit the form
      const hasSession = await this.checkSession();
      if (false && hasSession) {
        this.sessionLastConfirmed = new Date();
        this.form.submit();
        return;
      }

      // Otherwise, halt submission and display a login popup
      this.bsModal.show();
    });

    // Handle submitting the login form
    this.modalForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      this.hideError();
      const loginResult = await this.handleLogin();
      if (loginResult) {
        this.sessionLastConfirmed = new Date();
        this.bsModal.hide();
        this.form.submit();
        return;
      }

      this.showError();
      this.modalForm.querySelector('input[type="password"]').value = '';
    });
  }

  async handleLogin() {
    const { result } = await fetch(
      '/api/users/login',
      {
        method: 'POST',
        body: new FormData(this.modalForm),
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
}
