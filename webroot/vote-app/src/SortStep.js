import AlertNoProjects from './AlertNoProjects';
import ProjectSummary from './ProjectSummary';
import { DragDropContext, Droppable, Draggable } from 'react-beautiful-dnd';
import { useEffect, useState } from 'react';
import Button from "react-bootstrap/Button";
import Alert from "./Alert";
import * as React from "react";

const SortStep = (props) => {
  const [sortedProjects, setSortedProjects] = useState([]);
  const [unsortedProjects, setUnsortedProjects] = useState(props.projects);
  const [sortingIsFinished, setSortingIsFinished] = useState(false);

  // Moves project from unsorted to sorted
  const selectProject = (project) => {
    const newSortedProjects = sortedProjects.concat([project]);
    setSortedProjects(newSortedProjects);
    const newUnsortedProjects = [];
    for (let i = 0, length = unsortedProjects.length; i < length; i++) {
      if (unsortedProjects[i].id === project.id) {
        continue;
      }
      newUnsortedProjects.push(unsortedProjects[i]);
    }
    setUnsortedProjects(newUnsortedProjects);

    if (newUnsortedProjects.length === 0) {
      setSortingIsFinished(true);

      // Propagate final sorted list up to parent
      props.setSortedProjects(newSortedProjects);
    }
  };

  // Avoid leaving a single project in the unsorted array
  useEffect(() => {
    if (unsortedProjects.length === 1) {
      const lastProject = unsortedProjects.pop();
      selectProject(lastProject);
    }
  }, [unsortedProjects]);

  const onDragEnd = (result) => {
    const { destination, source } = result;

    // No change
    if (destination.droppableId === source.droppableId && destination.index === source.index) {
      return;
    }

    const newSortedProjects = [...sortedProjects];
    const movedProject = newSortedProjects[source.index];
    newSortedProjects.splice(source.index, 1);
    newSortedProjects.splice(destination.index, 0, movedProject);
    setSortedProjects(newSortedProjects);

    // Propagate final sorted list up to parent
    props.setSortedProjects(newSortedProjects);
  };

  if (props.projects.length === 0) {
    return <AlertNoProjects />;
  }

  const unsortedList = unsortedProjects.length > 0 ? (
      <>
        <table className="vote-project-list vote-select-for-sorting">
          <tbody>
          {unsortedProjects.map((project, index) => {
            return (
              <tr key={index}>
                <td className="vote-actions">
                  <button className="vote-actions-rank" onClick={() => {selectProject(project)}}>
                    Rank #{sortedProjects.length + 1}
                  </button>
                </td>
                <td>
                  <ProjectSummary project={project} />
                </td>
              </tr>
            );
          })}
          </tbody>
        </table>
      </>
  ) : '';

  const sortedList = sortedProjects.length > 0 ? (
      <>
        <DragDropContext onDragEnd={onDragEnd}>
          <table className="vote-project-list vote-sortable-table">
            <Droppable droppableId="droppable">
              {(provided, snapshot) => (
                <tbody {...provided.droppableProps}
                       ref={provided.innerRef}
                       className={snapshot.isDraggingOver ? 'is-dragging-over' : ''}
                >
                  {sortedProjects.map((project, index) => {
                    return (
                      <Draggable
                        key={project.id}
                        draggableId={String(project.id)}
                        index={index}
                      >
                        {(provided, snapshot) => (
                          <tr
                            {...provided.draggableProps}
                            {...provided.dragHandleProps}
                            ref={provided.innerRef}
                            className={snapshot.isDragging ? 'is-dragging' : ''}
                            style={provided.draggableProps.style}
                          >
                            <td className="vote-rank">
                              #{index + 1}
                            </td>
                            <td>
                              <ProjectSummary project={project} />
                            </td>
                          </tr>
                        )}
                      </Draggable>
                    );
                  })}
                  {provided.placeholder}
                </tbody>
              )}
            </Droppable>
          </table>
        </DragDropContext>
      </>
  ) : '';

  const navButtons = (
    <div className="vote-footer row">
      <div className="col">
        <Button
          variant="secondary"
          size="lg"
          onClick={props.handleGoToSelect}
          disabled={props.submitIsLoading}
        >
          Back
        </Button>
      </div>
      <div className="col">
        {sortingIsFinished &&
          <Button
            variant="primary"
            size="lg"
            onClick={() => {props.handlePostVotes(sortedProjects)}}
            disabled={props.submitIsLoading}
          >
            Submit votes
            {props.submitIsLoading &&
              <>
                {' '}
                <i className="fa-solid fa-spinner fa-spin-pulse" title="Loading"></i>
              </>
            }
          </Button>
        }
      </div>
    </div>
  );

  const alert = (
    <Alert flavor="info">
      <p>
        <span className="vote-step-title">Step two:</span> Now that you've <em>selected</em> the{' '}
        applications that you want funded, it's time to <em>rank</em> them, with #1 being the{' '}
        highest-priority for funding, and #{props.projects.length} being the lowest-priority.
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
  );

  return (
    <>
      {alert}
      {unsortedList}
      {sortedList}
      {navButtons}
    </>
  );
};

export default SortStep;
