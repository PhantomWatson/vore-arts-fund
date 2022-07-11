const ApplicationList = (props) => {
  return (
    <>
      {props.applications.length === 0 &&
        <p>
          No applications
        </p>
      }
      {props.applications.length > 0 &&
        <ul>
          {props.applications.map(function (application, index) {
            return (
              <li key={index}>
                {application.title}
                <br />
                from {application.user.name}
              </li>
            );
          })}
        </ul>
      }
    </>
  );
};

export default ApplicationList;
