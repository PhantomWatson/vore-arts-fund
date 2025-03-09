import {TYPE_CANCELED_CHECK, TYPE_DONATION, TYPE_LOAN} from "./transactionTypes.js";

function showProjectSelector(transactionType) {
  switch (+transactionType) {
    case TYPE_DONATION:
      return false;
    default:
      return true;
  }
}

function showAmountNet(transactionType) {
  switch (+transactionType) {
    case TYPE_LOAN:
    case TYPE_CANCELED_CHECK:
      return false;
    default:
      return true;
  }
}

export {showAmountNet, showProjectSelector};
