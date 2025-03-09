const testConfig = {
  action: 'add',
  cycles: [{id: 1, name: 'Cycle name', projects: [{id: 3, title: 'Project name'}]}],
  transactionTypes: [{id: 1, name: 'Donation'}, {id: 2, name: 'Bribe'}],
  endpointUrl: '/api/transactions',
};
//window.transactionForm = testConfig;

/*
 * Config values that could be set in window.transactionForm:
 *  - action: add|edit
 *  - cycles: [{id: 1, name: 'Cycle name', projects: [{id: 3, title: 'Project name'}]}],
 *  - transactionTypes: [{id: ..., name:...}]
 *  - endpointURL: ...
 */

const getConfig = () => {
  const config = window.transactionForm;
  console.log(config);
  const action = config.action ?? false;
  let errorLoadingConfig = '';
  if (action !== 'add' && action !== 'edit') {
    errorLoadingConfig += 'Invalid value for window.transactionForm.action. ';
  }
  const transactionTypes = config.transactionTypes ?? false;
  if (!transactionTypes) {
    errorLoadingConfig += 'window.transactionForm.transactionTypes not set. ';
  }
  const endpointUrl = config.endpointUrl ?? false;
  if (!endpointUrl) {
    errorLoadingConfig += 'window.transactionForm.endpointUrl not set. ';
  }
  const cycles = config.cycles ?? [];
  const transaction = config.transaction ?? {};

  return {action, transactionTypes, endpointUrl, cycles, transaction, errorLoadingConfig};
}

export {getConfig};
