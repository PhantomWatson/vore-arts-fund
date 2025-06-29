import './App.css';
import {useEffect, useState} from 'react';

function App() {
  const [paymentOption, setPaymentOption] = useState<string|null>(null);

  // donation + amountTowardBalance = amountToVaf
  const [donation, setDonation] = useState<string>('0.00');
  const [amountTowardBalance, setAmountTowardBalance] = useState<string>('0.00');
  const [amountToVaf, setAmountToVaf] = useState<string>('0.00');

  // amountToVaf + fee = total
  const [fee, setFee] = useState<string>('0.00');
  const [total, setTotal] = useState<string>('0.00');

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
    } else {
      setDonation('0.00');
    }
  }, [paymentOption]);

  useEffect(() => {
    if (processingFeeFlat === undefined || processingFeePercentage === undefined) {
      return;
    }

    const subtotalCents = (parseFloat(amountTowardBalance) * 100) + (parseFloat(donation) * 100);
    const totalCents = Math.ceil((subtotalCents + processingFeeFlat)/(1 - processingFeePercentage));
    const feeCents = totalCents - subtotalCents;
    setAmountToVaf((subtotalCents / 100).toFixed(2));
    setFee((feeCents / 100).toFixed(2));
    setTotal((totalCents / 100).toFixed(2));
  }, [amountTowardBalance, donation]);

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
          {paymentOption === 'full' &&
            <div className="form-group">
              <label htmlFor="donation">+ Optional extra donation</label>
              <div className="input-group">
                <div className="input-group-text">$</div>
                <input
                  type="number"
                  className="form-control"
                  id="donation"
                  name="donation"
                  min={0}
                  step={0.01}
                  value={donation}
                  onChange={(e) => setDonation(e.target.value)}
                  onBlur={(e) => setDonation(parseFloat(e.target.value).toFixed(2))}
                />
              </div>
            </div>
          }
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
                <td>${amountToVaf}</td>
              </tr>
              <tr>
                <th>Processing fee</th>
                <td>${parseFloat(amountToVaf) > 0 ? fee : '0.00'}</td>
              </tr>
              <tr>
                <th>Total</th>
                <td>${parseFloat(amountToVaf) > 0 ? total : '0.00'}</td>
              </tr>
            </tbody>
          </table>
          <input type="hidden" name="total" value={total} />
        </>
      }

      <button
        type="submit"
        className="btn btn-primary"
        disabled={paymentOption === null || parseFloat(amountToVaf) === 0}
      >
        Proceed to payment
      </button>
    </form>
  );
}

export default App
