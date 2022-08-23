import Alert from "./Alert";
import * as React from "react";

const VoteConfirmation = () => {
  return (
    <>
      <Alert flavor="success">
        <h2>
          Votes submitted
        </h2>
        <p>
          Thank you so much for submitting your votes! Your opinions are valued and help the Vore Arts Fund be
          a community-driven program. Be sure to tell the art lovers in your life to submit their votes too!
        </p>
      </Alert>
    </>
  )
};

export default VoteConfirmation;
