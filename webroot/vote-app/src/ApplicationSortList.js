import AlertNoApplications from './AlertNoApplications';
import ApplicationSummary from './ApplicationSummary';
import { DragDropContext, Droppable, Draggable } from 'react-beautiful-dnd';
import { useEffect, useState } from 'react';

const ApplicationSortList = (props) => {
  const [sortedApplications, setSortedApplications] = useState([]);
  const [unsortedApplications, setUnsortedApplications] = useState(props.applications);

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
  };

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
      }
    </>
  );
};

export default ApplicationSortList;
