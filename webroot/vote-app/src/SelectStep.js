import Application from "./Application";
import {useState} from "react";
import ApplicationSummary from "./ApplicationSummary";
import AlertNoApplications from "./AlertNoApplications";
import Alert from "./Alert";
import * as React from "react";
import SelectStepSubmit from "./SelectStepSubmit";

const SelectStep = (props) => {
  let [selectedApplication, selectApplication] = useState(null);
  const handleClose = () => {
    selectApplication(null);
  };

  if (props.applications.length === 0) {
    return <AlertNoApplications />;
  }

  const alert = (
    <Alert flavor="info">
      <span className="vote-step-title">Step one:</span>{' '}
      Review each application and either <strong>approve</strong> it{' '}
      if you think it should be funded or <strong>reject</strong> it.
    </Alert>
  );

  const table = (
    <table id="vote-application-select-list" className="vote-application-list">
      <tbody>
      {props.applications.map((application, index) => {
        return (
          <tr key={index}>
            <td className="vote-actions">
              {application.vote === null &&
                <button
                  className="vote-actions-vote"
                  onClick={() => {selectApplication(application)}}
                >
                  Review
                </button>
              }
              {application.vote !== null &&
                <>
                  <button
                    className="vote-actions-change-vote"
                    onClick={() => {selectApplication(application)}}
                  >
                    {application.vote === true &&
                      <i className="fa-solid fa-thumbs-up"></i>
                    }
                    {application.vote === false &&
                      <i className="fa-solid fa-thumbs-down"></i>
                    }
                    <br />
                    Change vote
                  </button>
                </>
              }
            </td>
            <td>
              <ApplicationSummary application={application} />
            </td>
          </tr>
        );
      })}
      </tbody>
    </table>
  );

  return (
    <>
      {alert}
      {table}
      <Application application={selectedApplication} handleClose={handleClose} handleVote={props.handleVote} />
      <SelectStepSubmit
        approvedApplications={props.approvedApplications}
        handleSubmitSelectStep={props.handleSubmitSelectStep}
        allVotesAreCast={props.allVotesAreCast}
        submitIsLoading={props.submitIsLoading}
      />
    </>
  );
};

export default SelectStep;
