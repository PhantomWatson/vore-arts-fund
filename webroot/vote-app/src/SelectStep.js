import Application from "./Application";
import {useState} from "react";
import ApplicationSummary from "./ApplicationSummary";
import AlertNoApplications from "./AlertNoApplications";

const SelectStep = (props) => {
  let [selectedApplication, selectApplication] = useState(null);
  const handleClose = () => {
    selectApplication(null);
  };

  if (props.applications.length === 0) {
    return <AlertNoApplications />;
  }

  return (
    <>
      <table id="vote-application-select-list" className="vote-application-list">
        <tbody>
          {props.applications.map((application, index) => {
            return (
              <tr key={index}>
                <td className="vote-actions">
                  {application.vote === null &&
                    <button className="vote-actions-vote" onClick={() => {selectApplication(application)}}>
                      Vote
                    </button>
                  }
                  {application.vote !== null &&
                    <>
                      {application.vote === true &&
                        <i className="fa-solid fa-thumbs-up"></i>
                      }
                      {application.vote === false &&
                        <i className="fa-solid fa-thumbs-down"></i>
                      }
                      <br />
                      <button className="vote-actions-change-vote">
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
      <Application application={selectedApplication} handleClose={handleClose} handleVote={props.handleVote} />
    </>
  );
};

export default SelectStep;
