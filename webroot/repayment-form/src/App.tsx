import './App.css';
import {useEffect, useState} from 'react';

function App() {
  const [paymentOption, setPaymentOption] = useState<string|null>(null);
  const [amount, setAmount] = useState<string>('0.00');
  const [fee, setFee] = useState<number>(0);

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
      setAmount((balance || 0).toFixed(2));
    }
  }, [paymentOption]);

  useEffect(() => {
    const fee = (parseInt(amount) * (processingFeePercentage || 0)) + (processingFeeFlat || 0) / 100;
    setFee(fee);
  }, [amount]);

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
    <form method="post">
      <div className="form-group">
        <div className="form-check">
          <label htmlFor="payment-option-partial" className="form-check-label">
            <input
              className="form-check-input"
              type="radio"
              name="paymentOption"
              value="partial"
              id="payment-option-partial"
              checked={paymentOption === 'partial'}
              onChange={(e) => setPaymentOption(e.target.value)}
            />
            Partial payment
          </label>
        </div>
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
            Full payment
          </label>
        </div>
      </div>

      {paymentOption !== null &&
        <>
          <div className="form-group">
            <label htmlFor="repayment-amount">Payment amount</label>
            <div className="input-group">
              <div className="input-group-text">$</div>
              <input
                type="number"
                className="form-control"
                id="repayment-amount"
                min={0}
                max={balance}
                step={0.01}
                value={amount}
                onChange={(e) => setAmount(e.target.value)}
                onBlur={(e) => setAmount(parseFloat(e.target.value).toFixed(2))}
                disabled={paymentOption === 'full'}
              />
            </div>
          </div>
          <table>
            <tbody>
              <tr>
                <th>Processing fee:</th>
                <td>${fee.toFixed(2)}</td>
              </tr>
              <tr>
                <th>Total:</th>
                <td>${(parseFloat(amount) + fee).toFixed(2)}</td>
              </tr>
            </tbody>
          </table>
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
