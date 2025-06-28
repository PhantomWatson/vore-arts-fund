import './App.css';
import {useEffect, useState} from 'react';

function App() {
  const [paymentOption, setPaymentOption] = useState<string|null>(null);
  const [amountTowardBalance, setAmountTowardBalance] = useState<string>('0.00');
  const [fee, setFee] = useState<number>(0);
  const [total, setTotal] = useState<number>(0);

  interface FormSetupData {
    projectId: number,
    balance: number,
    processingFee: {
      percentage: number,
      flat: number
    }
  }

  const setupData = (window as any)?.repaymentFormData as FormSetupData | undefined;
  const balance = setupData?.balance;
  const projectId = setupData?.projectId;
  const processingFeeFlat = setupData?.processingFee?.flat;
  const processingFeePercentage = setupData?.processingFee?.percentage;

  useEffect(() => {
    if (paymentOption === 'full') {
      setAmountTowardBalance((balance || 0).toFixed(2));
    }
  }, [paymentOption]);

  useEffect(() => {
    const fee = (parseInt(amountTowardBalance) * (processingFeePercentage || 0)) + (processingFeeFlat || 0) / 100;
    setFee(fee);
    setTotal(parseFloat(amountTowardBalance) + fee);
  }, [amountTowardBalance]);

  if (
    balance === undefined
    || projectId === undefined
    || processingFeeFlat === undefined
    || processingFeePercentage === undefined
  ) {
    console.error('Cannot load repayment-form; not all setup data is available.', setupData);
    return (
      <div className="alert alert-danger" role="alert">
        Error: Cannot load repayment form.
        Please try reloading this page, or <a href="/contact">contact us</a> for assistance.
      </div>
    );
  }

  if (balance === 0) {
    console.log('No balance to repay, skipping repayment form.');
    return <></>
  }

  return (
    <form method="post" action={`/my/loans/${projectId}/payment-process`}>
      <input type="hidden" name="paymentOption" value={paymentOption || ""} />
      <input type="hidden" name="total" value={total} />
      <div className="form-group">
        <div className="form-check">
          <label htmlFor="payment-option-full" className="form-check-label">
            <input
              className="form-check-input"
              type="radio"
              name="paymentOption"
              value="full"
              id="payment-option-full"
              checked={paymentOption === 'full'}
              onChange={(e) => setPaymentOption(e.target.value)}
            />
            Pay off remaining balance
          </label>
        </div>
        {paymentOption === 'full' &&
          <input type="hidden" name="amountTowardBalance" value={balance} />
        }
        <div className="form-check">
          <label htmlFor="payment-option-custom" className="form-check-label">
            <input
              className="form-check-input"
              type="radio"
              name="paymentOption"
              value="custom"
              id="payment-option-custom"
              checked={paymentOption === 'custom'}
              onChange={(e) => setPaymentOption(e.target.value)}
            />
            Pay custom amount{paymentOption === 'custom' ? ':' : ''}
          </label>
        </div>
        {paymentOption === 'custom' &&
          <div className="form-group">
            <label htmlFor="repayment-amount" className="visually-hidden">Payment amount</label>
            <div className="input-group">
              <div className="input-group-text">$</div>
              <input
                type="number"
                className="form-control"
                id="repayment-amount"
                name="amountTowardBalance"
                min={0}
                max={balance}
                step={0.01}
                value={amountTowardBalance}
                onChange={(e) => setAmountTowardBalance(e.target.value)}
                onBlur={(e) => setAmountTowardBalance(parseFloat(e.target.value).toFixed(2))}
              />
            </div>
          </div>
        }
      </div>

      {paymentOption !== null &&
        <>
          <table className="table repayment-breakdown-table">
            <tbody>
              <tr>
                <th>Payment to Vore Arts Fund</th>
                <td>${parseFloat(amountTowardBalance).toFixed(2)}</td>
              </tr>
              <tr>
                <th>Processing fee</th>
                <td>${parseFloat(amountTowardBalance) > 0 ? fee.toFixed(2) : '0.00'}</td>
              </tr>
              <tr>
                <th>Total</th>
                <td>${parseFloat(amountTowardBalance) > 0 ? total.toFixed(2) : '0.00'}</td>
              </tr>
            </tbody>
          </table>
          <input type="hidden" name="total" value={total} />
        </>
      }

      <button
        type="submit"
        className="btn btn-primary"
        disabled={paymentOption === null}
      >
        Proceed to payment
      </button>
    </form>
  );
}

export default App
