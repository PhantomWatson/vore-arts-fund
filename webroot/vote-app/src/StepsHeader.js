import Button from "react-bootstrap/Button";

const StepsHeader = (props) => {
  return (
    <ul id="vote-steps">
      <li className={props.currentStep === 'select' ? 'active' : ''}>
        <Button variant="link"
                disabled={props.selectIsDisabled}
                onClick={props.handleGoToSelect}
        >
          1. Select
        </Button>
      </li>
      <li className={props.currentStep === 'sort' ? 'active' : ''}>
        <Button variant="link"
                disabled={props.sortIsDisabled}
                onClick={props.handleGoToSort}
        >
          2. Sort
        </Button>
      </li>
      <li className={props.currentStep === 'submit' ? 'active' : ''}>
        <Button
          variant="link"
          disabled={props.submitIsDisabled}
          onClick={props.handleGoToSubmit}
        >
          3. Submit
        </Button>
      </li>
    </ul>
  );
};

export default StepsHeader;
