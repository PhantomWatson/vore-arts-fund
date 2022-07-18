import {useEffect, useState} from "react";
import ApplicationSummary from "./ApplicationSummary";
import AlertNoApplications from "./AlertNoApplications";

const ApplicationSortList = (props) => {
  const [sortedApplications, setSortedApplications] = useState([]);
  const [unsortedApplications, setUnsortedApplications] = useState(props.applications);
  const selectApplication = (application) => {
    // Move application from unsorted to sorted
    const newSortedApplications = sortedApplications.concat([application]);
    setSortedApplications(newSortedApplications);
    const newUnsortedApplications = [];
    for (let i = 0, length = unsortedApplications.length; i < length; i++) {
      if (unsortedApplications[i].id === application.id) {
        continue;
      }
      newUnsortedApplications.push(unsortedApplications[i]);
    }
    setUnsortedApplications(newUnsortedApplications);
  };

  // Avoid leaving a single application in the unsorted array
  useEffect(() => {
    if (unsortedApplications.length === 1) {
      const lastApplication = unsortedApplications.pop();
      selectApplication(lastApplication);
    }
  }, [unsortedApplications]);

  if (props.applications.length === 0) {
    return <AlertNoApplications />;
  }

  const instructions = (
    <p className="alert alert-info">
      {sortedApplications.length === 0 &&
        <>
          Select the application that you would <em>most</em> like to see funded from this list.
        </>
      }
      {sortedApplications.length > 0 &&
        <>
          Now continue selecting your favorite application from the remaining list until all applications have been
          put in order.
        </>
      }
    </p>
  );

  return (
    <>
      {unsortedApplications.length > 0 &&
        <>
          {instructions}
          <table className="vote-application-list">
            <tbody>
              {unsortedApplications.map((application, index) => {
                return (
                  <tr key={index}>
                    <td className="vote-actions">
                      <button className="vote-actions-rank" onClick={() => {selectApplication(application)}}>
                        Select
                      </button>
                    </td>
                    <td>
                      <ApplicationSummary application={application} />
                    </td>
                  </tr>
                );
              })}
            </tbody>
          </table>
        </>
      }
      {sortedApplications.length > 0 &&
        <>
          <p className="alert alert-info">
            Drag and drop to reorder
          </p>
          <table className="vote-application-list">
            <tbody>
              {sortedApplications.map((application, index) => {
                return (
                  <tr key={index}>
                    <td className="vote-rank">
                      #{index + 1}
                    </td>
                    <td>
                      <ApplicationSummary application={application} />
                    </td>
                  </tr>
                );
              })}
            </tbody>
          </table>
        </>
      }
    </>
  );
};

export default ApplicationSortList;
