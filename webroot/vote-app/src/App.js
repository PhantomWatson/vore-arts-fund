import './App.css';
import * as React from 'react';
import API from './API.js';
import SelectStep from './SelectStep';
import {useEffect, useState} from 'react';
import SortStep from './SortStep';
import StepsHeader from './StepsHeader';
import Alert from './Alert';
import VoteConfirmation from './VoteConfirmation';
import {useBeforeunload} from 'react-beforeunload';

const App = () => {
  const [projects, setProjects] = useState(null);
  const [currentStep, setCurrentStep] = useState('select');
  const [errorMsg, setErrorMsg] = useState(null);
  const [allVotesAreCast, setAllVotesAreCast] = useState(false);
  const [approvedProjects, setApprovedProjects] = useState([]);
  const [sortedProjects, setSortedProjects] = useState([]);
  const [submitIsLoading, setSubmitIsLoading] = useState(false);
  const [hasInteracted, setHasInteracted] = useState(false);

  const handleVote = (projectId, vote) => {
    // Update the selected project's vote
    let projectIsFound = false;
    for (let i = 0, length = projects.length; i < length; i++) {
      if (projects[i].id === projectId) {
        projects[i].vote = vote;
        projectIsFound = true;
      }
    }
    if (!projectIsFound) {
      console.log(`Project #${projectId} not found`);
    }
    setHasInteracted(true);
    setProjects(projects);

    // Update list of approved projects
    let approved = [];
    for (let i = 0, length = projects.length; i < length; i++) {
      if (projects[i].vote) {
        approved.push(projects[i]);
      }
    }
    setApprovedProjects(approved);

    // Update whether or not all projects have been voted on
    let pendingVoteFound = false;
    for (let i = 0, length = projects.length; i < length; i++) {
      if (projects[i].vote === null) {
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
    if (approvedProjects.length > 1) {
      setCurrentStep('sort');
      return;
    }

    // Or submit if there's only one project approved
    if (approvedProjects.length === 1) {
      await handlePostVotes(approvedProjects);
    }

    // Or abort with an error if no project was approved
    if (approvedProjects.length === 0) {
      throw new Error('Can\'t submit form with no approved projects');
    }
  };

  const handlePostVotes = async (sortedProjects) => {
    let success = false;
    setSubmitIsLoading(true);

    // Only submit project IDs
    let projectIds = [];
    sortedProjects.forEach(project => {
      projectIds.push(project.id);
    });

    try {
      success = await API.postVotes({
        projects: projectIds
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

  // Fetch projects and set their vote property to null (no vote cast)
  useEffect(async () => {
    let fetchedProjects = await API.getProjects(setErrorMsg);
    if (fetchedProjects === null) {
      if (!errorMsg) {
        setErrorMsg(
          'Sorry, but there was an error loading the current projects. '
          + 'Please try again or contact an administrator for assistance.'
        );
      }
    } else {
      fetchedProjects = fetchedProjects.map((project) => {
        project.vote = null;
        project.rank = null;
        return project;
      });
      setProjects(fetchedProjects);
    }
  }, []);

  /* Don't warn about navigating away if
   * - voting has already taken place
   * - no interaction has taken place
   * - or if no votes can/will be submitted
   */
  useBeforeunload((event) => {
    const warnIfNavigatingAway =
      (projects && projects.length > 0)
      && hasInteracted
      && !(currentStep === 'submit')
      && !(allVotesAreCast && approvedProjects.length === 0);
    if (warnIfNavigatingAway) {
      return 'Are you sure you want to leave before voting?';
    }
  });

  return (
    <div id="voter-app">
      {errorMsg &&
        <Alert flavor="danger">
          {errorMsg}
        </Alert>
      }
      {!errorMsg &&
        <>
          {projects === null &&
            <Alert flavor="loading">
              Loading projects...
            </Alert>
          }
          {projects !== null &&
            <>
              <StepsHeader currentStep={currentStep} />
              {currentStep === 'select' &&
                <SelectStep
                  projects={projects}
                  approvedProjects={approvedProjects}
                  handleVote={handleVote}
                  handleSubmitSelectStep={handleSubmitSelectStep}
                  allVotesAreCast={allVotesAreCast}
                  submitIsLoading={submitIsLoading}
                />
              }
              {currentStep === 'sort' &&
                <SortStep
                  projects={approvedProjects}
                  handleGoToSelect={handleGoToSelect}
                  handlePostVotes={handlePostVotes}
                  setSortedProjects={setSortedProjects}
                  submitIsLoading={submitIsLoading}
                />
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
