import "./App.css";
import * as React from 'react';
import API from "./API.js";
import SelectStep from "./SelectStep";
import {useEffect, useState} from "react";
import SortStep from "./SortStep";
import StepsHeader from "./StepsHeader";
import SelectStepSubmit from "./SelectStepSubmit";
import Alert from "./Alert";
import VoteConfirmation from "./VoteConfirmation";

const App = () => {
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

  const handleSubmitSelectStep = async () => {
    // Go to sort step
    if (approvedApplications.length > 1) {
      setCurrentStep('sort');
      return;
    }

    // Or submit if there's only one application approved
    if (approvedApplications.length === 1) {
      await handlePostVotes(approvedApplications);
    }

    // Or abort with an error if no application was approved
    if (approvedApplications.length === 0) {
      throw new Error('Can\'t submit form with no approved applications');
    }
  };

  const handlePostVotes = async (sortedApplications) => {
    let success = false;
    setSubmitIsLoading(true);
    try {
      success = await API.postVotes({
        applications: sortedApplications
      });
    } catch(error) {
      console.error('Error:', error);
    }
    setSubmitIsLoading(false);

    if (success) {
      setCurrentStep('submit');
    } else {
      alert(
        'Sorry, but an error is preventing your votes from being submitted. ' +
        'Please try again, or contact an administrator for assistance.'
      );
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
        <Alert flavor="danger">
          {errorMsg}
        </Alert>
      }
      {!errorMsg &&
        <>
          {applications === null &&
            <Alert flavor="loading">
              Loading applications...
            </Alert>
          }
          {applications !== null &&
            <>
              <StepsHeader currentStep={currentStep} />
              {currentStep === 'select' &&
                <>
                  <Alert flavor="info">
                    <span className="vote-step-title">Step one:</span>{' '}
                    Review each application and either <strong>approve</strong> it{' '}
                    if you think it should be funded or <strong>reject</strong> it.
                  </Alert>
                  <SelectStep applications={applications}
                              handleVote={handleVote}
                  />
                  <SelectStepSubmit
                    approvedApplications={approvedApplications}
                    handleSubmitSelectStep={handleSubmitSelectStep}
                    allVotesAreCast={allVotesAreCast}
                    submitIsLoading={submitIsLoading}
                  />
                </>
              }
              {currentStep === 'sort' &&
                <>
                  <Alert flavor="info">
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
                  </Alert>
                  <SortStep
                    applications={approvedApplications}
                    handleGoToSelect={handleGoToSelect}
                    handlePostVotes={handlePostVotes}
                    setSortedApplications={setSortedApplications}
                    submitIsLoading={submitIsLoading}
                  />
                </>
              }
              {currentStep === 'submit' &&
                <VoteConfirmation />
              }
            </>
          }
        </>
      }
    </div>
  );
};

export default App;
