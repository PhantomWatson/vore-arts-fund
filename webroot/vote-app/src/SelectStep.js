import Project from "./Project";
import {useState} from "react";
import ProjectSummary from "./ProjectSummary";
import AlertNoProjects from "./AlertNoProjects";
import Alert from "./Alert";
import * as React from "react";
import SelectStepSubmit from "./SelectStepSubmit";

const SelectStep = (props) => {
  let [selectedProject, selectProject] = useState(null);
  const handleClose = () => {
    selectProject(null);
  };

  if (props.projects.length === 0) {
    return <AlertNoProjects />;
  }

  const alert = (
    <Alert flavor="info">
      <span className="vote-step-title">Step one:</span>{' '}
      Review each application and either <strong>approve</strong> it{' '}
      if you think it should be funded or <strong>reject</strong> it.{' '}
      Then click <strong>Next</strong> to move to the next step.
    </Alert>
  );

  const table = (
    <table id="vote-project-select-list" className="vote-project-list table">
      <tbody>
      {props.projects.map((project, index) => {
        return (
          <tr key={index}>
            <td className="vote-actions">
              {project.vote === null &&
                <button
                  className="vote-actions-vote"
                  onClick={() => {selectProject(project)}}
                >
                  Review
                </button>
              }
              {project.vote !== null &&
                <>
                  <button
                    className="vote-actions-change-vote"
                    onClick={() => {selectProject(project)}}
                  >
                    {project.vote === true &&
                      <i className="fa-solid fa-thumbs-up"></i>
                    }
                    {project.vote === false &&
                      <i className="fa-solid fa-thumbs-down"></i>
                    }
                    <br />
                    Change vote
                  </button>
                </>
              }
            </td>
            <td>
              <ProjectSummary project={project} />
            </td>
          </tr>
        );
      })}
      </tbody>
    </table>
  );

  return (
    <>
      {alert}
      {table}
      <Project project={selectedProject} handleClose={handleClose} handleVote={props.handleVote} />
      <SelectStepSubmit
        approvedProjects={props.approvedProjects}
        handleSubmitSelectStep={props.handleSubmitSelectStep}
        allVotesAreCast={props.allVotesAreCast}
        submitIsLoading={props.submitIsLoading}
      />
    </>
  );
};

export default SelectStep;
