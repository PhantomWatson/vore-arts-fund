import Button from "react-bootstrap/Button";
import * as React from "react";

const SelectStepSubmit = (props) => {
  return (
    <>
      {props.allVotesAreCast && props.approvedApplications.length === 1 &&
        <p className="alert alert-info">
          Since you've only approved <strong>one</strong> application, you'll skip the sorting step and immediately
          submit your vote.
        </p>
      }
      {props.allVotesAreCast && props.approvedApplications.length > 0 &&
        <div className="vote-footer">
          <Button
            variant="primary"
            size="lg"
            onClick={props.handleSubmitSelectStep}
            disabled={!props.allVotesAreCast || props.approvedApplications.length === 0 || props.submitIsLoading}
          >
            {props.approvedApplications.length === 1 ? 'Cast votes' : 'Next'}
            {props.submitIsLoading &&
              <>
                {' '}
                <i className="fa-solid fa-spinner fa-spin-pulse" title="Loading"></i>
              </>
            }
          </Button>
        </div>
      }
      {props.allVotesAreCast && props.approvedApplications.length === 0 &&
        <p className="alert alert-warning">
          You must approve at least one application in order to cast a vote.
          If there are no applications that you'd like to approve,
          then you can abstain from voting this time.
        </p>
      }
    </>
  );
};

export default SelectStepSubmit;
