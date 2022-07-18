import {currencyFormatter} from "./Formatter";
import Application from "./Application";
import {useState} from "react";

const ApplicationList = (props) => {
  let [selectedApplication, selectApplication] = useState(null);
  const handleClose = () => {
    selectApplication(null);
  };

  return (
    <>
      {props.applications.length === 0 &&
        <p>
          No applications
        </p>
      }
      {props.applications.length > 0 &&
        <table id="vote-application-list">
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
                    <p className="application-title">
                      {application.title}
                    </p>
                    <p className="application-summary">
                      {currencyFormatter.format(application.amount_requested) + ' '}
                      requested by {application.user.name}
                    </p>
                  </td>
                </tr>
              );
            })}
          </tbody>
        </table>
      }
      <Application application={selectedApplication} handleClose={handleClose} handleVote={props.handleVote} />
    </>
  );
};

export default ApplicationList;
