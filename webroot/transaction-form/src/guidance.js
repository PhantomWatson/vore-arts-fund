import {TYPE_LOAN, TYPE_DONATION, TYPE_CANCELED_CHECK, TYPE_LOAN_REPAYMENT} from './transactionTypes.js';

function guidanceAmountGross(type) {
  switch (+type) {
    case TYPE_DONATION:
    case TYPE_LOAN_REPAYMENT:
      return 'The total amount paid, including processing fees';
    case TYPE_CANCELED_CHECK:
      return 'The value of the check';
    case TYPE_LOAN:
      return 'The amount of the loan';
    default:
      return 'The amount paid or received';
  }
}

function guidanceProject(type) {
  switch (+type) {
    case TYPE_LOAN:
    case TYPE_LOAN_REPAYMENT:
      return 'The project that this loan supported';
    case TYPE_CANCELED_CHECK:
      return 'The project that this loan was intended for';
    default:
      return '(Optional) The project that this transaction is associated with';
  }
}

function guidanceName(type) {
  switch (+type) {
    case TYPE_DONATION:
    case TYPE_LOAN_REPAYMENT:
      return 'The person paying us (leave blank for anonymous)';
    case TYPE_CANCELED_CHECK:
    case TYPE_LOAN:
      return 'The person the check was payable to';
    default:
      return 'The person or business that money was paid to or received from';
  }
}
function guidanceMetadata(type) {
  switch (+type) {
    case TYPE_DONATION:
    case TYPE_LOAN_REPAYMENT:
      return 'This is for any other information about this transaction that\'s important to remember.';
    case TYPE_LOAN:
      return 'Include the check number here';
    case TYPE_CANCELED_CHECK:
      return 'Include the check number here, as well as (optionally) the reason why the check was canceled';
    default:
      return 'This is for any other information about this transaction that\'s important to remember. For any checks written, the check number should be recorded here.';
  }
}

export {guidanceName, guidanceMetadata, guidanceProject, guidanceAmountGross};
