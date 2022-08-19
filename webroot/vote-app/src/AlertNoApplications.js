import Alert from "./Alert";

const AlertNoApplications = () => {
  return (
    <Alert flavor="info">
      Sorry, there are no applications available to vote on at the moment.
      Please check back later.
    </Alert>
  )
};

export default AlertNoApplications;
