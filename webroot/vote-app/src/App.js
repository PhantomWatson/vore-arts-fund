import * as React from 'react';
import "./App.css";
import ApplicationList from "./ApplicationList";
import API from "./API.js";
import {useEffect, useState} from "react";

const App = () => {
  const [applications, setApplications] = useState(null);
  const [currentStep, setCurrentStep] = useState('select');
  const [selectedApplication, setSelectedApplication] = useState(null);
  const [errorMsg, setErrorMsg] = useState(null);

  useEffect(async () => {
    // Fetch applications and set their vote property to null (no vote cast)
    let fetchedApplications = await API.getApplications(setErrorMsg);
    if (fetchedApplications === null) {
      if (!errorMsg) {
        setErrorMsg(
          'Sorry, but there was an error loading the current applications. '
          + 'Please try again or contact an administrator for assistance.'
        );
      }
    } else {
      fetchedApplications = fetchedApplications.map((application) => {
        application.vote = null;
        application.rank = null;
        return application;
      });
      setApplications(fetchedApplications);
    }
  }, []);

  return (
    <div id="voter-app">
      {errorMsg &&
        <p className="alert alert-danger">
          {errorMsg}
        </p>
      }
      {!errorMsg &&
        <>
          {applications === null &&
            <p>
              Loading applications...
            </p>
          }
          {applications !== null &&
            <>
              <ul id="vote-steps">
                <li className={currentStep === 'select' ? 'active' : ''}>
                 1. Select
                </li>
                <li className={currentStep === 'sort' ? 'active' : ''}>
                  2. Sort
                </li>
                <li className={currentStep === 'submit' ? 'active' : ''}>
                  3. Submit
                </li>
              </ul>
              {currentStep === 'select' &&
                <>
                  {selectedApplication === null &&
                    <>
                      <p>
                        <span className="vote-step-title">Step one:</span> Review each application and either <strong>approve</strong> it
                        if you think it should be funded or <strong>reject</strong> it.
                      </p>
                      <ApplicationList applications={applications} />
                    </>
                  }
                  {selectedApplication &&
                    <p>
                      Selected application {selectedApplication}
                    </p>
                  }
                </>
              }
              {currentStep === 'sort' &&
                <p>
                  Sort
                </p>
              }
              {currentStep === 'submit' &&
                <p>
                  Submit
                </p>
              }
            </>
          }
        </>
      }
    </div>
  );
};

export default App;
