import * as React from "react";

const Alert = (props) => {
  let iconClassName = props.iconClassName;
  let flavor = props.flavor;
  let iconTitle = props.iconTitle;
  if (iconClassName === undefined) {
    switch (props.flavor) {
      case 'info':
        iconClassName = 'fa-solid fa-circle-info';
        break;
      case 'warning':
        iconClassName = 'fa-solid fa-circle-info';
        break;
      case 'danger':
        iconClassName = 'fa-solid fa-circle-exclamation';
        break;
      case 'loading':
        iconClassName = 'fa-solid fa-spinner fa-spin-pulse';
        flavor = 'info';
        iconTitle = 'Loading';
        break;
    }
  }

  return (
    <div className={`alert alert-${flavor} d-flex align-items-center`}>
      {iconClassName &&
        <div className="flex-shrink-0 me-3">
          <i className={iconClassName} title={iconTitle}></i>
        </div>
      }
      <div>
        {props.children}
      </div>
    </div>
  );
};

export default Alert;
