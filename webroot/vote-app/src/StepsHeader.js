const StepsHeader = (props) => {
  return (
    <ul id="vote-steps">
      <li className={props.currentStep === 'select' ? 'active' : ''}>
          1. Select
      </li>
      <li className={props.currentStep === 'sort' ? 'active' : ''}>
          2. Sort
      </li>
      <li className={props.currentStep === 'submit' ? 'active' : ''}>
          3. Submit
      </li>
    </ul>
  );
};

export default StepsHeader;
