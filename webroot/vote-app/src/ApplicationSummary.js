import {currencyFormatter} from "./Formatter";

const ApplicationSummary = (props) => {
  return (
    <>
      <p className="application-title">
        {props.application.title}
      </p>
      <p className="application-summary">
        {currencyFormatter.format(props.application.amount_requested) + ' '}
        requested by {props.application.user.name}
      </p>
    </>
  );
};

export default ApplicationSummary;
