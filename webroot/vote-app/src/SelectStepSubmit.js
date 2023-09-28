import Button from "react-bootstrap/Button";
import * as React from "react";
import Alert from "./Alert";

const SelectStepSubmit = (props) => {
  return (
    <>
      {props.allVotesAreCast && props.approvedProjects.length === 1 &&
        <Alert flavor="info">
          Since you've only approved <strong>one</strong> application, you'll skip the sorting step and immediately
          submit your vote.
        </Alert>
      }
      {props.allVotesAreCast && props.approvedProjects.length > 0 &&
        <div className="vote-footer">
          <Button
            variant="primary"
            size="lg"
            onClick={props.handleSubmitSelectStep}
            disabled={!props.allVotesAreCast || props.approvedProjects.length === 0 || props.submitIsLoading}
          >
            {props.approvedProjects.length === 1 ? 'Cast votes' : 'Next'}
            {props.submitIsLoading &&
              <>
                {' '}
                <i className="fa-solid fa-spinner fa-spin-pulse" title="Loading"></i>
              </>
            }
          </Button>
        </div>
      }
      {props.allVotesAreCast && props.approvedProjects.length === 0 &&
        <Alert flavor="warning">
          You must approve at least one application in order to cast a vote.
          If there are no applications that you'd like to approve,
          then you can abstain from voting this time.
        </Alert>
      }
    </>
  );
};

export default SelectStepSubmit;
