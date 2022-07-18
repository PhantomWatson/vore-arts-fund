import "./App.css";
import * as React from 'react';
import API from "./API.js";
import ApplicationSelectList from "./ApplicationSelectList";
import Button from 'react-bootstrap/Button';
import {useEffect, useState} from "react";
import ApplicationSortList from "./ApplicationSortList";

const App = () => {
  const [applications, setApplications] = useState(null);
  const [currentStep, setCurrentStep] = useState('select');
  const [errorMsg, setErrorMsg] = useState(null);
  const [allVotesAreCast, setAllVotesAreCast] = useState(false);
  const [approvedApplications, setApprovedApplications] = useState([]);
  const [sortingIsFinished, setSortingIsFinished] = useState(false);
  const handleVote = (applicationId, vote) => {
    // Update the selected application's vote
    let applicationIsFound = false;
    for (let i = 0, length = applications.length; i < length; i++) {
      if (applications[i].id === applicationId) {
        applications[i].vote = vote;
        applicationIsFound = true;
      }
    }
    if (!applicationIsFound) {
      console.log(`Application #${applicationId} not found`);
    }
    setApplications(applications);

    // Update list of approved applications
    let approved = [];
    for (let i = 0, length = applications.length; i < length; i++) {
      if (applications[i].vote) {
        approved.push(applications[i]);
      }
    }
    setApprovedApplications(approved);

    // Update whether or not all applications have been voted on
    let pendingVoteFound = false;
    for (let i = 0, length = applications.length; i < length; i++) {
      if (applications[i].vote === null) {
        pendingVoteFound = true;
        break;
      }
    }
    setAllVotesAreCast(!pendingVoteFound);
  };
  const handleGoToSelect = () => {
    setCurrentStep('select');
  };
  const handleGoToSort = () => {
    if (approvedApplications.length > 1) {
      setCurrentStep('sort');
    } else {
      setCurrentStep('submit');
    }
  };
  const handleGoToSubmit = () => {
    setCurrentStep('submit');
  };

  // Fetch applications and set their vote property to null (no vote cast)
  useEffect(async () => {
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
                  <Button variant="link" disabled={!allVotesAreCast} onClick={handleGoToSelect}>
                    1. Select
                  </Button>
                </li>
                <li className={currentStep === 'sort' ? 'active' : ''}>
                  <Button variant="link" disabled={!allVotesAreCast} onClick={handleGoToSort}>
                    2. Sort
                  </Button>
                </li>
                <li className={currentStep === 'submit' ? 'active' : ''}>
                  <Button variant="link" disabled={!(allVotesAreCast && (sortingIsFinished || approvedApplications.length < 2))} onClick={handleGoToSubmit}>
                    3. Submit
                  </Button>
                </li>
              </ul>
              {currentStep === 'select' &&
                <>
                  <p className="alert alert-info">
                    <span className="vote-step-title">Step one:</span> Review each application and either <strong>approve</strong> it
                    if you think it should be funded or <strong>reject</strong> it.
                  </p>
                  <ApplicationSelectList applications={applications} handleVote={handleVote} />
                  <div className="vote-footer">
                    <Button disabled={!allVotesAreCast} variant="primary" size="lg" onClick={handleGoToSort}>
                      Next
                    </Button>
                  </div>
                </>
              }
              {currentStep === 'sort' &&
                <>
                  <p className="alert alert-info">
                    <span className="vote-step-title">Step two:</span> Now that you've <em>selected</em> the {' '}
                    applications that you want funded, it's time to <strong>rank</strong> them, with #1 being the {' '}
                    highest-priority for funding, and #{approvedApplications.length} being the lowest-priority.
                  </p>
                  <ApplicationSortList applications={approvedApplications} />
                </>
              }
              {currentStep === 'submit' &&
                <>
                  <p>
                    Submit
                  </p>
                </>
              }
            </>
          }
        </>
      }
    </div>
  );
};

export default App;
