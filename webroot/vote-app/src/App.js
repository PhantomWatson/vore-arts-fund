import "./App.css";
import * as React from 'react';
import API from "./API.js";
import SelectStep from "./SelectStep";
import Button from 'react-bootstrap/Button';
import {useEffect, useState} from "react";
import SortStep from "./SortStep";
import StepsHeader from "./StepsHeader";

const App = () => {
  const isDevMode = !process.env.NODE_ENV || process.env.NODE_ENV === 'development';
  const [applications, setApplications] = useState(null);
  const [currentStep, setCurrentStep] = useState('select');
  const [errorMsg, setErrorMsg] = useState(null);
  const [allVotesAreCast, setAllVotesAreCast] = useState(false);
  const [approvedApplications, setApprovedApplications] = useState([]);
  const [sortedApplications, setSortedApplications] = useState([]);
  const [sortingIsFinished, setSortingIsFinished] = useState(false);
  const [submitIsLoading, setSubmitIsLoading] = useState(false);

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

  const handleSubmit = async () => {
    setSubmitIsLoading(true);
    const urlBase = isDevMode ? 'http://vore.test:9000' : '';
    const url = urlBase + '/api/votes';
    let success = false;
    const data = {
      applications: sortedApplications
    };
    await fetch(url, {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify(data),
    })
      .then((response => response.json()))
      .then(data => {
        console.log('Success:', data);
        success = true;
      })
      .catch((error) => {
        alert(
          'Sorry, but an error is preventing your vote from being submitted. ' +
          'Please try again, or contact an administrator for assistance.'
        );
        console.error('Error:', error);
      });
    setSubmitIsLoading(false);

    if (success) {
      setCurrentStep('submit');
    }
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
              <StepsHeader currentStep={currentStep}
                           selectIsDisabled={!allVotesAreCast}
                           handleGoToSelect={handleGoToSelect}
                           sortIsDisabled={!allVotesAreCast}
                           handleGoToSort={handleGoToSort}
                           submitIsDisabled={
                             !(allVotesAreCast && (sortingIsFinished || approvedApplications.length < 2))
                           }
                           handleSubmit={handleSubmit}

              />
              {currentStep === 'select' &&
                <>
                  <p className="alert alert-info">
                    <span className="vote-step-title">Step one:</span>{' '}
                    Review each application and either <strong>approve</strong> it{' '}
                    if you think it should be funded or <strong>reject</strong> it.
                  </p>
                  <SelectStep applications={applications}
                              handleVote={handleVote}
                  />
                  {allVotesAreCast &&
                    <div className="vote-footer">
                      <Button
                        variant="primary"
                        size="lg"
                        onClick={handleGoToSort}
                      >
                        Next
                      </Button>
                    </div>
                  }
                </>
              }
              {currentStep === 'sort' &&
                <>
                  <div className="alert alert-info">
                    <p>
                      <span className="vote-step-title">Step two:</span> Now that you've <em>selected</em> the{' '}
                      applications that you want funded, it's time to <em>rank</em> them, with #1 being the{' '}
                      highest-priority for funding, and #{approvedApplications.length} being the lowest-priority.
                    </p>
                    <p>
                      First, <strong>select the application that you would <em>most</em> like to see funded</strong>{' '}
                      from this list.
                    </p>
                    <p>
                      Then select your <em>second-</em>favorite application, and so on, until all have been ranked.
                    </p>
                    <p>
                      Once you're finished, you can <strong>drag and drop applications to reorder them</strong>.
                    </p>
                  </div>
                  <SortStep
                    applications={approvedApplications}
                    handleGoToSelect={handleGoToSelect}
                    handleSubmit={handleSubmit}
                    setSortedApplications={setSortedApplications}
                    setSortingIsFinished={setSortingIsFinished}
                    setSubmitIsLoading={setSubmitIsLoading}
                    submitIsLoading={submitIsLoading}
                  />
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
