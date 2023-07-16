class AccountForm {
  constructor(args) {
    this.verificationEnabled = args.verificationEnabled;
    this.isVerified = args.isVerified;
    this.originalPhone = args.originalPhone.toString().replace(/[^0-9]/, '');
    this.form = document.getElementById('account-info-form');
    this.form.addEventListener('submit', (event) => {
      // No special processing if phone number verification is disabled
      if (!this.verificationEnabled) {
        return;
      }

      let updatedPhone = this.form.querySelector('input[type=tel]').value;
      updatedPhone = updatedPhone.replace(/[^0-9]/g, '');
      const phoneNumberChanged = updatedPhone !== this.originalPhone;

      // No special processing if phone number isn't changing
      if (!phoneNumberChanged) {
        return;
      }

      if (confirm(
        'If you update your phone number, you\'ll be sent a text message to verify the new number. Continue?'
      )) {
        return;
      }

      event.preventDefault();
    })
  }
}
