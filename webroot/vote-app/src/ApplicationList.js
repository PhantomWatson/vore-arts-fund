import {currencyFormatter} from "./Formatter";

const ApplicationList = (props) => {
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
                  <td>
                    <p className="application-title">
                      {application.title}
                    </p>
                    <p className="application-summary">
                      {currencyFormatter.format(application.amount_requested) + ' '}
                      requested by {application.user.name}
                    </p>
                  </td>
                  <td className="vote-actions">
                    <button>
                      👍 Approve
                    </button>
                    <button>
                      👎 Reject
                    </button>
                    <button>
                      👁 View details
                    </button>
                  </td>
                </tr>
              );
            })}
          </tbody>
        </table>
      }
    </>
  );
};

export default ApplicationList;
