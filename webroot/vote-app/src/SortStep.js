import AlertNoApplications from './AlertNoApplications';
import ApplicationSummary from './ApplicationSummary';
import { DragDropContext, Droppable, Draggable } from 'react-beautiful-dnd';
import { useEffect, useState } from 'react';
import Button from "react-bootstrap/Button";

const SortStep = (props) => {
  const [sortedApplications, setSortedApplications] = useState([]);
  const [unsortedApplications, setUnsortedApplications] = useState(props.applications);
  const [sortingIsFinished, setSortingIsFinished] = useState(false);

  // Moves application from unsorted to sorted
  const selectApplication = (application) => {
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

    if (newUnsortedApplications.length === 0) {
      setSortingIsFinished(true);

      // Propagate final sorted list up to parent
      props.setSortedApplications(newSortedApplications);
    }
  };

  // Avoid leaving a single application in the unsorted array
  useEffect(() => {
    if (unsortedApplications.length === 1) {
      const lastApplication = unsortedApplications.pop();
      selectApplication(lastApplication);
    }
  }, [unsortedApplications]);

  const onDragEnd = (result) => {
    const { destination, source } = result;

    // No change
    if (destination.droppableId === source.droppableId && destination.index === source.index) {
      return;
    }

    const newSortedApplications = [...sortedApplications];
    const movedApplication = newSortedApplications[source.index];
    newSortedApplications.splice(source.index, 1);
    newSortedApplications.splice(destination.index, 0, movedApplication);
    setSortedApplications(newSortedApplications);

    // Propagate final sorted list up to parent
    props.setSortedApplications(newSortedApplications);
  };

  if (props.applications.length === 0) {
    return <AlertNoApplications />;
  }

  const unsortedList = unsortedApplications.length > 0 ? (
      <>
        <table className="vote-application-list vote-select-for-sorting">
          <tbody>
          {unsortedApplications.map((application, index) => {
            return (
              <tr key={index}>
                <td className="vote-actions">
                  <button className="vote-actions-rank" onClick={() => {selectApplication(application)}}>
                    Rank #{sortedApplications.length + 1}
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
  ) : '';

  const sortedList = sortedApplications.length > 0 ? (
      <>
        <DragDropContext onDragEnd={onDragEnd}>
          <table className="vote-application-list vote-sortable-table">
            <Droppable droppableId="droppable">
              {(provided, snapshot) => (
                <tbody {...provided.droppableProps}
                       ref={provided.innerRef}
                       className={snapshot.isDraggingOver ? 'is-dragging-over' : ''}
                >
                  {sortedApplications.map((application, index) => {
                    return (
                      <Draggable
                        key={application.id}
                        draggableId={String(application.id)}
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
                              <ApplicationSummary application={application} />
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
            onClick={() => {props.handlePostVotes(sortedApplications)}}
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

  return (
    <>
      {unsortedList}
      {sortedList}
      {navButtons}
    </>
  );
};

export default SortStep;
